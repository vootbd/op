@extends('admin.layouts.admin')

@push('custom-style')
<link rel="stylesheet" href="{{ asset('css/admin/css/csv-control.css') }}">
@endpush

@push('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('seller.list') }}">管理 TOP</a></li>
<li class="breadcrumb-item active">CSV 出⼒項⽬設定</li>
@endpush

@section('content')
<div class="inner-content">
    <section class="box-content">
        <div class="d-flex align-items-center page-title">
            <h1>CSV 出⼒項⽬設定</h1>
        </div>
        {!! Form::open(array('route' => 'islands.store', 'method'=> 'POST', 'class'=> 'form-block', 'enctype' => 'multipart/form-data')) !!}
            @csrf
             <div class="form-content">
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('name', 'CSV 種別', ['class' => 'col-form-label']) !!}
                    </div>
                    <div class="field-section">
                        {!! Form::select('csv_type', ['product' => '商品管理CSV','category' => 'カテゴリ管理CSV','remote_island' => '離島管理CSV'], $type, ['id' => 'csv-type', 'class' => 'custom-select csv_type']) !!}
                        @if($errors->has('csv_type'))
                            <div class="error_msg">
                                {{ $errors->first('csv_type') }}
                            </div>
                        @endif
                    </div>
                </div>
                <div class="d-flex csv-main-content">


                    <div class="form-group csv-left-select">
                        <div class="label-section d-flex align-items-center">
                            {!! Form::label('name', '出⼒しない項⽬', ['class' => 'col-form-label']) !!}
                        </div>
                        <select class="csv-settings-option" name="available_columns[]" id="available_c" multiple="multiple" size="10">
                            @foreach ($data['not_in_output'] as $item)
                                <option data-id="{{ $item->id }}" data-order="{{$item->order }}" data-inoutput="{{$item->in_output }}" value="{{ $item->column_name }}">{{ $item->column_label }}</option>
                            @endforeach
                        </select>
                    </div>



                    <div class="form-group csv-left-button">
                        <div class="label-section d-flex align-items-center">
                            {!! Form::label('name', '項⽬操作', ['class' => 'col-form-label']) !!}
                        </div>
                        <div class="d-flex flex-column csv-buttons">
                            <button type="button" class="csv-action btn btn-settings-inactive" onclick="moveOptionsLeft()" ><i class="rito rito-arrow-left"></i>出力しない</button>
                            <button type="button" class="csv-action btn btn-settings-inactive" onclick="moveOptionsRight()"><i class="rito rito-arrow-right"></i>出⼒する</button>
                            <button type="button" class="csv-action btn btn-settings-inactive" onclick="moveAllLeft()"><i class="rito rito-arrow-left"></i>全て出⼒しない</button>
                            <button type="button" class="csv-action btn btn-settings-inactive" onclick="moveAllRight()"><i class="rito rito-arrow-right"></i>全て出力する</button>
                        </div>
                    </div>



                    <div class="form-group csv-right-select">
                        <div class="label-section d-flex align-items-center">
                            {!! Form::label('name', '出力する項目', ['class' => 'col-form-label']) !!}
                        </div>
                        <select class="csv-settings-option" name="c[]" id="selected_c" multiple="multiple" size="10">
                            @foreach ($data['in_output'] as $item)
                                <option data-id="{{ $item->id }}" data-order="{{$item->order }}" data-inoutput="{{$item->in_output }}" value="{{ $item->column_name }}">{{ $item->column_label }}</option>
                            @endforeach
                        </select>
                    </div>



                    <div class="form-group csv-right-button">
                        <div class="label-section d-flex align-items-center">
                            {!! Form::label('name', '並び順操作', ['class' => 'col-form-label']) !!}
                        </div>
                        <div class="d-flex flex-column csv-buttons">
                            <button type="button" class="csv-action btn btn-settings-inactive" onclick="moveOptionUp()"><i class="rito rito-arrow-up"></i>ひとつ上へ</button>
                            <button type="button" class="csv-action btn btn-settings-inactive" onclick="moveOptionDown()"><i class="rito rito-arrow-down"></i>ひとつ下へ</button>
                            <button type="button" class="csv-action btn btn-settings-inactive" onclick="moveOptionTop()"><i class="rito rito-arrow-up"></i>⼀番上へ</button>
                            <button type="button" class="csv-action btn btn-settings-inactive" onclick="moveOptionBottom()"><i class="rito rito-arrow-down"></i>⼀番下へ</button>
                        </div>
                    </div>
                </div>
             </div>
        {!! Form::close() !!}
    </section>
</div>
@endsection

@push('custom-scripts')

<script src="{{ asset('js/csv-setting.js') }}"></script>

@endpush