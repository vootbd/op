<?php

namespace App\Http\Controllers;

use App\Island;
use App\SellerProfile;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class SellerProfileController extends Controller
{
    private $temp = "temp";

    public function moveTempToLocation($file, $location_main)
    {
        $main_image = $file;
        $fileName = pathinfo($main_image, PATHINFO_FILENAME);
        $extension = pathinfo($main_image, PATHINFO_EXTENSION);

        $ImgName = $main_image;
        $ImgName_md = $fileName . "_md=294x350." . $extension;
        $ImgName_sm = $fileName . "_sm=116x132." . $extension;

        $oldLocation = "public/" . $this->temp . "/" . $ImgName;
        $oldLocation_md = "public/" . $this->temp . "/" . $ImgName_md;
        $oldLocation_sm = "public/" . $this->temp . "/" . $ImgName_sm;

        $location = "public/upload/profile/$location_main/" . $ImgName;
        $location_md = "public/upload/profile/$location_main/md/" . $ImgName_md;
        $location_sm = "public/upload/profile/$location_main/sm/" . $ImgName_sm;

        // Size:large
        if ($this->fileExists($ImgName)) {
            Storage::disk('s3')->move($oldLocation, $location);
        }

        // Size:medium
        if ($this->fileExists($ImgName_md)) {
            Storage::disk('s3')->move($oldLocation_md, $location_md);
        }

        // Size:small
        if ($this->fileExists($ImgName_sm)) {
            Storage::disk('s3')->move($oldLocation_sm, $location_sm);
        }

        $filename['image'] = "/upload/profile/$location_main/" . $ImgName;
        $filename['image_md'] = "/upload/profile/$location_main/md/" . $ImgName_md;
        $filename['image_sm'] = "/upload/profile/$location_main/sm/" . $ImgName_sm;
        return $filename;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $id)
    {
        if (!Auth::user()->hasRole('operator')) {
            if(unauthorizedAccess($id)){
                $error= ['要求されたページはこのアカウントでは表示できません。'];
                if (auth()->user()->hasRole('admin')){
                    return redirect()->route('user.activities')->withErrors($error);  
                } else if(auth()->user()->hasRole('seller')){
                    return redirect()->route('sellerProductList')->withErrors($error);
                } else if(auth()->user()->hasRole('buyer')){
                    return redirect()->route('buyer.top')->withErrors($error);
                }else{
                    return abort(401);
                }
            }
        }
        $islands = Island::pluck('name', 'id');
        $users = User::select('name', 'island_id')
            ->where('id', $id)
            ->get();
        return view('admin.profiles.create', compact('id', 'islands', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $this->validate($request, [
            'message' => 'max:2000',
            'profile1' => 'max:100',
            'profile2' => 'max:100',
            'profile3' => 'max:100',
            'profile4' => 'max:100',
            'profile5' => 'max:2000',
        ], [
            'message.max' => trans('user.max_char'),
            'profile1.max' => trans('user.max_char_profile'),
            'profile2.max' => trans('user.max_char_profile'),
            'profile3.max' => trans('user.max_char_profile'),
            'profile4.max' => trans('user.max_char_profile'),
            'profile5.max' => trans('user.max_char'),
        ]);
        $data = $request->all();
        $data['user_id'] = $data['seller_id'];
        $data['created_by'] = $user->id;
        $data['updated_by'] = $user->id;

        // upload profile image
        if ($data['cover_image'] != "" && $this->fileExists($data['cover_image'])) {
            $filename = $this->moveTempToLocation($data['cover_image'], 'cover_image');
            $data['cover_image'] = $filename['image'];
            $data['cover_image_md'] = $filename['image_md'];
            $data['cover_image_sm'] = $filename['image_sm'];
        }
        User::where('id', $data['seller_id'])->update(array('is_profile' => 1,'is_type' => 0,'type_role' => null,'is_comment_type' => 0, 'comment_type_role' => null));
        $sellerprofile = SellerProfile::create($data);
        // user activity log
        createUserActivity($request, '作成する', $user->name . '<' . $user->email . '> 作成する ' . $sellerprofile->profile1 . ' 島', '一般的な', null);

        //Seller role wise redirect for profile cereate and edit
        $role = $user->hasRole('seller');
        if (!empty($role) && $role == '1') {
            return redirect()->route('profile.edit', $user->id)
                ->with('success', trans('user.seller_create'));
        } else {
            return redirect()->route('seller.list')
                ->with('success', trans('user.seller_create'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\SellerProfile  $sellerProfile
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!Auth::user()->hasRole('operator')) {
            if(unauthorizedAccess($id)){
                $error= ['要求されたページはこのアカウントでは表示できません。'];
                if (auth()->user()->hasRole('admin')){
                    return redirect()->route('user.activities')->withErrors($error);  
                } else if(auth()->user()->hasRole('seller')){
                    return redirect()->route('sellerProductList')->withErrors($error);
                } else if(auth()->user()->hasRole('buyer')){
                    return redirect()->route('buyer.top')->withErrors($error);
                }else{
                    return abort(401);
                }
            }
            
        }
        $islands = Island::pluck('name', 'id');
        $users = User::select('name', 'island_id')
            ->where('id', $id)
            ->get();

        $data = SellerProfile::where('user_id', $id)->firstOrFail();
        return view('admin.profiles.edit', compact('id', 'islands', 'users', 'data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\SellerProfile  $sellerProfile
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $this->validate($request, [
            'message' => 'max:2000',
            'profile1' => 'max:100',
            'profile2' => 'max:100',
            'profile3' => 'max:100',
            'profile4' => 'max:100',
            'profile5' => 'max:2000',
        ], [
            'message.max' => trans('user.max_char'),
            'profile1.max' => trans('user.max_char_profile'),
            'profile2.max' => trans('user.max_char_profile'),
            'profile3.max' => trans('user.max_char_profile'),
            'profile4.max' => trans('user.max_char_profile'),
            'profile5.max' => trans('user.max_char'),
        ]);
        $oldData = SellerProfile::find($id);
        $data = $request->all();
        $data['updated_by'] = $user->id;
        $data['is_type'] = 0;
        $data['type_role'] = null;
        $data['is_comment_type'] = 0;
        $data['comment_type_role'] = null;

        // update profile image
        if ((isset($oldData->cover_image)) && ($data['cover_image'] != $oldData->cover_image) && ($data['cover_image'] != "") && ($this->fileExists($data['cover_image']))) {
            Storage::disk('s3')->delete("/public$oldData->cover_image");
            Storage::disk('s3')->delete("/public$oldData->cover_image_sm");
            Storage::disk('s3')->delete("/public$oldData->cover_image_md");
            $filename = $this->moveTempToLocation($data['cover_image'], 'cover_image');
            $data['cover_image'] = $filename['image'];
            $data['cover_image_md'] = $filename['image_md'];
            $data['cover_image_sm'] = $filename['image_sm'];
            $oldData->update($data);
        }elseif($data['cover_image'] == null || $data['cover_image'] == ''){
            Storage::disk('s3')->delete("/public$oldData->cover_image");
            Storage::disk('s3')->delete("/public$oldData->cover_image_sm");
            Storage::disk('s3')->delete("/public$oldData->cover_image_md");

            $data['cover_image'] = null;
            $data['cover_image_md'] = null;
            $data['cover_image_sm'] = null;
            $oldData->update($data);
        }elseif($data['cover_image'] != "" && $this->fileExists($data['cover_image'])){
            Storage::disk('s3')->delete("/public$oldData->cover_image");
            Storage::disk('s3')->delete("/public$oldData->cover_image_sm");
            Storage::disk('s3')->delete("/public$oldData->cover_image_md");
            $filename = $this->moveTempToLocation($data['cover_image'], 'cover_image');
            $data['cover_image'] = $filename['image'];
            $data['cover_image_md'] = $filename['image_md'];
            $data['cover_image_sm'] = $filename['image_sm'];
            $oldData->update($data);
        }
        $oldData->update($data);
        // user activity log
        createUserActivity($request, '更新', $user->name . '<' . $user->email . '> 更新 ' . $data['profile1'] . ' 島', '一般的な', null);

        //Role wise redirect page
        $role = $user->hasRole('seller');
        if (!empty($role) && $role == '1') {
            return redirect()->route('profile.edit', $user->id)
                ->with('success', trans('user.seller_update'));
        } else {
            return redirect()->route('seller.list')
                ->with('success', trans('user.seller_update'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SellerProfile  $sellerProfile
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if ($request->ajax()) {
            try {
                $sellerProfile = SellerProfile::find($id);
                $image_path = public_path() . $sellerProfile['thumbnail_image']; // Value is not URL but directory file path
                if (File::exists($image_path)) {
                    File::delete($image_path);
                    SellerProfile::where('id', $id)->update(array('thumbnail_image' => ""));
                }
            } catch (Exception $e) {
                return response()->json([
                    'status' => 500,
                    'message' => $e->getMessage(),
                ]);
            }
        }
    }

    private function fileExists($fileName)
    {
        return Storage::disk('s3')->exists("public/" . $this->temp . "/" . $fileName);
    }

    public function getReailTimeFormData(Request $request)
    {
        if ($request->ajax()) {
            try {
                $seller_id = $request->query('seller_id');
                $is_type_val = $request->query('is_type');
                $type_role = $request->query('type_role');
                $is_comment = $request->query('is_comment');
                if($type_role == 'undefined'){
                    $type_role = null;
                }

                if($is_comment == 'is_comment'){
                    $filedName = 'is_comment_type';
                    $filedRoleName = 'comment_type_role';
                }else {
                    $filedName = 'is_type';
                    $filedRoleName = 'type_role';
                }
                User::where('id', $seller_id)->update(array($filedName => $is_type_val,$filedRoleName => $type_role));

                $userData = User::select('is_comment_type','is_type','type_role')
                        ->where('id', $seller_id)
                        ->first();
                if(!empty($userData)){
                    $res['success'] = true;
                    $res['rs_class'] = 'success';
                    $res['is_type'] = $userData->is_type;
                    $res['role'] = $userData->type_role;
                    $res['is_comment_type'] = $userData->is_comment_type;
                    $res['seller_id'] = $seller_id;
                    return Response::json($res);
                }
                return Response::json($res);
            } catch (Exception $e) {
                return response()->json([
                    'status' => 500,
                    'message' => $e->getMessage(),
                ]);
            }
        }
    }

    public function getTypeCheck(Request $request)
    {
        if ($request->ajax()) {
            try {
                $userData = User::select('id','comment_type_role','is_comment_type','is_type','type_role')
                        ->where('is_active', 1)
                        ->whereNotNull('type_role')
                        ->get();
                //dd($userData);
                if(!empty($userData)){
                    $res['success'] = true;
                    $res['rs_class'] = 'success';
                    $res['data'] = $userData;
                    // $res['is_type'] = $userData->is_type;
                    // $res['role'] = $userData->type_role;
                    // $res['seller_id'] = $seller_id;
                    return Response::json($res);
                }
            } catch (Exception $e) {
                return response()->json([
                    'status' => 500,
                    'message' => $e->getMessage(),
                ]);
            }
        }
    }
}
