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
                        <div class="form-group">
                            <label>Nomer Telepon <text class="text-danger">*</text></label>
                            <input type="text" class="form-control @error('phone_number') is-invalid @enderror" name="phone_number" value="{{ old('phone_number') }}" placeholder="">
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