<div class="table-responsive">
	<table class="table table-bordered">
		<tr>
			<td align="center" colspan="2">
				<strong>INFORMASI METODE DEPOSIT</strong>
			</td>
		</tr>
		<tr>
			<th width="50%">ID</th>
			<td>{{ $target->id }}</td>
		</tr>
		<tr>
			<th>NAMA</th>
			<td>
				{{ $target->name }} ({{ $target->payment }} - {{ $target->type }})
			</td>
		</tr>
		<tr>
			<th>RATE</th>
			<td>{{ $target->rate }}</td>
		</tr>
		<tr>
			<th>MINIMAL</th>
			<td>Rp {{ number_format($target->min,0,',','.') }}</td>
		</tr>
		<tr>
			<th>KETERANGAN</th>
			<td>{!! nl2br($target->note) !!}</td>
		</tr>
	</table>
</div>