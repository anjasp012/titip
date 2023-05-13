<form method="post" action="{{ request()->url() }}" id="main_form">
	@csrf
    <div class="form-group">
        <label>No. Serial <text class="text-danger">*</text></label>
        <input type="text" class="form-control" name="serial_number" value="{{ old('serial_number') ?? $target->serial_number }}">
        <small class="text-danger serial_number_error"></small>
    </div>
    <div class="form-group">
        <label>Status <text class="text-danger">*</text></label>
        <select class="form-control" name="status" data-toggle="select2">
            <option value="" selected>Pilih...</option>
            @foreach($status_list as $item)
                <option value="{{ $item }}" {{ old('status') ? (old('status') == $item ? 'selected' : '') : ($target->status == $item ? 'selected' : '')  }}>{{ $item }}</option>
            @endforeach
        </select>
        <small class="text-danger status_error"></small>
    </div>
    <hr />
    <div class="text-right">
        <button type="reset" class="btn btn-danger"><i class="fa fa-redo"></i> Reset</button>
        <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Submit</button>
    </div>
</form>
<link href="{{ asset('assets/custom.css') }}" rel="stylesheet" type="text/css" />
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
</script>