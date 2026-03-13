var APAlerts = new function () {

    this.notificationDriver = "toastr"; //[nifty,toastr]
    this.dialogDriver = "SweetAlert"; //[SweetAlert, Bootbox]

    this.init = function(){
        let $this = this;
        if($this.notificationDriver == 'toastr'){

            toastr.options = {
                closeButton: true,
                debug: false,
                newestOnTop: true,
                progressBar: true,
                positionClass: 'toast-bottom-center',
                preventDuplicates: true,
                onclick: null
            };

        }

    },

    this.handleServerError = function(message, error,event, settings){
        console.log('Handle Server Error');
        let $this = this;

        if(error.responseJSON != undefined){
            let title = message;
            html = '<pre class=" text-sm text-secondary"><b>'+error.responseJSON.exception+'</b>';
            html += '<br/><i>' + error.responseJSON.file + ' @' + error.responseJSON.line + "</i>";
            html += '<br/>' + error.responseJSON.message;
            html += '</pre>';
            toastr.error(html, title, {
                timeOut: 0,
                showDuration: 0,
                hideDuration: 800,
                positionClass: 'toast-bottom-full-width',
            });

        }else{
            $this.error({title:message,message:'Console debug...'});
            console.log(message,error,event, settings);
        }


    }

    ///////////////////////////////////////////////////////////////////////////
    // N O T I F I C A TI O N S
    ///////////////////////////////////////////////////////////////////////////
    this.error = function (options) {

        // options = options || {};
        let $this = this;
        let message = options.message || options;
        let title = options.title || null;

        if ($this.notificationDriver == 'toastr') {

            toastr.error(message, title, {
                timeOut: 3500
            });

        }

        if ($this.notificationDriver == 'nifty') {

            $.niftyNoty({
                type: "danger",
                container: "floating",
                title: options.title || undefined,
                message: options.message || options,
                closeBtn: true,
                timer: 3500
            });

        }

    };

    this.success = function (options) {
        // options = options || {};
        let $this = this;
        let message = options.message || options;
        let title = options.title || null;

        if ($this.notificationDriver == 'toastr') {

            toastr.success(message, title, { timeOut: 2000 });

        }

        if ($this.notificationDriver == 'nifty') {

            $.niftyNoty({
                type: "success",
                container: "floating",
                title: options.title || undefined,
                message: options.message || options,
                closeBtn: true,
                timer: 2000
            });

        }
    };

    this.info = function (options) {
        // options = options || {};
        let $this = this;
        let message = options.message || options;
        let title = options.title || null;

        if ($this.notificationDriver == 'toastr') {

            toastr.info(message, title, {
                timeOut: 2500
            });

        }

        if ($this.notificationDriver == 'nifty') {

            $.niftyNoty({
                type: "info",
                container: "floating",
                title: options.title || undefined,
                message: options.message || options,
                closeBtn: true,
                timer: 2500
            });

        }
    };

    this.warning = function (options) {
        // options = options || {};
        let $this = this;
        let message = options.message || options;
        let title = options.title || null;

        if ($this.notificationDriver == 'toastr') {

            toastr.warning(message, title, {
                timeOut: 3500
            });

        }

        if ($this.notificationDriver == 'nifty') {

            $.niftyNoty({
                type: "warning",
                container: "floating",
                title: options.title || undefined,
                message: options.message || options,
                closeBtn: true,
                timer: 3500
            });

        }
    };

    ///////////////////////////////////////////////////////////////////////////
    // D I A L O G S
    ///////////////////////////////////////////////////////////////////////////
    this.confirm = function (options) {
        options = options || {};

        if (this.dialogDriver == "SweetAlert"){
            swal({
                    title: options.title || '',
                    html: options.message|| '',
                    type: options.type   || 'warning',
                    showCancelButton: options.showCancelButton || true,
                    confirmButtonClass: 'btn-success btn-trans waves-effect',
                    cancelButtonClass: 'btn-secondary btn-trans waves-effect',
                    confirmButtonText: options.confirmText || 'Realizar acción',
                    cancelButtonText: options.cancelText || 'Cancelar',
                }).then(function (input) {
                    if (input.value == true) {
                        options.callback();
                    }else{
                        if(options.callbackDismiss != undefined){
                            options.callbackDismiss()
                        }
                    }

                });

        } else if (this.dialogDriver =="Bootbox"){
            bootbox.confirm(
                {
                    title: options.title || null,
                    size: options.size || null,
                    message: options.message,
                    buttons: {
                        confirm: {
                            label: 'Aceptar',
                            className: 'btn-primary'
                        },
                        cancel: {
                            label: 'No',
                            className: 'btn-default'
                        }
                    },
                    callback: function (result) {
                        if (result) {
                            options.callback();
                        } else {
                            if (options.callbackDismiss) {
                                options.callbackDismiss();
                            }
                        }
                }
            })
        }

    },

    this.input = function (options) {
        options = options || {};

        swal({
            title: options.title || '',
            text: options.message || '',
            input: options.typeInput || 'text',
            showCancelButton: options.btnCancel || false,
            confirmButtonText: options.confirmText || 'Aceptar',
            cancelButtonText: options.cancelText || 'Cancelar',
            cancelButtonColor: options.cancelButtonColor || '',
            showLoaderOnConfirm: options.loader || false,
            closeOnConfirm: options.closeOnConfirm || false,
            inputPlaceholder: options.placeholder || '',
            inputAttributes: options.attributes || {},
            preConfirm: options.promiseCallback,
            allowOutsideClick: false
        }).then(
            options.successCallback,
            function (dismiss) {}
        );
    };
};

APAlerts.init();
