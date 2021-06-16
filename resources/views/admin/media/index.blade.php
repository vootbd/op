@extends('admin.layouts.admin')

@push('custom-style')
<link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin/css/media-index.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/bs-v4-datepicker/gijgo.min.css') }}">
@endpush

@section('content')
<div class="inner-content">
    @push('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('seller.list') }}">TOP</a></li>
    <li class="breadcrumb-item" aria-current="page"><a href="{{ route('medias.index') }}">メディア一覧</a></li> 
    @endpush

    <section id="scrollTop" class="box-content">       
        <div class="d-flex align-items-center page-title">
            <h1>メディア検索</h1>
        </div>
        {!! Form::open(array('class'=> 'form-block', 'method'=>'POST', 'enctype' => 'multipart/form-data','id'=> 'filterForm')) !!}
            @csrf
             <div class="form-content">
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('keyword', 'キーワード', ['class' => 'col-form-label']) !!}
                    </div>
                    <div class="field-section">
                        {!! Form::text('keyword',  null, ['class' => 'keyword', 'id' => 'search_keword']) !!}
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

    <section id="scrollTop" class="box-content mt-20">
        @if ($message = Session::get('message'))
        <div class="custom-alert success">
            <span class="rito rito-check"></span>
            <p>{{ $message }}</p>
        </div>
        @endif
        <div class="box-inner-content">
            <div class="search-content">
            </div>
            <div class="filter-content">
                <div class="filter">
                </div>
                <h2 class="top-info"> <span class="total-items-top"></span></h2>
                <div class="batch-operation">
                    {!! Form::select('bulk_delete', ['' => '一括操作', '1' => '削除'], null, ['class' => 'custom-select','id' => 'batch_name_for_delete']) !!}
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

                            <th>
                                <div class="d-flex link">タイトル
                                    <div class="sorting-caret">
                                        <span class="fa fa-caret-up"></span>
                                        <span class="fa fa-caret-down"></span>
                                    </div>
                                </div>
                            </th>
                            <th>
                                <div class="d-flex link">URL
                                    <div class="sorting-caret">
                                        <span class="fa fa-caret-up"></span>
                                        <span class="fa fa-caret-down"></span>
                                    </div>
                                </div>
                            </th>
                            <th>
                                <div class="d-flex link">ユーザー
                                    <div class="sorting-caret">
                                        <span class="fa fa-caret-up"></span>
                                        <span class="fa fa-caret-down"></span>
                                    </div>
                                </div>
                            </th>
                            <th>
                                <div class="d-flex link">日付
                                    <div class="sorting-caret">
                                        <span class="fa fa-caret-up"></span>
                                        <span class="fa fa-caret-down"></span>
                                    </div>
                                </div>
                            </th>
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

    function caretDisplyOnSorting(settings) {
        var sortingTh = settings.aaSorting[0]
        var th = $('th')
        $.each(th, function(i, val) {
            $(val).find('.fa-caret-down').css('opacity', '1');
            $(val).find('.fa-caret-up').css('opacity', '1');
        });
        if (sortingTh['_idx']) {
            $(th[sortingTh[0]]).find('.fa-caret-up').css('opacity', '0');
        } else {
            $(th[sortingTh[0]]).find('.fa-caret-down').css('opacity', '0');
        }
    }
    $(document).ready(function() {
        // Language var start
        var no_data_found = 'データが見つかりませんでした。';
        // Language var end
        var ASSET_URL = "{{ asset('upload/medias/sm/') }}/";
        var ASSET_URL_SVG = "{{ asset('upload/medias/') }}/";
        var default_image = "{{ asset('image/images-icon.svg') }}";
        var pdf_image = "{{ asset('image/pdf.jpg') }}";



        /*********************** User index page data showing start ***********************/
        $('.dropdown-icon').click(function() {
            $('.menu-dropdown').toggleClass('show')
        })
        $('body').click(function(event) {
            if (event.target.id != 'dropdown-icon' && $('.menu-dropdown').hasClass('show')) {
                $('.menu-dropdown').removeClass('show')
            }
            if (event.target.type == "checkbox") {
                var checkbox = $('.delete-checked');
                var flag = true;
                $.each(checkbox, function(i, val) {
                    if ($(val).prop('checked') == false) {
                        flag = false;
                    }
                })
                flag = checkbox.length == 0 ? false : flag;
                $('#gridCheck1').prop('checked', flag);
            }
        })

        var deleteUrl = SITEURL + '/media';
        var text_truncate = function(str, length, ending) {
            if (length == null) {
                length = 100;
            }
            if (ending == null) {
                ending = '...';
            }
            if (str.length > length) {
                return str.substring(0, length - ending.length) + ending;
            } else {
                return str;
            }
        };
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        table = $('#data-table').DataTable({
            processing: true,
            responsive: true,
            serverSide: true,
            ajax: {
                url: SITEURL + "/medias",
                type: 'GET',
                statusCode: {
                    401: function() {
                        location.reload()
                    },
                    419: function() {
                        location.reload()
                    }
                }
            },
            columns: [{
                    data: 'id',
                    'targets': 0,
                    'searchable': false,
                    'orderable': false,
                    'className': 'dt-body-center',
                    'render': function(data) {
                        return '<div class="custom-control custom-checkbox d-flex align-items-center"><input type="checkbox" class="custom-control-input delete-checked" id="check_' + data + '" name="delete_check[]" value="' + data + '"><label for="check_' + data + '" class="custom-control-label publish-label"></label>';
                    }
                },
                {
                    data: 'display_name',
                    'targets': 1,
                    'className': 'dt-body-center images-section',
                    'render': function(data, type, row) {
                        if (data != '') {
                            if (row.extention == 'pdf') {
                                return '<div><img src="' + pdf_image + '"/></div><a href="medias/' + row.id + '/edit" class="link">' + row.display_name + '</a>';
                            }                            
                            else if (row.extention == 'svg') {
                                return '<div><img src="' + ASSET_URL_SVG + row.original_name + '"/></div><a href="medias/' + row.id + '/edit" class="link">' + row.display_name + '</a>';
                            } 
                            else {
                                return '<div><img src="' + ASSET_URL + row.original_name + '"/></div><a href="medias/' + row.id + '/edit" class="link">' + row.display_name + '</a>';
                            }
                        } else {
                            if (row.extention == 'pdf') {
                                return '<div><img src="' + pdf_image + '"/></div><a href="medias/' + row.id + '/edit" class="link">' + row.display_name + '</a>';
                            }
                            else {
                                return '<div><img src="' + default_image + '"/></div><a href="medias/' + row.id + '/edit" class="link">' + row.display_name + '</a>';
                            }
                        }
                    }
                },
                {
                    data: 'url',
                    'targets': 2,
                    'className': 'copy-block',
                    'render': function(data, type, row) {
                        return '<span class="d-none" id="copy-url-el-'+row.id+'">'+data +'</span><img src="https://rito-portal-public-stg.s3-ap-northeast-1.amazonaws.com/public/image/copy.svg" style="width: 24px; cursor: pointer;" onclick="copyToClipboard('+"'#copy-url-el-"+row.id+"'"+')" />';
                    }
                },
                {
                    data: 'user_name',
                    name: 'user_name'
                },
                {
                    data: 'updated_at',
                    name: 'updated_at'
                },
            ],
            order: [
                [0, 'desc']
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
                "infoFiltered": "(からフィルタリング _MAX_ 総記録)",
                "oPaginate": {
                    "sFirst": "<i class='fa fa-angle-double-left'></i> 最初",
                    "sLast": "最終 <i class='fa fa-angle-double-right'></i>",
                    "sNext": "次 <i class='fa fa-angle-right'></i>",
                    "sPrevious": "<i class='fa fa-angle-left'></i> 前"
                }
            },
            "fnDrawCallback": function(oSettings) {
                totalItemsShow(table.page.info()['recordsDisplay'],oSettings.json.from,oSettings.json.to)
                caretDisplyOnSorting(oSettings)
                $($('th')[0]).css('width', '1px');
                $("#gridCheck1").prop("checked", false);
                $('.dataTables_paginate > .pagination').addClass('dt-pagination-sm');
            }
        });


        //coustome search input
        $('#search_click').on('click', function() {
            var input_search_data = $("#search_keword").val();
            table.search(input_search_data).draw();
        });

        $('#gridCheck1').on('click', function() {
            var rows = table.rows({
                'search': 'applied'
            }).nodes();
            $('input[type="checkbox"]', rows).prop('checked', this.checked);
        });
    });
    /***********coustom search input start ************/
    $('#page_search').on( 'click', function () {
        event.preventDefault() 
        var keyword = $("#search_keword").val();
        var start_date = $('#startDate').val();
        var end_date = $('#endDate').val();
        per_page = $('#perPage').val(); 
        table.page.len(per_page).ajax.url(SITEURL+'/medias?search_keword='+keyword+'&start_date='+start_date+'&end_date='+end_date+'&per_page='+per_page).load(); 
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
        table.page.len(per_page).ajax.url(SITEURL+'/medias?search_keword='+keyword+'&start_date='+start_date+'&end_date='+end_date+'&per_page='+per_page).load(); 
        $('#filterForm')[0].reset();    
    } );
