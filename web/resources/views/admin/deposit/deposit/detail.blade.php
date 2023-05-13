<div class="table-responsive">
	<table class="table table-bordered">
		<tr>
			<td align="center" colspan="2">
				<strong>INFORMASI DEPOSIT</strong>
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
			<td>{{ $target->user->username }}</td>
		</tr>
		<tr>
			<th>METODE</th>
			<td>
				@if ($target->deposit_method == true) 
				{{ $target->deposit_method->name }} ({{ $target->deposit_method->payment }} - {{ $target->deposit_method->type }})
				@else
				{{ $target->deposit_method_name }}
				@endif
			</td>
		</tr>
		<tr>
			<th>JUMLAH</th>
			<td>Rp {{ number_format($target->amount,0,',','.') }}</td>
		</tr>
		<tr>
			<th>SALDO DIDAPAT</th>
			<td>Rp {{ number_format($target->balance,0,',','.') }}</td>
		</tr>
		@if ($target->phone_number <> null)
		<tr>
			<th>NO. HANDPHONE</th>
			<td>{{ $target->phone_number }}</td>
		</tr>	
		@endif
		<tr>
			<th>STATUS</th>
			<td>{!! status($target->status) !!}</td>
		</tr>
		<tr>
			<th>ALAMAT IP</th>
			<td>{{ $target->ip_address }}</td>
		</tr>
	</table>
</div>