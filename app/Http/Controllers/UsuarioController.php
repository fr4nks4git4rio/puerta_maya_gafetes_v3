<?php

namespace App\Http\Controllers;

//Framework
use http\Env\Response;
use Illuminate\Http\Request;

//Vendors
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Datatables;
use Spatie\Permission\Models\Role;

//App Clases
use App\User;
use App\Local;


class UsuarioController extends Controller
{

    protected $rules = [
        'insert' => [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'telefono' => 'nullable',
            'usr_lcal_id' => 'required|exists:locales,lcal_id',

            'password' => 'required',
            'repeat' => 'required|same:password',

        ],
        'edit' => [
            'id' => 'required|exists:users',
            'name' => 'required',
            'email' => 'required|email',
            'telefono' => 'nullable',
            // 'usr_idioma'  => 'nullable',
            'password' => 'nullable',
            'repeat' => 'required_with:password|same:password',
            'usr_lcal_id' => 'required|exists:locales,lcal_id',
        ],
        'delete' => [
            // 'id' => 'required|exists:users'
        ]
    ];

    protected $etiquestas = [
        'id' => 'Id',
        'name' => 'Nombre',
        'email' => 'Email',
        'password' => 'Contraseña',
        'repeat' => 'Repetir',
        'telefono' => 'Teléfono',
        'usr_lcal_id' => 'Local',
        'usr_idioma' => 'Idioma',
    ];


    public function __construct()
    {
        $this->middleware('auth');
        $this->data = request()->all();
    }


    public function index(Request $request, Builder $htmlBuilder)
    {

        if ($request->ajax()) {
            return Datatables::of(User::select(['id', 'name', 'email', 'telefono', 'usr_lcal_id', 'lcal_nombre_comercial',
                'created_at', 'updated_at'])
                ->join('locales', 'usr_lcal_id', 'lcal_id')
                ->orderBy('name')
                ->with(['Roles'])
            )
                ->addColumn('roles', function (User $model) {

                    return implode(" | ", $model->Roles->sortBy('name')->pluck('name')->toArray());
                })
//                                 ->addColumn('local', function(User $model) {
//                                    // dd($model);
//                                    return $model->Local->lcal_nombre_comercial ?? "";
//                                })

                ->filterColumn('lcal_nombre_comercial', function ($query, $keyword) {
                    $query->whereRaw(" lcal_nombre_comercial like ?", ["%{$keyword}%"]);
                })
                ->rawColumns(['crgo_color', 'roles'])
                ->make(true);
        }

        //Definicion del script de frontend

        $htmlBuilder->parameters([
            'responsive' => true,
            'select' => 'single',
            'autoWidth' => false,
            'language' => [
                'url' => asset('plugins/datatables/datatables_local_es_ES.json')
            ],
//            'order'=>[[0,'desc']]
        ]);

        $dataTable = $htmlBuilder
            ->addColumn(['data' => 'id', 'name' => 'id', 'title' => 'Id', 'visible' => false])
            ->addColumn(['data' => 'name', 'name' => 'name', 'title' => 'Nombre'])
            ->addColumn(['data' => 'email', 'name' => 'email', 'title' => 'Correo'])
            ->addColumn(['data' => 'telefono', 'name' => 'telefono', 'title' => 'Teléfono'])
            ->addColumn(['data' => 'roles', 'name' => 'roles', 'title' => 'Roles', 'search' => false])
            ->addColumn(['data' => 'lcal_nombre_comercial', 'name' => 'lcal_nombre_comercial', 'title' => 'Local']);
        // ->addColumn(['data' => 'usr_idioma', 'name' => 'usr_idioma', 'title' => 'Idioma']);

        return view('web.usuario.index', compact('dataTable'));

    }


    public function form(User $usuario = null, Request $request)
    {

        $url = ($usuario == null) ? url('usuario/insert') : url('usuario/edit', $usuario->id);

        $locales = local::selectRaw('lcal_id , lcal_nombre_comercial')
            ->get()
            ->pluck('lcal_nombre_comercial', 'lcal_id')
            ->put('', 'SELECCIONE UNA OPCIÓN');

        return view('web.usuario.form', compact('usuario', 'url', 'locales'));
    }


