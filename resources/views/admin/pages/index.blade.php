@extends('admin.layouts.admin')

@push('custom-style')
<link rel="stylesheet" href="{{ asset('css/admin/css/pages-index.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/bs-v4-datepicker/gijgo.min.css') }}">
@endpush

@section('content')
<div class="inner-content">
    @push('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('seller.list') }}">TOP</a></li>
    <li class="breadcrumb-item active" aria-current="page"><a href="{{ route('pages.index')}}">ページ一覧</a></li> 
    @endpush

    @if ($message = Session::get('message'))
    <div class="custom-alert success">
        <span class="rito rito-check"></span>
        <p>{{ $message }}</p>
    </div>
    @endif    
    @if($message = Session::get('message_danger'))
        <div class="custom-alert alert">
            <span class="rito"></span>
            <p>{{ $message }}</p>
        </div>
    @endif
    <div class="custom-alert success" id="copy-message" style="display: none;">
        <span class="rito rito-check"></span>
        <p></p>
    </div> 
    <section id="scrollTop" class="box-content">       
        <div class="d-flex align-items-center page-title">
            <h1>ページ検索</h1>
        </div>
        {!! Form::open(array('class'=> 'form-block', 'method'=>'POST', 'enctype' => 'multipart/form-data','id'=> 'filterForm')) !!}
            @csrf
            <div class="form-content">
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('keyword', 'キーワード', ['class' => 'col-form-label']) !!}
                    </div>
                    <div class="field-section">
                        {!! Form::text('keyword',  null, ['class' => 'status keyword', 'id' => 'search_keword']) !!}
                    </div>
                </div>
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('status', 'ステータス', ['class' => 'col-form-label']) !!}
                    </div>
                    <div class="field-section">
                        {!! Form::select('status', [1 => '公開', 2 => '下書き' , 3 => '非公開 '], null, ['placeholder' => 'すべて', 'class' => 'custom-select status' , 'id' => 'search_status']) !!}
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
                            {!! Form::select('number',['10' => '10', '25' => '25', '50' => '50'], 10, ['class' => 'custom-select status number', 'id'=>'perPage']) !!}
                        <div class="error_msg" id="perPageError">
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-center form-submission">
                <button type="button" class="btn btn-clear" id="page_reset">クリア</button>
                <button type="button" class="btn btn-submit" id="page_search">検索</button>
            </div>
        {!! Form::close() !!}
    </section>
    <hr>
    <section class="box-content">
        <div class="box-inner-content"> 
            <div class="filter-content"> 
                <h2 class="top-info"> <span class="total-items-top"></span></h2>
                <div class="batch-operation">                    
                    {!! Form::select('bulk_delete', ['' => '一括操作', '1' => '削除',  '2' => '複製' , 'ステータス' => ['公開にする' , '下書きにする' , '非公開にする'] ], null, ['class' => 'custom-select','id' => 'batch_name_for_delete']) !!}
                    <div class="form-submission">
                        <button onclick="btnDeleteData()" class='btn btn-submit'>適用</button>
                    </div>
                </div>
            </div>
            <div class="dt">
                <table id="data-table" class="table">
                    <thead>
                        <tr>
                            <th style="width: 28px;">
                                <div class="custom-control custom-checkbox d-flex align-items-center mt">
                                    {!! Form::checkbox('title', null, false, ['class' => 'custom-control-input', 'id' => 'gridCheck1']) !!}
                                    {!! Form::label('gridCheck1', ' ', ['class' => 'custom-control-label publish-label']) !!}
                                </div>
                            </th>
                            <th style="margin-right:-20px;"><div class="d-flex link">タイトル<div class="sorting-caret"><span class="fa fa-caret-up"></span><span class="fa fa-caret-down"></span></div></div></th> 
                            <th><div class="d-flex">ユーザー<div class="sorting-caret"><span class="fa fa-caret-up"></span><span class="fa fa-caret-down"></span></div></div></th>
                            <th><div class="d-flex">ページ階層<div class="sorting-caret"><span class="fa fa-caret-up"></span><span class="fa fa-caret-down"></span></div></div></th>
                            <th>
                                <div class="d-flex">
                                    ステータス
                                    <div class="sorting-caret">
                                        <span class="fa fa-caret-up"></span>
                                        <span class="fa fa-caret-down"></span>
                                    </div>
                                </div>
                            </th>
                            <th><div class="d-flex">日付<div class="sorting-caret"><span class="fa fa-caret-up"></span><span class="fa fa-caret-down"></span></div></div></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                <div class="d-flex justify-content-between align-items-center data-pagination">
                    <div class="info"></div>
                    <ul class="pagination"></ul>
                </div>
            </div>            
        </div>
    </section>
