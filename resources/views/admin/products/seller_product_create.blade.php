@extends('admin.layouts.admin')

@push('custom-style')
<link rel="stylesheet" href="{{ asset('css/admin/css/seller-product-create.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/bs-v4-datepicker/font-awesome.min.css') }}"/>
<link rel="stylesheet" href="{{ asset('vendor/bs-v4-datepicker/gijgo.min.css') }}"/>
@endpush

@push('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('sellerProductList') }}">管理 TOP</a></li>
<li class="breadcrumb-item active" aria-current="page">商品登録 </li>
@endpush

@section('content')
<div class="inner-content">
    {!! Form::open(array('route' => ['seller.product.store'], 'class'=> 'form-block', 'method'=>'POST', 'enctype' => 'multipart/form-data')) !!}
    @csrf
    {{-- Product Informations section start --}}
    <section class="box-content">
        <div class="d-flex align-items-center page-title">
            <h1>商品登録</h1>
        </div>
        <div class="form-content">
            <div class="form-group d-flex">
                <div class="label-section d-flex align-items-center">
                    {!! Form::label('island', '離島 ', ['class' => 'col-form-label']) !!}
                    <span class="required-span">必須</span>
                </div>
                <div class="field-section">
                    {!! Form::select('island_id', $island, null, ['placeholder' => '選択してください', 'class' => 'custom-select island select2', 'id' => 'islandValue', 'onchange' => 'islandSelect()']) !!}
                    @if($errors->has('island_id'))
                        <div class="error_msg">
                            {{ $errors->first('island_id') }}
                        </div>
                    @endif
                </div>
            </div>
            <hr>
            <div class="form-group d-flex">
                <div class="label-section d-flex align-items-center">
                    {!! Form::label('release', '公開・非公開', ['class' => 'col-form-label']) !!}
                    <span class="required-span">必須</span>
                </div>
                <div class="d-flex">
                    <div class="custom-control custom-checkbox">
                        {!! Form::radio('status', 1, true, ['class' => 'custom-control-input', 'id' => 'gridCheck1']) !!}
                        {!! Form::label('gridCheck1', '公開', ['class' => 'custom-control-label']) !!}
                    </div>
                    <div class="custom-control custom-checkbox">
                        {!! Form::radio('status', 0, false, ['class' => 'custom-control-input', 'id' => 'gridCheck2']) !!}
                        {!! Form::label('gridCheck2', '非公開', ['class' => 'custom-control-label']) !!}
                    </div>
                </div>
                @if($errors->has('status'))
                    <div class="error_msg">
                        {{ $errors->first('status') }}
                    </div>
                @endif
            </div>
            <hr>
            <div class="form-group d-flex">
                <div class="label-section d-flex align-items-center">
                    {!! Form::label('product_name', '商品名', ['class' => 'col-form-label']) !!}
                    <span class="required-span">必須</span>
                </div>
                <div class="field-section">
                    {!! Form::text('name', null, ['placeholder' => '商品名（必須40文字まで）','class' => 'product-name', 'max' => 40]) !!}
                    @if($errors->has('name'))
                        <div class="error_msg">
                            {{ $errors->first('name') }}
                        </div>
                    @endif
                </div>
            </div>
            <div class="form-group d-flex">
                <div class="label-section d-flex align-items-center">
                    {!! Form::label('description', '製品説明', ['class' => 'col-form-label']) !!}
                    <span class="required-span">必須</span>
                </div>
                <div class="field-section">
                    {!! Form::textarea('product_explanation', null, ['placeholder' => '製品説明','class' => 'description', 'max' => 2000]) !!}
                    @if($errors->has('product_explanation'))
                        <div class="error_msg">
                            {{ $errors->first('product_explanation') }}
                        </div>
                    @endif
                </div>
            </div>
            <hr>
            <div class="form-group d-flex">
                <div class="label-section d-flex align-items-center height-space">
                    {!! Form::label('category_id', '製品カテゴリ', ['class' => 'col-form-label']) !!}
                </div>
                <div class="field-section">
                    <div class="product-category d-flex fix-width">カテゴリー<span class="required-span">必須</span></div>
                    {!! Form::select('category_id', $categories, null, ['placeholder' => '選択してください', 'class' => 'custom-select product-details select2']) !!}
                    @if($errors->has('category_id'))
                        <div class="error_msg">
                            {{ $errors->first('category_id') }}
                        </div>
                    @endif
                </div>
            </div>
      
        </div>
    </section>
    {{-- Product Informations section end --}}

    {{-- Imagess Upload section start --}}
    <section class="box-content mt-20">
        <div class="data-container" >
            <div class="data-block cover-image drop-container" data-id="0">
                <div class="field-text">メイン画像 <span class="required-span d-flex">必須</span></div>
                <div class="upload-btn-block">
                    <div class="select-btn" id="drop-container" data-id="coverImage">
                        {!! Form::label('coverImage', 'ファイルを選択', ['class'=>'']) !!}
                        {!! Form::hidden('cover_image',"", ['class' => 'product-image']) !!}
                        {!! Form::file('coverimage', ['id' => 'coverImage','class' => 'drop-area-text']) !!}
                    </div>
                    <div class="delete-btn" id="coverImageDelete">画像を削除する</div>
                </div>
                <div class="image-name" id="coverImageName">選択されていません</div>
                <div class="product-image">
                    <img id="coverImagePreview" src="" alt="">
                    <span class="formate-error coverImageerror">jpg、jpeg、png タイプの画像ファイルを選択してください。</span>
                </div>
                @if($errors->has('cover_image'))
                    <div class="error_msg">
                        {{ $errors->first('cover_image') }}
                    </div>
                @endif
            </div>
            <hr>
            <div class="data-block pb-0 drop-container" data-id="1">
                <div class="field-text mt-30">追加画像</div>
                <div class="upload-btn-block">
                    <p class="index">画像1</p>
                    <div class="select-btn" id="drop-container-1" data-id="thumbnail_image1">
                        {!! Form::label('thumbnail_image1', 'ファイルを選択') !!}
                        {!! Form::hidden('thumbnail_image_1',"", ['class' => 'product-image']) !!}
                        {!! Form::file('thumbnail_image1', ['id' => 'thumbnail_image1']) !!}
                    </div>
                    <div class="delete-btn" id="thumbnail_image1Delete">画像を削除する</div>
                </div>
                <div class="image-name space-top" id="thumbnail_image1Name">選択されていません</div>
                <div class="product-image">
                    <img id="thumbnail_image1Preview" src="" alt="">
                    <span class="formate-error thumbnail_image1error">jpg、jpeg、png タイプの画像ファイルを選択してください。</span>
                </div>
            </div>
            <div class="data-block pb-0 space-left drop-container" data-id="2">
                <div class="upload-btn-block">
                    <p class="index">画像2</p>
                    <div class="select-btn" id="drop-container-2" data-id="thumbnail_image2">
                        {!! Form::label('thumbnail_image2', 'ファイルを選択') !!}
                        {!! Form::hidden('thumbnail_image_2',"", ['class' => 'product-image']) !!}
                        {!! Form::file('thumbnail_image2', [ 'id' => 'thumbnail_image2']) !!}
                    </div>
                    <div class="delete-btn" id="thumbnail_image2Delete">画像を削除する</div>
                </div>
                <div class="image-name space-top" id="thumbnail_image2Name">選択されていません</div>
                <div class="product-image">
                    <img id="thumbnail_image2Preview" src="{{ asset('image/images-icon.svg') }}" alt="">
                    <span class="formate-error thumbnail_image2error">jpg、jpeg、png タイプの画像ファイルを選択してください。</span>
                </div>
            </div>
            <div class="data-block pb-0 space-left drop-container" data-id="3">
                <div class="upload-btn-block">
                    <p class="index">画像3</p>
                    <div class="select-btn" id="drop-container-3" data-id="thumbnail_image3">
                        {!! Form::label('thumbnail_image3', 'ファイルを選択') !!}
                        {!! Form::hidden('thumbnail_image_3',"", ['class' => 'product-image']) !!}
                        {!! Form::file('thumbnail_image3', ['id' => 'thumbnail_image3']) !!}
                    </div>
                    <div class="delete-btn" id="thumbnail_image3Delete" data-noimage="hide">画像を削除する</div>
                </div>
                <div class="image-name space-top" id="thumbnail_image3Name">選択されていません</div>
                <div class="product-image">
                    <img id="thumbnail_image3Preview" src="{{ asset('image/images-icon.svg') }}" alt="">
                    <span class="formate-error thumbnail_image3error">jpg、jpeg、png タイプの画像ファイルを選択してください。</span>
                </div>
            </div>
            <div class="data-block pb-0 space-left drop-container" data-id="4">
                <div class="upload-btn-block">
                    <p class="index">画像4</p>
                    <div class="select-btn" id="drop-container-4" data-id="thumbnail_image4">
                        {!! Form::label('thumbnail_image4', 'ファイルを選択') !!}
                        {!! Form::hidden('thumbnail_image_4',"", ['class' => 'product-image']) !!}
                        {!! Form::file('thumbnail_image4', ['id' => 'thumbnail_image4']) !!}
                    </div>
                    <div class="delete-btn" id="thumbnail_image4Delete">画像を削除する</div>
                </div>
                <div class="image-name space-top" id="thumbnail_image4Name">選択されていません</div>
                <div class="product-image">
                    <img id="thumbnail_image4Preview" src="{{ asset('image/images-icon.svg') }}" alt="">
                    <span class="formate-error thumbnail_image4error">jpg、jpeg、png タイプの画像ファイルを選択してください。</span>
                </div>
            </div>
            <div class="data-block pb-45 space-left drop-container" data-id="5">
                <div class="upload-btn-block">
                    <p class="index">画像5</p>
                    <div class="select-btn" id="drop-container5" data-id="thumbnail_image5">
                        {!! Form::label('thumbnail_image5', 'ファイルを選択') !!}
                        {!! Form::hidden('thumbnail_image_5',"", ['class' => 'product-image']) !!}
                        {!! Form::file('thumbnail_image5', ['id' => 'thumbnail_image5']) !!}
                    </div>
                    <div class="delete-btn" id="thumbnail_image5Delete">画像を削除する</div>
                </div>
                <div class="image-name space-top" id="thumbnail_image5Name">選択されていません</div>
                <div class="product-image">
                    <img id="thumbnail_image5Preview" src="{{ asset('image/images-icon.svg') }}" alt="">
                    <span class="formate-error thumbnail_image5error">jpg、jpeg、png タイプの画像ファイルを選択してください。</span>
                </div>
            </div>
        </div>
    </section>
    {{-- Imagess Upload section end --}}

    {{-- URL section start --}}
    <section class="box-content mt-20">
        <div class="form-content">
            <div class="form-group d-flex mb-0">
                <div class="label-section d-flex align-items-center height-space">
                    {!! Form::label('url', '追加情報', ['class' => 'col-form-label']) !!}
                </div>
                <div class="field-section">
                    <div class="product-category d-flex fix-width">動画URL</div>
                    {!! Form::text('url', null, ['class' => 'product-name', 'id' => 'youtubeUrl', 'onchange' => 'validateYouTubeUrl()']) !!}
                    <div class="error_msg video-url">有効なURLではありません</div>
                </div>
            </div>
        </div>
    </section>
    {{-- URL section end --}}

    {{-- Detail info with checkbox section start --}}
    <section class="box-content mt-20">
        <div class="d-flex align-items-center justify-content-between page-title">
            <h1>詳細情報</h1>
            <span class="rito rito-chevron-up detail-with-checkbox-btn rotate"></span>
        </div>
        <div class="form-content detail-with-checkbox">
            <div class="form-group d-flex">
                <div class="label-section d-flex align-items-center height-space">
                    {!! Form::label('product_details', '売り先', ['class' => 'col-form-label']) !!}
                </div>
                <div class="chekbox-block">
                    @foreach($salerData as $data)
                    <div class="custom-control custom-checkbox">
                        {!! Form::checkbox('salesDestination[]', $data->id, false, ['class' => 'custom-control-input', 'id' => 'saler-'.$data->id]) !!}
                        {!! Form::label('saler-'.$data->id, $data->name, ['class' => 'custom-control-label']) !!}
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="form-group d-flex">
                <div class="label-section d-flex align-items-center">
                    {!! Form::label('shipment_method', '発送方法', ['class' => 'col-form-label']) !!}
                </div>
                <div class="field-section">
                    {!! Form::text('shipment_method', null, ['class' => 'product-name']) !!}
                    @if($errors->has('shipment_method'))
                    <div class="error_msg">
                        {{ $errors->first('shipment_method') }}
                    </div>
                    @endif
                </div>
            </div>
            <div class="form-group d-flex">
                <div class="label-section d-flex align-items-center">
                    {!! Form::label('preservation_method', '保存方法', ['class' => 'col-form-label']) !!}
                </div>
                <div class="chekbox-block">
                    @foreach(preservationMethod() as $key => $method)
                    <div class="custom-control custom-checkbox">
                        {!! Form::checkbox('preservation_method', $key, false, ['class' => 'custom-control-input preservation-method', 'id' => 'preservation-'.$key]) !!}
                        {!! Form::label('preservation-'.$key, $method, ['class' => 'custom-control-label']) !!}
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="form-group d-flex">
                <div class="label-section d-flex align-items-center">
                    {!! Form::label('package_type', '包装形態', ['class' => 'col-form-label']) !!}
                </div>
                <div class="field-section">
                    {!! Form::text('package_type', null, ['class' => 'product-name']) !!}
                    @if($errors->has('package_type'))
                    <div class="error_msg">
                        {{ $errors->first('package_type') }}
                    </div>
                    @endif
                </div>
            </div>
            <div class="form-group d-flex">
                <div class="label-section d-flex align-items-center">
                    {!! Form::label('quality_retention_temperature', '品質保持温度', ['class' => 'col-form-label']) !!}
                </div>
                <div class="field-section">
                    {!! Form::text('quality_retention_temperature', null, ['class' => 'product-name']) !!}
                    @if($errors->has('quality_retention_temperature'))
                    <div class="error_msg">
                        {{ $errors->first('quality_retention_temperature') }}
                    </div>
                    @endif
                </div>
            </div>
            <div class="form-group d-flex">
                <div class="label-section d-flex align-items-center">
                    {!! Form::label('expiration_taste_quality', '賞味期限・消費期限', ['class' => 'col-form-label']) !!}
                </div>
                <div class="field-section">
                    {!! Form::textarea('expiration_taste_quality', null, ['class' => 'description']) !!}
                    @if($errors->has('expiration_taste_quality'))
                    <div class="error_msg">
                        {{ $errors->first('expiration_taste_quality') }}
                    </div>
                    @endif
                </div>
            </div>
            <div class="form-group d-flex">
                <div class="label-section d-flex align-items-center">
                    {!! Form::label('use_scene', '利用シーン（レシピ・調理法）', ['class' => 'col-form-label use-scene']) !!}
                </div>
                <div class="field-section">
                    {!! Form::textarea('use_scene', null, ['class' => 'description']) !!}
                    @if($errors->has('use_scene'))
                    <div class="error_msg">
                        {{ $errors->first('use_scene') }}
                    </div>
                    @endif
                </div>
            </div>
            <div class="form-group d-flex">
                <div class="label-section d-flex align-items-center height-space">
                    {!! Form::label('product_details', 'アレルギー表⽰（特定原料）', ['class' => 'col-form-label']) !!}
                </div>
                <div class="chekbox-block">
                    <span class="check-name">表示義務有り（※使⽤している原料にチェック）</span>
                    @foreach($allergy as $data)
                    <div class="custom-control custom-checkbox">
                        {!! Form::checkbox('allergyRecommended[]', $data->id, false, ['class' => 'custom-control-input', 'id' => 'allergy-'.$data->id]) !!}
                        {!! Form::label('allergy-'.$data->id, $data->name, ['class' => 'custom-control-label']) !!}
                    </div>
                    @endforeach
                    <span class="check-name pt-3">表示推奨（※使⽤している原料にチェック）</span>
                    @foreach($allergyRecommended as $data)
                    <div class="custom-control custom-checkbox">
                        {!! Form::checkbox('allergyRecommended[]', $data->id, false, ['class' => 'custom-control-input', 'id' => 'saler-'.$data->id]) !!}
                        {!! Form::label('saler-'.$data->id, $data->name, ['class' => 'custom-control-label']) !!}
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    {{-- Detail info with checkbox section end --}}

    {{-- Detail info section start--}}
    <section class="box-content mt-20">
        <div class="d-flex align-items-center justify-content-between page-title">
            <h1>詳細追加情報</h1>
            <span class="rito rito-chevron-up detail-info-btn rotate"></span>
        </div>
        <div class="form-content detail-info">
            <div class="form-group d-flex">
                <div class="label-section d-flex align-items-center">
                    {!! Form::label('di_item1', '想定の顧客情報（年齢層・性別・所得層など）', ['class' => 'col-form-label']) !!}
                </div>
                <div class="field-section">
                    <span class="from-date">
                        {{-- {!! Form::text('year', null, ['class' => 'year', 'autocomplete'=> 'off']) !!}
                        <span>年</span>
                        {!! Form::text('month', null, ['class' => 'month', 'autocomplete'=> 'off']) !!}
                        <span>月</span>
                        {!! Form::text('day', null, ['class' => 'day', 'autocomplete'=> 'off']) !!}
                        <span>日</span> --}}
                        {!! Form::text('di_item[]', null, ['class' => 'product-name', 'id' => '', 'autocomplete'=> 'off']) !!}
                        @if($errors->has('di_item.0'))
                        <div class="error_msg">
                            {{ $errors->first('di_item.0') }}
                        </div>
                        @endif
                    </span>
                </div>
            </div>
            <div class="form-group d-flex">
                <div class="label-section d-flex align-items-center">
                    {!! Form::label('di_item2', '１ケースあたりの⼊数', ['class' => 'col-form-label']) !!}
                </div>
                <div class="field-section">
                    {!! Form::text('di_item[]', null, ['class' => 'product-name']) !!}
                    @if($errors->has('di_item.1'))
                    <div class="error_msg">
                        {{ $errors->first('di_item.1') }}
                    </div>
                    @endif
                </div>
            </div>
            <div class="form-group d-flex">
                <div class="label-section d-flex align-items-center">
                    {!! Form::label('di_item3', '最⼤・最⼩ケース納品単位（○ケース/⽇などの単位記載）', ['class' => 'col-form-label']) !!}
                </div>
                <div class="field-section">
                    <span class="maximum-span">最⼤</span>
                    {!! Form::text('maximum', null, ['class' => 'product-name max-min maximum']) !!}
                    <span class="maximum-span">最⼩</span>
                    {!! Form::text('minimum', null, ['class' => 'product-name max-min minimum']) !!}

                    {!! Form::hidden('di_item[]', null, ['class' => 'product-name', 'id' => 'maximum-minimum']) !!}
                    @if($errors->has('di_item.2'))
                    <div class="error_msg">
                        {{ $errors->first('di_item.2') }}
                    </div>
                    @endif
                </div>
            </div>
            <div class="form-group d-flex">
                <div class="label-section d-flex align-items-center">
                    {!! Form::label('di_item4', '内容量（単位記載）', ['class' => 'col-form-label']) !!}
                </div>
                <div class="field-section">
                    {!! Form::text('di_item[]', null, ['class' => 'product-name']) !!}
                    @if($errors->has('di_item.3'))
                    <div class="error_msg">
                        {{ $errors->first('di_item.3') }}
                    </div>
                    @endif
                </div>
            </div>
            <div class="form-group d-flex">
                <div class="label-section d-flex align-items-center">
                    {!! Form::label('di_item5', 'ケースサイズと重量', ['class' => 'col-form-label']) !!}
                </div>
                <div class="field-section">
                    <span class="common-hight-side-span">縦</span>
                    {!! Form::text('vertical', null, ['class' => 'product-name common-hight vertical']) !!}
                    <span class="common-hight-side-span">横</span>
                    {!! Form::text('side', null, ['class' => 'product-name common-hight side']) !!}
                    <span class="common-hight-side-span">高さ</span>
                    {!! Form::text('height', null, ['class' => 'product-name common-hight height']) !!}
                    <span class="common-hight-side-span">重量</span>
                    {!! Form::text('weight', null, ['class' => 'product-name common-hight weight']) !!}


                    {!! Form::hidden('di_item[]', null, ['class' => 'product-name','id' => 'size-section']) !!}
                    @if($errors->has('di_item.4'))
                    <div class="error_msg">
                        {{ $errors->first('di_item.4') }}
                    </div>
                    @endif
                </div>
            </div>
            <div class="form-group d-flex">
                <div class="label-section d-flex align-items-center">
                    {!! Form::label('di_item6', '発注のリードタイム', ['class' => 'col-form-label']) !!}
                </div>
                <div class="field-section">
                    {!! Form::text('di_item[]', null, ['class' => 'product-name']) !!}
                    @if($errors->has('di_item.5'))
                    <div class="error_msg">
                        {{ $errors->first('di_item.5') }}
                    </div>
                    @endif
                </div>
            </div>
            <div class="form-group d-flex">
                <div class="label-section d-flex align-items-center">
                    {!! Form::label('di_item7', 'JANコード（13桁、もしくは8桁）', ['class' => 'col-form-label']) !!}
                </div>
                <div class="field-section">
                    {!! Form::text('di_item[]', null, ['class' => 'product-name']) !!}
                    @if($errors->has('di_item.6'))
                    <div class="error_msg">
                        {{ $errors->first('di_item.6') }}
                    </div>
                    @endif
                </div>
            </div>
            <div class="form-group d-flex">
                <div class="label-section d-flex align-items-center">
                    {!! Form::label('di_item8', '提供可能時期', ['class' => 'col-form-label']) !!}
                </div>
                <div class="field-section">
                    <span class="from-date">
                        {!! Form::text('di_item[]', null, ['class' => 'product-name','id' => 'startDate']) !!}
                        @if($errors->has('di_item.7'))
                        <div class="error_msg">
                            {{ $errors->first('di_item.7') }}
                        </div>
                    </span>
                    @endif
                </div>
            </div>
            <div class="form-group d-flex">
                <div class="label-section d-flex align-items-center">
                    {!! Form::label('di_item9', '商品のストーリー・作り手の想い', ['class' => 'col-form-label']) !!}
                </div>
                <div class="field-section">
                    {!! Form::textarea('di_item[]', null, ['class' => 'product-name description']) !!}
                    @if($errors->has('di_item.8'))
                    <div class="error_msg">
                        {{ $errors->first('di_item.8') }}
                    </div>
                    @endif
                </div>
            </div>
            <div class="form-group d-flex">
                <div class="label-section d-flex align-items-center">
                    {!! Form::label('di_item10', '商品の特徴', ['class' => 'col-form-label']) !!}
                </div>
                <div class="field-section">
                    {!! Form::textarea('di_item[]', null, ['class' => 'product-name description']) !!}
                    @if($errors->has('di_item.9'))
                    <div class="error_msg">
                        {{ $errors->first('di_item.9') }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-center form-submission">
            <button type="submit" class="btn btn-submit">登録する</button>
        </div>
    </section>
    {{-- Detail info section end--}}
    {!! Form::close() !!}
</div>
@endsection

@push('custom-scripts')
<script type="text/javascript">
    var noImage = "";
</script>
<script src="{{ asset('js/product-create.js') }}"></script>
<script src="{{ asset('vendor/bs-v4-datepicker/gijgo.min.js') }}"></script>
<script src="{{ asset('vendor/bs-v4-datepicker/messages.ja-jp.js') }}"></script>
@endpush