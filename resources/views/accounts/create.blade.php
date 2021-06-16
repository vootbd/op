@extends('admin.layouts.admin')

@push('custom-style')
<link rel="stylesheet" href="{{ asset('css/admin/css/account-create.css') }}">
@endpush

@section('content')
<div class="inner-content">
    <section class="box-content">
        <div class="d-flex align-items-center page-title">
            <h1>アカウントを登録</h1>
        </div>
        {!! Form::open(array('class'=> 'form-block', 'method'=>'POST', 'enctype' => 'multipart/form-data',)) !!}
            @csrf
             <div class="form-content">
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('account_type', 'アカウント種別', ['class' => 'col-form-label']) !!} 
                        <span class="required-span">必須</span>
                    </div>
                    <div class="field-section">
                    {!! Form::select('size', ['L' => 'Large', 'S' => 'Small'], null, ['placeholder' => '選択してください', 'class' => 'custom-select account-type']) !!}
                    </div>
                </div>
                <hr>
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('name', 'アカウント名', ['class' => 'col-form-label']) !!} 
                        <span class="required-span">必須</span>
                    </div>
                    <div class="field-section">
                         {!! Form::text('name', null, ['class' => 'name']) !!}
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
                        {!! Form::label('description', 'アカウント説明', ['class' => 'col-form-label']) !!} 
                    </div>
                    <div class="field-section">
                         {!! Form::textarea('description', null, ['class' => 'description']) !!}
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
                         {!! Form::email('email', null, ['class' => 'email']) !!}
                         @if($errors->has('email'))
                             <div class="error_msg">
                                 {{ $errors->first('email') }}
                             </div>
                         @endif
                     </div>
                </div>
                <div class="form-group d-flex">
                    <div class="label-section d-flex flex-column align-items-start mobile-res">
                        {!! Form::label('confrim_email', 'メールアドレスの確認', ['class' => 'col-form-label']) !!} 
                        <span class="required-span confrim-email-span">必須</span>
                    </div>
                    <div class="field-section">
                         {!! Form::email('confrim_email', null, ['class' => 'email']) !!}
                         @if($errors->has('confrim_email'))
                             <div class="error_msg">
                                 {{ $errors->first('confrim_email') }}
                             </div>
                         @endif
                     </div>
                </div>
                <hr>
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('password', 'メールアドレス', ['class' => 'col-form-label']) !!} 
                        <span class="required-span">必須</span>
                    </div>
                    <div class="field-section">
                         {!! Form::password('password', ['class' => 'password']) !!}
                         @if($errors->has('password'))
                             <div class="error_msg">
                                 {{ $errors->first('password') }}
                             </div>
                         @endif
                     </div>
                </div>
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('confrim_password', 'パスワードの確認', ['class' => 'col-form-label']) !!} 
                        <span class="required-span">必須</span>
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
             </div>
            <div class="d-flex justify-content-center form-submission">
                <button type="submit" class="btn btn-submit">登録する</button>
            </div>
        {!! Form::close() !!}
    </section>
</div>
@endsection