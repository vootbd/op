<?php

namespace App\Http\Controllers;

//If your CSV document was created or is read on a Macintosh computer, add the following lines before using the library to help PHP detect line ending.
if (!ini_get("auto_detect_line_endings")) {
    ini_set("auto_detect_line_endings", '1');
}

use App\AdditionalInformations;
use App\AllergyIndications;
use App\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Island;
use App\CsvSetting;
use App\Product;
use App\EcmallProducts;
use App\ProductAllergyIndication;
use App\ProductSalesDestination;
use App\CsvSettingsEcmall;
use App\LocalvendorSeller;
use App\LocalvendorEcmallId;
use App\Rules\ValidationRules;
use App\SalesDestination;
use Validator;
use Session;
use Response;
use Carbon\Carbon;
use Illuminate\Contracts\Cache\Store;
use League\Csv\Reader;
use League\Csv\Writer;
use Illuminate\Support\Facades\DB;

class CSVImportExportController extends CSVController
{
    //No of columns for import files of product, island or category. Matches header with input csv file
    const PRODUCT_CSV_NO_OF_COL = 48;
    const ISLAND_CSV_NO_OF_COL = 8;
    const CATEGORY_CSV_NO_OF_COL = 5;
    const ACTION_LIST = ['d', 'u', 'n'];
    private $additionalInfo;
    private $allergyRecommended;
    private $allergyNonRecommended;
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
     * d:delete
     * n:insert
     * u:update
     */
    public function importIslands(Request $request)
    {
        $rules = [
            'csv_file' => 'required|file|max:200|mimes:csv,txt,application/*'
        ];
        $message = [
            'csv_file.mimes' => trans('csv.mime_error'),
            'csv_file.max' => trans('csv.file_size_error')
        ];
        $validator = Validator::make($request->all(), $rules, $message);
        if ($validator->fails()) {
            session()->flash('warning', ['message' => $validator->errors()->first('csv_file'), 'downloadUrl' => '']);
            return redirect()->route('csvs.island');
        }
        $user = Auth::user();
        $errors['message'] = "";
        $errors['control'] = [];
        $path = $request->file('csv_file')->getRealPath();
        $csv = Reader::createFromPath($path, 'r');
        $csv->setHeaderOffset(0);

        $header = $csv->getHeader();
        $validHeader = $this->validateCSVHeader($header, self::ISLAND_CSV_NO_OF_COL);
        if ($validHeader) {
            session()->flash('warning', ['message' => trans('csv.header_error'), 'downloadUrl' => '']);
            return redirect()->route('csvs.island');
        }
        $headerEn = $this->colHeaderJpToEn('remote_island');
        $headerEnValid = $this->validateCSVTranslatedHeader($header, $headerEn);
        if ($headerEnValid) {
            session()->flash('warning', ['message' => trans('csv.header_error'), 'downloadUrl' => '']);
            return redirect()->route('csvs.island');
        }
        $records = $csv->getRecords();
        foreach ($records as $key => $row) {
            $rowNumber = $key + 1;
            if (count($row) !== self::ISLAND_CSV_NO_OF_COL || !isset($row[CSVController::CONTROL_COL_NAME])) {
                $errors['control'] = $this->appendError($errors['control'], $row[CSVController::CONTROL_COL_NAME], trans('csv.row_error'), trans('csv.line_break'), $row, $rowNumber);
                continue;
            }
            $action = isset($row[CSVController::CONTROL_COL_NAME]) ? $row[CSVController::CONTROL_COL_NAME] : '';
            if (!in_array($action, self::ACTION_LIST)) {
                $errors['control'] = $this->appendError($errors['control'], 'undefined', trans('csv.action_undefined'), trans('csv.action_undefined_details'), $row, $rowNumber);
                continue;
            }
            $mappedData = $this->jpToEnKeyConvert($headerEn, $row);
            try {
                if (!isset($mappedData['id'])) {
                    $errors['control'] = $this->appendError($errors['control'], $action, trans('csv.id_error'), trans('csv.id_error'), $row, $rowNumber);
                    continue;
                }
                $rowValidatorRules = ValidationRules::island($action, $mappedData);
                $rowValidator = Validator::make($mappedData, $rowValidatorRules['rules'], $rowValidatorRules['messages']);
                if ($action == 'd') {
                    if ($rowValidator->fails()) {
                        $errors['control'] = $this->appendError($errors['control'], $action, trans('csv.action_delete_failed'), $rowValidator->errors()->messages(), $row, $rowNumber);
                        continue;
                    }
                    $island = Island::where('id', $mappedData['id'])->where('deleted_at', null)->first();
                    if (isset($island->id)) {
                        Island::where('id', $mappedData['id'])->delete();
                    }
                } else if ($action == 'u') {
                    if ($rowValidator->fails()) {
                        $errors['control'] = $this->appendError($errors['control'], $action, trans('csv.action_update_failed'), $rowValidator->errors()->messages(), $row, $rowNumber);
                        continue;
                    }
                    $id = $mappedData['id'];
                    unset($mappedData['id']);
                    $mappedData['updated_by'] = $user->id;
                    $mappedData['created_at'] = $this->getCreateDate($mappedData, $action);
                    $mappedData['updated_at'] = $this->getUpdateDate($mappedData, $mappedData['created_at']);
                    if (empty($mappedData['created_at'])) {
                        unset($mappedData['created_at']);
                    }
                    Island::where('id', $id)->update($mappedData);
                } else if ($action == 'n') {
                    if ($rowValidator->fails()) {
                        $errors['control'] = $this->appendError($errors['control'], $action, trans('csv.action_insert_failed'), $rowValidator->errors()->messages(), $row, $rowNumber);
                        continue;
                    }
                    unset($mappedData['id']);
                    $mappedData['created_by'] = $user->id;
                    $mappedData['updated_by'] = $user->id;
                    $mappedData['created_at'] = $this->getCreateDate($mappedData, $action);
                    $mappedData['updated_at'] = $this->getUpdateDate($mappedData, $mappedData['created_at']);
                    Island::insert($mappedData);
                }
            } catch (\Exception $e) {
                $actionError = trans('csv.action_delete_failed');
                if ($action == 'n') {
                    $actionError = trans('csv.action_insert_failed');
                } else if ($action == 'u') {
                    $actionError = trans('csv.action_update_failed');
                }
                $errors['control'] = $this->appendError($errors['control'], $action, $actionError, $e->getMessage(), $row, $rowNumber);
            }
        }
        if (empty($errors['control'])) {
            session()->flash('message', trans('csv.csv_data_executed'));
        } else {
            $location = config('constants.CSV.FILE_PATH');
            $content = json_encode($errors['control']);
            $fileName = 'island_error_' . date("Y_m_d_H_i_s") . ".txt";
            $filePathName = asset('csvformat/' . $fileName);
            session()->flash('warning', ['message' => trans('csv.item_execute_failed'), 'downloadUrl' => $filePathName, 'fileName' => $fileName]);
            Storage::disk('s3')->put($location . $fileName, $content);
        }

        return redirect()->route('csvs.island');
    }

