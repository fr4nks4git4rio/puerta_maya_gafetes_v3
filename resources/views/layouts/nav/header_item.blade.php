@if(count($item['children'])>0)

    <li class="has_sub">

        <a href="{!! ($item->route == "")? "#" : url($item->route) !!}">
            <i class="{{$item->icon_class}}"></i>
            <span> {{$item->title_es}}</span>
                <span class="menu-arrow"></span>
        </a>

        <ul class="list-unstyled">
        @foreach( $item['children']->sortBy('weight') as $child)

            @if(count($navigation->where('navigation_id',$child->id))> 0)
                @if($child['header']==2)
                    @include('layouts.nav.subheader_item',['item'=>$child,'navigation'=>$navigation])
                @else
                    @include('layouts.nav.route_item',['item'=>$child,'navigation'=>$navigation])
                @endif
            @endif

        @endforeach
        </ul>
    </li>

@else

    <li class="">
        <a href="{!! url($item->route) !!}">
            <i class="{{$item->icon_class}}"></i>
            <span> {{$item->title_es}} </span> </a>
    </li>
@endif

