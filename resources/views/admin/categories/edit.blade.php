@extends('admin.layouts.admin')

@push('custom-style')
<link rel="stylesheet" href="{{ asset('css/admin/css/categorie-create.css') }}">
@endpush

@push('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('seller.list') }}">管理 TOP</a></li>
<li class="breadcrumb-item"><a href="{{ route('categories.index') }}">カテゴリー一覧</a></li>
<li class="breadcrumb-item active" aria-current="page">カテゴリーを 編集する</li>
@endpush

@section('content')
<div class="inner-content">
    <section class="box-content">
        <div class="d-flex align-items-center page-title">
            <h1>カテゴリーを 編集する</h1>
        </div>
        {!! Form::open(array('route' => ['categories.update', $data->id], 'method'=> 'PUT', 'class'=> 'form-block', 'enctype' => 'multipart/form-data')) !!}
            @csrf
             <div class="form-content">
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('category_id', 'カテゴリーID', ['class' => 'col-form-label']) !!}
                    </div>
                    <div class="d-flex align-items-center">
                        <p class="d-flex align-items-center bold">{{$data->id}}</p>
                    </div>
                </div>
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('dropdowns', 'カテゴリー階層', ['class' => 'col-form-label']) !!}
                        <span class="required-span">必須</span>
                    </div>
                    <div class="field-section">
                    <select class="custom-select" name="parent_id" id='dropdowns'>
                        @if(empty($data->parent_id))
                            <option value="">根底</option>
                        @else
                            <option value="">根底</option>
                            @foreach ($parent_category as $item)
                                <option value="{{ $item['id'] }}" {{ ($item["id"] == $data->parent_id) ? 'selected' : '' }}> {{ $item['name'] }} </option>
                            @endforeach
                        @endif
                    </select>
                        @if($errors->has('parent_id'))
                            <div class="error_msg">
                                {{ $errors->first('parent_id') }}
                            </div>
                        @endif
                    </div>
                </div>
                <div class="form-group d-flex mt-4">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('name', 'カテゴリーの名前', ['class' => 'col-form-label']) !!} 
                        <span class="required-span">必須</span>
                    </div>
                    <div class="field-section">
                        {!! Form::text('name', $data->name, ['class' => 'name','placeholder' => '離島名（必須 40 文字まで）','required' => true]) !!}
                        @if($errors->has('name'))
                            <div class="error_msg">
                                {{ $errors->first('name') }}
                            </div>
                        @endif
                    </div>
                </div>
             </div>
            <div class="d-flex justify-content-center form-submission">
                <button type="submit" class="btn btn-submit">変更する</button>
            </div>
        {!! Form::close() !!}
    </section>
</div>
@endsection