/*****Search form Reset end ******/
 

    //Text change for dropdown menu
    function menuClick(link) {
        event.preventDefault()
        var result = 'ステータスが' + $(link).html()
        var status = link.getAttribute('data-value');
        table.ajax.url(SITEURL + '/medias?is_active=' + status).load();
        $('.highlight').html(result)
        $('.menu-dropdown').toggleClass('show')
    }

    /*****Bulk and single delete function start ******/
    var deleteUrl = SITEURL + '/medias';
    var user_check_id = [];


    function btnDeleteData() {
        //Batch data pickup
        var batch_name_for_delet = $("#batch_name_for_delete").val();

        //check for batch select or no
        if (batch_name_for_delet == '1') {
            $(':checkbox:checked').each(function(i) {
                user_check_id[i] = $(this).val();
            });
            if (user_check_id.length > 0) {
                $('#delete-modal').modal('show');
                $('#modal-delete-button').attr('onclick', 'deleteData()');
            } else {
                alert("行が選択されていません");
            }
        } else {
            alert("バッチを選択してから削除してください");
        }
    }
    //datepicker
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
    
    //Bulk delete ajax call in controller
    function deleteData() {
        if (user_check_id.length > 0) {
            $.ajax({
                type: "DELETE",
                url: deleteUrl + "/" + user_check_id,
                success: function(data) {
                    console.log(data);
                    var oTable = $('#data-table').dataTable();
                    oTable.fnDraw(false);
                    $('#delete-modal').modal('hide');
                },
                error: function(data) {
                    if (data.status == 419) {
                        location.reload()
                    }
                    console.log('Error:', data);
                }
            });
        }
    }

    /*****Bulk and single delete function end ******/

    function copyToClipboard(element) {
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val($(element).text()).select();
        document.execCommand("copy");
        alert("Copied: "+$(element).text());
        $temp.remove();
    }
</script>
@endpush