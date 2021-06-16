$(function () {
    $('#csv-type').on('change', function () {
        var type = $(this).val(); // get selected value
        if (type) {
            window.location = "/csvs/settings/" + type;
        }
        return false;
    });
});


$('.csv-action').on('click', function () {
    $('.csv-action').removeClass('active');
    $(this).addClass('active');
});
function moveOptionsRight() {
    const avb = document.getElementById('available_c');
    const sel = document.getElementById('selected_c');
    moveItems(avb, sel);
}

function moveOptionsLeft() {
    const avb = document.getElementById('available_c');
    const sel = document.getElementById('selected_c');
    moveItems(sel, avb);
}

function moveItems(theSelFrom, theSelTo) {
    $(theSelFrom).find('option:selected').detach().prop("selected", false).appendTo($(theSelTo));
    updateCsvSettings();
}

function moveOptionUp() {
    $('#selected_c').find('option:selected').each(function () {
        $(this).prev(':not(:selected)').detach().insertAfter($(this));
    });
    updateCsvSettings(1);
}

function moveOptionTop() {
    $('#selected_c').find('option:selected').detach().prependTo($('#selected_c'));
    updateCsvSettings(1);
}

function moveOptionDown() {
    $($('#selected_c').find('option:selected').get().reverse()).each(function () {
        $(this).next(':not(:selected)').detach().insertBefore($(this));
    });
    updateCsvSettings(1);
}

function moveOptionBottom() {
    $('#selected_c').find('option:selected').detach().appendTo($('#selected_c'));
    updateCsvSettings(1);
}

function moveAllLeft() {
    moveAllItems('#selected_c', '#available_c');
}

function moveAllRight() {
    moveAllItems('#available_c', '#selected_c');
}

function moveAllItems(origin, dest) {
    $(origin).children().appendTo(dest);
    updateCsvSettings();
}


function updateCsvSettings(sortable) {
    if (typeof sortable == 'undefined') {
        sortable = 0;
    }
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    var outList = [];
    var inList = [];
    $("#available_c").find("option").each(function (i) {
        var el = $(this);
        var lObject = {
            'id': el.data('id'),
            'order': el.data('order'),
            'in_output': 0
        };
        outList.push(lObject);
    });
    $("#selected_c").find("option").each(function (i) {
        var el = $(this);
        var rObject = {
            'id': el.data('id'),
            'order': i,
            'in_output': 1
        };
        inList.push(rObject);
    });
    var listData = outList.concat(inList)
    $.post("/csvs/update-settings", { listData: listData, sortable: sortable, type: $("#csv-type").val() }, function (data) {
        if (data.sortable != 1) {
            var inOutput = data.data.in_output;
            var html = "";
            $("#selected_c").empty();
            for (var i = 0; i < inOutput.length; i++) {
                html = '<option data-id="' + inOutput[i].id + '"data-order="' + inOutput[i].order + '" data-inoutput="1" value="' + inOutput[i].column_name + '">' + inOutput[i].column_label + '</option>';
                $("#selected_c").append(html);
            }
        }
    });
}