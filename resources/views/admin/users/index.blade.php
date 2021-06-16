@extends('admin.layouts.admin')

@push('custom-style')
<link rel="stylesheet" href="{{ asset('css/admin/css/users-top.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/bs-v4-datepicker/gijgo.min.css') }}">
@endpush

@push('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('users.index') }}">管理 TOP</a></li>
<li class="breadcrumb-item active" aria-current="page">アカウント一覧</li>
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
            <h1>アカウント一覧</h1>
        </div>
        {!! Form::open(array('class'=> 'form-block', 'method'=> 'POST', 'enctype' => 'multipart/form-data','id'=> 'filterForm')) !!}
            @csrf
             <div class="form-content">
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('acount-type', 'アカウント種別', ['class' => 'col-form-label']) !!}
                    </div>
                    <div class="field-section">
                        {!! Form::select('acount_type', $roles, null, ['placeholder' => 'すべて', 'class' => 'custom-select acount-type']) !!}
                    </div>
                </div>
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('status', 'ステータス', ['class' => 'col-form-label']) !!}
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
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('number', '検索結果表示件数', ['class' => 'col-form-label']) !!}
                    </div>
                    <div class="field-section">
                        {!! Form::select('number',['20' => '20', '50' => '50', '100' => '100'], 20, ['class' => 'custom-select status number', 'id'=>'perPage']) !!}
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
    <section id="scrollTop" class="box-content mt-20">
        <div class="error_msg" id="serverError"></div>
        <div class="d-flex align-items-center page-title">
            <h1>検索結果一覧</h1>
        </div>
        <div class="d-flex align-items-center justify-content-between search-info-block">
            <h2> <span class="total-items"></span> 件が該当しました ( <span class="showing-items"></span> )</h2>
            {!! Form::select('size',[
                'desc' => '登録の早い順', 'asc' => '登録の遅い順'
                ], null, ['placeholder' => '新着順', 'class' => 'custom-select sort-block', 'id'=>'orderBy', 'onchange'=>'orderBy(this)']) !!}
        </div>
        <div class="data-container">
            <div class="d-flex align-items-start justify-content-start data-block sp">
                <div class="status">公開状態</div>
                <div class="account-type">アカウント種別</div>
                <div class="account-name">アカウント名</div>
                <div class="edit">編集</div>
                <div class="delete">削除</div>
            </div>
            <div class="loader-block">
                <div class="loader"></div>
            </div>
            <div id="data-table">
            </div>
            <div class="d-flex justify-content-between align-items-center data-pagination">
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
                $('#data-table').html(response.data.map(table_data));
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
                //$('.btn-submit').prop("disabled", true);
            },
            error: function (response) {
                console.log('Error:', response);
            }
        });
    }
    function table_data(user) {
        var view = '';
        view += '<div id="id-' + user.id + '" class="align-items-start justify-content-start data-block data-row">';
        view += '<div class="status">';
        if(user.is_active == 1) {
            view += '有効';
         }
         else {
            view += '停止中';
        }
        view += '</div>';
        view += '<div class="account-type">';
        view += user.name_jp;
        view += '</div>';
        view += '<div class="account-name">' + user.name + '</div>';
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