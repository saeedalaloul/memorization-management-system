<?php

namespace App\Http\Livewire;

use App\Models\ExamOrder;
use App\Models\ExamSettings;
use App\Models\QuranPart;
use App\Models\Student;
use App\Models\StudentSunnahDailyMemorization;
use App\Models\SunnahExam;
use App\Models\SunnahPart;
use App\Models\User;
use App\Notifications\NewExamOrderForExamsSupervisorNotify;
use App\Traits\NotificationTrait;
use Carbon\Carbon;
use Illuminate\Support\MessageBag;
use Spatie\Permission\Models\Role;

class SubmitOrderExamSunnah extends HomeComponent
{
    use NotificationTrait;

    public $student_name, $sunnah_parts = [], $suggested_exam_days = [], $sunnah_part_id, $suggested_day;

    public $listeners = [
        'submit_exam_sunnah_order' => 'requestExam',
    ];

    public function mount()
    {
        $this->current_role = auth()->user()->current_role;
        $this->link = 'manage_exams_orders/';
        $this->all_Suggested_Exam_Days();
    }

    public function render()
    {
        return view('livewire.submit-order-exam-sunnah');
    }

    public function requestExam($id)
    {
        $this->clearForm();
        $this->resetValidation();
        $student = Student::where('id', $id)->first();
        $this->modalId = $student->id;
        $this->student_name = $student->user->name;

        $this->checkLastExamStatus();
    }


    public function checkLastExamStatus()
    {
        $exam = SunnahExam::where('student_id', $this->modalId)
            ->whereHas('sunnahPart', function ($q) {
                $q->orderBy('arrangement', 'desc');
            })
            ->orderBy('datetime', 'desc')->first();

        if ($exam) {
            if ($exam->mark >= $exam->exam_success_mark->mark) {
                $this->all_Sunnah_Parts($exam->sunnahPart->arrangement, true);
                $this->dispatchBrowserEvent('showModalSubmitOrderExamSunnah');
            } else {
                $to = Carbon::createFromFormat('Y-m-d', date('Y-m-d', Carbon::now()->timestamp));
                $from = Carbon::createFromFormat('Y-m-d', Carbon::parse($exam->datetime)->format('Y-m-d'));
                $diff_in_days = $to->diffInDays($from);
                $number_days_exam = ExamSettings::find(1)->number_days_exam_sunnah;
                $days = ($diff_in_days - $number_days_exam);
                if ($days > 0) {
                    $this->all_Sunnah_Parts($exam->sunnah_part_id, false);
                    $this->dispatchBrowserEvent('showModalSubmitOrderExamSunnah');
                } else {
                    if (abs($days) === 0) {
                        $this->dispatchBrowserEvent('alert',
                            ['type' => 'error', 'message' => 'عذرا متبقي لهذا الطالب يوم حتى تتمكن من طلب اختبار جديد.']);
                    } else if (abs($days) === 1) {
                        $this->dispatchBrowserEvent('alert',
                            ['type' => 'error', 'message' => 'عذرا متبقي لهذا الطالب يومان حتى تتمكن من طلب اختبار جديد.']);
                    } else if (abs($days) === 2) {
                        $this->dispatchBrowserEvent('alert',
                            ['type' => 'error', 'message' => 'عذرا متبقي لهذا الطالب ثلاث أيام حتى تتمكن من طلب اختبار جديد.']);
                    } else if (in_array(abs($days), range(3, 10), true)) {
                        $this->dispatchBrowserEvent('alert',
                            ['type' => 'error', 'message' => 'عذرا متبقي لهذا الطالب ' . abs($days) . ' أيام حتى تتمكن من طلب اختبار جديد']);
                    } else if (in_array(abs($days), range(11, 15), true)) {
                        $this->dispatchBrowserEvent('alert',
                            ['type' => 'error', 'message' => 'عذرا متبقي لهذا الطالب ' . abs($days) . ' يوم حتى تتمكن من طلب اختبار جديد']);
                    }
                    $this->dispatchBrowserEvent('hideDialog');
                }
            }
        } else {
            $this->dispatchBrowserEvent('showModalSubmitOrderExamSunnah');
            $this->all_Sunnah_Parts(null, null);
        }
    }

    public function all_Suggested_Exam_Days()
    {
        $this->suggested_exam_days = explode(',', ExamSettings::where('id', 1)->first()->suggested_exam_days);
    }

    public function all_Sunnah_Parts($arrangement, $isSuccess)
    {
        if ($arrangement !== null) {
            if ($isSuccess) {
                $this->sunnah_parts = SunnahPart::query()->where('arrangement', $arrangement + 1)->get();
            } else {
                $this->sunnah_parts = SunnahPart::query()->where('id', $arrangement)->get();
            }
        } else {
            $this->sunnah_parts = SunnahPart::query()->orderBy('arrangement')->get();
        }
    }

