<form method="GET">
    <div class="row">
        <div class="form-group col-lg-2">
            <label>Kolom Sortir</label>
            <div class="input-group">
                <select class="form-control" name="sort_column" data-toggle="select2">
                    <option value="" selected>Semua...</option>
                    @foreach($sort_columns as $key => $value)
                        <option value="{{ $key }}" {{ request('sort_column') == $key ? 'selected' : '' }}>{{ $value }}</option>
                    @endforeach
                </select>		
                <span class="input-group-prepend">
                    <button class="btn btn-success" type="submit"><i class="fa fa-filter"></i></button>
                </span>
            </div>
        </div>
        <div class="form-group col-lg-2">
            <label>Tipe Sortir</label>
            <div class="input-group">
                <select class="form-control" name="sort_type">
                    <option value="" selected>Tipe...</option>
                    @foreach($sort_types as $key => $value)
                        <option value="{{ $key }}" {{ request('sort_type') == $key ? 'selected' : '' }}>{{ $value }}</option>
                    @endforeach
                </select>	
                <span class="input-group-prepend">
                    <button class="btn btn-success" type="submit"><i class="fa fa-filter"></i></button>
                </span>
            </div>
        </div>
        <div class="form-group col-lg-2">
            <label>Kolom Cari</label>
            <div class="input-group">
                <select class="form-control" name="search_column">
                    <option value="" selected>Kolom...</option>
                    @foreach($sort_columns as $key => $value)
                        <option value="{{ $key }}" {{ request('search_column') == $key ? 'selected' : '' }}>{{ $value }}</option>
                    @endforeach
                </select>
                <span class="input-group-prepend">
                    <button class="btn btn-success" type="submit"><i class="fa fa-search"></i></button>
                </span>
            </div>
        </div>
        <div class="form-group col-lg-2">
            <label>Operator Cari</label>
            <div class="input-group">
                <select class="form-control" name="search_operator">
                    <option value="" selected>Operator...</option>
                    @foreach($search_operators as $key => $value)
                        <option value="{{ $key }}" {{ request('search_operator') == $key ? 'selected' : '' }}>{{ $value }}</option>
                    @endforeach
                </select>
                <span class="input-group-prepend">
                    <button class="btn btn-success" type="submit"><i class="fa fa-search"></i></button>
                </span>
            </div>
        </div>
        <div class="form-group col-lg-4">
            <label>Kata Kunci Cari</label>
            <div class="input-group">
                <input type="text" class="form-control" name="search_value" placeholder="Kata Kunci..." value="{{ request('search_value') }}">
                <span class="input-group-prepend">
                    <button class="btn btn-success" type="submit"><i class="fa fa-search"></i></button>
                </span>
            </div>
        </div>
    </div>
</form>