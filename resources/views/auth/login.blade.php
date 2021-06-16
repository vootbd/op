@extends('admin.layouts.auth')

@push('custom-style')
<link rel="stylesheet" href="{{ asset('css/admin/css/login.css') }}">
@endpush

@section('content')
<div class="d-flex align-items-center flex-column login-block">
    <div class="logo-block">
        <img src="{{ asset('image/logo.svg') }}" alt="logo">
    </div>
    {!! Form::open(array('route' => 'login', 'class'=> 'form-block', 'method'=>'POST')) !!}
    @csrf
        @if ($message = Session::get('active'))
            <div class="custom-alert warning">
                <span class="rito rito-info"></span>
                <p>{{ $message }}</p>
                <span class="rito rito-x" id="alertClose"></span>
            </div>
        @endif
        <div class="form-group">
            {!! Form::label('email', 'アカウントもしくはメールアドレス', ['class' => 'input-label']) !!}
            <input id="email" type="text" class="{{ $errors->has('name') || $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('name') }}" required autocomplete="email" autofocus>
            @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
            @error('name')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
        </div>
        <div class="form-group">
            {!! Form::label('password', 'パスワード', ['class' => 'input-label']) !!}
            <input id="password" type="password" class="@error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-submit">
                <span>ログイン</span>
            </button>
        </div>
        @if (Route::has('password.request'))
        <div class="forgot-password-block">
            <a class="forgot-password" href="{{ route('password.request') }}">
                <img src="{{ asset('image/info.svg') }}" alt="info icon">
                <span>パスワードをお忘れの方</span>
            </a>
        </div>
        @endif
    {!! Form::close() !!}
</div>



{{-- <div class="col-md-5">
    <div class="card box-shadow">
        <div class="card-header">
            <p>{{ __('Login') }}</p>
            <a href="{{ route('register') }}">{{ __('Have not account yet? please sign up') }}</a>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group row">
                    <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                    <div class="col-md-6">
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="form-group row">
                    <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                    <div class="col-md-6">
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-6 offset-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                            <label class="form-check-label" for="remember">
                                {{ __('Remember Me') }}
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-8 offset-md-4">
                        <button type="submit" class="btn btn-submit">
                            <span>{{ __('Login') }}</span>
                        </button>

                        @if (Route::has('password.request'))
                            <a class="btn btn-link" href="{{ route('password.request') }}">
                                {{ __('Forgot Your Password?') }}
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
</div> --}}
@endsection
