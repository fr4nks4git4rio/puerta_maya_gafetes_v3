@php
    $items = Navigation::tree();
    $user = auth()->getUser();


    // dd($items, $navigation);
@endphp

{{-- <ul class="navigation-menu hidden-print"> --}}

    @foreach($user->roles as $role)

        @php
        $navigation = Navigation::getNavigationByRole($role->id);
        @endphp

        <li class="text-muted menu-title role-title">{{$role->name}}</li>
        {{-- <li class="role-title">
            <a href="#">{{$role->name}}</a>
        </li> --}}
        {{-- <span class="menu-title role-title"></span> --}}
        {{-- <span class="role-nav-items"> --}}

        @foreach($items as $item)

            @if(count($navigation->where('navigation_id',$item->id))> 0)
                @if($item->header == 1 && $item->route!="home/")

                    @include('layouts.nav.header_item',['item'=>$item,'navigation'=>$navigation])

                @endif

            @endif

        @endforeach
        {{-- </span> --}}

    @endforeach

{{-- </ul> --}}


