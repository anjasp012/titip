<form method="post" action="{{ request()->url() }}" id="main_form">
	@csrf
    <div class="form-group">
        <label>Nama <text class="text-danger">*</text></label>
        <input type="text" class="form-control" name="name" value="{{ old('name') ?? $target->name }}">
        <small class="text-danger name_error"></small>
    </div>
    <div class="row">
        <div class="form-group col-lg-6">
            <label>Username</label>
            <input type="text" class="form-control" name="username" placeholder="Kosongkan jika tidak dibutuhkan" value="{{ old('username') ?? $target->username }}">
        <small class="text-danger username_error"></small>
    </div>
        <div class="form-group col-lg-6">
            <label>Password</label>
            <input type="text" class="form-control" name="password" placeholder="Kosongkan jika tidak dibutuhkan" value="{{ old('password') ?? $target->password }}">
        <small class="text-danger password_error"></small>
    </div>
    </div>
    <div class="form-group">
        <label>Rekening <text class="text-danger">*</text></label>
        <input type="text" class="form-control" name="rekening" value="{{ old('rekening') ?? $target->rekening }}">
        <small class="text-danger rekening_error"></small>
    </div>
    <div class="form-group">
        <label>Token</label>
        <textarea class="form-control" name="token" placeholder="Kosongkan jika tidak dibutuhkan" rows="5">{{ old('token') ?? $target->token }}</textarea>
        <small class="text-danger token_error"></small>
    </div>
    <hr />
    <div class="text-right">
        <button type="reset" class="btn btn-danger"><i class="fa fa-redo"></i> Reset</button>
        <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Submit</button>
    </div>
</form>
<script src="{{ asset('assets/custom-form.js') }}"></script>
<script src="{{ asset('assets/custom-header.js') }}"></script>
<script src="{{ asset('assets/custom-footer.js') }}"></script>