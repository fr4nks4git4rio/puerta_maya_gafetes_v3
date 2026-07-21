{{-- <link rel="stylesheet" type="text/css" href="{{ asset('plugins/croppie/croppie.css') }}"/> --}}
@php
    $a_dias = [
        '1' => '1',
        '2' => '2',
        '3' => '3',
        '4' => '4',
        '5' => '5',
        '6' => '6',
        '7' => '7',
    ];
@endphp
<div class="container">

    {!! Form::model($permiso, ['id' => 'form-permiso', 'url' => $url, 'class' => 'form-horizontal']) !!}

    {!! Form::text('pmtt_id', null, ['class' => 'form-control d-none', 'placeholder' => '']) !!}
    {!! Form::text('pmtt_lcal_id', auth()->getUser()->Local->lcal_id, [
        'class' => 'form-control d-none',
        'placeholder' => '',
    ]) !!}


    <div class="row">
        <div class="col-md-6">



            <div class="form-group row">
                {!! Form::label('local', 'Local', ['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('local_numero', auth()->user()->Local->lcal_id, ['class' => 'form-control', 'readonly' => true]) !!}
                </div>
                <div class="col-sm-5">
                    {!! Form::text('local', auth()->user()->Local->lcal_nombre_comercial, [
                        'class' => 'form-control',
                        'readonly' => true,
                    ]) !!}
                </div>
            </div>

            <div class="form-group row">
                {!! Form::label('pmtt_fecha', 'Fecha Solcitud', ['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-8">
                    {!! Form::text('pmtt_fecha', date('Y-m-d H:i:s'), [
                        'class' => 'form-control',
                        'placeholder' => '',
                        'readonly' => true,
                    ]) !!}
                </div>
            </div>


            <div class="form-group row">
                {!! Form::label('pmtt_solicitante', 'Solicitante', ['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-8">
                    {!! Form::text('pmtt_solicitante', Auth::user()->name, ['class' => 'form-control', 'readonly' => true]) !!}
                </div>
            </div>



            <div class="form-group row">
                {!! Form::label('pmtt_empresa', 'Empresa/Contratista', ['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-8">
                    {!! Form::text('pmtt_empresa', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                </div>
            </div>
            <div class="form-group row">
                {!! Form::label('pmtt_representante', 'Representante', ['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-8">
                    {!! Form::text('pmtt_representante', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                </div>
            </div>

            <div class="form-group row">
                <input type="hidden" name="pmtt_listado_trabajadores" id="pmtt_listado_trabajadores"
                    value="{{ json_encode($trabajadores) }}">
                <div class="w-100">
                    {!! Form::label('pmtt_listado_trabajadores', 'Listado de trabajadores', ['class' => 'control-label']) !!}
                    <button type="button" id="btn-add-trabajador"
                        class="btn btn-primary float-right mr-2 btn-sm mt-1">Nuevo
                        Trabajador</button>
                </div>
                <table class="table table-striped table-condensed table-sm" id="listado_trabajadores">
                    <thead>
                        <tr>
                            <th class="d-none"></th>
                            <th>Trabajador</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>


        </div>
        <div class="col-md-6">




            <div class="form-group row">
                {!! Form::label('pmtt_vigencia_inicial', 'Fecha Inicio', ['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-3">
                    {!! Form::text('pmtt_vigencia_inicial', Carbon\Carbon::now()->format('Y-m-d'), [
                        'class' => 'form-control',
                        'placeholder' => '',
                    ]) !!}
                </div>
                {!! Form::label('hora_inicio', 'Hora', ['class' => 'col-sm-1 control-label']) !!}
                <div class="col-sm-4">
                    {!! Form::time('hora_inicio', Carbon\Carbon::now()->format('H:i'), [
                        'class' => 'form-control',
                        'placeholder' => '',
                    ]) !!}
                </div>
            </div>

            <div class="form-group row">
                {!! Form::label('pmtt_dias', 'Dias Solicitados', ['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-8">
                    {{--                    {!! Form::text('pmtt_dias', 7 ,["class"=>"form-control", "placeholder" => ""]);!!} --}}
                    {!! Form::select('pmtt_dias', $a_dias, 7, ['class' => 'form-control']) !!}
                </div>
            </div>

            <div class="form-group row">
                {!! Form::label('pmtt_vigencia_final', 'Fecha Fin', ['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-3">
                    {!! Form::text('pmtt_vigencia_final', '', [
                        'class' => 'form-control',
                        'disabled' => true,
                    ]) !!}
                </div>
                {!! Form::label('hora_fin', 'Hora', ['class' => 'col-sm-1 control-label']) !!}
                <div class="col-sm-4">
                    {!! Form::time('hora_fin', Carbon\Carbon::now()->format('H:i'), [
                        'class' => 'form-control',
                        'placeholder' => '',
                    ]) !!}
                </div>
            </div>

            <div class="form-group row">
                {!! Form::label('pmtt_tmtt_id', 'Tipo de Mantenimiento', ['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-8">
                    {!! Form::select('pmtt_tmtt_id', $tiposMantenimiento, null, [
                        'class' => 'form-control select2-control',
                        'placeholder' => 'Seleccione...',
                        'style' => 'width: 100%',
                    ]) !!}
                </div>
            </div>
            <div class="form-group row">
                {!! Form::label('pmtt_tesp_id', 'Trabajo Específico', ['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-8">
                    {!! Form::select('pmtt_tesp_id', [], null, [
                        'class' => 'form-control select2-control',
                        'placeholder' => 'Seleccione...',
                        'style' => 'width: 100%',
                    ]) !!}
                    <div class="alert alert-info mb-1" id="resumen_trab_esp" style="display: none"></div>
                    <div class="alert alert-danger mb-0" id="resumen_trab_esp_riesgo" style="display: none"></div>
                </div>
            </div>

            <div class="form-group row">
                {!! Form::label('pmtt_trabajo', 'Descripción de las actividades a realizar', [
                    'class' => 'col-sm-4 control-label',
                ]) !!}
                <div class="col-sm-8">
                    {!! Form::textarea('pmtt_trabajo', null, ['class' => 'form-control', 'size' => '50x2']) !!}
                </div>
            </div>


        </div>
    </div>



    {!! Form::close() !!}

</div>

<script type="text/javascript">
    var pmtt_tesp_id = '{{ $permiso ? $permiso->pmtt_tesp_id : '' }}';
    $(document).ready(function() {

        jModal = {
            modal: $('#modal-form'),
            form: '#form-permiso',

            init: function() {
                let $this = this;

                $('.select2-control').select2({
                    'allowClear': true,
                    placeholder: "Seleccione",
                    width: '100%'
                });

                $('#modal-btn-ok', $this.modal).click(function() {
                    $this.handleSubmit();
                });

                $('#pmtt_vigencia_inicial').datepicker({
                    autoclose: true,
                    format: 'yyyy-mm-dd',
                    language: '{{ App::getLocale() }}'
                });

                $('#pmtt_vigencia_final').datepicker({
                    autoclose: true,
                    format: 'yyyy-mm-dd',
                    language: '{{ App::getLocale() }}'
                });

                calcularFechaFin();

                if ($('#pmtt_listado_trabajadores').val() != " ") {
                    initListadoTrabajadores($('#pmtt_listado_trabajadores').val());
                }

                if ($('#pmtt_tmtt_id').val()) {
                    loadTrabajosEspecificos($('#pmtt_tmtt_id').val(), pmtt_tesp_id);
                }

                $('#pmtt_vigencia_inicial, #pmtt_dias, #hora_inicio', $this.modal).change(function(
                    event) {
                    calcularFechaFin();
                });

                $('#pmtt_representante', $this.modal).change(function(event) {
                    if (event.target.value)
                        addLineaTrabajador('', event.target.value);
                })

                $('#btn-add-trabajador', $this.modal).click(function() {
                    addLineaTrabajador();
                });

                $('#pmtt_tmtt_id', $this.modal).change(function(event) {
                    if (event.target.value)
                        loadTrabajosEspecificos(event.target.value);
                });

                $('#pmtt_tesp_id', $this.modal).change(function(event) {
                    $('#resumen_trab_esp').html('');
                    $('#resumen_trab_esp_riesgo').html('');
                    $('#resumen_trab_esp').css('display', 'none');
                    $('#resumen_trab_esp_riesgo').css('display', 'none');
                    if (event.target.value)
                        $.getJSON('/get-resumen-trab-esp/' + event.target.value,
                            function(data) {
                                var html = '';
                                var htmlRiesgo = '';
                                if (data.epp_basicos)
                                    html += '<b>EPP Básicos:</b> ' + data.epp_basicos + '<br>'
                                if (data.epp_especificos)
                                    html += '<b>EPP Específicos:</b> ' + data.epp_especificos + '<br>'
                                if (data.tipo_riesgo)
                                    htmlRiesgo += '<b>Tipo de Riesgo:</b> ' + data.tipo_riesgo
                                $('#resumen_trab_esp').html(html);
                                $('#resumen_trab_esp').css('display', 'block');
                                $('#resumen_trab_esp_riesgo').html(htmlRiesgo);
                                $('#resumen_trab_esp_riesgo').css('display', 'block');
                            });
                });

                function loadTrabajosEspecificos(tmtt_id, pmtt_tesp_id = null) {
                    var $select = $('#pmtt_tesp_id');
                    $select.empty();
                    $('#resumen_trab_esp').html('');
                    $('#resumen_trab_esp').css('display', 'none');
                    $.getJSON('/load-trab-esp-by-tipo-mante/' + tmtt_id,
                        function(data) {
                            $select.append('<option value="">Seleccione...</option>');
                            $select.select2({
                                data: data
                            });
                            if (pmtt_tesp_id)
                                $select.val(pmtt_tesp_id);
                            $select.trigger('change');
                        });
                }

                function initListadoTrabajadores(trabajadores) {
                    trabajadores = JSON.parse(trabajadores);
                    for (var i = 0; i < trabajadores.length; i++) {
                        addLineaTrabajador(trabajadores[i].pmtb_id, trabajadores[i].pmtb_nombre,
                            trabajadores[i].pmtb_nss);
                    }
                }

                function calcularFechaFin() {

                    fecha_inicial = $('#pmtt_vigencia_inicial').val();
                    dias = $('#pmtt_dias').val();
                    if (fecha_inicial && dias) {
                        inicio = new Date(fecha_inicial);
                        fin = new Date(inicio.getTime() + 1000 * 60 * 60 * 24 * (dias - 1));
                        anio = fin.getFullYear();
                        mes = fin.getMonth() + 1;
                        mes = mes < 10 ? ('0' + mes) : mes;
                        dia = fin.getDate() + 1;
                        dia = dia < 10 ? ('0' + dia) : dia;
                        $('#pmtt_vigencia_final').val(anio + '-' + mes + '-' + dia);
                    } else {
                        $('#pmtt_vigencia_final').val('');
                    }
                }

                function addLineaTrabajador(id = '', nombre = '', nss = '') {
                    table = document.getElementById('listado_trabajadores');
                    tbody = table.querySelector('tbody');
                    tr = document.createElement('tr');

                    tdId = document.createElement('td');
                    tdId.classList = 'd-none';
                    if (id) {
                        tdId.textContent = id;
                    }

                    tdNombre = document.createElement('td');
                    inputNombre = document.createElement('input');
                    inputNombre.name = 'nombre';
                    inputNombre.classList = 'form-control';
                    if (nombre != '') {
                        inputNombre.value = nombre;
                    }
                    tdNombre.appendChild(inputNombre);

                    // tdNss = document.createElement('td');
                    // inputNss = document.createElement('input');
                    // inputNss.name = 'nss';
                    // inputNss.classList = 'form-control';
                    // if (nss != '') {
                    //     inputNss.value = nss;
                    // }
                    // tdNss.appendChild(inputNss);

                    tdAcciones = document.createElement('td');
                    tdAcciones.classList = 'align-content-center';
                    buttonDel = document.createElement('button');
                    buttonDel.classList = 'btn btn-danger btn-xs';
                    buttonDel.textContent = "Eliminar";
                    buttonDel.type = "button";
                    buttonDel.onclick = function() {
                        // Buscamos el elemento <tr> más cercano hacia arriba en el DOM
                        const fila = this.closest('tr');
                        // Si existe la fila, la eliminamos
                        if (fila) {
                            fila.remove();
                        }
                    };
                    tdAcciones.appendChild(buttonDel);

                    tr.appendChild(tdId);
                    tr.appendChild(tdNombre);
                    // tr.appendChild(tdNss);
                    tr.appendChild(tdAcciones);
                    tbody.appendChild(tr);

                    // $('input[name="nss"]', $this.modal).keydown(function(e) {
                    //     // Permitir teclas de control (Retroceso, Tab, Flechas, etc.)
                    //     if (['Backspace', 'Tab', 'ArrowLeft', 'ArrowRight', 'Delete'].includes(e
                    //             .key)) {
                    //         return;
                    //     }

                    //     // Si no es un número del 0 al 9, prevenir la acción por defecto
                    //     if (!/[0-9]/.test(e.key)) {
                    //         e.preventDefault();
                    //     }
                    // });
                }

            },


            handleSubmit: function() {

                let $this = this;
                let url = $($this.form).attr('action');

                const tabla = document.getElementById('listado_trabajadores');
                const datos = [];

                // Iteramos por todas las filas del cuerpo de la tabla (tbody)
                // Usamos Array.from para poder usar .forEach
                var con_campos_vacios = false;
                Array.from(tabla.querySelectorAll('tbody tr')).forEach(fila => {

                    // Extraemos el texto de las celdas
                    // fila.cells[0] es la primera columna, [1] la segunda, etc.
                    const id = fila.cells[0].textContent;
                    const nombre = fila.cells[1].querySelector('input').value;
                    // const nss = fila.cells[2].querySelector('input').value;
                    if (!$.trim(nombre))
                        con_campos_vacios = true;

                    datos.push({
                        id: id,
                        nombre: nombre
                    });
                });

                if (con_campos_vacios) {
                    APAlerts.error("Existen campos vacíos en el listado de trabajadores");
                    return;
                }
                $('#pmtt_listado_trabajadores').val(JSON.stringify(datos));
                // let form = $($this.form)[0];

                $.ajax({
                    url: url,
                    method: 'POST',
                    // data: new FormData(form),
                    data: $($this.form).serialize(),
                    // contentType: false,
                    // cache: false,
                    // processData:false,
                    beforeSend: function() {
                        $('.input-error').remove();
                    },
                    success: function(res) {

                        if (res.success === true) {
                            APAlerts.success(res.message);
                            dTables.oTable.draw();
                            $('#modal-btn-close').click();

                        } else {

                            if (typeof res.message !== "undefined") {
                                APAlerts.error(res.message);
                                handleFormErrors($this.form, res.errors);
                            } else {
                                APAlerts.error(res);
                            }

                        }
                    }
                });

            }
        };

        jModal.init();

    });
</script>
