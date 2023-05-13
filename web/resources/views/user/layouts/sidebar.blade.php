<div class="col-lg-2 d-none d-sm-block">
    <ul class="sidebar-nav" id="sidebar-nav">
        <li class="nav-heading">Mapel</li>
        @foreach (\App\Models\PostCategory::get() as $item)
        <li class="nav-item">
            <a class="nav-link collapsed" href="{{url('categories/'.$item->slug)}}">
                <i class="bi bi-grid"></i>
                <span>{{$item->name}}</span>
            </a>
        </li>
        @endforeach
    </ul>
</div>
