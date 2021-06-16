@extends('admin.layouts.admin')

@push('custom-style')
<link rel="stylesheet" type="text/css" href="{{ asset('vendor/datatable/datatables.min.css') }}" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/admin/css/user-activities.css') }}">
@endpush

@push('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('users.index') }}">管理 TOP</a></li>
<li class="breadcrumb-item active" aria-current="page">ユーザーログ</li>
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
            <h1>ユーザーログ</h1>
        </div>
        <table id="data-table" class="table data-table display responsive" width="100%">
            <thead>
                <tr>
                    <th width="15%">番号</th>
                    <th width="10%">名前</th>
                    <th width="15%">アクティビティ</th>
                    <th width="10%">説明</th>
                    <th width="10%">IP</th>
                    <th width="15%">ブラウザ</th>
                    <th width="15%">日時</th>
                    <th width="20%">削除</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </section>
</div>

@include('admin.partials.delete_modal')

@endsection

@push('custom-scripts')
<script src="{{ asset('vendor/datatable/datatables.min.js') }}" defer></script>
<script src="{{ asset('js/deleteDataFunction.js') }}"></script>

<script type="text/javascript">
var SITEURL = '{{ URL::to('') }}';
var deleteUrl = SITEURL + '/user/activities';

$(document).ready( function () {
    // text limit function
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
    var table = $('#data-table').DataTable({
        processing: true,
        responsive: true,
        serverSide: true,
        ajax: {
            url: SITEURL + "/user/activities",
            type: 'GET'
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'email', name: 'email'},
            {data: 'action', name: 'action'},
            {data: 'description', name: 'description', orderable: false},
            {data: 'ip', name: 'ip', orderable: false},
            {
                data: 'browser',
                orderable: false,
                render: function (data) {
                    return text_truncate(data, 30);
                }
            },
            {data: 'created_at', name: 'created_at', orderable: false},
            {data: 'action-btn', name: 'action-btn', orderable: false }
        ],
        order: [[0, 'desc']],
        "language": {
            "lengthMenu": "公開 _MENU_ エントリー",
            "zeroRecords": no_data_found,
            "info": "表示中のページ _PAGE_ of _PAGES_",
            "infoEmpty": "利用可能な記録はありません",
            "sSearch": "検索:",
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
        $('#delete-modal').modal('show');
        $('#modal-delete-button').attr('onclick', 'deleteData(' + dataId + ',true)');
    });

});
</script>
@endpush