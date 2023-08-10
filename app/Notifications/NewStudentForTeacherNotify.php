<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewStudentForTeacherNotify extends Notification
{
    use Queueable;

    private $new_student;

    /**
     * Create a new notifications instance.
     *
     * @return void
     */
    public function __construct($new_student)
    {
        $this->new_student = $new_student;
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
            'student_id' => $this->new_student['student_id'],
            'student_name' => $this->new_student['student_name'],
            'old_teacher_id' => $this->new_student['old_teacher_id'],
            'old_teacher_name' => $this->new_student['old_teacher_name'],
            'new_teacher_name' => $this->new_student['new_teacher_name'],
            'new_teacher_id' => $this->new_student['new_teacher_id'],
        ];
    }
}
