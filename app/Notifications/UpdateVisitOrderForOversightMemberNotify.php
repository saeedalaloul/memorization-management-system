<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class UpdateVisitOrderForOversightMemberNotify extends Notification
{
    use Queueable;
    private $visit_order;
    /**
     * Create a new notifications instance.
     *
     * @return void
     */
    public function __construct($visit_order)
    {
        $this->visit_order = $visit_order;
    }

    /**
     * Get the notifications's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notifications.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'id'=>$this->visit_order['id'],
            'hostname'=> $this->visit_order['hostname'],
            'host_type'=> $this->visit_order['host_type'],
            'datetime'=> $this->visit_order['datetime'],
        ];
    }
}
