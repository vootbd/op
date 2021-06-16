
@push('custom-style')
<link rel="stylesheet" href="{{ asset('css/admin/css/product-detail.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/swiper/dist/css/swiper.min.css') }}">
@endpush


<div class="inner-content">
    @if(!empty($product->status) == 0)
    <section class="box-content">
        <div class="d-flex align-items-center inactive-product">
            <p>この製品は未登録です</p>
        </div>
    </section>
    @else 
    <section class="box-content">
        <div class="form-content">
            <div class="product-header-block">
                <div class="image-block">
                    <div class="swiper-container gallery-top">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide" style="background-image:url({{ asset($product->cover_image_md) }})"></div>
                            @if(!empty($product->productImages))
                                @foreach ($product->productImages as $img)
                                    <div class="swiper-slide" style="background-image:url({{asset($img->image_md)}})"></div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    <div class="swiper-container gallery-thumbs">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide" style="background-image:url({{ asset($product->cover_image_sm) }})"></div>
                            @if(!empty($product->productImages))
                                @foreach ($product->productImages as $data)
                                    <div class="swiper-slide" style="background-image:url({{ asset($data->image_sm) }})"></div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    <div class="images-download">
                        <a class="btn btn-download" href="{{ route('downloadFile') }}?product_id={{ $product->id}}"><i class="rito rito-image"></i>画像一括ダウンロード</a>
                    </div>
                    <div class="sp-btn-block">
                        <a class="btn btn-sample" href="{{ route('inquirys.create') }}?product={{ $product->id}}">サンプル申請する </a>
                    </div>
                    <div class="sp-link-block">
                        <a href="#"><i class="rito rito-mail"></i>サンプル申請する </a>
                        <a href="#"><i class="rito rito-phone-call"></i>サンプル申請する </a>
                    </div>
                </div>
                <div class="account-block">
                    <div class="button-block">
                        @if($created_days < 30)
                        <button class="btn btn-new">New</button>
                        @endif
                        <button class="btn btn-island">{{ !empty($island) ? $island['name'] : ''}}</button>
                        <a href="{{ route('download.pdf',$product->id) }}">Pdf Donwload</a>
                    </div>
                    <h2>{{ $product->name }}</h2>
                    <div class="price-block">
                        <p>{{ $users->display_name !== null ? $users->display_name : $users->name }}</p>
                        <div class="boder-buttom"></div>
                        <p>{{ $category->name }}</p>
                        <div></div>
                        <p class="amount">¥ {{ $product->sell_price }}</p>
                    </div>
                    <div class="btn-block">
                        <a class="btn btn-sample" href="{{ route('inquirys.create') }}?product={{ $product->id}}">サンプル申請する </a>
                    </div>
                    <div class="link-block">
                        <a href="#"><i class="rito rito-mail"></i>サンプル申請する </a>
                        <a href="#"><i class="rito rito-phone-call"></i>サンプル申請する </a>
                    </div>
                </div>
            </div>
            <div class="product-description-bolck">
                <div class="head-title">
                    <h2>商品の説明が表示されます。</h2>
                </div>
                <div class="sub-title">
                    <p>{{ $product->product_explanation }}</p>
                </div>
            </div>
            @if(!empty($product->url) && isset($product->url))
                <div class="video-block">
                    <iframe width="100%" height="100%" allowfullscreen frameborder="0" src="{{ !empty($product->url)? $product->url : '' }}"></iframe>
                </div>
            @endif
        </div>
    </section>
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
                    {{ $users->name }}
                </p>
            </div>
            <div class="d-flex align-items-start product-data">
                <p class="label-block">
                    発送方法
                </p>
                <p class="data-block">
                    {{ $product->shipment_method }}
                </p>
            </div>
            <div class="d-flex align-items-start product-data">
                <p class="label-block">
                    保存方法
                </p>
                <p class="data-block">
                    {{ $product->preservation_method }}
                </p>
            </div>
            <div class="d-flex align-items-start product-data">
                <p class="label-block">
                    包装形態
                </p>
                <p class="data-block">
                    {{ $product->package_type }}
                </p>
            </div>
            <div class="d-flex align-items-start product-data">
                <p class="label-block">
                    品質保持温度
                </p>
                <p class="data-block">
                    {{ $product->quality_retention_temperature }}
                </p>
            </div>
            <div class="d-flex align-items-start product-data">
                <p class="label-block">
                    賞味期限・消費期限
                </p>
                <p class="data-block">
                    {{ $product->expiration_taste_quality }}
                </p>
            </div>
            <div class="d-flex align-items-start product-data">
                <p class="label-block">
                    利用シーン（レシピ・調理法）
                </p>
                <p class="data-block">
                    {{ $product->use_scene }}
                </p>
            </div>
            <div class="d-flex align-items-start product-data">
                <p class="label-block">
                    アレルギー表示
                </p>
                <p class="data-block">
                    <span class="heading">表示義務有り</span>
                    @foreach ($allergys as $allergy)
                        @if($allergy->is_recommended == 0)
                            <span>{{ $allergy->name }}</span>
                        @endif
                    @endforeach
                </p>
            </div>
            <div class="d-flex align-items-start product-data">
                <p class="label-block">
                </p>
                <p class="data-block">
                    <span class="heading">表示推奨</span>
                    @foreach ($allergys as $allergy)
                        @if($allergy->is_recommended == 1)
                            <span>{{ $allergy->name }}</span>
                        @endif
                    @endforeach
                </p>
            </div>
        </div>
    </section>
    <section class="box-content mt-20">
        <div class="d-flex align-items-center page-title page-title">
            <h1>生産者情報</h1>
        </div>
        <div class="form-content">
            @foreach ($productAdditionals as $key => $additional)
                <div class="d-flex align-items-center product-data">
                    <p class="label-block">
                        項目 {{ numberToAlphabet($key)}}
                    </p>
                    <p class="data-block">
                        {{ $additional->description }}
                    </p>
                </div>
            @endforeach
        </div>
    </section>
    @endif
</div>
