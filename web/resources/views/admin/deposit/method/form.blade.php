<form method="post" action="{{ request()->url() }}" id="main_form">
	@csrf
    <div class="row">
        <div class="form-group col-lg-4">
            <label>Pembayaran <text class="text-danger">*</text></label>
            <select class="form-control" name="payment" data-toggle="select2">
                <option value="" selected>Pilih...</option>
                @foreach($payments as $item)
                    <option value="{{ $item }}" {{ old('payment') ? (old('payment') == $item ? 'selected' : '') : ($target->payment == $item ? 'selected' : '')  }}>{{ $item }}</option>
                @endforeach
            </select>
            <small class="text-danger payment_error"></small>
        </div>
        <div class="form-group col-lg-4">
            <label>Tipe <text class="text-danger">*</text></label>
            <select class="form-control" name="type" id="type" data-toggle="select2">
                <option value="" selected>Pilih...</option>
                @foreach($types as $item)
                    <option value="{{ $item }}" {{ old('type') ? (old('type') == $item ? 'selected' : '') : ($target->type == $item ? 'selected' : '')  }}>{{ $item }}</option>
                @endforeach
            </select>
            <small class="text-danger type_error"></small>
        </div>
        <div class="form-group col-lg-4">
            <label>Merchant <text class="text-danger">*</text></label>
            <select class="form-control" name="merchant">
                    <option value="bca">Bca</option>
                    <option value="bri">Bri</option>
                    <option value="bni">Bni</option>
                    <option value="mandiri">Mandiri</option>
                    <option value="gopay">Gopay</option>
                    <option value="shoopepay">Shoopepay</option>
					<option value="dana">Dana</option>
                    <option value="ovo">Ovo</option>
                    <option value="bni">Alfa</option>
                    <option value="alfamidi">Alfamidi</option>
                    <option value="indomaret">Indomaret</option>
            </select>
            <small class="text-danger type_error"></small>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-lg-3">
            <div class="form-group">
                <label>Nama <text class="text-danger">*</text></label>
                <input type="text" class="form-control" name="name" value="{{ old('name') ?? $target->name }}">
                <small class="text-danger name_error"></small>
            </div>
        </div>
        <div class="form-group col-lg-6">
            <div class="form-group">
                <label>Api Url</label>
                <input type="text" class="form-control" name="api_url" value="{{ old('api_url') ?? $target->api_url }}">
                <small class="text-danger api_url_error"></small>
            </div>
        </div>
        <div class="form-group col-lg-3">
            <div class="form-group">
                <label>Channel Code</label>
                <input type="text" class="form-control" name="merchant_code" value="{{ old('merchant_code') ?? $target->merchant_code }}">
                <small class="text-danger merchant_code_error"></small>
            </div>
        </div>
    </div>
    
   
    <div class="row">
        <div class="form-group col-lg-4">
            <label>APi ID </label>
            <input type="text" class="form-control" id="api_id" name="api_id" value="{{ old('api_id') ?? $target->api_id }}">
            <small class="text-danger api_id_error"></small>
        </div>
        <div class="form-group col-lg-4">
            <label>Public Key</label>
            <input type="text" class="form-control" name="api_public" value="{{ old('api_public') ?? $target->api_public }}">
            <small class="text-danger api_public_error"></small>
        </div>
        <div class="form-group col-lg-4">
            <label>Private Key </label>
            <input type="text" class="form-control" id="api_private" name="api_private" value="{{ old('api_private') ?? $target->api_private }}">
            <small class="text-danger api_private_error"></small>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-lg-6">
            <label>Rate <text class="text-danger">*</text></label>
            <input type="number" class="form-control" step="any" min="0" id="rate" name="rate" value="{{ old('rate') ?? $target->rate }}">
            <small class="text-danger rate_error"></small>
        </div>
        <div class="form-group col-lg-6">
            <label>Min. <text class="text-danger">*</text></label>
            <input type="text" class="form-control autonumeric-currency" name="min" value="{{ old('min') ?? $target->min }}">
            <small class="text-danger min_error"></small>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-lg-6">
            <label>No Rekening</label>
            <input type="text" class="form-control" id="rek" name="rek" value="{{ old('rek') ?? $target->rek }}">
            <small class="text-danger rek_error"></small>
        </div>
        <div class="form-group col-lg-6">
            <label>Nama Rekening</label>
            <input type="text" class="form-control" name="rek_name" value="{{ old('rek_name') ?? $target->rek_name }}">
            <small class="text-danger rek_name"></small>
        </div>
    </div>
    <div class="form-group">
        <label>Catatan <text class="text-danger">*</text></label>
        <textarea class="form-control custom-text-editor" name="note" rows="5">{{ old('note') ?? $target->note }}</textarea>
        <small class="text-danger note_error"></small>
    </div>
    <hr />
    <div class="text-right">
        <button type="reset" class="btn btn-danger"><i class="fa fa-redo"></i> Reset</button>
        <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Submit</button>
    </div>
</form>
<link href="{{ asset('assets/custom.css') }}" rel="stylesheet" type="text/css" />
<script src="{{ asset('assets/custom-header.js') }}"></script>
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
