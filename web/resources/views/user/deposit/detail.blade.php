@extends('user.layouts.app')
@section('content')

    <div class="col-md-3 mb-3">
        @include('user.layouts.left_profile')
    </div>
    <div class="col-md-9">
        
		
<div class="mb-5 pb-5">
	<div class="">
		<div class="container pt-3">
			<div class="card">
				<div class="card-body">
					<h5 class="card-title">Deposit ID : # {{$target->id}}</h5>
						{!! status($target->status) !!}
						<hr>
						
						<div class="text-center">
							<img src="{{ asset('assets/images/bank/'.$deposit_methods->merchant.'.png') }}" class="mt-3 methodlight" width="100">
							<br>
							@if ($target->qr_image != null)
							<img src="{{$target->qr_image}}" class="methodlight img-fluid mt-2">
							@endif
							@if ($target->pay_code != null)
								<div class="my-2">Kode Bayar</div>
								<input type="text" class="form-control text-center font-weight-bold" value="{{$target->pay_code}}" disabled>
								<div class="mt-2">
									<button onclick="copytext('{{$target->pay_code}}')" class="btn btn-primary btn-sm">Salin Nomor</button>
								</div>
							@endif
						</div>
                        
				</div>
			</div><!-- End Default Card -->

			  
			
            <div class="text-center text-dark">
                <div class="my-2"><b>Instruksi</b></div>
            </div>
			
				
					
                    @php
                        $msg = json_decode($target->server_note);
						if($msg){
							foreach ($msg as $key) {
								echo '<div class="card mt-2">
										<div class="card-header text-dark bold">
										'.$key->title.'
									</div>
									<div class="card-body">';
								echo "<ul>";
								for($i=0;$i<count($key->steps);$i++){
									echo "<li>".$key->steps[$i]."</li>";
								}
								echo "</ul>";
								echo "</div></div>";
							}
						}
                        
                        //var_dump($msg[0]);
                    @endphp
					
				</div>
			</div>
			
		</div>
	</div>
</div>

</div>

@endsection
@section('script')
<script>
	function copytext(data) {
	var $temp = $("<input>");
		$("body").append($temp);
		$temp.val(data).select();
		document.execCommand("copy");
		$temp.remove();
	} 
</script>
@endsection
