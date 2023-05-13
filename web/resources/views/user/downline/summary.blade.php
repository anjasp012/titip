@extends('user.layouts.app')
@section('breadcrumb-first', $breadcrumb->first)
@section('breadcrumb-second', $breadcrumb->second)
@section('content')
<div class="row">
    <div class="col-lg-6">
        <div class="card card-white m-b-30">
            <div class="card-body pt-3">
                <div class="float-left">
                    <span class="label label-warning mb-3">Referral</span>
                    <h5 class="">{{ number_format($referral_code_used->count('*'),0,',','.') }} Kali</h5>
                </div>
                <div class="float-right">
                    <i class="fa fa-user icon"></i>
                </div>
                <div class="clearfix"></div>
                <div class="">
                    <p class="mb-1 text-muted text-truncate">Total Kode Referral digunakan</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6 grid-margin">
        <div class="card card-white stats-widget card m-b-30">
            <div class="card-body pt-3">
                <div class="float-left">
                    <span class="label label-warning mb-3">Bonus</span>
                    <h5 class="stats-number">{{ number_format(Auth::user()->bonus_rp,0,',','.') }} </h5>
                </div>
                <div class="float-right">
                    <i class="fa fa-coins icon"></i>
                </div>
                <div class="clearfix"></div>
                <div class="mt-0">
                    <p class="mb-0 text-muted text-truncate">Total Bonus diterima</p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-12 grid-margin">
        <div class="card card-white stats-widget card m-b-30">
            <div class="card-body pt-3">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="card-title">Advertisement material</h5>
                    <select id="combo" class="form-control">
                        <option value="{{url('banner/1.png')}}" selected="selected">Banner 1</option>
                        <option value="{{url('banner/2.png')}}">Banner 2</option>
                        <option value="{{url('banner/3.png')}}">Banner 3</option>
                        <option value="{{url('banner/4.png')}}">Banner 4</option>
                        <option value="{{url('banner/5.png')}}">Banner 5</option>
                    </select>
                    <hr>
                    <p class="mb-0 text-muted text-truncate">HTML code for websites</p>
                    <textarea id="refhtml" class="ipinput form-control" onclick="this.select();" readonly="readonly" spellcheck="false">&lt;a href="{{ url('ref').'/'.Auth::user()->referral_code }}" alt="Banner"&gt;&lt;img src="{{url('banner/1.png')}}"&gt;&lt;/a&gt;
                    </textarea>
                </div>
                <div class="col-md-6">
                    <h3>Preview</h3>
                    <img id="refimg" src="{{url('banner/1.png')}}" alt="Referral banner" class="img-fluid">
                </div>
            </div>
            </div>
        </div>
    </div>
	<div class="col-lg-12">
        <div class="alert alert-dismissable alert-info text-dark">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <b><i class="fa fa-info-circle"></i> Informasi:</b> Ajak orang sekitar anda mendaftar dengan menggunakan Kode Referral anda untuk mendapatkan Bonus Poin Pendafaran.
        </div>
        <div class="card">
            <div class="card-body pt-3">
                <div class="input-group">
                    <input type="text" class="form-control" value="{{ url('ref').'/'.Auth::user()->referral_code }}" id="referral_code" readonly>
                    <div class="input-group-append">
                        <button class="btn btn-dark" type="button" onclick="copy('referral_code')"><i class="fa fa-copy fa-fw"></i> Salin</button>
                    </div>
                </div>
			</div>
		</div>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Nominal Penarikan</h5>
                <div class="alert alert-warning">
                    Penarikan Minimum Rp.200.000, setiap penarikan akan dikenakan biaya admin sebesar Rp.10.000. setiap penarikan akan di proses 2x24 JAM
                </div>
                <form method="post" action="{{ request()->url() }}" id="main_form">
                    @csrf
                <input type="text" class="form-control autonumeric-currency" name="nominal" id="nominal">
                
                <div class="my-3">
                    <b class="text-dark">Bank</b>
                </div>
                <select class="form-control" name="bank">
                    <option value="BCA">BCA</option>
                    <option value="BRI">BRI</option>
                    <option value="MANDIRI">MANDIRI</option>
                    <option value="BNI">BNI</option>
                    <option value="BTN">BTN</option>
                    <option value="CIBC">CIBC</option>
                    <option value="NISP">NISP</option>
                    <option value="PANIN">PANIN</option>
                </select>
                <div class="row">
                    <div class="col-md-6">
                        <div class="my-3">
                            <b class="text-dark">No Rekening</b>
                        </div>
                        <input type="text" name="rekening" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <div class="my-3">
                            <b class="text-dark">Nama Rekening</b>
                        </div>
                        <input type="text" name="nama" class="form-control">
                    </div>
                </div>
               

                <hr>
                <button type="submit" class="btn btn-primary btn-sm btn-block">Proses</button>
                </form>

            </div>
        </div><!-- End Default Card -->
	</div>
	<div class="col-lg-12">
        <div class="card">
            <div class="card-body pt-3">
                <div class="table-responsives">
                    {!! $dataTable->table(['class' => 'table table-borderless table-hover mb-0'], false) !!}
                </div>
			</div>
		</div>
	</div>
