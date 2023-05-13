<div style="overflow-y: scroll; max-height: 500px;">
    @foreach ($target->ticket_reply->sortByDesc('id') as $item)
    @if ($item->sender == 'Admin')
    <div class="alert alert-info text-dark">
        <div class="float-right">
            <b>Admin</b> (Admin)
        </div>
        <br />
        <div class="mb-2 mt-2 text-right"> 
            {!! $item->message !!} 
        </div>
        <div class="float-right" style="margin-top: -10px;">
            <small>
                {{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('d F Y - H:i') }}
                ({{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }})
            </small>
        </div>
        <br />
    </div>
    @else
    <div class="alert alert-warning text-dark">
        <div class="float-left">
            <b>{{ $item->user->username }}</b> ({{ $item->user->full_name }})
        </div>
        <br />
        <div class="mb-2 mt-2 text-left"> 
            {{ $item->message }} 
        </div>
        <div class="float-left">
            <small>
                {{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('d F Y - H:i') }}
                ({{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }})
            </small>
        </div>
        <br />
    </div>
    @endif
    @endforeach
    </div>
    <form method="post" action="{{ request()->url() }}" id="main_form">
        @method('patch')
        @csrf
        <div class="form-group">
            <label>Pesan <text class="text-danger">*</text></label>
            <textarea class="form-control custom-text-editor" name="message" rows="15">{{ old('message') }}</textarea>
            <small class="text-danger message_error"></small>
        </div>
        <hr />
        <div class="text-right">
            <button type="reset" class="btn btn-danger"><i class="fa fa-redo"></i> Reset</button>
            <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Submit</button>
        </div>
    </form>
</div>
<script src="{{ asset('assets/custom-form.js') }}"></script>
<script src="{{ asset('assets/custom-header.js') }}"></script>
<script src="{{ asset('assets/custom-footer.js') }}"></script>