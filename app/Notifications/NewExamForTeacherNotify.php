<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewExamForTeacherNotify extends Notification
{
    use Queueable;
    private $exam;
    /**
     * Create a new notifications instance.
     *
     * @return void
     */
    public function __construct($exam)
    {
        $this->exam = $exam;
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
        if ($this->exam->quranPart != null) {
            // في حالة كان نوع الاختبار قرآن
            return [
                'id'=>$this->exam->id,
                'student_name'=> $this->exam->student->user->name,
                'tester_name'=> $this->exam->tester->user->name,
                'mark'=> $this->exam->mark,
                'part_name'=> $this->exam->quranPart->name.' '.$this->exam->quranPart->description,
                'datetime'=> $this->exam->datetime,
            ];
        } else {
            // في حالة كان نوع الاختبار سنة
            return [
                'id'=>$this->exam->id,
                'student_name'=> $this->exam->student->user->name,
                'tester_name'=> $this->exam->tester->user->name,
                'mark'=> $this->exam->mark,
                'part_name'=> $this->exam->sunnahPart->name. ' (' . $this->exam->sunnahPart->total_hadith_parts. ') حديث',
                'datetime'=> $this->exam->datetime,
            ];
        }
    }
}
