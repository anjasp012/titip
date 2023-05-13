<form method="post" action="{{ request()->url() }}" id="main_form">
	@csrf
    <div class="form-group">
        <label>Pengguna <text class="text-danger">*</text></label>
        <select class="form-control" name="user_id" data-toggle="select2">
            <option value="" selected>Pilih...</option>
            @foreach($users as $item)
                <option value="{{ $item->id }}">{{ $item->username }} ({{ $item->full_name }})</option>
            @endforeach
        </select>
        <small class="text-danger user_id_error"></small>
    </div>
    <div class="form-group">
        <label>Subjek <text class="text-danger">*</text></label>
        <input type="text" class="form-control" name="subject" value="{{ old('subject') }}">
        <small class="text-danger subject_error"></small>
    </div>
    <div class="form-group">
        <label>Pesan <text class="text-danger">*</text></label>
        <textarea class="form-control custom-text-editor" name="message" rows="5">{{ old('message') }}</textarea>
        <small class="text-danger message_error"></small>
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