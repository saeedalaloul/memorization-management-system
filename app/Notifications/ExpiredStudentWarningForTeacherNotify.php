<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ExpiredStudentWarningForTeacherNotify extends Notification
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
        return [
            'id' => $this->student_warning->id,
            'student_id' => $this->student_warning->student_id,
            'student_name' => $this->student_warning->student->user->name,
            'reason' => $this->student_warning->reason,
            'warning_expiry_date' => $this->student_warning->warning_expiry_date,
            'notes' => $this->student_warning->notes,
        ];
    }
}
