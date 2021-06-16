<?php

namespace App\Http\Controllers;

use App\Directory;
use App\Page;
use Illuminate\Support\Facades\Auth;
use Session;
use Illuminate\Support\Facades\Response;
use Redirect;

class PagesController extends ApplicationController
{
    /**
     * Get page details
     */
    public function getPageDetails($slug = "/")
    {
        $urlMap = "index";
        list($root, $slugString) = explode('/page', request()->getPathInfo(), 2);
        $page = [];
        $directoryId = config('constants.DIRECTORY_ROOT')['id'];
        if ($slugString == "") {
            abort(404);
        }
        $allowedStatus=[1];
        if (Auth::check()) {
            $allowedStatus=[1, 2];
        }
        $dirList = explode('/', $slugString);
        if ($slugString == "/" || $slugString == "/index") {
            $page = Page::where('directory_id', $directoryId)
                        ->where('url_map', 'index')
                        ->whereIn('is_active', $allowedStatus)
                        ->whereNull('deleted_at')
                        ->where(function ($q) {
                            $q->where('publishing_date', '<=', date("Y-m-d"))
                              ->orWhereNull('publishing_date');
                        })
                        ->where(function ($q) {
                            $q->where('publishing_end_date', '>=', date("Y-m-d"))
                              ->orWhereNull('publishing_end_date');
                        })
                        ->first();
        } elseif (count($dirList) == 2) {
            $page = Page::where('directory_id', $directoryId)
                        ->where('url_map', $dirList[1])
                        ->whereIn('is_active', $allowedStatus)
                        ->whereNull('deleted_at')
                        ->where(function ($q) {
                            $q->where('publishing_date', '<=', date("Y-m-d"))
                              ->orWhereNull('publishing_date');
                        })
                        ->where(function ($q) {
                            $q->where('publishing_end_date', '>=', date("Y-m-d"))
                              ->orWhereNull('publishing_end_date');
                        })
                        ->first();
        } elseif (count($dirList) >= 3 && count($dirList) <= 5) {
            $page = $this->validatePageURL($dirList);
        } else { 
            $res = array( 
                'message' => trans('error.error_404'), 
            ); 
            Session::flash('message_danger', $res['message']); 
            return Redirect::back();
        }
        if (empty($page)) { 
            $res = array( 
            'message' => trans('error.error_404'), 
            ); 
        Session::flash('message_danger', $res['message']); 
        return Redirect::back();
        }

        return view('admin.pages.page-details', compact('page'));
    }

    private function validatePageURL($dirList)
    {
        $allowedStatus=[1];
        if (Auth::check()) {
            $allowedStatus=[1, 2];
        }
        $exists = false;
        $linearDirectories = $this->getLinearDirectoryHierarchy();
        $total = count($dirList);
        $dirName = $dirList[$total - 2];
        $pageMapUrl = $dirList[$total - 1];
        $dirListWithoutPage =  $dirList;
        unset($dirListWithoutPage[0]);
        unset($dirListWithoutPage[$total - 1]);
        $directory = Directory::where('name', $dirName)->first();
        $linearDirectoryHierarchyFromURL = implode("/", $dirListWithoutPage);

        if (empty($directory)) {
            abort(404);
        }
        foreach ($linearDirectories as $linearDirectory) {
            if ($linearDirectory['name'] == $linearDirectoryHierarchyFromURL) {
                $exists = true;
                break;
            }
        }
        if (!$exists) {
            abort(404);
        }
        if ($pageMapUrl == '' || $pageMapUrl == 'index') {
            $page = Page::where('directory_id', $directory->id)
                        ->where('url_map', 'index')
                        ->whereIn('is_active', $allowedStatus)
                        ->whereNull('deleted_at')
                        ->where(function ($q) {
                            $q->where('publishing_date', '<=', date("Y-m-d"))
                              ->orWhereNull('publishing_date');
                        })
                        ->where(function ($q) {
                            $q->where('publishing_end_date', '>=', date("Y-m-d"))
                              ->orWhereNull('publishing_end_date');
                        })
                        ->first();
        } else {
            $page = Page::where('directory_id', $directory->id)
                        ->where('url_map', $pageMapUrl)
                        ->whereIn('is_active', $allowedStatus)
                        ->whereNull('deleted_at')
                        ->where(function ($q) {
                            $q->where('publishing_date', '<=', date("Y-m-d"))
                              ->orWhereNull('publishing_date');
                        })
                        ->where(function ($q) {
                            $q->where('publishing_end_date', '>=', date("Y-m-d"))
                              ->orWhereNull('publishing_end_date');
                        })
                        ->first();
        }

        return $page;
    }
}
