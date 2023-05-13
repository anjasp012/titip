@if (Session::has('information_popup'))
@php
$information = \App\Models\WebsiteInformation::where('is_popup', '1')->orderBy('id', 'desc')->limit('5')->get();
@endphp
@if ($information->count() > 0)
<div class="modal fade" id="modal-info" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title mt-0"><i class="fa fa-info-circle fa-fw"></i> Informasi Terbaru</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" style="overflow-y: scroll; max-height: 500px;">
                @foreach ($information as $value)
                <div class="alert alert-info text-dark">
                    <div class="float-left">
                        {!! category($value->category) !!}
                    </div>
                    <br />
                    <div class="mb-2 mt-2 text-left"> 
                        {!! nl2br($value->content) !!}
                    </div>
                    <div class="float-left">
                        <small>
                            {{ \Carbon\Carbon::parse($value->created_at)->translatedFormat('d F Y - H:i') }}
                        </small>
                    </div>
                    <br />
                </div>
                @endforeach
                @if ($information->count() >= 5)
                <div class="text-center">
                    <a href="{{ url('page/information') }}" class="text-primary">Lihat semua...</a>
                </div>
                @endif     
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-success" onclick="read_popup()"><i class="fa fa-thumbs-up"></i> Saya Sudah Membaca</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $('#modal-info').modal('show');
    function read_popup() {
        $.ajax({
            type: "GET",
            data: "true",
            url: "{{ url('ajax/read_popup') }}",
            success: function() {
                $('#modal-info').modal('hide');
            },
            error: function() {
                alert('Terjadi kesalahan.');
            }
        });
    }
</script>
@endif
@endif