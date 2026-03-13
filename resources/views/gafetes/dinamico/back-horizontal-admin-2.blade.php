<br>
<br>
@if($es_vertical)
    <br>
@endif

<p class="text-center" style="font-size: 20px; line-height: 20px; color: black;">
    <b class="">TERMINAL MARITIMA PUERTA MAYA</b>
</p>
<div style="margin-right: 10mm; color: #000; text-align: center">
    {!! settings()->get('gft_admin_back_text') !!}
</div>
<div style="margin-right: 10mm; color: #000" class="text-center">
    <p class="text-center" style="color: #000;">NSS: <strong>{{$empleado->empl_nss ?? ""}}</strong></p>
</div>
<div style="text-align: center; padding-top: 30px; margin-right: 10mm">
    {!! QrCode::size(60)->errorCorrection('L')->generate($gafete->toStringQr()); !!}
</div>
{{--<p class="text-center" style="font-size: 16px; line-height: 17px; color: black; margin-right: 10mm; margin-left: 5mm;">--}}
{{--<b>Nombre o razón social</b>--}}
{{--<br>--}}
{{--COZUMEL CRUISE TERMINAL SA DE CV--}}

{{--<br>--}}
{{--<br>--}}

{{--<b>R.F.C</b>--}}
{{--<br>--}}
{{--CCT880315856--}}

{{--<br>--}}
{{--<br>--}}
{{--<b>Dirección</b>--}}
{{--<br>--}}
{{--Carretera a Chankanaab KM. 4.5, Cozumel, Quintana Roo Cp. 77600--}}

{{--</p>--}}

{{--@if($es_vertical)--}}
{{--    <br>--}}
{{--    <br>--}}
{{--@endif--}}

{{--@if($folio != "")--}}
{{--<small style="color: #000; font-size: 14px"> FOLIO: <strong>{{$folio}}</strong></small>--}}

{{--<small class="pull-right" style="color: #000;"> NSS: {{$empleado->empl_nss ?? ""}}</small>--}}

{{--@endif--}}


