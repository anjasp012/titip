/**
   * Easy selector helper function
   */
const select = (el, all = false) => {
    el = el.trim()
    if (all) {
        return [...document.querySelectorAll(el)]
    } else {
        return document.querySelector(el)
    }
}

/**
 * Easy event listener function
 */
const on = (type, el, listener, all = false) => {
    if (all) {
        select(el, all).forEach(e => e.addEventListener(type, listener))
    } else {
        select(el, all).addEventListener(type, listener)
    }
}

if (select('.search-bar-toggle')) {
    on('click', '.search-bar-toggle', function (e) {
        select('.search-bar').classList.toggle('search-bar-show')
    })
}
function reset_button(value = 0) {
    if (value == 0) {
        $('.btnsubmit').attr('disabled', 'true');
        $('.btnsubmit').text('');
        $('.btnsubmit').append('<span class=\"spinner-grow spinner-grow-sm mb-1\"></span> Mohon tunggu...');
        $('button[type="reset"]').hide();
    } else {
        $('.btnsubmit').removeAttr('disabled');
        $('.btnsubmit').removeAttr('span');
        $('.btnsubmit').text('');
        $('.btnsubmit').append('<i class=\"fa fa-check\"></i> Submit');
        $('button[type="reset"]').show();
    }
}

function modal(type, title = 'Data', url) {
    $('#modal').modal('show');
    if (type == 'add') {
        $('#modal-title').html('<i class="fa fa-plus-square"></i> Tambah ' + title + '');
    } else if (type == 'send') {
        $('#modal-title').html('<i class="fa fa-plus-square"></i> Kirim ' + title + '');
    } else if (type == 'edit') {
        $('#modal-title').html('<i class="fa fa-edit"></i> Edit ' + title + '');
    } else if (type == 'reply') {
        $('#modal-title').html('<i class="fa fa-edit"></i> Balas ' + title + '');
    } else if (type == 'delete') {
        $('#modal-title').html('<i class="fa fa-trash"></i> Delete ' + title + '');
    } else if (type == 'detail') {
        $('#modal-title').html('<i class="fa fa-search"></i> Detail ' + title + '');
    } else if (type == 'filter') {
        $('#modal-title').html('<i class="fa fa-filter"></i> Filter ' + title + '');
    } else if (type == 'confirm') {
        $('#modal-title').html('<i class="fa fa-check"></i> Confirm ' + title + '');
    } else {
        $('#modal-title').html('Empty');
    }
    if (type == 'add' || type == 'edit' || type == 'reply' || type == 'send') {
        $('#modal-footer').addClass('hidden');
    } else {
        $('#modal-footer').removeClass('hidden');
    }
    $.ajax({
        type: "GET",
        url: url,
        beforeSend: function () {
            $('#modal-detail-body').html('<div class="text-center">Loading...</div>');
        },
        success: function (result) {
            $('#modal-detail-body').html(result);
        },
        error: function () {
            $('#modal').modal('hide');
            swal.fire("Gagal!", "Terjadi kesalahan.", "error");
        }
    });
    $('#modal-detail').modal();
}