    public function exportIsland()
    {
        $headers = array(
            "Content-type" => "text/csv; charset=utf-8",
            "Content-Disposition" => "attachment; filename=islands.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );
        $csvSettings = CsvSetting::where('is_active', 1)->where('type', 'remote_island')->where('in_output', 1)->orderBy('order', 'ASC')->get();
        if ($csvSettings->isEmpty()) {
            session()->flash('warning', trans('csv.column_empty'));
            return redirect()->route('islands.index');
        }
        $columns = [];
        $columnsjs = [];
        foreach ($csvSettings as $csvSetting) {
            $columns[] = $csvSetting->column_name;
            $columnsjs[] = $csvSetting->column_label;
        }

        $islands = Island::where('deleted_at', null);
        foreach ($columns as $column) {
            $islands = $islands->addSelect($column);
        }
        $islands = $islands->get()->toArray();
        $csv = Writer::createFromString('');

        $csv->insertOne($columnsjs);
        $csv->insertAll($islands);
        $contents = $csv->getContent();

        return response()->streamDownload(function () use ($contents) {
            echo $contents;
        }, 'islands.csv', $headers);
    }


    public function importCategories(Request $request)
    {
        $rules = [
            'csv_file' => 'required|file|max:200|mimes:csv,txt,application/*'
        ];
        $message = [
            'csv_file.mimes' => trans('csv.mime_error'),
            'csv_file.max' => trans('csv.file_size_error')
        ];
        $validator = Validator::make($request->all(), $rules, $message);
        if ($validator->fails()) {
            session()->flash('warning', ['message' => $validator->errors()->first('csv_file'), 'downloadUrl' => '']);
            return redirect()->route('csvs.category');
        }
        $user = Auth::user();
        $errors['message'] = "";
        $errors['control'] = [];
        $path = $request->file('csv_file')->getRealPath();
        $csv = Reader::createFromPath($path, 'r');
        $csv->setHeaderOffset(0);

        $header = $csv->getHeader();
        $validHeader = $this->validateCSVHeader($header, self::CATEGORY_CSV_NO_OF_COL);
        if ($validHeader) {
            session()->flash('warning', ['message' => trans('csv.header_error'), 'downloadUrl' => '']);
            return redirect()->route('csvs.category');
        }
        $headerEn = $this->colHeaderJpToEn('category');
        $headerEnValid = $this->validateCSVTranslatedHeader($header, $headerEn);
        if ($headerEnValid) {
            session()->flash('warning', ['message' => trans('csv.header_error'), 'downloadUrl' => '']);
            return redirect()->route('csvs.category');
        }
        $records = $csv->getRecords();
        foreach ($records as $key => $row) {
            $rowNumber = $key + 1;
            if (count($row) !== self::CATEGORY_CSV_NO_OF_COL || !isset($row[CSVController::CONTROL_COL_NAME])) {
                $errors['control'] = $this->appendError($errors['control'], $row[CSVController::CONTROL_COL_NAME], trans('csv.row_error'), trans('csv.line_break'), $row, $rowNumber);
                continue;
            }
            $action = isset($row[CSVController::CONTROL_COL_NAME]) ? $row[CSVController::CONTROL_COL_NAME] : '';
            if (!in_array($action, self::ACTION_LIST)) {
                $errors['control'] = $this->appendError($errors['control'], 'undefined', trans('csv.action_undefined'), trans('csv.action_undefined_details'), $row, $rowNumber);
                continue;
            }
            $mappedData = $this->jpToEnKeyConvert($headerEn, $row);
            try {
                if (!isset($mappedData['id'])) {
                    $errors['control'] = $this->appendError($errors['control'], $action, trans('csv.id_error'), trans('csv.id_error'), $row, $rowNumber);
                    continue;
                }
                $rowValidatorRules = ValidationRules::category($action);
                $rowValidator = Validator::make($mappedData, $rowValidatorRules['rules'], $rowValidatorRules['messages']);
                if ($action == 'd') {
                    if ($rowValidator->fails()) {
                        $errors['control'] = $this->appendError($errors['control'], $action, trans('csv.action_delete_failed'), $rowValidator->errors()->messages(), $row, $rowNumber);
                        continue;
                    }
                    $category = Category::where('id', $mappedData['id'])->where('deleted_at', null)->first();
                    if (isset($category->id)) {
                        Category::where('id', $mappedData['id'])->delete();
                    }
                } else if ($action == 'u') {
                    if ($rowValidator->fails()) {
                        $errors['control'] = $this->appendError($errors['control'], $action, trans('csv.action_update_failed'), $rowValidator->errors()->messages(), $row, $rowNumber);
                        continue;
                    }
                    $id = $mappedData['id'];
                    unset($mappedData['id']);
                    $mappedData['updated_by'] = $user->id;
                    $mappedData['created_at'] = $this->getCreateDate($mappedData, $action);
                    $mappedData['updated_at'] = $this->getUpdateDate($mappedData, $mappedData['created_at']);
                    if (empty($mappedData['created_at'])) {
                        unset($mappedData['created_at']);
                    }
                    Category::where('id', $id)->update($mappedData);
                } else if ($action == 'n') {
                    if ($rowValidator->fails()) {
                        $errors['control'] = $this->appendError($errors['control'], $action, trans('csv.action_insert_failed'), $rowValidator->errors()->messages(), $row, $rowNumber);
                        continue;
                    }
                    unset($mappedData['id']);
                    $mappedData['created_by'] = $user->id;
                    $mappedData['updated_by'] = $user->id;
                    $mappedData['created_at'] = $this->getCreateDate($mappedData, $action);
                    $mappedData['updated_at'] = $this->getUpdateDate($mappedData, $mappedData['created_at']);
                    Category::insert($mappedData);
                }
            } catch (\Exception $e) {
                $actionError = trans('csv.action_delete_failed');
                if ($action == 'n') {
                    $actionError = trans('csv.action_insert_failed');
                } else if ($action == 'u') {
                    $actionError = trans('csv.action_update_failed');
                }
                $errors['control'] = $this->appendError($errors['control'], $action, $actionError, $e->getMessage(), $row, $rowNumber);
            }
        }
        if (empty($errors['control'])) {
            session()->flash('message', trans('csv.csv_data_executed'));
        } else {
            $location = config('constants.CSV.FILE_PATH');
            $content = json_encode($errors['control']);
            $fileName = 'category_error_' . date("Y_m_d_H_i_s") . ".txt";
            $filePathName = asset('csvformat/' . $fileName);
            session()->flash('warning', ['message' => trans('csv.item_execute_failed'), 'downloadUrl' => $filePathName, 'fileName' => $fileName]);
            Storage::disk('s3')->put($location . $fileName, $content);
        }

        return redirect()->route('csvs.category');
    }

    public function exportCategory()
    {
        $headers = array(
            "Content-type" => "text/csv; charset=utf-8",
            "Content-Disposition" => "attachment; filename=categories.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );
        $csvSettings = CsvSetting::where('is_active', 1)->where('type', 'category')->where('in_output', 1)->orderBy('order', 'ASC')->get();
        if ($csvSettings->isEmpty()) {
            session()->flash('warning', trans('csv.column_empty'));
            return redirect()->route('categories.index');
        }
        $columns = [];
        $columnsjs = [];
        foreach ($csvSettings as $csvSetting) {
            $columns[] = $csvSetting->column_name;
            $columnsjs[] = $csvSetting->column_label;
        }

        $categories = Category::where('deleted_at', null);
        foreach ($columns as $column) {
            $categories = $categories->addSelect($column);
        }
        $categories = $categories->get()->toArray();
        $csv = Writer::createFromString('');

        $csv->insertOne($columnsjs);
        $csv->insertAll($categories);
        $contents = $csv->getContent();

        return response()->streamDownload(function () use ($contents) {
            echo $contents;
        }, 'categories.csv', $headers);
    }

    public function importProducts(Request $request)
    {
        $rules = [
            'csv_file' => 'required|file|max:200|mimes:csv,txt,application/*'
        ];
        $message = [
            'csv_file.mimes' => trans('csv.mime_error'),
            'csv_file.max' => trans('csv.file_size_error')
        ];
        $validator = Validator::make($request->all(), $rules, $message);

        if ($validator->fails()) {
            session()->flash('warning', ['message' => $validator->errors()->first('csv_file'), 'downloadUrl' => '']);
            return redirect()->route('csvs.product');
        }
        $user = Auth::user();
        $errors['message'] = "";
        $errors['control'] = [];

        $path = $request->file('csv_file')->getRealPath();
        $csv = Reader::createFromPath($path, 'r');
        $csv->setHeaderOffset(0);
        $header = $csv->getHeader();
        $validHeader = $this->validateCSVHeader($header, self::PRODUCT_CSV_NO_OF_COL);
        if ($validHeader) {
            session()->flash('warning', ['message' => trans('csv.header_error'), 'downloadUrl' => '']);
            return redirect()->route('csvs.product');
        }

        $headerEn = $this->colHeaderJpToEn('product');
        $headerEnValid = $this->validateCSVTranslatedHeader($header, $headerEn);
        if ($headerEnValid) {
            session()->flash('warning', ['message' => trans('csv.header_error'), 'downloadUrl' => '']);
            return redirect()->route('csvs.product');
        }
        $records = $csv->getRecords();

        foreach ($records as $key => $row) {
            $rowNumber = $key + 1;
            if (count($row) !== self::PRODUCT_CSV_NO_OF_COL || !isset($row[CSVController::CONTROL_COL_NAME])) {
                $errors['control'] = $this->appendError($errors['control'], $row[CSVController::CONTROL_COL_NAME], trans('csv.row_error'), trans('csv.line_break'), $row, $rowNumber);
                continue;
            }
            $action = isset($row[CSVController::CONTROL_COL_NAME]) ? $row[CSVController::CONTROL_COL_NAME] : '';
            if (!in_array($action, self::ACTION_LIST)) {
                $errors['control'] = $this->appendError($errors['control'], 'undefined', trans('csv.action_undefined'), trans('csv.action_undefined_details'), $row, $rowNumber);
                continue;
            }
            $mappedData = $this->jpToEnKeyConvert($headerEn, $row);
            $mappedData['ecmall_shipping_weight']=floatval($mappedData['ecmall_shipping_weight']);
            $ecmall_seller_id = $mappedData['ecmall_seller_id'];
            unset($mappedData['ecmall_seller_id']);

            $product = $this->productData($mappedData);


            $allergy = $this->productAllergyIndicationObligation($mappedData);
            $additionInfo = $this->productAdditionalInfo($mappedData);
            try {
                if (!isset($mappedData['id'])) {
                    $errors['control'] = $this->appendError($errors['control'], $action, trans('csv.id_error'), trans('csv.id_error'), $row, $rowNumber);
                    continue;
                }
                $rowValidatorRules = ValidationRules::product($action);
                $rowValidator = Validator::make($mappedData, $rowValidatorRules['rules'], $rowValidatorRules['messages']);

                if ($action == 'd') {
                    if ($rowValidator->fails()) {
                        $errors['control'] = $this->appendError($errors['control'], $action, trans('csv.action_delete_failed'), $rowValidator->errors()->messages(), $row, $rowNumber);
                        continue;
                    }
                    $product = Product::where('id', $mappedData['id'])->where('deleted_at', null)->first();
                    if (isset($product->id)) {
                        if($product->ecmall_sku!=null){
                            $ecmall_product = EcmallProducts::where('product_id', $mappedData['id'])->delete();
                        }
                        Product::where('id', $mappedData['id'])->delete();
                    }
                } else if ($action == 'u') {
                    if ($rowValidator->fails()) {
                        $errors['control'] = $this->appendError($errors['control'], $action, trans('csv.action_update_failed'), $rowValidator->errors()->messages(), $row, $rowNumber);
                        continue;
                    }
                    $ecmall_sku = $mappedData['ecmall_sku'];
                    $id = $mappedData['id'];
                    if($ecmall_sku==null){
                        $product['ecmall_sku'] = '';
                        if (EcmallProducts::where('product_id', $id)->exists()) {
                            $ecmall_product = EcmallProducts::where('product_id', $id)->delete();
                        }
                    }
                    unset($mappedData['id']);
                    if (isset($product['id'])) {
                        unset($product['id']);
                    }
                    unset($product['cover_image']);
                    unset($product['cover_image_sm']);
                    unset($product['cover_image_md']);

                    $product['ecmall_sku'] = $ecmall_sku;
                    $product['updated_by'] = $user->id;
                    $product['created_at'] = $this->getCreateDate($mappedData, $action);
                    $product['updated_at'] = $this->getUpdateDate($mappedData, $mappedData['created_at']);
                    if (empty($mappedData['created_at'])) {
                        unset($product['created_at']);
                    }

                    if ($allergy['errorMessage'] != "") {
                        $errors['control'] = $this->appendError($errors['control'], 'n', trans('csv.action_insert_failed'), $allergy['errorMessage'], $row, $rowNumber);
                        continue;
                    }

                    Product::where('id', $id)->update($product);
                    ProductAllergyIndication::where('product_id', $id)->delete();
                    AdditionalInformations::where('product_id', $id)->delete();
                    if (isset($allergy['ids']) && count($allergy['ids']) > 0) {
                        $this->insertProductAllergy($id, $product, $allergy);
                    }

                    //update data in ecmall product table

                    if ($ecmall_sku!=null) {
                        $ecmall_product = EcmallProducts::where('product_id', '=', $id)->get()->first();
                        if ($ecmall_product) {
                            $ecmall_product->update($mappedData);
                            $ecmall_product->ecmall_sku = $mappedData['ecmall_sku'];
                            $ecmall_product['ecmall_temperature'] = $mappedData['ecmall_temperature'] == '' ? 'Ambient' : $mappedData['ecmall_temperature'];
                            $ecmall_product->created_by = $user->id;
                            $ecmall_product->updated_by = $user->id;
                            $ecmall_product->save();
                        }else {
                            $ecmall_product = new EcmallProducts($mappedData);

                            $ecmall_product->product_id = $id;
                            $ecmall_product->ecmall_sku = $mappedData['ecmall_sku'];
                            $ecmall_product['ecmall_temperature'] = $mappedData['ecmall_temperature'] == '' ? 'Ambient' : $mappedData['ecmall_temperature'];
                            $ecmall_product->created_by = $user->id;
                            $ecmall_product->updated_by = $user->id;
                            $ecmall_product->save();
                        }
                    }

                    $this->insertProductInfo($id, $product, $additionInfo);
                } else if ($action == 'n') {
                    if ($rowValidator->fails()) {
                        $errors['control'] = $this->appendError($errors['control'], $action, trans('csv.action_insert_failed'), $rowValidator->errors()->messages(), $row, $rowNumber);
                        continue;
                    }

                    unset($mappedData['id']);
                    $product['created_by'] = $user->id;
                    $product['updated_by'] = $user->id;
                    $product['ecmall_sku'] = $mappedData['ecmall_sku'];
                    $product['cover_image'] = '/upload/product/cover_image/default.jpg';
                    $product['cover_image_sm'] = '/upload/product/cover_image/sm/default_sm=116x132.jpg';
                    $product['cover_image_md'] = '/upload/product/cover_image/md/default_md=294x350.jpg';
                    $product['created_at'] = $this->getCreateDate($mappedData, $action);
                    $product['updated_at'] = $this->getUpdateDate($mappedData, $mappedData['created_at']);
                    if ($allergy['errorMessage'] != "") {
                        $errors['control'] = $this->appendError($errors['control'], 'n', trans('csv.action_insert_failed'), $allergy['errorMessage'], $row, $rowNumber);
                        continue;
                    }
                    $insertProduct = Product::insertGetId($product);
                    if ($insertProduct && count($allergy['ids']) > 0) {
                        $this->insertProductAllergy($insertProduct, $product, $allergy);
                    }
                    if ($insertProduct) {
                        $this->insertProductInfo($insertProduct, $product, $additionInfo);
                    }
                    if($product['ecmall_sku']!=null){
                        $ecmall_product = new EcmallProducts($mappedData);
                        $ecmall_product->product_id = $insertProduct;
                        $ecmall_product->ecmall_sku = $mappedData['ecmall_sku'];
                        $ecmall_product['ecmall_temperature'] = $mappedData['ecmall_temperature'] == '' ? 'Ambient' : $mappedData['ecmall_temperature'];
                        $ecmall_product->created_by = $user->id;
                        $ecmall_product->updated_by = $user->id;
                        $ecmall_localvendor_id = DB::table('localvendor_sellers')->select('user_id')->where('seller_id',$mappedData['seller_id'] )->pluck('user_id')->first();
                        $ecmall_product->save();
                    }
                }
            } catch (\Exception $e) {
                $actionError = trans('csv.action_delete_failed');
                if ($action == 'n') {
                    $actionError = trans('csv.action_insert_failed');
                } else if ($action == 'u') {
                    $actionError = trans('csv.action_update_failed');
                }
                $errors['control'] = $this->appendError($errors['control'], $action, $actionError, $e->getMessage(), $row, $rowNumber);
            }
        }
        if (empty($errors['control'])) {
            session()->flash('message', trans('csv.csv_data_executed'));
        } else {
            $location = config('constants.CSV.FILE_PATH');
            $content = json_encode($errors['control']);
            $fileName = 'product_error_' . date("Y_m_d_H_i_s") . ".txt";
            $filePathName = asset('csvformat/' . $fileName);
            session()->flash('warning', ['message' => trans('csv.item_execute_failed'), 'downloadUrl' => $filePathName, 'fileName' => $fileName]);
            Storage::disk('s3')->put($location . $fileName, $content);
        }
        return redirect()->route('csvs.product');
    }

    private function insertProductAllergy($productId, $product, $allergy)
    {
        foreach ($allergy['ids'] as $allergyId) {
            ProductAllergyIndication::insert([
                'product_id' => $productId,
                'allergy_indication_id' => $allergyId,
                'created_at' => $product['created_at'],
                'updated_at' => $product['updated_at']
            ]);
        }
    }

    private function insertProductInfo($productId, $product, $additionInfo)
    {
        foreach ($additionInfo as $info) {
            $data = [
                'product_id' => $productId,
                'description' => $info,
                'updated_at' => $product['updated_at']
            ];
            if (isset($product['created_at'])) {
                $data['created_at'] = $product['created_at'];
            }
            AdditionalInformations::insert($data);
        }
    }

    public function exportProduct(Request $request)
    {
        $headers = array(
            "Content-type" => "text/csv; charset=utf-8",
            "Content-Disposition" => "attachment; filename=products.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );
        $csvSettings = CsvSetting::where('is_active', 1)->where('type', 'product')->where('in_output', 1)->whereNotIn('column_description', ['product_allergy_indication', 'additional_informations'])->orderBy('order', 'ASC')->get();
        if ($csvSettings->isEmpty()) {
            session()->flash('warning', trans('csv.column_empty'));
            return redirect()->route('products.index');
        }
        $columns = [];
        $columnsjs = [];
        foreach ($csvSettings as $csvSetting) {
            $columns[] = $csvSetting->column_name;
            $columnsjs[] = $csvSetting->column_label;
        }
        /**
         * Filter data
         */
        $island = $request->input('remote_island');
        $status = $request->input('status');
        $startDate = $request->input('form-date');
        $endDate = $request->input('to-date');
        $seller = $request->input('producer');
        $keyword = $request->input('keyword');
        $category = $request->input('category');
        $productsList = Product::where('deleted_at', null);

        if (isset($startDate) && isset($endDate) && !empty($startDate) && !empty($endDate)) {
            $startDate = Carbon::parse($startDate);
            $endDate = Carbon::parse($endDate . ' 23:59:59');
            $productsList =   $productsList->whereBetween('created_at', [$startDate, $endDate]);
        }
        if (isset($island) && !empty($island)) {
            $productsList = $productsList->where('island_id', $island);
        }
        if (isset($status)) {
            $productsList = $productsList->where('status', $status);
        }

        if (isset($seller) && !empty($seller)) {
            $productsList = $productsList->where('seller_id', $seller);
        }
        if (isset($category) && !empty($category)) {
            $productsList = $productsList->where('category_id', $category);
        }
        if (isset($keyword) && !empty($keyword)) {
            $productsList = $productsList->search($keyword);
        }

        $productsList = $productsList->with('productAdditionalInformations')->with('productAllergyIndication');
        $productsList = $productsList->select('id', 'island_id', 'seller_id', 'status', 'name', 'product_explanation', 'category_id', 'price', 'tax', 'sell_price', 'url', 'shipment_method', 'preservation_method', 'package_type', 'quality_retention_temperature', 'expiration_taste_quality', 'use_scene', 'created_at', 'updated_at')->get();
        $getAdditionaMap = getAdditionalInformationFiledName();
        $productListsArr = $productsList->toArray();
        if (!isset($productListsArr) || count($productListsArr) == 0) {
            session()->flash('warning', trans('csv.column_empty'));
            return redirect()->route('products.index');
        }
        $newCsvProduct = [];
        try {
            foreach ($productListsArr as $key => $pData) {
                $this->additionalInfo = [];
                $this->allergyRecommended = [];
                $this->allergyNonRecommended = [];
                if (!isset($pData['product_additional_informations'])) {
                    $pData['product_additional_informations'] = [];
                }
                array_map(function ($a, $b) {
                    $this->additionalInfo[$a] =  isset($b) ? $b['description'] : '';
                    return true;
                }, $getAdditionaMap, $pData['product_additional_informations']);

                unset($pData['product_additional_informations']);

                if (!isset($pData['product_allergy_indication'])) {
                    $pData['product_allergy_indication'] = [];
                }

                foreach ($pData['product_allergy_indication'] as $pa) {
                    $allergyIndication = $this->getAllergyName($pa['allergy_indication_id']);
                    if (isset($allergyIndication) && !empty($allergyIndication)) {
                        if ($allergyIndication['is_recommended'] == 1) {
                            $this->allergyRecommended[] = $allergyIndication['name'];
                        } else {
                            $this->allergyNonRecommended[] = $allergyIndication['name'];
                        }
                    }
                }

                unset($pData['product_allergy_indication']);

                $pData = array_merge($pData, $this->additionalInfo);
                $pData['allergy_display_recommended'] = implode(CSVController::INTERNAL_DS . ' ', $this->allergyRecommended);
                $pData['allergy_display_obligation'] = implode(CSVController::INTERNAL_DS . ' ', $this->allergyNonRecommended);
                $newArra = [];
                foreach ($columns as $key) {
                    if (array_key_exists($key, $pData)) {
                        $newArray[$key] = $pData[$key];
                    }
                }
                array_push($newCsvProduct, $newArray);
            }
        } catch (\Exception $e) {
        }
        

        $csv = Writer::createFromString('');
        $csv->insertOne($columnsjs);
        $csv->insertAll($newCsvProduct);
        $contents = $csv->getContent();
        return response()->streamDownload(function () use ($contents) {
            echo $contents;
        }, 'products.csv', $headers);
    }

    private function getAllergyName($id)
    {
        $allergy = AllergyIndications::select('is_recommended', 'name')->where('id', $id)->where('is_active', 1)->where('deleted_at', null)->first()->toArray();

        return $allergy;
    }

    /**
     *Allergy FieldName Mapping for product info
     */
    private function getAllergyFieldNameMapping($productListsArr)
    {
        $finalResult = [];
        $arrLen = count($productListsArr);

        for ($i = 0; $i < $arrLen; $i++) {
            $finalResult['assumed_customer_information'] = !empty($productListsArr[$i]['assumed_customer_information']) ? $productListsArr[$i]['assumed_customer_information'] : '';

            $finalResult['number_of_inputs_per_case'] = !empty($productListsArr[$i]['number_of_inputs_per_case']) ? $productListsArr[$i]['number_of_inputs_per_case'] : '';

            $maxVal = !empty($productListsArr[$i]['largest_smallest_case_delivery_unit_maximum']) ? $productListsArr[$i]['largest_smallest_case_delivery_unit_maximum'] : '';

            $finalMaxVal = explode('/', $maxVal);

            $finalResult['largest_smallest_case_delivery_unit_maximum'] = !empty($finalMaxVal[0]) ? $finalMaxVal[0] : '';

            $finalResult['largest_smallest_case_delivery_unit_minimum'] = !empty($finalMaxVal[1]) ? $finalMaxVal[1] : '';

            $finalResult['contents_unit_description'] = !empty($productListsArr[$i]['largest_smallest_case_delivery_unit_minimum']) ? $productListsArr[$i]['largest_smallest_case_delivery_unit_minimum'] : '';

            $numbers = !empty($productListsArr[$i]['contents_unit_description']) ? $productListsArr[$i]['contents_unit_description'] : '';
            $numbers = explode('/', $numbers);
            $finalResult['case_size_and_weight_verticle'] = !empty($numbers[0]) ? $numbers[0] : '';
            $finalResult['case_size_and_weight_horizontal'] = !empty($numbers[1]) ? $numbers[1] : '';
            $finalResult['case_size_and_weight_height'] = !empty($numbers[2]) ? $numbers[2] : '';
            $finalResult['case_size_and_weight_width'] = !empty($numbers[3]) ? $numbers[3] : '';

            $finalResult['order_lead_time'] = !empty($productListsArr[$i]['case_size_and_weight_verticle']) ? $productListsArr[$i]['case_size_and_weight_verticle'] : '';

            $finalResult['jan_code'] = !empty($productListsArr[$i]['case_size_and_weight_horizontal']) ? $productListsArr[$i]['case_size_and_weight_horizontal'] : '';

            $finalResult['available_time'] = !empty($productListsArr[$i]['case_size_and_weight_height']) ? $productListsArr[$i]['case_size_and_weight_height'] : '';

            $finalResult['product_story_feelings_of_the_creator'] = !empty($productListsArr[$i]['case_size_and_weight_width']) ? $productListsArr[$i]['case_size_and_weight_width'] : '';

            $finalResult['product_features'] = !empty($productListsArr[$i]['order_lead_time']) ? $productListsArr[$i]['order_lead_time'] : '';


            $productListsArr[$i]['assumed_customer_information'] = $finalResult['assumed_customer_information'];
            $productListsArr[$i]['number_of_inputs_per_case'] = $finalResult['number_of_inputs_per_case'];
            $productListsArr[$i]['largest_smallest_case_delivery_unit_maximum'] =  $finalResult['largest_smallest_case_delivery_unit_maximum'];
            $productListsArr[$i]['largest_smallest_case_delivery_unit_minimum'] = $finalResult['largest_smallest_case_delivery_unit_minimum'];
            $productListsArr[$i]['contents_unit_description'] = $finalResult['contents_unit_description'];
            $productListsArr[$i]['case_size_and_weight_verticle'] = $finalResult['case_size_and_weight_verticle'];
            $productListsArr[$i]['case_size_and_weight_horizontal'] = $finalResult['case_size_and_weight_horizontal'];
            $productListsArr[$i]['case_size_and_weight_height'] = $finalResult['case_size_and_weight_height'];
            $productListsArr[$i]['case_size_and_weight_width'] = $finalResult['case_size_and_weight_width'];
            $productListsArr[$i]['order_lead_time'] = $finalResult['order_lead_time'];
            $productListsArr[$i]['jan_code'] = $finalResult['jan_code'];
            $productListsArr[$i]['available_time'] = $finalResult['available_time'];
            $productListsArr[$i]['product_story_feelings_of_the_creator'] = $finalResult['product_story_feelings_of_the_creator'];
            $productListsArr[$i]['product_features'] = $finalResult['product_features'];
        }

        return $productListsArr;
    }

    public function downloadCsv($file)
    {
        $fileName = $file;
        $exists = Storage::disk('s3')->exists(config('constants.CSV.FILE_PATH') . $fileName);
        if ($exists) {
            $content = file_get_contents(getCustomContentUrl() . 'csvformat/' . $fileName);
        } else {
            $content = trans('csv.invalid_content');
        }

        header('Content-Description: File Transfer');
        header('Content-Type: text/*');
        header('Content-disposition: attachment; filename=' . $fileName);
        header('Content-Length: ' . strlen($content));
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Expires: 0');
        header('Pragma: public');
        echo $content;
        exit;
    }

    public function ShimaShareFileUpload($action, $request)
    {
        $headers = array(
            "Content-type" => "text/csv; charset=utf-8",
            "Content-Disposition" => "attachment; filename=catalog_product.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );
        $csvSettings = CsvSettingsEcmall::where('type', 'product')->get();
        if ($csvSettings->isEmpty()) {
            session()->flash('warning', trans('csv.column_empty'));
            return redirect()->route('products.index');
        }

        $columns = [];
        $columnsjs = [];
        foreach ($csvSettings as $csvSetting) {
            $columns[] = $csvSetting->column_name;
            $columnsjs[] = $csvSetting->column_label;
        }
        $products = Product::join('ecmall_products', 'products.id', '=', 'ecmall_products.product_id')
                            ->leftJoin('sku_crosssell','products.id', '=', 'sku_crosssell.product_id' )
                            ->leftJoin('sku_relations','products.id', '=', 'sku_relations.product_id' )
                            ->leftJoin('localvendor_sellers', 'products.seller_id', '=', 'localvendor_sellers.seller_id')
                            ->leftJoin('localvendor_ecmallid', 'localvendor_sellers.user_id', '=', 'localvendor_ecmallid.localvendor_id')
                            ->select("products.ecmall_sku", DB::raw( '"" as store_view_code'),
                            DB::raw( '"Default" as attribute_set_code'),
                            DB::raw( '"simple" as product_type'),
                            DB::raw( '"" as categories'),
                            DB::raw( '"base" as product_websites'),
                            "ecmall_products.ecmall_product_name", "ecmall_products.ecmall_product_description","ecmall_products.ecmall_short_description", "ecmall_products.ecmall_shipping_weight",
                            DB::raw( '"1" as product_online'),
                            DB::raw( '"" as tax_class_name'),
                            DB::raw( '"" as visibility'),
                            DB::raw('(CASE 
                            WHEN db_ecmall_products.ecmall_selling_price IS NULL THEN db_products.price 
                            WHEN db_ecmall_products.ecmall_selling_price = 0 THEN db_products.price
                            ELSE db_ecmall_products.ecmall_selling_price 
                            END) AS price'),
                            DB::raw( '"" as special_price'),
                            DB::raw( '"" as special_price_from_date'),
                            DB::raw( '"" as special_price_to_date'),
                            DB::raw("CONCAT(db_ecmall_products.ecmall_sku,'',db_ecmall_products.product_id) AS ecmall_product_url"),
                            DB::raw( '"" as meta_title'),
                            DB::raw( '"" as meta_keywords'),
                            DB::raw( '"" as meta_description'),
                            "products.created_at", "products.updated_at",
                            DB::raw('"" as new_from_date'),
                            DB::raw('"" as new_to_date'),
                            DB::raw('"" display_product_options_in'),
                            DB::raw('"" map_price'),
                            DB::raw('"" msrp_price'),
                            DB::raw('"" as map_enabled'),
                            DB::raw('"" as gift_message_available'),
                            DB::raw('"" as custom_design'),
                            DB::raw('"" as custom_design_from'),
                            DB::raw('"" as custom_design_to'),
                            DB::raw('"" as custom_layout_update'),
                            DB::raw('"" as page_layout'),
                            DB::raw('"" as product_options_container'),
                            DB::raw('"" as msrp_display_actual_price_type'),
                            DB::raw('"" as country_of_manufacture'),
                            DB::raw('"" as additional_attributes'),
                            "ecmall_products.ecmall_stock_quantity",
                            DB::raw('"0" as out_of_stock_qty'),
                            DB::raw('"1" as use_config_min_qty'),
                            DB::raw('"0" as is_qty_decimal'),
                            DB::raw('"0" as allow_backorders'),
                            DB::raw('"1" as use_config_backorders'),
                            DB::raw('"1" as min_cart_qty'),
                            DB::raw('"0" as use_config_min_sale_qty'),
                            DB::raw('"0" as max_cart_qty'),
                            DB::raw('"1" as use_config_max_sale_qty'),
                            DB::raw('"1" as is_in_stock'),
                            DB::raw('"" as notify_on_stock_below'),
                            DB::raw('"1" as use_config_notify_stock_qty'),
                            DB::raw('"0" as manage_stock'),
                            DB::raw('"1" as use_config_manage_stock'),
                            DB::raw('"1" as use_config_qty_increments'),
                            DB::raw('"0" as qty_increments'),
                            DB::raw('"1" as use_config_enable_qty_inc'),
                            DB::raw('"0" as enable_qty_increments'),
                            DB::raw('"0" as is_decimal_divided'),
                            DB::raw('"1" as website_id'),
                            DB::raw('"0" as deferred_stock_update'),
                            DB::raw('"1" as use_config_deferred_stock_update'),
                            DB::raw('"" as related_skus'),
                            DB::raw('"" as crosssell_skus'),
                            DB::raw('"" as upsell_skus'),
                            DB::raw('"" as hide_from_product_page'),
                            DB::raw('"" as custom_options'),
                            DB::raw('"" as bundle_price_type'),
                            DB::raw('"" as bundle_skus_type'),
                            DB::raw('"" as bundle_price_view'),
                            DB::raw('"" as bundle_weight_type'),
                            DB::raw('"" as bundle_values'),
                            DB::raw('"" as associated_skus'),
                            "localvendor_ecmallid.ecmall_seller_id","ecmall_products.base_image", "ecmall_products.small_image", "ecmall_products.thumbnail_image", "ecmall_products.additional_image", "ecmall_products.ecmall_temperature");
                            if ($request->keyword != '')
                            {
                                $products = $products->where('products.name', 'LIKE', "%$request->keyword%");
                            }
                            if ($request->status != '')
                            {
                                $products = $products->where('products.status', $request->status);
                            }
                            if ($request->category != '')
                            {
                                $products = $products->where('products.category_id', $request->category);
                            }
                            if ($request->island != '')
                            {
                                $products = $products->where('products.island_id', $request->island);
                            }
                            if ($request->seller != '')
                            {
                                $products = $products->where('products.seller_id', $request->seller);
                            }
                            if ($request->vendor != '')
                            {
                                $products = $products->where('localvendor_sellers.user_id',  $request->vendor);
                            }
                            if ($request->start_date != '')
                            {
                                $start_date = Carbon::parse($request->start_date);
                                $products = $products->whereDate('products.created_at', '>=',   $start_date);
                            }
                            if ($request->end_date != '')
                            {
                                $end_date = Carbon::parse($request->end_date);
                                $products = $products->whereDate('products.created_at', '<=',   $end_date);
                            }
                            if ($action == 'append'){
                                if($request->salesCheckbox!=[0 => "0"])
                                {
                                    $sales = DB::table('product_sales_destination')
                                            ->whereIn('sales_destination_id', $request->salesCheckbox)
                                            ->pluck('product_id');
                                    $products = $products->whereIn('products.id', $sales);
                                }
                            }

                            if ($action == 'delete')
                            {
                                $products = $products->whereIn("products.id", $request->delete_id);
                            }
        $products = $products->get()->toArray();
        $csv = Writer::createFromString('');
        $csv->insertOne($columnsjs);
        $csv->insertAll($products);
        $contents = $csv->getContent();
        $fileName = "product_csv_files/"."product_catalog_".time().".csv";
        $file = substr($fileName, strpos($fileName, "/") + 1);
        Storage::disk('Ecmall_s3')->put($fileName, $contents);
        $this->accessApiTokenForShimaShare($file, $action);
    }

    public function shimaShareFileExport(Request $request)
    {
        $action = 'append';
        $this->ShimaShareFileUpload($action, $request);
    }

    public function shimaShareProductDelete(Request $request)
    {
        $action = $request->api_action;
        $this->ShimaShareFileUpload($action, $request);
    }

    public function getApiTokenForShimaShare()
    {
        $curl = curl_init();
        $api_user_name = env('SHIMA_SHARE_API_USERNAME');
        $api_password = env('SHIMA_SHARE_API_PASSWORD');

        curl_setopt_array($curl, array(
        CURLOPT_URL => env('SHIMA_SHARE_LOGIN_URL'),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',

        CURLOPT_POSTFIELDS =>'{
            "username": "'.$api_user_name.'",
            "password": "'.$api_password.'"
        }',

        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    public function accessApiTokenForShimaShare($file, $action)
    {
        $curl = curl_init();
        $bearer_token = $this->getApiTokenForShimaShare();
        $bearer_token = str_replace('"',"",$bearer_token);

        $payload = json_encode( array(
            "file_location"=> env('SHIMA_SHARE_FILE_LOCATION'),
            "file_name"=> $file,
            "action"=> $action,
            "access_key"=> env('AWS_ACCESS_KEY_ID'),
            "secret_key"=> env('AWS_SECRET_ACCESS_KEY')
        ) );

        curl_setopt_array($curl, array(
        CURLOPT_URL => env('SHIMA_SHARE_PRODUCT_EXPORT_URL'),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>$payload,
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer ' . $bearer_token ,
            'Content-Type: application/json',
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }
}
