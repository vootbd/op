@extends('admin.layouts.admin')

@push('custom-style')
<link rel="stylesheet" href="{{ asset('css/admin/css/inquiry.css') }}">
@endpush

@push('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('sellerProductList') }}">管理 TOP</a></li>
<li class="breadcrumb-item active" aria-current="page"> お問い合わせ </li>
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
            <h1>お問い合わせ</h1>
        </div>
        {!! Form::open(array('route' => 'inquirys.store', 'method'=> 'POST', 'class'=> 'form-block', 'enctype' => 'multipart/form-data')) !!}
            @csrf
            <div class="form-content">
                <div class="account-type-text">
                    <p>
                        以下のフォームからお問い合わせください。内容によっては回答を差し上げるまでにお時間をいただく場合がございます。
                    </p>
                    <p class="inquery-p-2">
                        ※メールのドメイン指定をされている場合は、事前にアドレス： ******, ドメイン： ****** の受信設定を行ってください。
                    </p>
                </div>
                <hr>
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('name', 'お名前', ['class' => 'col-form-label']) !!}
                        <span class="required-span">必須</span>
                    </div>
                    <div class="field-section">
                        {!! Form::text('name', null, ['class' => 'name', 'required' => true]) !!}
                        @if($errors->has('name'))
                            <div class="error_msg">
                                {{ $errors->first('name') }}
                            </div>
                        @endif
                    </div>
                </div>
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('email', 'メールアドレス', ['class' => 'col-form-label']) !!}
                        <span class="required-span">必須</span>
                    </div>
                    <div class="field-section">
                        {!! Form::email('email', null, ['class' => 'email','placeholder' => 'text@example.com', 'required' => true]) !!}
                        @if($errors->has('email'))
                            <div class="error_msg">
                                {{ $errors->first('email') }}
                            </div>
                        @endif
                    </div>
                </div>
                <div class="form-group d-flex">
                    <div class="label-section d-flex flex-column align-items-start">
                        {!! Form::label('confrim_email', 'メールアドレスの確認', ['class' => 'col-form-label']) !!}
                        <span class="required-span confrim-email-span">必須</span>
                    </div>
                    <div class="field-section">
                        {!! Form::email('confrim_email', null, ['class' => 'email','placeholder' => 'text@example.com', 'required' => true]) !!}
                        @if($errors->has('confrim_email'))
                            <div class="error_msg">
                                {{ $errors->first('confrim_email') }}
                            </div>
                        @endif
                    </div>
                </div>
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('inquiry_items', 'お問い合わせ項目', ['class' => 'col-form-label']) !!}
                        <span class="required-span">必須</span>
                    </div>
                    <div class="field-section">
                        <select name="inquiry_items" class="custom-select account-type">
                            <optgroup label="商品について">
                                <option value="サンプルお問い合わせ">サンプルお問い合わせ</option>
                                <option value="その他">その他</option>
                            </optgroup>
                            <optgroup label="サイトについて">
                                <option value="サイトの使い方について">サイトの使い方について</option>
                                <option value="ログインについて">ログインについて</option>
                                <option value="その他お問い合わせ">その他お問い合わせ</option>
                            </optgroup>
                        </select>
                    </div>
                </div>
                <div class="form-group d-flex">
                    <div class="label-section d-flex flex-column align-items-start">
                        {!! Form::label('inquiry_content', 'お問い合わせの内容', ['class' => 'col-form-label']) !!}
                        <span class="required-span inquiry-span">必須</span>
                    </div>
                    <div class="field-section">
                        {!! Form::textarea('inquiry_content', !empty($product->name) ? '商品名 : ' .$product->name .'&#013;&#010;&#013;'. 'ご連絡先 :'.'&#013;&#010;&#013;'.'お問い合わせ内容：' : '' , ['class' => 'content-of-inquiry']) !!}
                        @if($errors->has('inquiry_content'))
                            <div class="error_msg">
                                {{ $errors->first('inquiry_content') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-center form-submission">
                <button type="submit" class="btn btn-submit">送信する</button>
            </div>
        {!! Form::close() !!}
    </section>
</div>
@endsection