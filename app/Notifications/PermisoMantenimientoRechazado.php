<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

use App\PermisoMantenimiento;

class PermisoMantenimientoRechazado extends Notification
{
    use Queueable;


    private $permisoMantenimiento = null;
    private $comentario = null;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct( PermisoMantenimiento $permisoMantenimiento, string $comentario = null)
    {
        $this->permisoMantenimiento = $permisoMantenimiento;
        $this->comentario = $comentario;
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
            ->subject('Permiso de mantenimiento rechazado')
//            ->greeting('Puerta Maya - Sistema de credencialización')
            ->greeting('Puerta Maya Sistema de Gafetes y Acceso.')
//                    ->action('Notification Action', url('/'))
            ->line('Por este medio tenemos la atención de informarle que el siguiente permiso de mantenimiento ha sido rechazado:')
            ->line('Empresa: '.$this->permisoMantenimiento->pmtt_empresa)
            ->line('Representante: '.$this->permisoMantenimiento->pmtt_representante)
            ->line('Trabajo: '.$this->permisoMantenimiento->pmtt_trabajo)
            ->line('Vigencia: del'.$this->permisoMantenimiento->pmtt_vigencia_inicial .' al '. $this->permisoMantenimiento->pmtt_vigencia_final)
            ->line('Motivo: '.$this->comentario);
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
            'icono'   => "ti-paint-roller text-danger",
            'titulo'  => 'Permisos de mantenimiento',
            'texto'   => '<b>Mantenimiento Rechazado: </b><br/>'.
                         $this->permisoMantenimiento->pmtt_fecha . ', <b>'. $this->permisoMantenimiento->pmtt_empresa .'</b> ' .
                         $this->comentario,
//            'url'     => "permiso-mantenimiento"
        ];
    }
}
