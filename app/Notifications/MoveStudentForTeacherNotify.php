<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class MoveStudentForTeacherNotify extends Notification
{
    use Queueable;

    private $move_student;

    /**
     * Create a new notifications instance.
     *
     * @return void
     */
    public function __construct($move_student)
    {
        $this->move_student = $move_student;
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
            'id' => $this->move_student['id'],
            'student_id' => $this->move_student['student_id'],
            'student_name' => $this->move_student['student_name'],
            'old_teacher_id' => $this->move_student['old_teacher_id'],
            'old_teacher_name' => $this->move_student['old_teacher_name'],
            'new_teacher_name' => $this->move_student['new_teacher_name'],
            'new_teacher_id' => $this->move_student['new_teacher_id'],
        ];
    }
}
