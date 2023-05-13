<form method="post" action="{{ request()->url() }}" id="main_form">
	@csrf
	<div class="form-group">
		<label>Nama Lengkap <text class="text-danger">*</text></label>
		<input type="text" class="form-control" name="full_name" value="{{ old('full_name') ?? $target->full_name }}">
		<small class="text-danger full_name_error"></small>
	</div>
	<div class="form-group">
		<label>Nomor Telepon <text class="text-danger">*</text></label>
		<input class="form-control" type="number" name="phone_number" value="{{ old('phone_number') ?? $target->phone_number }}">
		<small class="text-danger phone_number_error"></small>
	</div>
	<div class="form-group">
		<label>Email <text class="text-danger">*</text></label>
		<input class="form-control" type="email" name="email" value="{{ old('email') ?? $target->email }}">
		<small class="text-danger email_error"></small>
	</div>
	<div class="form-group">
		<label>Username <text class="text-danger">*</text></label>
		<input type="text" class="form-control" name="username" value="{{ old('username') ?? $target->username }}">
		<small class="text-danger username_error"></small>
	</div>
	<div class="form-group">
		<label>Password {!! request()->segment(4) == null ? '<text class="text-danger">*</text>' : '' !!}</label>
		<input type="password" class="form-control" name="password" placeholder="{{ request()->segment(4) == true ? 'Kosongkan jika tidak dibutuhkan' : '' }}" value="{{ old('password') }}">
		<small class="text-danger password_error"></small>
	</div>
	<div class="form-group">
		<label>Balance <text class="text-danger">*</text></label>
		<div class="input-group">
			<input type="text" class="form-control" name="balance" value="{{ old('balance') ?? $target->balance }}" id="api_key">
			<small class="text-danger balance_error"></small>
		</div>
	</div>
	<div class="form-group">
		<label>Point <text class="text-danger">*</text></label>
		<div class="input-group">
			<input type="text" class="form-control" name="point" value="{{ old('point') ?? $target->point }}" id="point">
			<small class="text-danger point_error"></small>
		</div>
	</div>
	{{--<div class="form-group">
		<label>Saldo <text class="text-danger">*</text></label>
		<input type="text" class="form-control autonumeric-currency" name="balance" value="{{ old('balance') ?? $target->balance }}">
		<small class="text-danger balance_error"></small>
	</div
	@if (request()->segment(4) == true)
	<div class="form-group">
		<label>Kunci API <text class="text-danger">*</text></label>
		<div class="input-group">
			<input type="text" class="form-control" name="api_key" value="{{ old('api_key') ?? $target->api_key }}" id="api_key">
			<div class="input-group-append">
				<button class="btn btn-dark" type="button" onclick="create_api_key()"><i class="fa fa-random fa-fw"></i></button>
			</div>
			<small class="text-danger api_key_error"></small>
		</div>
	</div>
	@endif
	<div class="form-group">
		<label>Level <text class="text-danger">*</text></label>
		<select class="form-control" name="level" data-toggle="select2">
			<option value="" selected>Pilih...</option>
			@foreach($levels as $item)
				<option value="{{ $item }}" {{ old('level') ? (old('level') == $item ? 'selected' : '') : ($target->level == $item ? 'selected' : '')  }}>{{ $item }}</option>
			@endforeach
		</select>
		<small class="text-danger level_error"></small>
	</div>>--}}
    <hr />
    <div class="text-right">
        <button type="reset" class="btn btn-danger"><i class="fa fa-redo"></i> Reset</button>
        <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Submit</button>
    </div>
</form>
<script src="{{ asset('assets/autoNumeric-min.js') }}"></script>
<script src="{{ asset('assets/custom-form.js') }}"></script>
<script src="{{ asset('assets/custom-footer.js') }}"></script>
<script>
    $(document).ready(function() {
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
	function create_api_key() {
		$.ajax({
			type: "GET",
			url: "{{ url('ajax/create_api_key') }}",
			success: function(data) {
				$('#api_key').val(data);
			}
		});
	}
</script>
