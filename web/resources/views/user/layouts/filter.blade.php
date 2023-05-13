<div class="row">
	<div class="col-lg-12">
		<form method="get">
			<div class="row">
				<div class="form-group col-lg-2">
					<label>Kolom Sortir</label>
					<select class="form-control" name="sort_column">
						<option value="" selected>Kolom...</option>
						@foreach($sort_column as $key => $value)
							@if (request('sort_column') == $key)
							    <option value="{{ $key }}" selected>{{ $value }}</option>
							@else
							    <option value="{{ $key }}">{{ $value }}</option>
							@endif
						@endforeach
					</select>
				</div>
				<div class="form-group col-lg-2">
					<label>Tipe Sortir</label>
					<select class="form-control" name="sort_type">
						<option value="" selected>Tipe...</option>
						@foreach($sort_type as $key => $value)
							@if (request('sort_type') == $key)
							    <option value="{{ $key }}" selected>{{ $value }}</option>
							@else
							    <option value="{{ $key }}">{{ $value }}</option>
							@endif
						@endforeach
					</select>
				</div>
				<div class="form-group col-lg-2">
					<label>Kolom Cari</label>
					<select class="form-control" name="search_column">
						<option value="" selected>Kolom...</option>
						@foreach($sort_column as $key => $value)
							@if (request('search_column') == $key)
							    <option value="{{ $key }}" selected>{{ $value }}</option>
							@else
							    <option value="{{ $key }}">{{ $value }}</option>
							@endif
						@endforeach
					</select>
				</div>
				<div class="form-group col-lg-5">
					<label>Kata Kunci Cari</label>
					<input type="text" class="form-control" name="search_value" placeholder="Kata Kunci..." value="{{ request('search_value') }}">
				</div>
				<div class="form-group col-lg-1">
					<label>Submit</label>
					<button type="submit" class="btn btn-block btn-success"><i class="fa fa-filter"></i></button>
				</div>
			</div>
		</form>
	</div>
</div>