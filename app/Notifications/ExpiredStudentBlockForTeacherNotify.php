<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ExpiredStudentBlockForTeacherNotify extends Notification
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
        return [
            'id' => $this->student_block->id,
            'student_name' => $this->student_block->student->user->name,
            'reason' => $this->student_block->reason,
            'block_expiry_date' => $this->student_block->block_expiry_date,
            'notes' => $this->student_block->notes,
        ];
    }
}
