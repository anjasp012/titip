<form method="post" action="{{ request()->url() }}" id="main_form">
	@csrf
    <div class="form-group">
        <label>Kategori <text class="text-danger">*</text></label>
        <select class="form-control" name="category" data-toggle="select2">
            <option value="" selected>Pilih...</option>
            @foreach($categories as $item)
                <option value="{{ $item }}" {{ old('category') ? (old('category') == $item ? 'selected' : '') : ($target->category == $item ? 'selected' : '')  }}>{{ $item }}</option>
            @endforeach
        </select>
        <small class="text-danger category_error"></small>
    </div>

    <div class="form-group">
        <label>Title <text class="text-danger">*</text></label>
        <input type="text" class="form-control" name="title">
        <small class="text-danger category_error"></small>
    </div>

    <div class="form-group">
        <label>Konten <text class="text-danger">*</text></label>
        <textarea class="form-control custom-text-editor" name="content" rows="5">{{ old('content') ?? $target->content }}</textarea>
        <small class="text-danger content_error"></small>
    </div>
    {{--
    <div class="form-group">
        <div class="custom-control custom-switch">
            <input id="is_popup" name="is_popup" type="checkbox" class="custom-control-input" @if ($target->id == true AND $target->is_popup == '1') checked @endif>
            <label class="custom-control-label" for="is_popup">Popup</label>
        </div>
    </div>
    --}}
    <hr />
    <div class="text-right">
        <button type="reset" class="btn btn-danger"><i class="fa fa-redo"></i> Reset</button>
        <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Submit</button>
    </div>
</form>
<link href="{{ asset('assets/custom.css') }}" rel="stylesheet" type="text/css" />
<script src="{{ asset('assets/custom-form.js') }}"></script>
<script src="{{ asset('assets/custom-header.js') }}"></script>
<script src="{{ asset('assets/custom-footer.js') }}"></script>
