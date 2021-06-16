<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use App;
use App\Rules\UrlValidator;
use App\Rules\LatLongValidator;
use App\helpers;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use App\Rules\ValidatePublishingEndDate;
use Validator;
use Session;
use Carbon\Carbon;

class PageController extends ApplicationController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $table = 'App\Page';
        dateComparison($table);
        if ($request->ajax()) {
            $is_active = $request->is_active;
            $start = $request->get('start');
            $length = $request->get('length'); 
            $orderColumn = $request->input("order");
            $columns = $request->input("columns");

            $keyword = $request->query("search_keword");
            $status = $request->query('is_active'); 
            $start_date = $request->query('start_date');
            $end_date = $request->query('end_date');            
            $getOrderColumn = $columns[$orderColumn[0]["column"]]["data"];
            $orderColumnName = ($getOrderColumn == 'directory_name') ? 'directory_id': $getOrderColumn;
            $orderingStyle = $orderColumn[0]["dir"];
            $countSearch = -1; 
            $pages = DB::table('pages')
                ->join('users', 'users.id', '=', 'pages.created_by')
                ->select('pages.*', 'pages.display_date', 'pages.status_label', 'users.name AS user_name')
                ->where('pages.deleted_at', null);
            $count = Page::all()->count();
            if ($is_active == "3" || $is_active == "1" || $is_active == "2") {
                $pages = $pages->where('pages.is_active', (int) $is_active);
                $count = $pages->count();
            } 
            //*******************  Search query start **************/    
            if (isset($keyword)) {
                $searchValue = $keyword;
                $pages = $pages->where(function ($q) use ($keyword) {
                    return $q->where('pages.page_title', 'LIKE', "%$keyword%");
                });
                $countSearch = $pages->count();
            } 
            if (isset($start_date) && isset($end_date)) {
                $start_date = Carbon::parse($start_date);
                $end_date = Carbon::parse($end_date.' 23:59:59');
                $pages = $pages->whereBetween('display_date', [$start_date, $end_date]);
                $count = $pages->count();
            }
            if (isset($status)) {
                $pages = $pages->where('pages.is_active', $status);
                $count = $pages->count();
            } 
            //*******************  Search query end **************/
            
            if ($orderColumnName == 'display_date') {
                $pages = $pages->orderBy($orderColumnName, 'DESC')
                    ->orderBy('status_label', $orderingStyle)
                    ->orderBy('id', 'DESC')
                    ->skip($start)
                    ->take($length)->get();
            } else {
                $pages = $pages
                    ->orderBy($orderColumnName, $orderingStyle)
                    ->skip($start)
                    ->take($length)->get();
            }
            if ($countSearch < 0) {
                $countSearch = $count;
            }
            $dataTable =  DataTables::of($pages)
                ->addIndexColumn()
                ->with([
                    "recordsTotal"    => $count,
                    "from"    => $start+1,
                    "to"    => (count($pages) < $length)?($start + count($pages)):($start + $length),
                    "recordsFiltered" => $countSearch,
                    'order' => $is_active
                ])
                ->editColumn('directory_name', function ($item) {
                    if ($item->directory_id == config('constants.DIRECTORY_UNASSIGN')['id']) {
                        return config('constants.DIRECTORY_UNASSIGN')['name'];
                    }
                    return $this->getPageDirectory($item->directory_id);
                })
                ->editColumn('display_date', function ($d) {
                    return getDateJp($d->display_date);
                })
                ->skipPaging()
                ->make(true);

            return $dataTable;
        }

        return view('admin.pages.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $directories = $this->getLinearDirectoryHierarchy();
        $getUrl = $this->getURL();
        return view('admin.pages.create', compact('directories', 'getUrl'));
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

        $res = array(
            'success' => false,
            'message' => trans('error.default'),
            'rs_class' => 'danger',
            'data' => []
        );

        $rules = [
            'url_map' => ['required', 'min:1', 'max:' . config('constants.CUSTOM_URL_MAX_LENGTH', 100), new UrlValidator],
            'page_title' => 'required|min:2|max:' . config('constants.DEFAULT_MAX_LENGTH', 100),
            'description' => 'required|min:2',
            'search_keys' => 'max:' . config('constants.TEXT_AREA_MAX_LENGTH', 2000),
            'publishing_status' => 'required|integer|between:1,3',
            'directory' => 'required|numeric|min:1',
        ];

        if (isset($data['publishing_date']) && $data['publishing_date'] != '') {
            $rules['publishing_date'] = 'required|date|date_format:Y/m/d';
        } else {
            $data['publishing_date'] = '';
        }
        if (isset($data['publishing_end_date']) && $data['publishing_end_date'] != '') {
            $rules['publishing_end_date'] = 'required|date|date_format:Y/m/d';
        } else {
            $data['publishing_end_date'] = '';
        }
        $messages = [
            'page_title.unique' => trans('error.unique_page_title'),
            'publishing_date.date_format' => trans('error.invalid_date'),
            'publishing_date.date' => trans('error.invalid_date'),
            'publishing_date.after_or_equal' => trans('error.invalid_date'),
            'url_map.url_map_unique' => trans('page.url_unique'),
            'page_title.min' => trans('page.title_min'),   
            'description.min' => trans('page.description_min'),   
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        $pageExist = Page::where('url_map', $data['url_map'])
            ->where('directory_id', $data['directory'])->where('deleted_at', null)->first();

        if ($validator->passes() && !$pageExist) {
            $page = new Page;
            $page->url_map = $data['url_map'];
            $page->page_title =  $data['page_title'];
            $page->description = $data['description'];
            $page->search_keys = $data['search_keys'];
            $page->page_css = $data['page_css'];
            if (isset($data['publishing_date']) && $data['publishing_date'] != '') {
                $page->publishing_date = date('Y-m-d H:i:s', strtotime($data['publishing_date']));
            } else {
                $page->publishing_date = null;
            }
            if (isset($data['publishing_end_date']) && $data['publishing_end_date'] != '') {
                $page->publishing_end_date = date('Y-m-d H:i:s', strtotime($data['publishing_end_date']));
            } else {
                $page->publishing_end_date = null;
            }

            $today = date('Y-m-d H:i:s');
            if ($data['publishing_date'] != null) {
                $page->display_date = $data['publishing_date'];
            } else {
                $page->display_date = $today;
            }
            $page->status_label = 2;
            $page->created_by = Auth::user()->id;
            $page->directory_id = $data['directory'];
            $page->is_active = $data['publishing_status'];

            $createPage =  $page->save();
            if ($createPage) {
                // user activity log
                createUserActivity($request, '記事作成', $user->name . '<' . $user->email . '> 記事作成 ' . $page->page_title . ' 論文', '一般的な', null);
                $res['success'] = true;
                $res['rs_class'] = 'success';
                $res['message'] = trans('page.created');
                $res['redirects'] = url('pages/' . $page->id . '/edit');
                Session::flash('message', $res['message']);
                //dashboardActivity('pages/' . $page->id . '/edit', "Page Create");
            } else {
                $res['message'] = trans('page.failed');
            }
        } else {
            if ($pageExist) {
                $res['success'] = false;
                $res['rs_class'] = 'danger';
                $res['message'] = trans('page.failed');
                $res['data'] = $validator->errors()->messages();
                $res['data']['url_map'][0] = trans('page.url_unique');
            } else {
                $res['message'] = trans('page.failed');
                $res['data'] = $validator->errors()->messages();
            }
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
        $directories = $this->getLinearDirectoryHierarchy();
        $page = Page::findOrFail($id);
        $activeDirectory = config('constants.DIRECTORY_UNASSIGN');
        foreach ($directories as $directory) {
            if ($directory['id'] == $page->directory_id) {
                $activeDirectory = $directory;
            }
        }

        $directoryName = "/";
        if (!empty($activeDirectory)) {
            $directoryName = $activeDirectory['name'];
            if ($directoryName != '/') {
                $directoryName  = "/" . $directoryName . "/";
            }
        }
        $getUrl = $this->getURL();

        return view('admin.pages.edit', compact('page', 'directories', 'activeDirectory', 'directoryName', 'getUrl'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
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
            'url_map' => ['required', 'min:1', 'max:' . config('constants.CUSTOM_URL_MAX_LENGTH', 100), new UrlValidator],
            'page_title' => 'required|min:2|max:' . config('constants.DEFAULT_MAX_LENGTH', 100),
            'description' => 'required|min:2',
            'search_keys' => 'max:' . config('constants.TEXT_AREA_MAX_LENGTH', 2000),
            'publishing_status' => 'required|integer|between:1,3',
            'directory' => 'required|numeric|min:1',

        ];

        if (isset($data['publishing_date']) && $data['publishing_date'] != '') {
            $rules['publishing_date'] = 'required|date|date_format:Y/m/d';
        } else {
            $data['publishing_date'] = '';
        }
        if (isset($data['publishing_end_date']) && $data['publishing_end_date'] != '') {
            $rules['publishing_end_date'] = ['required', 'date', 'date_format:Y/m/d'];
        } else {
            $data['publishing_end_date'] = '';
        }
        if (!isset($data['directory'])) {
            $rules['directory'] = ['required', 'numeric'];
            $data['directory'] = config('constants.DIRECTORY_UNASSIGN')['id'];
        }
        $messages = [
            'page_title.unique' => trans('error.unique_page_title'),
            'publishing_date.date_format' => trans('error.invalid_date'),
            'publishing_date.date' => trans('error.invalid_date'),
            'publishing_date.after_or_equal' => trans('error.invalid_date'),
            'url_map.url_map_unique' => trans('page.url_unique'),
            'page_title.min' => trans('page.title_min'),   
            'description.min' => trans('page.description_min'),          
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        $pageExist = Page::where('url_map', $data['url_map'])->where('directory_id', $data['directory'])->where('deleted_at', null)->where('id', '!=', $id)->first();

        if ($validator->passes() && !$pageExist) {
            $page  = Page::find($id);
            $page->url_map =  $data['url_map'];
            if ($page->page_title != $data['page_title']) {
                $page->page_title = $data['page_title'];
            }
            $page->description = $data['description'];
            $page->search_keys = $data['search_keys'];
            $page->page_css = $data['page_css'];
            if (isset($data['publishing_date']) && $data['publishing_date'] != '') {
                $page->publishing_date = date('Y-m-d H:i:s', strtotime($data['publishing_date']));
            } else {
                $page->publishing_date = null;
            }

            if (isset($data['publishing_end_date']) && $data['publishing_end_date'] != '') {
                $page->publishing_end_date = date('Y-m-d H:i:s', strtotime($data['publishing_end_date']));
            } else {
                $page->publishing_end_date = null;
            }
            $currentDate = date("Y-m-d H:i:s");
            if ($data['publishing_date'] >= $currentDate) {
                $page->display_date = $data['publishing_date'];
                $page->status_label = 2;
            } else {
                $page->display_date = $currentDate;
                $page->status_label = 3;
            }
            $page->created_by = Auth::user()->id;
            $page->directory_id = $data['directory'];
            $page->is_active = $data['publishing_status'];

            $updatePage =  $page->save();
            if ($updatePage) {
                createUserActivity($request, '更新ページ', $user->name . '<' . $user->email . '> ページ更新 ' . $page->page_title . ' 論文', '一般的な', null);
                //dashboardActivity('pages/' . $page->id . '/edit', "Page Edit");
                $res['success'] = true;
                $res['rs_class'] = 'success';
                $res['message'] = trans('page.updated');
                $res['redirects'] = url('pages/' . $page->id . '/edit');
                Session::flash('message', $res['message']);
            } else {
                $res['message'] =  trans('page.update_failed');
            }
        } else {
            if ($pageExist) {
                $res['success'] = false;
                $res['rs_class'] = 'danger';
                $res['message'] = trans('page.update_failed');
                $res['data'] = $validator->errors()->messages();
                $res['data']['url_map'][0] = trans('page.url_unique');
            } else {
                $res['message'] =  trans('page.update_failed');
                $res['data'] = $validator->errors()->messages();
            }
        }

        return Response::json($res);
    }
    /**
     * Duplicate the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function duplicatePage(Request $request, $id)
    {
        $ids = explode(",", $id); 
        $user = Auth::user();
        foreach ($ids as $page_id) {
            if ($page_id != 'on') {
                $data = Page::findOrFail($page_id);
                $current_timestamp = Carbon::now()->timestamp;
                $pageTitle = $data->page_title . "の複製";
                $urlMap = $data->url_map . "-copy" . $current_timestamp;

                $page = new Page;
                $page->url_map = $urlMap;
                $page->page_title = $pageTitle;
                $page->description = $data->description;
                $page->search_keys = $data->search_keys;
                $page->page_css = $data->page_css;
                $page->publishing_date = $data->publishing_date;
                $page->publishing_end_date = $data->publishing_end_date;
                $page->created_by = $data->created_by;
                $page->directory_id = $data->directory_id;
                $page->is_active = $data->is_active;
                $page->display_date = $data->display_date;
                $page->status_label = $data->status_label;

                $createPage =  $page->save();
                if ($createPage) {
                    // user activity log
                    createUserActivity($request, '記事作成', $user->name . '<' . $user->email . '> 記事作成 ' . $page->page_title . ' 論文', '一般的な', null);
                    $res['success'] = true;
                    $res['rs_class'] = 'success';
                    $res['message'] = trans('page.duplicate');
                    $res['redirects'] = url('/pages');
                    Session::flash('message', $res['message']); 
                } else {
                    $res['message'] = trans('page.failed');
                }
            }
        }
        return Response::json($res);
    }

    /**
     * Change the specified Item Status.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function changePageStatus(Request $request, $id)
    {
        $ids = explode(",", $id);
        $status = array_pop($ids);
        $user = Auth::user();
        foreach ($ids as $page_id) {
            if ($page_id != 'on') {
                $page = Page::findOrFail($page_id);
                if ($status == 1) {
                    $page->is_active = 1;
                } else if ($status == 2) {
                    $page->is_active = 2;
                } else if ($status == 3) {
                    $page->is_active = 3;
                }
                $updatePage =  $page->save();
                if ($updatePage) {
                    // user activity log
                    createUserActivity($request, '記事作成', $user->name . '<' . $user->email . '> 記事作成 ' . $page->page_title . ' 論文', '一般的な', null);
                    $res['success'] = true;
                    $res['rs_class'] = 'success';
                    $res['message'] = trans('page.status');
                    $res['redirects'] = url('/pages');
                    Session::flash('message', $res['message']); 
                } else {
                    $res['message'] = trans('page.failed');
                }
            }
        }
        return Response::json($res);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $user = Auth::user();
        $ids = explode(",", $id);
        Log::warning('Deleting user profile for user: ' . Auth::user()->email);
        Page::whereIn('id', $ids)->delete();
        // user activity log
        createUserActivity($request, '記事を削除する', $user->name . '<' . $user->email . '> 記事を削除するIds ' . $id . ' 論文', '一般的な', null);
        return response()->json(['message' => trans('page.deleted')]);
    }
    /**
     * Validate url mapp
     */
    public function urlCheck(Request $request)
    {
        $data = $request->input();
        $res = array(
            'success' => false,
            'message' => 'Something went wrong.',
            'rs_class' => 'alert',
            'data' => []
        );
        if ($data['page_edit']) {
            $pageExist = Page::where('url_map', $data['url_map'])->where('directory_id', $data['directory_id'])->where('deleted_at', null)->where('id', '!=', $data['page_id'])->first();
        } else {
            $pageExist = Page::where('url_map', $data['url_map'])->where('directory_id', $data['directory_id'])->where('deleted_at', null)->first();
        }

        if (!$pageExist) {
            $res['success'] = true;
            $res['rs_class'] = 'success';
            $res['message'] = $data['url_map'];
            $res['splash_message'] = trans('page.url_ok');
        } else {
            $res['message'] = 'Something went wrong. Please check the error(s):';
            $res['data'] = trans('page.url_unique');
            $res['splash_message'] = trans('page.url_unique');
        }
        return Response::json($res);
    }

    /**
     * Get directory name for a page
     */
    private function getPageDirectory($dir_id)
    {
        $directories = $this->getLinearDirectoryHierarchy();
        $pageDirectory = "/";
        foreach ($directories as $dir => $directory) {
            if ($directory['id'] == $dir_id) {
                $pageDirectory = $directory['name'];
                break;
            }
        }
        return $pageDirectory;
    }

    /**
     *Get Url
     */
    private function getURL()
    {
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            return  "https://" . request()->getHost() . "/" . "page";
        } else {
            return "http://" . request()->getHost() . "/" . "page";
        }
    }
}
