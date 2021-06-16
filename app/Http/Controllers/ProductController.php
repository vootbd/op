<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use League\Flysystem\Filesystem;
use League\Flysystem\ZipArchive\ZipArchiveAdapter;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Str;
use App\Prefecture;
use DB;
use Validator;
use Redirect;
use Response;
use Carbon\Carbon;
use App\User;
use App\Category;
use App\ProductSalesDestination;
use App\ProductAllergyIndication;
use App\SalesDestination;
use App\AllergyIndications;
use App\AdditionalInformations;
use App\Product;
use App\ProductImage;
use App\EcmallProducts;
use App\EcmallProductImage;
use App\Island;
use App\SkuCrosssell;
use PDF;
use App\Rules\UrlValidator;
use App\Rules\LatLongValidator;
use App\helpers;
use App\UserIsland;
use App\LocalvendorSeller;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables; 
use App\Rules\ValidatePublishingEndDate; 
use Session;

class ProductController extends Controller
{
    private $temp = "temp";
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {

        // permission for admin and operator
        $this->middleware('permission:product-list|product-delete', ['only' => ['index']]);
        $this->middleware('permission:product-create', ['only' => ['create']]);
        $this->middleware('permission:product-create|seller-product-create', ['only' => ['store']]);
        $this->middleware('permission:product-list|seller-product-create|seller-product-edit', ['only' => ['read']]);
        $this->middleware('permission:product-edit', ['only' => ['edit']]);
        $this->middleware('permission:product-edit|seller-product-edit', ['only' => ['update']]);
        $this->middleware('permission:product-delete|seller-product-delete', ['only' => ['destroy']]);
        // permission for seller
        $this->middleware('permission:seller-product-list|seller-product-delete', ['only' => ['sellerProductList']]);
        $this->middleware('permission:seller-product-create', ['only' => ['sellerProductCreate']]);
        $this->middleware('permission:seller-product-edit', ['only' => ['sellerEdit']]);
        // permission for buyer
        $this->middleware('permission:buyer-product-list', ['only' => ['buyerProductList']]);
        $this->middleware('permission:buyer-product-detail', ['only' => ['show']]);
    }

    protected function upload($file, $location_main)
    {
        $imagePath = 'temp/' . $file;
        $main_image = $file;
        $extension = pathinfo($main_image, PATHINFO_EXTENSION);
        $location = "public/upload/product/$location_main/";
        $location_md = "public/upload/product/$location_main/md/";
        $location_sm = "public/upload/product/$location_main/sm/";
        $ImgName = date('Ymdhis') . rand(10000, 99999) . '.' . $extension;
        $ImgName_md = date('Ymdhis') . rand(10000, 99999) . '_md=294x350.' . $extension;
        $ImgName_sm = date('Ymdhis') . rand(10000, 99999) . '_sm=116x132.' . $extension;
        // Instantiate SimpleImage class
        $image = Image::make($imagePath)->encode($extension);
        $image_md = Image::make($imagePath)->resize(350, 294, function ($aspect) {
            $aspect->aspectRatio();
        })->encode($extension);
        $image_sm = Image::make($imagePath)->resize(116, 132, function ($aspect) {
            $aspect->aspectRatio();
        })->encode($extension);
        // Size:large
        Storage::disk('s3')->put($location . $ImgName, (string) $image);
        // Size:medium
        Storage::disk('s3')->put($location_md . $ImgName_md, (string) $image_md);
        // Size:small
        Storage::disk('s3')->put($location_sm . $ImgName_sm, (string) $image_sm);
        $filename['image'] = "/upload/product/$location_main/" . $ImgName;
        $filename['image_md'] = "/upload/product/$location_main/md/" . $ImgName_md;
        $filename['image_sm'] = "/upload/product/$location_main/sm/" . $ImgName_sm;
        return $filename;
    }


    public function moveTempToLocation($file, $location_main)
    {
        $main_image = $file;
        $fileName = pathinfo($main_image, PATHINFO_FILENAME);
        $extension = pathinfo($main_image, PATHINFO_EXTENSION);

        $ImgName = $main_image;
        $ImgName_md = $fileName . "_md=294x350." . $extension;
        $ImgName_sm = $fileName . "_sm=116x132." . $extension;

        $oldLocation =  "public/" . $this->temp . "/" . $ImgName;
        $oldLocation_md =  "public/" . $this->temp . "/" . $ImgName_md;
        $oldLocation_sm =  "public/" . $this->temp . "/" . $ImgName_sm;

        $location = "public/upload/product/$location_main/" . $ImgName;
        $location_md = "public/upload/product/$location_main/md/" . $ImgName_md;
        $location_sm = "public/upload/product/$location_main/sm/" . $ImgName_sm;


        // Size:large
        if ($this->fileExists($ImgName)) {
            Storage::disk('s3')->move($oldLocation, $location);
        }

        // Size:medium
        if ($this->fileExists($ImgName_md)) {
            Storage::disk('s3')->move($oldLocation_md, $location_md);
        }

        // Size:small
        if ($this->fileExists($ImgName_sm)) {
            Storage::disk('s3')->move($oldLocation_sm, $location_sm);
        }

        $filename['image'] = "/upload/product/$location_main/" . $ImgName;
        $filename['image_md'] = "/upload/product/$location_main/md/" . $ImgName_md;
        $filename['image_sm'] = "/upload/product/$location_main/sm/" . $ImgName_sm;
        return $filename;
    }

    public function moveTempToLocationEcmall($file, $location_main)
    {   
        $main_image = $file;
        $fileName = pathinfo($main_image, PATHINFO_FILENAME);
        $extension = pathinfo($main_image, PATHINFO_EXTENSION);

        $ImgName = $main_image;
        $ImgName_md = $fileName . "_md=400x400." . $extension;
        $ImgName_sm = $fileName . "_sm=400x400." . $extension;

        $oldLocation =  "public/" . $this->temp . "/" . $ImgName;
        $oldLocation_md =  "public/" . $this->temp . "/" . $ImgName_md;
        $oldLocation_sm =  "public/" . $this->temp . "/" . $ImgName_sm;

        $location = "public/upload/product/$location_main/" . $ImgName;
        $location_md = "public/upload/product/$location_main/md/" . $ImgName_md;
        $location_sm = "public/upload/product/$location_main/sm/" . $ImgName_sm;


        // Size:large
        if ($this->fileExists($ImgName)) {
            Storage::disk('s3')->move($oldLocation, $location);
        }

        // Size:medium
        if ($this->fileExists($ImgName_md)) {
            Storage::disk('s3')->move($oldLocation_md, $location_md);
        }

        // Size:small
        if ($this->fileExists($ImgName_sm)) {
            Storage::disk('s3')->move($oldLocation_sm, $location_sm);
        }

        $filename['image'] = "/upload/product/$location_main/" . $ImgName;
        $filename['image_md'] = "/upload/product/$location_main/md/" . $ImgName_md;
        $filename['image_sm'] = "/upload/product/$location_main/sm/" . $ImgName_sm;
        return $filename; 
    }

    // image upload in S3
    protected function imageUpload($requestFile, $location_main)
    {
        $main_image = $requestFile;
        $extension = $main_image->getClientOriginalExtension();
        $location = "public/upload/product/$location_main/";
        $location_md = "public/upload/product/$location_main/md/";
        $location_sm = "public/upload/product/$location_main/sm/";
        $ImgName = date('Ymdhis') . rand(10000, 99999) . '.' . $extension;
        $ImgName_md = date('Ymdhis') . rand(10000, 99999) . '_md=294x350.' . $extension;
        $ImgName_sm = date('Ymdhis') . rand(10000, 99999) . '_sm=116x132.' . $extension;
        // Instantiate SimpleImage class
        $image = Image::make($main_image)->encode($extension);
        $image_md = Image::make($main_image)->resize(350, 294, function ($aspect) {
            $aspect->aspectRatio();
        })->encode($extension);
        $image_sm = Image::make($main_image)->resize(116, 132, function ($aspect) {
            $aspect->aspectRatio();
        })->encode($extension);
        // Size:large
        Storage::disk('s3')->put($location . $ImgName, (string) $image);
        // // Size:medium
        Storage::disk('s3')->put($location_md . $ImgName_md, (string) $image_md);
        // // Size:small
        Storage::disk('s3')->put($location_sm . $ImgName_sm, (string) $image_sm);
        $filename['image'] = "/upload/product/$location_main/" . $ImgName;
        $filename['image_md'] = "/upload/product/$location_main/md/" . $ImgName_md;
        $filename['image_sm'] = "/upload/product/$location_main/sm/" . $ImgName_sm;
        return $filename;
    }

