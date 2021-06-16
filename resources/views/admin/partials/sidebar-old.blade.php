<div class="sidebar" id="sidebar">
    <span id="navbarClose" class="rito rito-x"></span>
    @role('admin')
    {{-- Admin Site Management nav block --}}
    <div class="d-flex flex-column account-block">
        <h2 class="title">
            <img src="{{ asset('image/icon-buy.svg') }}" alt="buyer icon">
            <span class="text">サイト管理</span>
        </h2>
        <a class="menu-link{{ Route::is('roles.edit') ? ' active' : '' }}" href="{{ route('roles.edit', 1) }}">
            <span>アカウント権限</span>
            <span class="rito rito-chevron-right"></span>
        </a>
        {{-- <a class="menu-link" href="">
            <span>ログ</span>
            <span class="rito rito-chevron-right"></span>
        </a> --}}
        @can('activity-log')
        <a class="menu-link{{ Route::is('user.activities') ? ' active' : '' }}" href="{{ route('user.activities') }}">
            <span>ユーザーログ</span>
            <span class="rito rito-chevron-right"></span>
        </a>
        @endcan
        <a class="menu-link{{ Route::is('blockedUserList') ? ' active' : '' }}" href="{{ route('blockedUserList') }}">
            <span>アカウントのロック解除</span>
            <span class="rito rito-chevron-right"></span>
        </a>
    </div>
    {{-- Admin Account Management nav block --}}
    <div class="d-flex flex-column account-block">
        <h2 class="title">
            <img class="icon-support" src="{{ asset('image/icon-buy.svg') }}" alt="island icon">
            <span class="text">アカウント管理</span>
        </h2>
        <a class="menu-link{{ Route::is('users.index', 'users.edit') ? ' active' : '' }}" href="{{ route('users.index') }}">
            <span>アカウント一覧</span>
            <span class="rito rito-chevron-right"></span>
        </a>
        <a class="menu-link{{ Route::is('users.create') ? ' active' : '' }}" href="{{ route('users.create') }}">
            <span>運用アカウント登録</span>
            <span class="rito rito-chevron-right"></span>
        </a>
    </div>
    @endrole
    @role('operator')
    {{-- Operator Buyer Management nav block --}}
    <div class="d-flex flex-column account-block">
        <h2 class="title">
            <img src="{{ asset('image/icon-buy.svg') }}" alt="buyer icon">
            <span class="text">バイヤー管理</span>
        </h2>
        <a class="menu-link{{ Route::is('buyer.list') ? ' active' : '' }}" href="{{ route('buyer.list') }}">
            <span>アカウント一覧</span>
            <span class="rito rito-chevron-right"></span>
        </a>
        <a class="menu-link{{ Route::is('buyerCreate') ? ' active' : '' }}" href="{{ route('buyerCreate') }}">
            <span>アカウント登録</span>
            <span class="rito rito-chevron-right"></span>
        </a>
    </div>
    {{-- Operator Products Management nav block --}}
    <div class="d-flex flex-column account-block">
        <h2 class="title">
            <img class="icon-support" src="{{ asset('image/icon-product.svg') }}" alt="island icon">
            <span class="text">商品管理</span>
        </h2>
        <a class="menu-link{{ Route::is('products.index', 'products.show', 'products.read', 'products.edit') ? ' active' : '' }}" href="{{ route('products.index') }}">
            <span>商品一覧</span>
            <span class="rito rito-chevron-right"></span>
        </a>
        <a class="menu-link{{ Route::is('products.create') ? ' active' : '' }}" href="{{ route('products.create') }}">
            <span>商品登録</span>
            <span class="rito rito-chevron-right"></span>
        </a>
        <a class="menu-link{{ Route::is('categories.index') ? ' active' : '' }}" href="{{ route('categories.index') }}">
            <span>カテゴリー一覧</span>
            <span class="rito rito-chevron-right"></span>
        </a>
        <a class="menu-link{{ Route::is('categories.create', 'categories.edit') ? ' active' : '' }}" href="{{ route('categories.create') }}">
            <span>カテゴリー登録</span>
            <span class="rito rito-chevron-right"></span>
        </a>
    </div>
    {{-- Operator Seller Management nav block --}}
    <div class="d-flex flex-column account-block">
        <h2 class="title">
            <img src="{{ asset('image/icon-buy.svg') }}" alt="buyer icon">
            <span class="text">事業者管理</span>
        </h2>
        <a class="menu-link{{ Route::is('seller.list' ,'seller.profile.create','profile.edit') ? ' active' : '' }}" href="{{ route('seller.list') }}">
            <span>アカウント一覧</span>
            <span class="rito rito-chevron-right"></span>
        </a>
        <a class="menu-link{{ Route::is('sellerCreate') ? ' active' : '' }}" href="{{ route('sellerCreate') }}">
            <span>アカウント登録</span>
            <span class="rito rito-chevron-right"></span>
        </a>
    </div>
    {{-- Operator Island nav block --}}
    <div class="d-flex flex-column account-block">
        <h2 class="title">
            <img src="{{ asset('image/icon-island.svg') }}" alt="island icon">
            <span class="text">離島管理</span>
        </h2>
        <a class="menu-link{{ Route::is('islands.index', 'islands.edit') ? ' active' : '' }}" href="{{ route('islands.index') }}">
            <span>離島一覧</span>
            <span class="rito rito-chevron-right"></span>
        </a>
        <a class="menu-link{{ Route::is('islands.create') ? ' active' : '' }}" href="{{ route('islands.create') }}">
            <span>離島登録</span>
            <span class="rito rito-chevron-right"></span>
        </a>
    </div>
    @endrole
    @role('seller')
    {{-- Seller Products Management nav block --}}
    <div class="d-flex flex-column account-block">
        <h2 class="title">
            <img class="icon-support" src="{{ asset('image/icon-product.svg') }}" alt="island icon">
            <span class="text">商品管理</span>
        </h2>
        <a class="menu-link{{ Route::is('products.show', 'products.read', 'seller.product.edit', 'sellerProductList') ? ' active' : '' }}" href="{{ route('sellerProductList') }}">
            <span>商品一覧</span>
            <span class="rito rito-chevron-right"></span>
        </a>
        <a class="menu-link{{ Route::is('seller.product.create') ? ' active' : '' }}" href="{{ route('seller.product.create') }}">
            <span>商品登録</span>
            <span class="rito rito-chevron-right"></span>
        </a>
    </div>
    {{-- Seller Profile Management nav block --}}
    <div class="d-flex flex-column account-block">
        <h2 class="title">
            <img src="{{ asset('image/icon-buy.svg') }}" alt="buyer icon">
            <span class="text">事業者管理</span>
        </h2>
        <a class="menu-link{{ Route::is('seller.profile.create','profile.edit') ? ' active' : '' }}" href="{{ (Auth::user()->is_profile == 0) ? URL::to('/').'/seller/profile/create/'.Auth::user()->id : URL::to('/').'/seller/profile/'.Auth::user()->id.'/edit' }}">
            <span>プロフィール</span>
            <span class="rito rito-chevron-right"></span>
        </a>
    </div>
    @endrole
    @role('buyer')
    {{-- Buyer Search by category nav block --}}
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
    @endrole
    {{-- Profile Settings nav block --}}
    <div class="d-flex flex-column account-block">
        <h2 class="title">
            <img src="{{ asset('image/icon-tool.svg') }}" alt="support icon">
            <span class="text">設定</span>
        </h2>
        @can('inquery')
        <a class="menu-link{{ Route::is('inquirys.create') ? ' active' : '' }}" href="{{ route('inquirys.create') }}">
            <span>お問い合わせ</span>
            <span class="rito rito-chevron-right"></span>
        </a>
        @endcan
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