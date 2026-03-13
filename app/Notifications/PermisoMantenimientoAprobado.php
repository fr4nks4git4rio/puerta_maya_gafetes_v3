<?php

namespace App\Notifications;

use App\PermisoMantenimiento;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;


class PermisoMantenimientoAprobado extends Notification
{
    use Queueable;


    private $permisoMantenimiento = null;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct( PermisoMantenimiento $permisoMantenimiento)
    {
        $this->permisoMantenimiento = $permisoMantenimiento;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database','mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Permiso de mantenimiento aprobado')
//            ->greeting('Puerta Maya - Sistema de credencialización')
            ->greeting('Puerta Maya Sistema de Gafetes y Acceso.')
//                    ->action('Notification Action', url('/'))
            ->line('Por este medio tenemos la atención de informarle que el siguiente permiso de mantenimiento ha sido aprobado:')
            ->line('Empresa: '.$this->permisoMantenimiento->pmtt_empresa)
            ->line('Representante: '.$this->permisoMantenimiento->pmtt_representante)
            ->line('Trabajo: '.$this->permisoMantenimiento->pmtt_trabajo)
            ->line('Vigencia: del'.$this->permisoMantenimiento->pmtt_vigencia_inicial .' al '. $this->permisoMantenimiento->pmtt_vigencia_final);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
//            'ptmp_id' => $this->permisoTemporal->ptmp_id,
//            'nombre'  => $this->permisoTemporal->ptmp_nombre,
//            'inicio'  => $this->permisoTemporal->ptmp_vigencia_inicial,
//            'fin'     => $this->permisoTemporal->ptmp_vigencia_final,
            'icono'   => "ti-hummer text-success",
            'titulo'  => 'Permisos de mantenimiento',
            'texto'   => 'El <b>permiso de mantenimiento</b> para <b>' .
                            $this->permisoMantenimiento->pmtt_empresa. '</b> del '.
                            $this->permisoMantenimiento->pmtt_vigencia_inicial .
                            ' al '. $this->permisoMantenimiento->pmtt_vigencia_final .
                            ' ha sido aprobado.',
//            'url'     => "permiso-temporal/recepcion"
        ];
    }
}
