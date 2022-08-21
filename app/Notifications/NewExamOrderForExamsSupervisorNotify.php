<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewExamOrderForExamsSupervisorNotify extends Notification
{
    use Queueable;
    private $exam_order;
    /**
     * Create a new notifications instance.
     *
     * @return void
     */
    public function __construct($exam_order)
    {
        $this->exam_order = $exam_order;
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
            'id'=>$this->exam_order->id,
            'student_name'=> $this->exam_order->student->user->name,
            'teacher_name'=> $this->exam_order->teacher->user->name,
            'quran_part_name'=> $this->exam_order->quranPart->name.' '.$this->exam_order->quranPart->description,
        ];
    }
}