    protected function imageCopyAndUpload($location_main, $oldImage, $oldImage_md, $oldImage_sm)
    {
        $getExtension = explode(".", $oldImage);
        $extension = $getExtension[1];
        $location = "public/upload/product/$location_main/";
        $location_md = "public/upload/product/$location_main/md/";
        $location_sm = "public/upload/product/$location_main/sm/";
        $ImgName = date('Ymdhis') . rand(10000, 99999) . '.' . $extension;
        $ImgName_md = date('Ymdhis') . rand(10000, 99999) . '_md=294x350.' . $extension;
        $ImgName_sm = date('Ymdhis') . rand(10000, 99999) . '_sm=116x132.' . $extension;
        // Size:large
        Storage::disk('s3')->copy("public" . $oldImage, $location . $ImgName);
        // // Size:medium
        Storage::disk('s3')->copy("public" . $oldImage_md, $location_md . $ImgName_md);
        // // Size:small
        Storage::disk('s3')->copy("public" . $oldImage_sm, $location_sm . $ImgName_sm);
        $filename['image'] = "/upload/product/$location_main/" . $ImgName;
        $filename['image_md'] = "/upload/product/$location_main/md/" . $ImgName_md;
        $filename['image_sm'] = "/upload/product/$location_main/sm/" . $ImgName_sm;
        return $filename;
    } 

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $seller = User::role(['seller'])->pluck('name', 'id');
        $vendor = User::role(['vendor'])->pluck('name', 'id'); 
        $categories = Category::pluck('name', 'id');
        $salerDestination = SalesDestination::select('id', 'name')->get();
        $islandDropDown = Prefecture::with(['islands' => function ($qu) {
            return $qu->select('id', 'name', 'prefecture_id');
        }])
        ->select('id','name')
        ->get();
        if ($request->ajax()) {
            $is_active = $request->is_active;
            $start = $request->get('start');
            $length = $request->get('length'); 
            $orderColumn = $request->input("order");
            $columns = $request->input("columns"); 
            $salesCheckbox = $request->query('salesCheckbox');
            $keyword = $request->query("search_keword");
            $island = $request->query("search_island");
            $seller = $request->query("search_seller");
            $vendor = $request->query("search_vendor");
            $category = $request->query("search_category");
            $is_active = $request->query('is_active'); 
            $start_date = $request->query('start_date');
            $end_date = $request->query('end_date');            
            $getOrderColumn = $columns[$orderColumn[0]["column"]]["data"];
            $orderColumnName = ($getOrderColumn == 'directory_name') ? 'directory_id': $getOrderColumn;
            $orderingStyle = $orderColumn[0]["dir"];
            $check_box_val = $request->query('check_box_val');
            $countSearch = -1;
            
            $data = DB::table('products')
                        ->Join('users', 'users.id', '=', 'products.seller_id')
                        ->Join('localvendor_sellers', 'localvendor_sellers.seller_id', '=', 'products.seller_id')  
                        ->select('products.id', 'products.status', 'products.name', 'products.sell_price', 'products.cover_image_md', 'products.seller_id', 'products.island_id', 'products.category_id', 'products.created_at', 'products.updated_at', 'products.created_by', 'users.name AS seller' , 'localvendor_sellers.user_id AS vendor', 'products.ecmall_sku')
                        ->where('products.deleted_at', null)
                        ->groupBy('products.id'); 
            $count = Product::where('deleted_at', null)->count();
            if ($is_active == "1" || $is_active == "2") {
                $data = $data->where('products.status', (int) $is_active);
                $count = $data->count();
            } 
            //*******************  Search query start **************/    
            if ($check_box_val == 1) {
                $data = $data->where('products.ecmall_sku', '!=', '');
                $data_count = $data->get()->toArray();
                $count = count($data_count);
            }
            if (isset($keyword)) {
                $searchValue = $keyword;
                $data = $data->where(function ($q) use ($keyword) {
                    return $q->where('products.name', 'LIKE', "%$keyword%");
                });
                $data_count = $data->get()->toArray(); 
                $count = count($data_count);  
            } 
            if (isset($island)) {
                $data = $data->where('products.island_id', $island);
                $data_count = $data->get()->toArray(); 
                $count = count($data_count); 
            } 
            if (isset($vendor)) {  
                $data = $data->where('localvendor_sellers.user_id',  $vendor); 
                $data_count = $data->get()->toArray(); 
                $count = count($data_count);  
            } 
            if (isset($seller)) {  
                $data = $data->where('products.seller_id', $seller); 
                $data_count = $data->get()->toArray(); 
                $count = count($data_count); 
            }  
            if (isset($category)) {
                $data = $data->where('products.category_id', $category);
                $data_count = $data->get()->toArray(); 
                $count = count($data_count); 
            } 
            if (isset($status)) {
                $data = $data->where('products.status', $status);
                $data_count = $data->get()->toArray(); 
                $count = count($data_count); 
            }
            if (isset($start_date) && isset($end_date)) {
                $start_date = Carbon::parse($start_date);
                $end_date = Carbon::parse($end_date . ' 23:59:59');
                $data = $data->whereBetween('products.created_at', [$start_date, $end_date]);
                $data_count = $data->get()->toArray(); 
                $count = count($data_count); 
            }
            if (($salesCheckbox!="0") &&($salesCheckbox != "")) {
                $salesCheckbox = explode(',',$salesCheckbox);   
                $sales = DB::table('product_sales_destination')
                        ->whereIn('sales_destination_id', $salesCheckbox)
                        ->pluck('product_id'); 
                $data = $data->whereIn('products.id', $sales); 
                $data_count = $data->get()->toArray(); 
                $count = count($data_count); 
            }  
            if ($orderColumnName == 'products.created_at') {
                $data = $data->orderBy($orderColumnName, 'DESC')
                    ->orderBy('products.status', $orderingStyle)
                    ->orderBy('products.id', 'DESC') 
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
                ->editColumn('sell_price', function ($p) {   
                    return priceCheck($p->sell_price); 
                }) 
                ->skipPaging()
                ->make(true);

            return $dataTable;
        }       
        return view('admin.products.index', compact('seller', 'islandDropDown','vendor', 'categories', 'salerDestination'));  
    }

