@extends('admin.layouts.admin')

@push('custom-style')
<link rel="stylesheet" href="{{ asset('css/admin/css/operator-product-list.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/bs-v4-datepicker/gijgo.min.css') }}">
@endpush

@push('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('users.index') }}">管理 TOP</a></li>
<li class="breadcrumb-item active" aria-current="page">商品検索</li>
@endpush

@section('content')
<div class="inner-content">

    @if ($message = Session::get('success'))
        <div class="custom-alert success">
            <span class="rito rito-check"></span>
            <p>{{ $message }}</p>
            <span class="rito rito-x" id="alertClose"></span>
        </div>
    @endif

    <section class="box-content">
        <div class="d-flex align-items-center page-title">
            <h1>商品検索</h1>
        </div>
        {!! Form::open(array('class'=> 'form-block', 'method'=>'POST', 'enctype' => 'multipart/form-data','id'=> 'filterForm')) !!}
            @csrf
             <div class="form-content">
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('keyword', 'キーワード', ['class' => 'col-form-label']) !!}
                    </div>
                    <div class="field-section">
                        {!! Form::text('keyword', null, ['class' => 'keyword']) !!}
                        @if($errors->has('keyword'))
                            <div class="error_msg">
                                {{ $errors->first('keyword') }}
                            </div>
                        @endif
                    </div>
                </div>
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('categories', 'カテゴリー', ['class' => 'col-form-label']) !!}
                    </div>
                    <div class="field-section">
                        {!! Form::select('acount_type', $islands, null, ['placeholder' => 'すべて', 'class' => 'custom-select categories']) !!}
                    </div>
                </div>
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('status', '公開・非公開', ['class' => 'col-form-label']) !!}
                    </div>
                    <div class="field-section">
                        {!! Form::select('status', [1 => '有効な', 0 => 'やめる'], null, ['placeholder' => 'すべて', 'class' => 'custom-select status']) !!}
                    </div>
                </div>
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-start">
                        {!! Form::label('status', '登録・更新日', ['class' => 'col-form-label']) !!}
                    </div>
                    <div class="field-section d-flex flex-column date-picker-block">
                        <div class="d-flex from-date">
                        {!! Form::text('form-date', null, ['placeholder' => 'すべて','class' => 'title', 'id' => "startDate"]) !!}
                        <span class="d-flex align-items-center from-span">から</span>
                        </div>
                        <div class="error_msg" id="startDateError">
                        </div>
                        <div class="d-flex align-items-center to-date">
                            {!! Form::text('to-date', null, ['placeholder' => 'すべて','class' => 'title', 'id' => "endDate"]) !!}
                            <span class="d-flex align-items-center to-span">まで</span>
                        </div>
                        <div class="error_msg" id="endDateError">
                        </div>
                    </div>
                </div>
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-start">
                        {!! Form::label('public', '売り先', ['class' => 'col-form-label']) !!}
                    </div>
                    <div class="field-section d-flex custom-with">
                        <div class="custom-control custom-checkbox">
                            {!! Form::checkbox('public', null, true, ['class' => 'custom-control-input', 'id' => 'gridCheck1']) !!}
                            {!! Form::label('gridCheck1', '外食', ['class' => 'custom-control-label']) !!}
                        </div>
                        <div class="custom-control custom-checkbox">
                            {!! Form::checkbox('private', null, true, ['class' => 'custom-control-input', 'id' => 'gridCheck1']) !!}
                            {!! Form::label('gridCheck1', '商社・卸売', ['class' => 'custom-control-label']) !!}
                        </div>
                        <div class="custom-control custom-checkbox">
                            {!! Form::checkbox('public', null, true, ['class' => 'custom-control-input', 'id' => 'gridCheck1']) !!}
                            {!! Form::label('gridCheck1', 'メーカー', ['class' => 'custom-control-label']) !!}
                        </div>
                        <div class="custom-control custom-checkbox">
                            {!! Form::checkbox('private', null, true, ['class' => 'custom-control-input', 'id' => 'gridCheck1']) !!}
                            {!! Form::label('gridCheck1', 'スーパーマーケット', ['class' => 'custom-control-label']) !!}
                        </div>
                        <div class="custom-control custom-checkbox">
                            {!! Form::checkbox('private', null, true, ['class' => 'custom-control-input', 'id' => 'gridCheck1']) !!}
                            {!! Form::label('gridCheck1', '百貨店', ['class' => 'custom-control-label']) !!}
                        </div>
                        <div class="custom-control custom-checkbox">
                            {!! Form::checkbox('public', null, true, ['class' => 'custom-control-input', 'id' => 'gridCheck1']) !!}
                            {!! Form::label('gridCheck1', 'ホテル', ['class' => 'custom-control-label']) !!}
                        </div>
                        <div class="custom-control custom-checkbox">
                            {!! Form::checkbox('private', null, true, ['class' => 'custom-control-input', 'id' => 'gridCheck1']) !!}
                            {!! Form::label('gridCheck1', 'その他', ['class' => 'custom-control-label']) !!}
                        </div>
                    </div>
                </div>
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('number', '検索結果表示件数', ['class' => 'col-form-label']) !!}
                    </div>
                    <div class="field-section">
                        {!! Form::number('number', 20, ['class' => 'number', 'id'=>'perPage','min'=>1]) !!}
                        <div class="error_msg" id="perPageError">
                        </div>
                    </div>
                </div>
             </div>
            <div class="d-flex justify-content-center form-submission">
                <button type="button" class="btn btn-clear" onclick="formReset()">クリア</button>
                <button type="submit" class="btn btn-submit" onclick="formSubmit()">検索</button>
            </div>
        {!! Form::close() !!}
    </section>
    <section class="box-content mt-20">
        <div class="error_msg" id="serverError"></div>
        <div class="d-flex align-items-center page-title">
            <h1>検索結果一覧</h1>
        </div>
        <div class="d-flex align-items-center justify-content-between search-info-block data-pagination">
            <h2> <span id="total-items"></span> 件が該当しました ( <span id="showing-items"></span> )</h2>
            <ul class="pagination"></ul>
            {!! Form::select('size',[
                'desc' => '登録の早い順', 'asc' => '登録の遅い順'
                ], null, ['placeholder' => '新着順 ...', 'class' => 'custom-select sort-block', 'id'=>'orderBy', 'onchange'=>'orderBy(this)']) !!}
        </div>
        <hr>
        <div class="data-container">
            <div class="d-flex align-items-start justify-content-start data-block sp">
                <div class="status">売り先</div>
                <div class="product-name">商品名</div>
                <div class="product-image">商品画像</div>
                <div class="product-price">商品価格</div>
                <div class="duplication">複製</div>
                <div class="edit">編集</div>
                <div class="delete">削除</div>
            </div>
            <div class="loader-block">
                <div class="loader"></div>
            </div>
            <div class="d-flex align-items-center justify-content-start data-block data-row bg">
                <div class="status">有効</div>
                <div class="product-name">商品名ｻンプルテキスト サ</div>
                <div class="product-image"><img src="{{asset('image/images-icon.svg')}}" alt=""></div>
                <div class="product-price">1000</div>
                <div class="duplication">
                    <a href="" class="btn btn-copy">複製する</a>
                </div>
                <div class="edit">
                    <a href="" class="btn btn-edit">編集</a>
                    <a href="" class="btn btn-view">編集</a>
                </div>
                <div class="delete">
                    <a href="" class="btn btn-delete">削除</a>
                </div>
            </div>
            <div class="d-flex align-items-center justify-content-start data-block data-row">
                <div class="status">有効</div>
                <div class="product-name">商品名ｻンプルテキスト サ</div>
                <div class="product-image"><img src="{{asset('image/images-icon.svg')}}" alt=""></div>
                <div class="product-price">1000</div>
                <div class="duplication">
                    <a href="" class="btn btn-copy">複製する</a>
                </div>
                <div class="edit">
                    <a href="" class="btn btn-edit">編集</a>
                    <a href="" class="btn btn-view">編集</a>
                </div>
                <div class="delete">
                    <a href="" class="btn btn-delete">削除</a>
                </div>
            </div>
            <div id="data-table">
            </div>
            <div class="d-flex justify-content-start align-items-center data-pagination">
                <div class="info"></div>
                <ul class="pagination"></ul>
            </div>
        </div>
    </section>