</div>
@include('admin.partials.delete_modal')
@include('admin.partials.copy_modal')
@include('admin.partials.status_modal')
@endsection
@push('custom-scripts')
<script src="{{ asset('vendor/datatable/datatables.min.js') }}"></script>
<script src="{{ asset('vendor/bs-v4-datepicker/gijgo.min.js') }}"></script>
<script src="{{ asset('vendor/bs-v4-datepicker/messages.ja-jp.js') }}"></script>
<script>
var table; 

function totalItemsShow(data,from,to){
    $('.total-items-top').html(data + " 件が該当しました (" + ((to == 0)?to:from) + ' - ' + to + " 件)");
    $('#data-table_info').html(data + " 件が該当しました (" + ((to == 0)?to:from) + ' - ' + to + " 件)");    
}

function caretDisplyOnSorting(settings){
    var sortingTh = settings.aaSorting[0]
    var th = $('th')
    $.each(th,function(i,val){
        $(val).find('.fa-caret-down').css('opacity','1');
        $(val).find('.fa-caret-up').css('opacity','1');
    });
    if(sortingTh['_idx']){
        $(th[sortingTh[0]]).find('.fa-caret-up').css('opacity','0');
    }
    else{
        $(th[sortingTh[0]]).find('.fa-caret-down').css('opacity','0');
    }
}
$( document ).ready( function () {
    // Language var start
    var no_data_found = 'データが見つかりませんでした。';
    // Language var end

    /************* User index page data showing start ****************/ 
    var deleteUrl = SITEURL + '/pages';
    var text_truncate = function ( str, length, ending ) {
        if ( length == null ) {
            length = 100;
        }
        if ( ending == null ) {
            ending = '...';
        }
        if ( str.length > length ) {
            return str.substring( 0, length - ending.length ) + ending;
        } else {
            return str;
        }
    };  
    $.ajaxSetup( {
        headers: {
            'X-CSRF-TOKEN': $( 'meta[name="csrf-token"]' ).attr( 'content' )
        }
    } );
    table = $('#data-table').DataTable( {
        processing: true,
        responsive: true,
        serverSide: true,   
        ajax: {
            url: SITEURL + '/pages',
            type: 'GET',
            statusCode: {
                401: function(){
                    location.reload()
                },
                419: function(){
                    location.reload()
                }
            },            
        },
        columns: [
            {
                data: 'id',
                'targets': 0,
                'searchable': false,
                'orderable': false, 
                'className': 'dt-body-center',
                'width': '0%',
                'render': function (data) {
                    return '<div class="custom-control custom-checkbox d-flex align-items-center" style="margin-top:-20px;"><input type="checkbox" class="custom-control-input delete-checked" id="check_' + data + '" name="delete_check[]" value="' + data + '"><label for="check_' + data + '" class="custom-control-label publish-label"></label>';
                }
            },
            {
                data: 'page_title',
                'targets': 1,
                'className': 'dt-body-center',
                'width': '30%',
                'render': function (data,type,row) {
                    if(row.directory_name=='<?php echo config('constants.DIRECTORY_ROOT')['name']; ?>') {
                        return "<a href='/page/"+row.url_map+"' class='link'>"+data+"</a> <a href='pages/"+row.id+"/edit' class='link'><i class='fa fa-pencil-square-o edit-icon'></i></a> ";
                    }
                    else {
                        return "<a href='/page/"+row.directory_name+"/"+row.url_map+"' class='link'>"+data+"</a> <a href='pages/"+row.id+"/edit' class='link'><i class='fa fa-pencil-square-o edit-icon'></i></a> ";
                    }
                }
            }, 
            { data: 'user_name', name: 'user_name','width': '10%' },
            { data: 'directory_name', name: 'directory_name','width': '30%'},
            { 
                data: 'is_active', 
                name:'is_active',
                'width': '10%',
                'render': function (data,type,row) {
                    return data === 1 ? "公開" : (data === 2 ? "下書き" : "非公開");
                }
            },
            { data: 'display_date', 
                'targets': 1,
	            'width': '20%',
                'className': 'dt-body-center',
                'render': function (data,type,row) { 
                    return row.status_label == 1 ? '公開終了' + '<br>' + data + '': (row.status_label == 2 ? '公開日' : '更新日') + '<br>' + data + '';
                }
            },
        ],
        dom: 'Bfrtip', 
        "pagingType": "full_numbers", 
        bFilter: false,              
        "language": {
            "lengthMenu": "公演 _MENU_ エントリー",
            "zeroRecords": no_data_found,
            "info": "_TOTAL_ 件 ( _START_ - _END_ 件)",
            "infoEmpty": "利用可能な記録はありません",
            "sSearch": "探す:",
            "sProcessing": "処理...",
            "sLoadingRecords": "レコードの読み込み...",
            "infoFiltered": "",
            "oPaginate": {
                "sFirst": "<i class='fa fa-angle-double-left'></i> 最初",
                "sLast": "最終 <i class='fa fa-angle-double-right'></i>",
                "sNext": "次 <i class='fa fa-angle-right'></i>",
                "sPrevious": "<i class='fa fa-angle-left'></i> 前"
            }
        }, 
        order: [ [ 5, 'desc' ] ],
        "fnDrawCallback": function (oSettings) { 
            totalItemsShow(table.page.info()['recordsDisplay'],oSettings.json.from,oSettings.json.to)
            caretDisplyOnSorting(oSettings)
            $($('th')[0]).css('width','1px')
            $("#gridCheck1"). prop("checked", false);
            $('.dataTables_paginate > .pagination').addClass('dt-pagination-sm');
            console.log(table.page.info()['length']); 
        }
    });

/***********coustom search input start ************/
    $('#page_search').on( 'click', function () {
        event.preventDefault() 
        var keyword = $("#search_keword").val();
        var status = $("#search_status").val();
        var start_date = $('#startDate').val();
        var end_date = $('#endDate').val();
        per_page = $('#perPage').val();  
        table.page.len(per_page).ajax.url(SITEURL+'/pages?is_active='+status+'&search_keword='+keyword+'&start_date='+start_date+'&end_date='+end_date).load();  
    } );

    $('#gridCheck1').on('click',function() {
        var rows = table.rows({'search':'applied'}).nodes();
        $('input[type="checkbox"]',rows).prop('checked',this.checked);
    });
/***********coustom search input End ************/
/*****Search form Reset Start ******/ 
    $('#page_reset').on( 'click', function () {
        event.preventDefault()
        current_page = 1;
        start_date = '';
        end_date = '';
        keyword = '';
        per_page = 10; 
        order_by = 'desc';
        status = ''; 
        table.page.len(per_page).ajax.url(SITEURL+'/pages?is_active='+status+'&search_keword='+keyword+'&start_date='+start_date+'&end_date='+end_date).load(); 
        $('#filterForm')[0].reset();    
    } );
/*****Search form Reset end ******/
});  

