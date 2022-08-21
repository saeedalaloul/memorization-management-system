<?php

namespace App\Http\Livewire;

use App\Models\ExamCustomQuestion;
use App\Models\ExamSettings;
use App\Models\QuranPart;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\MessageBag;

class ExamsSettings extends HomeComponent
{
    public $quran_part_id, $exam_question_count, $exam_question_count_update;

    public $allow_exams_update, $exam_questions_min, $exam_questions_max, $exam_questions_summative_three_part,
        $exam_questions_summative_five_part, $exam_questions_summative_ten_part,
        $number_days_exam, $exam_success_rate, $summative_exam_success_rate;

    public function render()
    {
        return view('livewire.exams-settings', [
            'exam_custom_questions' => $this->all_Exams_Custom_Question(),
            'quran_parts' => $this->all_Quran_Parst(),
        ]);
    }

    public function mount()
    {
        $this->current_role = auth()->user()->current_role;
        $this->get_Exams_Settings();
    }

    public function store()
    {
        $this->validate();
        if ($this->exam_questions_min > $this->exam_questions_max) {
            $messageBag = new MessageBag;
            $messageBag->add('exam_questions_min', 'يجب أن لا يكون أدنى رقم أكبر من أقصى رقم');
            $this->setErrorBag($messageBag);
        } else {
            $examSettings = ExamSettings::find(1);
            if ($examSettings) {
                $examSettings->update([
                    'allow_exams_update' => $this->allow_exams_update == null ? false : $this->allow_exams_update,
                    'exam_questions_min' => $this->exam_questions_min,
                    'exam_questions_max' => $this->exam_questions_max,
                    'exam_questions_summative_three_part' => $this->exam_questions_summative_three_part,
                    'exam_questions_summative_five_part' => $this->exam_questions_summative_five_part,
                    'exam_questions_summative_ten_part' => $this->exam_questions_summative_ten_part,
                    'number_days_exam' => $this->number_days_exam,
                    'exam_success_rate' => $this->exam_success_rate,
                    'summative_exam_success_rate' => $this->summative_exam_success_rate,
                ]);
            } else {
                ExamSettings::create([
                    'allow_exams_update' => $this->allow_exams_update == null ? false : $this->allow_exams_update,
                    'exam_questions_min' => $this->exam_questions_min,
                    'exam_questions_max' => $this->exam_questions_max,
                    'exam_questions_summative_three_part' => $this->exam_questions_summative_three_part,
                    'exam_questions_summative_five_part' => $this->exam_questions_summative_five_part,
                    'exam_questions_summative_ten_part' => $this->exam_questions_summative_ten_part,
                    'number_days_exam' => $this->number_days_exam,
                    'exam_success_rate' => $this->exam_success_rate,
                    'summative_exam_success_rate' => $this->summative_exam_success_rate,
                ]);
            }
            $this->dispatchBrowserEvent('alert',
                ['type' => 'success', 'message' => 'تمت عملية حفظ اعدادات الإختبارات القرأنية بنجاح.']);
        }
    }

    public function rules()
    {
        return [
            'exam_questions_min' => 'required|numeric|between:7,10',
            'exam_questions_max' => 'required|numeric|between:7,10',
            'exam_questions_summative_three_part' => 'required|numeric|between:3,4',
            'exam_questions_summative_five_part' => 'required|numeric|between:4,5',
            'exam_questions_summative_ten_part' => 'required|numeric|between:4,5',
            'number_days_exam' => 'required|numeric|between:1,15',
            'exam_success_rate' => 'required|numeric|between:80,90',
            'summative_exam_success_rate' => 'required|numeric|between:80,90',
        ];
    }

