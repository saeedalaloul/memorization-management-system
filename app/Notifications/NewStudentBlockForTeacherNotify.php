<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewStudentBlockForTeacherNotify extends Notification
{
    use Queueable;

    private $student_block;

    /**
     * Create a new notifications instance.
     *
     * @return void
     */
    public function __construct($student_block)
    {
        $this->student_block = $student_block;
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
        if (isset($this->student_block['number_pages'])) {
            return [
                'id' => $this->student_block['id'],
                'student_id' => $this->student_block['student_id'],
                'student_name' => $this->student_block['student_name'],
                'reason' => $this->student_block['reason'],
                'number_times' => $this->student_block['number_times'],
                'number_pages' => $this->student_block['number_pages'],
            ];
        }

        return [
            'id' => $this->student_block['id'],
            'student_id' => $this->student_block['student_id'],
            'student_name' => $this->student_block['student_name'],
            'reason' => $this->student_block['reason'],
            'number_times' => $this->student_block['number_times'],
        ];
    }
}
