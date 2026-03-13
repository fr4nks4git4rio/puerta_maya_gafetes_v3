

    <br>
    <br>

    <p class="text-center" style="font-size: 20px; line-height: 22px; color: black;">
        <b class="">TERMINAL MARITIMA PUERTA MAYA</b>
    </p>

    <p class="text-center" style="font-size: 16px; line-height: 17px; color: black; margin-right: 10mm">
        <b>
            ¿Qué debo hacer si tengo un accidente de trabajo dentro de la empresa? <br>

            1.- Avisar al jefe directo, en caso de no estar, avisar al supervisor de seguridad en turno. <br>

            2.- Acudir a la clínica del IMSS para atención médica. Si no puede ir solo, solicite que lo acompañen, o puede llamar a un familiar para que venga por usted. <br>

            NOTA: Si la primera atención médica es una clínica particular, deberá llevar los documentos al IMSS para solicitar su incapacidad.

        </b>
    </p>

<br>
<br>

    @if($folio != "")
    <small style="color: #000;"> FOLIO: {{$folio}}</small>

    <small class="pull-right" style="color: #000;"> NSS: {{$gafete->Empleado->empl_nss ?? ""}}</small>

    @endif


