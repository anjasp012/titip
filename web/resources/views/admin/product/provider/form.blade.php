<form method="post" action="{{ request()->url() }}" id="main_form">
	@csrf
	<div class="form-group">
        <label>Nama <text class="text-danger">*</text></label>
        <input type="text" class="form-control" name="name" value="{{ old('name') ?? $target->name }}">
        <small class="text-danger name_error"></small>
    </div>
    <div class="row">
        <div class="form-group col-lg-4">
            <label>URL Pemesanan <text class="text-danger">*</text></label>
            <input type="url" class="form-control" name="provider_url_order" value="{{ old('provider_url_order') ?? $target->provider_url_order }}">
            <small class="text-danger provider_url_order_error"></small>
        </div>
        <div class="form-group col-lg-4">
            <label>URL Status <text class="text-danger">*</text></label>
            <input type="url" class="form-control" name="provider_url_status" value="{{ old('provider_url_status') ?? $target->provider_url_status }}">
            <small class="text-danger provider_url_status_error"></small>
        </div>
        <div class="form-group col-lg-4">
            <label>URL Layanan <text class="text-danger">*</text></label>
            <input type="url" class="form-control" name="provider_url_service" value="{{ old('provider_url_service') ?? $target->provider_url_service }}">
            <small class="text-danger provider_url_service_error"></small>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-lg-4">
            <label>ID <text class="text-danger">*</text></label>
            <input type="text" class="form-control" name="provider_id" value="{{ old('provider_id') ?? $target->provider_id }}">
            <small class="text-danger provider_id_error"></small>
        </div>
        <div class="form-group col-lg-4">
            <label>Kunci <text class="text-danger">*</text></label>
            <input type="text" class="form-control" name="provider_key" value="{{ old('provider_key') ?? $target->provider_key }}">
            <small class="text-danger provider_key_error"></small>
        </div>
        <div class="form-group col-lg-4">
            <label>Rahasia</label>
            <input type="text" class="form-control" name="provider_secret" placeholder="Kosongkan jika tidak dibutuhkan" value="{{ old('provider_secret') ?? $target->provider_secret }}">
		    <small class="text-danger provider_secret_error"></small>
        </div>
    </div>
    <hr />
    <div class="text-right">
        <button type="reset" class="btn btn-danger"><i class="fa fa-redo"></i> Reset</button>
        <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Submit</button>
    </div>
</form>
<script src="{{ asset('assets/custom-form.js') }}"></script>