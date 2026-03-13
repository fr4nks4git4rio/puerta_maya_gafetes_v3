@php
    $items = Navigation::tree();
    $user = auth()->getUser();
    $navigation = $user->getNavigation();
    // dd($user->getNavigation());
@endphp

<ul id="mainnav-menu" class="list-group hidden-print">

    @foreach($items as $item)

        @if(count($navigation->where('navigation_id',$item->id))> 0)
            @if($item->header == 0)
                @include('layouts.nav.route_item',['item'=>$item])
            @else
                <li class="list-divider"></li>
                @include('layouts.nav.header_item',['item'=>$item])
            @endif

                @foreach( $item['children']->sortBy('weight') as $child)
                    @if(count($navigation->where('navigation_id',$child->id))> 0)
                        @include('layouts.nav.route_item',['item'=>$child])
                    @endif
                @endforeach
        @endif

    @endforeach

    {{-- <hr> --}}

    <br>

    <br>

    <br>
</ul>
