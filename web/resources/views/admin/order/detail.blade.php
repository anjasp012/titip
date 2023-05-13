<div class="table-responsive">
	<table class="table table-bordered">
		<tr>
			<td align="center" colspan="2">
				<strong>INFORMASI PESANAN</strong>
			</td>
		</tr>
		<tr>
			<th width="50%">ID</th>
			<td>{{ $target->id }}</td>
		</tr>
		<tr>
			<th width="50%">DIBUAT</th>
			<td>
				{{ \Carbon\Carbon::parse($target->created_at)->translatedFormat('d F Y - H:i') }}
				({{ \Carbon\Carbon::parse($target->created_at)->diffForHumans() }})
			</td>
		</tr>
		<tr>
			<th width="50%">DIPERBARUI</th>
			<td>
				{{ \Carbon\Carbon::parse($target->updated_at)->translatedFormat('d F Y - H:i') }}
				({{ \Carbon\Carbon::parse($target->updated_at)->diffForHumans() }})
			</td>
		</tr>
		<tr>
			<th>PENGGUNA</th>
			<td>
				@if ($target->user == true)
				{{$target->user->username }} ({{$target->user->full_name }})
				@endif
			</td>
		</tr>
		<tr>
			<th>PRODUK</th>
			<td>{{ $target->product->name }}</td>
		</tr>
		<tr>
			<th>TARGET</th>
			<td>{{ $target->target }}</td>
		</tr>
		<tr>
			<th>NO. SERIAL</th>
			<td>
                <div class="input-group">
                    <input type="text" class="form-control" value="{!! $target->serial_number !!}" id="data-{{ $target->id }}" readonly>
                    <div class="input-group-append">
                        <button class="btn btn-dark" type="button" onclick="copy('data-{{ $target->id }}')"><i class="fa fa-copy fa-fw"></i> Salin</button>
                    </div>
                </div>
			</td>
		</tr>
		<tr>
			<th>HARGA</th>
			<td>Rp {{ number_format($target->price,0,',','.') }}</td>
		</tr>
		<tr>
			<th>KEUNTUNGAN</th>
			<td>Rp {{ number_format($target->profit,0,',','.') }}</td>
		</tr>
		<tr>
			<th>STATUS</th>
			<td>{!! status($target->status) !!}</td>
		</tr>
		<tr>
			<th>SUMBER</th>
			<td>{{ ($target->is_api == '1') ? 'API' : 'WEB' }}</td>
		</tr>
		<tr>
			<th>PENGEMBALIAN DANA</th>
			<td>{{ ($target->is_refund == '1') ? 'YA' : 'TIDAK' }}</td>
		</tr>
		<tr>
			<th>ALAMAT IP</th>
			<td>{{ $target->ip_address }}</td>
		</tr>
		<tr>
			<th>ID PESANAN PENYEDIA</th>
			<td>{{ $target->provider_order_id }}</td>
		</tr>
		<tr>
			<th>LOG PESANAN</th>
			<td>
				<textarea class="form-control" rows="5" disabled>{{ $target->provider_order_log }}</textarea>
			</td>
		</tr>
		<tr>
			<th>LOG STATUS</th>
			<td>
				<textarea class="form-control" rows="5" disabled>{{ $target->provider_status_log }}</textarea>
			</td>
		</tr>
	</table>
</div>