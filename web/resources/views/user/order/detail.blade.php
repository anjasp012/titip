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
			<th width="50%">TANGGAL/WAKTU</th>
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
			<th>LAYANAN</th>
			<td>{{ $target->service == true ? $target->service->name : $target->service_name }}</td>
		</tr>
		<tr>
			<th>TARGET</th>
			<td>
                <div class="input-group">
                    <input type="text" class="form-control" value="{!! $target->target !!}" id="data-{{ $target->id }}" readonly>
                    <div class="input-group-append">
                        <button class="btn btn-dark" type="button" onclick="copy('data-{{ $target->id }}')"><i class="fa fa-copy fa-fw"></i> Salin</button>
                    </div>
                </div>
            </td>
		</tr>
		<tr>
			<th>JUMLAH</th>
			<td>{{ number_format($target->quantity,0,',','.') }}</td>
		</tr>
		<tr>
			<th>HARGA</th>
			<td>Rp {{ number_format($target->price,0,',','.') }}</td>
		</tr>
		<tr>
			<th>JUMLAH AWAL</th>
			<td>{{ number_format($target->start_count,0,',','.') }}</td>
		</tr>
		<tr>
			<th>SISA</th>
			<td>{{ number_format($target->remains,0,',','.') }}</td>
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
			<th>KUSTOM KOMENTAR</th>
			<td>
				<textarea class="form-control" rows="5" readonly>{{ $target->custom_comments }}</textarea>
			</td>
		</tr>
		<tr>
			<th>ALAMAT IP</th>
			<td>{{ $target->ip_address }}</td>
		</tr>
	</table>
</div>
<script>
function copyInput() {
  	var copyText = document.getElementById("serial_number");
  	copyText.select();
  	document.execCommand("copy");
	Command: toastr["success"]("Teks disalin!", "Berhasil")
}
</script>