@extends('admin.layouts.admin')

@push('custom-style')
<link rel="stylesheet" href="{{ asset('css/admin/css/island-index.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/bs-v4-datepicker/gijgo.min.css') }}">
@endpush

@section('content')
<div class="inner-content">
    @push('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('seller.list') }}">TOP</a></li>
    <li class="breadcrumb-item active" aria-current="page"><a href="{{ route('islands.index')}}">離島一覧</a></li> 
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
    <div class="custom-alert alert" id="delete-message" style="display: none;"> 
        <p></p>
    </div> 
    <section id="scrollTop" class="box-content">       
        <div class="d-flex align-items-center page-title">
            <h1>離島検索</h1>
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
                        {!! Form::label('prefecture', '都道府県', ['class' => 'col-form-label']) !!}
                    </div>
                    <div class="field-section">
                        {!! Form::select('prefecture', $prefectures, null, ['placeholder' => 'すべて', 'class' => 'custom-select status' , 'id' => 'search_prefecture']) !!}
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
                <button type="button" class="btn btn-submit" id="island_search">検索</button>
            </div>
            <div class="d-flex justify-content-center csv-btn-section">
                <a class="btn btn-download" href="{{ route('csv.island-export')}}"><i class="rito rito-download"></i>CSV ダウンロード </a>
                <a class="btn btn-setting" href="{{ route('csv.control','remote_island')}}"><i class="rito rito-settings"></i>CSV 出力項目設定</a>
            </div>
        {!! Form::close() !!}
    </section>
    <hr>
    <section class="box-content">
        <div class="box-inner-content"> 
            <div class="filter-content"> 
                <h2 class="top-info"> <span class="total-items-top"></span></h2>
                <div class="batch-operation">                    
                    {!! Form::select('bulk_delete', ['' => '一括操作', '1' => '削除' ], null, ['class' => 'custom-select','id' => 'batch_name_for_delete']) !!}
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
                            <th style="margin-right:-20px;"><div class="d-flex link">離島名<div class="sorting-caret"><span class="fa fa-caret-up"></span><span class="fa fa-caret-down"></span></div></div></th> 
                            <th><div class="d-flex">都道府県<div class="sorting-caret"><span class="fa fa-caret-up"></span><span class="fa fa-caret-down"></span></div></div></th> 
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
    var deleteUrl = SITEURL + '/islands';
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
            url: SITEURL + '/islands',
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
                'className': 'dt-body-center',
                'width': '60%',
                name: 'name',
                'render': function (data,type,row) {
                    return "<a href='islands/"+row.id+"/edit' class='link'>"+data+"</a>";
                }
            }, 
            { data: 'prefecture_name', name: 'prefecture_name','width': '40%' }, 
            
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
        order: [ [ 2, 'desc' ] ],
        "fnDrawCallback": function (oSettings) { 
            totalItemsShow(table.page.info()['recordsDisplay'],oSettings.json.from,oSettings.json.to)
            caretDisplyOnSorting(oSettings)
            $($('th')[0]).css('width','1px')
            $("#gridCheck1"). prop("checked", false);
            $('.dataTables_paginate > .pagination').addClass('dt-pagination-sm'); 
        }
    }); 
    
    /***********coustom search input start ************/
    $('#island_search').on( 'click', function () {
        
        event.preventDefault() 
        var keyword = $("#search_keword").val();
        var prefecture = $("#search_prefecture").val(); 
        per_page = $('#perPage').val();   
        table.page.len(per_page).ajax.url(SITEURL+'/islands?search_prefecture='+prefecture+'&search_keword='+keyword).load();  
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
        keyword = '';
        per_page = 10; 
        order_by = 'desc';
        prefecture = ''; 
        table.page.len(per_page).ajax.url(SITEURL+'/islands?search_prefecture='+prefecture+'&search_keword='+keyword).load();  
        $('#filterForm')[0].reset();    
    } );
    /*****Search form Reset end ******/
});  

/*****Bulk and single delete function start ******/
var deleteUrl = SITEURL + '/islands';  
var user_check_id = [];
var opt_group =[];
function btnDeleteData() {
    user_check_id = [];
    var status = [];
    opt_group =[];
    //Batch data pickup
    var batch_name_for_delet = $("#batch_name_for_delete").val();  

    //check for batch select or no 
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
    else {
        alert("バッチを選択してから削除してください");
    } 
}  

//Bulk delete ajax call in controller
function deleteData(){
    if(user_check_id.length>0){ 
        $.ajax({
            type: "DELETE",
            url: deleteUrl + "/" + user_check_id,
            success: function (data) {
                $('#delete-message').css('display', 'flex');
                $("#delete-message p").html(data.message);
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

</script>
@endpush