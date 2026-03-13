
    <br>
    <p class="text-center" style="font-size: 20px; line-height: 20px; color:black;">
        <b class="">TERMINAL MARITIMA PUERTA MAYA</b>
    </p>

    <div  style="margin-right: 10mm; color: #000; text-align: justify">
        {!! settings()->get('gft_back_text') !!}
    </div>

{{--    @if($folio != "")--}}
{{--        <small style="color: #000;"> FOLIO: {{$folio}}</small>--}}

{{--        @if($empleado->empl_nss)--}}
{{--        <small class="pull-right" style="color: #000;"> NSS: {{$empleado->empl_nss ?? ""}}</small>--}}
{{--        @endif--}}

{{--    @endif--}}
    <div class="text-center">
        {!! QrCode::size(60)->generate($gafete->toStringQr()); !!}
    </div>