    /**
     * Display Local Vendor On drop down of Island Select by Ajax
     *
     * @return \Illuminate\Http\Response
     */
    public function getVendor($id)
    {     
        $vendor = DB::table('user_islands')
                    ->join('localvendor_sellers' , 'localvendor_sellers.seller_id', '=', 'user_islands.user_id')
                    ->join('users' , 'users.id','=', 'localvendor_sellers.user_id')
                    ->where('user_islands.island_id',$id) 
                    ->pluck('users.name', 'users.id'); 
        return json_encode($vendor);
    }
    /**
     * Display Local Vendor On drop down of Island Select by Ajax
     *
     * @return \Illuminate\Http\Response
     */
    public function getSeller($id)
    {     
        $seller = DB::table('localvendor_sellers') 
                    ->join('users' , 'users.id','=', 'localvendor_sellers.seller_id')
                    ->where('localvendor_sellers.user_id',$id) 
                    ->pluck('users.name', 'users.id'); 
        return json_encode($seller);
    }
    /**
     * Display Local Vendor On drop down of Island Select by Ajax in Vendor role 
     *
     * @return \Illuminate\Http\Response
     */
    public function getSellerIsland($id)
    {   
        $seller = DB::table('user_islands') 
                    ->join('users' , 'users.id','=', 'user_islands.user_id')
                    ->where('user_islands.island_id',$id) 
                    ->pluck('users.name', 'users.id');  
        return json_encode($seller);
    } 
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function sellerIsland(Request $request, $id)
    {
        $islandId = User::find($id);
        return response()->json($islandId->island_id);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function sellerProductList(Request $request)
    {
        $user = Auth::user();
        $users = User::role(['seller'])->pluck('name', 'id');
        $islands = islandList();
        $categories = Category::pluck('name', 'id');
        $salerDestination = SalesDestination::select('id', 'name')->get();

        if ($request->ajax()) {
            $is_active = $request->is_active;
            $start = $request->get('start');
            $length = $request->get('length'); 
            $orderColumn = $request->input("order");
            $columns = $request->input("columns"); 
            $salesCheckbox = $request->query('salesCheckbox');
            $keyword = $request->query("search_keword"); 
            $seller = $request->query("search_seller");
            $category = $request->query("search_category");
            $is_active = $request->query('is_active'); 
            $start_date = $request->query('start_date');
            $end_date = $request->query('end_date');            
            $getOrderColumn = $columns[$orderColumn[0]["column"]]["data"];
            $orderColumnName = ($getOrderColumn == 'directory_name') ? 'directory_id': $getOrderColumn;
            $orderingStyle = $orderColumn[0]["dir"];
            $countSearch = -1;  
            $data = DB::table('products')
                        ->Join('localvendor_sellers', 'localvendor_sellers.seller_id', '=', 'products.seller_id')
                        ->select('products.status', 'products.id', 'products.name', 'products.cover_image_md', 'products.seller_id', 'products.category_id', 'products.created_at', 'products.updated_at', 'products.created_by' , 'localvendor_sellers.user_id as vendor')
                        ->where('products.seller_id',$user->id )
                        ->where('deleted_at', null)
                        ->groupBy('products.id');
            
            $count = Product::where('seller_id',$user->id)
                            ->where('deleted_at', null) 
                            ->count();
            if ($is_active == "1" || $is_active == "2") {
                $data = $data->where('status', (int) $is_active);
                $count = $data->count();
            } 
            //*******************  Search query start **************/    
            if (isset($keyword)) {
                $searchValue = $keyword;
                $pages = $data->where(function ($q) use ($keyword) {
                    return $q->where('name', 'LIKE', "%$keyword%");
                });
                $countSearch = $data->count();
            }
            if (isset($status)) {
                $data = $data->where('status', $status);
                $count = $data->count();
            } 
            if (isset($start_date) && isset($end_date)) {
                $start_date = Carbon::parse($start_date);
                $end_date = Carbon::parse($end_date . ' 23:59:59');
                $data = $data->whereBetween('created_at', [$start_date, $end_date]);
            }
            if (($salesCheckbox!="0") &&($salesCheckbox != "")) {
                $salesCheckbox = explode(',',$salesCheckbox);   
                $sales = DB::table('product_sales_destination')
                        ->whereIn('sales_destination_id', $salesCheckbox)
                        ->pluck('product_id'); 
                $data = $data->whereIn('products.id', $sales); 
                $data_count = $data->get()->toArray(); 
                $count = count($data_count); 
            }  
            if (isset($category)) {
                $data = $data->where('category_id', $category);
            }
            if (isset($seller)) {
                $data = $data->where('seller_id', $seller);
            } 
            if ($orderColumnName == 'created_at') {
                $data = $data->orderBy($orderColumnName, 'DESC')
                    ->orderBy('status', $orderingStyle)
                    ->orderBy('id', 'DESC')
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

        return view('admin.products.seller-product-list', compact('users', 'islands', 'categories', 'salerDestination'));
    }  
    /**
     * Display a listing of the buyerProductList.
     *
     * @return \Illuminate\Http\Response
     */
    public function buyerProductList(Request $request)
    {
        $user = Auth::user();
        $islands = islandList();
        $categories = Category::pluck('name', 'id');
        $salerDestination = SalesDestination::select('id', 'name')->get();
        $maxPrice = Product::max('sell_price');
        $setCategory = $request->query('category');
        $setIsland = $request->query('island');

        if ($request->ajax()) {
            try {
                $salesCheckbox = $request->query('salesCheckbox');
                $order_by = $request->query('order_by');
                $per_page = $request->query('per_page');
                $start_date = $request->query('start_date');
                $end_date = $request->query('end_date');
                $category = $request->query('category');
                $island = $request->query('island');
                $keyword = $request->query('keyword');
                $rangeFrom = $request->query('rangeFrom');
                $rangeTo = $request->query('rangeTo');
                $data = Product::select('status', 'id', 'name', 'sell_price', 'cover_image_md', 'category_id', 'created_at', 'island_id')->where('status', 1)->whereBetween('sell_price', [$rangeFrom, $rangeTo]);

                if (isset($start_date) && isset($end_date)) {
                    $start_date = Carbon::parse($start_date);
                    $end_date = Carbon::parse($end_date . ' 23:59:59');
                    $data = $data->whereBetween('created_at', [$start_date, $end_date]);
                }
                if (isset($salesCheckbox)) {
                    $salesCheckbox = explode(',', $salesCheckbox);
                    $data = $data->whereHas('salesDestination', function ($q) use ($salesCheckbox) {
                        $q->whereIn('sales_destinations.id', $salesCheckbox);
                    });
                }
                if (isset($category)) {
                    $data = $data->where('category_id', $category);
                }
                if (isset($island)) {
                    $data = $data->where('island_id', $island);
                }
                if (isset($keyword)) {
                    $data = $data->search($keyword);
                }
                return $data->orderBy('id', $order_by)->paginate($per_page);
            } catch (Exception $e) {
                return response()->json([
                    'status' => 500,
                    'message' => $e->getMessage(),
                ]);
            }
        }

        return view(
            'admin.products.buyer-product-list',
            compact(
                'islands',
                'categories',
                'setCategory',
                'setIsland',
                'maxPrice',
                'salerDestination'
            )
        );
    }
/**************Local Vendor Product List *******/
    public function localvendorProductList(Request $request)
    { 
        $user = Auth::user(); 
        $seller = User::role(['seller'])->pluck('name', 'id');  
        $categories = Category::pluck('name', 'id');
        $salerDestination = SalesDestination::select('id', 'name')->get();
        $islandDropDown = Prefecture::with(['islands' => function ($qu) {
            return $qu->select('id', 'name', 'prefecture_id');
        }])
        ->select('id','name')
        ->get();
        if ($request->ajax()) {
            $is_active = $request->is_active;
            $start = $request->get('start');
            $length = $request->get('length'); 
            $orderColumn = $request->input("order");
            $columns = $request->input("columns"); 
            $salesCheckbox = $request->query('salesCheckbox');
            $keyword = $request->query("search_keword");
            $island = $request->query("search_island");
            $seller = $request->query("search_seller"); 
            $category = $request->query("search_category");
            $is_active = $request->query('is_active'); 
            $start_date = $request->query('start_date');
            $end_date = $request->query('end_date');            
            $getOrderColumn = $columns[$orderColumn[0]["column"]]["data"];
            $orderColumnName = ($getOrderColumn == 'directory_name') ? 'directory_id': $getOrderColumn;
            $orderingStyle = $orderColumn[0]["dir"];
            $countSearch = -1; 
            $data = DB::table('products')
                        ->Join('users', 'users.id', '=', 'products.seller_id')
                        ->Join('localvendor_sellers', 'localvendor_sellers.seller_id', '=', 'products.seller_id')  
                        ->select('products.id', 'products.status', 'products.name', 'products.sell_price', 'products.cover_image_md', 'products.seller_id', 'products.island_id', 'products.category_id', 'products.created_at', 'products.updated_at', 'products.created_by', 'users.name AS seller' , 'localvendor_sellers.user_id')
                        ->where('products.deleted_at', null)
                        ->where('localvendor_sellers.user_id', $user->id)
                        ->groupBy('products.id');  
            $count = DB::table('products')
                        ->Join('localvendor_sellers', 'localvendor_sellers.seller_id', '=', 'products.seller_id')   
                        ->where('localvendor_sellers.user_id',$user->id)
                        ->where('products.deleted_at', null)
                        ->count(); 
            if ($is_active == "1" || $is_active == "2") {
                $data = $data->where('products.status', (int) $is_active);
                $count = $data->count();
            } 
            //*******************  Search query start **************/    
            if (isset($keyword)) {
                $searchValue = $keyword;
                $data = $data->where(function ($q) use ($keyword) {
                    return $q->where('products.name', 'LIKE', "%$keyword%");
                });
                $data_count = $data->get()->toArray(); 
                $count = count($data_count);  
            } 
            if (isset($island)) {
                $data = $data->where('products.island_id', $island);
                $data_count = $data->get()->toArray(); 
                $count = count($data_count); 
            }  
            if (isset($seller)) {  
                $data = $data->where('products.seller_id', $seller); 
                $data_count = $data->get()->toArray(); 
                $count = count($data_count); 
            }  
            if (isset($category)) {
                $data = $data->where('products.category_id', $category);
                $data_count = $data->get()->toArray(); 
                $count = count($data_count); 
            } 
            if (isset($status)) {
                $data = $data->where('products.status', $status);
                $data_count = $data->get()->toArray(); 
                $count = count($data_count); 
            } 
            if (isset($start_date) && isset($end_date)) {
                $start_date = Carbon::parse($start_date);
                $end_date = Carbon::parse($end_date . ' 23:59:59');
                $data = $data->whereBetween('products.created_at', [$start_date, $end_date]);
                $data_count = $data->get()->toArray(); 
                $count = count($data_count); 
            }
            if ($salesCheckbox != "") {
                $salesCheckbox = explode(',',$salesCheckbox);   
                $sales = DB::table('product_sales_destination')
                        ->whereIn('sales_destination_id', $salesCheckbox)
                        ->pluck('product_id'); 
                $data = $data->whereIn('products.id', $sales); 
                $data_count = $data->get()->toArray(); 
                $count = count($data_count); 
            }  
            if ($orderColumnName == 'products.created_at') {
                $data = $data->orderBy($orderColumnName, 'DESC')
                    ->orderBy('products.status', $orderingStyle)
                    ->orderBy('products.id', 'DESC') 
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
                ->editColumn('sell_price', function ($p) {   
                    return priceCheck($p->sell_price); 
                }) 
                ->skipPaging()
                ->make(true);

            return $dataTable;
        }       
        return view('admin.products.localvendor-product-list', compact('seller', 'islandDropDown', 'categories',  'salerDestination'));  
    }
        /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function removeProduct(Request $request, $id)
    {
        $user = Auth::user();
        $ids = explode(",", $id);
        Log::warning('Deleting user profile for user: ' . Auth::user()->email);
        Product::whereIn('id', $ids)->delete();
        // user activity log
        createUserActivity($request, '記事を削除する', $user->name . '<' . $user->email . '> 記事を削除するIds ' . $id . ' 論文', '一般的な', null); 
        $res['success'] = true;
        $res['rs_class'] = 'success';
        $res['message'] = trans('product.remove');
        $res['redirects'] = url('/products');
        Session::flash('message', $res['message']);   
        return Response::json($res);
    }
    public function vendorRemoveProduct(Request $request, $id)
    {
        $user = Auth::user();
        $ids = explode(",", $id);
        Log::warning('Deleting user profile for user: ' . Auth::user()->email);
        Product::whereIn('id', $ids)->delete();
        // user activity log
        createUserActivity($request, '記事を削除する', $user->name . '<' . $user->email . '> 記事を削除するIds ' . $id . ' 論文', '一般的な', null); 
        $res['success'] = true;
        $res['rs_class'] = 'success';
        $res['message'] = trans('product.remove');
        $res['redirects'] = url('/localvendor/products/list');
        Session::flash('message', $res['message']);   
        return Response::json($res);
    }
    public function sellerRemoveProduct(Request $request, $id)
    {
        $user = Auth::user();
        $ids = explode(",", $id);
        Log::warning('Deleting user profile for user: ' . Auth::user()->email);
        Product::whereIn('id', $ids)->delete();
        // user activity log
        createUserActivity($request, '記事を削除する', $user->name . '<' . $user->email . '> 記事を削除するIds ' . $id . ' 論文', '一般的な', null); 
        $res['success'] = true;
        $res['rs_class'] = 'success';
        $res['message'] = trans('product.remove');
        $res['redirects'] = url('/seller/products/list');
        Session::flash('message', $res['message']);   
        return Response::json($res);
    }

    /**
     * Duplicate the specified resource in storage by Operator Role
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function DuplicateProduct(Request $request, $id)
    {
        $ids = explode(",", $id); 
        $user = Auth::user();
        foreach ($ids as $product_id) {
            if ($product_id != 'on') {
                $data = Product::findOrFail($product_id);
                $current_timestamp = Carbon::now()->timestamp;
                $productTitle = $data->name . "の複製"; 

                $product = new Product; 
                $product->name = $productTitle;
                $product->island_id = $data->island_id;
                $product->seller_id = $data->seller_id;
                $product->status = $data->status;
                $product->product_explanation = $data->product_explanation;
                $product->category_id = $data->category_id;
                $product->price = $data->price;
                $product->tax = $data->tax;
                $product->sell_price = $data->sell_price;
                $product->cover_image = $data->cover_image;
                $product->cover_image_md = $data->cover_image_md;
                $product->cover_image_sm = $data->cover_image_sm;
                $product->url = $data->url;
                $product->shipment_method = $data->shipment_method;
                $product->preservation_method = $data->preservation_method;
                $product->package_type = $data->package_type;
                $product->quality_retention_temperature = $data->quality_retention_temperature;
                $product->expiration_taste_quality = $data->expiration_taste_quality;
                $product->use_scene = $data->use_scene; 

                $product->created_by = $data->created_by;   
                $product->updated_by = $data->updated_by; 
                $createProduct =  $product->save();
                if ($createProduct) {
                    // user activity log
                    createUserActivity($request, '記事作成', $user->name . '<' . $user->email . '> 記事作成 ' . $product->name . ' 論文', '一般的な', null);
                    $res['success'] = true;
                    $res['rs_class'] = 'success';
                    $res['message'] = trans('product.duplicate');
                    $res['redirects'] = url('/products');
                    Session::flash('message', $res['message']); 
                } else {
                    $res['message'] = trans('product.failed');
                }
            }
        }
        return Response::json($res);
    }
    /**
     * Duplicate the specified resource in storage by Local Vendor
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function vendorDuplicateProduct(Request $request, $id)
    {
        $ids = explode(",", $id); 
        $user = Auth::user();
        foreach ($ids as $product_id) {
            if ($product_id != 'on') {
                $data = Product::findOrFail($product_id);
                $current_timestamp = Carbon::now()->timestamp;
                $productTitle = $data->name . "の複製"; 

                $product = new Product; 
                $product->name = $productTitle;
                $product->island_id = $data->island_id;
                $product->seller_id = $data->seller_id;
                $product->status = $data->status;
                $product->product_explanation = $data->product_explanation;
                $product->category_id = $data->category_id;
                $product->price = $data->price;
                $product->tax = $data->tax;
                $product->sell_price = $data->sell_price;
                $product->cover_image = $data->cover_image;
                $product->cover_image_md = $data->cover_image_md;
                $product->cover_image_sm = $data->cover_image_sm;
                $product->url = $data->url;
                $product->shipment_method = $data->shipment_method;
                $product->preservation_method = $data->preservation_method;
                $product->package_type = $data->package_type;
                $product->quality_retention_temperature = $data->quality_retention_temperature;
                $product->expiration_taste_quality = $data->expiration_taste_quality;
                $product->use_scene = $data->use_scene; 

                $product->created_by = $data->created_by;   
                $product->updated_by = $data->updated_by; 
                $createProduct =  $product->save();
                if ($createProduct) {
                    // user activity log
                    createUserActivity($request, '記事作成', $user->name . '<' . $user->email . '> 記事作成 ' . $product->name . ' 論文', '一般的な', null);
                    $res['success'] = true;
                    $res['rs_class'] = 'success';
                    $res['message'] = trans('product.duplicate');
                    $res['redirects'] = url('/localvendor/products/list');
                    Session::flash('message', $res['message']); 
                } else {
                    $res['message'] = trans('product.failed');
                }
            }
        }
        return Response::json($res);
    }
    /**
     * Duplicate the specified resource in storage be seller
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function sellerDuplicateProduct(Request $request, $id)
    {
        $ids = explode(",", $id); 
        $user = Auth::user();
        foreach ($ids as $product_id) {
            if ($product_id != 'on') {
                $data = Product::findOrFail($product_id);
                $current_timestamp = Carbon::now()->timestamp;
                $productTitle = $data->name . "の複製"; 

                $product = new Product; 
                $product->name = $productTitle;
                $product->island_id = $data->island_id;
                $product->seller_id = $data->seller_id;
                $product->status = $data->status;
                $product->product_explanation = $data->product_explanation;
                $product->category_id = $data->category_id;
                $product->price = $data->price;
                $product->tax = $data->tax;
                $product->sell_price = $data->sell_price;
                $product->cover_image = $data->cover_image;
                $product->cover_image_md = $data->cover_image_md;
                $product->cover_image_sm = $data->cover_image_sm;
                $product->url = $data->url;
                $product->shipment_method = $data->shipment_method;
                $product->preservation_method = $data->preservation_method;
                $product->package_type = $data->package_type;
                $product->quality_retention_temperature = $data->quality_retention_temperature;
                $product->expiration_taste_quality = $data->expiration_taste_quality;
                $product->use_scene = $data->use_scene; 

                $product->created_by = $data->created_by;   
                $product->updated_by = $data->updated_by; 
                $createProduct =  $product->save();
                if ($createProduct) {
                    // user activity log
                    createUserActivity($request, '記事作成', $user->name . '<' . $user->email . '> 記事作成 ' . $product->name . ' 論文', '一般的な', null);
                    $res['success'] = true;
                    $res['rs_class'] = 'success';
                    $res['message'] = trans('product.duplicate');
                    $res['redirects'] = url('/seller/products/list');
                    Session::flash('message', $res['message']); 
                } else {
                    $res['message'] = trans('product.failed');
                }
            }
        }
        return Response::json($res);
    }
        /**
     * Change the specified Item Status for Local Vendor
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function ChangeProductStatus(Request $request, $id)
    {
        $ids = explode(",", $id);
        $status_check = array_pop($ids); 
        $user = Auth::user();
        foreach ($ids as $product_id) {
            if ($product_id != 'on') {                
                $product = Product::findOrFail($product_id);                
                if ($status_check == 1) {
                    $product->status = 1;                    
                } else if ($status_check == 2) {
                    $product->status = 2; 
                }  
                $updateProduct =  $product->save();
                
                if ($updateProduct) {                     
                    // user activity log
                    createUserActivity($request, '記事作成', $user->name . '<' . $user->email . '> 記事作成 ' . $product->name . ' 論文', '一般的な', null);
                    $res['success'] = true;
                    $res['rs_class'] = 'success';
                    $res['message'] = trans('product.status');
                    $res['redirects'] = url('/products');
                    Session::flash('message', $res['message']); 
                } else {
                    $res['message'] = trans('product.failed');
                }
            }
        }
        return Response::json($res);
    }
    /**
     * Change the specified Item Status for Local Vendor
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function vendorChangeProductStatus(Request $request, $id)
    {
        $ids = explode(",", $id);
        $status_check = array_pop($ids); 
        $user = Auth::user();
        foreach ($ids as $product_id) {
            if ($product_id != 'on') {                
                $product = Product::findOrFail($product_id);                
                if ($status_check == 1) {
                    $product->status = 1;                    
                } else if ($status_check == 2) {
                    $product->status = 2; 
                }  
                $updateProduct =  $product->save();
                
                if ($updateProduct) {                     
                    // user activity log
                    createUserActivity($request, '記事作成', $user->name . '<' . $user->email . '> 記事作成 ' . $product->name . ' 論文', '一般的な', null);
                    $res['success'] = true;
                    $res['rs_class'] = 'success';
                    $res['message'] = trans('product.status');
                    $res['redirects'] = url('/localvendor/products/list');
                    Session::flash('message', $res['message']); 
                } else {
                    $res['message'] = trans('product.failed');
                }
            }
        }
        return Response::json($res);
    }
    /**
     * Change the specified Item Status for Seller
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function sellerChangeProductStatus(Request $request, $id)
    {
        $ids = explode(",", $id);
        $status_check = array_pop($ids); 
        $user = Auth::user();
        foreach ($ids as $product_id) {
            if ($product_id != 'on') {                
                $product = Product::findOrFail($product_id);                
                if ($status_check == 1) {
                    $product->status = 1;                    
                } else if ($status_check == 2) {
                    $product->status = 2; 
                }  
                $updateProduct =  $product->save();
                
                if ($updateProduct) {                     
                    // user activity log
                    createUserActivity($request, '記事作成', $user->name . '<' . $user->email . '> 記事作成 ' . $product->name . ' 論文', '一般的な', null);
                    $res['success'] = true;
                    $res['rs_class'] = 'success';
                    $res['message'] = trans('product.status');
                    $res['redirects'] = url('/seller/products/list');
                    Session::flash('message', $res['message']); 
                } else {
                    $res['message'] = trans('product.failed');
                }
            }
        }
        return Response::json($res);
    }    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $salerData = SalesDestination::select('id', 'name')->get();
        $allergy = AllergyIndications::select('id', 'name')->where('is_recommended', 0)->get();
        $allergyRecommended = AllergyIndications::select('id', 'name')->where('is_recommended', 1)->get();
        $categories = Category::pluck('name', 'id');
        $users = User::role(['seller'])->pluck('name', 'id');
        $island = islandList();
        return view(
            'admin.products.create',
            compact(
                'island',
                'users',
                'salerData',
                'allergyRecommended',
                'allergy',
                'categories'
            )
        );
    }

    /**
     * Show the form for creating a new seller product create.
     *
     * @return \Illuminate\Http\Response
     */

    public function sellerProductCreate()
    {
        $user = Auth::user();
        $salerData = SalesDestination::select('id', 'name')->get();
        $allergy = AllergyIndications::select('id', 'name')->where('is_recommended', 0)->get();
        $allergyRecommended = AllergyIndications::select('id', 'name')->where('is_recommended', 1)->get();
        $categories = Category::pluck('name', 'id');
        $island = islandList();
        return view(
            'admin.products.seller_product_create',
            compact(
                'island',
                'salerData',
                'allergyRecommended',
                'allergy',
                'categories',
                'user'
            )
        );
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
        if ($user->hasRole('seller')) {
            $request->request->add([
                'seller_id' => $user->id, 
            ]);
        }

        $this->validate($request,[
            'island_id' => 'required',
            'seller_id' => 'required',
            'status' => 'required|in:0,1',
            'name' => 'required|max:40',
            'product_explanation' => 'required|max:2000',
            'category_id' => 'required',
            'price' => 'required|max:7',
            'tax' => 'required|in:8,10',
            'shipment_method' => 'max:100',
            'preservation_method' => 'max:100',
            'package_type' => 'max:100',
            'quality_retention_temperature' => 'max:100',
            'expiration_taste_quality' => 'max:2000',
            'use_scene' => 'max:2000',
            'url' => 'max:255',
            'cover_image' => 'required|max:50',
            'di_item.0' => 'max:100',
            'di_item.1' => 'max:100',
            'di_item.2' => 'max:100',
            'di_item.3' => 'max:100',
            'di_item.4' => 'max:100',
            'di_item.5' => 'max:100',
            'di_item.6' => 'max:100',
            'di_item.7' => 'max:100',
            'di_item.8' => 'max:2000',
            'di_item.9' => 'max:2000', 
            'ecmall_sku' => $request->ecmall_link=== '1' ? 'required|max:20': 'nullable',
            'ecmall_product_name' => $request->ecmall_link=== '1' ? 'required|max:127': 'nullable',
            'ecmall_product_description'  => $request->ecmall_link=== '1' ? 'required|max:5120': 'nullable',
            'ecmall_short_description' =>  $request->ecmall_link=== '1' ? 'required|max:400': 'nullable',
            'ecmall_shipping_weight' => $request->ecmall_link=== '1' ? 'required|numeric': 'nullable',
            'ecmall_stock_quantity' => $request->ecmall_link=== '1' ? 'required|numeric': 'nullable',
            'base_image' => $request->ecmall_link=== '1' ? 'required': 'nullable',
            'ecmall_quantity_update_status' => $request->ecmall_link=== '1' ? 'required': 'nullable',
        ], [
            'island_id' => trans('product.island_select'),
            'seller_id' => trans('product.seller_select'),
            'status' => trans('product.stauts'),
            'name' => trans('product.name'),
            'name.max' => trans('product.name_max'),
            'product_explanation' => trans('product.product_explanation'),
            'product_explanation.max' => trans('product.product_explanation_max'),
            'category_id' => trans('product.category_select'),
            'price' => trans('product.price'),
            'price.max' => trans('product.price_max'),
            'tax' => trans('product.tax'),
            'shipment_method.max' => trans('product.shipment_method_max'),
            'preservation_method.max' => trans('product.shipment_method_max'),
            'package_type.max' => trans('product.shipment_method_max'),
            'quality_retention_temperature.max' => trans('product.shipment_method_max'),
            'expiration_taste_quality.max' => trans('product.product_explanation_max'),
            'use_scene.max' => trans('product.product_explanation_max'),
            'url.max' => trans('product.url_max'),
            'cover_image' => trans('product.cover_image'),
            'di_item.0.max' => trans('product.shipment_method_max'),
            'di_item.1.max' => trans('product.shipment_method_max'),
            'di_item.2.max' => trans('product.shipment_method_max'),
            'di_item.3.max' => trans('product.shipment_method_max'),
            'di_item.4.max' => trans('product.shipment_method_max'),
            'di_item.5.max' => trans('product.shipment_method_max'),
            'di_item.6.max' => trans('product.shipment_method_max'),
            'di_item.7.max' => trans('product.shipment_method_max'),
            'di_item.8.max' => trans('product.product_explanation_max'),
            'di_item.9.max' => trans('product.product_explanation_max'), 
            'ecmall_sku.required' => $request->ecmall_link=== '1' ? trans('product.ecmall_required') : '',
            'ecmall_sku.max' => $request->ecmall_link=== '1' ? trans('product.ecmall_sku') : '',
            'ecmall_product_name.required' => $request->ecmall_link=== '1' ? trans('product.ecmall_required') : '',
            'ecmall_product_name.max' => $request->ecmall_link=== '1' ? trans('product.ecmall_product_name') : '',
            'ecmall_product_description.required' => $request->ecmall_link=== '1' ? trans('product.ecmall_required') : '',
            'ecmall_product_description.max'  => $request->ecmall_link=== '1' ? trans('product.ecmall_product_description') : '',
            'ecmall_short_description.required' => $request->ecmall_link=== '1' ? trans('product.ecmall_required') : '',
            'ecmall_short_description.max' =>  $request->ecmall_link=== '1' ? trans('product.ecmall_short_description') : '',
            'ecmall_shipping_weight.required' => $request->ecmall_link=== '1' ? trans('product.ecmall_required') : '',
            'ecmall_shipping_weight.numeric' => $request->ecmall_link=== '1' ? trans('product.ecmall_shipping_weight') : '',
            'ecmall_stock_quantity.required' => $request->ecmall_link=== '1' ? trans('product.ecmall_required') : '',
            'ecmall_stock_quantity.numeric' => $request->ecmall_link=== '1' ? trans('product.ecmall_stock_quantity') : '',
            'base_image.required' => $request->ecmall_link=== '1' ? trans('product.ecmall_required') : '',
            'ecmall_quantity_update_status.required' => $request->ecmall_link=== '1' ? trans('product.ecmall_required') : '',
        ]); 

        $data = $request->all(); 

        if (isset($data['url'])) {
            $youTubeUrl = $data['url'];
            $youTubeUrl = str_replace('watch?v=', 'embed/', $youTubeUrl);
            $data['url'] = $youTubeUrl;
        }

        $data['created_by'] = $user->id;
        $data['updated_by'] = $user->id;

        if ($data['cover_image'] != "") {
            $filename = $this->moveTempToLocation($data['cover_image'], 'cover_image');
            $data['cover_image'] = $filename['image'];
            $data['cover_image_md'] = $filename['image_md'];
            $data['cover_image_sm'] = $filename['image_sm'];
        } 

        $productData = Product::create($data);  
        for ($i = 1; $i <= 5; $i++) {
            if ($data['thumbnail_image_' . $i] != "" && $this->fileExists($data['thumbnail_image_' . $i])) {
                $filename = $this->moveTempToLocation($data['thumbnail_image_' . $i], 'thumbnail_image');
                ProductImage::create([
                    'product_id' => $productData->id,
                    'image' => $filename['image'],
                    'image_sm' => $filename['image_md'],
                    'image_md' => $filename['image_sm'],
                    'image_serial' => $i
                ]);
            }
        }  
        
        if($productData->ecmall_link == 1){
            $crosscell_id = $productData->id; 
            $new_crosscell = [
                'product_id' => $crosscell_id ,
                'cart_sku' => $data['ecmall_sku'],
                'created_by' =>  $user->id,
                'updated_by' => $user->id 
            ]; 
            SkuCrosssell::create($new_crosscell); 
            $ecmall_data = [
                'product_id' => $productData->id,
                'ecmall_sku' => $data['ecmall_sku'], 
                'ecmall_product_name' => $data['ecmall_product_name'],
                'ecmall_product_description' => $data['ecmall_product_description'],
                'ecmall_short_description' => $data['ecmall_short_description'],
                'ecmall_shipping_weight' => $data['ecmall_shipping_weight'],
                'ecmall_stock_quantity' => $data['ecmall_stock_quantity'],
                'ecmall_temperature' => $data['ecmall_temperature'],
                'created_by' => $user->id,
                'updated_by' => $user->id 
            ];  
            if ($data['base_image'] != "") {
                $filename = $this->moveTempToLocationEcmall($data['base_image'], 'base_image');
                $replace = env('ASSET_URL').'/upload';
                $find = "/upload";

                $ecmall_data['base_image'] = str_replace( $find ,$replace,$filename['image']);
                $ecmall_data['thumbnail_image'] = str_replace( $find ,$replace,$filename['image_md']);
                $ecmall_data['small_image'] = str_replace( $find ,$replace,$filename['image_sm']);
            }  
            $EcmallProductData = EcmallProducts::create($ecmall_data);  
            $ecmall_image_data = new EcmallProductImage;  
            
            for ($i = 1; $i <= 5; $i++) {
                if ($data['additional_image_' . $i] != "" && $this->fileExists($data['additional_image_' . $i])) {
                    $filename = $this->moveTempToLocationEcmall($data['additional_image_' . $i], 'additional_image');
                    EcmallProductImage::create([
                        'product_id' => $productData->id,
                        'image' => $filename['image'],
                        'image_md' => $filename['image_md'],
                        'image_sm' => $filename['image_sm'],
                        'image_serial' => $i, 
                        'created_by' =>  $user->id,
                        'updated_by' => $user->id 
                    ]); 
                }
            }   
            $ASSET_URL=env('ASSET_URL'); 
            $replace = env('ASSET_URL').'/upload';
            $find = "/upload";
            $ecmall_image_find = EcmallProductImage::where('product_id',$productData->id)->pluck('image')->toArray();
            if($ecmall_image_find){
                $string = implode(',',$ecmall_image_find);
                $addition_array = str_replace( $find ,$replace,$string); 
            }
            else{
                $addition_array = '';
            }
            $product_update = EcmallProducts::where('product_id', $productData->id)->pluck('id');  
            if($product_update){ 
                DB::table('ecmall_products')
                    ->where('product_id', $productData->id)
                    ->update([
                    'additional_image'  => $addition_array
                ]); 
            }  
        } 

        // Sales Destination data save
        if (!empty($data['salesDestination'])) {
            foreach ($request->salesDestination as $id) {
                ProductSalesDestination::create([
                    'sales_destination_id' => $id,
                    'product_id' => $productData->id
                ]);
            }
        }

        // Allergy Indications data save
        if (!empty($data['allergyRecommended'])) {
            foreach ($request->allergyRecommended as $id) {
                ProductAllergyIndication::create([
                    'allergy_indication_id' => $id,
                    'product_id' => $productData->id
                ]);
            }
        }

        // Product Information data save
        if (!empty($data['di_item'])) {
            foreach ($request->di_item as $item) {
                AdditionalInformations::create([
                    'description' => $item,
                    'product_id' => $productData->id
                ]);
            }
        }
        // user activity log
        createUserActivity($request, '作成する', $user->name . '<' . $user->email . '> 作成する ' . $productData->name . ' 製品', '一般的な', null);

        //check redirect url for seller
        $url = '';
        if ($user->hasRole('seller')) {
            $url = 'sellerProductList';
        } else {
            $url = 'products.index';
        }
        return redirect()->route($url)
            ->with('success', trans('product.create'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Product  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $sellerId = '';
        $productCreateAt = '';
        $productId = '';
        $categoryId = '';
        $islandId = '';

        $product = Product::find($id);
        if (!empty($product->created_at) || !empty($product->id) || !empty($product->seller_id) || !empty($product->category_id) || !empty($product->island_id)) {
            $productCreateAt = $product->created_at;
            $productId = $product->id;
            $sellerId = $product->seller_id;
            $categoryId = $product->category_id;
            $islandId = $product->island_id;
        }

        $created = new Carbon($productCreateAt);
        $now = Carbon::now();
        $created_days = $created->diffInDays($now);

        $users = User::where('id', $sellerId)->select('display_name', 'name')->first();

        //Product allergy table join with allergy indications
        $allergys = DB::table('product_allergy_indication')
            ->leftJoin('allergy_indications', 'product_allergy_indication.allergy_indication_id', '=', 'allergy_indications.id')
            ->where('product_id', $productId)
            ->select('name', 'is_recommended')
            ->get();

        //Product additional informations query
        $productAdditionals = DB::table('additional_informations')
            ->where('product_id', $productId)
            ->select('description')
            ->get();

        $productSalesDestinations = DB::table('product_sales_destination')
            ->leftJoin('sales_destinations', 'product_sales_destination.sales_destination_id', '=', 'sales_destinations.id')
            ->where('product_id', $productId)
            ->select('name')
            ->get();
        $category = Category::where('id', $categoryId)->select('name')->first();
        $island = Island::where('id', $islandId)->select('name')->first();

        return view('admin.products.detail', compact('product', 'users', 'allergys', 'productAdditionals', 'created_days', 'category', 'island','productSalesDestinations'));
    }

    public function downloadPdf($id)
    {
        $sellerId = '';
        $productCreateAt = '';
        $productId = '';
        $categoryId = '';
        $islandId = '';

        $product = Product::find($id);
        if (!empty($product->created_at) || !empty($product->id) || !empty($product->seller_id) || !empty($product->category_id) || !empty($product->island_id)) {
            $productCreateAt = $product->created_at;
            $productId = $product->id;
            $sellerId = $product->seller_id;
            $categoryId = $product->category_id;
            $islandId = $product->island_id;
        }

        $created = new Carbon($productCreateAt);
        $now = Carbon::now();
        $created_days = $created->diffInDays($now);

        $users = User::where('id', $sellerId)->select('display_name', 'name')->first();

        //Product allergy table join with allergy indications
        $allergys = DB::table('product_allergy_indication')
            ->leftJoin('allergy_indications', 'product_allergy_indication.allergy_indication_id', '=', 'allergy_indications.id')
            ->where('product_id', $productId)
            ->select('name', 'is_recommended')
            ->get();

        //Product additional informations query
        $productAdditionals = DB::table('additional_informations')
            ->where('product_id', $productId)
            ->select('description')
            ->get();

        $category = Category::where('id', $categoryId)->select('name')->first();
        $island = Island::where('id', $islandId)->select('name')->first();
        $pdf = PDF::loadView('admin.products.product-detail-pdf', compact('product', 'users', 'allergys', 'productAdditionals', 'created_days', 'category', 'island'));
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('test.pdf');
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function read($id)
    {
        $sellerId = '';
        $productCreateAt = '';
        $productId = '';
        $categoryId = '';
        $islandId = '';

        $product = Product::find($id);
        if (!empty($product->created_at) || !empty($product->id) || !empty($product->seller_id) || !empty($product->category_id) || !empty($product->island_id)) {
            $productCreateAt = $product->created_at;
            $productId = $product->id;
            $sellerId = $product->seller_id;
            $categoryId = $product->category_id;
            $islandId = $product->island_id;
        }

        $created = new Carbon($productCreateAt);
        $now = Carbon::now();
        $created_days = $created->diffInDays($now);

        $users = User::where('id', $sellerId)->select('display_name', 'name')->first();

        //Product allergy table join with allergy indications
        $allergys = DB::table('product_allergy_indication')
            ->leftJoin('allergy_indications', 'product_allergy_indication.allergy_indication_id', '=', 'allergy_indications.id')
            ->where('product_id', $productId)
            ->select('name', 'is_recommended')
            ->get();

        //Product additional informations query
        $productAdditionals = DB::table('additional_informations')
            ->where('product_id', $productId)
            ->select('description')
            ->get();

        $productSalesDestinations = DB::table('product_sales_destination')
            ->leftJoin('sales_destinations', 'product_sales_destination.sales_destination_id', '=', 'sales_destinations.id')
            ->where('product_id', $productId)
            ->select('name')
            ->get();
        $category = Category::where('id', $categoryId)->select('name')->first();
        $island = Island::where('id', $islandId)->select('name')->first();

        return view('admin.products.read', compact('product', 'users', 'allergys', 'productAdditionals', 'created_days', 'category', 'island','productSalesDestinations'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $salerData = SalesDestination::select('id', 'name')->get();
        $salerSelected = ProductSalesDestination::where('product_id', $product->id)->pluck('sales_destination_id')->toArray();
        $allergy = AllergyIndications::select('id', 'name')->where('is_recommended', 0)->get();
        $allergySelected = ProductAllergyIndication::where('product_id', $product->id)->pluck('allergy_indication_id')->toArray();
        $allergyRecommended = AllergyIndications::select('id', 'name')->where('is_recommended', 1)->get();
        $categories = Category::pluck('name', 'id');
        $users = User::role(['seller'])->pluck('name', 'id');
        $island = islandList(); 
        
        $ecmall = EcmallProducts::where('product_id', $product->id)->get()->first();  
        if(isset($ecmall->id)){
            $ecmall_images = EcmallProductImage::select('id','product_id','image','image_sm', 'image_md','created_at','updated_at','image_serial')->where('product_id', $product->id)->get();  
        }
        else{
            $ecmall_images = null; 
            $ecmall = null;
        }
        $additionalInformations = AdditionalInformations::where('product_id', $product->id)->get();
        return view(
            'admin.products.edit',
            compact(
                'product',
                'island',
                'users',
                'salerData',
                'allergyRecommended',
                'allergy',
                'categories',
                'salerSelected',
                'allergySelected',
                'additionalInformations',
                'ecmall',
                'ecmall_images', 
            )
        );
    }

    /**
     * Show the form for editing the specified resource for seller edit.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function sellerEdit($id)
    {
        $product = Product::findOrFail($id);
        if(unauthorizedAccess($product->seller_id)){
            $error= ['要求されたページはこのアカウントでは表示できません。'];
            return redirect()->route('sellerProductList')->withErrors($error);
        }
        $salerData = SalesDestination::select('id', 'name')->get();
        $salerSelected = ProductSalesDestination::where('product_id', $product->id)->pluck('sales_destination_id')->toArray();
        $allergy = AllergyIndications::select('id', 'name')->where('is_recommended', 0)->get();
        $allergySelected = ProductAllergyIndication::where('product_id', $product->id)->pluck('allergy_indication_id')->toArray();
        $allergyRecommended = AllergyIndications::select('id', 'name')->where('is_recommended', 1)->get();
        $categories = Category::pluck('name', 'id');
        $additionalInformations = AdditionalInformations::where('product_id', $product->id)->get();
        $ecmall = EcmallProducts::findOrFail($product->id);
        $ecmall_images = EcmallProductImage::select('product_id','base_image','small_image','thumbnail_image','additional_image')->where('product_id', $product->id)->get(); 
        foreach( $ecmall_images as $image ){
            $ecmall_image = $image;
        } 
        return view(
            'admin.products.seller_product_edit',
            compact(
                'product',
                'salerData',
                'allergyRecommended',
                'allergy',
                'categories',
                'salerSelected',
                'allergySelected',
                'additionalInformations',
                'ecmall',
                'ecmall_image'
            )
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Product  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {   
        request()->validate([
            'island_id' => 'required',
            'seller_id' => 'required',
            'status' => 'required|in:0,1',
            'name' => 'required|max:40',
            'product_explanation' => 'required|max:2000',
            'category_id' => 'required',
            'price' => 'required|max:7',
            'tax' => 'required|in:8,10',
            'shipment_method' => 'max:100',
            'preservation_method' => 'max:100',
            'package_type' => 'max:100',
            'quality_retention_temperature' => 'max:100',
            'expiration_taste_quality' => 'max:2000',
            'use_scene' => 'max:2000',
            'url' => 'max:255',
            'cover_image' => 'required|max:50',
            'di_item.0' => 'max:100',
            'di_item.1' => 'max:100',
            'di_item.2' => 'max:100',
            'di_item.3' => 'max:100',
            'di_item.4' => 'max:100',
            'di_item.5' => 'max:100',
            'di_item.6' => 'max:100',
            'di_item.7' => 'max:100',
            'di_item.8' => 'max:2000',
            'di_item.9' => 'max:2000', 
            'ecmall_sku' => $request->ecmall_link=== '1' ? 'required|max:20': 'nullable',
            'ecmall_product_name' => $request->ecmall_link=== '1' ? 'required|max:127': 'nullable',
            'ecmall_product_description'  => $request->ecmall_link=== '1' ? 'required|max:5120': 'nullable',
            'ecmall_short_description' =>  $request->ecmall_link=== '1' ? 'required|max:400': 'nullable',
            'ecmall_shipping_weight' => $request->ecmall_link=== '1' ? 'required|numeric': 'nullable',
            'ecmall_stock_quantity' => $request->ecmall_link=== '1' ? 'required|numeric': 'nullable',
            'base_image' => $request->ecmall_link=== '1' ? 'required': 'nullable',
            'ecmall_quantity_update_status' => $request->ecmall_link=== '1' ? 'required': 'nullable',
        ], [
            'island_id' => trans('product.island_select'),
            'seller_id' => trans('product.seller_select'),
            'status' => trans('product.stauts'),
            'name' => trans('product.name'),
            'name.max' => trans('product.name_max'),
            'product_explanation' => trans('product.product_explanation'),
            'product_explanation.max' => trans('product.product_explanation_max'),
            'category_id' => trans('product.category_select'),
            'price' => trans('product.price'),
            'price.max' => trans('product.price_max'),
            'tax' => trans('product.tax'),
            'shipment_method.max' => trans('product.shipment_method_max'),
            'preservation_method.max' => trans('product.shipment_method_max'),
            'package_type.max' => trans('product.shipment_method_max'),
            'quality_retention_temperature.max' => trans('product.shipment_method_max'),
            'expiration_taste_quality.max' => trans('product.product_explanation_max'),
            'use_scene.max' => trans('product.product_explanation_max'),
            'url.max' => trans('product.url_max'),
            'cover_image' => trans('product.cover_image'),
            'di_item.0.max' => trans('product.shipment_method_max'),
            'di_item.1.max' => trans('product.shipment_method_max'),
            'di_item.2.max' => trans('product.shipment_method_max'),
            'di_item.3.max' => trans('product.shipment_method_max'),
            'di_item.4.max' => trans('product.shipment_method_max'),
            'di_item.5.max' => trans('product.shipment_method_max'),
            'di_item.6.max' => trans('product.shipment_method_max'),
            'di_item.7.max' => trans('product.shipment_method_max'),
            'di_item.8.max' => trans('product.product_explanation_max'),
            'di_item.9.max' => trans('product.product_explanation_max'), 
            'ecmall_sku.required' => $request->ecmall_link=== '1' ? trans('product.ecmall_required') : '',
            'ecmall_sku.max' => $request->ecmall_link=== '1' ? trans('product.ecmall_sku') : '',
            'ecmall_product_name.required' => $request->ecmall_link=== '1' ? trans('product.ecmall_required') : '',
            'ecmall_product_name.max' => $request->ecmall_link=== '1' ? trans('product.ecmall_product_name') : '',
            'ecmall_product_description.required' => $request->ecmall_link=== '1' ? trans('product.ecmall_required') : '',
            'ecmall_product_description.max'  => $request->ecmall_link=== '1' ? trans('product.ecmall_product_description') : '',
            'ecmall_short_description.required' => $request->ecmall_link=== '1' ? trans('product.ecmall_required') : '',
            'ecmall_short_description.max' =>  $request->ecmall_link=== '1' ? trans('product.ecmall_short_description') : '',
            'ecmall_shipping_weight.required' => $request->ecmall_link=== '1' ? trans('product.ecmall_required') : '',
            'ecmall_shipping_weight.numeric' => $request->ecmall_link=== '1' ? trans('product.ecmall_shipping_weight') : '',
            'ecmall_stock_quantity.required' => $request->ecmall_link=== '1' ? trans('product.ecmall_required') : '',
            'ecmall_stock_quantity.numeric' => $request->ecmall_link=== '1' ? trans('product.ecmall_stock_quantity') : '',
            'base_image.required' => $request->ecmall_link=== '1' ? trans('product.ecmall_required') : '',
            'ecmall_quantity_update_status.required' => $request->ecmall_link=== '1' ? trans('product.ecmall_required') : '',
        ]); 

        $user = Auth::user();
        $oldData = Product::find($id);
        $oldEcmallData = EcmallProducts::where('product_id', $oldData->id)->get()->first();
        $oldCrosscell = SkuCrosssell::where('product_id', $oldData->id)->get()->first();  
        $data = $request->all();

        if (isset($data['url'])) {
            $youTubeUrl = $data['url'];
            $youTubeUrl = str_replace('watch?v=', 'embed/', $youTubeUrl);
            $data['url'] = $youTubeUrl;
        }

        $data['updated_by'] = $user->id;

        if (isset($oldData->cover_image) && $data['cover_image'] != $oldData->cover_image && $data['cover_image'] != "" && $this->fileExists($data['cover_image'])) {
            Storage::disk('s3')->delete("/public$oldData->cover_image");
            Storage::disk('s3')->delete("/public$oldData->cover_image_sm");
            Storage::disk('s3')->delete("/public$oldData->cover_image_md");
            $filename = $this->moveTempToLocation($data['cover_image'], 'cover_image');
            $data['cover_image'] = $filename['image'];
            $data['cover_image_md'] = $filename['image_md'];
            $data['cover_image_sm'] = $filename['image_sm'];
        }else {
            $data['cover_image'] = $oldData->cover_image;
            $data['cover_image_md'] = $oldData->cover_image_md;
            $data['cover_image_sm'] = $oldData->cover_image_sm;
        }

        $productData = $oldData->update($data); 
        for ($i = 1; $i <= 5; $i++) {
            $imageId = $data['thumbnail_id_' . $i];
            $thumbnailImage = $data['thumbnail_image_' . $i];
            $process = $this->processImage($imageId, $thumbnailImage);
            if ($process) {
                if ($data['thumbnail_image_' . $i] != "" && $this->fileExists($data['thumbnail_image_' . $i])) {
                    $filename = $this->moveTempToLocation($data['thumbnail_image_' . $i], 'thumbnail_image');
                    ProductImage::create([
                        'product_id' => $id,
                        'image' => $filename['image'],
                        'image_sm' => $filename['image_md'],
                        'image_md' => $filename['image_sm'],
                        'image_serial' => $i
                    ]);
                }
            }
        }
        
        if($data['ecmall_link'] == '1'){ 
            if($oldCrosscell){
                $crosscell_id = $oldData->id; 
                $update_crosscell = [ 
                    'product_id' => $crosscell_id ,
                    'cart_sku' => $data['ecmall_sku'],
                    'created_by' =>  $user->id,
                    'updated_by' => $user->id 
                ];  
                $crosscell_modify = $oldCrosscell->update($update_crosscell); 
            } 
            else{
                $crosscell_id = $oldData->id; 
                $new_crosscell = [
                    'product_id' => $crosscell_id ,
                    'cart_sku' => $data['ecmall_sku'],
                    'created_by' =>  $user->id,
                    'updated_by' => $user->id 
                ]; 
                SkuCrosssell::create($new_crosscell); 
            }
            $ecmall_data = [ 
                'ecmall_sku' => $data['ecmall_sku'], 
                'ecmall_product_name' => $data['ecmall_product_name'],
                'ecmall_product_description' => $data['ecmall_product_description'],
                'ecmall_short_description' => $data['ecmall_short_description'],
                'ecmall_shipping_weight' => $data['ecmall_shipping_weight'],
                'ecmall_stock_quantity' => $data['ecmall_stock_quantity'],
                'ecmall_temperature' => $data['ecmall_temperature'],
                'created_by' => $user->id,
                'updated_by' => $user->id 
            ]; 
            if($oldEcmallData != null){ 
                if (isset($oldEcmallData->base_image) && $data['base_image'] != $oldEcmallData['base_image'] && $data['base_image'] != "" ) {
                    Storage::disk('s3')->delete("/public$oldEcmallData->base_image");
                    Storage::disk('s3')->delete("/public$oldEcmallData->base_image_sm");
                    Storage::disk('s3')->delete("/public$oldEcmallData->base_image_md");
                    $filename = $this->moveTempToLocationEcmall($data['base_image'], 'base_image');
                    $replace = env('ASSET_URL').'/upload';
                    $find = "/upload";

                    $ecmall_data['base_image'] = str_replace( $find ,$replace,$filename['image']);
                    $ecmall_data['thumbnail_image'] = str_replace( $find ,$replace,$filename['image_md']);
                    $ecmall_data['small_image'] = str_replace( $find ,$replace,$filename['image_sm']); 
                }else {
                    $ecmall_data['base_image'] = $oldEcmallData['base_image'];
                    $ecmall_data['small_image'] = $oldEcmallData['base_image'];
                    $ecmall_data['thumbnail_image'] = $oldEcmallData['base_image'];
                } 
                $EcmallProductData = $oldEcmallData->update($ecmall_data);
            }else {
                if ($data['base_image'] != "") {
                    $filename = $this->moveTempToLocationEcmall($data['base_image'], 'base_image');
                    $replace = env('ASSET_URL').'/upload';
                    $find = "/upload";

                    $ecmall_data['base_image'] = str_replace( $find ,$replace,$filename['image']);
                    $ecmall_data['thumbnail_image'] = str_replace( $find ,$replace,$filename['image_md']);
                    $ecmall_data['small_image'] = str_replace( $find ,$replace,$filename['image_sm']);
                }  
                $ecmall_data['product_id'] = $oldData->id;
                $EcmallProductData = EcmallProducts::create($ecmall_data);
            }
            
            $e_p['aa'] = $oldData->id;  
            for ($i = 1; $i <= 5; $i++) { 
                $imageId = $data['additional_id_' . $i];
                $additionalImage = $data['additional_image_' . $i]; 
                $process = $this->processEcmallImage($imageId, $additionalImage);
                
                if ($process) {
                    if ($data['additional_image_' . $i] != "" && $this->fileExists($data['additional_image_' . $i])) {
                        $filename = $this->moveTempToLocationEcmall($data['additional_image_' . $i], 'additional_image');
                        EcmallProductImage::create([
                            'product_id' => $e_p['aa'],
                            'image' => $filename['image'],
                            'image_sm' => $filename['image_sm'],
                            'image_md' => $filename['image_md'],
                            'image_serial' => $i, 
                            'created_by' =>  $user->id,
                            'updated_by' => $user->id 
                        ]); 
                    }
                }
            }  
            $replace = env('ASSET_URL').'/upload';
            $find = "/upload";
            $ecmall_image_find = EcmallProductImage::where('product_id',$e_p['aa'])->pluck('image')->toArray();
            if($ecmall_image_find){
                $string = implode(',',$ecmall_image_find);
                $addition_array = str_replace( $find ,$replace,$string); 
            }
            else{
                $addition_array = '';
            }
            
            $product_update = EcmallProducts::where('product_id', $e_p['aa'])->pluck('id');  
            if($product_update){ 
                DB::table('ecmall_products')
                    ->where('product_id', $e_p['aa'])
                    ->update([
                    'additional_image'  => $addition_array
                ]); 
            } 
        }else if($data['ecmall_link'] == '0'){  
            $Ecmall_image_delete = EcmallProductImage::where('product_id',$oldData->id); 
            $EcmallData_delete = EcmallProducts::where('product_id', $oldData->id);
            $oldCrosscell_delete = SkuCrosssell::where('product_id', $oldData->id);  
            if($Ecmall_image_delete){ 
                $Ecmall_image_delete->delete(); 
            }
            if($EcmallData_delete){
                $EcmallData_delete->delete();
            }
            if($oldCrosscell_delete){
                $oldCrosscell_delete->delete();
            }
        }

        // Allergy Indications data delete
        $allergyRecommended_to_delete = ProductAllergyIndication::where('product_id', $oldData->id)->get();
        foreach ($allergyRecommended_to_delete as $data) {
            ProductAllergyIndication::where('id', $data->id)->delete();
        }

        // Allergy Indications data save
        if (!empty($request->allergyRecommended)) {
            foreach ($request->allergyRecommended as $id) {
                ProductAllergyIndication::create([
                    'allergy_indication_id' => $id,
                    'product_id' => $oldData->id
                ]);
            }
        }

        // Sales Destination data delete
        $salesDestination_to_delete = ProductSalesDestination::where('product_id', $oldData->id)->get();
        foreach ($salesDestination_to_delete as $data) {
            ProductSalesDestination::where('id', $data->id)->delete();
        }

        // Sales Destination data save
        if (!empty($request->salesDestination)) {
            foreach ($request->salesDestination as $id) {
                ProductSalesDestination::create([
                    'sales_destination_id' => $id,
                    'product_id' => $oldData->id
                ]);
            }
        }

        // Product Information data save
        if($request->di_item){
            foreach ($request->di_item as $item) {
                AdditionalInformations::where('id', $item['id'])->update([
                    'description' => $item['val']
                ]);
            }
        }

        // user activity log
        createUserActivity($request, '更新', $user->name . '<' . $user->email . '> 更新 ' . $oldData->name . ' 製品', '一般的な', null);
        /**
         * check redirect url for seller
         */
        $url = '';
        if ($user->hasRole('seller')) {
            $url = 'sellerProductList';
        } else {
            $url = 'products.index';
        }
        return redirect()->route($url)
            ->with('success', trans('product.update'));
    }


    /**
     * Delete Thumb Image file
     */

    private function deleteThumbImage($list = array())
    {

        if (!empty($list)) {
            foreach ($list as $imageId) {
                $thumbImage = ProductImage::find($imageId);
                Storage::disk('s3')->delete("/public" . $thumbImage->image);
                Storage::disk('s3')->delete("/public" . $thumbImage->image_sm);
                Storage::disk('s3')->delete("/public" . $thumbImage->image_md);
                ProductImage::where('id', $imageId)->delete();
            }
        }
    }

    private function processImage($imageId, $newImage)
    {
        $thumbImage = ProductImage::find($imageId);
        $processNewUpload = false;
        if (isset($thumbImage->image) && $newImage == "") {
            Storage::disk('s3')->delete("/public" . $thumbImage->image);
            Storage::disk('s3')->delete("/public" . $thumbImage->image_sm);
            Storage::disk('s3')->delete("/public" . $thumbImage->image_md);
            ProductImage::where('id', $imageId)->delete();
        } else if (isset($thumbImage->image) && $newImage != $thumbImage->image && $this->fileExists($newImage)) {
            Storage::disk('s3')->delete("/public" . $thumbImage->image);
            Storage::disk('s3')->delete("/public" . $thumbImage->image_sm);
            Storage::disk('s3')->delete("/public" . $thumbImage->image_md);
            ProductImage::where('id', $imageId)->delete();
            $processNewUpload = true;
        } else if (!isset($thumbImage->image) && $newImage != "") {
            $processNewUpload = true;
        }

        return $processNewUpload;
    }

    private function processEcmallImage($imageId, $newImage)
    {
        $addImage = EcmallProductImage::find($imageId); 
        $processNewUpload = false;
        if (isset($addImage->image) && $newImage == "") {
            Storage::disk('s3')->delete("/public" . $addImage->image);
            Storage::disk('s3')->delete("/public" . $addImage->image_sm);
            Storage::disk('s3')->delete("/public" . $addImage->image_md);
            EcmallProductImage::where('id', $imageId)->delete();
        } else if (isset($addImage->image) && $newImage != $addImage->image && $this->fileExists($newImage)) {
            Storage::disk('s3')->delete("/public" . $addImage->image);
            Storage::disk('s3')->delete("/public" . $addImage->image_sm);
            Storage::disk('s3')->delete("/public" . $addImage->image_md);
            EcmallProductImage::where('id', $imageId)->delete();
            $processNewUpload = true;
        } else if (!isset($addImage->image) && $newImage != "") {
            $processNewUpload = true;
        }

        return $processNewUpload;
    }

    /**
     * Product all Image for Zip Archive then download
     * downloadFile function start
     */
    public function downloadFile(Request $request)
    {
        try {
            $user = Auth::user();
            $s3Disk = Storage::disk('s3');
            $validator = Validator::make($request->all(), [
                'product_id' => 'required'
            ]);

            if ($validator->fails()) {
                return Redirect::back()->withErrors(['msg', $validator->errors()]);
            }

            $product = Product::findOrFail($request->product_id);

            $source_disk = 's3';
            $source_path = '';
            //All imagess array create 
            $file_names = array($product->cover_image);
            //multi image download
            if (!empty($product->productImages) && isset($product->productImages)) {
                foreach ($product->productImages as $img) {
                    array_push($file_names, $img->image);
                }
            }
            //product all file zip
            $zip = new Filesystem(new ZipArchiveAdapter(public_path('すべての画像（' . $user->id . '）.zip')));

            foreach ($file_names as $file_name) {
                $file_content = Storage::disk($source_disk)->get("/public" . $file_name);
                $zip->put($file_name, $file_content);
            }
            $zip->getAdapter()->getArchive()->close();
            //return response()->download(public_path(), 'すべての画像（'.$user->id.'）.zip')->deleteFileAfterSend(true);
            return redirect('すべての画像（' . $user->id . '）.zip');
        } catch (\Exception $e) {
            return Redirect::back()->withErrors(['msg', '何かがおかしかった。 しばらくしてからお試しください。']);
        }
    }

    /**
     * Product copy a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function productCopy(Request $request, $id)
    {
        $user = Auth::user();
        $data = Product::find($id);
        $newName = "(copy) " . $data->name;
        if (strlen($newName) > 40) {
            $newName = substr($newName, 0, 40);
        }

        $filename = $this->imageCopyAndUpload('cover_image', $data->cover_image, $data->cover_image_md, $data->cover_image_sm);
        $productData = Product::create([
            'island_id' => $data->island_id,
            'seller_id' => $data->seller_id,
            'status' => 0,
            'name' => $newName,
            'product_explanation' => $data->product_explanation,
            'category_id' => $data->category_id,
            'price' => $data->price,
            'tax' => $data->tax,
            'sell_price' => $data->sell_price,
            'cover_image' => $filename['image'],
            'cover_image_md' => $filename['image_md'],
            'cover_image_sm' => $filename['image_sm'],
            'url' => $data->url,
            'shipment_method' => $data->shipment_method,
            'preservation_method' => $data->preservation_method,
            'package_type' => $data->package_type,
            'quality_retention_temperature' => $data->quality_retention_temperature,
            'expiration_taste_quality' => $data->expiration_taste_quality,
            'use_scene' => $data->use_scene,
            'created_by' => $user->id,
            'updated_by' => $user->id
        ]);

        $images = ProductImage::where('product_id', $data->id)->get();
        if (!empty($images)) {
            foreach ($images as $image) {
                $filename = $this->imageCopyAndUpload('thumbnail_image', $image->image, $image->image_md, $image->image_sm);
                ProductImage::create([
                    'product_id' => $productData->id,
                    'image' => $filename['image'],
                    'image_sm' => $filename['image_md'],
                    'image_md' => $filename['image_sm']
                ]);
            }
        }

        // Sales Destination data save
        $salesDestination = ProductSalesDestination::where('product_id', $data->id)->pluck('sales_destination_id');
        if (!empty($salesDestination)) {
            foreach ($salesDestination as $id) {
                ProductSalesDestination::create([
                    'sales_destination_id' => $id,
                    'product_id' => $productData->id
                ]);
            }
        }

        // Allergy Indications data save
        $allergyRecommended = ProductAllergyIndication::where('product_id', $data->id)->pluck('allergy_indication_id');
        if (!empty($allergyRecommended)) {
            foreach ($allergyRecommended as $id) {
                ProductAllergyIndication::create([
                    'allergy_indication_id' => $id,
                    'product_id' => $productData->id
                ]);
            }
        }

        // Product Information data save
        $di_item = AdditionalInformations::where('product_id', $data->id)->pluck('description');
        if (!empty($di_item)) {
            foreach ($di_item as $item) {
                AdditionalInformations::create([
                    'description' => $item,
                    'product_id' => $productData->id
                ]);
            }
        }
        // user activity log
        createUserActivity($request, 'コピー', $user->name . '<' . $user->email . '> コピー ' . $productData->name . ' 製品', '一般的な', null);

        return response()->json($productData);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Product  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $user = Auth::user();
        $productData = Product::find($id);
        Product::find($id)->delete();
        // user activity log
        createUserActivity($request, '削除する', $user->name . '<' . $user->email . '> 削除する ' . $productData->name . '<' . $productData->name . '> アカウント', '一般的な', null);

        return response()->json(['success' => trans('product.delete')]);
    }

    private function fileExists($fileName)
    {
        return Storage::disk('s3')->exists("public/" . $this->temp . "/" . $fileName);
    }

    // seller product store
    public function sellerProductStore(Request $request)
    { 
        $user = Auth::user();
        if ($user->hasRole('seller')) {
            $request->request->add([
                'seller_id' => $user->id,
                'island_id' => $user->island_id
            ]);
        }

        request()->validate([
            'island_id' => 'required',
            'seller_id' => 'required',
            'status' => 'required|in:0,1',
            'name' => 'required|max:40',
            'product_explanation' => 'required|max:2000',
            'category_id' => 'required',
            'shipment_method' => 'max:100',
            'preservation_method' => 'max:100',
            'package_type' => 'max:100',
            'quality_retention_temperature' => 'max:100',
            'expiration_taste_quality' => 'max:2000',
            'use_scene' => 'max:2000',
            'url' => 'max:255',
            'cover_image' => 'required|max:50',
            'di_item.0' => 'max:100',
            'di_item.1' => 'max:100',
            'di_item.2' => 'max:100',
            'di_item.3' => 'max:100',
            'di_item.4' => 'max:100',
            'di_item.5' => 'max:100',
            'di_item.6' => 'max:100',
            'di_item.7' => 'max:100',
            'di_item.8' => 'max:2000',
            'di_item.9' => 'max:2000'
        ], [
            'island_id' => trans('product.island_select'),
            'seller_id' => trans('product.seller_select'),
            'status' => trans('product.stauts'),
            'name' => trans('product.name'),
            'name.max' => trans('product.name_max'),
            'product_explanation' => trans('product.product_explanation'),
            'product_explanation.max' => trans('product.product_explanation_max'),
            'category_id' => trans('product.category_select'),
            'shipment_method.max' => trans('product.shipment_method_max'),
            'preservation_method.max' => trans('product.shipment_method_max'),
            'package_type.max' => trans('product.shipment_method_max'),
            'quality_retention_temperature.max' => trans('product.shipment_method_max'),
            'expiration_taste_quality.max' => trans('product.product_explanation_max'),
            'use_scene.max' => trans('product.product_explanation_max'),
            'url.max' => trans('product.url_max'),
            'cover_image' => trans('product.cover_image'),
            'di_item.0.max' => trans('product.shipment_method_max'),
            'di_item.1.max' => trans('product.shipment_method_max'),
            'di_item.2.max' => trans('product.shipment_method_max'),
            'di_item.3.max' => trans('product.shipment_method_max'),
            'di_item.4.max' => trans('product.shipment_method_max'),
            'di_item.5.max' => trans('product.shipment_method_max'),
            'di_item.6.max' => trans('product.shipment_method_max'),
            'di_item.7.max' => trans('product.shipment_method_max'),
            'di_item.8.max' => trans('product.product_explanation_max'),
            'di_item.9.max' => trans('product.product_explanation_max')
        ]);

        $data = $request->all();

        if (isset($data['url'])) {
            $youTubeUrl = $data['url'];
            $youTubeUrl = str_replace('watch?v=', 'embed/', $youTubeUrl);
            $data['url'] = $youTubeUrl;
        }

        $data['created_by'] = $user->id;
        $data['updated_by'] = $user->id;

        if ($data['cover_image'] != "" && $this->fileExists($data['cover_image'])) {
            $filename = $this->moveTempToLocation($data['cover_image'], 'cover_image');
            $data['cover_image'] = $filename['image'];
            $data['cover_image_md'] = $filename['image_md'];
            $data['cover_image_sm'] = $filename['image_sm'];
        }

        $productData = Product::create($data);

        for ($i = 1; $i <= 5; $i++) {
            if ($data['thumbnail_image_' . $i] != "" && $this->fileExists($data['thumbnail_image_' . $i])) {
                $filename = $this->moveTempToLocation($data['thumbnail_image_' . $i], 'thumbnail_image');
                ProductImage::create([
                    'product_id' => $productData->id,
                    'image' => $filename['image'],
                    'image_sm' => $filename['image_md'],
                    'image_md' => $filename['image_sm'],
                    'image_serial' => $i
                ]);
            }
        }

        // Sales Destination data save
        if (!empty($data['salesDestination'])) {
            foreach ($request->salesDestination as $id) {
                ProductSalesDestination::create([
                    'sales_destination_id' => $id,
                    'product_id' => $productData->id
                ]);
            }
        }

        // Allergy Indications data save
        if (!empty($data['allergyRecommended'])) {
            foreach ($request->allergyRecommended as $id) {
                ProductAllergyIndication::create([
                    'allergy_indication_id' => $id,
                    'product_id' => $productData->id
                ]);
            }
        }

        // Product Information data save
        if (!empty($data['di_item'])) {
            foreach ($request->di_item as $item) {
                AdditionalInformations::create([
                    'description' => $item,
                    'product_id' => $productData->id
                ]);
            }
        }
        // user activity log
        createUserActivity($request, '作成する', $user->name . '<' . $user->email . '> 作成する ' . $productData->name . ' 製品', '一般的な', null);

        //check redirect url for seller
        $url = '';
        if ($user->hasRole('seller')) {
            $url = 'sellerProductList';
        } else {
            $url = 'products.index';
        }
        return redirect()->route($url)
            ->with('success', trans('product.create'));
       
    }
    public function sellerProductUpdate(Request $request, $id)
    {
        request()->validate([
            'island_id' => 'required',
            'seller_id' => 'required',
            'status' => 'required',
            'name' => 'required|max:40',
            'product_explanation' => 'required|max:2000',
            'category_id' => 'required',
            'shipment_method' => 'max:100',
            'url' => 'max:255',
            'preservation_method' => 'max:100',
            'package_type' => 'max:100',
            'quality_retention_temperature' => 'max:100',
            'expiration_taste_quality' => 'max:2000',
            'use_scene' => 'max:2000',
            'di_item.0.val' => 'max:100',
            'di_item.1.val' => 'max:100',
            'di_item.2.val' => 'max:100',
            'di_item.3.val' => 'max:100',
            'di_item.4.val' => 'max:100',
            'di_item.5.val' => 'max:100',
            'di_item.6.val' => 'max:100',
            'di_item.7.val' => 'max:100',
            'di_item.8.val' => 'max:2000',
            'di_item.9.val' => 'max:2000',
            'cover_image' => 'required|max:50',
        ], [
            'island_id' => trans('product.island_select'),
            'seller_id' => trans('product.seller_select'),
            'status' => trans('product.stauts'),
            'name' => trans('product.name'),
            'name.max' => trans('product.name_max'),
            'product_explanation' => trans('product.product_explanation'),
            'product_explanation.max' => trans('product.product_explanation_max'),
            'category_id' => trans('product.category_select'),
            'shipment_method.max' => trans('product.shipment_method_max'),
            'preservation_method.max' => trans('product.shipment_method_max'),
            'package_type.max' => trans('product.shipment_method_max'),
            'quality_retention_temperature.max' => trans('product.shipment_method_max'),
            'expiration_taste_quality.max' => trans('product.product_explanation_max'),
            'use_scene.max' => trans('product.product_explanation_max'),
            'url.max' => trans('product.url_max'),
            'di_item.0.val.max' => trans('product.shipment_method_max'),
            'di_item.1.val.max' => trans('product.shipment_method_max'),
            'di_item.2.val.max' => trans('product.shipment_method_max'),
            'di_item.3.val.max' => trans('product.shipment_method_max'),
            'di_item.4.val.max' => trans('product.shipment_method_max'),
            'di_item.5.val.max' => trans('product.shipment_method_max'),
            'di_item.6.val.max' => trans('product.shipment_method_max'),
            'di_item.7.val.max' => trans('product.shipment_method_max'),
            'di_item.8.val.max' => trans('product.product_explanation_max'),
            'di_item.9.val.max' => trans('product.product_explanation_max'),
            'cover_image' => trans('product.cover_image')
        ]);

        $user = Auth::user();
        $oldData = Product::find($id);
        $data = $request->all();

        if (isset($data['url'])) {
            $youTubeUrl = $data['url'];
            $youTubeUrl = str_replace('watch?v=', 'embed/', $youTubeUrl);
            $data['url'] = $youTubeUrl;
        }

        $data['updated_by'] = $user->id;

        if (isset($oldData->cover_image) && $data['cover_image'] != $oldData->cover_image && $data['cover_image'] != "" && $this->fileExists($data['cover_image'])) {
            Storage::disk('s3')->delete("/public$oldData->cover_image");
            Storage::disk('s3')->delete("/public$oldData->cover_image_sm");
            Storage::disk('s3')->delete("/public$oldData->cover_image_md");
            $filename = $this->moveTempToLocation($data['cover_image'], 'cover_image');
            $data['cover_image'] = $filename['image'];
            $data['cover_image_md'] = $filename['image_md'];
            $data['cover_image_sm'] = $filename['image_sm'];
        }else {
            $data['cover_image'] = $oldData->cover_image;
            $data['cover_image_md'] = $oldData->cover_image_md;
            $data['cover_image_sm'] = $oldData->cover_image_sm;
        }

        $productData = $oldData->update($data);

        for ($i = 1; $i <= 5; $i++) {
            $imageId = $data['thumbnail_id_' . $i];
            $thumbnailImage = $data['thumbnail_image_' . $i];
            $process = $this->processImage($imageId, $thumbnailImage);
            if ($process) {
                if ($data['thumbnail_image_' . $i] != "" && $this->fileExists($data['thumbnail_image_' . $i])) {
                    $filename = $this->moveTempToLocation($data['thumbnail_image_' . $i], 'thumbnail_image');
                    ProductImage::create([
                        'product_id' => $id,
                        'image' => $filename['image'],
                        'image_sm' => $filename['image_md'],
                        'image_md' => $filename['image_sm'],
                        'image_serial' => $i
                    ]);
                }
            }
        }
        // Allergy Indications data delete
        $allergyRecommended_to_delete = ProductAllergyIndication::where('product_id', $oldData->id)->get();
        foreach ($allergyRecommended_to_delete as $data) {
            ProductAllergyIndication::where('id', $data->id)->delete();
        }

        // Allergy Indications data save
        if (!empty($request->allergyRecommended)) {
            foreach ($request->allergyRecommended as $id) {
                ProductAllergyIndication::create([
                    'allergy_indication_id' => $id,
                    'product_id' => $oldData->id
                ]);
            }
        }

        // Sales Destination data delete
        $salesDestination_to_delete = ProductSalesDestination::where('product_id', $oldData->id)->get();
        foreach ($salesDestination_to_delete as $data) {
            ProductSalesDestination::where('id', $data->id)->delete();
        }

        // Sales Destination data save
        if (!empty($request->salesDestination)) {
            foreach ($request->salesDestination as $id) {
                ProductSalesDestination::create([
                    'sales_destination_id' => $id,
                    'product_id' => $oldData->id
                ]);
            }
        }

        // Product Information data save

        foreach ($request->di_item as $item) {
            AdditionalInformations::where('id', $item['id'])->update([
                'description' => $item['val']
            ]);
        }

        // user activity log
        createUserActivity($request, '更新', $user->name . '<' . $user->email . '> 更新 ' . $oldData->name . ' 製品', '一般的な', null);
        /**
         * check redirect url for seller
         */
        $url = '';
        if ($user->hasRole('seller')) {
            $url = 'sellerProductList';
        } else {
            $url = 'products.index';
        }
        return redirect()->route($url)
            ->with('success', trans('product.update'));
    }
}
