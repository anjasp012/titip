<form method="post" action="{{ request()->url() }}" id="main_form">
	@csrf
	<div class="form-group">
		<label>Kategori <text class="text-danger">*</text></label>
		<select class="form-control" name="category_id" id="category_id" data-toggle="select2">
			<option value="" selected>Pilih...</option>
			@foreach($categories as $item)
				<option value="{{ $item->id }}" {{ old('category_id') ? (old('category_id') == $item->id ? 'selected' : '') : ($target->category_id == $item->id ? 'selected' : '')  }}>{{ $item->name }}</option>
			@endforeach
		</select>
        <small class="text-danger category_id_error"></small>
	</div>
	<div class="form-group">
		<label>Nama <text class="text-danger">*</text></label>
		<input type="text" class="form-control" name="name" value="{{ old('name') ?? $target->name }}">
        <small class="text-danger name_error"></small>
	</div>
	<hr />
    <div class="text-right">
        <button type="reset" class="btn btn-danger"><i class="fa fa-redo"></i> Reset</button>
        <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Submit</button>
    </div>
</form>
<script src="{{ asset('assets/custom-form.js') }}"></script>