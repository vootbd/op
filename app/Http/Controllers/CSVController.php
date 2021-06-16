<?php

namespace App\Http\Controllers;

use App\AllergyIndications;
use App\CsvSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Island;
use App\Category;
use App\Product;
use App\User;
use GuzzleHttp\Handler\Proxy;
use Validator;



class CSVController extends Controller
{
    const CONTROL_COL_NAME = 'コントロールカラム';
    const INTERNAL_DS=';';
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            if ($user->hasRole('operator') === false) {
                return abort(401);
            }
            return $next($request);
        });
    }

    /**
     * Get csv Product by fields
     */
    public function csvProduct()
    {
        $productFields = CsvSetting::where('type', 'product')->orderBy('id')->get();
        return view('admin.csvs.csv_product', compact('productFields'));
    }

    /**
     * Get csv remote island by fields
     */
    public function csvIsland()
    {
        $islandFields = CsvSetting::where('type', 'remote_island')->orderBy('id')->get();
        return view('admin.csvs.csv_island', compact('islandFields'));
    }

    /**
     * Get csv Category by fields
     */
    public function csvCategory()
    {
        $categoryFields = CsvSetting::where('type', 'category')->orderBy('id')->get();
        return view('admin.csvs.csv_category', compact('categoryFields'));
    }

    /**
     * Get csv settings by type
     */

    public function getCSVSettingsByType()
    {
        $type = "remote_island";
        $data['not_in_output'] = DB::table('csv_setting_user')
                                        ->join('users', 'users.id', '=', 'csv_setting_user.user_id')
                                        ->join('csv_settings', 'csv_settings.id', '=', 'csv_setting_user.csv_setting_id')
                                        ->select('csv_settings.type','csv_settings.column_name','csv_settings.column_label','csv_settings.column_description','csv_setting_user.*')
                                        ->where('status', 1)
                                        ->where('user_id', auth()->id())
                                        ->where('type', $type)
                                        ->where('csv_setting_user.in_output', 0)
                                        ->get();
        $data['in_output'] = DB::table('csv_setting_user')
                                        ->join('users', 'users.id', '=', 'csv_setting_user.user_id')
                                        ->join('csv_settings', 'csv_settings.id', '=', 'csv_setting_user.csv_setting_id')
                                        ->select('csv_settings.type','csv_settings.column_name','csv_settings.column_label','csv_settings.column_description','csv_setting_user.*')
                                        ->where('status', 1)
                                        ->where('user_id', auth()->id())
                                        ->where('type', $type)
                                        ->where('csv_setting_user.in_output', 1)
                                        ->orderBy('order', 'ASC')
                                        ->get();

        return Response::json($data);
    }

    /**
     * Update list and their heirarchy
     */

    public function updateSettings(Request $request)
    {

        $datas = $request->input('listData');
        $sortable = $request->input('sortable');
        $type = $request->input('type');
        $notInOutput =  DB::table('csv_setting_user')
                            ->join('users', 'users.id', '=', 'csv_setting_user.user_id')
                            ->join('csv_settings', 'csv_settings.id', '=', 'csv_setting_user.csv_setting_id')
                            ->select('csv_settings.type','csv_settings.column_name','csv_settings.column_label','csv_settings.column_description','csv_setting_user.*')
                            ->where('status', 1)
                            ->where('user_id', auth()->id())
                            ->where('type', $type)
                            ->where('csv_setting_user.in_output', 0)
                            ->pluck('order')
                            ->toArray();
        $availableIndex = [];

        for ($i = 0; $i < count($datas); $i++) {
            if (!in_array($i, $notInOutput)) {
                $availableIndex[] = $i;
            }
        }
        foreach ($datas as $key => $data) {
            if ($sortable == true && $data['in_output'] == 1) {
                $updatedData = [
                    'order' => isset($availableIndex[$key]) ? $availableIndex[$key] : $key,
                    'in_output' => $data['in_output']
                ];
            } else {
                $updatedData = [
                    'in_output' => $data['in_output']
                ];
            }
            DB::table('csv_setting_user')
                ->select('csv_setting_user.*')
                //->where('user_id', auth()->id())
                ->where('id', $data['id'])
                ->update($updatedData);
        }
        $res = array(
            'success' => true,
            'message' => trans('csv.settings_update'),
            'rs_class' => 'success',
            'sortable' => $sortable,
            'data' => [
                'not_in_output' => DB::table('csv_setting_user')
                                    ->join('users', 'users.id', '=', 'csv_setting_user.user_id')
                                    ->join('csv_settings', 'csv_settings.id', '=', 'csv_setting_user.csv_setting_id')
                                    ->select('csv_settings.type','csv_settings.column_name','csv_settings.column_label','csv_settings.column_description','csv_setting_user.*')
                                    ->where('status', 1)
                                    ->where('user_id', auth()->id())
                                    ->where('type', $type)
                                    ->where('csv_setting_user.in_output', 0)
                                    ->get(),
                'in_output' => DB::table('csv_setting_user')
                                    ->join('users', 'users.id', '=', 'csv_setting_user.user_id')
                                    ->join('csv_settings', 'csv_settings.id', '=', 'csv_setting_user.csv_setting_id')
                                    ->select('csv_settings.type','csv_settings.column_name','csv_settings.column_label','csv_settings.column_description','csv_setting_user.*')
                                    ->where('status', 1)
                                    ->where('user_id', auth()->id())
                                    ->where('type', $type)
                                    ->where('csv_setting_user.in_output', 1)
                                    ->orderBy('order', 'ASC')
                                    ->get()
            ]
        );
        return Response::json($res);
    }

    protected function colHeaderJpToEn($csvType = 'remote_island')
    {
        $settings[self::CONTROL_COL_NAME] = 'control';
        $data = DB::table('csv_setting_user')
                    ->join('users', 'users.id', '=', 'csv_setting_user.user_id')
                    ->join('csv_settings', 'csv_settings.id', '=', 'csv_setting_user.csv_setting_id')
                    ->select('csv_settings.type','csv_settings.column_name','csv_settings.column_label','csv_settings.column_description','csv_setting_user.*')
                    ->where('status', 1)
                    ->where('type', $csvType)
                    ->where('csv_settings.deleted_at', null)
                    ->pluck('column_name', 'column_label')
                    ->toArray();
        $settings = array_merge($settings, $data);
        return $settings;
    }
    
    protected function jpToEnKeyConvert($mapHeader, $item)
    {
        $insert = [];
        foreach ($mapHeader as $key => $header) {
            if (isset($item[$key])) {
                $insert[$header] = _trim($item[$key]);
            }else{
                $insert[$header] = "";
            }
        }

        if(isset($insert['control'])){
            unset($insert['control']);
        }

        return $insert;
    }
    
    public function csvControl($type)
    {
        $data['not_in_output'] = DB::table('csv_setting_user')
            ->join('users', 'users.id', '=', 'csv_setting_user.user_id')
            ->join('csv_settings', 'csv_settings.id', '=', 'csv_setting_user.csv_setting_id')
            ->select('csv_settings.type','csv_settings.column_name','csv_settings.column_label','csv_settings.column_description','csv_setting_user.*')
            ->where('status', 1)
            ->where('user_id', auth()->id())
            ->where('type', $type)
            ->where('csv_setting_user.in_output', 0)
            ->get();
        $data['in_output'] = DB::table('csv_setting_user')
            ->join('users', 'users.id', '=', 'csv_setting_user.user_id')
            ->join('csv_settings', 'csv_settings.id', '=', 'csv_setting_user.csv_setting_id')
            ->select('csv_settings.type','csv_settings.column_name','csv_settings.column_label','csv_settings.column_description','csv_setting_user.*')
            ->where('user_id', auth()->id())
            ->where('status', 1)
            ->where('type', $type)
            ->where('csv_setting_user.in_output', 1)
            ->orderBy('order', 'ASC')
            ->orderBy('id')
            ->get();

        return view('admin.csvs.csv_control', compact('data', 'type'));
    }



    protected function appendError($controll = [], $action, $message, $messageDetails, $row, $rowNumber = '')
    {
        $error = [
            'rowNumber' => $rowNumber,
            'action' => $action,
            'message' => $message,
            'reason' => $messageDetails,
            'data' => $row

        ];

        array_push($controll, $error);

        return $controll;
    }

    protected function productData($data)
    {
        $product = [];
        if (isset($data['island_id'])) {
            $product['island_id'] = _trim($data['island_id']);
        }
        if (isset($data['seller_id'])) {
            $product['seller_id'] = _trim($data['seller_id']);
        }
        if (isset($data['status'])) {
            $product['status'] = _trim($data['status']);
        }
        if (isset($data['name'])) {
            $product['name'] = _trim($data['name']);
        }
        if (isset($data['product_explanation'])) {
            $product['product_explanation'] = _trim($data['product_explanation']);
        }
        if (isset($data['category_id'])) {
            $product['category_id'] = _trim($data['category_id']);
        }
        if (isset($data['price'])) {
            $product['price'] = _trim($data['price']);
        }
        if (isset($data['tax'])) {
            $product['tax'] = _trim($data['tax']);
        }
        if (isset($data['url'])) {
            $product['url'] = _trim($data['url']);
        }
        if (isset($data['price']) && isset($data['tax'])) {
            if (_trim($data['tax']) == "") {
                $data['tax'] = 0;
            }
            if (_trim($data['price']) == "") {
                $data['price'] = 0;
            }
            $product['sell_price'] = $data['price'] * (1 + ($data['tax'] / 100));
        } else if (isset($data['price'])) {
            $product['sell_price'] = _trim($data['price']);
        }


        if (isset($data['shipment_method'])) {
            $product['shipment_method'] = _trim($data['shipment_method']);
        }
        if (isset($data['preservation_method'])) {
            $product['preservation_method'] = _trim($data['preservation_method']);
        }
        if (isset($data['package_type'])) {
            $product['package_type'] = _trim($data['package_type']);
        }
        if (isset($data['ecmall_sku'])) {
            $product['ecmall_sku'] = _trim($data['ecmall_sku']);
        }
        if (isset($data['quality_retention_temperature'])) {
            $product['quality_retention_temperature'] = _trim($data['quality_retention_temperature']);
        }
        if (isset($data['expiration_taste_quality'])) {
            $product['expiration_taste_quality'] = _trim($data['expiration_taste_quality']);
        }
        if (isset($data['use_scene'])) {
            $product['use_scene'] = _trim($data['use_scene']);
        }
        if (isset($data['created_at'])) {
            $product['created_at'] = _trim($data['created_at']);
        }
        if (isset($data['updated_at'])) {
            $product['updated_at'] = _trim($data['updated_at']);
        }



        if (isset($data['cover_image'])) {
            $product['cover_image'] = _trim($data['cover_image']);
            $product['cover_image_sm'] = _trim($data['cover_image_sm']);
            $product['cover_image_md'] = _trim($data['cover_image_md']);
        } else {
            $product['cover_image'] = 'default.png';
            $product['cover_image_sm'] = 'default.png';
            $product['cover_image_md'] = 'default.png';
        }

        return $product;
    }

    protected function productAllergyIndicationObligation($data)
    {
        $allergy = [];
        $ids = [];
        $obligation = [];
        $recommended = [];
        $errorMessage = "";
        if (isset($data['allergy_display_obligation']) && !empty($data['allergy_display_obligation'])) {
            $obligation = explode(self::INTERNAL_DS, _trim($data['allergy_display_obligation']));
        }
        if (isset($data['allergy_display_recommended']) && !empty($data['allergy_display_recommended'])) {
            $recommended = explode(self::INTERNAL_DS, _trim($data['allergy_display_recommended']));
        }

        $allergy = array_merge($obligation, $recommended);
        if (count($allergy) > 0) {
            $ids = AllergyIndications::whereIn('name', $allergy)->pluck('id')->toArray();
            if (count($allergy) !== count($ids)) {
                $errorMessage = trans('csv.allergy_item_not_registered');
            }
        }
        return [
            'ids' => $ids,
            'errorMessage' => $errorMessage
        ];
    }

    protected function productAdditionalInfo($data)
    {
        $info = [];
        if (isset($data['assumed_customer_information'])) {
            $info[] = _trim($data['assumed_customer_information']);
        } else {
            $info[] = NULL;
        }
        if (isset($data['number_of_inputs_per_case'])) {
            $info[] = _trim($data['number_of_inputs_per_case']);
        } else {
            $info[] = NULL;
        }

        if (isset($data['largest_smallest_case_delivery_unit_maximum'])) {
            $in[] = _trim($data['largest_smallest_case_delivery_unit_maximum']);
        } else {
            $in[] = "";
        }

        if (isset($data['largest_smallest_case_delivery_unit_minimum'])) {
            $in[] = _trim($data['largest_smallest_case_delivery_unit_minimum']);
        } else {
            $in[] = "";
        }

        $info[] = implode('/', $in);

        if (isset($data['contents_unit_description'])) {
            $info[] = _trim($data['contents_unit_description']);
        } else {
            $info[] = NULL;
        }

        if (isset($data['case_size_and_weight_verticle'])) {
            $size[] = _trim($data['case_size_and_weight_verticle']);
        } else {
            $size[] = "";
        }
        if (isset($data['case_size_and_weight_horizontal'])) {
            $size[] = _trim($data['case_size_and_weight_horizontal']);
        } else {
            $size[] = "";
        }
        if (isset($data['case_size_and_weight_height'])) {
            $size[] = _trim($data['case_size_and_weight_height']);
        } else {
            $size[] = "";
        }
        if (isset($data['case_size_and_weight_width'])) {
            $size[] = _trim($data['case_size_and_weight_width']);
        } else {
            $size[] = "";
        }

        $info[] = implode('/', $size);

        if (isset($data['order_lead_time'])) {
            $info[] = _trim($data['order_lead_time']);
        } else {
            $info[] = NULL;
        }

        if (isset($data['jan_code'])) {
            $info[] = _trim($data['jan_code']);
        } else {
            $info[] = NULL;
        }

        if (isset($data['available_time'])) {
            $info[] = _trim($data['available_time']);
        } else {
            $info[] = NULL;
        }
        if (isset($data['product_story_feelings_of_the_creator'])) {
            $info[] = _trim($data['product_story_feelings_of_the_creator']);
        } else {
            $info[] = NULL;
        }
        if (isset($data['product_features'])) {
            $info[] = _trim($data['product_features']);
        } else {
            $info[] = NULL;
        }

        return $info;
    }

    protected function getCreateDate($mappedData, $action = 'n')
    {

        if (!isset($mappedData['created_at']) || (isset($mappedData['created_at']) && empty($mappedData['created_at']))) {
            $mappedData['created_at'] = ($action == 'n') ? date('Y-m-d H:i:s') : '';
        }

        if (!empty($mappedData['created_at'])) {
            $mappedData['created_at'] = date('Y-m-d H:i:s', strtotime($mappedData['created_at']));
        }

        return _trim($mappedData['created_at']);
    }

    protected function getUpdateDate($mappedData, $createdAt = "")
    {
        if (!isset($mappedData['updated_at']) || (isset($mappedData['updated_at']) && empty($mappedData['updated_at']))) {
            $mappedData['updated_at'] =  ($createdAt == "") ? date('Y-m-d') : $createdAt;
        }

        if (!empty($mappedData['updated_at'])) {
            $mappedData['updated_at'] = date('Y-m-d H:i:s', strtotime($mappedData['updated_at']));
        }

        return _trim($mappedData['updated_at']);
    }

    protected function validateCSVHeader($header,$noOfCol){
        return (!isset($header) || empty($header) ||  !is_array($header) || (count($header) != $noOfCol) || isset($header[0]) && $header[0] !=self::CONTROL_COL_NAME);
    }

    protected function validateCSVTranslatedHeader($header,$headerEn){
        return (!isset($headerEn) || empty($headerEn) ||  !is_array($headerEn) || (count($headerEn) != count($header)));
    }
  
}
