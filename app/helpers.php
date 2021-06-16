<?php

use App\UserActivity;
use App\Category;
use App\Island;
use App\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

function getCategories()
{
    $categories = Category::where('is_active', 1)->select('id', 'name')->get();
    return $categories;
}

function getIslands()
{
    $islands = Island::where('is_active', 1)->select('id', 'name')->get();
    return $islands;
}

function getDateJp($dateData)
{
    setlocale(LC_ALL, "ja_JP.utf8");
    $date_format = "%Y年 %m月 %e日";
    return strftime($date_format, strtotime($dateData));
}

function priceCheck($sell_price)
{ 
    if(isset($sell_price)){
        return $sell_price;
    }
    else{
        return '-';
    }    
}

function convertUser($created_by)
{
    $user = DB::table('users')->select('name')->where('id',$created_by)->get();
    $user = User::where('id','=', $created_by)->pluck('name');  
    return $user->toArray();
} 

// log_level ############
// general = 一般的な
// warning = 警告
// security = 保安
// login = ログインする
// logout = ログアウト
// create = 作成する
// update = 更新
// delete = 削除する

function createUserActivity($request, $action, $description, $log_level, $email)
{
    $userActivity = new UserActivity();
    $userActivity->action = $action;
    $userActivity->email = $email ?? auth()->user()->name . '<' . auth()->user()->email . '>';
    $userActivity->description = $description;
    $userActivity->log_level = $log_level;
    $userActivity->ip = $request->ip();
    $userActivity->browser = $request->header('User-Agent');
    $userActivity->save();
}

function pr($arr)
{
    echo "<pre>";
    print_r($arr);
    echo "<pre>";
    die();
}

// last login helpers create
function lastLoginUser()
{
    $date = Auth::user()->last_login;
    $jplast_login = Carbon::parse($date)->format('Y/m/d H:i');
    return $jplast_login;
}

function isChecked($optionId, $itemArray = array())
{
    $checked = false;
    if (!empty($itemArray) && isset($optionId)) {
        if (in_array($optionId, $itemArray)) {
            $checked = true;
        }
    }
    return $checked;
}

/**
 * Number to alphabet map
 */

function numberToAlphabet($index = 0)
{
    $arr = array('想定の顧客情報（年齢層・性別・所得層など）', '１ケースあたりの⼊数', '最⼤・最⼩ケース納品単位（○ケース/⽇などの単位記載）', '内容量（単位記載）', 'ケースサイズと重量', '発注のリードタイム', 'JANコード（13桁、もしくは8桁）', '提供可能時期', '商品のストーリー・作り手の想い', '商品の特徴');
    if (isset($arr[$index])) {
        return $arr[$index];
    }
    return $index;
}

/**
 * Unauthorized User
 */

function unauthorizedAccess($id)
{
    if (Auth::user()->id != $id) {
        return true;
    }
}

/**
 * preservation method label text array
 */
function preservationMethod()
{
    $arr = array('常温', '冷蔵', '冷凍', 'チルド', 'その他');
    return $arr;
}

//Forget password link
function islandList()
{
    $islands = Island::select('id', 'name', 'code')->get();

    $island = [];
    foreach ($islands as $data) {
        $island[$data->id] = $data->name . ' (' . $data->code . ') ';
    }
    return $island;
}

function getImageName($path)
{
    if (isset($path) && !empty($path)) {
        $im_path = explode("/", $path);
        return end($im_path);
    }
    return $path;
}

function getFromObject($object, $index, $thumbnail = true)
{
    $image = getImageByImageSerial($object, $index);
    if (isset($image) && !empty($image)) {
        if ($thumbnail == true) {
            return $image->image_sm;
        }
        return getImageName($image->image);
    }
    return '';
}

function getDeleteClass($object, $index)
{
    $image = getImageByImageSerial($object, $index);
    if (isset($image) && !empty($image)) {
        return '';
    }
    return 'd-none';
}

function getImageId($object, $index)
{
    $image = getImageByImageSerial($object, $index);
    if (isset($image) && !empty($image)) {
        return $image->id;
    }
    return 0;
}

function getImageByImageSerial($object, $index)
{
    $imageId = $index + 1;
    foreach ($object as $image) {
        if ($image->image_serial == $imageId) {
            return $image;
        }
    }
    return [];
}

function preservationMethodList($index)
{
    $list = [
        0 => '常温',
        1 => '冷蔵',
        2 => '冷凍',
        3 => 'チルド',
        4 => 'その他'
    ];
    return $list[$index];
}


/**
 * Check code is runnung in local server
 */
function isLocal()
{
    $url = url('/');
    if (preg_match('/\blocalhost\b/', $url)) {
        return true;
    }

    return false;
}


/**
 * getAdditionalInformationFiledName
 */

function getAdditionalInformationFiledName()
{
    $arr = array('assumed_customer_information', 'number_of_inputs_per_case', 'largest_smallest_case_delivery_unit_maximum', 'largest_smallest_case_delivery_unit_minimum', 'contents_unit_description', 'case_size_and_weight_verticle', 'case_size_and_weight_horizontal', 'case_size_and_weight_height', 'case_size_and_weight_width', 'order_lead_time', 'jan_code', 'available_time', 'product_story_feelings_of_the_creator', 'product_features');

    return $arr;
}

/**
 * Get file path for custom content
 */

function getCustomContentUrl()
{
    $path = "https://s3.ap-northeast-1.amazonaws.com/rito-db-stg-public/public/";
    if (isLocal() == false) {
        return asset('/');
    }

    return $path;
}

/**
 * Filter data
 */

function _trim($val = "")
{
    return trim($val);
}

function isJapanese($line)
{
    return preg_match('/[\x{4E00}-\x{9FBF}\x{3040}-\x{309F}\x{30A0}-\x{30FF}]/u', $line);
}
function toLocalDate($date = "")
{
    if ($date == "") {
        $date = date('Y/m/d H:i:s');
    }
    return date('Y/m/d', strtotime($date));
}

function dateComparison($table){
    $todayDate = date("Y-m-d"); 
        $status_label = $table::all()  
        ->where('publishing_end_date', '<>', null)
        ->where('publishing_end_date', '<', $todayDate)
        ->where('status_label', '<>' , '1'); 
        
        foreach($status_label as $value){  
            $value['status_label'] = 1;  
            $value['display_date'] = $value['publishing_end_date'];
            $value->save();
        } 
}