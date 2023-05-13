@extends('admin.layouts.app')
@section('breadcrumb-first', $breadcrumb->first)
@section('breadcrumb-second', $breadcrumb->second)
@section('content')
<div class="row">
    <div class="col d-flex justify-content-center">
        <div class="col col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form method="post" action="{{ request()->url() }}">
                        @csrf
                        <div class="form-group hidden">
                            <label>Kode Referensi <text class="text-danger">*</text></label>
                            <input type="text" class="form-control @error('reference_code') is-invalid @enderror" name="reference_code" value="{{ Session::get('result')['reference_code'] ?? '' }}" placeholder="" readonly>
                            @error('reference_code')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Kode OTP<text class="text-danger">*</text></label>
                            <input type="text" class="form-control @error('otp_code') is-invalid @enderror" name="otp_code" value="{{ old('otp_code') }}" placeholder="">
                            @error('otp_code')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <div class="form-group hidden">
                            <label>Nomer Telepon <text class="text-danger">*</text></label>
                            <input type="text" class="form-control @error('phone_number') is-invalid @enderror" name="phone_number" value="{{ Session::get('result')['phone_number'] ?? '' }}" placeholder="" readonly>
                            @error('phone_number')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <button type="reset" class="btn btn-danger">Reset</button>
                        <button type="submit" class="btn btn-success">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection