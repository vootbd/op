@extends('admin.layouts.admin')

@push('custom-style')
<link rel="stylesheet" href="{{ asset('css/admin/css/product-read.css') }}">
@endpush

@push('breadcrumb')
@role('operator')
<li class="breadcrumb-item"><a href="{{ route('seller.list') }}">管理 TOP</a></li>
@endrole()
@role('seller')
<li class="breadcrumb-item"><a href="{{ route('sellerProductList') }}">管理 TOP</a></li>
@endrole()
<li class="breadcrumb-item"><a href="{{ route('products.index') }}">商品一覧</a></li>
<li class="breadcrumb-item active" aria-current="page"> 商品確認</li>
@endpush

@section('content')

<div class="inner-content">
    <section class="box-content">
        <div class="d-flex align-items-center page-title">
            <h1>商品情報の確認</h1>
        </div>
        <div class="form-content">
            <div class="form-group d-flex">
                <div class="label-section d-flex align-items-center">
                    <label>公開・非公開</label>
                </div>
                <div class="field-section">
                    <p>{{ ($product->status == 1) ? '公開':'非公開' }}</p>
                </div>
            </div>
            <hr>
            <div class="form-group d-flex">
                <div class="label-section d-flex align-items-center">
                    <label>商品名</label>
                </div>
                <div class="field-section">
                    <p>{{ $product->name }}</p>
                </div>
            </div>
            <div class="form-group d-flex description">
                <div class="label-section d-flex align-items-start">
                    <label>商品の説明</label>
                </div>
                <div class="field-section">
                    <p>{{ $product->product_explanation }}</p>
                </div>
            </div>
            <hr>
            <div class="form-group d-flex">
                <div class="label-section d-flex align-items-center">
                    <label>商品の詳細</label>
                </div>
                <div class="field-section">
                    <p>カテゴリー</p>
                </div>
            </div>
            <div class="form-group d-flex">
                <div class="label-section d-flex align-items-center">
                    <label></label>
                </div>
                <div class="field-section">
                    <p>{{ $category->name }}</p>
                </div>
            </div>
            <div class="form-group d-flex">
                <div class="label-section d-flex align-items-start">
                    <label>希望小売価格</label>
                </div>
                <div class="field-section d-flex selling-block">
                    <P class="selling-price">価格</p>
                    <P class="yean">￥</p>
                    <p>{{ $product->price }}</p>
                </div>
            </div>
            <div class="form-group d-flex selling-sub">
                <div class="label-section d-flex align-items-start">
                    <label></label>
                </div>
                <div class="field-section d-flex selling-block">
                    <P class="selling-price">消費税率</p>
                    <P class="percentage">{{ $product->tax }}%</p>
                </div>
            </div>
            <div class="form-group d-flex">
                <div class="label-section d-flex align-items-start">
                    <label></label>
                </div>
                <div class="field-section d-flex selling-total-block">
                    <P class="total-selling-price">消費税率</p>
                    <P class="total-yean">￥</p>
                    <p>{{ $product->sell_price }}</p>
                </div>
            </div>
        </div>
    </section>
    <section class="box-content mt-20">
        <div class="form-content ">
            <div class="form-group d-flex">
                <div class="label-section d-flex align-items-center">
                    <label>メイン画像</label>
                </div>
                <div class="field-section">
                    <img src="{{ asset($product->cover_image_sm) }}">
                </div>
            </div>
            @if(! $product->productImages->isEmpty())
            <hr>
            <div class="form-group d-flex">
                <div class="label-section d-flex align-items-center">
                    <label>追加画像</label>
                </div>
            </div>
            @if(!empty($product->productImages[0]))
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        <label>画像1</label>
                    </div>
                    <div class="field-section">
                        <img src="{{ asset($product->productImages[0]->image_sm) }}">
                    </div>
                </div>
            @endif
            @if(!empty($product->productImages[1]))
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        <label>画像2</label>
                    </div>
                    <div class="field-section">
                        <img src="{{ asset($product->productImages[1]->image_sm) }}">
                    </div>
                </div>
            @endif
            @if(!empty($product->productImages[2]))
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        <label>画像3</label>
                    </div>
                    <div class="field-section">
                        <img src="{{ asset($product->productImages[2]->image_sm) }}">
                    </div>
                </div>
            @endif
            @if(!empty($product->productImages[3]))
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        <label>画像4</label>
                    </div>
                    <div class="field-section">
                        <img src="{{ asset($product->productImages[3]->image_sm) }}">
                    </div>
                </div>
            @endif
            @if(!empty($product->productImages[4]))
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        <label>画像5</label>
                    </div>
                    <div class="field-section">
                        <img src="{{ asset(!empty($product->productImages[4]) ? $product->productImages[4]->image_sm: '') }}">
                    </div>
                </div>
            @endif
            @endif
        </div>
    </section>
        {{-- URL section start --}}
        @if(!empty($product->url) && isset($product->url))
            <section class="box-content mt-20">
                <div class="form-content">  
                    <div class="product-description-bolck">
                        <div class="video-block">
                            <iframe width="100%" height="100%" allowfullscreen frameborder="0" src="{{ !empty($product->url)? $product->url : '' }}"></iframe>
                        </div>
                    </div>
                </div>
            </section>
          @endif
    {{-- URL section end --}}

    {{-- Detail info with checkbox section start --}}
    <section class="box-content mt-20">
        <div class="d-flex align-items-center page-title page-title">
            <h1>詳細情報</h1>
        </div>
        <div class="form-content">
            <div class="d-flex align-items-start product-data">
                <p class="label-block">
                    売り先
                </p>
                <p class="data-block">
                    @if(isset($productSalesDestinations) && !empty($productSalesDestinations))
                        @foreach ($productSalesDestinations as $destination)
                            <span>{{ $destination->name }}</span>
                        @endforeach
                    @endif
                </p>
            </div>
            @if(!empty($product->shipment_method) && isset($product->shipment_method))
            <div class="d-flex align-items-start product-data">
                <p class="label-block">
                    発送方法
                </p>
                <p class="data-block">
                    {{ $product->shipment_method }}
                </p>
            </div>
            @endif
            @if(!empty($product->preservation_method) && isset($product->preservation_method))
            <div class="d-flex align-items-start product-data">
                <p class="label-block">
                    保存方法
                </p>
                <p class="data-block">
                    {{ preservationMethodList($product->preservation_method) }}
                </p>
            </div>
            @endif
            @if(!empty($product->package_type) && isset($product->package_type))
            <div class="d-flex align-items-start product-data">
                <p class="label-block">
                    包装形態
                </p>
                <p class="data-block">
                    {{ $product->package_type }}
                </p>
            </div>
            @endif
            @if(!empty($product->quality_retention_temperature ) && isset($product->quality_retention_temperature ))
            <div class="d-flex align-items-start product-data">
                <p class="label-block">
                    品質保持温度
                </p>
                <p class="data-block">
                    {{ $product->quality_retention_temperature }}
                </p>
            </div>
            @endif
            @if(!empty($product->expiration_taste_quality) && isset($product->expiration_taste_quality ))
            <div class="d-flex align-items-start product-data">
                <p class="label-block">
                    賞味期限・消費期限
                </p>
                <p class="data-block">
                    {{ $product->expiration_taste_quality }}
                </p>
            </div>
            @endif
            @if(!empty($product->use_scene) && isset($product->use_scene ))
            <div class="d-flex align-items-start product-data">
                <p class="label-block">
                    利用シーン（レシピ・調理法）
                </p>
                <p class="data-block">
                    {{ $product->use_scene }}
                </p>
            </div>
            @endif
            @if (! $allergys->isEmpty())
            <div class="d-flex align-items-start product-data">
                <p class="label-block">
                    アレルギー表⽰（特定原料）
                </p>
                <p class="data-block">
                    <span class="heading">表示義務有り（※使⽤している原料にチェック）</span>
                    @foreach ($allergys as $allergy)
                        @if($allergy->is_recommended == 0)
                            <span>{{ $allergy->name }}</span>
                        @endif
                    @endforeach
                </p>
            </div>
            @endif
            @if (! $allergys->isEmpty())
            <div class="d-flex align-items-start product-data">
                <p class="label-block">
                </p>
                <p class="data-block">
                    <span class="heading">表示推奨（※使⽤している原料にチェック）</span>
                    @foreach ($allergys as $allergy)
                       @if($allergy->is_recommended == 1)
                            <span>{{ $allergy->name }}</span>
                        @endif
                    @endforeach
                </p>
            </div>
            @endif
        </div>
    </section>
    <section class="box-content mt-20">
        <div class="d-flex align-items-center page-title page-title">
            <h1>詳細追加情報</h1>
        </div>
        <div class="form-content">
            @foreach ($productAdditionals as $key => $additional)
                @php
                    if($loop->index == 2) {
                        $addClass = " maximum-minimum-input";
                        $spanClass = "<span class='min-max-show-data'></span>";
                    }else if($loop->index == 4) {
                        $addClass = " size-section-input";
                        $spanClass = "<span class='size-show-data'></span>";
                    }else if(9 < $loop->index){
                        $labelName = "項目 "; 
                    }else{
                        $labelName = '';
                        $addClass = '';
                        $spanClass = "";
                    }
                @endphp
                @if(10 > $loop->index)
                    @if(!empty($additional->description))
                    <div class="d-flex align-items-center product-data">
                        <p class="label-block">
                            {{ $labelName.numberToAlphabet($key)}}
                        </p>
                        <p class="data-block{{$addClass}}">
                            {!! $spanClass !!}
                            {{ $additional->description }}
                        </p>
                    </div>
                    @endif
                @endif
            @endforeach
        </div>
        {{-- Detail info section end--}}
        <div class="d-flex justify-content-center form-submission">
            @role('operator')
            <a href="{{ route('products.edit', $product->id) }}" class="btn btn-submit">編集する</a>
            @endrole
            @role('seller')
            <a href="{{ route('seller.product.edit', $product->id) }}" class="btn btn-submit">編集する</a>
            @endrole
        </div>
    </section>
</div>
@endsection
@push('custom-scripts')
<script src="{{ asset('js/read-detail.js') }}"></script>

@endpush