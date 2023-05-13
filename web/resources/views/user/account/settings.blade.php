@extends('user.layouts.app')
@section('breadcrumb-first', $breadcrumb->first)
@section('breadcrumb-second', $breadcrumb->second)
@section('content')
<section class="section profile">
<div class="col-md-8 mx-auto">
	<div class="card">
		<div class="card-body pt-3">
		  <!-- Bordered Tabs -->
			<ul class="nav nav-tabs nav-tabs-bordered">

					<li class="nav-item">
					<button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile-overview">Overview</button>
					</li>

					<li class="nav-item">
					<button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-edit">Edit Profile</button>
					</li>

					<li class="nav-item">
					<button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-settings">Settings</button>
					</li>

					<li class="nav-item">
					<button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-change-password">Change Password</button>
					</li>

			</ul>
		  <div class="tab-content pt-2">

			<div class="tab-pane fade show active profile-overview" id="profile-overview">
				<h5 class="card-title">Profile Details</h5>

				<div class="row">
					<div class="col-lg-3 col-md-4 label ">Nama Lengkap</div>
					<div class="col-lg-9 col-md-8">{{Auth::user()->full_name}}</div>
				</div>

				<div class="row">
					<div class="col-lg-3 col-md-4 label">Username</div>
					<div class="col-lg-9 col-md-8">{{Auth::user()->username}}</div>
				</div>

				<div class="row">
					<div class="col-lg-3 col-md-4 label">Email</div>
					<div class="col-lg-9 col-md-8">{{Auth::user()->email}}</div>
				</div>

				<div class="row">
					<div class="col-lg-3 col-md-4 label">Telepon</div>
					<div class="col-lg-9 col-md-8">{{Auth::user()->phone_number}}</div>
				</div>

				<div class="row">
					<div class="col-lg-3 col-md-4 label ">Url Referrals</div>
					<small>Share link dibawah ini dan dapatkan lebih banyak point</small>
					<div class="col-lg-9 col-md-8">https://titiptugas.com/ref/{{Auth::user()->referral_code}}</div>
				</div>
			</div>

			<div class="tab-pane fade profile-edit pt-3" id="profile-edit">

			  <!-- Profile Edit Form -->
			  <form method="post" action="{{ request()->url() }}/updateprofile" id="main_form">
				@csrf
					<div class="row mb-3">
					<label for="profileImage" class="col-md-4 col-lg-3 col-form-label">Profile Image</label>
						<div class="col-md-8 col-lg-9">
							@if (Auth::user()->avatar !="")
								<img src="{{ url('public/avatar/'.Auth::user()->avatar)}}" alt="Profile">
							@else
								<img src="{{ asset('assets/titiptugas/img/profile.png')}}" alt="Profile">
							@endif
							
							<div class="pt-2">
								<input class="form-control" type="file" id="formFile" name="gambar">
							</div>
						</div>
					</div>

					<div class="row mb-3">
						<label for="fullName" class="col-md-4 col-lg-3 col-form-label">Nama Lengkap</label>
						<div class="col-md-8 col-lg-9">
							<input type="text" class="form-control" id="full_name" placeholder="" name="full_name" value="{{Auth::user()->full_name}}">
							<small class="text-danger full_name_error"></small>
						</div>
					</div>

				
					<div class="row mb-3">
						<label for="company" class="col-md-4 col-lg-3 col-form-label">Username</label>
						<div class="col-md-8 col-lg-9">
							<input type="text" class="form-control" id="username" name="username" value="{{Auth::user()->username}}" readonly>
							<small class="text-danger username_error"></small>
						</div>
					</div>

					<div class="row mb-3">
						<label for="Phone" class="col-md-4 col-lg-3 col-form-label">Phone</label>
						<div class="col-md-8 col-lg-9">
							<input name="phone" type="text" class="form-control" id="Phone" value="{{Auth::user()->phone_number}}" readonly>
						</div>
					</div>

					<div class="row mb-3">
						<label for="Email" class="col-md-4 col-lg-3 col-form-label">Email</label>
						<div class="col-md-8 col-lg-9">
							<input name="email" type="email" class="form-control" id="Email" value="{{Auth::user()->email}}" readonly>
						</div>
					</div>

					<div class="row mb-3">
						<label for="Twitter" class="col-md-4 col-lg-3 col-form-label">Twitter Profile</label>
						<div class="col-md-8 col-lg-9">
							<input name="twitter" type="text" class="form-control" id="Twitter" value="">
						</div>
					</div>

					<div class="row mb-3">
						<label for="Facebook" class="col-md-4 col-lg-3 col-form-label">Facebook Profile</label>
						<div class="col-md-8 col-lg-9">
							<input name="facebook" type="text" class="form-control" id="Facebook" value="">
						</div>
					</div>

					<div class="row mb-3">
						<label for="Instagram" class="col-md-4 col-lg-3 col-form-label">Instagram Profile</label>
						<div class="col-md-8 col-lg-9">
							<input name="instagram" type="text" class="form-control" id="Instagram" value="">
						</div>
					</div>
					<div class="text-center">
						<button type="submit" class="btn btn-primary rounded-pill"><i class="fa fa-check"></i> Simpan</button>
					</div>
			  </form><!-- End Profile Edit Form -->

			</div>

			<div class="tab-pane fade pt-3" id="profile-settings">

			  <!-- Settings Form -->
			  <form>

				<div class="row mb-3">
				  <label for="fullName" class="col-md-4 col-lg-3 col-form-label">Email Notifications</label>
				  <div class="col-md-8 col-lg-9">
					<div class="form-check">
					  <input class="form-check-input" type="checkbox" id="changesMade" checked>
					  <label class="form-check-label" for="changesMade">
						Changes made to your account
					  </label>
					</div>
					<div class="form-check">
					  <input class="form-check-input" type="checkbox" id="newProducts" checked>
					  <label class="form-check-label" for="newProducts">
						Post Answer
					  </label>
					</div>
					<div class="form-check">
					  <input class="form-check-input" type="checkbox" id="proOffers">
					  <label class="form-check-label" for="proOffers">
						Like Post
					  </label>
					</div>
					<div class="form-check">
					  <input class="form-check-input" type="checkbox" id="securityNotify" checked disabled>
					  <label class="form-check-label" for="securityNotify">
						Security alerts
					  </label>
					</div>
				  </div>
				</div>

				<div class="text-center">
				  <button type="submit" class="btn btn-primary">Save Changes</button>
				</div>
			  </form><!-- End settings Form -->

			</div>

			<div class="tab-pane fade pt-3" id="profile-change-password">
			  <!-- Change Password Form -->
			  <form>

				<div class="row mb-3">
				  <label for="currentPassword" class="col-md-4 col-lg-3 col-form-label">Current Password</label>
				  <div class="col-md-8 col-lg-9">
					<input name="password" type="password" class="form-control" id="currentPassword">
				  </div>
				</div>

				<div class="row mb-3">
				  <label for="newPassword" class="col-md-4 col-lg-3 col-form-label">New Password</label>
				  <div class="col-md-8 col-lg-9">
					<input name="newpassword" type="password" class="form-control" id="newPassword">
				  </div>
				</div>

				<div class="row mb-3">
				  <label for="renewPassword" class="col-md-4 col-lg-3 col-form-label">Re-enter New Password</label>
				  <div class="col-md-8 col-lg-9">
					<input name="renewpassword" type="password" class="form-control" id="renewPassword">
				  </div>
				</div>

				<div class="text-center">
				  <button type="submit" class="btn btn-primary">Change Password</button>
				</div>
			  </form><!-- End Change Password Form -->

			</div>

		  </div><!-- End Bordered Tabs -->

		</div>
	  </div>
