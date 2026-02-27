@props(['route', 'active', 'ariaLabel' => null])
<a href="{{route($route)}}" @if($ariaLabel) aria-label="{{ $ariaLabel }}" @endif class=" {{ request()->routeIs($active) ? 'bg-byolink-3' : 'bg-byolink-1' }} w-full py-1.5 rounded-md text-center text-white font-black hover:bg-byolink-3 duration-300 relative">
    <div class=" absolute top-1/2 -translate-y-1/2 left-2 w-4 aspect-square">
        {{$svg}}
    </div>
    <p>{{$slot}}</p>
    <div class=" absolute top-1/2 -translate-y-1/2 right-2 w-4 aspect-square">
        {{$svg}}
    </div>
</a>
