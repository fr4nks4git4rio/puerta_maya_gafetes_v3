@php
    $items = Navigation::tree();
    $user = auth()->getUser();
    $navigation = $user->getNavigation();

    // dd($items, $navigation);
@endphp

{{-- <ul class="navigation-menu hidden-print"> --}}

    @foreach($items as $item)

        @if(count($navigation->where('navigation_id',$item->id))> 0)
            @if($item->header == 1)

                @include('layouts.nav.header_item',['item'=>$item,'navigation'=>$navigation])

            @endif

        @endif

    @endforeach

{{-- </ul> --}}
