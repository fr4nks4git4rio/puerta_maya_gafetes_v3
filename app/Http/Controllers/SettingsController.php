<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

// use App\Navigation;

class SettingsController extends Controller
{

    protected $rules = [
        'insert-role' => [
            'name' => 'required|alpha',
            'guard_name' => 'required',
        ],

        'edit-role' => [
            'id' => 'required|exists:roles',
            'name' => 'required|alpha',
            'guard_name' => 'required',
        ],

        'insert-permission' => [
            'name' => 'required|alpha_dash',
            'guard_name' => 'required',
        ],
        'edit-permission' => [
            'id' => 'required|exists:permissions',
            'name' => 'required|alpha_dash',
            'guard_name' => 'required',
        ],
        'delete-permission' => [
            'id' => 'required|exists:permissions',
        ]
    ];

    protected $etiquetas = [
        'id' => 'ID',
        'name' => 'Nombre',
        'guard_name' => 'Nombre del Guard',
        // 'guard_name'      => 'Nombre',
        'clie_telefono' => 'Telefono',
        'clie_email' => 'Email',
        'clie_direccion' => 'Direccion',
        'clie_direccion_cuba' => 'Direccion destino',
        'clie_pasaporte' => 'Pasaporte',
        'clie_id_ci' => 'ID/CI',
    ];


    public function __construct()
    {
        $this->middleware('auth');
        $this->data = request()->all();
    }


    public function index(Request $request)
    {

        $url = url('settings/set-setting');
        return view('web.settings.index', compact('url'));
    }

    public function setSetting(Request $request)
    {

        $key = $request->get('key');
        $value = $request->get('value');

        if ($value == "") {
            return response()->json($this->ajaxResponse(false, 'Debe asignar un valor a la configuracion'));
        }

        settings()->set($key, $value);
        settings()->save();

        $keyName = [
            'gft_acceso_pvez' => 'Tarifa de gafete de Acceso por Primera vez',
            'gft_acceso_reposicion' => 'Tarifa de gafete de Acceso por Reposición',
            'gft_est_auto_pvez' => 'Tarifa de gafete de Estacionamiento para Autos por Primera vez',
            'gft_est_auto_reposicion' => 'Tarifa de gafete de Estacionamiento para Autos por Reposición',
            'gft_est_moto_pvez' => 'Tarifa de gafete de Estacionamiento para Autos por Primera vez',
            'gft_est_moto_reposicion' => 'Tarifa de gafete de Estacionamiento para Autos por Reposición',
            'tipo_cambio' => 'Tipo de Cambio'
        ];

        $Name = $keyName[$key] ?? $key;

        activity()
            ->inLog('Configuración')
            ->log("Modificación de configuración <b>$Name</b> a -> <b>$value</b>");

        return response()->json($this->ajaxResponse(true, 'Configuración <b>¡Guardada!</b>'));

    }

    public function panelPac()
    {
        return view('web.cod_facturacion.panel_pac');
    }

    public function cabeceraFactura()
    {
        return view('web.cod_facturacion.cabecera_factura');
    }

    public function guardarCabeceraFactura(Request $request)
    {
        $input = $request->only(['cfdi_nombre_emisor', 'cfdi_rfc_emisor', 'cfdi_lugar_expedicion', 'cfdi_telefono_emisor', 'cfdi_direccionfiscal_emisor', 'cfdi_regimenfiscal_emisor']);

        foreach ($input as $key => $value) {
            settings()->set($key, $value);
            settings()->save();
        }
        return response()->json($this->ajaxResponse(true, 'Cabecera de Factura <b>¡Guardada!</b>'));
    }

    ///////////// R O L E S ///////////////////////////////////////////
    public function rolesView(Request $request)
    {

        $roles = \DB::table('roles')->get();

        return view('web.settings.roles', compact('roles'));
    }

    public function roleForm(Role $role = null, Request $request)
    {

        $url = ($role == null) ? url('settings/insert-role') : url('settings/edit-role');

        return view('web.settings.role_form', compact('role', 'url'));
    }