</div>
</section>
@endsection

@section('script')
<script>
	function reset_button(value = 0) {
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
	$(function() {
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
					reset_button(1);
					if (data.status == false) {
						if (data.type == 'validation') {
							$.each(data.message, function(prefix, val) {
								$("input[name="+prefix+"]").addClass('is-invalid');
								$('small.'+prefix+'_error').text(val[0]);
							});
						}
						if (data.type == 'alert') {
							swal.fire("Gagal!", data.message, "error");
						}
					} else {
						swal.fire("Berhasil!", data.message, "success").then(function () {
							location.reload();
						});
					}
				},
				error:function() {
					swal.fire("Gagal!", "Terjadi kesalahan.", "error");
				},
			});
		});
	});
	function setNotification(elt, url) {
		$.ajax({
			url: url+'/'+$(elt).attr('value'),
			type: 'GET',
			error: function() {
				alertify.error('<i class="fa fa-times"></i> Terjadi kesalahan.')
			},
			success: function(result) {
				result = JSON.parse(result);
				if (result.result == false) {
					alertify.error('<i class="fa fa-times"></i> Terjadi kesalahan.')
				} else {
					if ($(elt).attr('value') == '1') {
						$("input[id="+$(elt).attr('id')+"]").val('0');
					} else {
						$("input[id="+$(elt).attr('id')+"]").val('1');
					}
					alertify.success('<span class="text-white"><i class="fa fa-check"></i> '+result.message+'</span>');
				}
			}
		});
	}
</script>
@endsection
