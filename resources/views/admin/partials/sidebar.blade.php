<div class="sidebar" id="sidebar">
    <span id="navbarClose" class="rito rito-x"></span>
    @role('admin')
    {{-- Admin Site Management nav block --}}
    @if(Auth::user()->can('role-edit') || Auth::user()->can('activity-log') || Auth::user()->can('account-unblock'))
    <div class="d-flex flex-column account-block">
        <h2 class="title">
            <img src="{{ asset('image/icon-buy.svg') }}" alt="buyer icon">
            <span class="text">サイト管理</span>
        </h2>
        @can('role-edit')
        <a class="menu-link{{ Route::is('roles.edit') ? ' active' : '' }}" href="{{ route('roles.edit', 1) }}">
            <span>アカウント権限</span>
            <span class="rito rito-chevron-right"></span>
        </a>
        @endcan
        @can('activity-log')
        <a class="menu-link{{ Route::is('user.activities') ? ' active' : '' }}" href="{{ route('user.activities') }}">
            <span>ユーザーログ</span>
            <span class="rito rito-chevron-right"></span>
        </a>
        @endcan
        @can('account-unblock')
        <a class="menu-link{{ Route::is('blockedUserList') ? ' active' : '' }}" href="{{ route('blockedUserList') }}">
            <span>アカウントのロック解除</span>
            <span class="rito rito-chevron-right"></span>
        </a>
        @endcan
    </div>
    @endif

    {{-- Admin Account Management nav block --}}
    @if(Auth::user()->can('all-account-list') || Auth::user()->can('operator-create') || Auth::user()->can('seller-create') || Auth::user()->can('buyer-create'))
    <div class="d-flex flex-column account-block">
        <h2 class="title">
            <img class="icon-support" src="{{ asset('image/icon-buy.svg') }}" alt="island icon">
            <span class="text">アカウント管理</span>
        </h2>
        @can('all-account-list')
        <a class="menu-link{{ Route::is('users.index', 'users.edit') ? ' active' : '' }}" href="{{ route('users.index') }}">
            <span>アカウント一覧</span>
            <span class="rito rito-chevron-right"></span>
        </a>
        @endcan
        @can('operator-create')
        <a class="menu-link{{ Route::is('users.create') ? ' active' : '' }}" href="{{ route('users.create') }}">
            <span>運用アカウント登録</span>
            <span class="rito rito-chevron-right"></span>
        </a>
        @endcan
        @can('seller-create')
        <a class="menu-link{{ Route::is('sellerCreate') ? ' active' : '' }}" href="{{ route('sellerCreate') }}">
            <span>売り手アカウント登録</span>
            <span class="rito rito-chevron-right"></span>
        </a>
        @endcan
        @can('buyer-create')
        <a class="menu-link{{ Route::is('buyerCreate') ? ' active' : '' }}" href="{{ route('buyerCreate') }}">
            <span>バイヤーアカウント登録</span>
            <span class="rito rito-chevron-right"></span>
        </a>
        @endcan
    </div>
    @endif
    @if(Auth::user()->can('product-create') || Auth::user()->can('product-list') || Auth::user()->can('category-list') || Auth::user()->can('category-create'))
    {{-- Operator Products Management nav block --}}
    <div class="d-flex flex-column account-block">
        <h2 class="title">
            <img class="icon-support" src="{{ asset('image/icon-product.svg') }}" alt="island icon">
            <span class="text">商品管理</span>
        </h2>
        @can('product-list')
        <a class="menu-link{{ Route::is('products.index', 'products.show', 'products.read', 'products.edit') ? ' active' : '' }}" href="{{ route('products.index') }}">
            <span>商品一覧</span>
            <span class="rito rito-chevron-right"></span>
        </a>
        @endcan
        @can('product-create')
        <a class="menu-link{{ Route::is('products.create') ? ' active' : '' }}" href="{{ route('products.create') }}">
            <span>商品登録</span>
            <span class="rito rito-chevron-right"></span>
        </a>
        @endcan
        @can('category-list')
        <a class="menu-link{{ Route::is('categories.index') ? ' active' : '' }}" href="{{ route('categories.index') }}">
            <span>カテゴリー一覧</span>
            <span class="rito rito-chevron-right"></span>
        </a>
        @endcan
        @can('category-create')
        <a class="menu-link{{ Route::is('categories.create', 'categories.edit') ? ' active' : '' }}" href="{{ route('categories.create') }}">
            <span>カテゴリー登録</span>
            <span class="rito rito-chevron-right"></span>
        </a>
        @endcan
    </div>
    @endif
    @if(Auth::user()->can('island-create') || Auth::user()->can('island-list'))
    {{-- Operator Island nav block --}}
    <div class="d-flex flex-column account-block">
        <h2 class="title">
            <img src="{{ asset('image/icon-island.svg') }}" alt="island icon">
            <span class="text">離島管理</span>
        </h2>
        @can('island-create')
        <a class="menu-link{{ Route::is('islands.index', 'islands.edit') ? ' active' : '' }}" href="{{ route('islands.index') }}">
            <span>離島一覧</span>
            <span class="rito rito-chevron-right"></span>
        </a>
        @endcan
        @can('island-list')
        <a class="menu-link{{ Route::is('islands.create') ? ' active' : '' }}" href="{{ route('islands.create') }}">
            <span>離島登録</span>
            <span class="rito rito-chevron-right"></span>
        </a>
        @endcan
    </div>
    @endif
    @endrole
    @role('operator')
    @if(Auth::user()->can('product-create') || Auth::user()->can('product-list') || Auth::user()->can('category-list') || Auth::user()->can('category-create'))
    {{-- Operator Products Management nav block --}}
    <div class="d-flex flex-column account-block">
        <h2 class="title">
            <img class="icon-support" src="{{ asset('image/icon-product.svg') }}" alt="island icon">
            <span class="text">商品管理</span>
        </h2>
        @can('product-create')
        <a class="menu-link{{ Route::is('products.index', 'products.show', 'products.read', 'products.edit') ? ' active' : '' }}" href="{{ route('products.index') }}">
            <span>商品一覧</span>
            <span class="rito rito-chevron-right"></span>
        </a>
        @endcan
        @can('product-list')
        <a class="menu-link{{ Route::is('products.create') ? ' active' : '' }}" href="{{ route('products.create') }}">
            <span>商品登録</span>
            <span class="rito rito-chevron-right"></span>
        </a>
        @endcan
        @can('category-list')
        <a class="menu-link{{ Route::is('categories.index', 'categories.edit') ? ' active' : '' }}" href="{{ route('categories.index') }}">
            <span>カテゴリー一覧</span>
            <span class="rito rito-chevron-right"></span>
        </a>
        @endcan
        <!-- @can('category-create')
        <a class="menu-link{{ Route::is('categories.create', 'categories.edit') ? ' active' : '' }}" href="{{ route('categories.create') }}">
            <span>カテゴリー登録</span>
            <span class="rito rito-chevron-right"></span>
        </a>
        @endcan -->
        {{-- @can('csv-product') --}}
        <a class="menu-link{{ Route::is('csvs.product') ? ' active' : '' }}" href="{{ route('csvs.product') }}">
            <span>商品 CSV 登録</span>
            <span class="rito rito-chevron-right"></span>
        </a>
        {{-- @endcan --}}
        {{-- @can('csv-category') --}}
        <a class="menu-link{{ Route::is('csvs.category') ? ' active' : '' }}" href="{{ route('csvs.category') }}">
            <span>カテゴリー CSV 登録</span>
            <span class="rito rito-chevron-right"></span>
        </a>
        {{-- @endcan --}}
    </div>
    @endif
    {{-- Opportunity management nav block --}}
    <div class="d-flex flex-column account-block">
        <h2 class="title">
            <img class="icon-support" src="{{ asset('image/opportunity-icon.jpg') }}" alt="island icon">
            <span class="text">商談管理</span>
        </h2>
        {{-- @can('product-create') --}}
        <a class="menu-link{{ Route::is('products.index', 'products.show', 'products.read', 'products.edit') ? ' active' : '' }}" href="{{ route('products.index') }}">
            <span>商談一覧</span>
            <span class="rito rito-chevron-right"></span>
        </a>
        {{-- @endcan --}}
        {{-- @can('product-list') --}}
        <a class="menu-link{{ Route::is('products.create') ? ' active' : '' }}" href="{{ route('products.create') }}">
            <span>商談登録</span>
            <span class="rito rito-chevron-right"></span>
        </a>
        {{-- @endcan --}}

        {{-- @can('csv-product')  --}}
        <a class="menu-link{{ Route::is('csvs.product') ? ' active' : '' }}" href="{{ route('csvs.product') }}">
            <span>メッセージ</span>
            <span class="rito rito-chevron-right"></span>
        </a>
        {{-- @endcan  --}}

    </div>
    {{-- Operator Buyer Management nav block --}}
    @if(Auth::user()->can('buyer-create') || Auth::user()->can('buyer-list'))
    <div class="d-flex flex-column account-block">
        <h2 class="title">
            <img src="{{ asset('image/icon-buy.svg') }}" alt="buyer icon">
            <span class="text">バイヤー管理</span>
        </h2>
        @can('buyer-list')
        <a class="menu-link{{ Route::is('buyer.list') ? ' active' : '' }}" href="{{ route('buyer.list') }}">
            <span>アカウント一覧</span>
            <span class="rito rito-chevron-right"></span>
        </a>
        @endcan
        @can('buyer-create')
        <a class="menu-link{{ Route::is('buyerCreate') ? ' active' : '' }}" href="{{ route('buyerCreate') }}">
            <span>アカウント登録</span>
            <span class="rito rito-chevron-right"></span>
        </a>
        @endcan
    </div>
    @endif

       {{-- Localvendor management nav block --}}
       <div class="d-flex flex-column account-block">
        <h2 class="title">
            <img class="icon-support" src="{{ asset('image/localvendor-icon.jpg') }}" alt="island icon">
            <span class="text">地域商社管理</span>
        </h2>
        {{-- @can('product-create') --}}
        <a class="menu-link{{ Route::is('localvendor.list') ? ' active' : '' }}" href="{{ route('localvendor.list') }}">
            <span>地域商社一覧</span>
            <span class="rito rito-chevron-right"></span>
        </a>
        {{-- @endcan --}}
        {{-- @can('product-list') --}}
        <a class="menu-link{{ Route::is('localvendor.create') ? ' active' : '' }}" href="{{ route('localvendor.create') }}">
            <span>地域商社登録</span>
            <span class="rito rito-chevron-right"></span>
        </a>
        {{-- @endcan --}}


    </div>

    {{-- Operator Seller Management nav block --}}
    @if(Auth::user()->can('seller-create') || Auth::user()->can('seller-list'))
    <div class="d-flex flex-column account-block">
        <h2 class="title">
            <img src="{{ asset('image/icon-buy.svg') }}" alt="buyer icon">
            <span class="text">事業者管理</span>
        </h2>
        @can('seller-create')
        <a class="menu-link{{ Route::is('seller.list' ,'seller.profile.create','profile.edit') ? ' active' : '' }}" href="{{ route('seller.list') }}">
            <span>事業者一覧</span>
            <span class="rito rito-chevron-right"></span>
        </a>
        @endcan
        @can('seller-list')
        <a class="menu-link{{ Route::is('sellerCreate') ? ' active' : '' }}" href="{{ route('sellerCreate') }}">
            <span>事業者登録</span>
            <span class="rito rito-chevron-right"></span>
        </a>
        @endcan
    </div>
    @endif
    @if(Auth::user()->can('island-create') || Auth::user()->can('island-list'))
    {{-- Operator Island nav block --}}
    <div class="d-flex flex-column account-block">
        <h2 class="title">
            <img src="{{ asset('image/icon-island.svg') }}" alt="island icon">
            <span class="text">離島管理</span>
        </h2>
        @can('island-create')
        <a class="menu-link{{ Route::is('islands.index', 'islands.edit') ? ' active' : '' }}" href="{{ route('islands.index') }}">
            <span>離島一覧</span>
            <span class="rito rito-chevron-right"></span>
        </a>
        @endcan
        @can('island-list')
        <a class="menu-link{{ Route::is('islands.create') ? ' active' : '' }}" href="{{ route('islands.create') }}">
            <span>離島登録</span>
            <span class="rito rito-chevron-right"></span>
        </a>
        @endcan
        {{-- @can('csv-island') --}}
        <a class="menu-link{{ Route::is('csvs.island') ? ' active' : '' }}" href="{{ route('csvs.island') }}">
            <span>離島 CSV 登録</span>
            <span class="rito rito-chevron-right"></span>
        </a>
        {{-- @endcan --}}
    </div>
    @endif
    {{-- Operator Page Management nav block --}}
    {{-- @if(Auth::user()->can('pages.index') || Auth::user()->can('pages.create')) --}}
    <div class="d-flex flex-column account-block">
        <h2 class="title">
            <img src="{{ asset('image/page-icon.jpg') }}" alt="page icon">
            <span class="text">ページ管理</span>
        </h2>
        {{-- @can('pages.create') --}}
        <a class="menu-link{{ Route::is('pages.index' ,'pages.create','pages.edit') ? ' active' : '' }}" href="{{ route('pages.index') }}">
            <span>ページ一覧</span>
            <span class="rito rito-chevron-right"></span>
        </a>
        {{-- @endcan
        @can('pages.index') --}}
        <a class="menu-link{{ Route::is('pages.create') ? ' active' : '' }}" href="{{ route('pages.create') }}">
            <span>ページ登録</span>
            <span class="rito rito-chevron-right"></span>
        </a>
        {{-- @endcan --}}
        {{-- @can('directories') --}}
        <a class="menu-link{{ Route::is('directories', 'directories.index') ? ' active' : '' }}" href="{{ route('directories.index') }}">
            <span> ページ階層管理 </span>
            <span class="rito rito-chevron-right"></span>
        </a>
        {{-- @endcan --}}
    </div>
    {{-- @endif --}}
    {{-- Operator Media Management nav block --}}
    {{-- @if(Auth::user()->can('medias.index') || Auth::user()->can('medias.create')) --}}
    <div class="d-flex flex-column account-block">
        <h2 class="title">
            <img src="{{ asset('image/media-icon.jpg') }}" alt="page icon">
            <span class="text">メディア管理</span>
        </h2>
        {{-- @can('medias.create') --}}
        <a class="menu-link{{ Route::is('medias.index','medias.edit') ? ' active' : '' }}" href="{{ route('medias.index') }}">
            <span>メディア一覧</span>
            <span class="rito rito-chevron-right"></span>
        </a>
        {{-- @endcan
        @can('pages.index') --}}
        <a class="menu-link{{ Route::is('medias.create') ? ' active' : '' }}" href="{{ route('medias.create') }}">
            <span>メディア登録</span>
            <span class="rito rito-chevron-right"></span>
        </a>
        {{-- @endcan --}}
    </div>
    {{-- @endif --}}
    {{-- Operator ledger nav block --}}
    <div class="d-flex flex-column account-block">
        <h2 class="title">
            <img src="{{ asset('image/icon-leggersheet.svg') }}" alt="ledger icon">
            <span class="text">帳票</span>
        </h2>
        {{-- @can('island-list') --}}
        <a class="menu-link{{ Route::is('ledger.sheet') ? ' active' : '' }}" href="{{ route('ledger.sheet') }}">
            <span>帳票作成</span>
            <span class="rito rito-chevron-right"></span>
        </a>
        {{-- @endcan --}}
    </div>
    @endrole

    @role('vendor')
    {{-- @if(Auth::user()->can('product-create') || Auth::user()->can('product-list')) --}}
    {{-- Operator Products Management nav block --}}
    <div class="d-flex flex-column account-block">
        <h2 class="title">
            <img class="icon-support" src="{{ asset('image/icon-product.svg') }}" alt="island icon">
            <span class="text">商品管理</span>
        </h2>
        {{-- @can('product-create') --}}
        <a class="menu-link{{ Route::is('localvendorProductList', 'products.show', 'products.read', 'products.edit') ? ' active' : '' }}" href="{{ route('localvendorProductList') }}">
            <span>商品一覧</span>
            <span class="rito rito-chevron-right"></span>
        </a>
        {{-- @endcan --}}
        {{-- @can('product-list') --}}
        <a class="menu-link{{ Route::is('products.create') ? ' active' : '' }}" href="{{ route('products.create') }}">
            <span>商品登録</span>
            <span class="rito rito-chevron-right"></span>
        </a>
        {{-- @endcan --}}

        {{-- @can('csv-product')  --}}
        <a class="menu-link{{ Route::is('csvs.product') ? ' active' : '' }}" href="{{ route('csvs.product') }}">
            <span>商品 CSV 登録</span>
            <span class="rito rito-chevron-right"></span>
        </a>
        {{-- @endcan  --}}

    </div>
    {{-- @endif --}}
     {{-- Vendor Opportunity  Management nav block --}}
    {{-- @if(Auth::user()->can('product-create') || Auth::user()->can('product-list') || Auth::user()->can('category-list') || Auth::user()->can('category-create')) --}}
    {{-- Operator Products Management nav block --}}
    <div class="d-flex flex-column account-block">
        <h2 class="title">
            <img class="icon-support" src="{{ asset('image/opportunity-icon.jpg') }}" alt="island icon">
            <span class="text">商談管理</span>
        </h2>
        {{-- @can('product-create') --}}
        <a class="menu-link{{ Route::is('products.index', 'products.show', 'products.read', 'products.edit') ? ' active' : '' }}" href="{{ route('products.index') }}">
            <span>商談一覧</span>
            <span class="rito rito-chevron-right"></span>
        </a>
        {{-- @endcan --}}
        {{-- @can('product-list') --}}
        <a class="menu-link{{ Route::is('products.create') ? ' active' : '' }}" href="{{ route('products.create') }}">
            <span>商談登録</span>
            <span class="rito rito-chevron-right"></span>
        </a>
        {{-- @endcan --}}

        {{-- @can('csv-product')  --}}
        <a class="menu-link{{ Route::is('csvs.product') ? ' active' : '' }}" href="{{ route('csvs.product') }}">
            <span>メッセージ</span>
            <span class="rito rito-chevron-right"></span>
        </a>
        {{-- @endcan  --}}

    </div>
    {{-- @endif --}}
    {{-- Operator Seller Management nav block --}}
    @if( Auth::user()->can('seller-list'))
    <div class="d-flex flex-column account-block">
        <h2 class="title">
            <img src="{{ asset('image/icon-buy.svg') }}" alt="buyer icon">
            <span class="text">事業者管理</span>
        </h2>
        {{-- @can('seller-create') --}}
        <a class="menu-link{{ Route::is('localvendor.seller.list' ,'seller.profile.create','profile.edit') ? ' active' : '' }}" href="{{ route('localvendor.seller.list') }}">
            <span>事業者一覧</span>
            <span class="rito rito-chevron-right"></span>
        </a>
        {{-- @endcan --}}
    </div>
    @endif
    <div class="d-flex flex-column account-block">
        <h2 class="title">
            <img src="{{ asset('image/icon-leggersheet.svg') }}" alt="ledger icon">
            <span class="text">帳票</span>
        </h2>

        <a class="menu-link{{ Route::is('ledger.sheet') ? ' active' : '' }}" href="{{ route('ledger.sheet') }}">
            <span>帳票作成</span>
            <span class="rito rito-chevron-right"></span>
        </a>

    </div>
    @endrole

    @role('seller')
    {{-- Seller Products Management nav block --}}
    @if(Auth::user()->can('seller-product-list') || Auth::user()->can('seller-product-create'))
    <div class="d-flex flex-column account-block">
        <h2 class="title">
            <img class="icon-support" src="{{ asset('image/icon-product.svg') }}" alt="island icon">
            <span class="text">商品管理</span>
        </h2>
        @can('seller-product-list')
        <a class="menu-link{{ Route::is('products.show', 'products.read', 'seller.product.edit', 'sellerProductList') ? ' active' : '' }}" href="{{ route('sellerProductList') }}">
            <span>商品一覧</span>
            <span class="rito rito-chevron-right"></span>
        </a>
        @endcan
        @can('seller-product-create')
        <a class="menu-link{{ Route::is('seller.product.create') ? ' active' : '' }}" href="{{ route('seller.product.create') }}">
            <span>商品登録</span>
            <span class="rito rito-chevron-right"></span>
        </a>
        @endcan
        @can('seller-product-create')
        <a class="menu-link{{ Route::is('seller.product.create') ? ' active' : '' }}" href="{{ route('seller.product.create') }}">
            <span>商品CSV登録</span>
            <span class="rito rito-chevron-right"></span>
        </a>
        @endcan
    </div>
    @endif
    {{-- Seller Profile Management nav block --}}
    {{-- <div class="d-flex flex-column account-block">
        <h2 class="title">
            <img src="{{ asset('image/icon-buy.svg') }}" alt="buyer icon">
            <span class="text">事業者管理</span>
        </h2>
        <a class="menu-link{{ Route::is('seller.profile.create','profile.edit') ? ' active' : '' }} mouse-pointer" href="{{ (Auth::user()->is_profile == 0) ? URL::to('/').'/seller/profile/create/'.Auth::user()->id : URL::to('/').'/seller/profile/'.Auth::user()->id.'/edit' }}" id="seller-profile-left-menu-{{Auth::user()->id}}">
            <span>プロフィール</span>
            <span class="rito rito-chevron-right"></span>
        </a>
        <div class="d-none common-data-id type-block align-items-center justify-content-center profile-edit-time-{{Auth::user()->id}}" id="real-time-type-{{Auth::user()->id}}" data-toggle="modal" data-target="#profile-modal-id" data-id="{{Auth::user()->id}}" onclick="getSellerIdByClick({{Auth::user()->id}},'{{ (Auth::user()->is_profile == 0) ? URL::to('/').'/seller/profile/create/'.Auth::user()->id : URL::to('/').'/seller/profile/'.Auth::user()->id.'/edit' }}')"><i class="fa fa-user-circle-o" aria-hidden="true"></i> <span>編集中</span>
        </div>
        <a class="menu-link{{ Route::is('comments.create','comments.edit') ? ' active' : '' }}" href="{{ (Auth::user()->is_comment == 0) ? URL::to('/').'/comments/create?seller='.Auth::user()->id : URL::to('/').'/comments/'.Auth::user()->id.'/edit' }}" id="comment-type-seller-left-menu-{{Auth::user()->id}}">
            <span>商談メモ</span>
            <span class="rito rito-chevron-right"></span>
        </a>
        <div class="d-none common-data-id type-block align-items-center justify-content-center comment-type-operator-{{Auth::user()->id}}" id="comment-type-operator-{{Auth::user()->id}}" data-toggle="modal" data-target="#profile-modal-id" data-id="{{Auth::user()->id}}" onclick="getSellerIdByClick({{Auth::user()->id}},'{{ (Auth::user()->is_comment == 0) ? URL::to('/').'/comments/create?seller='.Auth::user()->id : URL::to('/').'/comments/'.Auth::user()->id.'/edit' }}')"><i class="fa fa-user-circle-o" aria-hidden="true"></i> <span>編集中</span>
        </div>
    </div> --}}

    {{-- seller ledger nav block --}}
    {{-- <div class="d-flex flex-column account-block">
        <h2 class="title">
            <img src="{{ asset('image/icon-leggersheet.svg') }}" alt="ledger icon">
            <span class="text">帳票</span>
        </h2>

        <a class="menu-link{{ Route::is('ledger.sheet') ? ' active' : '' }}" href="{{ route('ledger.sheet') }}">
            <span>帳票作成</span>
            <span class="rito rito-chevron-right"></span>
        </a>

    </div> --}}
    @endrole
    @role('buyer')
    {{-- Buyer Search by category nav block --}}
    @can('buyer-product-list')
    <div class="d-flex flex-column account-block">
        <h2 class="title">
            <span class="text pl-0">カテゴリーから探す</span>
        </h2>
        @foreach(getCategories() as $data)
        <a class="menu-link" href="{{ route('buyer.top') }}?category={{ $data->id }}">
            <span>{{ $data->name }}</span>
            <span class="rito rito-chevron-right"></span>
        </a>
        @endforeach
    </div>
    {{-- Buyer Search by remote island nav block --}}
    <div class="d-flex flex-column account-block">
        <h2 class="title">
            <span class="text pl-0">離島から探す</span>
        </h2>
        @foreach(getIslands() as $data)
        <a class="menu-link" href="{{ route('buyer.top') }}?island={{ $data->id }}">
            <span>{{ $data->name }}</span>
            <span class="rito rito-chevron-right"></span>
        </a>
        @endforeach
    </div>
    @endcan
    @endrole
    {{-- Profile Settings nav block --}}
    <div class="d-flex flex-column account-block">
        <h2 class="title">
            <img src="{{ asset('image/icon-tool.svg') }}" alt="support icon">
            <span class="text">設定</span>
        </h2>
        @role('seller')
        <a class="menu-link{{ Route::is('csv.control','product') ? ' active' : '' }}" href="{{ route('csv.control', 'product') }}">
            <span>CSV 出力項目設定</span>
            <span class="rito rito-chevron-right"></span>
        </a>
        @endrole
        @role('operator')
        <a class="menu-link{{ Route::is('csv.control','product') ? ' active' : '' }}" href="{{ route('csv.control', 'product') }}">
            <span>CSV 出力項目設定</span>
            <span class="rito rito-chevron-right"></span>
        </a>
        @endrole
        @role('vendor')
        <a class="menu-link{{ Route::is('csv.control','product') ? ' active' : '' }}" href="{{ route('csv.control', 'product') }}">
            <span>CSV 出力項目設定</span>
            <span class="rito rito-chevron-right"></span>
        </a>
        <a class="menu-link{{ Route::is('localvendor.edit','profile.edit') ? ' active' : '' }} mouse-pointer" href="{{ (Auth::user()->is_profile == 0) ? URL::to('/').'/users/'.Auth::user()->id.'/edit' : URL::to('/').'/localvendor/edit/'.Auth::user()->id.'/edit' }}" id="seller-profile-left-menu-{{Auth::user()->id}}">
            <span>プロフィール</span>
            <span class="rito rito-chevron-right"></span>
        </a>
        @endrole
        @can('inquery')
        <a class="menu-link{{ Route::is('inquirys.create') ? ' active' : '' }}" href="{{ route('inquirys.create') }}">
            <span>お問い合わせ</span>
            <span class="rito rito-chevron-right"></span>
        </a>
        @endcan
        @role('seller')
        <a class="menu-link{{ Route::is('seller.profile.create','profile.edit') ? ' active' : '' }} mouse-pointer" href="{{ (Auth::user()->is_profile == 0) ? URL::to('/').'/seller/profile/create/'.Auth::user()->id : URL::to('/').'/seller/profile/'.Auth::user()->id.'/edit' }}" id="seller-profile-left-menu-{{Auth::user()->id}}">
            <span>プロフィール</span>
            <span class="rito rito-chevron-right"></span>
        </a>
        @endrole
        <a class="menu-link{{ Route::is('settings', 'editName', 'editEmail', 'showChangePassword') ? ' active' : '' }}" href="{{ route('settings') }}">
            <span>ログインID／パスワード </span>
            <span class="rito rito-chevron-right"></span>
        </a>
        <a class="menu-link" href="{{ route('logout') }}" onclick="event.preventDefault();
        document.getElementById('sidebar-logout-form').submit();">
            <span>ログアウト</span>
            <span class="rito rito-chevron-right"></span>
            <form id="sidebar-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </a>
    </div>
</div>
@include('admin.profiles.profile-modal')