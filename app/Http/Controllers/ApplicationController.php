<?php

namespace App\Http\Controllers;

use App\Directory;

class ApplicationController extends Controller
{
    protected function getDirectoryHierarchy($pid = 0)
    {
        if ($pid > 0) {
            $directoriesLOneTwo = Directory::where('id', $pid)->where('deleted_at', null)->where('name', '!=', 'unassigned')->orderBy('order')->orderBy('id')->with('children')->get()->toArray();
        } else {
            $directoriesLOneTwo = Directory::where('parent_id', $pid)->where('deleted_at', null)->where('name', '!=', 'unassigned')->orderBy('order')->orderBy('id')->with('children')->get()->toArray();
        }


        if (isset($directoriesLOneTwo)) {
            foreach ($directoriesLOneTwo as $lbl1 => $dirs) {
                if (isset($dirs['children']) && !empty($dirs['children'])) {
                    $childrens = $dirs['children'];
                    foreach ($childrens as $lbl2 => $children) {
                        $children['children'] = Directory::where('parent_id', $children['parent_id'])->where('deleted_at', null)->where('name', '!=', 'unassigned')
                            ->orderBy('order')->orderBy('id')->with('children')->get()->toArray();

                        $directoriesLOneTwo[$lbl1]['children'] = $children['children'];
                    }
                }
            }
        }

        return $directoriesLOneTwo;
    }

    protected function getLinearDirectoryHierarchy()
    {
        $datalabels = $this->getDirectoryHierarchy();
        $labelOneData = [];
        foreach ($datalabels as $l1 => $stage1) {
            $newArrayData = array(
                'id' => $stage1['id'],
                'name' => $stage1['name']
            );
            $stages2 = $stage1['children'];
            array_push($labelOneData, $newArrayData);
            if (isset($stages2) && !empty($stages2)) {
                foreach ($stages2 as $l2 => $stage2) {
                    $newArrayData = array(
                        'id' => $stage2['id'],
                        'name' => $stage1['name'] . "/" . $stage2['name']
                    );
                    $stages3 = $stage2['children'];
                    array_push($labelOneData, $newArrayData);
                    if (isset($stages3) && !empty($stages3)) {
                        foreach ($stages3 as $l3 => $stage3) {
                            $newArrayData = array(
                                'id' => $stage3['id'],
                                'name' => $stage1['name'] . "/" . $stage2['name'] . "/" . $stage3['name']
                            );
                            array_push($labelOneData, $newArrayData);
                        }
                    }
                }
            }
        }

        return $labelOneData;
    }
}