</div>
{!! $dataTable->scripts() !!}
<script>
    var table = document.getElementById("data-table");
    table.children[0].className = "thead-light";
    $('#search-form').on('submit', function(e) {
        window.LaravelDataTables["data-table"].draw();
        e.preventDefault();
    });

    $(document).ready(function() {
		
	     
	
	function numeric(string) {
		return string.trim().replace('Rp ', '').split('.').join('');
	}
	$('.autonumeric-currency').autoNumeric('init', {
		mDec  : '0',
		aSep  : '.',
		aDec  : ',',
		aSign : 'Rp ',
	});
	$('.autonumeric-quantity').autoNumeric('init', {
		mDec  : '0',
		aSep  : '.',
		aDec  : ',',
		aSign : '# ',
	});
	$("#nominal").keyup(function() { 
		$("#amount").val(numeric($(this).val()));
	});
});

$(function() {
    function reset_button(value = 0) {
        if (value == 0) {
            $('button[type="submit"]').attr('disabled', 'true');
            $('button[type="submit"]').text('');
            $('button[type="submit"]').append('<span class=\"spinner-grow spinner-grow-sm mb-1\"></span> Mohon tunggu...');
        } else {
            $('button[type="submit"]').removeAttr('disabled');
            $('button[type="submit"]').removeAttr('span');
            $('button[type="submit"]').text('Submit');
        }
    }
    $("#main_form").on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: $(this).attr('action'),
            method: $(this).attr('method'),
            data: new FormData(this),
            processData: false,
            dataType: 'json',
            contentType: false,
            beforeSend: function() {
                reset_button(0);
                $(document).find('small.text-danger').text('');
                $(document).find('input').removeClass('is-invalid');
            },
            success: function(data) {
                reset_buttons(1);
                if (data.status == false) {
                    if (data.type == 'validation') {
                        swal.fire("Gagal!", "Silahkan Isi Data Dengan Benar", "error");
                    }
                    if (data.type == 'alert') {
                        swal.fire("Gagal!", data.message, "error");
                    }
                } else {
                    swal.fire("Berhasil!", data.message, "success").then(function () {
                        window.location.reload();
                    });
                    
                }
                reset_buttons(1);
            },
            error:function() {
                swal.fire("Gagal!", "Terjadi kesalahan..", "error");
                reset_buttons(1);
            },
        });
        e.preventDefault();
    });
});

	function reset_buttons(value = 0) {
		if (value == 0) {
			$('button[type="submit"]').attr('disabled', 'true');
			$('button[type="submit"]').text('');
			$('button[type="submit"]').append('<span class=\"spinner-grow spinner-grow-sm mb-1\"></span> Mohon tunggu...');
			$('button[type="reset"]').hide();
		} else {
			$('button[type="submit"]').removeAttr('disabled');
			$('button[type="submit"]').removeAttr('span');
			$('button[type="submit"]').text('Submit');
			$('button[type="reset"]').show();
		}
	}
    
	function copy(id) {
		var copyText = document.getElementById(id);
		copyText.select();
		copyText.setSelectionRange(0, 99999); /* For mobile devices */
		document.execCommand("copy");
		swal.fire("Disalin!", "'"+copyText.value+"'.", "success");
	} 

    $("#combo").change(function(){
        var valdata = this.value;
          $("#refhtml").text('<a href="{{ url('ref').'/'.Auth::user()->referral_code }}" target="_blank"><img src="'+valdata+'"></a>');
          $('#refimg').attr('src',valdata);
          $('#refdirect').text(valdata);
    });
</script>
@endsection
