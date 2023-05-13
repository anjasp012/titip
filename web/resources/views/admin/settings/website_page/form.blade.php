<form method="post" action="{{ request()->url() }}" id="main_form">
	@csrf
    <div class="row">
        <div class="form-group col-lg-6">
            <label>Judul <text class="text-danger">*</text></label>
            <input type="text" class="form-control" id="theTitle" name="title" value="{{ old('title') ?? $target->title }}">
            <small class="text-danger title_error"></small>
        </div>
        <div class="form-group col-lg-6">
            <label>Slug <text class="text-danger">*</text></label>
            <input type="text" class="form-control" id="theSlug" name="slug" value="{{ old('slug') ?? $target->slug }}">
            <small class="text-danger slug_error"></small>
        </div>
    </div>
    <div class="form-group">
        <label>Konten <text class="text-danger">*</text></label>
        <textarea class="form-control custom-text-editor" name="content" rows="5">{{ old('content') ?? $target->content }}</textarea>
        <small class="text-danger content_error"></small>
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