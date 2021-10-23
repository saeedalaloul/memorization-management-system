<?php

namespace App\Http\Livewire;

use App\Models\ExamCustomQuestion;
use App\Models\ExamSettings;
use App\Models\QuranPart;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\MessageBag;
use Livewire\Component;
use Livewire\WithPagination;

class ExamsSettings extends Component
{
    use WithPagination;

    public $quran_part_id, $exam_question_count, $exam_question_count_update, $modalId;
    public $updateMode = false, $isOpenTabFirst = true, $isOpenTabSecond = false;

    public $allow_exams_update, $exam_questions_min, $exam_questions_max,
        $number_days_exam, $exam_success_rate;
    protected $paginationTheme = 'bootstrap';

    public function render()
    {
        return view('livewire.exams-settings', [
            'exam_custom_questions' => $this->all_Exams_Custom_Question(),
            'quran_parts' => $this->all_Quran_Parst(),
        ]);
    }

    public function setOpenTab($isOpen)
    {
        if ($isOpen == 1) {
            $this->isOpenTabFirst = !$this->isOpenTabFirst;
            $this->isOpenTabSecond = false;
        } else {
            $this->isOpenTabSecond = !$this->isOpenTabSecond;
            $this->isOpenTabFirst = false;
        }
    }

    public function mount()
    {
        $this->get_Exams_Settings();
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName, [
            'exam_questions_min' => 'required|numeric|between:7,10',
            'exam_questions_max' => 'required|numeric|between:7,10',
            'number_days_exam' => 'required|numeric|between:1,15',
            'exam_success_rate' => 'required|numeric|between:80,90',

            'quran_part_id' => 'required|unique:exam_custom_questions,quran_part_id,' . $this->modalId,
            'exam_question_count' => 'required|numeric|between:7,12',

            'exam_question_count_update' => 'required|numeric|between:7,12',
        ]);
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
                    'number_days_exam' => $this->number_days_exam,
                    'exam_success_rate' => $this->exam_success_rate,
                ]);
            } else {
                ExamSettings::create([
                    'allow_exams_update' => $this->allow_exams_update == null ? false : $this->allow_exams_update,
                    'exam_questions_min' => $this->exam_questions_min,
                    'exam_questions_max' => $this->exam_questions_max,
                    'number_days_exam' => $this->number_days_exam,
                    'exam_success_rate' => $this->exam_success_rate,
                ]);
            }
            session()->flash('success_message', 'تمت عملية حفظ اعدادات الإختبارات القرأنية بنجاح.');
        }
    }

    public function rules()
    {
        return [
            'exam_questions_min' => 'required|numeric|between:7,10',
            'exam_questions_max' => 'required|numeric|between:7,10',
            'number_days_exam' => 'required|numeric|between:1,15',
            'exam_success_rate' => 'required|numeric|between:80,90',
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
            'number_days_exam.numeric' => 'يجب أن يكون رقم',
            'number_days_exam.between' => 'يجب أن يكون الرقم بين 1 أو 15 يوم',
            'exam_success_rate.numeric' => 'يجب أن يكون رقم',
            'exam_success_rate.between' => 'يجب أن يكون الرقم بين 80 أو 90 علامة',

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
        $this->modalId = null;
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
            $this->number_days_exam = $exams_settings->number_days_exam;
            $this->exam_success_rate = $exams_settings->exam_success_rate;
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
            ExamCustomQuestion::create(['quran_part_id' => $this->quran_part_id, 'exam_question_count' => $this->exam_question_count]);
            $this->resetInputFields();
            session()->flash('success_message', 'تمت عملية حفظ البيانات بنجاح.');
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            session()->flash('failure_message', '' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $examCustomQuestion = ExamCustomQuestion::find($id);
        if ($examCustomQuestion) {
            $examCustomQuestion->delete();
            session()->flash('failure_message', 'تمت عملية حذف البيانات بنجاح.');
        }
    }

    public function edit($id, $count)
    {
        $this->updateMode = true;
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
            $examCustomQuestion->update(['exam_question_count' => $this->exam_question_count_update]);
            session()->flash('success_message', 'تمت عملية تحديث البيانات بنجاح.');
            $this->resetInputFieldsUpdated();
        }
    }

    public function resetInputFields()
    {
        $this->quran_part_id = '';
        $this->exam_question_count = '';
        $this->resetValidation();
    }

    public function resetInputFieldsUpdated(){
        $this->updateMode = false;
        $this->modalId = null;
        $this->exam_question_count_update = null;
        $this->resetValidation();
    }

    public function all_Exams_Custom_Question()
    {
        return ExamCustomQuestion::all();
    }

    public function all_Quran_Parst()
    {
        return QuranPart::query()->whereDoesntHave('examCustomQuestion')->orderBy('id')->get();
    }
}
