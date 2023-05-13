@extends('user.layouts.app')
@section('breadcrumb-first', $breadcrumb->first)
@section('breadcrumb-second', $breadcrumb->second)
@section('content')
@php
$profile_success = '{
	"status": true,
	"data": {
		"full_name": "Jhon Delton",
		"username": "jhondelton",
		"balance": 10000
	},
}';
$profile_failed = '{
	"status": false,
	"data": {
		"message": "API Key salah"
	}
}';
$product_success = '{
	"status": true,
	"data": [
		{
            "product_id": "AX5",
            "category": "Pulsa Reguler",
            "sub_category": "AXIS",
            "product_name": "AXIS 5K",
            "normal_price": "5500",
            "special_price": "5000",
            "status": "1"
        },
	]
}';
$product_failed = '{
	"status": false,
	"data": {
		"message": "API Key salah"
	}
}';
$order_success = '{
	"status": true,
	"data": {
		"id": 123
	}
} ';
$order_failed = '{
	"status": false,
	"data": {
		"message": "Saldo tidak cukup"
	}
}';
$status_success = '{
	"status": true,
	"data": {
        "target": "081234567890",
		"status": "Success",
		"serial_number": 12346789,
	}
}';
$status_failed = '{
	"status": false,
	"data": {
		"message": "Pesanan tidak ditemukan"
	}
}';
$example_class = '&#x3C;?php
function connect($end_point, $post) {
&#x9;$_post = array();
&#x9;if (is_array($post)) {
&#x9;&#x9;foreach ($post as $name =&#x3E; $value) {
&#x9;&#x9;&#x9;$_post[] = $name.&#x27;=&#x27;.urlencode($value);
&#x9;&#x9;}
&#x9;}
&#x9;$ch = curl_init($end_point);
&#x9;curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
&#x9;curl_setopt($ch, CURLOPT_POST, 1);
&#x9;curl_setopt($ch, CURLOPT_HEADER, 0);
&#x9;curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
&#x9;if (is_array($post)) {
&#x9;&#x9;curl_setopt($ch, CURLOPT_POSTFIELDS, join(&#x27;&#x26;&#x27;, $_post));
&#x9;}
&#x9;curl_setopt($ch, CURLOPT_USERAGENT, &#x27;Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)&#x27;);
&#x9;$result = curl_exec($ch);
&#x9;if (curl_errno($ch) != 0 &#x26;&#x26; empty($result)) {
&#x9;&#x9;$result = false;
&#x9;}
&#x9;curl_close($ch);
&#x9;return $result;
}

$api_url = &#x27;api_url&#x27;; // api url
$post_data = array(
&#x9;&#x27;api_key&#x27; =&#x3E; &#x27;randomkey&#x27;, // api key Anda
);

