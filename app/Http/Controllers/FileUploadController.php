<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Intervention\Image\ImageManagerStatic as Image;

class FileUploadController extends Controller
{
    private $temp;
    public function __construct()
    {
        $this->temp = 'temp';
        $this->checkDir($this->temp);
    }
    public function upload(Request $request)
    {
        $time = date('YmdHis').rand(10000,99999);
        request()->validate([
            'image' => 'required|mimes:jpeg,png,jpg,gif,svg|max:607200',
            'image_id' => 'required|numeric'
        ], ['image' => trans('product.cover_image'),]);

        $file = $request->file('image');
        $params['original_name'] = $file->getClientOriginalName();
        $params['extension'] = $file->getClientOriginalExtension();
        $params['filesize'] = $file->getSize();
        $params['image_id'] =  $request->input('image_id');
        $destinationPath = $this->temp;
        if($params['image_id'] > 9 && $params['image_id'] <=15){
            $params['file_name'] = $time.'.'.$params['extension'];
            $params['file_name_md'] = $time.'_md=400x400.'.$params['extension'];
            $params['file_name_sm'] = $time.'_sm=400x400.'. $params['extension'];
        } else{
            $params['file_name'] = $time.'.'.$params['extension'];
            $params['file_name_md'] = $time.'_md=294x350.'.$params['extension'];
            $params['file_name_sm'] = $time.'_sm=116x132.'. $params['extension'];
        }

        $file->move($destinationPath . "/", $params['file_name']);
        //$file->move($destinationPath . "/", $params['file_name_md']);
        //$file->move($destinationPath . "/", $params['file_name_sm']);

        $params['path'] = URL::to('/') . "/" . $this->temp . "/" . $params['file_name'];
        $filePath = "public/" . $this->temp . "/"; 
        if($params['image_id'] > 9 && $params['image_id'] <=15){
            $image = Image::make($destinationPath . "/" . $params['file_name'])->resize(1200, 1200, function ($aspect) {
                $aspect->aspectRatio();
            })->encode($params['extension']);
            $image_md = Image::make($destinationPath . "/" . $params['file_name'])->resize(400, 400, function ($aspect) {
                $aspect->aspectRatio();
            })->encode($params['extension']);
            $image_sm = Image::make($destinationPath . "/" . $params['file_name'])->resize(400, 400, function ($aspect) {
                $aspect->aspectRatio();
            })->encode($params['extension']);
        }else{
            $image = Image::make($destinationPath . "/" . $params['file_name'])->encode($params['extension']);
            $image_md = Image::make($destinationPath . "/" . $params['file_name'])->resize(294, 350, function ($aspect) {
                $aspect->aspectRatio();
            })->encode($params['extension']);
            $image_sm = Image::make($destinationPath . "/" . $params['file_name'])->resize(116, 132, function ($aspect) {
                $aspect->aspectRatio();
            })->encode($params['extension']);
        } 
        Storage::disk('s3')->put($filePath . $params['file_name'], (string) $image);
        Storage::disk('s3')->put($filePath . $params['file_name_md'], (string) $image_md);
        Storage::disk('s3')->put($filePath . $params['file_name_sm'], (string) $image_sm);
        $params['s3'] = asset($destinationPath . "/" . $params['file_name']);

        return response()
            ->json($params)
            ->withCallback($request->input('callback'));
    }

    public function checkDir($folderUrl)
    {
        if (!is_dir($folderUrl)) {
            mkdir($folderUrl, 0777);
        }
    }
}
