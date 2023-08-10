<?php

namespace App\Http\Livewire;

use App\Models\Exam;
use App\Models\Student;
use App\Models\StudentDailyMemorization;

class ResetDailyMemorization extends HomeComponent
{

    public $student_name, $student_id, $reset_data_daily_memorization_type, $last_quran_part_id,
        $message_warning_reset_data;

    public $listeners = [
        'reset_daily_memorization' => 'reset_data_daily_memorization',
    ];

    public function render()
    {
        return view('livewire.reset-daily-memorization');
    }

    public function reset_data_daily_memorization($id)
    {
        $this->clearForm();
        $this->resetValidation();
        $this->reset_data_daily_memorization_type = 0;
        $student = Student::where('id', $id)->first();
        $this->student_id = $student->id;
        $this->student_name = $student->user->name;
        $this->last_quran_part_id = $student->current_part_id;
        $this->dispatchBrowserEvent('showDialogDailyMemorization');
    }

    public function updatedResetDataDailyMemorizationType($type)
    {
        $dailyMemorization = StudentDailyMemorization::query()
            ->where('student_id', $this->student_id)
            ->orderByDesc('datetime')->first();

        if ($type === '1') {
            // تصفير البيانات لبداية الجزء الحالي.
            if ($dailyMemorization !== null) {
                $this->message_warning_reset_data = "سيتم حذف جميع بيانات الحفظ والمراجعة لسور الجزء الحالي " . $dailyMemorization->quranSuraTo->quranPart->description;
            } else {
                $this->message_warning_reset_data = "لا بيانات سابقة";
            }
        } elseif ($type === '2') {
            // تصفير جميع بيانات الحفظ والمراجعة وأي اختبارات أنجزها الطالب.

            $exam = Exam::where('student_id', $this->student_id)->orderBy('datetime', 'desc')->first();

            if ($exam !== null) {
                if ($exam->mark >= $exam->exam_success_mark->mark) {
                    $countOfPartQuran = $exam->quranPart->total_memorization_parts;
                } else {
                    $countOfPartQuran = $exam->quranPart->total_memorization_parts - 1;
                }

                if ($countOfPartQuran === 0) {
                    $this->message_warning_reset_data = "سيتم حذف جميع بيانات الحفظ والمراجعة وأي اختبارات أنجزها الطالب.";
                } elseif ($countOfPartQuran === 1) {
                    $this->message_warning_reset_data = "سيتم حذف جميع بيانات الحفظ والمراجعة وأي اختبارات أنجزها الطالب والبالغ عددها اختبار واحد.";
                } else if (in_array($countOfPartQuran, range(2, 30), true)) {
                    $this->message_warning_reset_data = "سيتم حذف جميع بيانات الحفظ والمراجعة وأي اختبارات أنجزها الطالب والبالغ عددها" . " ($countOfPartQuran) " . "اختبار.";
                }
            } else if ($dailyMemorization === null) {
                $this->message_warning_reset_data = "لا اختبارات أو بيانات سابقة";
            }
        }
    }

    public function reset_daily_memorization()
    {
        if ($this->student_id !== null) {
            $student = Student::find($this->student_id);
            if ($this->reset_data_daily_memorization_type === '1' && $this->last_quran_part_id !== null) {
                $student->daily_memorization()->whereHas('quranSuraFrom', function ($q) {
                    $q->where('quran_part_id', $this->last_quran_part_id);
                })->whereHas('quranSuraTo', function ($q) {
                    $q->where('quran_part_id', $this->last_quran_part_id);
                })->delete();
                $this->emit('refresh');
                $this->dispatchBrowserEvent('hideDialog');
                $this->dispatchBrowserEvent('alert',
                    ['type' => 'success', 'message' => 'تم حذف بيانات الحفظ والمراجعة للجزء الحالي للطالب بنجاح.']);
            } else if ($this->reset_data_daily_memorization_type === '2') {
                $student->daily_memorization()->delete(); // حذف جميع بيانات الحفظ والمراجعة.
                $student->exam_order()->delete(); // حذف جميع طلبات الإختبارات للطالب.
                $student->exams()->delete(); // حذف جميع اختبارات الطالب.
                $this->emit('refresh');

                $this->dispatchBrowserEvent('hideDialog');
                $this->dispatchBrowserEvent('alert',
                    ['type' => 'success', 'message' => 'تم حذف جميع بيانات الحفظ والمراجعة وأي اختبارات قرآنية أنجزها الطالب بنجاح.']);
            }
        }
    }


    private function clearForm()
    {
        $this->student_name = null;
        $this->student_id = null;
        $this->reset_data_daily_memorization_type = null;
        $this->last_quran_part_id = null;
        $this->message_warning_reset_data = null;
    }
}
