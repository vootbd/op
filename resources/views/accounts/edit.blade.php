@extends('admin.layouts.admin')

@push('custom-style')
<link rel="stylesheet" href="{{ asset('css/admin/css/account-edit.css') }}">
@endpush

@section('content')
<div class="inner-content">
    {!! Form::open(array('class'=> 'form-block', 'method'=>'POST', 'enctype' => 'multipart/form-data',)) !!}
    @csrf
        <section class="box-content">
            <div class="d-flex align-items-center page-title">
                <h1>アカウントを登録</h1>
            </div>
            <div class="form-content">
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('', 'アカウント種別', ['class' => 'col-form-label']) !!}    
                    </div>
                    <div class="field-section">
                        <p class="name">運営</p>
                    </div>
                </div>
                <hr>
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('status', 'ステータス', ['class' => 'col-form-label']) !!}
                    </div>
                    <div class="field-section">
                        <p>有効 <span class="status">停止させる</span> </p>
                    </div>
                </div>
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('name', 'アカウント名', ['class' => 'col-form-label']) !!} 
                    </div>
                    <div class="field-section">
                        <p>oneproof</p>
                    </div>
                </div>
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('display_name', '表示名', ['class' => 'col-form-label']) !!}
                    </div>
                    <div class="field-section">
                        {!! Form::text('display_name', 'ワンプルーフ', ['class' => 'display-name']) !!}
                        @if($errors->has('display_name'))
                            <div class="error_msg">
                                {{ $errors->first('display_name') }}
                            </div>
                        @endif
                    </div>
                </div>
                <div class="form-group d-flex">
                    <div class="label-section ">
                        {!! Form::label('description', 'アカウント説明', ['class' => 'col-form-label']) !!}
                    </div>
                    <div class="field-section">
                        {!! Form::textarea('description', "sample text sample text sample text", ['class' => 'description']) !!}
                        @if($errors->has('description'))
                            <div class="error_msg">
                                {{ $errors->first('description') }}
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
                        {!! Form::email('email', "test@example.com", ['class' => 'email']) !!}
                        @if($errors->has('email'))
                            <div class="error_msg">
                                {{ $errors->first('email') }}
                            </div>
                        @endif
                    </div>
                </div>
                <hr>
            </div>
        </section>
        <!--  password change block   -->
        <section class="box-content mt-20">
            <div class="form-content">
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center mobile-res">
                        {!! Form::label('current_password', '現在のパスワード', ['class' => 'col-form-label']) !!}
                    </div>
                    <div class="field-section">
                        {!! Form::password('current_password', ['class' => 'current-password']) !!}
                        @if($errors->has('current_password'))
                            <div class="error_msg">
                                {{ $errors->first('current_password') }}
                            </div>
                        @endif
                    </div>
                </div>
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('new_password', '新しいパスワード', ['class' => 'col-form-label']) !!}
                    </div>
                    <div class="field-section">
                        {!! Form::password('new_password', ['class' => 'new-password']) !!}
                        @if($errors->has('new_password'))
                            <div class="error_msg">
                                {{ $errors->first('new_password') }}
                            </div>
                        @endif
                    </div>
                </div>
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('confrim_password', '新しいパスワードの確認', ['class' => 'col-form-label']) !!}
                    </div>
                    <div class="field-section">
                        {!! Form::password('confrim_password', ['class' => 'password']) !!}
                        @if($errors->has('confrim_password'))
                            <div class="error_msg">
                                {{ $errors->first('confrim_password') }}
                            </div>
                        @endif
                    </div>
                </div>
                <div class="d-flex justify-content-center form-submission">
                    <button type="submit" class="btn btn-submit">登録する</button>
                </div>
            </div>
        </section>
    {!! Form::close() !!}
</div>
@endsection