/*****Bulk and single delete function start ******/
var deleteUrl = SITEURL + '/pages';
var copyUrl = SITEURL + '/pages/copy';
var statusUrl = SITEURL + '/pages/status-change';
var user_check_id = [];
var opt_group =[];
function btnDeleteData() {
    user_check_id = [];
    var status = [];
    opt_group =[];
    //Batch data pickup
    var batch_name_for_delet = $("#batch_name_for_delete").val(); 
    opt_group = $('#batch_name_for_delete :selected').parent().attr('label'); 

    //check for batch select or no
    if(opt_group){    
        if(batch_name_for_delet == '0'){
            status = 1;
            $(':checkbox:checked').each(function(i){
                user_check_id[i] = $(this).val();            
            });
            user_check_id = user_check_id + ',' + status;  
            if(user_check_id.length>2){
                $('#status-modal').modal('show');
                $('#modal-status-button').attr('onclick', 'changeStatus()');
            }
            else {
                alert("行が選択されていません");
            }
        }
        else if(batch_name_for_delet == '1'){
            status = 2;
            $(':checkbox:checked').each(function(i){
                user_check_id[i] = $(this).val();
            });
            user_check_id = user_check_id + ',' + status;   
            if(user_check_id.length>2){
                $('#status-modal').modal('show');
                $('#modal-status-button').attr('onclick', 'changeStatus()');
            }
            else {
                alert("行が選択されていません");
            }
        }
        else if(batch_name_for_delet == '2'){
            status = 3;
            $(':checkbox:checked').each(function(i){
                user_check_id[i] = $(this).val(); 
            });
            user_check_id = user_check_id + ',' + status;        
            if(user_check_id.length>2){
                $('#status-modal').modal('show');
                $('#modal-status-button').attr('onclick', 'changeStatus()');
            }
            else {
                alert("行が選択されていません");
            }
        }
        else {
            alert("バッチを選択してから削除してください");
        }
    }
    else{
        if(batch_name_for_delet == '1'){
            $(':checkbox:checked').each(function(i){
                user_check_id[i] = $(this).val();
            });
            if(user_check_id.length>0){
                $('#delete-modal').modal('show');
                $('#modal-delete-button').attr('onclick', 'deleteData()');
            }
            else {
                alert("行が選択されていません");
            }
        }
        else if(batch_name_for_delet == '2'){
            $(':checkbox:checked').each(function(i){
                user_check_id[i] = $(this).val();
            });
            if(user_check_id.length>0){
                $('#copy-modal').modal('show');
                $('#modal-copy-button').attr('onclick', 'copyData()');
            }
            else {
                alert("行が選択されていません");
            }
        }
        else {
            alert("バッチを選択してから削除してください");
        }
    }   
}

