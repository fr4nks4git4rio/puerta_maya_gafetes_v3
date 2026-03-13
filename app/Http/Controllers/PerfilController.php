<?php

namespace App\Http\Controllers;

use App\Local;
use Illuminate\Http\Request;

use App\Cuenta;
use App\Condominio;
use App\Concepto;
use App\User;


use App\Reports\EstadoCuentaReport;

class PerfilController extends Controller
{

    protected $rules = [
        'change-password' => [

            'current_password'  => 'required',
            'new_password'      => [
                'required',
                'min:8',             // must be at least 8 characters in length
                'regex:/[a-z]/',      // must contain at least one lowercase letter
                'regex:/[A-Z]/',      // must contain at least one uppercase letter
                'regex:/[0-9]/',      // must contain at least one digit
                'regex:/[-_@$!%*#?&]/', // must contain a special character
            ],

            'repeat_password'   => 'required|same:new_password',

        ],

        'update-user-info' => [

            'usr_id'  => 'required',
            'usr_name'  => 'required',
            'usr_telefono'  => 'required|numeric'

        ],

        'update-local-info' => [

            'usr_id'  => 'required',
            'lcal_id'    => 'required|exists:locales,lcal_id',
            'lcal_razon_social'      => 'required',
            'lcal_rfc'    => 'required|max:13'

        ],
    ];

    protected $etiquetas = [
        'current_password'  => 'Contraseña actual',
        'new_password'      => 'Contraseña nueva',
        'repeat_password'   => 'Contraseña nueva',
        'user_id'  => 'Usuario',
        'usr_name'  => 'Nombre',
        'usr_telefono'  => 'Teléfono',

        'lcal_id'    => 'Local',
        'lcal_razon_social'      => 'Razón Social',
        'lcal_rfc'    => 'RFC'
    ];

    public function __construct()
    {
        $this->middleware('auth');
        $this->data = request()->all();
    }


    public function index(){

        $data = [
            'url_change_password' => url('profile/change-password'),
            'url_change_user_info' => url('profile/change-user-info'),
            'url_change_local_info' => url('profile/change-local-info'),
            'user' => auth()->getUser()
        ];

        return view('web.perfil.index')->with($data);
    }

    public function doChangePassword(Request $request)
    {

        $this -> action = 'change-password';

        if(! $this->validateAction('change-password'))
        {
            return response() -> json($this -> ajaxResponse(FALSE,'Errores en formulario!'));
        }

        $user = \Auth::getUser();

        if(! \Hash::check($this->data['current_password'],$user -> password) )
        {
            return response() -> json($this -> ajaxResponse(FALSE,'Current password is incorrect'));
        }


        $user -> password = \Hash::make($this->data['new_password']);
        $user -> save();

        return response() -> json($this -> ajaxResponse(true,'password updated!'));

    }

    public function doChangeUserInfo(Request $request)
    {

        if(! $this->validateAction('update-user-info'))
        {
            return response() -> json($this -> ajaxResponse(FALSE,'Errores en formulario!'));
        }

        $user = \Auth::getUser();

        $user->name = $this->data['usr_name'];
        $user->telefono = $this->data['usr_telefono'];

        $user -> save();

        return response() -> json($this -> ajaxResponse(true,'Usuario actualizado!'));

    }

    public function doChangeLocalInfo(Request $request)
    {

        return response() -> json($this -> ajaxResponse(FALSE,'Funcionalidad desactivada'));

        if(! $this->validateAction('update-local-info'))
        {
            return response() -> json($this -> ajaxResponse(FALSE,'Errores en formulario!'));
        }
        $user = \Auth::getUser();

        $local = Local::find($this->data['lcal_id']);

        if($local->lcal_id == $user->Local->lcal_id ){

            $local->lcal_rfc = $this->data['lcal_rfc'];
            $local->lcal_razon_social = $this->data['lcal_razon_social'];
            $local->save();

        }


        return response() -> json($this -> ajaxResponse(true,'Información del local actualizada!'));

    }

}
