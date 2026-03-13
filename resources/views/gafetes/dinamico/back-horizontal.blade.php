<br>
<br>
<p class="text-center" style="font-size: 20px; line-height: 20px; color:black;">
    <b class="">TERMINAL MARITIMA PUERTA MAYA</b>
</p>

<div  style="margin-right: 10mm; color: #000; text-align: justify">
    {!! settings()->get('gft_back_text') !!}
</div>

{{--<div style="margin-right: 10mm; color: #000" class="text-center">--}}
{{--    @if($empleado->empl_nss)--}}
{{--        <p class="text-left" style="color: #000;"> NSS: {{$empleado->empl_nss ?? ""}}</p>--}}
{{--    @endif--}}
{{--</div>--}}

{{--@if($folio != "")--}}
{{--<small style="color: #000; font-size: 14px"> FOLIO: <strong>{{$folio}}</strong></small>--}}
<div style="text-align: center; margin-top: 5px; margin-right: 3mm">
    {!! QrCode::size(60)->errorCorrection('L')->generate($gafete->toStringQr()); !!}
</div>

{{--<small class="pull-right" style="color: #000;"> NSS: {{$empleado->empl_nss ?? ""}}</small>--}}

{{--@endif--}}

{{--@endif--}}