$api = json_decode(connect($api_url, $post_data));
print(&#x22;&#x3C;pre&#x3E;&#x22;.print_r($api,true).&#x22;&#x3C;/pre&#x3E;&#x22;);';
@endphp
<div class="row">
    <div class="col-lg-12">
        <div class="alert alert-dismissable alert-info text-dark">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <b><i class="fa fa-info-circle"></i> Informasi:</b> Silahkan menghubungi Admin jika mengalami kesulitan dalam penggunaan API.
        </div>
    </div>
	<div class="col-lg-3">
		<div class="card m-b-30">
			<div class="card-body">
				<div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
					<a class="nav-link active" href="#tab-main" data-toggle="pill" role="tab"><i class="fa fa-home fa-fw"></i> Utama</a>
					<a class="nav-link" href="#tab-profile" data-toggle="pill" role="tab"><i class="fa fa-user fa-fw"></i> Profil</a>
					<a class="nav-link" href="#tab-products" data-toggle="pill" role="tab"><i class="fa fa-tags fa-fw"></i> Produk</a>
					<a class="nav-link" href="#tab-order" data-toggle="pill" role="tab"><i class="fa fa-shopping-cart fa-fw"></i> Pesanan</a>
					<a class="nav-link" href="#tab-status" data-toggle="pill" role="tab"><i class="fa fa-search fa-fw"></i> Status</a>
					<a class="nav-link" href="#tab-example-class" data-toggle="pill" role="tab"><i class="fa fa-code fa-fw"></i> Contoh Class</a>
				</div>
			</div>
		</div>
    </div>
    <div class="col-lg-9">
        <div class="card m-b-30">
            <div class="card-body">
				<div class="tab-content">
					<div class="tab-pane fade show active" id="tab-main">
						<div class="table-responsive">
							<table class="table table-bordered">
								<tr>
									<th width="50%">METODE HTTP</th>
									<td>POST</td>
								</tr>
								<tr>
									<th>FORMAT RESPON</th>
									<td>JSON</td>
								</tr>
								<tr>
									<th>API KEY</th>
									<td>
										{{ Auth::user()->api_key }}
										<a href="{{ url('account/create_api_key') }}" class="btn btn-sm btn-primary" title="Buat ulang"><i class="fa fa-random"></i> Buat Ulang</a>
									</td>
								</tr>
							</table>
						</div>
					</div>
					<div class="tab-pane fade" id="tab-profile">
						<h5>1. Endpoint</h5>
						<div class="table-responsive">
							<table class="table table-bordered table-hover mb-0 mt-0">
								<tr>
									<td>{{ url('api/profile') }}</td>
								</tr>
							</table>
						</div>
						<hr />
						<h5>2. Request</h5>
						<div class="table-responsive">
							<table class="table table-borderless table-hover mb-0 mt-0">
								<thead class="thead-light">
									<tr>
										<th>Parameter</th>
										<th>Keterangan</th>
										<th>Tipe Data</th>
										<th>Wajib</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>api_key</td>
										<td>API Key Anda.</td>
										<td><code>string</code></td>
										<td>Ya</td>
									</tr>
								</tbody>
							</table>
						</div>
						<hr />
						<h5>3. Response</h5>
						<div class="table-responsive">
							<table class="table table-bordered mb-0 mt-0">
								<tr>
									<th width="50%">Contoh Respon Sukses</th>
									<th>Contoh Respon Gagal</th>
								</tr>
								<tr>
									<td>
										<pre>
											<code class="language-json">
												{!! $profile_success !!}
					                    	</code>
										</pre>
									</td>
									<td>
										<pre>
											<code class="language-json">
												{!! $profile_success !!}
					                    	</code>
										</pre>
									</td>
								</tr>
							</table>
						</div>
					</div>
					<div class="tab-pane fade" id="tab-products">
						<h5>1. Endpoint</h5>
						<div class="table-responsive">
							<table class="table table-bordered table-hover mb-0 mt-0">
								<tr>
									<td>{{ url('api/products') }}</td>
								</tr>
							</table>
						</div>
						<hr />
						<h5>2. Request</h5>
						<div class="table-responsive">
							<table class="table table-borderless table-hover mb-0 mt-0">
								<thead class="thead-light">
									<tr>
										<th>Parameter</th>
										<th>Keterangan</th>
										<th>Tipe Data</th>
										<th>Wajib</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>api_key</td>
										<td>API Key Anda.</td>
										<td><code>string</code></td>
										<td>Ya</td>
									</tr>
								</tbody>
							</table>
						</div>
						<hr />
						<h5>3. Response</h5>
						<div class="table-responsive">
							<table class="table table-bordered">
								<tr>
									<th width="50%">Contoh Respon Sukses</th>
									<th>Contoh Respon Gagal</th>
								</tr>
								<tr>
									<td>
										<pre>
											<code class="language-json">
												{!! $product_success !!}
					                    	</code>
										</pre>
									</td>
									<td>
										<pre>
											<code class="language-json">
												{!! $product_failed !!}
					                    	</code>
										</pre>
									</td>
								</tr>
							</table>
						</div>
					</div>
					<div class="tab-pane fade" id="tab-order">
						<h5>1. Endpoint</h5>
						<div class="table-responsive">
							<table class="table table-bordered table-hover mb-0 mt-0">
								<tr>
									<td>{{ url('api/order') }}</td>
								</tr>
							</table>
						</div>
						<hr />
						<h5>2. Request</h5>
						<div class="table-responsive">
							<table class="table table-borderless table-hover mb-0 mt-0">
								<thead class="thead-light">
									<tr>
										<th>Parameter</th>
										<th>Keterangan</th>
										<th>Tipe Data</th>
										<th>Wajib</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>api_key</td>
										<td>API Key Anda.</td>
										<td><code>string</code></td>
										<td>Ya</td>
									</tr>
									<tr>
										<td>product</td>
										<td>ID Produk, dapat dilihat di <a href="{{ url('page/product/list') }}" class="btn btn-sm btn-primary">Daftar Produk</a>.</td>
										<td><code>string</code></td>
										<td>Ya</td>
									</tr>
									<tr>
										<td>target</td>
										<td>Input dengan target anda, contoh: no. hp/id pln/id game,dsb.</td>
										<td><code>string</code></td>
										<td>Ya</td>
									</tr>
								</tbody>
							</table>
						</div>
						<hr />
						<h5>3. Response</h5>
						<div class="table-responsive">
							<table class="table table-bordered">
								<tr>
									<th width="50%">Contoh Respon Sukses</th>
									<th>Contoh Respon Gagal</th>
								</tr>
								<tr>
									<td>
										<pre>
											<code class="language-json">
												{!! $order_success !!}
					                    	</code>
										</pre>
									</td>
									<td>
										<pre>
											<code class="language-json">
												{!! $order_failed !!}
					                    	</code>
										</pre>
									</td>
								</tr>
							</table>
						</div>
					</div>
					<div class="tab-pane fade" id="tab-status">
						<h5>1. Endpoint</h5>
						<div class="table-responsive">
							<table class="table table-bordered table-hover mb-0 mt-0">
								<tr>
									<td>{{ url('api/status') }}</td>
								</tr>
							</table>
						</div>
						<hr />
						<h5>2. Request</h5>
						<div class="table-responsive">
							<table class="table table-borderless table-hover mb-0 mt-0">
								<thead class="thead-light">
									<tr>
										<th>Parameter</th>
										<th>Keterangan</th>
										<th>Tipe Data</th>
										<th>Wajib</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>api_key</td>
										<td>API Key Anda.</td>
										<td><code>string</code></td>
										<td>Ya</td>
									</tr>
									<tr>
										<td>id</td>
										<td>ID Pesanan.</td>
										<td><code>integer</code></td>
										<td>Ya</td>
									</tr>
								</tbody>
							</table>
						</div>
						<hr />
						<h5>3. Response</h5>
						<div class="table-responsive">
							<table class="table table-bordered">
								<tr>
									<th width="50%">Contoh Respon Sukses</th>
									<th>Contoh Respon Gagal</th>
								</tr>
								<tr>
									<td>
										<pre>
											<code class="language-json">
												{!! $status_success !!}
					                    	</code>
										</pre>
									</td>
									<td>
										<pre>
											<code class="language-json">
												{!! $status_failed !!}
					                    	</code>
										</pre>
									</td>
								</tr>
							</table>
						</div>
					</div>
					<div class="tab-pane fade" id="tab-example-class">
						<pre>
							<code class="language-php">
								{!! $example_class !!}
							</code>
						</pre>
					</div>
				</div>
            </div>
        </div>
    </div>
</div>
@endsection