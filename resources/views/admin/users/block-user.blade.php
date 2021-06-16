@extends('admin.layouts.admin')

@push('custom-style')
<link rel="stylesheet" type="text/css" href="{{ asset('vendor/datatable/datatables.min.css') }}" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/admin/css/block-user.css') }}">
@endpush

@push('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('users.index') }}">管理 TOP</a></li>
<li class="breadcrumb-item active" aria-current="page">アカウントのロック解除</li>
@endpush

@section('content')
<div class="inner-content">
    <section class="box-content">
        <div class="d-flex align-items-center page-title">
            <h1>アカウントのロック解除</h1>
        </div>
        <table id="data-table" class="table data-table display responsive" width="100%">
            <thead>
                <tr>
                    <th width="20%">シリアルナンバー</th>
                    <th width="20%">名前</th>
                    <th width="25%">メールアドレス</th>
                    <th width="20%">日時</th>
                    <th width="20%">削除</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </section>
</div>
@endsection
@push('custom-scripts')
<script src="{{ asset('vendor/datatable/datatables.min.js') }}" defer></script>
<script type="text/javascript">
var SITEURL = '{{ URL::to('') }}';

$(document).ready( function () {
    $(".data-table-loader").fadeOut();
    $("#data-table thead").fadeIn('slow');
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    var table = $('#data-table').DataTable({
        processing: true,
        responsive: true,
        serverSide: true,
        ajax: {
            url: SITEURL + "/block/user/list",
            type: 'GET'
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false},
            {data: 'name', name: 'name', orderable: false},
            {data: 'email', name: 'email'},
            {data: 'created_at', name: 'created_at',  orderable: false},
            {data: 'action-btn', name: 'action-btn', orderable: false}
        ],
        order: [[0, 'desc']],
        "language": {
            "lengthMenu": "公開 _MENU_ エントリー",
            "zeroRecords": no_data_found,
            "info": "表示中のページ _PAGE_ of _PAGES_",
            "infoEmpty": "利用可能な記録はありません",
            "sSearch": "探す:",
            "sProcessing":    "処理...",
            "sLoadingRecords": "レコードの読み込み...",
            "infoFiltered": "(からフィルタリング _MAX_ 総記録)",
            "oPaginate": {
                "sFirst":    "最初",
                "sLast":    "最終",
                "sNext":    "次",
                "sPrevious": "前"
            }
        }
    });

    $('body').on('click', '#deleteData', function () {
        var dataId = $(this).data("id");
        var isDelete = confirm("Are you sure want to delete!");

        if(isDelete) {
            $.ajax({
                type: "DELETE",
                url: "{{route('unblock.user')}}",
                data:{
                    "email":dataId
                },
                success: function (data) {
                    var oTable = $('#data-table').dataTable();
                    oTable.fnDraw(false);
                },
                error: function (data) {
                    console.log('Error:', data);
                }
            });
        }
    });

});

</script>
@endpush