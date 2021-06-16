<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Spatie\Permission\Models\Permission;
use App\UserActivity;

// use Yajra\DataTables\Contracts\DataTable;

class UserActivityController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:activity-log');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $activities = UserActivity::all();
        if ($request->ajax()) {
            return DataTables::of($activities)
                ->addIndexColumn()
                ->addColumn('action-btn', function ($row) {
                    $btn = '<a href="javascript:void(0)" id="deleteData"  data-toggle="tooltip"  data-id="' . $row->id . '" data-original-title="Delete" class="btn btn-dt-delete">削除</a>';
                    return $btn;
                })
                ->rawColumns(['action-btn'])
                ->make(true);
        }
        return view('admin.users.user-activities');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\UserActivity  $userActivity
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        UserActivity::find($id)->delete();
        return response()->json(['success' => 'Category deleted successfully.']);
    }
}