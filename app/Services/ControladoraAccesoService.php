<?php

namespace App\Services;

use App\Controladora;
use App\DoorControllerLog;
use App\Empleado;
use App\LogAcceso;
use App\Puerta;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ControladoraAccesoService
{
    protected $client;
    protected $controller;
    protected $description;
    protected $endPoint;
    protected $responseMessage;
    protected $responseStatusCode;
    protected $inicioPeticion;

    public function __construct(Controladora $controller)
    {
        $this->controller = $controller;
        $this->client = new Client([
            'base_uri' => "https://{$controller->ctrl_ip}",
            'auth' => [
                $controller->ctrl_usuario,
                Crypt::decrypt($controller->ctrl_contrasenna),
                'digest'
            ],
            'timeout' => 5,
            'verify' => config('app.env') == 'production'
        ]);
    }

    /**
     * Summary of addPerson
     * @param array $data => [ $empleado => Empleado::class, puerta => Puerta::class, inicio => Datetime | null, fin => Datetime | null ]
     * @return array
     */
    public function testConnection()
    {
        $this->endPoint = '/ISAPI/System/deviceInfo';
        $this->description = 'Verificación de conexión con la controladora.';
        $this->inicioPeticion = now();
        try {
            $response = $this->client->get(
                $this->endPoint,
                [
                    'http_errors' => false,
                    'timeout'     => 5,
                    'headers'     => [
                        'Accept' => 'application/json',
                    ],
                ]
            );

            $statusCode = $response->getStatusCode();
            $body       = (string) $response->getBody();

            $this->responseStatusCode = $statusCode;
            // 401 → Auth incorrecta
            if ($statusCode === 401) {
                $this->responseMessage = 'Usuario o contraseña inválidos.';
                $response = [
                    'online'  => false,
                    'error'   => 'AUTH_FAILED',
                    'message' => $this->responseMessage,
                ];
            }

            // Sin respuesta válida
            if ($statusCode !== 200) {
                $this->responseMessage = 'La controladora no respondió correctamente.';
                $response = [
                    'online'  => false,
                    'error'   => 'DEVICE_UNREACHABLE',
                    'message' => $this->responseMessage,
                ];
            }

            // ISAPI puede responder XML o JSON
            if (str_starts_with(trim($body), '<?xml')) {
                $this->responseMessage = 'Controladora online (respuesta XML).';
                $response = [
                    'online'  => true,
                    'format'  => 'xml',
                    'message' => $this->responseMessage,
                ];
            }

            $json = json_decode($body, true);

            $this->responseMessage = 'Controladora online (respuesta JSON).';
            $response = [
                'online'  => true,
                'format'  => 'json',
                'data'    => $json,
            ];
        } catch (\GuzzleHttp\Exception\ConnectException $e) {
            $this->responseStatusCode = 500;
            $this->responseMessage = 'No se pudo establecer conexión con la controladora.';
            $response = [
                'online'  => false,
                'error'   => 'CONNECTION_FAILED',
                'message' => $this->responseMessage,
            ];
        } catch (\Throwable $e) {
            $this->responseStatusCode = 500;
            $this->responseMessage = $e->getMessage();
            $response = [
                'online'  => false,
                'error'   => 'UNKNOWN_ERROR',
                'message' => $this->responseMessage,
            ];
        }

        $this->registerLog();
        return $response;
    }

    /**
     * Summary of addPerson
     * @param array $data => [ $empleado => Empleado::class, puerta => Puerta::class, inicio => Datetime | null, fin => Datetime | null ]
     * @return array
     */
    public function addPerson($data)
    {
        $inicio = $data['inicio'] ?: now();
        $fin = $data['fin'] ?: now()->addYear();
        $payload = [
            'UserInfo' => [
                'employeeNo' => "{$data['empleado']->empl_id}",
                'name' => $data['empleado']->empl_nombre,
                'userType' => "normal",
                'doorRight' => "{$data['puertas_numeros']}",
                'Valid' => [
                    'enable' => true,
                    'beginTime' => $inicio->format('Y-m-d') . "T" . $inicio->format('H:i:s'),
                    'endTime' => $fin->format('Y-m-d') . "T" . $fin->format('H:i:s')
                ]
            ]
        ];
        if ($payload['UserInfo']['doorRight']) {
            $doors = explode(',', $payload['UserInfo']['doorRight']);
            foreach ($doors as $door)
                $payload['UserInfo']['rightPlan'][] = [
                    'doorNo' => (int)$door,
                    'planTemplateNo' => "65535"
                ];
        }
        $this->endPoint = "/ISAPI/AccessControl/UserInfo/Record?format=json";
        $this->description = "Crear una nueva persona.";
        $this->inicioPeticion = now();
        $response =  $this->client->post(
            $this->endPoint,
            [
                'headers' => [
                    'Accept' => 'application/json',
                ],
                'json' => $payload,
                'http_errors' => false,
            ]
        );
        $statusCode = $response->getStatusCode();
        $body       = json_decode((string) $response->getBody(), true);

        return $this->parseResponse($statusCode, $body);
    }

    /**
     * Summary of updatePerson
     * @param array $data => [ $empleado => Empleado::class, puerta => Puerta::class ]
     * @return array
     */
    public function updatePerson($data)
    {
        $payload = [
            'UserInfo' => [
                'employeeNo' => "{$data['empleado']->empl_id}",
                'name' => $data['empleado']->empl_nombre,
                'doorRight' => "{$data['puertas_numeros']}"
            ]
        ];
        if ($payload['UserInfo']['doorRight']) {
            $doors = explode(',', $payload['UserInfo']['doorRight']);
            foreach ($doors as $door)
                $payload['UserInfo']['rightPlan'][] = [
                    'doorNo' => (int)$door,
                    'planTemplateNo' => "65535"
                ];
        }
        if (isset($data['Valid']))
            $payload['UserInfo']['Valid'] = $data['Valid'];

        $this->endPoint = "/ISAPI/AccessControl/UserInfo/Modify?format=json";
        $this->description = "Modificar una persona.";
        $this->inicioPeticion = now();
        $response =  $this->client->put(
            $this->endPoint,
            [
                'headers' => [
                    'Accept' => 'application/json',
                ],
                'json' => $payload,
                'http_errors' => false,
            ]
        );
        $statusCode = $response->getStatusCode();
        $body       = json_decode((string) $response->getBody(), true);

        return $this->parseResponse($statusCode, $body);
    }

    /**
     * Summary of deletePerson
     * @param array $data => [ $empleado => Empleado::class ]
     * @return array
     */
    public function deletePerson($data)
    {
        $payload = [
            'UserInfoDetail' => [
                'mode' => "byEmployeeNo",
                'EmployeeNoList' => [
                    ['employeeNo' => "{$data['empleado']->empl_id}"]
                ]
            ]
        ];
        $this->endPoint = "/ISAPI/AccessControl/UserInfoDetail/Delete?format=json";
        $this->description = "Eliminar una persona.";
        $this->inicioPeticion = now();
        $response =  $this->client->put(
            $this->endPoint,
            [
                'headers' => [
                    'Accept' => 'application/json',
                ],
                'json' => $payload,
                'http_errors' => false,
            ]
        );
        $statusCode = $response->getStatusCode();
        $body       = json_decode((string) $response->getBody(), true);

        return $this->parseResponse($statusCode, $body);
    }

    /**
     * Summary of addCard
     * @param array $data => [ $empleado => Empleado::class, card => string ]
     * @return array
     */
    public function addCard($data)
    {
        $payload = [
            'CardInfo' => [
                'employeeNo' => "{$data['empleado']->empl_id}",
                'cardNo' => $data['card'],
                'cardType' => "normalCard",
                'checkCardNo' => true,
                'checkEmployeeNo' => true
            ]
        ];
        $this->endPoint = "/ISAPI/AccessControl/CardInfo/Record?format=json";
        $this->description = "Adicionar una tarjeta.";
        $this->inicioPeticion = now();
        $response =  $this->client->post(
            $this->endPoint,
            [
                'headers' => [
                    'Accept' => 'application/json',
                ],
                'json' => $payload,
                'http_errors' => false,
            ]
        );
        $statusCode = $response->getStatusCode();
        $body       = json_decode((string) $response->getBody(), true);

        return $this->parseResponse($statusCode, $body);
    }

    /**
     * Summary of deleteCard
     * @param array $data => [ card => string ]
     * @return array
     */
    public function deleteCard($data)
    {
        $payload = [
            'CardInfoDelCond' => [
                'CardNoList' => [
                    ['cardNo' => "{$data['card']}"]
                ]
            ]
        ];
        $this->endPoint = "/ISAPI/AccessControl/CardInfo/Delete?format=json";
        $this->description = "Eliminar una tarjeta.";
        $this->inicioPeticion = now();
        $response =  $this->client->put(
            $this->endPoint,
            [
                'headers' => [
                    'Accept' => 'application/json',
                ],
                'json' => $payload,
                'http_errors' => false,
            ]
        );
        $statusCode = $response->getStatusCode();
        $body       = json_decode((string) $response->getBody(), true);

        return $this->parseResponse($statusCode, $body);
    }

    /**
     * Summary of openDoor
     * @param array $data => [ door => string ]
     * @return array
     */
    public function openDoor($data)
    {
        $payload = '<?xml version="1.0" encoding="UTF-8"?>
<RemoteControlDoor version="2.0" xmlns="http://www.isapi.org/ver20/XMLSchema">
    <cmd>open</cmd>
</RemoteControlDoor>';
        $this->endPoint = "/ISAPI/AccessControl/RemoteControl/door/{$data['door']}";
        $this->description = "Abrir puerta.";
        $this->inicioPeticion = now();
        $response =  $this->client->put(
            $this->endPoint,
            [
                'headers' => [
                    'Content-Type' => 'application/xml',
                    'Accept' => 'application/xml',
                ],
                'body' => $payload,
                'http_errors' => false,
            ]
        );
        $statusCode = $response->getStatusCode();
        $body       = json_decode((string) $response->getBody(), true);

        return $this->parseResponse($statusCode, $body);
    }
    /**
     * Summary of closeDoor
     * @param array $data => [ door => string ]
     * @return array
     */
    public function closeDoor($data)
    {
        $payload = '<?xml version="1.0" encoding="UTF-8"?>
<RemoteControlDoor version="2.0" xmlns="http://www.isapi.org/ver20/XMLSchema">
    <cmd>close</cmd>
</RemoteControlDoor>';
        $this->endPoint = "/ISAPI/AccessControl/RemoteControl/door/{$data['door']}";
        $this->description = "Cerrar puerta.";
        $this->inicioPeticion = now();
        $response =  $this->client->put(
            $this->endPoint,
            [
                'headers' => [
                    'Content-Type' => 'application/xml',
                    'Accept' => 'application/xml',
                ],
                'body' => $payload,
                'http_errors' => false,
            ]
        );
        $statusCode = $response->getStatusCode();
        $body       = json_decode((string) $response->getBody(), true);

        return $this->parseResponse($statusCode, $body);
    }

    /**
     * Summary of deleteCard
     * @param array $data => [ card => string ]
     * @return array
     */
    public function pollingSync($sync_in_out_status = false)
    {
        $position = 0;
        $responseStatus = 'MORE';

        $this->endPoint = "/ISAPI/AccessControl/AcsEvent?format=json";
        $this->description = "Recuperar y sincronizar eventos de la controladora (PULLING).";
        $this->inicioPeticion = now();
        // $lastSync = settings()->get('last_sync_access_log', 0);
        $lastEventDate = DB::table('log_accesos')->whereIn('lgac_source', ['pulling', 'event_notification_stream'])->orderByDesc('lgac_created_at')->first();
        while ($responseStatus == 'MORE') {
            $payload = [
                'AcsEventCond' => [
                    'searchID' => (string)Str::uuid(),
                    'searchResultPosition' => $position,
                    'maxResults' => 10,
                    'major' => 5,
                    'minor' => 1
                ]
            ];
            if ($lastEventDate)
                $payload['AcsEventCond']['startTime'] = Str::replaceFirst(' ', 'T', $lastEventDate) . "Z";
            $response =  $this->client->post(
                $this->endPoint,
                [
                    'headers' => [
                        'Accept' => 'application/json',
                    ],
                    'json' => $payload,
                    'http_errors' => false,
                ]
            );
            $statusCode = $response->getStatusCode();
            $body       = json_decode((string) $response->getBody(), true);

            $responseStatus = $body['AcsEvent']['responseStatusStrg'];
            $last_event_date = '';
            $empleados_to_sync = [];
            if (in_array($responseStatus, ['OK', 'MORE'])) {
                $events = $body['AcsEvent']['InfoList'];
                foreach ($events as $event) {
                    $empleado = DB::table('empleados')
                        ->where('empl_id', $event['employeeNoString'])
                        ->first();
                    if ($empleado) {
                        $door = DB::table('puertas')
                            ->where('door_controladora_id', $this->controller->ctrl_id)
                            ->where('door_numero', $event['doorNo'])
                            ->first();
                        $logAcceso = LogAcceso::firstOrCreate([
                            'lgac_serial_no' => $event['serialNo']
                        ], [
                            'lgac_major' => $event['major'],
                            'lgac_minor' => $event['minor'],
                            'lgac_card_number' => $event['cardNo'],
                            'lgac_lector' => $event['cardReaderNo'],
                            'lgac_door_number' => $event['doorNo'],
                            'lgac_door_id' => $door->door_id,
                            'lgac_ctrl_id' => $door->door_controladora_id,
                            'lgac_empl_id' => $event['employeeNoString'],
                            'lgac_tipo' => $door->door_direccion,
                            'lgac_in_out_state' => $door->door_direccion === 'ENTRADA',
                            'lgac_source' => 'pulling',
                            'lgac_time' => $event['time'],
                            'lgac_payload' => json_encode($event),
                            'lgac_created_at' => str_replace(['T', 'Z'], [' ', ''], $event['time'])
                        ]);
                        if ($sync_in_out_status) {
                            $ubicacion = DB::table('empleados_ubicacion')->where('emplub_empl_id', $empleado->empl_id)->first();

                            if (!$ubicacion || Carbon::parse($ubicacion->emplub_fecha)->lessThanOrEqualTo($logAcceso->lgac_created_at)) {

                                $controladora = Controladora::where('ctrl_id', $empleado->GafeteAcceso()->getVGafeteRfidV3()->controladora_id)->first();

                                $autos = $ubicacion ? $ubicacion->emplub_autos : 0;
                                $motos = $ubicacion ? $ubicacion->emplub_motos : 0;

                                $autos = $door->door_direccion == 'ENTRADA' ? ($door->door_tipo == 'AUTO' ? ($autos + 1) : $autos) : ($door->door_tipo == 'AUTO' ? ($autos - 1) : $autos);
                                $motos = $door->door_direccion == 'ENTRADA' ? ($door->door_tipo == 'MOTO' ? ($motos + 1) : $motos) : ($door->door_tipo == 'MOTO' ? ($motos - 1) : $motos);
                                if (!$ubicacion) {
                                    DB::table('empleados_ubicacion')->insert([
                                        'emplub_empl_id' => $empleado->empl_id,
                                        'emplub_door_out_id' => $door->door_id,
                                        'emplub_ubicacion' => $door->door_direccion == 'ENTRADA',
                                        'emplub_fecha' => $logAcceso->lgac_created_at,
                                        'emplub_autos' => $autos,
                                        'emplub_motos' => $motos
                                    ]);
                                } else {
                                    DB::table('empelados_ubicacion')
                                        ->where('emplub_empl_id', $empleado->empl_id)
                                        ->update([
                                            'emplub_door_out_id' => $door->door_id,
                                            'emplub_ubicacion' => $door->door_direccion == 'ENTRADA',
                                            'emplub_fecha' => $logAcceso->lgac_created_at,
                                            'emplub_autos' => $autos,
                                            'emplub_motos' => $motos
                                        ]);
                                }

                                if (!in_array($empleado->empl_id, $empleados_to_sync))
                                    $empleados_to_sync[] = $empleado->empl_id;
                            }
                        }
                        $last_event_date = $event['time'];
                    }
                }
            }

            $position++;
        }

        $response = $this->parseResponse($statusCode, $body);
        if ($response['success'] && count($body['AcsEvent']['InfoList']) > 0) {
            settings()->set('last_sync_access_log', $last_event_date);

            if ($sync_in_out_status && count($empleados_to_sync) > 0) {
                foreach ($empleados_to_sync as $empl) {
                    set_time_limit(60);
                    $empleado = $empl;
                    $ubicacion = $empleado->Ubicacion;
                    $controladora = Controladora::where('ctrl_id', $empleado->GafeteAcceso()->getVGafeteRfidV3()->controladora_id)->first();
                    $controllerService = new ControladoraAccesoService($controladora);
                    $door = $ubicacion->emplub_door_out_id ? Puerta::find($ubicacion->emplub_door_out_id) : Puerta::find($ubicacion->emplub_door_in_id);
                    if ($door->door_direccion == 'SALIDA') {
                        $numeros = implode(
                            ',',
                            $empleado->GafeteAcceso()
                                ->Puertas()
                                ->where('door_direccion', 'ENTRADA')
                                ->where('door_modo', 'FISICA')
                                ->pluck('door_numero')->toArray()
                        );
                    } else {
                        $numeros = implode(
                            ',',
                            $empleado->GafeteAcceso()
                                ->Puertas()
                                ->where('door_direccion', 'SALIDA')
                                ->where('door_modo', 'FISICA')
                                ->pluck('door_numero')->toArray()
                        );
                    }
                    $data = [
                        'empleado' => $empleado,
                        'puertas_numeros' => $numeros
                    ];
                    $controllerService->updatePerson($data);
                    sleep(1);
                }
            }
        }
        return $response;
    }

    private function parseResponse($statusCode, $body)
    {
        $this->responseStatusCode = $statusCode;
        // ✅ CASO OK
        if ($statusCode === 200 || ($body['statusCode'] ?? null) === 1) {
            $this->responseMessage = "Petición realizada satisfactoriamente.";
            $response = [
                'success' => true,
                'data'    => $body,
            ];
        } elseif (isset($body['subStatusCode'])) {
            // ⚠️ ERRORES ISAPI
            switch ($body['subStatusCode']) {

                case 'employeeNoAlreadyExist':
                    $this->responseMessage = 'El empleado ya existe en la controladora.';
                    $response = [
                        'success' => false,
                        'error'   => 'EMPLOYEE_ALREADY_EXISTS',
                        'message' => $this->responseMessage,
                    ];
                    break;

                case 'employeeNoNotExist':
                    $this->responseMessage = 'El empleado no existe en la controladora.';
                    $response = [
                        'success' => false,
                        'error'   => 'EMPLOYEE_NOT_EXISTS',
                        'message' => $this->responseMessage,
                    ];
                    break;

                case 'badJsonContent':
                    $this->responseMessage = 'El JSON enviado no cumple el esquema ISAPI.';
                    $response = [
                        'success' => false,
                        'error'   => 'INVALID_JSON',
                        'message' => $this->responseMessage,
                    ];
                    break;

                case 'MessageParametersLack':
                    $this->responseMessage = 'El JSON enviado le faltan parámetros requeridos segun el esquema ISAPI.';
                    $response = [
                        'success' => false,
                        'error'   => 'MISSING_PARAMETERS',
                        'message' => $this->responseMessage,
                    ];
                    break;

                case 'cardNoAlreadyExist':
                    $this->responseMessage = 'La Tarjeta ya existe en la controladora.';
                    $response = [
                        'success' => false,
                        'error'   => 'CARD_ALREADY_EXISTS',
                        'message' => $this->responseMessage,
                    ];
                    break;

                default:
                    $this->responseMessage = $body['statusString'] ?? 'Error desconocido.';
                    $response = [
                        'success' => false,
                        'error'   => 'ISAPI_ERROR',
                        'message' => $this->responseMessage,
                        'raw'     => $body,
                    ];
                    break;
            }
        } else {
            $this->responseMessage = $body['statusString'] ?? 'Error desconocido.';
            // ❌ ERROR NO CONTROLADO
            $response = [
                'success' => false,
                'error'   => 'UNKNOWN_ERROR',
                'status'  => $statusCode,
                'raw'     => $body,
                'message' => $this->responseMessage
            ];
        }
        $this->registerLog();
        return $response;
    }

    private function registerLog()
    {
        DoorControllerLog::create([
            'dclg_controller_ip' => $this->controller->ctrl_ip,
            'dclg_description' => $this->description,
            'dclg_end_point' => $this->endPoint,
            'dclg_state' => $this->responseStatusCode == 200 ? 1 : 2,
            'dclg_response_message' => $this->responseMessage,
            'dclg_response_status_code' => $this->responseStatusCode,
            'dclg_user_id' => optional(Auth()->user())->id,
            'dclg_created_at' => $this->inicioPeticion
        ]);
    }
}