    public function insertRole(Request $request)
    {

        if (!$this->validateAction('insert-role')) {
            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));
        } else {

            \DB::beginTransaction();
            try {
                Role::create(['name' => strtoupper(trim($this->data['name']))]);
            } catch (\Exception $e) {
                \DB::rollBack();
                return response()->json($this->ajaxResponse(false, "Error en el servidor!", $e->getMessage()));
            }

            \DB::commit();
            activity()->log("Creó el Rol: <b>{$this->data['name']}</b>");
            return response()->json($this->ajaxResponse(true, 'Rol creado correctamente.'));
        }

    }

    public function rolePermissionsView(Role $role)
    {

        $permissions = \DB::table('permissions')->get();

        return view('web.settings.role_permissions', compact('permissions', 'role'));
    }

    public function roleSetPermissions(Role $role, Request $request)
    {

        $permisos = $request->get('permission') ?? [];
        $permisos = array_keys($permisos);

        $role->syncPermissions($permisos);

        return response()->json($this->ajaxResponse(true, 'Configuración <b>¡Guardada!</b>'));

    }

    public function roleNavigationView(Role $role)
    {

        $items = \Navigation::tree();
        $stored = \DB::table('role_has_navigation')->where('role_id', $role->id)->get();
        // dd($stored);

        return view('web.settings.role_navigation', compact('items', 'role', 'stored'));
    }

    public function roleSetNavigation(Role $role, Request $request)
    {

        $items = $request->get('navigation') ?? [];
        $items = array_keys($items);

        //borrar navegacion de un rol
        \DB::table('role_has_navigation')->where('role_id', $role->id)->delete();


        foreach ($items as $i) {
            \DB::table('role_has_navigation')->insert(['navigation_id' => $i, 'role_id' => $role->id]);
        }

        return response()->json($this->ajaxResponse(true, 'Configuración <b>¡Guardada!</b>'));

    }


    //////////////// P E R M I S S I O N S //////////////////////////
    public function permissionsView(Request $request)
    {

        $permissions = \DB::table('permissions')->get();

        return view('web.settings.permissions', compact('permissions'));
    }

    public function permissionForm(Permission $permission = null, Request $request)
    {

        $url = ($permission == null) ? url('settings/insert-permission') : url('settings/edit-permission');

        return view('web.settings.permission_form', compact('permission', 'url'));
    }

    public function insertPermission(Request $request)
    {

        if (!$this->validateAction('insert-permission')) {
            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));
        } else {

            \DB::beginTransaction();
            try {
                Permission::create(['name' => strtolower(trim($this->data['name']))]);
            } catch (\Exception $e) {
                \DB::rollBack();
                return response()->json($this->ajaxResponse(false, "Error en el servidor!", $e->getMessage()));
            }

            \DB::commit();
            activity()->log("Creó el Permiso: <b>{$this->data['name']}</b>");
            return response()->json($this->ajaxResponse(true, 'Permiso creado correctamente.'));
        }

    }

    public function editPermission(Request $request)
    {

        if (!$this->validateAction('edit-permission')) {
            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));
        } else {

            \DB::beginTransaction();
            try {
                $permiso = Permission::findOrFail($this->data['id']);
                $permiso->name = strtolower(trim($this->data['name']));
                $permiso->save();

            } catch (\Exception $e) {
                \DB::rollBack();
                return response()->json($this->ajaxResponse(false, "Error en el servidor!", $e->getMessage()));
            }

            \DB::commit();
            activity()->log("Editó el nombre del Permiso: <b>{$this->data['name']}</b>");

            return response()->json($this->ajaxResponse(true, 'Permiso editado correctamente.'));
        }

    }

    public function deletePermission(Request $request)
    {

        if (!$this->validateAction('delete-permission')) {
            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));
        } else {

            \DB::beginTransaction();
            try {

                $permiso = Permission::findOrFail($this->data['id']);
                $log_message = "Elimino el Permiso: <b>{$permiso->name}</b>";

                $permiso->delete();

            } catch (\Exception $e) {
                \DB::rollBack();
                return response()->json($this->ajaxResponse(false, "Error en el servidor!", $e->getMessage()));
            }

            \DB::commit();
            activity()->log($log_message);
            return response()->json($this->ajaxResponse(true, 'Permiso eliminado correctamente.'));
        }

    }

}
