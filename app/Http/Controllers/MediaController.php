<?php

namespace App\Http\Controllers;

use App\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Validator;
use Yajra\DataTables\Facades\DataTables;
use Session;
use Carbon\Carbon;

class MediaController extends Controller
{
    private $photos_path;

    public function __construct()
    {
        $this->photos_path = public_path('/uploads/media');
    }
    /**
     * Display a listing of the resource.
     *
     *  @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $keyword = $request->query("search_keword");
            $per_page = $request->query('per_page');
            $start_date = $request->query('start_date');
            $end_date = $request->query('end_date'); 

            $is_active = $request->is_active;
            $start = $request->get('start');
            $length = $request->get('length');
            $orderColumn = $request->input("order");
            $columns = $request->input("columns");
            $orderColumnName = $columns[$orderColumn[0]["column"]]["data"];
            $orderingStyle = $orderColumn[0]["dir"];
            $countSearch = -1;
            $medias= DB::table('medias')
                ->join('users', 'users.id', '=', 'medias.created_by')
                ->select('medias.*', 'users.name AS user_name')
                ->where('medias.deleted_at', null);
            $count = Media::all()->count();
            if (isset($keyword)) {
                $searchValue = $keyword;
                $medias = $medias->where(function ($q) use ($keyword) {
                    return $q->where('medias.display_name', 'LIKE', "%$keyword%");
                });
                $countSearch = $medias->count();
            } 
            if (isset($start_date) && isset($end_date)) {
                $start_date = Carbon::parse($start_date);
                $end_date = Carbon::parse($end_date.' 23:59:59');
                $medias = $medias->whereBetween('medias.updated_at', [$start_date, $end_date]);
                $count = $medias->count();
            }           
            if (isset($per_page)) {
                $length = $per_page;
            }
            $medias = $medias->orderBy($orderColumnName, $orderingStyle)->skip($start)->take($length)->get();
            if ($countSearch < 0) {
                $countSearch = $count;
            }
            return DataTables::of($medias)
                ->addIndexColumn()
                ->with([
                    "recordsTotal"    => $count,
                    "recordsFiltered" => $countSearch,
                    'order' => $is_active,
                    "from"    => $start+1,
                    "to"    => (count($medias) < $length)?($start + count($medias)):($start + $length),
                ])
                ->editColumn('updated_at', function ($item) {
                    return getDateJp($item->updated_at);
                })
                ->skipPaging()
                ->make(true);
        }
        return view('admin.media.index');
    }
    
    // create media page
    public function create()
    {
        return view('admin.media.create');
    }

    // edit media image
    public function edit($id)
    {
        $media = Media::findOrFail($id);
        return view('admin.media.edit', compact('media'));
    }
    
    // update media image
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $data = $request->input();
        $res = array(
            'success' => false,
            'message' => trans('error.default'),
            'rs_class' => 'danger',
            'data' => []
        );
        $rules = [
            'display_name' => 'max:' . config('constants.DEFAULT_MAX_LENGTH', 100),
            'alt_text' => 'max:' . config('constants.DEFAULT_MAX_LENGTH', 100)
        ];

        $messages = [
            'display_name.max' => trans('media.max_char'),
            'alt_text.max' => trans('media.max_char'),
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->passes()) {
            $media  = Media::find($id);
            $media->display_name = $data['display_name'];
            $media->alt_text = $data['alt_text'];

            $updateMedia =  $media->save();
            if ($updateMedia) {
                createUserActivity($request, '更新ページ', $user->name . '<' . $user->email . '> ページ更新 ' . $media->display_name . ' 論文', '一般的な', null);
                $res['success'] = true;
                $res['rs_class'] = 'success';
                $res['message'] = trans('media.update');
                $res['redirects'] = url('medias/' . $media->id . '/edit');
                Session::flash('message', $res['message']);
            } else {
                $res['message'] =  trans('media.failed');
            }
        } else {
            $res['message'] =  trans('media.wrong');
            $res['data'] = $validator->errors()->messages();
        }

        return Response::json($res);
    }

    // upload media image
    public function upload(Request $request)
    {
        $user = Auth::user();
        $data = $request->all();
        $res = array(
            'success' => false,
            'message' => trans('error.default'),
            'rs_class' => 'danger',
            'data' => []
        );
        $rules = [
            'thumbnail_image' => 'mimes:jpeg,png,jpg,gif,pdf,svg|max:10240',
        ];

        $messages = [
            'thumbnail_image.max' => trans('media.max_size_img'),
            'thumbnail_image.mimes' => trans('media.file_types'),
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        $image = $data['thumbnail_image'];
        $extension = $image->getClientOriginalExtension();
        $originalname = $image->getClientOriginalName();
        $location = config('constants.IMG.MEDIA.UPLOAD_PATH', 'public/temp');
        $mimetype = $image->getClientMimeType();

        if ($validator->passes()) {
            $media = new Media;
            $geImageName = $this->getThumbImage($request);
            $path =Storage::disk('s3')->url($location . $geImageName);
            $imgsizes = Storage::disk('s3')->size($location . $geImageName);
            $upload_file = $request->file('thumbnail_image');
            if ($extension == 'pdf' || $extension == 'svg') {
                $height = 0;
                $width = 0;
            } else {
                $height = Image::make($upload_file)->height();
                $width = Image::make($upload_file)->width();
            }
            $media->display_name = $geImageName;
            $media->original_name = $geImageName;
            $media->url = asset(config('constants.IMG.MEDIA.LOAD_PATH').$geImageName);
            $media->mime_type = $mimetype;
            $media->extention = $extension;
            $media->size = $imgsizes;
            $media->width = $width;
            $media->height = $height;
            $media->created_by = Auth::user()->id;
            $createMedia =  $media->save();
            if ($createMedia) {
                // user activity log
                createUserActivity($request, '記事作成', $user->name.'<'.$user->email.'> 記事作成 '.null.' 論文', '一般的な', null);
                $res['success'] = true;
                $res['rs_class'] = 'success';
                $res['message'] = trans('media.create');
                $res['redirects'] = url('medias/create');
                $res['redirects'] = url('medias/'.$media->id.'/edit');
                Session::flash('message', $res['message']);
            } else {
                $res['message'] = trans('media.failed');
            }
        } else {
            $res['message'] =  trans('media.wrong');
            $res['data'] = $validator->errors()->messages();
        }
        return Response::json($res);
    }

    private function getThumbImage(Request $request, $currentImage = "")
    {
        $filename = $currentImage;
        if ($request->hasFile('thumbnail_image')) {
            if ($request->file('thumbnail_image')->isValid()) {
                $filename = $this->imageUpload($request->file('thumbnail_image'));
            }
        }
        return $filename;
    }

    //Image upload for s3
    protected function imageUpload($requestFile)
    {
        $main_image = $requestFile;
        $extension = $main_image->getClientOriginalExtension();
        $location = config('constants.IMG.MEDIA.UPLOAD_PATH', 'public/temp');
        $location_md = $location . "md/";
        $location_sm = $location . "sm/";
        $ImgName = $this->renameUploadedFile($main_image->getClientOriginalName(), $location);

        if ($extension == 'pdf') {
            Storage::disk('s3')->put($location . $ImgName, (string) file_get_contents($main_image), ['visibility' => 'public', 'mimetype' => 'application/pdf']);
        } elseif ($extension == 'svg') {
            Storage::disk('s3')->put($location . $ImgName, (string) file_get_contents($main_image), ['visibility' => 'public', 'mimetype' => 'image/svg+xml']);
        } else {
            // Instantiate SimpleImage class
            $image = Image::make($main_image)->encode($extension);
            $image_md = Image::make($main_image)->resize(900, 700, function ($aspect) {
                $aspect->aspectRatio();
            })->encode($extension);
            $image_sm = Image::make($main_image)->resize(116, 132, function ($aspect) {
                $aspect->aspectRatio();
            })->encode($extension);

            Storage::disk('s3')->put($location . $ImgName, (string) $image); // Size:large
            Storage::disk('s3')->put($location_md . $ImgName, (string) $image_md);  // Size:medium
            Storage::disk('s3')->put($location_sm . $ImgName, (string) $image_sm); // Size:small
        }
        return $ImgName;
    }

    public function destroy(Request $request, $id)
    {
        $user = Auth::user();
        $ids = explode(",", $id);
        Log::warning('Deleting user profile for user: ' . Auth::user()->email);
        Media::whereIn('id', $ids)->delete();
        // user activity log
        createUserActivity($request, '記事を削除する', $user->name.'<'.$user->email.'> 記事を削除するIds '.$id.' 論文', '一般的な', null);
        return response()->json(['success' => trans('media.delete')]);
    }

    /**
     * Get renamed file
     * @filename = "abc.png, 検索結果一覧.png"
     * $fileDirectory: for root use '/' only for other use 'folder/'
     */

    private function renameUploadedFile($fileName, $fileDirectory = '/')
    {
        $fileDirectory = $fileDirectory;
        $ext = pathinfo($fileName, PATHINFO_EXTENSION);
        $file =  pathinfo($fileName, PATHINFO_FILENAME);
        if (isJapanese($fileName)) {
            return time() . '.' . $ext;
        }

        $fileCounter = 0;
        while ($this->fileExists($fileName)) {
            $fileCounter++;
            $fileName = $file.'_('.$fileCounter.').'.$ext;
        }

        return $fileName;
    }

    private function fileExists($fileName)
    {
        $location = config('constants.IMG.MEDIA.UPLOAD_PATH', 'public/temp');
        return Storage::disk('s3')->exists($location. $fileName);
    }

}
