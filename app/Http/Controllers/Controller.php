<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $data;
    protected $errors;

    protected $etiquetas = [];
    protected $rules = [];


    /**
     * Undocumented function
     *
     * @param string $action
     * @return void
     */
    protected function getRules(string $action)
    {
        // dd($action,2);
        return $this->rules[$action];
    }

    /**
     * Undocumented function
     *
     * @param string $action
     * @return boolean
     */
    protected function validateAction(string $action)
    {
        // dd($action);
        $rules = $this->getRules($action);

        $etiquetas = $this->etiquetas;

        $this->data = array_only($this->data,array_keys($rules));

        $validation = \Validator::make($this->data, $rules, array(), $etiquetas);

        $isValid = $validation->passes();

        $this->errors = $validation -> messages();

        return $isValid;
    }


    /**
     * Undocumented function
     *
     * @param boolean $success
     * @param string $mensaje
     * @param [type] $data
     * @return void
     */
    protected function ajaxResponse(bool $success = true, string $mensaje = null, $data = null)
    {
        if($mensaje == null)
        {
            $mensaje = ($success)? 'Operación Exitosa' : 'Error en la aplicación...';
        }


        $debug = \Config::get('app.debug');

        $return = [
            'success' => $success,
            'message' => $mensaje,
            'errors' => $this->errors,
            'debug_mode' => $debug
        ];

        if($data !== null)
        {
            $return['data'] = $data;
        }

        if($success === false && $debug == true )
        {
            $return['request-data'] = $this->data;
            $return['labels']  = $this->etiquetas;
        }

        return $return;

    }


}
