@extends('admin.layouts.admin')

@push('custom-style')
<link rel="stylesheet" href="{{ asset('css/admin/css/island-create.css') }}">
@endpush

@push('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('seller.list') }}">管理 TOP</a></li>
<li class="breadcrumb-item"><a href="{{ route('islands.index') }}">離島を登録</a></li>
<li class="breadcrumb-item active" aria-current="page">離島を登録</li>
@endpush

@section('content')
<div class="inner-content">
    <section class="box-content">
        <div class="d-flex align-items-center page-title">
            <h1>離島を登録</h1>
        </div>
        {!! Form::open(array('route' => 'islands.store', 'method'=> 'POST', 'class'=> 'form-block', 'enctype' => 'multipart/form-data')) !!}
            @csrf
             <div class="form-content">
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('name', '離島の名前', ['class' => 'col-form-label']) !!} 
                        <span class="required-span">必須</span>
                    </div>
                    <div class="field-section">
                        {!! Form::text('name', null, ['class' => 'name','placeholder' => '離島名（必須 40 文字まで）']) !!}
                        @if($errors->has('name'))
                            <div class="error_msg">
                                {{ $errors->first('name') }}
                            </div>
                        @endif
                    </div>
                </div>
                <div class="form-group d-flex mt-4">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('name', '離島コード
                        ', ['class' => 'col-form-label']) !!} 
                        <span class="required-span">必須</span>
                    </div>
                    <div class="field-section">
                        {!! Form::text('code', null, ['class' => 'name', 'placeholder' => '島コード（最大5文字必要）','required' => true]) !!}
                        @if($errors->has('code'))
                            <div class="error_msg">
                                {{ $errors->first('code') }}
                            </div>
                        @endif
                    </div>
                </div>
                <div class="form-group d-flex mt-4">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('prefecture', '都道府県', ['class' => 'col-form-label']) !!}
                        <span class="required-span">必須</span>
                    </div>
                    <div class="field-section">
                        <select id="prefecture_id" class="custom-select select2" name="prefecture_id">
                            <option selected="selected" value="">都道府県</option>  
                            @foreach ($prefecture as $item)
                                <option value="{{ $item->id }}">{{ $item->name }} </option>             
                            @endforeach
                        </select>
                        @if($errors->has('prefecture_id'))
                             <div class="error_msg">
                                 {{ $errors->first('prefecture_id') }}
                             </div>
                         @endif
                    </div>
                </div>
                <div class="form-group d-flex mt-4">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('jurisdiction', '離島の管轄⾃治体', ['class' => 'col-form-label']) !!} 
                    </div>
                    <div class="field-section">
                        {!! Form::text('jurisdiction', null, ['class' => 'name', 'placeholder' => '','required' => false]) !!}
                        @if($errors->has('jurisdiction'))
                            <div class="error_msg">
                                {{ $errors->first('jurisdiction') }}
                            </div>
                        @endif
                    </div>
                </div>
                <div class="form-group d-flex mt-4">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('autonomous', '管轄⾃治体コード', ['class' => 'col-form-label']) !!} 
                    </div>
                    <div class="field-section">
                        {!! Form::text('autonomous_code', null, ['class' => 'name', 'placeholder' => '','required' => false]) !!}
                        @if($errors->has('autonomous_code'))
                            <div class="error_msg">
                                {{ $errors->first('autonomous_code') }}
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