@extends('dashboard.layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb d-flex justify-content-between">
        <div class="pull-left">
            <h2 class="title-text">Users Activity Management </h2>
        </div>
    </div>
</div>

<div class="box-shadow shadow">
    <div class="data-table-loader"></div>
    <table id="data-table" class="table table-bordered data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Id</th>
                <th>Log Name</th>
                <th>Description</th>
                <th>Subject Id</th>
                <th>Subject Type</th>
                <th>Created at</th>
                <th width="90px">Action</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>

<!-- Modal -->
<div class="modal fade" id="modal-log" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalScrollableTitle">Modal title</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="user-info">
              <h3 class="user-title"></h3>
              <p class="user-role"></p>
              <p class="user-email"></p>
          </div>
          <div class="content-info">
              <p class="content-model"></p>
              <p class="log-name"></p>
              <p class="created-at"></p>
              <p class="description"></p>
              {{-- <blockquote>
                <pre> --}}
                  <code class="code"></code>
                {{-- </pre>
              </blockquote> --}}
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
</div>
@endsection

@push('custom-scripts')
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
            url: SITEURL + "/dashboard/user/logs",
            type: 'GET'
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'id', name: 'id'},
            {data: 'log_name', name: 'log_name'},
            {data: 'description', name: 'description'},
            {data: 'subject_id', name: 'subject_id'},
            {data: 'subject_type', name: 'subject_type'},
            {data: 'created_at', name: 'created_at'},
            {data: 'action', name: 'action', orderable: false}
        ],
        order: [[0, 'desc']]
    });

    $('body').on('click', '#viewData', function() {
        var dataId = $(this).data("id");
        $.ajax({
                type: "GET",
                url: "{{ route('userLogShow') }}?id=" + dataId,
                success: function (response) {
                    console.log(response);
                    console.log(response.data.properties);
                    $('#modal-log').modal('show');
                    $('.user-title').html(response.user.name);
                    $('.user-role').html(response.role);
                    $('.user-email').html(response.user.email);
                    $('.user-create').html(response.user.created_at);
                    $('.content-model').html(response.data.causer_type);
                    $('.log-name').html(response.data.log_name);
                    $('.created-at').html(response.data.created_at);
                    $('.description').html(response.data.description);
                    $('.code').html(JSON.stringify(response.data.properties));
                },
                error: function (data) {
                    console.log('Error:', data);
                }
            });
    });

    $('body').on('click', '#deleteData', function () {
        var dataId = $(this).data("id");
        var isDelete = confirm("Are you sure want to delete!");

        if(isDelete) {
            $.ajax({
                type: "GET",
                url: "{{ route('userLogDestroy') }}?id=" + dataId,
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