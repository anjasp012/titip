<form method="post" action="{{ request()->url() }}" id="main_form">
	@csrf
	<div class="form-group">
		<label>Nama <text class="text-danger">*</text></label>
		<input type="text" class="form-control" name="name" value="{{ old('name') ?? $target->name }}">
        <small class="text-danger name_error"></small>
	</div>
	<div class="form-group">
		<label>Ikon <text class="text-danger">*</text></label>
		<input type="text" class="form-control" name="icon" value="{{ old('icon') ?? $target->icon  }}" placeholder="fa fa-globe">
        <small class="text-danger icon_error"></small>
	</div>
    <hr />
    <div class="text-right">
        <button type="reset" class="btn btn-danger"><i class="fa fa-redo"></i> Reset</button>
        <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Submit</button>
    </div>
</form>
<script src="{{ asset('assets/custom-form.js') }}"></script>