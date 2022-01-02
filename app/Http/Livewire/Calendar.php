<?php

namespace App\Http\Livewire;

use App\Models\Event;
use App\Models\ExamOrder;
use Livewire\Component;

class Calendar extends Component
{

    public $events = '';

    public function getevent()
    {
        $exams = ExamOrder::query()->
        select('id', 'exam_date as start', 'student_id', 'quran_part_id')
            ->whereNotNull('exam_date')
            ->where('status', '=', 2)
            ->orderByDesc('exam_date')->limit(10)->get();
        $events = [];

        if ($exams) {
            foreach ($exams as $key => $exam) {
                $events[$key]['id'] = $exam->id;
                $events[$key]['start'] = $exam->start;
                $events[$key]['title'] = $exam->quranPart->name . " للطالب: " . $exam->student->user['name'];
            }
        }


        return json_encode($events);
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function addevent($event)
    {
//        $input['title'] = $event['title'];
//        $input['start'] = $event['start'];
//        Event::create($input);
    }


    /**
     * Write code on Method
     *
     * @return response()
     */
    public function eventDrop($event, $oldEvent)
    {
        $eventdata = ExamOrder::find($event['id']);
        $eventdata->exam_date = $event['start'];
        $eventdata->save();
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function render()
    {

        $exams = ExamOrder::query()->
        select('id', 'exam_date as start', 'student_id', 'quran_part_id')
            ->whereNotNull('exam_date')
            ->where('status', '=', 2)
            ->orderByDesc('exam_date')->limit(10)->get();
        $events = [];

        if ($exams) {
            foreach ($exams as $key => $exam) {
                $events[$key]['id'] = $exam->id;
                $events[$key]['start'] = $exam->start;
                $events[$key]['title'] = $exam->quranPart->name . " للطالب: " . $exam->student->user['name'];
            }
        }
        $this->events = json_encode($events);

        return view('livewire.calendar');
    }
}
