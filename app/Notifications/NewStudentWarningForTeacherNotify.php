<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewStudentWarningForTeacherNotify extends Notification
{
    use Queueable;

    private $student_warning;

    /**
     * Create a new notifications instance.
     *
     * @return void
     */
    public function __construct($student_warning)
    {
        $this->student_warning = $student_warning;
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
        if (isset($this->student_warning['number_pages'])) {
            return [
                'id' => $this->student_warning['id'],
                'student_name' => $this->student_warning['student_name'],
                'reason' => $this->student_warning['reason'],
                'number_times' => $this->student_warning['number_times'],
                'number_pages' => $this->student_warning['number_pages'],
            ];
        } else {
            return [
                'id' => $this->student_warning['id'],
                'student_name' => $this->student_warning['student_name'],
                'reason' => $this->student_warning['reason'],
                'number_times' => $this->student_warning['number_times'],
            ];
        }
    }
}
