<?php

namespace App\Notifications;

use App\Models\Clase;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class ClaseCanceladaNotification extends Notification
{
    use Queueable;

    protected $clase;

    /**
     * Create a new notification instance.
     */
    public function __construct(Clase $clase)
    {
        $this->clase = $clase;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'mensaje' => 'La clase de ' . $this->clase->tipo_entrenamiento->nombre . ' del ' . 
                         $this->clase->fecha->format('d/m/Y') . ' a las ' . 
                         $this->clase->hora_inicio->format('H:i') . ' ha sido cancelada.',
            'clase_id' => $this->clase->id,
            'tipo_entrenamiento' => $this->clase->tipo_entrenamiento->nombre,
            'fecha' => $this->clase->fecha->format('d/m/Y'),
            'hora' => $this->clase->hora_inicio->format('H:i'),
            'tipo' => 'clase_cancelada',
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'mensaje' => 'La clase de ' . $this->clase->tipo_entrenamiento->nombre . ' del ' . 
                         $this->clase->fecha->format('d/m/Y') . ' a las ' . 
                         $this->clase->hora_inicio->format('H:i') . ' ha sido cancelada.',
            'clase_id' => $this->clase->id,
            'tipo_entrenamiento' => $this->clase->tipo_entrenamiento->nombre,
            'fecha' => $this->clase->fecha->format('d/m/Y'),
            'hora' => $this->clase->hora_inicio->format('H:i'),
            'tipo' => 'clase_cancelada',
        ]);
    }
}
