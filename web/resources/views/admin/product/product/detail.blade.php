<div class="table-responsive">
	<table class="table table-bordered">
		<tr>
			<td align="center" colspan="2">
				<strong>INFORMASI PENYEDIA</strong>
			</td>
		</tr>
		<tr>
			<th width="50%">ID</th>
			<td>{{ $target->provider->id }}</td>
		</tr>
		<tr>
			<th>NAMA</th>
			<td>{{ $target->provider->name }}</td>
		</tr>
		<tr>
			<th>ID PRODUK</th>
			<td>{{ $target->provider_product_id }}</td>
		</tr>
		<tr>
			<td align="center" colspan="2">
				<strong>INFORMASI PRODUK</strong>
			</td>
		</tr>
		<tr>
			<th width="50%">ID</th>
			<td>{{ $target->id }}</td>
		</tr>
		<tr>
			<th>NAMA</th>
			<td>{{ $target->name }}</td>
		</tr>
		<tr>
			<th>KATEGORI</th>
			<td>{{ $target->category->name }} - {{ $target->sub_category->name }}</td>
		</tr>
		<tr>
			<th>HARGA AGEN</th>
			<td>Rp {{ number_format($target->agen_price,0,',','.') }}</td>
		</tr>
		<tr>
			<th>HARGA RESELLER</th>
			<td>Rp {{ number_format($target->reseller_price,0,',','.') }}</td>
		</tr>
		<tr>
			<th>BONUS</th>
			<td>{{ number_format($target->bonus,0,',','.') }} Poin</td>
		</tr>
		<tr>
			<th>KEUNTUNGAN</th>
			<td>Rp {{ number_format($target->profit,0,',','.') }}</td>
		</tr>
		<tr>
			<th>STATUS</th>
			<td>{!! status($target->status) !!}</td>
		</tr>
	</table>
</div>