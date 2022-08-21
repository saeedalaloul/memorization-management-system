<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewVisitForAdminNotify extends Notification
{
    use Queueable;
    private $visit;
    /**
     * Create a new notifications instance.
     *
     * @return void
     */
    public function __construct($visit)
    {
        $this->visit = $visit;
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
            'id'=>$this->visit['id'],
            'hostname'=> $this->visit['hostname'],
            'host_type'=> $this->visit['host_type'],
            'oversight_member_name'=> $this->visit['oversight_member_name'],
            'datetime'=> $this->visit['datetime'],
        ];
    }
}
