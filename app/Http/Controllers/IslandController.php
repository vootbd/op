<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use App\Island;
use App\Prefecture;
use App\ProductIsland;
use Validator;
use App\Rules\UrlValidator;
use App\Rules\LatLongValidator;
use App\helpers;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use App\Rules\ValidatePublishingEndDate; 
use Session;
use Carbon\Carbon;

class IslandController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
         $this->middleware('permission:island-list|island-create|island-delete', ['only' => ['index']]);
         $this->middleware('permission:island-create', ['only' => ['create', 'store']]);
         $this->middleware('permission:island-edit', ['only' => ['edit', 'update']]);
         $this->middleware('permission:island-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */ 
    public function index(Request $request){ 
        $prefectures = Prefecture::orderBy('id','asc')->pluck('name', 'id');
        if ($request->ajax()) {  
            $start = $request->get('start');
            $length = $request->get('length'); 
            $orderColumn = $request->input("order");
            $columns = $request->input("columns"); 
            $keyword = $request->query("search_keword");
            $prefecture = $request->query("search_prefecture");             
            $getOrderColumn = $columns[$orderColumn[0]["column"]]["data"];
            $orderColumnName = ($getOrderColumn == 'prefecture_name') ? 'prefecture_id': $getOrderColumn;
            $orderingStyle = $orderColumn[0]["dir"];
            $countSearch = -1; 
            $islands = DB::table('islands')
                ->join('prefectures', 'prefectures.id', '=', 'islands.prefecture_id')
                ->select('islands.name', 'islands.id', 'islands.prefecture_id as prefecture', 'prefectures.name AS prefecture_name' , 'prefectures.id AS prefecture_id')
                ->where('islands.deleted_at', null);
            $count = Island::all()->count();  
            //*******************  Search query start **************/    
            if (isset($keyword)) {
                $searchValue = $keyword;
                $islands = $islands->where(function ($q) use ($keyword) {
                    return $q->where('islands.name', 'LIKE', "%$keyword%");
                });
                $countSearch = $islands->count();
            } 
            if (isset($prefecture)) {
                $islands = $islands->where('prefecture_id', $prefecture);
                $count = $islands->count();
            } 
            //*******************  Search query end **************/  
            if ($orderColumnName == 'prefecture_id') { 
                $islands = $islands->orderBy('prefecture_name', 'DESC')
                    ->orderBy('prefecture_id', $orderingStyle) 
                    ->orderBy('islands.id', 'DESC')
                    ->skip($start)
                    ->take($length)->get();
            } else {
                $islands = $islands
                    ->orderBy($orderColumnName, $orderingStyle)
                    ->skip($start)
                    ->take($length)->get();
            }
            if ($countSearch < 0) {
                $countSearch = $count;
            }
            $dataTable =  DataTables::of($islands)
                ->addIndexColumn()
                ->with([
                    "recordsTotal"    => $count,
                    "from"    => $start+1,
                    "to"    => (count($islands) < $length)?($start + count($islands)):($start + $length),
                    "recordsFiltered" => $countSearch,
                    'order' => 'prefecture_id'
                ])  
                ->skipPaging()
                ->make(true);

            return $dataTable;
        }

        return view('admin.islands.index',compact('prefectures'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $prefecture = Prefecture::Select('id','name')->orderBy('id','ASC')->get();
        return view('admin.islands.create',compact('prefecture'));
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
            'prefecture_id' => 'required',
            'name' => 'required|max:40',
            'code' => 'required|max:5|unique:islands,code',
            'jurisdiction' => 'max:255',
            'autonomous_code' => 'max:255'
        ], [
            'name.max' => trans('island.max_char'),
            'code.unique' => trans('island.unique_code'),
            'code.max' => trans('island.code_max'),
            'jurisdiction.max' => trans('error.max_char'),
            'autonomous_code.max' => trans('error.max_char')
        ]);
        $data = $request->all();
        $data['created_by'] = $user->id;
        $data['updated_by'] = $user->id;

        $island = Island::create($data);
        // user activity log
        createUserActivity($request, '作成する', $user->name.'<'.$user->email.'> 作成する '.$island->name.' 島', '一般的な', null);

        return redirect()->route('islands.index')
                        ->with('success',trans('island.create'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Island  $island
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = Island::find($id);
        $islandPref = Prefecture::find($data->prefecture_id);
        $prefecture = Prefecture::Select('id','name')->orderBy('id','ASC')->get();
        return view('admin.islands.edit', compact('data','prefecture','islandPref'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Island  $island
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $user = Auth::user();
        $this->validate($request, [
            'prefecture_id' => 'required',
            'name' => 'required|max:40',
            'code' => 'required|max:5|unique:islands,code,'. $id ,
            'jurisdiction' => 'max:255',
            'autonomous_code' => 'max:255'
        ], [
            'name.max' => trans('island.max_char'),
            'code.unique' => trans('island.unique_code'),
            'code.max' => trans('island.code_max'),
            'jurisdiction.max' => trans('error.max_char'),
            'autonomous_code.max' => trans('error.max_char')
        ]);
  

        
        $input = $request->all();
        $island = Island::find($id);
        $productIsland = ProductIsland::where('code', $island->code)->first();
        if ($productIsland){
            if($productIsland && !ProductIsland::where('code', $input['code'])->first()){
                // update record in Rito Portal
                $productIsland->code = $input['code'];
                $productIsland->save();
            }
         
        }
        $island['prefecture_id'] = $input['prefecture_id'];
        $island['updated_by'] = $user->id;
        $island->update($input);
        // user activity log
        createUserActivity($request, '更新', $user->name.'<'.$user->email.'> 更新 '.$island->name.' 島', '一般的な', null);
        
        return redirect()->route('islands.index')
                        ->with('success',trans('island.update'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Island  $island
     * @return \Illuminate\Http\Response
     */ 
    public function destroy(Request $request, $id)
    {
        $user = Auth::user();
        $ids = explode(",", $id);  
        Island::whereIn('id', $ids)->delete(); 
        createUserActivity($request, '削除する', $user->name.'<'.$user->email.'> 削除する '.'Island'.' 島', '一般的な', null);
        return response()->json(['message' => trans('island.delete')]); 
    }
}
