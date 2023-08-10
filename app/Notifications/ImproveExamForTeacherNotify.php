<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ImproveExamForTeacherNotify extends Notification
{
    use Queueable;
    private $exam_improvement;
    /**
     * Create a new notifications instance.
     *
     * @return void
     */
    public function __construct($exam_improvement)
    {
        $this->exam_improvement = $exam_improvement;
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
        if ($this->exam_improvement->exam->quranPart !== null) {
            return [
                'id'=>$this->exam_improvement->id,
                'student_name'=> $this->exam_improvement->exam->student->user->name,
                'tester_name'=> $this->exam_improvement->tester->user->name,
                'part_name'=> $this->exam_improvement->exam->quranPart->name.' '.$this->exam_improvement->exam->quranPart->description,
                'mark'=> $this->exam_improvement->mark,
                'datetime'=> $this->exam_improvement->datetime,
            ];
        }

        return [
            'id'=>$this->exam_improvement->id,
            'student_name'=> $this->exam_improvement->exam->student->user->name,
            'tester_name'=> $this->exam_improvement->tester->user->name,
            'part_name'=> $this->exam_improvement->exam->sunnahPart->name. ' (' . $this->exam_improvement->exam->sunnahPart->total_hadith_parts. ') حديث',
            'mark'=> $this->exam_improvement->mark,
            'datetime'=> $this->exam_improvement->datetime,
        ];
    }
}
