<?php

namespace App\Events;

use App\Models\Clase;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ClaseCancelada implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $clase;
    public $usuariosAfectados;

    /**
     * Create a new event instance.
     */
    public function __construct(Clase $clase, array $usuariosAfectados)
    {
        $this->clase = $clase;
        $this->usuariosAfectados = $usuariosAfectados;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // Crear un canal privado para cada usuario afectado
        $channels = [];
        foreach ($this->usuariosAfectados as $usuarioId) {
            $channels[] = new PrivateChannel('user.' . $usuarioId);
        }
        return $channels;
    }

    /**
     * Datos que se enviarán al frontend
     */
    public function broadcastWith(): array
    {
        return [
            'mensaje' => 'La clase de ' . $this->clase->tipo_entrenamiento->nombre . ' programada para el ' . 
                         $this->clase->fecha->format('d/m/Y') . ' a las ' . 
                         $this->clase->hora_inicio->format('H:i') . ' ha sido cancelada.',
            'clase_id' => $this->clase->id,
            'tipo_entrenamiento' => $this->clase->tipo_entrenamiento->nombre,
            'fecha' => $this->clase->fecha->format('d/m/Y'),
            'hora' => $this->clase->hora_inicio->format('H:i'),
        ];
    }

    /**
     * Nombre del evento en el frontend
     */
    public function broadcastAs(): string
    {
        return 'clase.cancelada';
    }
}