//Bulk Status Change ajax call in controller
function changeStatus(){
    if(user_check_id.length>0){
        $.ajax({
            type: "POST",
            url: statusUrl + "/" + user_check_id ,
            success: function (data) {
                $('#copy-message').css('display', 'flex');
                $("#copy-message p").html(data.message);
                console.log(data);
                var oTable = $('#data-table').dataTable();
                oTable.fnDraw(false);
                $('#status-modal').modal('hide');
            },
            error: function (data) {
                if(data.status == 419){
                    location.reload()
                }
                console.log('Error:', data);
            }
        });  
    }
}

//Bulk copy ajax call in controller
function copyData(){
    if(user_check_id.length>0){
        $.ajax({
            type: "POST",
            url: copyUrl + "/" + user_check_id,
            success: function (data) {
                $('#copy-message').css('display', 'flex');
                $("#copy-message p").html(data.message);
                console.log(data);
                var oTable = $('#data-table').dataTable();
                oTable.fnDraw(false);
                $('#copy-modal').modal('hide');
            },
            error: function (data) {
                if(data.status == 419){
                    location.reload()
                }
                console.log('Error:', data);
            }
        });  
    }
}

//Bulk delete ajax call in controller
function deleteData(){
    if(user_check_id.length>0){
        console.log(deleteUrl + "/" + user_check_id);
        $.ajax({
            type: "DELETE",
            url: deleteUrl + "/" + user_check_id,
            success: function (data) {
                $('#copy-message').css('display', 'flex');
                $("#copy-message p").html(data.message);
                console.log(data);
                var oTable = $('#data-table').dataTable();
                oTable.fnDraw(false);
                $('#delete-modal').modal('hide');
            },
            error: function (data) {
                if(data.status == 419){
                    location.reload()
                }
                console.log('Error:', data);
            }
        });
    }
}
/*****Bulk and single delete function end ******/

/*****Search form Date picker starts ******/
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
/*****Search form Date picker end ******/
</script>
@endpush