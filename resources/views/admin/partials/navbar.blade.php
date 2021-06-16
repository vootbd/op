<div class="top-navbar w-100">
  <div class="container-fluid">
    <div class="d-flex justify-content-between">
        <div class="d-flex align-items-center logo-block">
          @role('admin')
          <a href="{{ route('user.activities') }}">
          @endrole()
          @role('operator')
          <a href="{{ route('seller.list') }}">
          @endrole()
          @role('seller')
          <a href="{{ route('sellerProductList') }}">
          @endrole()
          @role('buyer')
          <a href="{{ route('buyer.top') }}">
          @endrole()
            <img src="{{ asset('image/logo.svg') }}" alt="" class="logo">
          </a>
        </div>
        <div class="d-flex align-items-center user-block">
          <p class="user" id="toggle-user-navbar">{{ Auth::user()->roles->first()->name_jp }}</p>
          <div class="user-navbar">
            <p class="text">最終ログイン </p>
            <p class="date">{{ lastLoginUser() }}</p>
            @if (Auth::user()->email)
            <a href="{{ route('showChangePassword') }}" class="user-links">パスワード変更 </a>
            @else
            <a href="{{ route('editEmail') }}" class="user-links">メールアドレスの新規登録 </a>
            @endif
            <a href="" class="user-links" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">ログアウト</a>
            </a>
            <form id="logout-form" class="d-none" action="{{ route('logout') }}" method="POST">
                @csrf
            </form>
          </div>
          <span class="rito rito-menu" id="menuToggle"></span>
        </div>
    </div>
  </div>
</div>
