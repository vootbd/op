<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Directory;
use App\Page;
use App\Rules\DirectoryValidator;
use App\Rules\DirectoryUniqueValidator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Validator;
use Session;

class DirectoryController extends ApplicationController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $directories = Directory::where('parent_id', 0)->where('name', '!=', '/')->where('name', "!=", 'unassigned')->where('deleted_at', null)->orderBy('order')->orderBy('id')->with('children')->with('pages')->get();

        return view('admin.directories.index')->with([
            'directories' => $directories
        ]);
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
        $data = $request->input();
        $data['directory'] = strtolower($data['directory']);
        $data['directory'] = str_replace(" ", "-", $data['directory']);
        $res = array(
            'success' => false,
            'message' => trans('error.default'),
            'rs_class' => 'danger',
            'data' => []
        );
        $rules = [
            'directory_id' => 'required|numeric',
            'directory' => ['required', 'min:1', 'max:' . config('constants.NAME_MAX_LENGTH', 40), new DirectoryValidator($data), 'regex:/^[A-za-z][A-za-z0-9-]*$/']
        ];
        $messages = [
            'directory.regex' => trans('directory.invalid_pattern')
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->passes()) {
            $directory = new Directory();
            $directory->name =  $data['directory'];
            $directory->caption = $data['directory'];
            $directory->parent_id = $data['directory_id'];
            $directory->order =  0;
            $directory->created_by = Auth::user()->id;
            $directory->created_at =  date('Y-m-d H:i:s');
            $directory->updated_at =  date('Y-m-d H:i:s');
            $createDirectory =  $directory->save();
            if ($createDirectory) {
                createUserActivity($request, 'ディレクトリの作成', $user->name . '<' . $user->email . '> ディレクトリの作成' .  $directory->name . 'ディレクトリ', '一般的な', null);
                $res['success'] = true;
                $res['rs_class'] = 'success';
                $res['message'] = trans('directory.created');
                $res['redirects'] = url('directories/'.$directory->id.'/edit');
                Session::flash('message', $res['message']);
            } else {
                $res['message'] = trans('directory.failed');
            }
        } else {
            $res['message'] = trans('directory.wrong');
            $res['data'] = $validator->errors()->messages();
        }
        return Response::json($res);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $directorie = Directory::find($id);
        $parent_dir = '';
        if ((isset($directorie->parent_id) && $directorie->parent_id != 0)) {
            $parent = Directory::find($directorie->parent_id);
            $parent_dir .= $parent->name;
            if ((isset($parent->parent_id) && $parent->parent_id != 0)) {
                $parent = Directory::find($parent->parent_id);
                $parent_dir = $parent->name . '/' . $parent_dir;
            }
        }
        return view('admin.directories.edit', compact('directorie', 'parent_dir'));
    }

    public function update(Request $request, $id)
    {
        $directory = Directory::find($id);

        $user = Auth::user();
        $data = $request->input();
        $data['directory'] = strtolower($data['directory']);
        $data['directory'] = str_replace(" ", "-", $data['directory']);
        $data['directory_id'] = $id;
        $res = array(
            'success' => false,
            'message' => trans('error.default'),
            'rs_class' => 'danger',
            'data' => []
        );
        $rules = [
            'directory_id' => 'required|numeric',
            'directory' => ['required', 'min:1', 'max:' . config('constants.NAME_MAX_LENGTH', 40), new DirectoryUniqueValidator($data), 'regex:/^[A-za-z][A-za-z0-9-]*$/']
        ];
        $messages = [
            'directory.regex' => trans('directory.invalid_pattern')
        ];


        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->passes()) {
            $directory->name =  $data['directory'];
            $directory->caption = $data['name'];
            $directory->created_by = Auth::user()->id;
            $directory->created_at =  date('Y-m-d H:i:s');
            $directory->updated_at =  date('Y-m-d H:i:s');
            $createDirectory =  $directory->save();
            if ($createDirectory) {
                createUserActivity($request, 'ディレクトリの作成', $user->name . '<' . $user->email . '> ディレクトリの作成' .  $directory->name . 'ディレクトリ', '一般的な', null);
                $res['success'] = true;
                $res['rs_class'] = 'success';
                $res['message'] = trans('directory.update');
                $res['redirects'] = url('directories/'.$directory->id.'/edit');
                Session::flash('message', $res['message']);
            } else {
                $res['message'] = trans('directory.failed');
            }
        } else {
            $res['message'] = trans('directory.wrong');
            $res['data'] = $validator->errors()->messages();
        }
        return Response::json($res);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyback($id)
    {
        $date = date('Y-m-d H:i:s');
        $directory = Directory::find($id);
        if ($directory->delete()) {
            $rootDirectory = Directory::where('name', '/')->get();
            $rootDirectoryId = $rootDirectory->id;
            Page::where('directory_id', $id)->update(['directory_id' => $rootDirectoryId, 'updated_at' => $date]);
            $childDirectories =  Directory::where('parent_id', $id)->get()->toArray();
            if (isset($childDirectories) && !empty($childDirectories)) {
                Page::where('directory_id', $childDirectories->id)->update(['directory_id' => $rootDirectoryId, 'updated_at' => $date]);
                Directory::find($childDirectories->id)->delete();
            }
            Session::flash('message', trans('directory.deleted'));

            /**
             * Unlink pages
             */

            return response()->json(['success' => trans('directory.deleted')]);
        }
    }

    public function destroy($id)
    {
        $date = date('Y-m-d H:i:s');
        $directories = $this->getDirectoryHierarchy($id);
        $deletingIds = [];
        $rootDirectory = Directory::where('name', '/')->get();
        $rootDirectoryId = $rootDirectory[0]->id;
        $unassignId = \Config::get('constants.UNASSIGN_DIRECTORY');

        if (isset($directories) && !empty($directories)) {
            $deletingIds[] = $id;
            if (isset($directories[0]['children']) && !empty($directories[0]['children'])) {
                $childrens = $directories[0]['children'];
                foreach ($childrens as $children) {
                    $deletingIds[] = $children['id'];
                    if (isset($children['children']) && !empty($children['children'])) {
                        $childrens = $children['children'];
                        foreach ($childrens as $children) {
                            $deletingIds[] = $children['id'];
                        }
                    }
                }
            }
        }

        if (!empty($deletingIds)) {
            foreach ($deletingIds as $id) {
                $dir = Directory::find($id);
                if ($dir->delete()) {
                    Page::where("directory_id", $dir->id)->update(['directory_id' => $unassignId, 'updated_at' => $date]);
                }
            }
            Session::flash('message', trans('directory.deleted'));
        }
        return response()->json(['success' => trans('directory.deleted')]);
    }

    public function ajaxUpdate(Request $request)
    {
        $pages = $request->pages;
        $this->pageSorting($pages);
    }

    protected function pageSorting($pages)
    {
        if (isset($pages) && $pages != '') {
            for ($i = 0; $i < count($pages); $i++) {
                $page = Directory::find($pages[$i]['id']);
                $page->order = $i;
                $page->parent_id = null;
                $page->save();

                if (isset($pages[$i]["children"])) {
                    for ($j = 0; $j < count($pages[$i]["children"]); $j++) {
                        $page = Directory::find($pages[$i]["children"][$j]["id"]);
                        $page->order = $j;
                        $page->parent_id = $pages[$i]['id'];
                        $page->save();

                        if (isset(($pages[$i]["children"][$j]["children"]))) {
                            for ($k = 0; $k < count(($pages[$i]["children"][$j]["children"])); $k++) {
                                $page = Directory::find(($pages[$i]["children"][$j]["children"])[$k]["id"]);
                                $page->order = $k;
                                $page->parent_id = ($pages[$i]["children"][$j])['id'];
                                $page->save();
                            }
                        }
                    }
                }
            }
        }
    }
}
