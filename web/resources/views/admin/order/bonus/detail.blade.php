<div class="table-responsive">
	<table class="table table-bordered">
		<tr>
			<td align="center" colspan="2">
				<strong>INFORMASI BONUS PESANAN</strong>
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
			<th>JUMLAH</th>
			<td>{{ number_format($target->amount,0,',','.') }} Poin</td>
		</tr>
		<tr>
			<th>STATUS</th>
			<td>
				@if ($target->is_sent == '1')
                    {!! '<a href="javascript:void(0);" class="badge badge-success badge-sm">TERKIRIM</a>' !!}
                @elseif ($target->is_sent == '0')
                    {!! '<a href="javascript:void(0);" class="badge badge-warning badge-sm">BELUM DIKIRIM</a>' !!}
                @else
                    {!! '<a href="javascript:void(0);" class="badge badge-info badge-sm">ERROR</a>' !!}
                @endif
			</td>
		</tr>
		<tr>
			<td align="center" colspan="2">
				<strong>INFORMASI PESANAN</strong>
			</td>
		</tr>
		<tr>
			<th width="50%">ID</th>
			<td>{{ $target->order_id }}</td>
		</tr>
		<tr>
			<th>PRODUK</th>
			<td>{{ $target->order->product->name }}</td>
		</tr>
	</table>
</div>