    public function insert(Request $request)
    {

        if (!$this->validateAction('insert')) {
            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));
        } else {

            \DB::beginTransaction();
            try {

                $this->data['password'] = \Hash::make($this->data['password']);

                User::create($this->data);
            } catch (\Exception $e) {
                \DB::rollBack();
                return response()->json($this->ajaxResponse(false, "Error en el servidor!", $e->getMessage()));
            }

            \DB::commit();
            return response()->json($this->ajaxResponse(true, 'Usuario creado correctamente.'));
        }

    }

    public function edit(User $usuario, Request $request)
    {

        if (!$this->validateAction('edit')) {
            return response()->json($this->ajaxResponse(false, 'Errores en el formulario!'));
        } else {

            \DB::beginTransaction();
            try {
                $usuario->name = $this->data['name'];
                $usuario->email = $this->data['email'];
                $usuario->telefono = $this->data['telefono'];
                $usuario->usr_lcal_id = $this->data['usr_lcal_id'];
                // $usuario->usr_idioma = $this->data['usr_idioma'];

                if ($this->data['password'] != "") {
                    $usuario->password = \Hash::make($this->data['password']);
                }

                $usuario->save();
            } catch (\Exception $e) {
                \DB::rollBack();
                return response()->json($this->ajaxResponse(false, "Error en el servidor!", $e->getMessage()));
            }

            \DB::commit();
            return response()->json($this->ajaxResponse(true, 'Usuario actualizado.'));
        }

    }

    public function delete(User $usuario, Request $request)
    {

        if (!$this->validateAction('delete')) {
            return response()->json($this->ajaxResponse(false, 'Error en la petición!'));
        } else {

            \DB::beginTransaction();
            try {
                $usuario->delete();
            } catch (\Exception $e) {
                \DB::rollBack();
                return response()->json($this->ajaxResponse(false, "Error en el servidor!", $e->getMessage()));
            }

            \DB::commit();
            return response()->json($this->ajaxResponse(true, 'Usuario eliminado.'));
        }
    }


    public function formRoles(User $usuario, Request $request)
    {

        $url = url('usuario/set-roles', $usuario->id);

        $roles = Role::all();

        return view('web.usuario.form_roles', compact('usuario', 'url', 'roles'));
    }

    public function formToken(User $usuario, Request $request)
    {

        $url_refresh_token = url('usuario/refresh-token', $usuario->id);
        $url_unset_token = url('usuario/unset-token', $usuario->id);


        return view('web.usuario.form_token', compact('usuario', 'url_refresh_token', 'url_unset_token'));
    }

    public function unsetToken(User $usuario)
    {

        $usuario->door_token = null;
        $usuario->save();

        return response()->json($usuario);

    }

    public function refreshToken(User $usuario)
    {

        $token = \Str::random(5) . date('md') . \Str::random(5) . date('His');

        $usuario->door_token = $token;
        $usuario->save();

        return response()->json($usuario);

    }

    public function setRoles(User $usuario, Request $request)
    {

        $roles = $request->get('roles', []);
        $roles = array_keys($roles);

        $usuario->syncRoles($roles);

        //Activity Log/////////////////////////////////////////////////////////////////////////
        activity()
            ->performedOn($usuario)
            ->inLog('Usuario')
            ->withProperties(['roles' => $roles])
            ->log("Cambió los roles asignados al usuario <b>{$usuario->name}</b>");


        ///////////////////////////////////////////////////////////////////////////////////////

        return response()->json($this->ajaxResponse(true, 'Roles asignados correctamente.'));

    }


    /*Obtiene la información para un campo select2*/
    public function getSelectOptions(Request $request)
    {
        $q = $request->q;

        $records = User::select(\DB::raw('id as id, concat(name," - " ,email) as text'))
            ->where('name', 'like', "%$q%")
            ->get()->toArray();
        $records[0]['selected'] = true;
        return response()->json($records);
    }


    public function getJSON(User $usuario)
    {
        return response()->json($usuario);
    }


}
