<br>
<br>
<p class="text-center" style="font-size: 20px; line-height: 22px; color:black;">
    <b class="">TERMINAL MARITIMA PUERTA MAYA</b>
</p>

<div class="text-justify"
   style="font-size: 16px; line-height: 17px; color:black; margin-right: 10mm; margin-left: 2mm; text-align: center">
    {!! settings()->get('gft_back_text') !!}
    {{--<b>El presente gafete acredita exclusivamente al titular para el acceso a las áreas comunes de la Terminal Marítima Puerta Maya, debe portarse en un lugar visible, es intransferible y de uso obligatorio dentro de la terminal.</b>--}}

    {{--<br>--}}
    {{--<br>--}}

    {{--<b>Esta credencial es propiedad de puerta maya, podrá ser retirada en cualquier momento por la misma por así considerarlo conveniente y no constituye relación laboral con la empresa.</b>--}}
</div>

@if($folio != "")
    <small style="color: #000;"> FOLIO: {{$folio}}</small>

    @if($empleado->empl_nss)
        <small class="pull-right" style="color: #000;"> NSS: {{$empleado->empl_nss ?? ""}}</small>
    @endif

@endif


