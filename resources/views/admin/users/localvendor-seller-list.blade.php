@extends('admin.layouts.admin')

@push('custom-style') 
<link rel="stylesheet" href="{{ asset('css/admin/css/localvendor-seller-list.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/bs-v4-datepicker/font-awesome.min.css') }}"/>
<link rel="stylesheet" href="{{ asset('vendor/bs-v4-datepicker/gijgo.min.css') }}"/>
@endpush

@push('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('localvendorProductList') }}">管理 TOP</a></li>
<li class="breadcrumb-item active" aria-current="page"> 事業者アカウント検索 </li>
@endpush
@section('content')
<div class="inner-content">  
    @if ($message = Session::get('message'))
    <div class="custom-alert success">
        <span class="rito rito-check"></span>
        <p>{{ $message }}</p>
    </div>
    @endif
    @if ($success = Session::get('success'))
    <div class="custom-alert success">
        <span class="rito rito-check"></span>
        <p>{{ $success }}</p>
    </div>
    @endif
    <div class="custom-alert success" id="copy-message" style="display: none;">
        <span class="rito rito-check"></span>
        <p></p>
    </div> 
    <section id="scrollTop" class="box-content">       
        <div class="d-flex align-items-center page-title">
            <h1>事業者アカウント検索</h1>
        </div>
        {!! Form::open(array('class'=> 'form-block', 'method'=>'POST', 'enctype' => 'multipart/form-data','id'=> 'filterForm')) !!}
            @csrf
            <div class="form-content">
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('island', '自治体／離島', ['class' => 'col-form-label']) !!}
                    </div>
                    <div class="field-section">
                        <select  id="search_island" class="custom-select select2 name select-width island status" name="island_id">
                            <option selected="selected" value="">離島</option>
                                @foreach ($islandDropDown as $item)    
                                    @if($item->islands != '[]')  
                                        <optgroup label="{{ $item->name }}">  
                                            @foreach ($item->islands as $key => $value)     
                                                    <option value="{{ $value->id }}">{{ $value->name }} </option>   
                                            @endforeach  
                                        </optgroup>   
                                    @endif 
                                @endforeach  
                        </select>
                    </div>
                </div> 
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('status', 'ステータス', ['class' => 'col-form-label']) !!}
                    </div>
                    <div class="field-section">
                        {!! Form::select('status', [1 => '有効', 0 => '停止'], null, ['placeholder' => 'すべて', 'class' => 'custom-select status' , 'id' => 'search_status']) !!}
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
                        {!! Form::label('rank', '事業者のランク', ['class' => 'col-form-label']) !!}
                    </div>
                    <div class="field-section">
                        {!! Form::select('rank',['' => 'すべて','a' => 'A','b' => 'B','c' => 'C'], null, ['class' => 'custom-select status rank number', 'id' => 'search_rank']) !!}
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
                    {!! Form::select('bulk_delete', ['' => '一括操作', '1' => '削除', 'ステータス' => ['有効にする' , '停止にする'] ], null, ['class' => 'custom-select','id' => 'batch_name_for_delete']) !!}
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
                            
                            <th style="margin-right:-20px;"><div class="d-flex link">事業者名<div class="sorting-caret"><span class="fa fa-caret-up"></span><span class="fa fa-caret-down"></span></div></div></th> 

                            <th style="margin-right:-20px;"><div class="d-flex link">地域商社<div class="sorting-caret"><span class="fa fa-caret-up"></span><span class="fa fa-caret-down"></span></div></div></th> 
                            <th>
                                <div class="d-flex link">
                                    ステータス
                                    <div class="sorting-caret">
                                        <span class="fa fa-caret-up"></span>
                                        <span class="fa fa-caret-down"></span>
                                    </div>
                                </div>
                            </th>
                            <th><div class="d-flex link">更新日<div class="sorting-caret"><span class="fa fa-caret-up"></span><span class="fa fa-caret-down"></span></div></div></th>
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
var ASSET_URL = "{{ config('app.asset_url') }}";
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
            url: SITEURL + '/localvendor/seller/list',
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
                data: 'name',
                'targets': 1,
                'className': 'dt-body-center vendor-main',
                'width': '40%',
                'render': function (data,type,row) { 
                    return "<div class = 'vendor-first'><a href='/users/"+row.id+"/edit' class='link' style='display:block; margin-bottom:-11px;'>"+data+"</a><div class = 'pr-sm-0 pr-md-5' style = 'display:inline-block;'><span><img class='icon-support pr-2' style = 'height:25px;' src='{{ asset('image/icon-product-gray.png') }}' alt='island icon'></span><a href='/products/"+row.id+"/edit' class='link'>商品一覧</a></div><br class='d-md-none'><div class = 'pr-sm-0 pr-md-5 pt-sm-3'  style = display:inline-block;'><span><img class='icon-support pr-2' style = 'height:25px' src='{{ asset('image/message.png') }}'' alt='island icon'></span><a href='/products/read/"+row.id+"' class='link'>メッセージ</a></div><br class='d-md-none'><div class = 'pt-sm-3 pt-md-3' style = 'display:inline-block;'><span><i class='fa fa-user-circle-o fa-2x pr-2'></i></span><a href='/seller/profile/"+row.id+"/edit' class='link'>プロフィール編集</a></div></div>"; 
                }
            }, 
            { data: 'vendor', 
                name: 'vendor', 
                'width': '20%',
            },
            { data: 'is_active', 
                name:'is_active',
                'width': '20%',
                'render': function (data,type,row) {
                    return data === 1 ? "有効" : "停止";
                }
            },
            { data: 'created_at', 
                'targets': 1,
	            'width': '20%',
                'className': 'dt-body-center',
                'render': function (data,type,row) { 
                    if(!row.updated_at){
                        return data; 
                    }
                    else{
                        return row.updated_at;
                    }                    
                }
            },
        ],
        dom: 'Bpfrtip', 
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
        order: [ [ 4, 'desc' ] ],
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
        //console.log(status);
        var category = $("#search_category").val();
        var rank = $("#search_rank" ).val();
        var island = $("#search_island").val(); 
        var seller = $("#search_seller").val();
        var start_date = $('#startDate').val();
        var end_date = $('#endDate').val(); 
        var salesCheckbox = "";
        per_page = $('#perPage').val();  
        salesCheckbox = $('input[type=checkbox]:checked').map(function(_,el){
                            return $(el).val();
                        }).get();
        table.page.len(per_page).ajax.url(SITEURL+'/localvendor/seller/list?is_active='+status+'&search_keword='+keyword+'&start_date='+start_date+'&end_date='+end_date+'&search_rank='+rank+'&search_island='+island+'&search_category='+category+'&search_seller='+seller+'&salesCheckbox='+salesCheckbox).load();  
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
        rank = ''; 
        category = '';
        island = '';
        seller = '';  
        salesCheckbox = ""; 
        //$("#search_island").val("すべて");
        table.page.len(per_page).ajax.url(SITEURL+'/localvendor/seller/list?is_active='+status+'&search_keword='+keyword+'&start_date='+start_date+'&end_date='+end_date+'&search_rank='+rank+'&search_island='+island+'&search_category='+category+'&search_seller='+seller+'&salesCheckbox='+salesCheckbox).load(); 
        $('#filterForm')[0].reset();
        $('#search_island').select2({
            placeholder: "離島",
            allowClear: true
        });
        $('#search_localvendor').select2({
            placeholder: "地域商社",
            allowClear: true
        });      
    } );
/*****Search form Reset end ******/
});  
/*****Bulk and single delete function start ******/
var deleteUrl = SITEURL + '/seller/delete';
var copyUrl = SITEURL + '/localvendor/copy';
var statusUrl = SITEURL + '/seller/status-change';
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
            status = 0;
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
            type: "POST",
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