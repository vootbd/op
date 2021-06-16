<?php

namespace App\Http\Controllers;

use Hash;
use Mail;
use Session;
use App\Area;
use App\User;
use Exception;
use App\Island;
use Carbon\Carbon;
use App\CsvSetting;
use App\Prefecture;
use App\UserIsland;
use App\Mail\UserMail;
use App\SellerContact;
use App\SellerProfile;
use App\LocalvendorSeller;
use App\LocalvendorContact;
use Illuminate\Http\Request;
use App\Rules\MatchOldPassword;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        // permission for admin
        $this->middleware('permission:all-account-list|all-account-delete|all-account-edit', ['only' => ['index']]);
        $this->middleware('permission:operator-create', ['only' => ['create']]);
        $this->middleware('permission:operator-create|seller-create|buyer-create|seller-buyer-create', ['only' => ['store']]);
        $this->middleware('permission:all-account-edit|buyer-edit|seller-edit|seller-buyer-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:all-account-delete|buyer-delete|seller-delete|seller-buyer-delete', ['only' => ['destroy']]);

        // // permission for admin and operator
        $this->middleware('permission:buyer-list|buyer-delete|seller-buyer-list|seller-buyer-delete', ['only' => ['buyerList']]);
        $this->middleware('permission:buyer-create|seller-buyer-create', ['only' => ['buyerCreate']]);

        $this->middleware('permission:seller-list|seller-delete', ['only' => ['sellerList']]);
        $this->middleware('permission:seller-create', ['only' => ['sellerCreate']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $roles = Role::where('name', '!=', 'admin')->pluck('name_jp', 'id');
        if ($request->ajax()) {
            try {
                $acount_type = $request->query('acount_type');
                $order_by = $request->query('order_by');
                $status = $request->query('status');
                $per_page = $request->query('per_page');
                $start_date = $request->query('start_date');
                $end_date = $request->query('end_date');
                $data = User::join(
                    'model_has_roles',
                    'users.id',
                    '=',
                    'model_has_roles.model_id'
                )
                    ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                    ->where('model_type', '=', 'App\\User')
                    ->select('users.id', 'users.name', 'users.is_active', 'roles.name_jp');
                if (isset($acount_type)) {
                    $data = $data->where('model_has_roles.role_id', $acount_type);
                } else {
                    $data = $data->role(['operator', 'seller', 'buyer']);
                }
                if (isset($start_date) && isset($end_date)) {
                    $start_date = Carbon::parse($start_date);
                    $end_date = Carbon::parse($end_date.' 23:59:59');
                    $data = $data->whereBetween('users.created_at', [$start_date, $end_date]);
                }
                if (isset($status)) {
                    $data = $data->where('users.is_active', $status);
                }
                return $data->orderBy('users.id', $order_by)->paginate($per_page);
            } catch (Exception $e) {
                return response()->json([
                    'status' => 500,
                    'message' => $e->getMessage(),
                ]);
            }
        }

        return view('admin.users.index', compact('roles'));
    }

    /**
     * Display a listing Users of the Island resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function islandUsersList(Request $request, $id)
    {
        if ($request->ajax()) {
            try {
                $data = DB::table('user_islands')
                            ->Join('users', 'users.id', '=', 'user_islands.user_id')
                            ->select('users.id', 'users.name')
                            ->where('user_islands.island_id', $id)->get();
                return response()->json($data);
            } catch (Exception $e) {
                return response()->json([
                    'status' => 500,
                    'message' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Display a listing of the seller resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function sellerList(Request $request)
    {
        $user = Auth::user();
        $users = User::role(['vendor'])->pluck('name', 'id');
        $islands = islandList();
        $islandDropDown = Prefecture::with(['islands' => function ($qu) {
            return $qu->select('id', 'name', 'prefecture_id');
        }])
        ->select('id', 'name')
        ->get();
        $rank = User::role(['seller'])->pluck('rank');
        if ($request->ajax()) {
            $is_active = $request->is_active;
            $start = $request->get('start');
            $length = $request->get('length');
            $orderColumn = $request->input("order");
            $columns = $request->input("columns");
            $salesCheckbox = $request->query('salesCheckbox');
            $island = $request->query("search_island");
            $localvendor = $request->query("search_localvendor");
            $rank = $request->query("search_rank");
            $is_active = $request->query('is_active');
            $start_date = $request->query('start_date');
            $end_date = $request->query('end_date');
            $getOrderColumn = $columns[$orderColumn[0]["column"]]["data"];
            $orderColumnName = ($getOrderColumn == 'directory_name') ? 'directory_id': $getOrderColumn;
            $orderingStyle = $orderColumn[0]["dir"];
            $countSearch = -1;
            $data = DB::table('users')->leftJoin('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
                                      ->leftJoin('localvendor_sellers', 'localvendor_sellers.seller_id', '=', 'users.id')
                                      ->leftJoin('user_islands', 'user_islands.user_id', '=', 'localvendor_sellers.seller_id')
                                      ->where('model_has_roles.role_id', '=', 3)
                                      ->select('users.is_active', 'users.id', 'users.name', 'localvendor_sellers.user_id AS vendor', 'user_islands.island_id', 'users.rank', 'users.created_at', 'users.updated_at')
                                      ->where('users.deleted_at', null)
                                      ->groupBy('users.id');
            $data2 = $data->get()->toArray();
            $count = count($data2);
            if ($is_active == "0" || $is_active == "1") {
                $data = $data->where('is_active', (int) $is_active);
                $count = $data->count();
            }
            //*******************  Search query start **************/
            if (isset($island)) {
                $data = $data->where('user_islands.island_id', $island);
                $data_count = $data->get()->toArray();
                $count = count($data_count);
            }
            if (isset($localvendor)) {
                $data = $data->where('localvendor_sellers.user_id', $localvendor);
                $data_count = $data->get()->toArray();
                $count = count($data_count);
            }
            if (isset($is_active)) {
                $data = $data->where('users.is_active', $is_active);
                $data_count = $data->get()->toArray();
                $count = count($data_count);
            }
            if (isset($start_date) && isset($end_date)) {
                $start_date = Carbon::parse($start_date);
                $end_date = Carbon::parse($end_date . ' 23:59:59');
                $data = $data->whereBetween('users.updated_at', [$start_date, $end_date]);
                $data_count = $data->get()->toArray();
                $count = count($data_count);
            }
            if (isset($rank)) {
                $data = $data->where('users.rank', $rank);
                $data_count = $data->get()->toArray();
                $count = count($data_count);
            }
            if ($orderColumnName == 'users.created_at') {
                $data = $data->orderBy($orderColumnName, 'DESC')
                    ->orderBy('users.is_active', $orderingStyle)
                    ->orderBy('users.id', 'DESC')
                    ->skip($start)
                    ->take($length)->get();
            } else {
                $data = $data
                    ->orderBy($orderColumnName, $orderingStyle)
                    ->skip($start)
                    ->take($length)->get();
            }
            if ($countSearch < 0) {
                $countSearch = $count;
            }
            $dataTable =  DataTables::of($data)
                ->addIndexColumn()
                ->with([
                    "recordsTotal"    => $count,
                    "from"    => $start+1,
                    "to"    => (count($data) < $length)?($start + count($data)):($start + $length),
                    "recordsFiltered" => $countSearch,
                    'order' => $is_active
                ])
                ->editColumn('created_at', function ($d) {
                    return getDateJp($d->created_at);
                })
                ->editColumn('updated_at', function ($d) {
                    return getDateJp($d->updated_at);
                })
                ->editColumn('vendor', function ($s) {
                    return convertUser($s->vendor);
                })
                ->skipPaging()
                ->make(true);

            return $dataTable;
        }
        return view('admin.users.seller-list', compact('users', 'islands', 'islandDropDown', 'rank'));
    }

    /**
     * Display a listing of the seller resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function localvendorSellerList(Request $request)
    {
        $user = Auth::user();
        $users = User::role(['vendor'])->pluck('name', 'id');
        $islands = islandList();
        $islandDropDown = Prefecture::with(['islands' => function ($qu) {
            return $qu->select('id', 'name', 'prefecture_id');
        }])
        ->select('id','name')
        ->get();
        $rank = User::role(['seller'])->pluck('rank');
        if ($request->ajax()) {
            $is_active = $request->is_active;
            $start = $request->get('start');
            $length = $request->get('length');
            $orderColumn = $request->input("order");
            $columns = $request->input("columns");
            $salesCheckbox = $request->query('salesCheckbox');
            $island = $request->query("search_island"); 
            $rank = $request->query("search_rank");
            $is_active = $request->query('is_active');
            $start_date = $request->query('start_date');
            $end_date = $request->query('end_date');
            $getOrderColumn = $columns[$orderColumn[0]["column"]]["data"];
            $orderColumnName = ($getOrderColumn == 'directory_name') ? 'directory_id': $getOrderColumn;
            $orderingStyle = $orderColumn[0]["dir"];
            $countSearch = -1;
            $data = DB::table('users')->leftJoin('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
                                      ->leftJoin('localvendor_sellers','localvendor_sellers.seller_id','=', 'users.id')
                                      ->leftJoin('user_islands', 'user_islands.user_id', '=', 'localvendor_sellers.seller_id')
                                      ->where('model_has_roles.role_id', '=',3)
                                      ->select('users.is_active', 'users.id', 'users.name','localvendor_sellers.user_id AS vendor','user_islands.island_id', 'users.rank','users.created_at', 'users.updated_at')
                                      ->where('users.deleted_at', NULL)
                                      ->where('localvendor_sellers.user_id', $user->id)
                                      ->groupBy('users.id');
            $data2 = $data->get()->toArray();
            $count = count($data2);
            if ($is_active == "0" || $is_active == "1") {
                $data = $data->where('is_active', (int) $is_active);
                $count = $data->count();
            }
            //*******************  Search query start **************/
            if (isset($island)) {
                $data = $data->where('user_islands.island_id', $island);
                $data_count = $data->get()->toArray();
                $count = count($data_count);
            } 
            if (isset($is_active)) {
                $data = $data->where('users.is_active', $is_active);
                $data_count = $data->get()->toArray();
                $count = count($data_count);
            }
            if (isset($start_date) && isset($end_date)) {
                $start_date = Carbon::parse($start_date);
                $end_date = Carbon::parse($end_date . ' 23:59:59');
                $data = $data->whereBetween('users.updated_at', [$start_date, $end_date]);
                $data_count = $data->get()->toArray();
                $count = count($data_count);
            }
            if (isset($rank)) {
                $data = $data->where('users.rank', $rank);
                $data_count = $data->get()->toArray();
                $count = count($data_count);
            }
            if ($orderColumnName == 'users.created_at') {
                $data = $data->orderBy($orderColumnName, 'DESC')
                    ->orderBy('users.is_active', $orderingStyle)
                    ->orderBy('users.id', 'DESC')
                    ->skip($start)
                    ->take($length)->get();
            } else {
                $data = $data
                    ->orderBy($orderColumnName, $orderingStyle)
                    ->skip($start)
                    ->take($length)->get();
            }
            if ($countSearch < 0) {
                $countSearch = $count;
            }
            $dataTable =  DataTables::of($data)
                ->addIndexColumn()
                ->with([
                    "recordsTotal"    => $count,
                    "from"    => $start+1,
                    "to"    => (count($data) < $length)?($start + count($data)):($start + $length),
                    "recordsFiltered" => $countSearch,
                    'order' => $is_active
                ])
                ->editColumn('created_at', function ($d) {
                    return getDateJp($d->created_at);
                })
                ->editColumn('updated_at', function ($d) {
                    return getDateJp($d->updated_at);
                })
                ->editColumn('vendor', function ($s) {
                    return convertUser($s->vendor);
                })
                ->skipPaging()
                ->make(true);

            return $dataTable;
        }
        return view('admin.users.localvendor-seller-list', compact('users', 'islands', 'islandDropDown','rank'));
    }
    
    /**
     * Display a listing of the buyer resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function buyerList(Request $request)
    {
        if ($request->ajax()) {
            $is_active = $request->is_active;
            $start = $request->get('start');
            $length = $request->get('length');
            $orderColumn = $request->input("order");
            $columns = $request->input("columns");
            $is_active = $request->query('is_active');
            $start_date = $request->query('start_date');
            $end_date = $request->query('end_date');
            $getOrderColumn = $columns[$orderColumn[0]["column"]]["data"];
            $orderColumnName = ($getOrderColumn == 'directory_name') ? 'directory_id': $getOrderColumn;
            $orderingStyle = $orderColumn[0]["dir"];
            $countSearch = -1;
            $data = User::select('id', 'name', 'is_active', 'created_at', 'updated_at')->role(['buyer']);
            $count = $data->count();
            if ($is_active == "0" || $is_active == "1") {
                $data = $data->where('is_active', (int) $is_active);
                $count = $data->count();
            }
            //*******************  Search query start **************/
            if (isset($is_active)) {
                $data = $data->where('users.is_active', $is_active);
                $data_count = $data->get()->toArray();
                $count = count($data_count);
            }
            if (isset($start_date) && isset($end_date)) {
                $start_date = Carbon::parse($start_date);
                $end_date = Carbon::parse($end_date . ' 23:59:59');
                $data = $data->whereBetween('users.updated_at', [$start_date, $end_date]);
                $data_count = $data->get()->toArray();
                $count = count($data_count);
            }
            if ($orderColumnName == 'users.created_at') {
                $data = $data->orderBy($orderColumnName, 'DESC')
                    ->orderBy('users.is_active', $orderingStyle)
                    ->orderBy('users.id', 'DESC')
                    ->skip($start)
                    ->take($length)->get();
            } else {
                $data = $data
                    ->orderBy($orderColumnName, $orderingStyle)
                    ->skip($start)
                    ->take($length)->get();
            }
            if ($countSearch < 0) {
                $countSearch = $count;
            }
            $dataTable =  DataTables::of($data)
                ->addIndexColumn()
                ->with([
                    "recordsTotal"    => $count,
                    "from"    => $start+1,
                    "to"    => (count($data) < $length)?($start + count($data)):($start + $length),
                    "recordsFiltered" => $countSearch,
                    'order' => $is_active
                ])
                ->editColumn('created_at', function ($d) {
                    return getDateJp($d->created_at);
                })
                ->editColumn('updated_at', function ($d) {
                    return getDateJp($d->updated_at);
                })
                ->skipPaging()
                ->make(true);

            return $dataTable;
        }
        return view('admin.users.buyer-list');
    }

    public function localvendorList(Request $request)
    {
        $user = Auth::user();
        $users = User::role(['vendor'])->pluck('name', 'id');
        $islands = islandList();
        $islandDropDown = Prefecture::with(['islands' => function ($qu) {
            return $qu->select('id', 'name', 'prefecture_id');
        }])
        ->select('id', 'name')
        ->get();
        if ($request->ajax()) {
            $is_active = $request->is_active;
            $start = $request->get('start');
            $length = $request->get('length');
            $orderColumn = $request->input("order");
            $columns = $request->input("columns");
            $salesCheckbox = $request->query('salesCheckbox');
            $island = $request->query("search_island");
            $is_active = $request->query('is_active');
            $start_date = $request->query('start_date');
            $end_date = $request->query('end_date');
            $getOrderColumn = $columns[$orderColumn[0]["column"]]["data"];
            $orderColumnName = ($getOrderColumn == 'directory_name') ? 'directory_id': $getOrderColumn;
            $orderingStyle = $orderColumn[0]["dir"];
            $countSearch = -1;
            $data = DB::table('users')->leftJoin('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
                                      ->leftJoin('localvendor_sellers', 'localvendor_sellers.user_id', '=', 'users.id')
                                      ->leftJoin('user_islands', 'user_islands.user_id', '=', 'localvendor_sellers.seller_id')
                                      ->where('model_has_roles.role_id', '=', 5)
                                      ->select('users.is_active', 'users.id', 'user_islands.island_id', 'users.name', 'users.created_at', 'users.updated_at')
                                      ->where('users.deleted_at', null)
                                      ->groupBy('users.id');
            $count = DB::table('users')->leftJoin('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
                                       ->where('model_has_roles.role_id', '5')
                                       ->where('users.deleted_at', null)
                                       ->count();
            if ($is_active == "0" || $is_active == "1") {
                $data = $data->where('users.is_active', (int) $is_active);
                $count = $data->count();
            }
            //*******************  Search query start **************/
            if (isset($island)) {
                $data = $data->where('user_islands.island_id', $island);
                $data_count = $data->get()->toArray();
                $count = count($data_count);
            }
            if (isset($is_active)) {
                $data = $data->where('users.is_active', $is_active);
                $data_count = $data->get()->toArray();
                $count = count($data_count);
            }
            if (isset($start_date) && isset($end_date)) {
                $start_date = Carbon::parse($start_date);
                $end_date = Carbon::parse($end_date . ' 23:59:59');
                $data = $data->whereBetween('users.updated_at', [$start_date, $end_date]);
                $data_count = $data->get()->toArray();
                $count = count($data_count);
            }
            if ($orderColumnName == 'users.created_at') {
                $data = $data->orderBy($orderColumnName, 'DESC')
                    ->orderBy('users.is_active', $orderingStyle)
                    ->orderBy('users.id', 'DESC')
                    ->skip($start)
                    ->take($length)->get();
            } else {
                $data = $data
                    ->orderBy($orderColumnName, $orderingStyle)
                    ->skip($start)
                    ->take($length)->get();
            }
            if ($countSearch < 0) {
                $countSearch = $count;
            }
            $dataTable =  DataTables::of($data)
                ->addIndexColumn()
                ->with([
                    "recordsTotal"    => $count,
                    "from"    => $start+1,
                    "to"    => (count($data) < $length)?($start + count($data)):($start + $length),
                    "recordsFiltered" => $countSearch,
                    'order' => $is_active
                ])
                ->editColumn('created_at', function ($d) {
                    return getDateJp($d->created_at);
                })
                ->editColumn('updated_at', function ($d) {
                    return getDateJp($d->updated_at);
                })
                ->skipPaging()
                ->make(true);

            return $dataTable;
        }
        return view('admin.users.vendor-list', compact('users', 'islands', 'islandDropDown'));
    }

    public function changeBuyerStatus(Request $request, $id)
    {
        $ids = explode(",", $id);
        $status_check = array_pop($ids);
        $user = Auth::user();
        foreach ($ids as $user_id) {
            if ($user_id != 'on') {
                $user = User::findOrFail($user_id);
                if ($status_check == 1) {
                    $user->is_active = 1;
                } elseif ($status_check == 0) {
                    $user->is_active = 0;
                }
                $updateUser =  $user->save();
                if ($updateUser) {
                    // user activity log
                    createUserActivity($request, '記事作成', $user->name . '<' . $user->email . '> 記事作成 ' . $user->name . ' 論文', '一般的な', null);
                    $res['success'] = true;
                    $res['rs_class'] = 'success';
                    $res['message'] = trans('user.status');
                    $res['redirects'] = url('/buyer/list');
                    Session::flash('message', $res['message']);
                } else {
                    $res['message'] = trans('user.failed');
                }
            }
        }
        return Response::json($res);
    }

    public function changeVendorStatus(Request $request, $id)
    {
        $ids = explode(",", $id);
        $status_check = array_pop($ids);
        $user = Auth::user();
        foreach ($ids as $user_id) {
            if ($user_id != 'on') {
                $user = User::findOrFail($user_id);
                if ($status_check == 1) {
                    $user->is_active = 1;
                } elseif ($status_check == 0) {
                    $user->is_active = 0;
                }
                $updateUser =  $user->save();
                if ($updateUser) {
                    // user activity log
                    createUserActivity($request, '記事作成', $user->name . '<' . $user->email . '> 記事作成 ' . $user->name . ' 論文', '一般的な', null);
                    $res['success'] = true;
                    $res['rs_class'] = 'success';
                    $res['message'] = trans('user.status');
                    $res['redirects'] = url('/localvendor/list');
                    Session::flash('message', $res['message']);
                } else {
                    $res['message'] = trans('user.failed');
                }
            }
        }
        return Response::json($res);
    }

    public function sellerStatus(Request $request, $id)
    {
        $ids = explode(",", $id);
        $status_check = array_pop($ids);
        $user = Auth::user();
        foreach ($ids as $user_id) {
            if ($user_id != 'on') {
                $user = User::findOrFail($user_id);
                if ($status_check == 1) {
                    $user->is_active = 1;
                } elseif ($status_check == 0) {
                    $user->is_active = 0;
                }
                $updateUser =  $user->save();
                if ($updateUser) {
                    // user activity log
                    createUserActivity($request, '記事作成', $user->name . '<' . $user->email . '> 記事作成 ' . $user->name . ' 論文', '一般的な', null);
                    $res['success'] = true;
                    $res['rs_class'] = 'success';
                    $res['message'] = trans('user.status');
                    $res['redirects'] = url('/operator/top');
                    Session::flash('message', $res['message']);
                } else {
                    $res['message'] = trans('user.failed');
                }
            }
        }
        return Response::json($res);
    }
    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function profile()
    {
        return view('admin.users.profile');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::pluck('name', 'name')->all();

        return view('admin.users.create', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     * Seller Create and view
     * @return \Illuminate\Http\Response
     */
    public function sellerCreate()
    {
        $islands = islandList();
        $islandDropDown = Prefecture::with([
            'islands' => function ($qu) {
                return $qu->select('id', 'name', 'prefecture_id');
            }])
        ->orderBy('id', 'ASC')
        ->select('id', 'name')
        ->get();
        return view('admin.users.seller-create', compact('islands', 'islandDropDown'));
    }
    // localvendor create
    public function localVendorCreate()
    {
        $assignedSellers = LocalvendorSeller::select('seller_id')->distinct()->get()->toArray();
        $assignedSellerIds=[];
        foreach ($assignedSellers as $key=>$value) {
            array_push($assignedSellerIds, $value['seller_id']);
        }
        $sellerList =  User::role(['seller'])->whereNotIn('id', $assignedSellerIds)->select('name', 'id')->get();
        return view('admin.users.localvendor-create', compact('sellerList'));
    }
    /**
     * Show the form for creating a new resource.
     * Buyer Create and view
     * @return \Illuminate\Http\Response
     */
    public function buyerCreate()
    {
        return view('admin.users.buyer-create');
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
            'name' => 'required|regex:/^[a-zA-Z0-9\-_]*$/i|max:40|unique:users,name',
            'display_name' => 'max:100',
            'explanation' => 'max:2000',
            'password' => [
                'required', 'string', 'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'confirmed',
            ],
        ], [
            'name.required' => trans('error.required'),
            'name.regex' => trans('error.user_name'),
            'name.max' => trans('error.max_char'),
            'name.unique' =>  trans('error.unique'),
            'explanation.max' =>  trans('user.max_char'),
            'display_name.max' => trans('error.max_char'),
            'password.min' => trans('auth.password_min'),
            'password.regex' => trans('auth.password_regex'),
            'password.confirmed' => trans('auth.password_confirmed')
        ]);

        $data = $request->all();
        $data['password'] = Hash::make($data['password']);

        //Seller role Check
        if (!empty($data['seller']) && $data['seller'] == 'seller') {
            if ((!empty($data['contact_email']) && isset($data['contact_email']))) {
                $this->validate($request, [
                    'contact_email' => 'regex:/(.+)@(.+)\.(.+)/i|max:120',
                    'email' => 'required|regex:/(.+)@(.+)\.(.+)/i|unique:users,email|max:120'
                ], [
                    'contact_email.max' =>  trans('error.max_mail'),
                    'contact_email.regex' => trans('error.email'),
                    'email.unique' =>  trans('error.unique_email'),
                    'email.regex' =>  trans('error.email'),
                    'email.max' =>  trans('error.max_mail'),
                    'email.required' => trans('error.required'),
                ]);
            } else {
                $this->validate($request, [
                    'contact_email' => 'max:120',
                    'email' => 'required|regex:/(.+)@(.+)\.(.+)/i|unique:users,email|max:120'
                ], [
                    'contact_email.max' =>  trans('error.max_mail'),
                    'email.max' =>  trans('error.max_mail'),
                    'email.unique' =>  trans('error.unique_email'),
                    'email.regex' =>  trans('error.email'),
                    'email.required' => trans('error.required'),
                ]);
            }
            $this->validate($request, [
                'island_ids' => 'required',
                'number_of_employe' => 'max:255',
                'representative' => 'max:255',
                'high_sales' => 'max:255',
                'telephone' => 'max:40',
                'fax' => 'max:40',
                'contact_email' => 'max:120',
                'contact_name' => 'max:255',
                'url' => 'max:255',
            ], [
                'island_ids.required' => trans('error.island_ids_required'),
                'number_of_employe.max' => trans('error.max_char'),
                'representative.max' =>  trans('error.max_char'),
                'high_sales.max' =>  trans('error.max_char'),
                'telephone.max' =>  trans('error.max_name'),
                'contact_email.max' =>  trans('error.max_mail'),
                'contact_name.max' => trans('error.max_char'),
                'url.max' => trans('error.max_char')
            ]);
            $data['island_id'] = $data['island_id'];
        } elseif (!empty($data['vendor']) && $data['vendor'] == 'vendor') {
            if (!empty($data['contact_email']) && isset($data['contact_email'])) {
                $this->validate($request, [
                    'contact_email' => 'regex:/(.+)@(.+)\.(.+)/i|max:120',
                    'email' => 'required|regex:/(.+)@(.+)\.(.+)/i|unique:users,email|max:120'
                ], [
                    'contact_email.max' =>  trans('error.max_mail'),
                    'contact_email.regex' => trans('error.email'),
                    'email.unique' =>  trans('error.unique_email'),
                    'email.regex' =>  trans('error.email'),
                    'email.max' =>  trans('error.max_mail'),
                    'email.required' => trans('error.required'),
                ]);
            } else {
                $this->validate($request, [
                    'email' => 'required|regex:/(.+)@(.+)\.(.+)/i|unique:users,email|max:120'
                ], [
                    'email.max' =>  trans('error.max_mail'),
                    'email.unique' =>  trans('error.unique_email'),
                    'email.regex' =>  trans('error.email'),
                    'email.required' => trans('error.required'),
                ]);
            }
            $this->validate($request, [
                'number_of_employe' => 'max:255',
                'representative' => 'max:255',
                'high_sales' => 'max:255',
                'telephone' => 'max:40',
                'fax' => 'max:40',
                'contact_email' => 'max:120',
                'contact_name' => 'max:255',
                'url' => 'max:255',
            ], [
                'seller_ids.required' => trans('error.seller_ids_required'),
                'number_of_employe.max' => trans('error.max_char'),
                'representative.max' =>  trans('error.max_char'),
                'high_sales.max' =>  trans('error.max_char'),
                'telephone.max' =>  trans('error.max_name'),
                'contact_email.max' =>  trans('error.max_mail'),
                'contact_name.max' => trans('error.max_char'),
                'url.max' => trans('error.max_char')
            ]);
        } else {
            $this->validate($request, [
                'email' => 'required|regex:/(.+)@(.+)\.(.+)/i|unique:users,email|max:120'
            ], [
                'email.required' => trans('error.required'),
                'email.unique' =>  trans('error.unique_email'),
                'email.regex' =>  trans('error.email'),
                'email.max' =>  trans('error.max_mail'),
            ]);
        }
        // shimashare seller id validation;
        if (isset($request->shimashare_seller_id) && $request->shimashare_seller_id != null) {
            $this->validate(
                $request,
                [
                'shimashare_seller_id' => 'regex:/^[0-9]*$/'
            ],
                [
                'shimashare_seller_id.regex' => trans('error.shimashare_seller_id_regex'),
            ]
            );
        }

        $data['created_by'] = $user->id;
        $data['updated_by'] = $user->id;
        $userData = User::create($data);
        // create seller profile
        $sellerProfile = SellerProfile::create([
            'user_id' => $userData->id,
            'created_by' => $user->id,
            'updated_by' => $user->id
        ]);
        if ($userData) {
            if (isset($data['island_ids']) && !empty($data['island_ids'])) {
                $this->createUserIsland($userData->id, $data['island_ids']);
            }
        }
        // vendor
        if ($userData) {
            if (isset($data['seller_ids']) && !empty($data['seller_ids'])) {
                $this->createLocalvendorSeller($userData->id, $data['seller_ids']);
            }
            if (isset($data['shimashare_seller_id']) && !empty($data['shimashare_seller_id'])) {
                DB::table('localvendor_ecmallid')->insert(array(
                    'localvendor_id'   => $userData->id,
                    'ecmall_seller_id' => $data['shimashare_seller_id'],
                    'created_by'       => $user->id,
                    'updated_by'       => $user->id
                ));
            }
        }
        //Role assign for seller, buyer and operator
        $roleUrl = '';
        if (!empty($data['seller']) && $data['seller'] == 'seller') {

            // seller contact info data insert
            SellerContact::create([
                'user_id' => $userData->id,
                'number_of_employe' => $data['number_of_employe'],
                'representative' => $data['representative'],
                'high_sales' => $data['high_sales'],
                'telephone' => $data['telephone'],
                'fax' => $data['fax'],
                'contact_email' => $data['contact_email'],
                'contact_name' => $data['contact_name'],
                'url' => $data['url'],
                'created_by' => $user->id,
                'updated_by' => $user->id
            ]);
            $userData->assignRole(3);
            $roleName = '事業者';
            $roleUrl = 'seller';
        } elseif (!empty($data['buyer']) && $data['buyer'] == 'buyer') {
            $userData->assignRole(4);
            $roleName = 'バイヤー';
            $roleUrl = 'buyer';
        } elseif (!empty($data['vendor']) && $data['vendor'] == 'vendor') {
            // vendor contact info data insert
            LocalvendorContact::create([
                'user_id' => $userData->id,
                'number_of_employe' => $data['number_of_employe'],
                'representative' => $data['representative'],
                'high_sales' => $data['high_sales'],
                'telephone' => $data['telephone'],
                'fax' => $data['fax'],
                'contact_email' => $data['contact_email'],
                'contact_name' => $data['contact_name'],
                'url' => $data['url'],
                'created_by' => $user->id,
                'updated_by' => $user->id
            ]);
            $userData->assignRole(5);
            $roleName = '地域商社';
            $roleUrl = 'localvendor';
        } else {
            $userData->assignRole(2);
            $roleName = '運営者';
            $csvs = CsvSetting::pluck('id')->toArray();
            $stack = array();
            foreach ($csvs as $csv) {
                array_push($stack, array(
                    'user_id' => User::latest()->first()->id,
                    'csv_setting_id' => $csv,
                    'in_output' => 1,
                    'status' => 1,
                    'order' => 0
                ));
            }
            DB::connection('mysql')
                 ->table('csv_setting_user')
                 ->insert($stack);
        }

        //User account info send start for buyer and operator
        if ((!empty($data['seller']) && $data['seller'] != 'seller')) {
            $details = array(
                'title' => trans('user.account_info'),
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $request['password'],
                'url' => route('login'),
            );
            $toEmail = $data['email'];
            Mail::to($toEmail)->send(new UserMail($details));
        }
        //User account info send end

        // user activity log
        createUserActivity($request, '作成する', $user->name . '<' . $user->email . '> 作成する ' . $userData->name . '<' . $userData->name . '> ' . $roleName . ' アカウント', '一般的な', null);

        $redirectUrl = '';
        if ($user->hasRole('admin')) {
            $redirectUrl = 'users.index';
        } elseif ($user->hasRole('operator')) {
            $redirectUrl = $roleUrl.'.list';
        }

        return redirect()->route($redirectUrl)
            ->with('success', trans('user.create'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);

        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!Auth::user()->hasRole('operator')) {
            if (unauthorizedAccess($id)) {
                $error= ['要求されたページはこのアカウントでは表示できません。'];
                if (auth()->user()->hasRole('admin')) {
                    return redirect()->route('user.activities')->withErrors($error);
                } elseif (auth()->user()->hasRole('seller')) {
                    return redirect()->route('sellerProductList')->withErrors($error);
                } elseif (auth()->user()->hasRole('buyer')) {
                    return redirect()->route('buyer.top')->withErrors($error);
                } elseif (auth()->user()->hasRole('vendor')) {
                    return redirect()->route('localvendorProductList')->withErrors($error);
                } else {
                    return abort(401);
                }
            }
        }
        $data = User::with('roles')->with('userIslands')->with('sellerContact')->find($id);
        if (auth()->user()->hasRole('seller')) {
            $error= ['要求されたページはこのアカウントでは表示できません。'];
            return redirect()->route('sellerProductList')->withErrors($error);
        }
        $islands = islandList();
        $islandDropDown = Prefecture::with(['islands' => function ($qu) {
            return $qu->select('id', 'name', 'prefecture_id');
        }])
        ->orderBy('id', 'ASC')
        ->select('id', 'name')
        ->get();
        $island_Pref = [];
        $islandIds = UserIsland::where('user_id', '=', $id)->select('island_id')->get()->toArray();
        $islandPref = Island::whereIn('id', $islandIds)->with('prefectures')->select('prefecture_id')->get()->toArray();
        foreach ($islandPref as $item) {
            $island_Pref[] = $item['prefecture_id'];
        }
        $islandPrefecture = Prefecture::with(['islands' => function ($qu) use ($islandIds) {
            return $qu->whereIn('id', $islandIds)->select('id', 'name', 'prefecture_id');
        }])
        ->select('id', 'name')
        ->whereIn('id', $island_Pref)
        ->get();
        $areas = Area::select('area_name', 'id')
                ->where('is_active', 1)
                ->get();
        $prefecture = Prefecture::select('id', 'name', 'area_id')->get();
        $localVendor = User::with('roles')->with('localvendorSellers', 'localvendorEcmallId')->find($id);
        $sellerList =  User::role(['seller'])
                        ->leftJoin('localvendor_sellers', 'localvendor_sellers.seller_id', '=', 'users.id')
                        ->select('users.name', 'users.id', 'localvendor_sellers.user_id as localVendorId')
                        ->where('is_active', 1)
                        ->get();
        $localVendorList = User::role('vendor')->select('id', 'name')->get();

        $localVendorNameById = [];
        foreach ($localVendorList as $vendor) {
            $localVendorNameById [$vendor->id] = $vendor->name;
        }

        $sellerNameById = [];
        foreach ($sellerList as $seller) {
            $sellerNameById [$seller->id] = $seller->name;
        }
        $localVendorContacts = LocalVendorContact::where('user_id', $id)->get();
        return view('admin.users.edit', compact('id', 'data', 'islands', 'prefecture', 'islandPrefecture', 'islandDropDown', 'sellerList', 'localVendor', 'sellerNameById', 'localVendorNameById', 'localVendorContacts'));
    }
    public function localVendorEdit($id)
    {
        $localVendor = User::with('roles')->with('localvendorSellers', 'localvendorEcmallId')->find($id);
        $sellerList =  User::role(['seller'])
                        ->leftJoin('localvendor_sellers', 'localvendor_sellers.seller_id', '=', 'users.id')
                        ->select('users.name', 'users.id', 'localvendor_sellers.user_id as localVendorId')
                        ->where('is_active', 1)
                        ->get();
        $localVendorList = User::role('vendor')->select('id', 'name')->get();

        $localVendorNameById = [];
        foreach ($localVendorList as $vendor) {
            $localVendorNameById [$vendor->id] = $vendor->name;
        }

        $sellerNameById = [];
        foreach ($sellerList as $seller) {
            $sellerNameById [$seller->id] = $seller->name;
        }
        $localVendorContacts = LocalVendorContact::where('user_id', $id)->get();
        return view('admin.users.localvendor-edit', compact('sellerList', 'localVendor', 'sellerNameById', 'localVendorNameById', 'localVendorContacts'));
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
        if (!empty($request->password) && (isset($request->password))) {
            $this->validate($request, [
                'display_name' => 'max:255',
                'explanation' => 'max:2000',
                'password' => [
                    'required', 'string', 'min:8',
                    'regex:/[a-z]/',
                    'regex:/[A-Z]/',
                    'regex:/[0-9]/',
                    'confirmed',
                ],
            ], [
                'display_name.max' => trans('error.max_char'),
                'explanation.max' =>  trans('user.max_char'),
                'password.min' => trans('auth.password_min'),
                'password.regex' => trans('auth.password_regex'),
                'password.confirmed' => trans('auth.password_confirmed')
            ]);
        } else {
            $this->validate($request, [
                'display_name' => 'max:255',
                'explanation' => 'max:2000',
            ], [
                'display_name.max' => trans('error.max_char'),
                'explanation.max' =>  trans('user.max_char'),
            ]);
        }
        $userData = User::find($id);

        //unique email and request email check
        $email = User::where('id', $id)->first()->email;
        if ($request->email != $email) {
            $this->validate(
                $request,
                [
                'email' => 'required|regex:/(.+)@(.+)\.(.+)/i|unique:users,email|max:120'
            ],
                [
                    'email.required' => trans('error.required'),
                    'email.unique' =>  trans('error.unique_email'),
                    'email.max' =>  trans('error.max_mail'),
                    'email.regex' =>  trans('error.email'),
                ]
            );
        }

        // shimashare seller id validation;
        if (isset($request->shimashare_seller_id) && $request->shimashare_seller_id != null) {
            $this->validate(
                $request,
                [
                'shimashare_seller_id' => 'regex:/^[0-9]*$/'
            ],
                [
                'shimashare_seller_id.regex' => trans('error.shimashare_seller_id_regex'),
            ]
            );
        }

        $input = $request->all();
        //Role type check for seller
        if (!empty($request->password) && (isset($request->password))) {
            $input['password'] = Hash::make($input['password']);
        } else {
            $input['password'] = $userData->password;
        }

        //Island id empty check
        if ($user->hasRole('operator') && $userData->hasRole('seller')) {
            if (!empty($input['contact_email']) || isset($input['contact_email'])) {
                $this->validate($request, [
                    'contact_email' => 'regex:/(.+)@(.+)\.(.+)/i|max:120',
                ], [
                    'contact_email.max' =>  trans('error.max_mail'),
                    'contact_email.regex' => trans('error.email')
                ]);
            }
            $this->validate($request, [
                'island_ids' => 'required',
                'number_of_employe' => 'max:255',
                'representative' => 'max:255',
                'high_sales' => 'max:255',
                'telephone' => 'max:40',
                'fax' => 'max:40',
                'contact_name' => 'max:255',
                'url' => 'max:255',
            ], [
                'island_ids.required' => trans('error.island_ids_required'),
                'number_of_employe.max' => trans('error.max_char'),
                'representative.max' =>  trans('error.max_char'),
                'high_sales.max' =>  trans('error.max_char'),
                'telephone.max' =>  trans('error.max_name'),
                'contact_name.max' => trans('error.max_char'),
                'url.max' => trans('error.max_char')
            ]);
            $input['island_id'] = $input['island_id'];
        }
        //Contact validation for Local Vendor
        if ($user->hasRole('operator') && $userData->hasRole('vendor')) {
            if (!empty($input['contact_email']) || isset($input['contact_email'])) {
                $this->validate($request, [
                    'contact_email' => 'regex:/(.+)@(.+)\.(.+)/i|max:120',
                ], [
                    'contact_email.max' =>  trans('error.max_mail'),
                    'contact_email.regex' => trans('error.email')
                ]);
            }
            $this->validate($request, [
                'number_of_employe' => 'max:255',
                'representative' => 'max:255',
                'high_sales' => 'max:255',
                'telephone' => 'max:40',
                'fax' => 'max:40',
                'contact_name' => 'max:255',
                'url' => 'max:255',
            ], [
                'number_of_employe.max' => trans('error.max_char'),
                'representative.max' =>  trans('error.max_char'),
                'high_sales.max' =>  trans('error.max_char'),
                'telephone.max' =>  trans('error.max_name'),
                'contact_name.max' => trans('error.max_char'),
                'url.max' => trans('error.max_char')
            ]);
            $input['roleName']='地域商社';
        }

        $input['updated_by'] = $user->id;

        $updated = $userData->update($input);
        //island update for seller
        if ($updated && $userData->hasRole('seller')) {
            $this->deleteUserIsland($userData->id);
            if (isset($input['island_ids']) && !empty($input['island_ids'])) {
                $this->createUserIsland($userData->id, $input['island_ids']);
            }
        }

        //seller update for local vendor
        if ($updated && $userData->hasRole('vendor')) {
            $this->deleteLocalVendorSeller($userData->id);
            if (isset($input['seller_ids']) && !empty($input['seller_ids'])) {
                $this->createLocalvendorSeller($userData->id, $input['seller_ids']);
            }
        }
        if ($updated && $userData->hasRole('vendor')) {
            DB::table('localvendor_ecmallid')->where('localvendor_id', $userData->id)->delete();
            if (isset($input['shimashare_seller_id']) && !empty($input['shimashare_seller_id'])) {
                DB::table('localvendor_ecmallid')->insert(array(
                    'localvendor_id'   => $userData->id,
                    'ecmall_seller_id' => $input['shimashare_seller_id'],
                    'created_by'       => $user->id,
                    'updated_by'       => $user->id
                ));
            }
        }

        //Seller contact info data updated
        if ($user->hasRole('operator') && $userData->hasRole('seller')) {
            SellerContact::where('user_id', $userData->id)->update([
                'user_id' => $userData->id,
                'number_of_employe' => $input['number_of_employe'],
                'representative' => $input['representative'],
                'high_sales' => $input['high_sales'],
                'telephone' => $input['telephone'],
                'fax' => $input['fax'],
                'contact_email' => $input['contact_email'],
                'contact_name' => $input['contact_name'],
                'url' => $input['url'],
                'created_by' => $user->id,
                'updated_by' => $user->id
            ]);
        }

        //Local vendor contact info data updated
        if ($user->hasRole('operator') && $userData->hasRole('vendor')) {
            LocalvendorContact::where('user_id', $userData->id)->update([
                'user_id' => $userData->id,
                'number_of_employe' => $input['number_of_employe'],
                'representative' => $input['representative'],
                'high_sales' => $input['high_sales'],
                'telephone' => $input['telephone'],
                'fax' => $input['fax'],
                'contact_email' => $input['contact_email'],
                'contact_name' => $input['contact_name'],
                'url' => $input['url'],
                'created_by' => $user->id,
                'updated_by' => $user->id
            ]);
        }

        //User account info send start
        if (!empty($request->password) && (isset($request->password))) {
            $details = array(
                'title' => '口座情報',
                'name'=> $userData->name,
                'email' => $input['email'],
                'password' => $request['password'],
                'url' => route('login')
            );
            $toEmail = $input['email'];
            Mail::to($toEmail)->send(new UserMail($details));
        }
        //User account info send end

        // user activity log
        createUserActivity($request, '更新', $user->name . '<' . $user->email . '> 更新 ' . $userData->name . '<' . $userData->name . '> ' . $input['roleName'] . ' アカウント', '一般的な', null);

        $redirectUrl = '';
        if ($user->hasRole('admin')) {
            $redirectUrl = 'users.index';
        } elseif ($user->hasRole('operator')) {
            if ($userData->hasRole('seller')) {
                $redirectUrl = 'seller.list';
            } elseif ($userData->hasRole('vendor')) {
                $redirectUrl = 'localvendor.list';
            } else {
                $redirectUrl = 'buyer.list';
            }
        } elseif ($user->hasRole('vendor')) {
            if ($userData->hasRole('vendor')) {
                $redirectUrl = 'localvendorProductList';
            }
        }

        return redirect()->route($redirectUrl)
            ->with('success', trans('user.update'));
    }

    /**
     * Update the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateUserStatus(Request $request, $id)
    {
        $user = Auth::user();
        $userData = User::find($id);
        $userData->is_active = $request->status;
        $userData->save();
        $getData = User::find($id);
        // user activity log
        createUserActivity($request, '削除する', $user->name . '<' . $user->email . '> 更新 ' . $getData->name . '<' . $getData->name . '> アカウント', '一般的な', null);

        return response()->json(['success' => $getData->is_active]);
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
        $userData = User::find($id);
        $this->deleteUserIsland($userData->id);
        User::find($id)->delete();
        // user activity log
        createUserActivity($request, '削除する', $user->name . '<' . $user->email . '> 削除する ' . $userData->name . '<' . $userData->name . '> アカウント', '一般的な', null);

        return response()->json(['success' =>  trans('user.delete')]);
    }

    public function buyerDelete(Request $request, $id)
    {
        $user = Auth::user();
        $ids = explode(",", $id);
        User::whereIn('id', $ids)->delete();
        // user activity log
        createUserActivity($request, '記事を削除する', $user->name . '<' . $user->email . '> 記事を削除するIds ' . $id . ' 論文', '一般的な', null);
        $res['success'] = true;
        $res['rs_class'] = 'success';
        $res['message'] = trans('user.delete');
        $res['redirects'] = url('/buyer/list');
        Session::flash('message', $res['message']);
        return Response::json($res);
    }

    public function localvendorDelete(Request $request, $id)
    {
        $user = Auth::user();
        $ids = explode(",", $id);
        foreach ($ids as $id) {
            $this->deleteLocalVendorSeller($id);
        }
        foreach ($ids as $id) {
            DB::table('localvendor_ecmallid')->where('localvendor_id', $id)->delete();
        }
        User::whereIn('id', $ids)->delete();
        // user activity log
        createUserActivity($request, '記事を削除する', $user->name . '<' . $user->email . '> 記事を削除するIds ' . $id . ' 論文', '一般的な', null);
        $res['success'] = true;
        $res['rs_class'] = 'success';
        $res['message'] = trans('user.delete');
        $res['redirects'] = url('/localvendor/list');
        Session::flash('message', $res['message']);
        return Response::json($res);
    }

    public function sellerDelete(Request $request, $id)
    {
        $user = Auth::user();
        $ids = explode(",", $id);
        User::whereIn('id', $ids)->delete();
        // user activity log
        createUserActivity($request, '記事を削除する', $user->name . '<' . $user->email . '> 記事を削除するIds ' . $id . ' 論文', '一般的な', null);
        $res['success'] = true;
        $res['rs_class'] = 'success';
        $res['message'] = trans('user.delete');
        $res['redirects'] = url('/operator/top');
        Session::flash('message', $res['message']);
        return Response::json($res);
    }

    /**
     * (Edit name,edit email change password) & setting functionality start
     */
    public function settings()
    {
        $user = Auth::user();
        return view('admin.users.setting', compact('user'));
    }

    public function editName()
    {
        $user = Auth::user();
        return view('admin.users.edit-name', compact('user'));
    }

    public function updateName(Request $request)
    {
        $user = Auth::user();
        $this->validate($request, [
            'display_name' => 'required|max:255',
        ], [
            'display_name.max' => trans('error.max_char'),
        ]);
        User::where('id', $user->id)->update(['display_name' => $request->display_name]);
        // user activity log
        createUserActivity($request, '更新', $user->name . '<' . $user->email . '> 更新 自分の表示名', '一般的な', null);

        return redirect()->route('settings')->with('success', trans('user.setting_name'));
    }

    public function editMail()
    {
        $user = Auth::user();
        if (empty($user->email)) {
            return view('admin.users.edit-email', compact('user'));
        } else {
            return view('admin.users.edit-email', compact('user'));
        }
    }

    public function updateMail(Request $request)
    {
        $user = Auth::user();
        $this->validate($request, [
            'password' => [
                'required', new MatchOldPassword ,'string', 'min:8'
            ],
            'email' => 'required|regex:/(.+)@(.+)\.(.+)/i|unique:users,email|max:120',
        ], [
            'password.min' =>  trans('auth.password_min'),
            'email.max' => trans('error.max_mail'),
            'email.unique' => trans('error.unique_email'),
            'email.regex' => trans('error.email'),
        ]);
        User::where('id', $user->id)->update(['email' => $request->email]);
        // user activity log
        createUserActivity($request, '更新', $user->name . '<' . $user->email . '> 更新 自分のメール', '一般的な', null);

        return redirect()->route('settings')->with('success', trans('user.setting_email'));
    }

    public function changePassword(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'old_password' => ['required', new MatchOldPassword],
            'password' => [
                'required', 'string', 'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'confirmed',
            ],
            'password_confirmation' => ['required'],
        ], [
            'password.min' =>  trans('auth.password_min'),
            'password.regex' => trans('auth.password_regex'),
            'password.confirmed' => trans('auth.password_confirmed')
        ]);

        User::find(auth()->user()->id)->update(['password' => Hash::make($request->password)]);
        // user activity log
        createUserActivity($request, '更新', $user->name . '<' . $user->email . '> 更新 自分のパスワード', '一般的な', null);

        return redirect()->route('settings')->with('success', trans('user.setting_pass'));
    }

    /**
     * Display a listing of the buyer resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function softDeleteCheck(Request $request, $email)
    {
        if ($request->ajax()) {
            try {
                $checkEmail = User::withTrashed()->select('email', 'deleted_at')->where('email', '=', $email)->whereNotNull('deleted_at')->first();
                return response()->json([
                    'status' => $checkEmail
                ]);
            } catch (Exception $e) {
                return response()->json([
                    'status' => 500,
                    'message' => $e->getMessage(),
                ]);
            }
        }
    }
    /**
     * Display a listing of the buyer resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function userActive(Request $request, $email)
    {
        if ($request->ajax()) {
            try {
                User::where('email', $email)->restore();
                $checkEmail = User::withTrashed()->select('id')->where('email', $email)->first();
                return response()->json([
                    'id' => $checkEmail->id
                ]);
            } catch (Exception $e) {
                return response()->json([
                    'status' => 500,
                    'message' => $e->getMessage(),
                ]);
            }
        }
    }

    private function createUserIsland($UserId, $islands = [])
    {
        $today = date('Y-m-d H:i:s');
        foreach ($islands as $key => $island) {
            $islandCode = Island::where('id', $island)->select('code')->get()->toArray();
            if (isset($islandCode[0]['code'])) {
                $islandCode = $islandCode[0]['code'];
                DB::table('user_islands')->updateOrInsert(
                    ['user_id' => $UserId, 'island_id' => $island, 'island_code' => $islandCode]
                );
            }
        }
        return true;
    }
    private function createLocalvendorSeller($UserId, $sellers = [])
    {
        $today = date('Y-m-d H:i:s');
        foreach ($sellers as $key => $seller) {
            DB::table('localvendor_sellers')->updateOrInsert(
                ['user_id' => $UserId,'seller_id' => $seller]
            );
        }
        return true;
    }
    private function deleteUserIsland($userId)
    {
        UserIsland::where('user_id', $userId)->forceDelete();
    }
    private function deleteLocalVendorSeller($userId)
    {
        LocalvendorSeller::where('user_id', $userId)->forceDelete();
    }
}
