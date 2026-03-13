<?php

namespace App\Clases;

use App\Controladora;
use App\DoorControllerLog;

class DoorCommandGeneratorV2
{

    private $ip = '0.0.0.0';
    private $action = '';
    private $port = 4370;
    private $password = 'empty';
    private $token = 'null';
    private $description = '';
    private $controller_name = '';

    private $logRecord = null;

    private $exe = 'rfid.door.controller.exe';

    private $command = '';

    private $path = '';


    public function __construct(Controladora $controller)
    {
        $this->token = uniqid('', true);
        $this->ip = $controller->ctrl_ip;
        $this->controller_name = $controller->ctrl_nombre;

        $this->path = base_path();

    }

    public function setCards($pin, $cardNumber, $startTime = 0, $endTime = 0)
    {

        $this->action = 'setcards';
        $this->description = 'Dando de alta tarjeta';

        $cardPassword = 'empty';
        $this->command = $this->path . DIRECTORY_SEPARATOR . 'dctool' . DIRECTORY_SEPARATOR . $this->baseCommand() . "{$this->action} {$pin} {$cardNumber} {$cardPassword} {$startTime} {$endTime}";

        $this->createLog();

        return $this->systemCall();
    }

    public function delCards($pin, $cardNumber, $startTime = 0, $endTime = 0)
    {

        $this->action = 'delcards';
        $this->description = 'Dando de baja a tarjeta';

        $cardPassword = 'empty';
        $this->command = $this->path . DIRECTORY_SEPARATOR . 'dctool' . DIRECTORY_SEPARATOR . $this->baseCommand() . "{$this->action} {$pin} {$cardNumber} {$cardPassword} {$startTime} {$endTime}";

        $this->createLog();

        return $this->systemCall();
    }

    public function authCards($pin, $doorPin)
    {

        $this->action = 'authcards';
        $this->description = 'Autorizando tarjeta';

        $AuthorizeTimezoneId = 1;

        $this->command = $this->path . DIRECTORY_SEPARATOR . 'dctool' . DIRECTORY_SEPARATOR . $this->baseCommand() . "{$this->action} {$pin} {$AuthorizeTimezoneId} {$doorPin}";

        $this->createLog();

        return $this->systemCall();
    }

    public function lockCards($pin, $doorPin)
    {

        $this->action = 'lockcards';
        $this->description = 'Revocando tarjeta';

        $AuthorizeTimezoneId = 1;

        $this->command = $this->path . DIRECTORY_SEPARATOR . 'dctool' . DIRECTORY_SEPARATOR . $this->baseCommand() . "{$this->action} {$pin} {$AuthorizeTimezoneId} {$doorPin}";

        $this->createLog();

        return $this->systemCall();
    }

    public function openDoor($doorPin, $seconds = 15)
    {

        $this->action = 'opendoor';
        $this->description = 'Abriendo puerta con  PIN: ' . $doorPin;

        $this->command = $this->path . DIRECTORY_SEPARATOR . 'dctool' . DIRECTORY_SEPARATOR . $this->baseCommand() . "{$this->action} {$doorPin} {$seconds}";

        //dd($this->command);

        $this->createLog();

        return $this->systemCall();
    }

    public function getAccessLog()
    {

        $this->action = 'getlogs';
        $this->description = 'Obtener registros de acceso de la controladora: ' . $this->controller_name;

        $this->command = $this->path . DIRECTORY_SEPARATOR . 'dctool' . DIRECTORY_SEPARATOR . $this->baseCommand() . "{$this->action}";

        $this->createLog();

        return $this->systemCall();
    }

    public function pingController()
    {

        $this->action = 'pingControler';
        $this->description = 'Probando conexión de la Controladora';

//        $this->command = 'ping ' . "{$this->ip}";
        $this->command = $this->path . DIRECTORY_SEPARATOR . 'dctool' . DIRECTORY_SEPARATOR . "{$this->exe} {$this->token} {$this->ip} {$this->port}";

        $this->createLog();

        return $this->systemCall();
    }


    ///////////////////////////////////////////////////////////////////////////

    private function baseCommand()
    {
        return "{$this->exe} {$this->token} {$this->ip} {$this->port} {$this->password} ";
    }

    private function createLog()
    {

        $data = [
            'dclg_token' => $this->token,
            'dclg_action' => $this->action,
            'dclg_controller_ip' => $this->ip,
            'dclg_description' => $this->description,
            'dclg_full_command' => $this->command,
            'dclg_state' => 0
        ];

        if (auth()->user() != null) {
            $data['dclg_user_id'] = auth()->user()->id;
        }

        $record = DoorControllerLog::create($data);

        $this->logRecord = $record;

    }

    private function systemCall()
    {
        $caller = "";
        $output = "";
        $success = false;

        $enabled = settings()->get('door_controller_enabled', 0);


        try {

            if ($enabled == 1) {


                //system
                if (function_exists('system')) {
                    ob_start();
                    system($this->command, $return_var);
                    $output = ob_get_contents();
                    ob_end_clean();

                    $caller = 'system';
                } //exec
                else if (function_exists('exec')) {
                    exec($this->command, $output, $return_var);
                    $output = implode('\n', $output);
                    $caller = 'exec';
                } //shell_exec
                else if (function_exists('shell_exec')) {
                    $output = shell_exec($this->command);
                    $return_var = 'shell_exec';
                    $caller = 'shell_exec';
                } else {
                    $output = 'Command execution not possible on this system';
                    $return_var = 1;
                }
            } else {
                $output = 'Door Controller disabled on settings, succeed.';
                $return_var = 1;
            }

            $output = utf8_encode($output);

            if (strpos($output, 'succeed') > 1) $success = true;

            $r_v = strlen($return_var) === 1 ? $return_var : 2;
            $output = strlen($return_var) === 1 ? $output : 'Connexion intent failed.';

            if ($this->logRecord != null && $this->token == $this->logRecord->dclg_token) {
                $this->logRecord->dclg_response_message = $output;
                $this->logRecord->dclg_state = $r_v;
                $this->logRecord->save();
            }

        } catch (\ErrorException $e) {
            $output = $e->getMessage();
        }


        return array('output' => $output, 'success' => $success, 'command' => $this->command, 'token' => $this->token, 'caller' => $caller);

    }


}
