@extends('dashboard.layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb d-flex justify-content-between">
        <div class="pull-left">
            <h2 class="title-text"> Show User</h2>
        </div>
        <div class="pull-right right-group-btn">
            <a class="btn btn-back" href="{{ route('users.index') }}">
                <img src="{{ asset('dashboard/image/icons/left-arrow-gray.svg') }}" alt="">
                <span>Back</span>
            </a>
            <a class="btn btn-back btn-auto" href="{{ route('users.index') }}">
                {{-- <img src="{{ asset('dashboard/image/icons/left-arrow-gray.svg') }}" alt=""> --}}
                <span>Edit this user</span>
            </a>
            <a class="btn btn-back btn-auto" href="{{ route('users.index') }}">
                <span>Create a new user</span>
                {{-- <img src="{{ asset('dashboard/image/icons/left-arrow-gray.svg') }}" alt=""> --}}
            </a>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Name:</strong>
            {{ $user->name }}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Email:</strong>
            {{ $user->email }}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Roles:</strong>
            @if(!empty($user->getRoleNames()))
                @foreach($user->getRoleNames() as $v)
                    <label class="badge badge-success">{{ $v }}</label>
                @endforeach
            @endif
        </div>
    </div>
</div>
@endsection