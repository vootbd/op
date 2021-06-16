@extends('admin.layouts.auth')

@push('custom-style')
<link rel="stylesheet" href="{{ asset('css/admin/css/reset-password.css') }}">
@endpush

@section('content')

<div class="d-flex align-items-center flex-column reset-password-block">
    <div class="logo-block">
        <img src="{{ asset('image/logo.svg') }}" alt="logo">
    </div>
    {!! Form::open(array('route' => 'password.update', 'class'=> 'form-block', 'method'=>'POST')) !!}
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    <div class="d-flex align-items-center justify-content-center hader-text">パスワードのリセット</div>
        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif
        <div class="form-group email">
            <input id="email" type="email" class="form-control email @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>
        </div>
        <div class="form-group email-status">
            @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group">
            {!! Form::label('password', '新しいパスワード', ['class' => 'input-label']) !!}
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" onkeyup = 'passwordValidation(this)'>
            <div class="invalid-feedback"></div>
            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group">
            {!! Form::label('password-confirm', '新しいパスワードを再度入力してください', ['class' => 'input-label']) !!}
            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
            <span class="forget-text">
                <p>6文字以上の新しいパスワードを作成してください。</p>
                <p>パスワードは、英数字と記号の組み合わせにすると安全です。</p>
            </span>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-submit">
                <span>送信する</span>
            </button>
        </div>
    {!! Form::close() !!}
    <a href="" class="d-flex align-items-start justify-content-start forget-footer">
        <img src="{{ asset('image/icon-support-gray.svg') }}" class="icon-support-messeage">
        <span>サポートへお問い合わせ</span>
    </a>
</div>
@endsection
@push('custom-scripts')
<script type="text/javascript">
    function passwordValidation(password) {
        var defaultSpan = $(password).siblings('.invalid-feedback')[1];
        if(defaultSpan) {
            defaultSpan.innerHTML = "";
        }
        var span = $(password).siblings('.invalid-feedback')[0];
        var result = '';
        if(password.value.length < 8) {
            result+= '<strong>パスワードは8文字以上である必要があります。</strong><br>'
        }
        if(!password.value.match('[a-z]'))  {
            result+= '<strong>小文字を含める必要があります。</strong><br>'
        }
        if(!password.value.match('[A-Z]'))  {
            result+= '<strong>大文字を含める必要があります。</strong><br>'
        }
        if(!password.value.match('[0-9]'))  {
            result+='<strong>数字を含める必要があります。</strong><br>'
        }
        if(result.length > 1) {
            $(span).css('display','block');
            span.innerHTML = result;
        }
        else {
            span.innerHTML = "";
        }
        if(password.value.length == 0) {
            span.innerHTML = "";
        }
    }
</script>
@endpush