    public function messages()
    {
        return [
            'exam_questions_min.required' => 'يجب تحديد أدنى عدد أسئلة اختبار',
            'exam_questions_min.numeric' => 'يجب أن يكون رقم',
            'exam_questions_min.between' => 'يجب أن يكون الرقم بين 7 أو 10 أسئلة',
            'exam_questions_max.required' => 'يجب تحديد أقصى عدد أسئلة اختبار',
            'exam_questions_max.numeric' => 'يجب أن يكون رقم',
            'exam_questions_max.between' => 'يجب أن يكون الرقم بين 7 أو 10 أسئلة',
            'exam_questions_summative_three_part.required' => 'يجب تحديد عدد أسئلة اختبار التجميعي (3) أجزاء',
            'exam_questions_summative_three_part.numeric' => 'يجب أن يكون رقم',
            'exam_questions_summative_three_part.between' => 'يجب أن يكون الرقم بين 3 أو 4 أسئلة',
            'exam_questions_summative_five_part.required' => 'يجب تحديد عدد أسئلة اختبار التجميعي (5) أجزاء',
            'exam_questions_summative_five_part.numeric' => 'يجب أن يكون رقم',
            'exam_questions_summative_five_part.between' => 'يجب أن يكون الرقم بين 4 أو 5 أسئلة',
            'exam_questions_summative_ten_part.required' => 'يجب تحديد عدد أسئلة اختبار التجميعي (10) أجزاء',
            'exam_questions_summative_ten_part.numeric' => 'يجب أن يكون رقم',
            'exam_questions_summative_ten_part.between' => 'يجب أن يكون الرقم بين 4 أو 5 أسئلة',
            'number_days_exam.numeric' => 'يجب أن يكون رقم',
            'number_days_exam.between' => 'يجب أن يكون الرقم بين 1 أو 15 يوم',
            'exam_success_rate.numeric' => 'يجب أن يكون رقم',
            'exam_success_rate.between' => 'يجب أن يكون الرقم بين 80 أو 90 علامة',
            'summative_exam_success_rate.numeric' => 'يجب أن يكون رقم',
            'summative_exam_success_rate.between' => 'يجب أن يكون الرقم بين 80 أو 90 علامة',
            'quran_part_id.required' => 'يجب اختيار الجزء',
            'quran_part_id.unique' => 'الجزء المحدد موجود مسبقا',
            'exam_question_count.required' => 'حقل عدد أسئلة جزء الإختبار مطلوب',
            'exam_question_count.numeric' => 'يجب أن يكون رقم',
            'exam_question_count.between' => 'يجب أن يكون الرقم بين 7 أو 12 سؤال',

            'exam_question_count_update.required' => 'حقل عدد أسئلة جزء الإختبار مطلوب',
            'exam_question_count_update.numeric' => 'يجب أن يكون رقم',
            'exam_question_count_update.between' => 'يجب أن يكون الرقم بين 7 أو 12 سؤال',
        ];
    }

    public function clearForm()
    {
        $this->modalId = '';
        $this->quran_part_id = null;
        $this->exam_question_count = null;
        $this->resetValidation();
    }

    public function get_Exams_Settings()
    {
        $exams_settings = ExamSettings::find(1);
        if ($exams_settings) {
            $this->allow_exams_update = $exams_settings->allow_exams_update;
            $this->exam_questions_max = $exams_settings->exam_questions_max;
            $this->exam_questions_min = $exams_settings->exam_questions_min;
            $this->exam_questions_summative_three_part = $exams_settings->exam_questions_summative_three_part;
            $this->exam_questions_summative_five_part = $exams_settings->exam_questions_summative_five_part;
            $this->exam_questions_summative_ten_part = $exams_settings->exam_questions_summative_ten_part;
            $this->number_days_exam = $exams_settings->number_days_exam;
            $this->exam_success_rate = $exams_settings->exam_success_rate;
            $this->summative_exam_success_rate = $exams_settings->summative_exam_success_rate;
        }
    }

    public function storeExamsCustomQuestion()
    {
        $this->validate([
            'quran_part_id' => 'required|unique:exam_custom_questions,quran_part_id,',
            'exam_question_count' => 'required|numeric|between:7,12',
        ]);
        DB::beginTransaction();
        try {
            ExamCustomQuestion::create(['quran_part_id' => $this->quran_part_id, 'question_count' => $this->exam_question_count]);
            $this->dispatchBrowserEvent('alert',
                ['type' => 'success', 'message' => 'تمت عملية حفظ البيانات بنجاح.']);
            DB::commit();
            $this->resetInputFields();
        } catch (Exception $e) {
            DB::rollback();
            $this->catchError = $e->getMessage();
        }
    }

    public function destroy($id)
    {
        $examCustomQuestion = ExamCustomQuestion::find($id);
        if ($examCustomQuestion) {
            $examCustomQuestion->delete();
            $this->dispatchBrowserEvent('alert',
                ['type' => 'error', 'message' => 'تمت عملية حذف البيانات بنجاح.']);
        }
    }

    public function edit($id, $count)
    {
        $this->modalId = $id;
        $this->exam_question_count_update = $count;
    }

    public function update()
    {
        $this->validate([
            'exam_question_count_update' => 'required|numeric|between:7,12',
        ]);
        $examCustomQuestion = ExamCustomQuestion::find($this->modalId);
        if ($examCustomQuestion) {
            $examCustomQuestion->update(['question_count' => $this->exam_question_count_update]);
            $this->dispatchBrowserEvent('alert',
                ['type' => 'success', 'message' => 'تمت عملية تحديث البيانات بنجاح.']);
            $this->resetInputFieldsUpdated();
        }
    }

    public function resetInputFields()
    {
        $this->quran_part_id = '';
        $this->exam_question_count = '';
        $this->resetValidation();
    }

    public function resetInputFieldsUpdated()
    {
        $this->modalId = '';
        $this->exam_question_count_update = null;
        $this->resetValidation();
    }

    public function all_Exams_Custom_Question()
    {
        return ExamCustomQuestion::with(['quranPart'])->get();
    }

    public function all_Quran_Parst()
    {
        return QuranPart::query()->where('type', '!=', QuranPart::DESERVED_TYPE)->whereDoesntHave('examCustomQuestion')->orderBy('id')->get();
    }
}
