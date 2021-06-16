@extends('admin.layouts.admin')

@push('custom-style')
<link rel="stylesheet" href="{{ asset('css/admin/css/login-password.css') }}">
@endpush

@push('breadcrumb')
@role('admin')
<li class="breadcrumb-item"><a href="{{ route('users.index') }}">管理 TOP</a></li>
@endrole()
@role('operator')
<li class="breadcrumb-item"><a href="{{ route('seller.list') }}">管理 TOP</a></li>
@endrole()
@role('seller')
<li class="breadcrumb-item"><a href="{{ route('sellerProductList') }}">管理 TOP</a></li>
@endrole()
@role('buyer')
<li class="breadcrumb-item"><a href="{{ route('buyer.top') }}">管理 TOP</a></li>
@endrole()
<li class="breadcrumb-item active" aria-current="page">アカウント一覧</li>
@endpush

@section('content')
<div class="inner-content">

    @if ($message = Session::get('success'))
        <div class="custom-alert success">
            <span class="rito rito-check"></span>
            <p>{{ $message }}</p>
            <span class="rito rito-x" id="alertClose"></span>
        </div>
    @endif

    <section class="box-content">
        <div class="d-flex align-items-center page-title">
            <h1>ログイン／パスワード</h1>
        </div>
        <div class="content-block">
            <div class="d-flex align-items-center justify-content-start data-block data-row">
                <div class="label-name">表示名</div>
            <div class="data-name">
                {{ $user->display_name }}
            </div>
                <a href="{{ route('editName') }}" class="btn btn-edit">変更</a>
            </div>
            <div class="d-flex align-items-center justify-content-start data-block data-row">
                <div class="label-name">メールアドレス</div>
                <div class="data-name">
                    {{ $user->email }}
                </div>
                <a href="{{ route('editEmail') }}" class="btn btn-edit ">変更</a>
            </div>
            <div class="d-flex align-items-center justify-content-start data-block data-row">
                <div class="label-name">パスワード</div>
                <div class="data-name">********</div>
                <a href="{{ route('showChangePassword') }}" class="btn btn-edit">変更</a>
            </div>
        </div>
    </section>
</div>
@endsection