    public function submitExamRequest()
    {
        $isComplete = false;
        $this->validate([
            'sunnah_part_id' => 'required|numeric',
            'suggested_day' => 'required|string',
        ]);

        $examOrder = ExamOrder::query()
            ->where('student_id', $this->modalId)->first();
        if ($examOrder !== null) {
            if ($examOrder->partable_type === QuranPart::class &&
                $examOrder->status === ExamOrder::FAILURE_STATUS ||
                $examOrder->status === ExamOrder::REJECTED_STATUS) {
                $examOrder->delete();
                $isComplete = true;
            } else {
                $messageBag = new MessageBag();
                $messageBag->add('modalId', 'عذرا يوجد طلب مسبق لهذا الطالب');
                $this->setErrorBag($messageBag);
            }
        } else {
            $isComplete = true;
        }

        if ($isComplete) {
            $sunnahPart = SunnahPart::where('id', $this->sunnah_part_id)->first();
            if ($sunnahPart) {
                $dailyMemorization = StudentSunnahDailyMemorization::query()->where('student_id', $this->modalId)
                    ->where('type', StudentSunnahDailyMemorization::MEMORIZE_TYPE)
                    ->where('book_id', $sunnahPart->sunnah_book_id)
                    ->orderByDesc('datetime')->first();

                if ($dailyMemorization !== null && $dailyMemorization->hadith_to >= intval($sunnahPart->name)) {
                    $teacher_id = null;
                    if ($this->current_role === 'أمير المركز') {
                        $teacher_id = Student::with(['group_sunnah'])->where('id', $this->modalId)->first()->group_sunnah->teacher_id;
                    } else if ($this->current_role === 'محفظ') {
                        $teacher_id = auth()->id();
                    }

                    if ($teacher_id !== null) {
                        $examOrder = ExamOrder::create([
                            'partable_id' => $this->sunnah_part_id,
                            'partable_type' => SunnahPart::class,
                            'student_id' => $this->modalId,
                            'teacher_id' => $teacher_id,
                            'user_signature_id' => auth()->id(),
                            'status' => ExamOrder::IN_PENDING_STATUS,
                            'type' => ExamOrder::NEW_TYPE,
                            'suggested_day' => $this->suggested_day,
                        ]);


                        // start push notifications to exams supervisor
                        $role = Role::where('name', User::EXAMS_SUPERVISOR_ROLE)->first();
                        $role_users = $role->users();
                        if ($role_users->first()) {
                            $role_users->first()->notify(new NewExamOrderForExamsSupervisorNotify($examOrder));
                            $title = "طلب اختبار جديد";
                            if ($examOrder->partable_type === 'App\Models\QuranPart') {
                                $message = "لقد قام المحفظ: " . $examOrder->teacher->user->name . " بطلب اختبار جديد للطالب " . $examOrder->student->user->name . " في الجزء: " . $examOrder->partable->name . ' ' . $examOrder->partable->description;
                            } else {
                                $message = "لقد قام المحفظ: " . $examOrder->teacher->user->name . " بطلب اختبار جديد للطالب " . $examOrder->student->user->name . " في الجزء: " . $examOrder->partable->name . ' (' . $examOrder->partable->total_hadith_parts . ') حديث';
                            }
                            $this->push_notification($message, $title, $this->link . $examOrder->id, [$role_users->first()->user_fcm_token->device_token ?? null]);
                        }
                        // end push notifications to exams supervisor

                        $this->dispatchBrowserEvent('hideDialog');

                        $this->dispatchBrowserEvent('alert',
                            ['type' => 'success', 'message' => 'تمت عملية طلب الإختبار بنجاح.']);
                        $this->clearForm();
                    }
                } else {
                    $messageBag = new MessageBag();
                    $messageBag->add('sunnah_part_id', 'عذرا لا يمكنك إجراء طلب الإختبار بسبب عدم الإنتهاء من حفظ الأحاديث المطلوبة لهذا الإختبار.');
                    $this->setErrorBag($messageBag);
                }
            }
        }
    }

    private function clearForm()
    {
        $this->modalId = '';
        $this->student_name = null;
        $this->suggested_day = null;
        $this->sunnah_part_id = null;
    }

    public
    function messages()
    {
        return [
            'modalId.required' => 'حقل الطالب مطلوب',
            'sunnah_part_id.required' => 'حقل الجزء مطلوب',
            'sunnah_part_id.numeric' => 'حقل الجزء يجب أن يكون رقم',
            'suggested_day.required' => 'حقل يوم الإختبار مطلوب',
            'suggested_day.string' => 'حقل يوم الإختبار يجب أن يكون نص',
        ];
    }
}
