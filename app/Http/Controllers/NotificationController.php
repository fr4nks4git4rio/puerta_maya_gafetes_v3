<?php

namespace App\Http\Controllers;

//Framework
use Illuminate\Http\Request;

//Vendors
use Illuminate\Notifications\Notification;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Datatables;

//App Clases
//use App\VActivity;


class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request, Builder $htmlBuilder)
    {
        if ($request->ajax()) {
            $dataTable = Datatables::of(
                \App\Notification::select(["id", "data", "created_at"])
                    ->where('read_at', null)
                    ->where('notifiable_id', auth()->id())
            )
                ->editColumn('title', function ($model) {
                    return "<b>".json_decode($model->data)->titulo."</b>";
                })
                ->editColumn('data', function ($model) {
                    return json_decode($model->data)->texto;
                })
                ->editColumn('created_at', function ($model) {
                    return $model->created_at->format('d/m/Y H:i:s');
                })
                ->rawColumns(['title', 'data'])
                ->make(true);

            return $dataTable;
        }

        //Definicion del script de frontend

        $htmlBuilder->parameters([
            'responsive' => true,
            'select' => 'false',
            'autoWidth' => false,
            'language' => [
                'url' => asset('plugins/datatables/datatables_local_es_ES.json')
            ],
            'order' => [[0, 'desc']]
        ]);

        $dataTable = $htmlBuilder
            ->addColumn(['data' => 'id', 'name' => 'id', 'title' => 'Id', 'visible' => false])
            ->addColumn(['data' => 'title', 'name' => 'title', 'title' => 'Título', 'search' => true])
            ->addColumn(['data' => 'data', 'name' => 'data', 'title' => 'Texto', 'search' => true])
            ->addColumn(['data' => 'created_at', 'name' => 'created_at', 'title' => 'Fecha de Notificación', 'search' => true]);


        return view('web.notificaciones.index', compact('dataTable'));

    }


    public function markAsRead($noti_id)
    {
//        dd($noti_id);

        try {

            auth()->getUser()
                ->unreadNotifications
                ->where('id', $noti_id)
                ->first()
                ->markAsRead();

            return response()->json($this->ajaxResponse(true, 'Notificación marcada como leída.'));

        } catch (\Exception $e) {
            return response()->json($this->ajaxResponse(false, "Error en el servidor!", $e->getMessage()));
        }


    }

    public function markAsReadByUser()
    {

        \App\Notification::where('read_at', null)
            ->where('notifiable_id', auth()->id())
            ->update([
                'read_at' => now()->format('Y-m-d H:i:s')
            ]);

        return response()->json($this->ajaxResponse(true, 'Notificaciones <b>LEIDAS</b> correctamente.'));
    }

    public function markSomeAsReadByUser(Request $request)
    {
        \App\Notification::where('read_at', null)
            ->whereIn('id', $request->notificaciones_ids)
            ->update([
                'read_at' => now()->format('Y-m-d H:i:s')
            ]);

        return response()->json($this->ajaxResponse(true, 'Notificaciones <b>LEIDAS</b> correctamente.'));
    }

}
