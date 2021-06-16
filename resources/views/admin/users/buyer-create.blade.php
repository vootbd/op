@extends('admin.layouts.admin')

@push('custom-style')
<link rel="stylesheet" href="{{ asset('css/admin/css/buyer-create.css') }}">
@endpush

@push('breadcrumb')
@role('operator')
<li class="breadcrumb-item"><a href="{{ route('seller.list') }}">管理 TOP</a></li>
@endrole()
@role('seller')
<li class="breadcrumb-item"><a href="{{ route('sellerProductList') }}">管理 TOP</a></li>
@endrole()
<li class="breadcrumb-item active" aria-current="page">バイヤーアカウントを登録</li>
@endpush

@section('content')
<div class="inner-content">
    <section class="box-content">
        <div class="d-flex align-items-center page-title">
            <h1>バイヤーアカウントを登録</h1>
        </div>
        {!! Form::open(array('route' => ['users.store'], 'method'=> 'POST', 'class'=> 'form-block', 'id' =>'form-user-create', 'enctype' => 'multipart/form-data')) !!}
            @csrf
            <input type="hidden" value="buyer" name="buyer">
             <div class="form-content">
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('name', 'アカウント名', ['class' => 'col-form-label']) !!} 
                        <span class="required-span">必須</span>
                    </div>
                    <div class="field-section">
                         {!! Form::text('name', null, ['required' => true,'class' => 'name']) !!}
                         @if($errors->has('name'))
                             <div class="error_msg">
                                 {{ $errors->first('name') }}
                             </div>
                         @endif
                     </div>
                </div>
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('display_name', '表示名', ['class' => 'col-form-label']) !!}
                    </div>
                    <div class="field-section">
                         {!! Form::text('display_name', null, ['class' => 'display-name']) !!}
                         @if($errors->has('display_name'))
                             <div class="error_msg">
                                 {{ $errors->first('display_name') }}
                             </div>
                         @endif
                     </div>
                </div>
                <div class="form-group d-flex">
                    <div class="label-section ">
                        {!! Form::label('explanation', 'アカウント説明', ['class' => 'col-form-label']) !!}
                    </div>
                    <div class="field-section">
                         {!! Form::textarea('explanation', null, ['class' => 'description']) !!}
                         @if($errors->has('explanation'))
                             <div class="error_msg">
                                 {{ $errors->first('explanation') }}
                             </div>
                         @endif
                     </div>
                </div>
                <hr>
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('email', 'メールアドレス', ['class' => 'col-form-label']) !!}
                        <span class="required-span">必須</span>
                    </div>
                    <div class="field-section">
                         {!! Form::email('email', null, ['required' => true,'class' => 'email']) !!}
                         @if($errors->has('email'))
                             <div class="error_msg">
                                 {{ $errors->first('email') }}
                             </div>
                         @endif
                     </div>
                     <span class="loader-email-check"></span>
                     <span  class="loader-email-box"> <i class="rito rito-check"></i></span>
                </div>
                <hr>
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('password', 'パスワード', ['class' => 'col-form-label']) !!}
                        <span class="required-span">必須</span>
                    </div>
                    <div class="field-section">
                         {!! Form::password('password', ['required' => true,'class' => 'password','onkeyup' => 'passwordValidation(this)']) !!}
                         <span class="invalid-feedback"></span>
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
                         {!! Form::password('password_confirmation', ['required' => true,'class' => 'password']) !!}
                         @if($errors->has('password_confirmation'))
                             <div class="error_msg">
                                 {{ $errors->first('password_confirmation') }}
                             </div>
                         @endif
                     </div>
                </div>
             </div>
            <div class="d-flex justify-content-center form-submission">
                <button type="button" onclick="softDeleteCheck()" class="btn btn-submit">登録する</button>
            </div>
        {!! Form::close() !!}
    </section>
    @include('admin.partials.softdelete_check')
</div>
@endsection
@push('custom-scripts')
<script src="{{ asset('js/softDeleteCheck.js') }}"></script>
@endpush