</div>

@include('admin.partials.delete_modal')

@endsection

@push('custom-scripts')
<script src="{{ asset('vendor/bs-v4-datepicker/gijgo.min.js') }}"></script>
<script src="{{ asset('vendor/bs-v4-datepicker/messages.ja-jp.js') }}"></script>
<script src="{{ asset('js/deleteDataFunction.js') }}"></script>

<script type="text/javascript">
    var SITEURL = '{{ URL::to('') }}';
    var deleteUrl = SITEURL + '/users';
    var per_page = 20;
    var current_page = 1;
    var start_date = $('#startDate').val();
    var end_date = $('#endDate').val();
    var order_by = 'desc';
    var acount_type = $('.acount-type').val();
    $(document).ready(function(){
        getPagination(current_page);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });
    function formSubmit() {
        event.preventDefault()
        start_date = $('#startDate').val();
        end_date = $('#endDate').val();
        per_page = $('#perPage').val();
        current_page = 1;
        status = $('.status').val()
        acount_type = $('.acount-type').val();
        var error = 0;
        var date_regex = /^(0[1-9]|1[0-2])\/(0[1-9]|1\d|2\d|3[01])\/(19|20)\d{2}$/;
        if(!(start_date == '' && end_date == '')) {
            if(!(date_regex.test(start_date))) {
                error++;
                $('#startDateError').html('<p>'+ invalid_date +'</p>');
            }
            if(!(date_regex.test(end_date))) {
                error++;
                $('#endDateError').html('<p>'+ invalid_date +'</p>');
            }
        }
        if(end_date < end_date) {
            error++;
            $('#endDateError').html('<p>開始日は終了日より大きくすることはできません</p>');
        }
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
            $('#startDateError').html('');
            $('#endDateError').html('');
            getPagination(current_page);
        }
    }

    function formReset() {
        current_page = 1;
        start_date = '';
        end_date = '';
        order_by = 'desc';
        status = '';
        acount_type = '';
        $('#filterForm')[0].reset();
        getPagination(current_page);
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
        var url = SITEURL + '/users?page=' + current_page + '&per_page=' + per_page + '&start_date=' + start_date + '&end_date=' + end_date + '&order_by=' + order_by + '&status=' + status + '&acount_type=' + acount_type;

        $.ajax({
            type: "GET",
            url: url,
            success: function (response) {
                $('#serverError').html('');
                $('.loader-block').hide();
                $('#data-table').show();
                $('.pagination').show();
                $('#showing-items').html(response.from + ' - ' + response.to);
                $('#total-items').html(response.total);
                $('#data-table').html(response.data.map(table_data));
                $('.pagination').html(getPaginationNav(response));
            },
            error: function (response) {
                console.log('Error:', response);
            }
        });
    }
    function table_data(user) {
        var view = '';
        view += '<div id="id-' + user.id + '" class="d-flex align-items-start justify-content-start data-block data-row">';
        view += '<div class="status">';
        if(user.is_active == 1) {
            view += '有効な';
         }
         else {
            view += 'やめる';
        }
        view += '</div>';
        view += '<div class="account-name">';
        view += user.name;
        view += '</div>';
        view += '<div class="profile">';
        view += '<a href="' + SITEURL + '/users/' + user.id + '/edit" class="btn btn-profile">登録</a>';
        view += '</div>';
        view += '<div class="product-list">';
        view += '<a href="' + SITEURL + '/users/' + user.id + '/edit" class="btn btn-product-list">商品一覧へ</a>';
        view += '</div>';
        view += '<div class="edit">';
        view += '<a href="' + SITEURL + '/users/' + user.id + '/edit" class="btn btn-edit">編集</a>';
        view += '</div>';
        view += '<div class="delete">';
        view += '<button type="button" onclick="btnDeleteData(' + user.id + ')" class="btn btn-delete">削除</button>';
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

    var today = new Date(new Date().getFullYear(), new Date().getMonth(), new Date().getDate());
    $('#startDate').datepicker({
        uiLibrary: 'bootstrap4',
        locale: 'ja-jp',
        iconsLibrary: 'fontawesome',
        maxDate: function () {
            return $('#endDate').val();
        }
    });
    $('#endDate').datepicker({
        uiLibrary: 'bootstrap4',
        locale: 'ja-jp',
        iconsLibrary: 'fontawesome',
        minDate: function () {
            return $('#startDate').val();
        }
    });

    function btnDeleteData(dataId) {
        $('#delete-modal').modal('show');
        $('#modal-delete-button').attr('onclick', 'deleteData(' + dataId + ', false)');
    }

</script>
@endpush