@extends('admin.layouts.admin')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb d-flex justify-content-between align-items-center">
        <div class="pull-left d-flex align-items-center">
            <h2 class="title-text">Create New Role</h2>
        </div>
        <div class="pull-right">
            <a class="btn btn-back" href="{{ route('roles.index') }}">
                <img src="{{ asset('dashboard/image/icons/left-arrow-gray.svg') }}" alt="">
                <span>Back</span>
            </a>
        </div>
    </div>
</div>

@if (count($errors) > 0)
    <div class="alert alert-danger">
        <strong>Whoops!</strong> There were some problems with your input.<br><br>
        <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
        </ul>
    </div>
@endif


{!! Form::open(array('route' => 'roles.store', 'class'=> 'box-shadow shadow', 'method'=>'POST')) !!}
<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Name:</strong>
            {!! Form::text('name', null, array('placeholder' => 'Name','class' => 'form-control')) !!}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Permission:</strong>
            <br/>
            @foreach($permission as $value)
                <label>{{ Form::checkbox('permission[]', $value->id, false, array('class' => 'name')) }}
                {{ $value->name }}</label>
            <br/>
            @endforeach
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12 text-center d-flex justify-content-center">
        <button type="submit" class="btn btn-submit">
            <span>Submit</span>
            <img src="{{ asset('dashboard/image/icons/check-mark.svg') }}" alt="">
        </button>
    </div>
</div>
{!! Form::close() !!}

 
@endsection