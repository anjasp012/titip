@extends('admin.layouts.app')
@section('breadcrumb-first', $breadcrumb->first)
@section('breadcrumb-second', $breadcrumb->second)
@section('content')
@if ($errors->any() == true)
<div class="row">
	<div class="col-lg-12">
		<div class="alert alert-dismissable alert-danger text-dark">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <b><i class="fa fa-times-circle"></i> Gagal:</b><br />
            @foreach ($errors->all() as $error)
                {!! $error.'<br />' !!}
            @endforeach
            </ul>
		</div>
	</div>
</div>
@endif
<div class="row">
	<div class="col-lg-3">
		<div class="card m-b-30">
			<div class="card-body">
				<div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
					<a class="nav-link active" href="#main" data-toggle="pill" role="tab"><i class="fa fa-home fa-fw"></i> Utama</a>
					{{--<a class="nav-link" href="#socials" data-toggle="pill" role="tab"><i class="fa fa-globe fa-fw"></i> Media Sosial</a>
					<a class="nav-link" href="#smtp" data-toggle="pill" role="tab">{!! website_config('template')->number == 'custom-template' ? '<i class="mdi mdi-email fa-fw"></i>' : '<i class="mdi mdi-email-edit fa-fw"></i>' !!} SMTP</a>
					<a class="nav-link" href="#notifications" data-toggle="pill" role="tab"><i class="fa fa-bell fa-fw"></i> Notifikasi</a>
					<a class="nav-link" href="#template" data-toggle="pill" role="tab"><i class="fa fa-desktop fa-fw"></i> Tampilan</a>
					<a class="nav-link" href="#product" data-toggle="pill" role="tab"><i class="fa fa-tags fa-fw"></i> Produk</a>
					<a class="nav-link" href="#banner" data-toggle="pill" role="tab"><i class="fa fa-file fa-fw"></i> Banner</a>
                    <a class="nav-link" href="#others" data-toggle="pill" role="tab"><i class="fa fa-folder fa-fw"></i> Lain-Lain</a>--}}

				</div>
			</div>
		</div>
    </div>
    <div class="col-lg-9">
        <div class="card m-b-30">
            <div class="card-body">
				<form method="post" action="{{ request()->url() }}" role="form" enctype="multipart/form-data">
                    @method('patch')
                    @csrf
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="main">
                            <div class="form-group">
                            	<label>Nama Website <text class="text-danger">*</text></label>
                                <input type="text" name="website_name" class="form-control" value="{{ old('website_name') ?? website_config('main')->website_name }}" />
                            </div>
                            <hr />
                            {{--
                            <div class="form-group">
                                <label>Logo Website</label>
                                <div class="form-group">
                                    <input type="file" class="dropify" accept=".png, .jpg, .jpeg, .svg" name="website_logo" id="website_logo" data-max-file-size="10M" @if (website_config('main')->website_logo <> null) data-default-file="{{ website_config('main')->website_logo }}" data-show-remove="false" @endif data-toggle="tooltip" title="Ukuran yang Disarankan: 150px x 34px" />
                                </div>
                                @if (website_config('main')->website_logo <> null)
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control" value="{{ website_config('main')->website_logo }}" disabled>
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            <a href="{{ url('admin/settings/website_configs/delete_logo') }}" class="text-dark">
                                                Hapus Logo
                                            </a>
                                        </span>
                                    </div>
                                </div>
                                @endif          
                            </div>
                            <hr />
                            <div class="form-group">
                                <label>Favicon Website</label>
                                <div class="form-group">
                                    <input type="file" class="dropify" accept=".png, .jpg, .jpeg, .svg" name="website_favicon" id="website_favicon" data-max-file-size="10M" @if (website_config('main')->website_favicon <> null) data-default-file="{{ website_config('main')->website_favicon }}" data-show-remove="false" @endif data-toggle="tooltip" title="Ukuran yang Disarankan: 150px x 34px" />
                                </div>
                                @if (website_config('main')->website_favicon <> null)
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control" value="{{ website_config('main')->website_favicon }}" disabled>
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            <a href="{{ url('admin/settings/website_configs/delete_favicon') }}" class="text-dark">
                                                Hapus Favicon
                                            </a>
                                        </span>
                                    </div>
                                </div>
                                @endif          
                            </div>
                            <hr />--}}
                            <div class="form-group">
								<label>Tentang Kami <text class="text-danger">*</text></label>
								<textarea class="form-control custom-text-editor" name="about_us" rows="5">{{ old('about_us') ?? website_config('main')->about_us }}</textarea>
							</div>
                            <hr />
                            <div class="form-group">
								<label>Meta Keywords <text class="text-danger">*</text></label>
								<textarea class="form-control" name="meta_keywords" rows="5">{{ old('meta_keywords') ?? website_config('main')->meta_keywords }}</textarea>
							</div>
                            <hr />
                            <div class="form-group">
								<label>Meta Description <text class="text-danger">*</text></label>
								<textarea class="form-control" name="meta_description" rows="5">{{ old('meta_description') ?? website_config('main')->meta_description }}</textarea>
							</div>
                        </div>
                        <div class="tab-pane fade" id="socials">
                            <p class="text-muted">Tautan media sosial yang akan ditampilkan di footer website. Masukkan hanya id / nama halaman</p>
							<div class="form-group">
								<label>Facebook</label>
								<input type="text" name="socials_facebook" class="form-control" value="{{ old('socials_facebook') ?? website_config('socials')->facebook }}" />
							</div>
							<div class="form-group">
								<label>Whatsapp</label>
								<input type="text" name="socials_whatsapp" class="form-control" value="{{ old('socials_whatsapp') ?? website_config('socials')->whatsapp }}" />
							</div>
							<div class="form-group">
								<label>Telegram</label>
								<input type="text" name="socials_telegram" class="form-control" value="{{ old('socials_telegram') ?? website_config('socials')->telegram }}" />
							</div>
							<div class="form-group">
								<label>Instagram</label>
								<input type="text" name="socials_instagram" class="form-control" value="{{ old('socials_instagram') ?? website_config('socials')->instagram }}" />
							</div>
							<div class="form-group">
								<label>Twitter</label>
								<input type="text" name="socials_twitter" class="form-control" value="{{ old('socials_twitter') ?? website_config('socials')->twitter }}" />
							</div>
                        </div>
                        <div class="tab-pane fade" id="smtp">
                            <div class="form-group">
                                <label>Host</label>
                                <input type="text" name="smtp_host" class="form-control" value="{{ old('smtp_host') ?? website_config('smtp')->host }}" />
                            </div>
                            <div class="form-group">
                                <label>Email Dari</label>
                                <input type="email" name="smtp_from" class="form-control" value="{{ old('smtp_from') ?? website_config('smtp')->from }}" />
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Enkripsi</label>
                                        <select name="smtp_encryption" class="form-control">
                                            <option value="0" @if (website_config('smtp')->encryption == null) selected="selected" @endif>Tidak ada</option>
                                            <option value="ssl" @if (website_config('smtp')->encryption == 'ssl') selected="selected" @endif>SSL</option>
                                            <option value="tls" @if (website_config('smtp')->encryption == 'tls') selected="selected" @endif>TLS</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <div class="form-group">
                                        <label>Port</label>
                                        <input type="text" name="smtp_port" class="form-control" value="{{ old('smtp_port') ?? website_config('smtp')->port }}" />
                                    </div>
                                </div>
                            </div>
                            <div class="custom-control custom-switch mb-3">
                                <input id="smtp_auth" name="smtp_auth" type="checkbox" class="custom-control-input" @if (website_config('smtp')->auth == '1') checked @endif>
                                <label class="custom-control-label" for="smtp_auth">Autentikasi</label>
                            </div>
                            <div class="form-group">
                                <label>Username</label>
                                <input type="text" name="smtp_username" class="form-control" value="{{ old('smtp_username') ?? website_config('smtp')->username }}" @if (website_config('smtp')->auth == null) disabled @endif/>
                            </div>
                            <div class="form-group">
                                <label>Password</label>
                                <input type="password" name="smtp_password" class="form-control" value="{{ old('smtp_password') ?? website_config('smtp')->password }}" @if (website_config('smtp')->auth == null) disabled @endif/>
                            </div>
                            <div class="my-3">
                                <a href="{{ url('admin/settings/website_configs/test_email') }}" class="btn btn-outline-info">Kirim Email Percobaan</a>
                                <small class="form-text text-muted">Sistem akan mengirim email ke nilai dari bidang <strong> Email Dari </strong> yang Anda tetapkan di atas. Pastikan untuk menyimpan pengaturan terlebih dahulu!</small>
                            </div>
                        </div>
                        {{--<div class="tab-pane fade" id="notifications">
                            <div class="form-group">
                                <label>Email Penerima Notifikasi</label>
                                <input type="email" name="notification_email" class="form-control" value="{{ old('notification_email') ?? website_config('notification')->email }}" />
                            </div>
                            <h6 class="font-weight-medium">Pemesanan</h6>
                            <div class="custom-control custom-switch mb-0">
                                <input type="checkbox" class="custom-control-input" id="notification_order" name="notification_order" @if (website_config('notification')->order == '1') checked @endif>
                                <label class="custom-control-label font-weight-normal" for="notification_order">Informasi pembuatan atau pembaruan pesanan pengguna.</label>
                            </div>
                            <h6 class="font-weight-medium mt-2">Deposit</h6>
                            <div class="custom-control custom-switch mb-0">
                                <input type="checkbox" class="custom-control-input" id="notification_deposit" name="notification_deposit" @if (website_config('notification')->deposit == '1') checked @endif>
                                <label class="custom-control-label font-weight-normal" for="notification_deposit">Informasi pembuatan atau pembaruan deposit pengguna.</label>
                            </div>
                            <h6 class="font-weight-medium mt-2">Tiket</h6>
                            <div class="custom-control custom-switch mb-2">
                                <input type="checkbox" class="custom-control-input" id="notification_ticket" name="notification_ticket" @if (website_config('notification')->ticket == '1') checked @endif>
                                <label class="custom-control-label font-weight-normal" for="notification_ticket">Informasi pembuatan atau pembaruan tiket pengguna.</label>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="template">
                            <div class="form-group">
								<div class="row">
                                    <div class="form-group col-lg-6">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" id="first_template" name="website_template" value="first-template" class="custom-control-input" @if (website_config('template')->number == 'first-template') checked @endif>
                                            <label class="custom-control-label mb-2" for="first_template">Tampilan Satu</label>
                                            <img src="{{ asset('assets/images/first-template.png') }}" class="rounded img-responsive waves-effect waves-light" data-toggle="modal" data-target="#firstTemplate" target="_blank" style="text-align: center; width:100%;max-width:1000px;">
                                        </div>
                                        <div class="modal fade" id="firstTemplate" tabindex="-1" role="dialog" aria-labelledby="firstTemplateLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="firstTemplateLabel">
                                                            <i class="fa fa-desktop"></i>
                                                            Tampilan Satu
                                                        </h5>
                                                        <button type="button" class="close waves-effect waves-light" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <img src="{{ asset('assets/images/first-template.png') }}" class="rounded img-responsive waves-effect waves-light"  style="text-align: center; width:100%;max-width:1000px;">
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-light waves-effect waves-light" data-dismiss="modal">Tutup</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
							</div>
                        </div>
                        <div class="tab-pane fade" id="product">
							<div class="form-group">
								<label>Keuntungan Harga Agen</label>
								<input type="text" name="profit_agen_price" class="form-control autonumeric-currency" value="{{ old('profit_agen_price') ?? website_config('product_profit')->agen_price }}" />
							</div>
                            <div class="form-group">
								<label>Keuntungan Harga Reseller</label>
								<input type="text" name="profit_reseller_price" class="form-control autonumeric-currency" value="{{ old('profit_reseller_price') ?? website_config('product_profit')->reseller_price }}" />
							</div>
                            <div class="form-group">
								<label>Bonus Pesanan (Poin)</label>
								<input type="text" name="order_bonus" class="form-control autonumeric-quantity" value="{{ old('order_bonus') ?? website_config('product_profit')->order_bonus }}" />
							</div>
                        </div>
                        <div class="tab-pane fade" id="banner">
                            <div class="form-group">
                                <label>Banner Website</label>
                                <div class="form-group">
                                    <input type="file" class="dropify" accept=".png, .jpg, .jpeg, .svg" name="website_banner" id="website_banner" data-max-file-size="10M" @if (website_config('banner')->value <> null) data-default-file="{{ website_config('banner')->value }}" data-show-remove="false" @endif data-toggle="tooltip" title="Ukuran yang Disarankan: 150px x 34px" />
                                </div>
                                @if (website_config('banner')->value <> null)
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control" value="{{ website_config('banner')->value }}" disabled>
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            <a href="{{ url('admin/settings/website_configs/delete_banner') }}" class="text-dark">
                                                Hapus Banner
                                            </a>
                                        </span>
                                    </div>
                                </div>
                                @endif          
                            </div>
                        </div>
                        <div class="tab-pane fade" id="others">
                            <h6 class="font-weight-medium">Mode Pengembangan</h6>
                            <div class="custom-control custom-switch mb-0">
                                <input type="checkbox" class="custom-control-input" id="is_website_under_maintenance" name="is_website_under_maintenance" @if (website_config('main')->is_website_under_maintenance == '1') checked @endif>
                                <label class="custom-control-label font-weight-normal" for="is_website_under_maintenance">Menampilkan halaman mode pengembangan dari website.</label>
                            </div>
                            <h6 class="font-weight-medium mt-2">Halaman Landing</h6>
                            <div class="custom-control custom-switch mb-0">
                                <input type="checkbox" class="custom-control-input" id="is_landing_page_enabled" name="is_landing_page_enabled" @if (website_config('main')->is_landing_page_enabled == '1') checked @endif>
                                <label class="custom-control-label font-weight-normal" for="is_landing_page_enabled">Menampilkan halaman landing dari website.</label>
                            </div>
                            <h6 class="font-weight-medium mt-2">Konfirmasi Email Pengguna</h6>
                            <div class="custom-control custom-switch mb-0">
                                <input type="checkbox" class="custom-control-input" id="is_email_confirmation_enabled" name="is_email_confirmation_enabled" @if (website_config('main')->is_email_confirmation_enabled == '1') checked @endif>
                                <label class="custom-control-label font-weight-normal" for="is_email_confirmation_enabled">Mengirim email aktivasi akun untuk pengguna baru.</label>
                            </div>
                            <h6 class="font-weight-medium mt-2">Pendaftaran Pengguna Baru</h6>
                            <div class="custom-control custom-switch mb-0">
                                <input type="checkbox" class="custom-control-input" id="is_register_enabled" name="is_register_enabled" @if (website_config('main')->is_register_enabled == '1') checked @endif>
                                <label class="custom-control-label font-weight-normal" for="is_register_enabled">Mengaktifkan menu pendaftaran pengguna.</label>
                            </div>
                            <h6 class="font-weight-medium mt-2">Atur Ulang Kata Sandi</h6>
                            <div class="custom-control custom-switch mb-0">
                                <input type="checkbox" class="custom-control-input" id="is_reset_password_enabled" name="is_reset_password_enabled" @if (website_config('main')->is_reset_password_enabled == '1') checked @endif>
                                <label class="custom-control-label font-weight-normal" for="is_reset_password_enabled">Mengaktifkan menu atur ulang kata sandi pengguna.</label>
                            </div>
                            <hr />
                            {{--<div class="form-group">
								<label>Informasi Pemesanan</label>
								<textarea class="form-control custom-text-editor" id="snow-editor" name="order_info" rows="5">{{ old('order_info') ?? website_config('other')->order_info }}</textarea>
							</div>
                            <hr />
                            <div class="form-group">
                                <label>Informasi Deposit</label>
								<textarea class="form-control custom-text-editor" name="deposit_info" rows="5">{{ old('deposit_info') ?? website_config('other')->deposit_info }}</textarea>
                            </div>--}}
                            
                        <div>
							<button type="submit" class="btn btn-success">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    $('input[name="smtp_auth"]').on('change', (event) => {
        if($(event.currentTarget).is(':checked')) {
            $('input[name="smtp_username"],input[name="smtp_password"]').removeAttr('disabled');
        } else {
            $('input[name="smtp_username"],input[name="smtp_password"]').attr('disabled', 'true');
        }
    });
	$(document).ready(function() {
		$(".dropify").dropify({
			messages:{
				default:"Seret atau jatuhkan file disini atau klik",
				replace:"Seret atau jatuhkan atau klik untuk menggantikn",
                remove:  'Hapus',
				error:"Ooops, terjadi kesalahan."
			},
			error:{
				fileSize:"Ukuran File terlalu besar (Maksimal 10MB)."
			}
		})
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
	});
</script>
@endsection
