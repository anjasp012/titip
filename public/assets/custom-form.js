
function reset_button(value = 0) {
    if (value == 0) {
        $('button[type="submit"]').attr('disabled', 'true');
        $('button[type="submit"]').text('');
        $('button[type="submit"]').append('<span class=\"spinner-grow spinner-grow-sm mb-1\"></span> Mohon tunggu...');
        $('button[type="reset"]').hide();
    } else {
        $('button[type="submit"]').removeAttr('disabled');
        $('button[type="submit"]').removeAttr('span');
        $('button[type="submit"]').text('');
        $('button[type="submit"]').append('<i class=\"fa fa-check\"></i> Submit');
        $('button[type="reset"]').show();
    }
}
$(function () {
    $("#main_form").on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: $(this).attr('action'),
            method: $(this).attr('method'),
            data: new FormData(this),
            processData: false,
            dataType: 'json',
            contentType: false,
            beforeSend: function () {
                reset_button(0);
                $(document).find('small.text-danger').text('');
                $(document).find('input').removeClass('is-invalid');
            },
            success: function (data) {
                reset_button(1);
                if (data.status == false) {
                    if (data.type == 'validation') {
                        $.each(data.message, function (prefix, val) {
                            $("input[name=" + prefix + "]").addClass('is-invalid');
                            $('small.' + prefix + '_error').text(val[0]);
                        });
                    }
                    if (data.type == 'alert') {
                        swal.fire("Gagal!", data.message, "error");
                    }
                } else {
                    reset_button(1);
                    swal.fire("Berhasil!", data.message, "success").then(function () {
                        $("#modal").modal('hide');
                        window.LaravelDataTables["data-table"].draw();
                    });
                }
            },
            error: function () {
                swal.fire("Gagal!", "Terjadi kesalahan.", "error");
            },
        });
    });
});
