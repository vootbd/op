<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
 */
// Ck editor routes
Route::get('ckeditor', 'CkeditorController@index');
Route::post('ckeditor/upload', 'CkeditorController@upload')->name('ckeditor.upload');

// Auth routes
Auth::routes(['register' => false]);

// Admin routes
Route::get('/', 'Auth\LoginController@showLoginForm');
Route::group(['middleware' => ['auth', 'checkActiveUser'], ['role:admin|operator|seller|buyer']], function () {
    // User management resources
    Route::resource('roles', 'RoleController');
    Route::resource('users', 'UserController');
    Route::get('update/user/status/{id}', 'UserController@updateUserStatus')->name('updateUserStatus');
    Route::get('seller/create', 'UserController@sellerCreate')->name('sellerCreate');
    Route::post('/seller/delete/{id}', 'UserController@sellerDelete');
    Route::post('/seller/status-change/{id}', 'UserController@sellerStatus');
    Route::get('buyer/create', 'UserController@buyerCreate')->name('buyerCreate');
    Route::get('profile', 'UserController@profile')->name('userProfile');
    Route::get('block/user/list', 'Auth\LoginController@blockedUserList')->name('blockedUserList');
    route::any('unblock/user', 'Auth\LoginController@unblockUser')->name('unblock.user');
    Route::get('operator/top', 'UserController@sellerList')->name('seller.list');
    Route::get('localvendor/seller/list', 'UserController@localvendorSellerList')->name('localvendor.seller.list');
    Route::get('buyer/list', 'UserController@buyerList')->name('buyer.list');
    Route::post('/buyer/status-change/{id}', 'UserController@changeBuyerStatus');
    Route::post('/buyer/delete/{id}', 'UserController@buyerDelete');
    Route::get('localvendor/create','UserController@localVendorCreate')->name('localvendor.create');
    Route::post('localvendor/store','UserController@localVendorStore')->name('localvendor.store');
    Route::get('localvendor/list', 'UserController@localVendorList')->name('localvendor.list');
    Route::get('localvendor/profile/{id}/edit','UserController@localVendorEdit')->name('localvendor.edit');
    Route::post('/localvendor/list/status-change/{id}', 'UserController@changeVendorStatus');
    Route::post('/localvendor/delete/{id}', 'UserController@localvendorDelete');
    Route::get('soft/delete/check/{email}', 'UserController@softDeleteCheck')->name('soft.delete.check');
    Route::get('user/active/{email}', 'UserController@userActive')->name('user.active');

    /*************** Setting route start*****************/
    Route::get('settings', 'UserController@settings')->name('settings');
    Route::get('settings/edit/name', 'UserController@editName')->name('editName');
    Route::post('settings/update/name', 'UserController@updateName')->name('updateName');
    Route::get('settings/edit/email', 'UserController@editMail')->name('editEmail');
    Route::post('settings/update/email', 'UserController@updateMail')->name('updateMail');
    Route::get('settings/change/password', function () {
        $user = Auth::user();
        return view('admin.users.change-password')->with(['user' => $user]);
    })->name('showChangePassword');
    Route::post('settings/change/password', 'UserController@changePassword')->name('changePassword');

    Route::get('user/activities', 'UserActivityController@index')->name('user.activities');
    Route::delete('user/activities/{id}', 'UserActivityController@destroy')->name('user.activities.destroy');
    Route::get('island/users/{id}', 'UserController@islandUsersList');
    /***************Setting route end*******************/

    /******** Seller Profile route start*********/
    Route::resource('seller/profile', 'SellerProfileController');
    Route::get('seller/profile/create/{id}', 'SellerProfileController@create')->name('seller.profile.create');
    Route::get('get/form/data', 'SellerProfileController@getReailTimeFormData')->name('get.form.data');
    Route::get('get/type/check', 'SellerProfileController@getTypeCheck')->name('get.type.check');
    /******** Seller Profile route end*********/

    /******** govenement/island route start*********/
    Route::resource('islands', 'IslandController');
    /******** govenement/island route end*********/

    /******** Categorie route start*********/
    Route::resource('categories', 'CategoryController');
    // categories route start
    Route::get('/categories', 'CategoryController@index')->name('categories.index');
    Route::post('/categories', 'CategoryController@store')->name('categories.store');
    Route::put('/categories/update/{id}', 'CategoryController@update')->name('categories.update');
    Route::any('/categories/update-sorting', 'CategoryController@ajaxUpdate');
    Route::any('/categories/delete', 'CategoryController@ajaxDelete');
    Route::get('/categories/{category}/edit', 'CategoryController@edit')->name('categories.edit');
    Route::get('/categories/thumbnail/sorting','CategoryController@thumbnailSorting')->name('categories.thumbnail.sorting');
    //Route::get('/categories/seller/create','CategoryController@sellerCategoryCreate')->name('seller.category.create');
    /******** Categorie route end*********/

    /******** Product route start*********/
    Route::resource('products', 'ProductController');
    Route::get('dropdownlist/getvendor/{id}','ProductController@getVendor');
    Route::get('dropdownlist/getseller/{id}','ProductController@getSeller');
    Route::get('/dropdown/getseller-island/{id}','ProductController@getSellerIsland');
    Route::get('products/read/{id}', 'ProductController@read')->name('products.read');
    Route::get('products/detail/{id}', 'ProductController@show')->name('products.detail');
    Route::get('products/copy/{id}', 'ProductController@ProductCopy')->name('products.copy');
    Route::get('seller/products/list', 'ProductController@sellerProductList')->name('sellerProductList');
    Route::post('seller/products/store', 'ProductController@sellerProductStore')->name('seller.product.store');
    Route::get('localvendor/products/list', 'ProductController@localvendorProductList')->name('localvendorProductList');  
    Route::post('/products/copy/{id}', 'ProductController@DuplicateProduct');
    Route::post('/products/localvendor/copy/{id}', 'ProductController@vendorDuplicateProduct');
    Route::post('/products/seller/copy/{id}', 'ProductController@sellerDuplicateProduct');
    Route::post('/products/remove/{id}', 'ProductController@removeProduct');
    Route::post('/localvendor/products/remove/{id}', 'ProductController@vendorRemoveProduct');
    Route::post('/seller/products/remove/{id}', 'ProductController@sellerRemoveProduct');
    Route::post('/products/status-change/{id}', 'ProductController@ChangeProductStatus');
    Route::post('/products/localvendor/status-change/{id}', 'ProductController@vendorChangeProductStatus');
    Route::post('/products/seller/status-change/{id}', 'ProductController@sellerChangeProductStatus');
    Route::get('seller/island/{id}', 'ProductController@sellerIsland');
    Route::put('seller/products/update/{id}', 'ProductController@sellerProductUpdate')->name('seller.product.update');
    Route::get('buyer/top', 'ProductController@buyerProductList')->name('buyer.top');
    Route::get('seller/products/create', 'ProductController@sellerProductCreate')->name('seller.product.create');
    Route::get('seller/products/edit/{id}', 'ProductController@sellerEdit')->name('seller.product.edit');
    Route::get('/downloadFile', 'ProductController@downloadFile')->name('downloadFile');
    Route::get('/downloadPdf/{id}', 'ProductController@downloadPdf')->name('download.pdf');
    /******** Product route end*********/
    Route::any('image-upload-single', 'FileUploadController@upload')->name('image-upload');
    /******** inquirys route start*********/
    Route::resource('inquirys', 'InquiryController');
    /******** inquirys route end*********/

    /******** pages route start*********/
    Route::resource('pages', 'PageController')->except('show');
    Route::post('/pages/copy/{id}', 'PageController@duplicatePage');
    Route::post('/pages/status-change/{id}', 'PageController@changePageStatus');
    Route::post('/urlCheck/pages', 'PageController@urlCheck')->name('pages.urlCheck');
    Route::get('page/{slug}', 'PagesController@getPageDetails')->where('slug', '.*');
    /******** pages route end*********/

    //directory route start*********/
    Route::post('/directories', 'DirectoryController@store')->name('directories.store');
    Route::resource('/directories', 'DirectoryController');
    Route::any('/directories/order', 'DirectoryController@ajaxUpdate');
    //directory route end*********/

    /******** comments route start*********/
    Route::resource('comments', 'CommentContoller');
    Route::any('comments/create/data/{id}', 'CommentContoller@store')->name('comments.store');
    Route::get('comments/{id}/edit', 'CommentContoller@store')->name('comments.edit');
    /******** comments route end*********/

    /******** pdf generate route start**/
    Route::get('generate-pdf', 'PDFController@index');
    Route::get('pdf', 'PDFController@generatePDF');
    /******** pdf generate route end*****/


    /**
     * CSV related routes start
     */
    Route::get('csvs/island/create', 'CSVController@csvIsland')->name('csvs.island');
    Route::get('csvs/product/create', 'CSVController@csvProduct')->name('csvs.product');
    Route::get('csvs/category/create', 'CSVController@csvCategory')->name('csvs.category');
    Route::post('/csvs/update-settings', 'CSVController@updateSettings');
    Route::post('/csvs/islands/store', 'CSVImportExportController@importIslands')->name('csv.island-create');
    Route::get('/csvs/islands/export', 'CSVImportExportController@exportIsland')->name('csv.island-export');
    Route::post('/csvs/category/store', 'CSVImportExportController@importCategories')->name('csv.category.create');
    Route::get('/csvs/categories/export', 'CSVImportExportController@exportCategory')->name('csv.category-export');
    Route::post('/csvs/products/store', 'CSVImportExportController@importProducts')->name('csv.product-create');
    Route::post('/csvs/products/export', 'CSVImportExportController@exportProduct')->name('csv.product.export');
    Route::get('csvs/settings/{type}', 'CSVController@csvControl')->name('csv.control');
    Route::get('csvs/errors/download/{file}', 'CSVImportExportController@downloadCsv')->name('csv.download.errors');
    Route::post('shima-share-file-export/', 'CSVImportExportController@shimaShareFileExport')->name('shima-share-file-export');
    Route::post('shima-share-product-export/', 'CSVImportExportController@shimaShareProductDelete')->name('shima-share-product-delete');
    
    /**
      * Csv related routes end
      */
    Route::get('ledgers/sheet', 'LedgerController@ledgerSheet')->name('ledger.sheet');
    Route::any('/ledgers/sheet/pdf', 'LedgerController@ledgerSheetPdf')->name('ledgers.sheet.pdf');

    // Media
    Route::resource('medias', 'MediaController')->except('show');
    Route::any('/medias/upload', 'MediaController@upload')->name('medias.upload');
});
/**
 * Load page details by url param
 */

if (App::environment('staging') || App::environment('production')) {
  URL::forceScheme('https');
}
