@if(session()->has('result'))
@php
if(session()->get('result')['alert'] == 'danger') {
	$icon_class = '<i class="fa fa-times-circle"></i>';
} else {
	$icon_class = '<i class="fa fa-check-circle"></i>';
}
@endphp
<div class="row">
	<div class="col-lg-12">
		<div class="alert alert-dismissable alert-{{ session()->get('result')['alert'] }} text-dark">
		    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
		    <b>{!! $icon_class !!} {{ session()->get('result')['title'] }}:</b> {!! session()->get('result')['message'] !!}
		</div>
	</div>
</div>
@endif