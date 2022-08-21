<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RejectionActivityOrderForTeacherNotify extends Notification
{
    use Queueable;
    private $activity_order;
    /**
     * Create a new notifications instance.
     *
     * @return void
     */
    public function __construct($activity_order)
    {
        $this->activity_order = $activity_order;
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
            'id'=> $this->activity_order->id,
            'activity_type_name'=> $this->activity_order->activity_type->name,
            'datetime'=> $this->activity_order->datetime,
            'notes' => $this->activity_order->notes,
        ];
    }
}
