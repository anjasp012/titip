@if (Session::has('result'))
@php
if (Session::get('result')['alert'] == 'danger') {
	$icon_class = '<i class="fa fa-times-circle"></i>';
} else {
	$icon_class = '<i class="fa fa-check-circle"></i>';
}
@endphp
<div class="row">
	<div class="col-lg-12">
		<div class="alert alert-dismissable alert-{{ Session::get('result')['alert'] }} text-dark">
		    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
		    <b>{!! $icon_class !!} {{ Session::get('result')['title'] }}:</b> {!! Session::get('result')['message'] !!}
		</div>
	</div>
</div>
@endif