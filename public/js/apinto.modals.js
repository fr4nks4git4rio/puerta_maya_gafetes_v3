var APModal = {

    modal: null,

    /**
     * options = {
     * url: '',
     * params: {},
     * title: 'Win',
     * dom : 'modal-dlg',
     * buttons : { ok : true, cl : true  },
     * size: 'modal-md',
     * backdrop: 'static',
     * headerClass : 'bg-olive'
     *
     * }
     */
    open: function (options) {

        let $this = this;
        var w = $(window).width();
        var dom = options.dom || 'modal-dlg';
        var bgClass = options.bgClass || '';
        var size = options.size || 'modal-md';
        var title = options.title || 'Ventana emergente';
        // var backdrop = options.backdrop || true;
        var backdrop = options.backdrop || 'static';
        var bOk = options.bOk || true;
        var bCa = true;
        var bOkLabel = options.bOkLabel || 'Aceptar';

        if (options.hasOwnProperty('bOk')) {
            bOk = options.bOk;
        }
        if (options.hasOwnProperty('bCa')) {
            bCa = options.buttons.bCa;
        }

        var html = '';
        html = '<div class="modal fade" id="' + dom + '" role="dialog" aria-hidden="true">';
        html += '  <div class="modal-dialog ' + size + '">';
        html += '    <div class="modal-content">';
        html += '      <div class="modal-header ' + bgClass + '">';
        html += '        <h4 class="modal-title" id="myModalLabel">Modal title</h4>';
        html += '        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>';
        html += '      </div>';
        html += '      <div class="modal-body">';
        html += '        Loading...';
        html += '      </div>';
        html += '      <div class="modal-footer">';
        if (bCa) {
            html += '        <button type="button" id="modal-btn-close" class="btn btn-secondary btn-trans waves-effect pull-left" data-dismiss="modal"><i class="pli-close"></i>  Cerrar</button>';
        }

        if (bOk) {
            html += '        <button type="button" id="modal-btn-ok" class="btn btn-success btn-trans waves-effect"><i class="pli-yes"></i> ' + bOkLabel + '</button>';
        }
        html += '      </div>';
        html += '    </div>';
        html += '  </div>';
        html += '</div>';

        $('body').append(html);
        $('#' + dom + ' h4').html(title);

        $this.modal = $('#' + dom).modal({
            keyboard: false,
            backdrop: 'static'
        }).show();


        $('#' + dom + ' .modal-body').css({
            overflow: 'auto',
            'min-height': '300px'
        });


        $('#' + dom).on('show.bs.modal', function () {
            $(this).find('.modal-body').css({
                'max-height': '100%',
                'max-width': '100%'
            });
        });

        $this.modal.on('hidden.bs.modal', function () {
            $('#' + dom).remove();
        });

        $this.load(options);
        return $this.modal;
    },



    load: function (options) {
        let $this = this;
        let element = options.dom || 'modal-dlg';
        let modal = $("#" + element);
        let body = $this.modal.find('.modal-body');

        if (options.url) {
            //set a new Ajax request
            $.ajax({
                url: options.url,
                data: options.params,
                type: options.method || "POST",
                dataType: "html",
                beforeSend: function () {
                    body.html('<div style="height:100%; padding-top:20%; text-align:center;"><span style="font-weight:bold; font-size: 2em;" class="ajax-loading-animation"><i class="fa fa-cog fa-spin"></i> Cargando ...</span></div>');
                },
                success: function (data, textStatus, xhr) {
                    body.html(data);
                },
                complete: function (jqXHR, textStatus) {

                },
                error: function (jqXHR, textStatus, error) {
                    body.html('<h4 class="ajax-loading-error"><i class="fa fa-warning text-danger"></i> Error requesting <span class="txt-color-red">' + textStatus + "</span>: " + jqXHR.status + ' <span style="text-transform: capitalize;">' + error + "</span></h4>")
                }

            });
        } else if (options.html) {
            body.html(options.html);
        }

    }


    /**
     *
     * @param cls
     */
    // this.setSize = function (cls) {
    //     console.log(this.modal)
    // };

}

//ocultar tooltips al abrir modal
// $('body').on('show.bs.modal', function () {
//     $('[data-toggle="tooltip"], .tooltip').tooltip("hide");
// });
