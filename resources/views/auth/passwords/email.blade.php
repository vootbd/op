@extends('admin.layouts.auth')

@push('custom-style')
<link rel="stylesheet" href="{{ asset('css/admin/css/forget.css') }}">
@endpush

@section('content')
<div class="d-flex align-items-center flex-column forget-password-block">
    <div class="logo-block">
        <img src="{{ asset('image/logo.svg') }}" alt="logo">
    </div>
    {!! Form::open(array('route' => 'password.email', 'class'=> 'form-block', 'method'=>'POST')) !!}
    @csrf
    @if (session('status'))
        <div class="custom-alert success">
            <span class="rito rito-check"></span>
            <p>{{ session('status') }}</p>
            <span class="rito rito-x" id="alertClose"></span>
        </div>
    @endif
    <div class="d-flex align-items-center justify-content-center hader-text">パスワードの再設定</div>
        <div class="form-group">
            {!! Form::label('email', 'メールアドレス', ['class' => 'input-label']) !!}
            <input id="email" type="email" class="@error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
            @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
            <span class="forget-text">
                * ご登録されたメールアドレスにパスワード再設定のご案内が送信されます。
            </span>
        </div>
        <div class="form-group mobile-res">
            <button type="submit" class="btn btn-submit">
                <span>送信する</span>
            </button>
        </div>
    {!! Form::close() !!}
    <div class="d-flex align-items-start justify-content-start forget-footer">
        <a  href="" class="d-flex comm">
        <img src="{{ asset('image/icon-support-gray.svg') }}" class="icon-support-contact">
            <span>
                サポートへお問い合わせ
            </span>
        </a>
        <a href="" class="d-flex email-not-send">
            <img src="{{ asset('image/icon-support-gray.svg') }}" class="icon-support-messeage">
            <span>メールが届かない場合</span>
        </a>
    </div>
</div>
@endsection
