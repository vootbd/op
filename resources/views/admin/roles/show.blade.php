@extends('dashboard.layouts.app')


@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb d-flex justify-content-between">
        <div class="pull-left">
            <h2 class="title-text"> Show Role</h2>
        </div>
        <div class="pull-right right-group-btn">
            <a class="btn btn-back" href="{{ route('roles.index') }}">
                <img src="{{ asset('dashboard/image/icons/left-arrow-gray.svg') }}" alt="">
                <span>Back</span>
            </a>
            <a class="btn btn-back btn-auto" href="{{ route('roles.index') }}">
                {{-- <img src="{{ asset('dashboard/image/icons/left-arrow-gray.svg') }}" alt=""> --}}
                <span>Edit this Role</span>
            </a>
            <a class="btn btn-back btn-auto" href="{{ route('roles.index') }}">
                <span>Create a new Role</span>
                {{-- <img src="{{ asset('dashboard/image/icons/left-arrow-gray.svg') }}" alt=""> --}}
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Name:</strong>
            {{ $role->name }}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Permissions:</strong>
            @if(!empty($rolePermissions))
                @foreach($rolePermissions as $v)
                    <label class="label label-success">{{ $v->name }},</label>
                @endforeach
            @endif
        </div>
    </div>
</div>

 
@endsection