@extends('admin.layouts.admin')

@push('custom-style')
<link rel="stylesheet" href="{{ asset('css/admin/css/buyer-search.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/ion-rangeslider/jquery.mobile-1.4.5.min.css') }}"/>
<link rel="stylesheet" href="{{ asset('vendor/bs-v4-datepicker/font-awesome.min.css') }}"/>
<link rel="stylesheet" href="{{ asset('vendor/bs-v4-datepicker/gijgo.min.css') }}"/>
<link rel="stylesheet" href="{{ asset('vendor/select2/css/select2.min.css') }}" />
@endpush

@push('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('buyer.top') }}">管理 TOP</a></li>
<li class="breadcrumb-item active" aria-current="page">商品検索 </li>
@endpush


@section('content')
<div class="inner-content">
    @if ($errors->any())
    <div class="custom-alert alert">
        <span class="rito rito-check"></span>
        <p>{{$errors->first()}}</p>
    </div>
    @endif
    <section class="box-content">
        <div class="d-flex align-items-center page-title">
            <h1 class="text-search">商品検索</h1>
            <div class="text-search-block">
                <h1>バナナ</h1>
                <p>の検索結果</p>
                <h2> <span class="total-items"></span> 件が該当しました ( <span class="showing-items"></span>件)</h2>
                {!! Form::select('size',[
                '0' => '隠す', '1' => '公演'
                ], null, ['placeholder' => '検索条件を隠す', 'class' => 'custom-select sort-block', 'id'=>'hideFilterBar', 'onchange'=>'hideFilterBar()']) !!}
            </div>
        </div>
        {!! Form::open(array('class'=> 'form-block', 'id' => 'filterForm', 'method'=>'POST', 'enctype' => 'multipart/form-data',)) !!}
            @csrf
             <div class="form-content">
                <h1 class="text-search">検索項目を入力して「検索」ボタンをクリックしてください</h1>
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('keyword', 'キーワード', ['class' => 'col-form-label']) !!}
                    </div>
                     <div class="field-section">
                        {!! Form::text('keyword', null, ['class' => 'keyword', 'id' => 'input-keyword']) !!}
                        <p id="block-keyword">
                            <span class="keyword-text">バナナ</span>
                            <span class="keyword-delete" onclick="keywordDelete()">削除する</span>
                        </p>
                     </div>
                </div>
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('category', 'カテゴリー', ['class' => 'col-form-label']) !!}
                    </div>
                    <div class="field-section">
                        {!! Form::select('category', $categories, null, ['placeholder' => 'すべて', 'class' => 'custom-select category select2']) !!}
                    </div>
                </div>
                <div class="form-group d-flex mb-0">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('public', '離島', ['class' => 'col-form-label']) !!}
                    </div>
                    <div class="field-section">
                        {!! Form::select('island', $islands, null, ['placeholder' => 'すべて', 'class' => 'custom-select island select2']) !!}
                    </div>
                </div>
                <div class="form-group d-flex">
                    <div class="label-section range-label d-flex align-items-center">
                        {!! Form::label('title', '価格', ['class' => 'col-form-label']) !!}
                    </div>
                    <div class="field-section">
                        <div class="range-block">
                            <input type="text" class="js-range-slider" name="my_range" value="" />
                        </div>
                        <div class="d-flex justify-content-start align-items-center rangeslide-block">
                            <div class="d-flex justify-content-start align-items-center rangeslider-input">
                                {!! Form::number('from', '0', ['id' => 'range-from', 'class' => 'keyword', 'onchange' => 'rangeInputChange()']) !!}
                                <span>円</span>
                            </div>
                            <div class="d-flex justify-content-start align-items-center rangeslider-input-s">
                                {!! Form::number('to', $maxPrice, ['id' => 'range-to', 'class' => 'keyword', 'onchange' => 'rangeInputChange()']) !!}
                                <span>円</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-start">
                        {!! Form::label('public', '売り先', ['class' => 'col-form-label']) !!}
                    </div>
                    <div class="field-section d-flex custom-with">
                        @foreach($salerDestination ->sortBy('id') as $data)
                        <div class="custom-control custom-checkbox">
                            {!! Form::checkbox('salesDestination[]', $data->id, false, ['class' => 'custom-control-input salesCheckbox', 'id' => 'saler-'.$data->id]) !!}
                            {!! Form::label('saler-'.$data->id, $data->name, ['class' => 'custom-control-label']) !!}
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('number_search', '検索結果表示件数', ['class' => 'col-form-label']) !!}
                    </div>
                    <div class="field-section">
                        {!! Form::select('number',['20' => '20', '50' => '50', '100' => '100'], 20, ['class' => 'custom-select status number-search', 'id'=>'perPage']) !!}
                        <span>件</span>
                        <div class="error_msg" id="perPageError"></div>
                    </div>
                </div>
             </div>
            <div class="d-flex justify-content-center form-submission">
                <button type="button" class="btn btn-clear" onclick="formReset()">クリア</button>
                <button type="button" class="btn btn-submit" onclick="formSubmit()">検索</button>
            </div>
        {!! Form::close() !!}
    </section>
    <section class="box-content mt-20">
        <div id="searchPageTitle" class="align-items-center page-title">
            <h1>新着商品</h1>
        </div>
        <div id="searchPagePagination" class="align-items-center justify-content-between search-info-block">
            <div class="data-pagination">
                <ul class="pagination">
                </ul>
            </div>
            {!! Form::select('size',[
                'desc' => '登録の早い順', 'asc' => '登録の遅い順'
                ], null, ['placeholder' => '新着順 ...', 'class' => 'custom-select sort-block', 'id'=>'orderBy', 'onchange'=>'orderBy(this)']) !!}
        </div>
        <div class="data-container">
            <div class="loader-block">
                <div class="loader"></div>
            </div>
            <div class="data-block" id="data-table">
            </div>
            <div class="d-flex justify-content-between align-items-center data-pagination">
                <ul class="pagination"></ul>
            </div>
        </div>
    </section>
