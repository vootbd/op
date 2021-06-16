@extends('admin.layouts.admin')

@push('custom-style')
<link rel="stylesheet" href="{{ asset('css/admin/css/login-password.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin/css/change-password.css') }}">
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
                <a href="{{ route('showChangePassword') }}" class="btn btn-edit">変更</a>
            </div>
        </div>
    </section>
    <section class="box-content mt-20 mobile">
        <div class="d-flex align-items-center page-title">
            <h1>パスワードの変更</h1>
        </div>
        {!! Form::open(array('route' => 'changePassword','class'=> 'form-block', 'method'=>'POST', 'enctype' => 'multipart/form-data',)) !!}
            @csrf
            <div class="form-content">
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('old_password', '現在のパスワード', ['class' => 'col-form-label']) !!}
                        <span class="required-span">必須</span>
                    </div>
                    <div class="field-section d-flex flex-column">
                         {!! Form::password('old_password', ['class' => 'current_password', 'required' => true]) !!}
                         @if($errors->has('old_password'))
                             <span class="error_msg">
                                {{ $errors->first('old_password') }}
                             </span>
                         @endif
                         <span>パスワードをお忘れですか？</span>
                     </div>
                </div>
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('password', '新しいパスワード', ['class' => 'col-form-label']) !!}
                        <span class="required-span">必須</span>
                    </div>
                    <div class="field-section">
                         {!! Form::password('password', ['class' => 'password', 'required' => true,'onkeyup' => 'passwordValidation(this)']) !!}
                         <div class="invalid-feedback"></div>
                         @if($errors->has('password'))
                            <div class="error_msg">
                                {{ $errors->first('password') }}
                            </div>
                         @endif
                     </div>
                </div>
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('password_confirmation', 'パスワードの確認', ['class' => 'col-form-label']) !!}
                        <span class="required-span">必須</span>
                    </div>
                    <div class="field-section">
                         {!! Form::password('password_confirmation', ['class' => 'password', 'required' => true]) !!}
                         @if($errors->has('password_confirmation'))
                             <div class="error_msg">
                                 {{ $errors->first('password_confirmation') }}
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