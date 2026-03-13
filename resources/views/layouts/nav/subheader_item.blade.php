@if(count($item['children'])>0)

    <li class="has-sub">

        <a href="{!! ($item->route == "")? "#" : url($item->route) !!}">
            {{$item->title_es}}
        </a>

        <ul class="list-unstyled">
        @foreach( $item['children']->sortBy('weight') as $child)

            @if(count($navigation->where('navigation_id',$child->id))> 0)
                @include('layouts.nav.route_item',['item'=>$child])
            @endif

        @endforeach
        </ul>
    </li>

@else

     @include('layouts.nav.route_item',['item'=>$item])
@endif