</div>
@endsection

@push('custom-scripts')
<script src={{ asset('vendor/ion-rangeslider/ion.rangeSlider.min.js') }}></script>

<script src="{{ asset('vendor/bs-v4-datepicker/gijgo.min.js') }}"></script>
<script src="{{ asset('vendor/bs-v4-datepicker/messages.ja-jp.js') }}"></script>
<script src="{{ asset('vendor/moment/moment.js') }}"></script>
<script src="{{ asset('vendor/select2/js/select2.min.js') }}"></script>
<script>
    var SITEURL = '{{ URL::to('') }}';
    var maxPrice = '{{ $maxPrice }}';
    var ASSET_URL = "{{ config('app.asset_url') }}";
    var deleteUrl = SITEURL + 'buyer/top';
    var per_page = 20;
    var current_page = 1;
    var order_by = 'desc';
    var setCategory = "{{ $setCategory }}";
    var category;
    if(setCategory != '') {
        category = setCategory;
        $('.category').val(setCategory);
    } else {
        category = $('.category').val();
    }
    var setIsland = "{{ $setIsland }}";
    var island;
    if(setIsland != '') {
        island = setIsland;
        $('.island').val(setIsland);
    } else {
        island = $('.island').val();
    }
    var salesCheckbox = "";
    var keyword = $('.keyword').val();
    var rangeFrom = 0;
    var rangeTo = maxPrice;
    $(document).ready(function() {
        getPagination(current_page);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });

    function formReset() {
        current_page = 1;
        start_date = '';
        end_date = '';
        order_by = 'desc';
        status = '';
        island = '';
        category = '';
        keyword = '';
        salesCheckbox = '';
        $('#range-from').val(0);
        $('#range-to').val(maxPrice);
        $('#filterForm')[0].reset();
        rangeInputChange();
        getPagination(current_page);
        $('#searchPagePagination').hide();
        $('#searchPageTitle').css('display', 'flex');
    }
    
    function formSubmit() {
        event.preventDefault()
        salesCheckbox = $('input[type=checkbox]:checked').map(function(_,el){
                            return $(el).val();
                        }).get();
        per_page = $('#perPage').val();
        current_page = 1;
        category = $('.category').val();
        island = $('.island').val();
        keyword = $('.keyword').val();
        rangeFrom = $('#range-from').val();
        rangeTo = $('#range-to').val();
        error = 0;
        date_regex = /^(0[1-9]|1[0-2])\/(0[1-9]|1\d|2\d|3[01])\/(19|20)\d{2}$/;
        if (per_page < 1 ) {
            error++;
            $('#perPageError').html('<p>無効な番号</p>');
        }
        if (per_page > 100 ) {
            error++;
            $('#perPageError').html('<p>' + invalid_number_max +'</p>');
        }
        if(error < 1) {
            $('#perPageError').html('');
            getPagination(current_page);
            $('#searchPageTitle').hide();
            $('#searchPagePagination').css('display', 'flex');
        }
    }

    function orderBy(option) {
        selected = $(option).children("option:selected").val();
        order_by = selected == ''?'desc':selected;
        current_page = 1;
        getPagination(current_page);
    }
    
    function getPagination(page){
        $('.loader-block').show();
        $('#data-table').html('');
        $('.pagination').hide();
        current_page = page;
        var url = SITEURL + '/buyer/top?page=' + current_page +'&salesCheckbox='+salesCheckbox+ '&per_page=' + per_page + '&order_by=' + order_by + '&category=' + category + '&keyword=' + keyword + '&rangeFrom=' + rangeFrom + '&rangeTo=' + rangeTo + '&island=' + island;

        $.ajax({
            type: "GET",
            url: url,
            success: function (response) {
                $('#serverError').html('');
                $('.loader-block').hide();
                $('#data-table').show();
                $('.pagination').show();
                $('.pagination').html(getPaginationNav(response));
                if(response.data.length != 0) {
                    $('#data-table').html(response.data.map(table_data));
                    $('.showing-items').html(response.from + ' - ' + response.to);
                    $('.total-items').html(response.total);
                } else {
                    $('#data-table').html('<p class="no-data">関連データが見つかりません</p>');
                    $('.showing-items').html('0 - 0');
                    $('.total-items').html('0');
                }
                if(keyword != '') {
                    $('.text-search').hide();
                    $('.text-search-block').css('display', 'flex');
                    $('.text-search-block h1').html(keyword);
                    $('.keyword-text').html(keyword);
                    $('#input-keyword').hide();
                    $('#block-keyword').show();
                } else {
                    $('.text-search').show();
                    $('.text-search-block').css('display', 'none');
                    $('#input-keyword').show();
                    $('#block-keyword').hide();
                }
            },
            error: function (response) {
                console.log('Error:', response);
            }
        });
    }
    function keywordDelete() {
        keyword = '';
        $('#input-keyword').val('');
        current_page = 1;
        getPagination(current_page);
    }
    function hideFilterBar() {
        var getValue = $('#hideFilterBar').val();
        if(getValue === '1') {
            $('#filterForm').fadeIn('slow');
        } else {
            $('#filterForm').fadeOut('slow');
        }

    }
    function table_data(object) {
        var view = '';
        view += '<div class="product-block col-md-3 col-sm-3">';
        view += '<div class="product-image-block">';
        view += '<a href="' + SITEURL + '/products/detail/' + object.id + '" class="product-image">';
        view += '<img src="' + ASSET_URL + object.cover_image_md + '" alt="">';
        if(moment().diff(object.created_at, 'days') < 30) {
            view += '<span class="new">New</span>';
        }
        view += '</a>';
        view += '<div class="description"><a href="' + SITEURL + '/products/detail/' + object.id + '">' + object.name + '</a></div>';
        view += '<div class="ammount"><p>¥' + object.sell_price + '</p></div>';
        view += '</div>';
        view += '</div>';
        return view;
    }
    function getPaginationNav(object) {
        var view = '';

        view+= '<li class="pagination-item">';
        view+= '<span class="pagination-link" onclick="getPagination(1)" aria-label="Previous">';
        view+= '<span aria-hidden="true">&laquo;</span>';
        view+= '</span>';
        view+= '</li>';
        if(object.prev_page_url != null) {
            if(object.current_page > 2) {
                view+= '<li class="pagination-item">';
                view+= '<span class="pagination-link" onclick="getPagination(' + (object.current_page - 2) + ')">' + (object.current_page - 2) + '</span>';
                view+= '</li>';
            }
            view+= '<li class="pagination-item">';
            view+= '<span class="pagination-link" onclick="getPagination(' + (object.current_page - 1) + ')">' + (object.current_page - 1) + '</span>';
            view+= '</li>';
        }
        view+= '<li class="pagination-item">';
        view+= '<span class="pagination-link active">' + object.current_page + '</span>';
        view+= '</li>';
        if(object.next_page_url != null) {
            view+= '<li class="pagination-item">';
            view+= '<span class="pagination-link"  onclick="getPagination(' + (object.current_page + 1) + ')">' + (object.current_page + 1) + '</span>';
            view+= '</li>';
            if(object.current_page < (object.last_page - 1)) {
                view+= '<li class="pagination-item">';
                view+= '<span class="pagination-link"  onclick="getPagination(' + (object.current_page + 2) + ')">' + (object.current_page + 2) + '</span>';
                view+= '</li>';
            }
        }
        view+= '<li class="pagination-item">';
        view+= '<span class="pagination-link"  onclick="getPagination(' + object.last_page + ')" aria-label="Next">';
        view+= '<span aria-hidden="true">&raquo;</span>';
        view+= '</span>';
        view+= '</li>';
        return view;
    }

    $(document).ready(function() {
        $(".js-range-slider").ionRangeSlider({
            type: "double",
            skin: "big",
            min: 0,
            max: maxPrice,
            from: 0,
            to: maxPrice,
            drag_interval: true,
            min_interval: null,
            max_interval: null,
            onChange: function (data) {
                $("#range-from").val(data.from);
                $("#range-to").val(data.to);
            }
        });
    });

    function rangeInputChange(){
        var from = $("#range-from").val();
        var to = $("#range-to").val();
        let my_range = $(".js-range-slider").data("ionRangeSlider");
        my_range.update({
            from: from,
            to: to
        });
    }

</script>
@endpush