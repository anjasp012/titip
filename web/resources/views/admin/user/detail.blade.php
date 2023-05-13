<div class="table-responsive">
	<table class="table table-bordered">
		<tr>
			<td align="center" colspan="2">
				<strong>INFORMASI UMUM</strong>
			</td>
		</tr>
		<tr>
			<th width="50%">TOTAL PEMESANAN</th>
			<td>Rp {{ number_format($target->order()->sum('price'),0,',','.') }} ({{ $target->order()->count('*') }})</td>
		</tr>
		<tr>
			<th width="50%">TOTAL DEPOSIT</th>
			<td>Rp {{ number_format($target->deposit()->sum('balance'),0,',','.') }} ({{ $target->deposit()->count('*') }})</td>
		</tr>
		<tr>
			<td align="center" colspan="2">
				<strong>INFORMASI PENGGUNA</strong>
			</td>
		</tr>
		<tr>
			<th width="50%">ID</th>
			<td>{{ $target->id }}</td>
		</tr>
		<tr>
			<th>BERGABUNG</th>
			<td>
				{{ \Carbon\Carbon::parse($target->created_at)->translatedFormat('d F, Y') }}
				({{ \Carbon\Carbon::parse($target->created_at)->diffForHumans() }})
			</td>
		</tr>
		<tr>
			<th>USERNAME</th>
			<td>{{ $target->username }}</td>
		</tr>
		<tr>
			<th>NAMA LENGKAP</th>
			<td>{{ $target->full_name }}</td>
		</tr>
		<tr>
			<th>EMAIL</th>
			<td>{{ $target->email }}</td>
		</tr>
		<tr>
			<th>NO. TELEPON</th>
			<td>{{ $target->phone_number }}</td>
		</tr>
		<tr>
			<th>SALDO</th>
			<td>Rp {{ number_format($target->balance,0,',','.') }}</td>
		</tr>
		<tr>
			<th>POIN</th>
			<td>{{ number_format($target->point,0,',','.') }} Poin</td>
		</tr>
		<tr>
			<th>API KEY</th>
			<td>{{ $target->api_key }}</td>
		</tr>
		<tr>
			<th>KODE REFERAL</th>
			<td>{{ $target->referral_code }}</td>
		</tr>
		<tr>
			<th>UPLINE</th>
			<td>{{ $target->upline }}</td>
		</tr>
		<tr>
			<th>VERIFIKASI</th>
			<td>{!! $target->is_verified == '0' ? '<i class="fa fa-times text-danger"></i> Belum' : '<i class="fa fa-check text-success"></i> Sudah' !!}</td>
		</tr>
		<tr>
			<th>STATUS</th>
			<td>{!! status($target->status) !!}</td>
		</tr>
	</table>
</div>