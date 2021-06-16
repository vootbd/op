@extends('admin.layouts.admin')

@push('custom-style')
<link rel="stylesheet" href="{{ asset('css/admin/css/categorie-create.css') }}">
@endpush

@push('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('seller.list') }}">管理 TOP</a></li>
<li class="breadcrumb-item"><a href="{{ route('categories.index') }}">カテゴリー一覧</a></li>
<li class="breadcrumb-item active" aria-current="page">カテゴリーを登録</li>
@endpush

@section('content')
<div class="inner-content">
    <section class="box-content">
        <div class="d-flex align-items-center page-title">
            <h1>カテゴリーを登録</h1>
        </div>
        {!! Form::open(array('route' => 'categories.store', 'method'=> 'POST', 'class'=> 'form-block', 'enctype' => 'multipart/form-data')) !!}
            @csrf
             <div class="form-content">
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('name', 'カテゴリーの名前', ['class' => 'col-form-label']) !!} 
                        <span class="required-span">必須</span>
                    </div>
                    <div class="field-section">
                        {!! Form::text('name', null, ['class' => 'name','placeholder' => 'カテゴリーの名前（必須 40 文字まで）','required' => true]) !!}
                        @if($errors->has('name'))
                            <div class="error_msg">
                                {{ $errors->first('name') }}
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