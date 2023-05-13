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
                            <label>Token Unik <text class="text-danger">*</text></label>
                            <input type="text" class="form-control @error('unique_token') is-invalid @enderror" name="unique_token" value="{{ Session::get('result')['unique_token'] ?? '' }}" placeholder="" readonly>
                            @error('unique_token')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>PIN<text class="text-danger">*</text></label>
                            <input type="text" class="form-control @error('pin') is-invalid @enderror" name="pin" value="{{ old('pin') }}" placeholder="">
                            @error('pin')
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