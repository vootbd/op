@extends('admin.layouts.admin')

@push('custom-style')
<link rel="stylesheet" href="{{ asset('css/admin/css/login-password.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin/css/edit-name.css') }}">
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
<li class="breadcrumb-item"><a href="{{ route('settings') }}">アカウント一覧</a></li>
<li class="breadcrumb-item active" aria-current="page">ログイン／パスワード</li>
@endpush

@section('content')
<div class="inner-content">
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
                <a href="{{ route('changePassword') }}" class="btn btn-edit">変更</a>
            </div>
        </div>
    </section>
    <section class="box-content mt-20">
        <div class="d-flex align-items-center page-title">
            <h1>表示名の変更</h1>
        </div>
        {!! Form::open(array('route' => 'updateName', 'class'=> 'form-block', 'method'=>'POST', 'enctype' => 'multipart/form-data',)) !!}
            @csrf
            <div class="form-content">
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('display_name', '表示名', ['class' => 'col-form-label']) !!}
                        <span class="required-span">必須</span>
                    </div>
                    <div class="field-section">
                        {!! Form::text('display_name', $user->display_name, ['class' => 'name']) !!}
                        @if($errors->has('display_name'))
                            <div class="error_msg">
                                {{ $errors->first('display_name') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="d-flex form-submission">
                <button type="reset" class="btn btn-clear">キャンセル</button>
                <button type="submit" class="btn btn-submit">変更する</button>
            </div>
        {!! Form::close() !!}
    </section>
</div>
@endsection