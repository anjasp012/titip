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
        <label>Metode <text class="text-danger">*</text></label>
        <select class="form-control" name="deposit_method_id" id="deposit_method_id" data-toggle="select2">
            <option value="" selected>Pilih...</option>
            @foreach($methods as $item)
                <option value="{{ $item->id }}">{{ $item->name }} ({{ $item->payment }} - {{ $item->type }})</option>
            @endforeach
        </select>
        <small class="text-danger deposit_method_id_error"></small>
    </div>
    <div class="form-group">
        <label>Jumlah <text class="text-danger">*</text></label>
        <input type="text" class="form-control autonumeric-currency" name="amount" value="{{ old('amount') }}">
        <small class="text-danger amount_error"></small>
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