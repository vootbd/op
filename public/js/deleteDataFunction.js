function deleteData(dataId, isDataTable) {
    $.ajax({
        type: "DELETE",
        url: deleteUrl + "/" + dataId,
        success: function (data) {
            if(isDataTable) {
                var oTable = $('#data-table').dataTable();
                oTable.fnDraw(false);
            } else {
                $('#id-' + dataId).fadeOut();
            }
            $('#delete-modal').modal('hide');
            if (typeof getPagination === "function") {
                getPagination(current_page);
            }
        },
        error: function (data) {
            console.log('Error:', data);
        }
    });
}