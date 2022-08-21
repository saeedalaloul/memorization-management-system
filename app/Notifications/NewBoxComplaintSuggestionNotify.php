<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewBoxComplaintSuggestionNotify extends Notification
{
    use Queueable;
    private $box_complaint_suggestion;
    /**
     * Create a new notifications instance.
     *
     * @return void
     */
    public function __construct($box_complaint_suggestion)
    {
        $this->box_complaint_suggestion = $box_complaint_suggestion;
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
            'id'=>$this->box_complaint_suggestion->id,
            'sender_name'=> $this->box_complaint_suggestion->sender->name,
            'category'=> $this->box_complaint_suggestion->category,
            'datetime'=> $this->box_complaint_suggestion->datetime,
        ];
    }
}
