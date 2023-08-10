<?php

namespace App\Http\Livewire;

use App\Models\Exam;
use App\Models\ExamOrder;
use App\Models\ExamSettings;
use App\Models\QuranPart;
use App\Models\Student;
use App\Models\StudentDailyMemorization;
use App\Models\SunnahPart;
use App\Models\User;
use App\Notifications\NewExamOrderForExamsSupervisorNotify;
use App\Traits\NotificationTrait;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\MessageBag;
use JsonException;
use Spatie\Permission\Models\Role;

class SubmitOrderExam extends HomeComponent
{
    use NotificationTrait;

    public $student, $student_name, $quran_parts = [], $suggested_exam_days = [], $quran_part_id, $suggested_day;

    public $listeners = [
        'submit_exam_order' => 'requestExam',
    ];

    public function mount()
    {
        $this->current_role = auth()->user()->current_role;
        $this->link = 'manage_exams_orders/';
        $this->all_Suggested_Exam_Days();
    }

    public function all_Suggested_Exam_Days()
    {
        $this->suggested_exam_days = explode(',', ExamSettings::where('id', 1)->first()->suggested_exam_days);
    }

    public function render()
    {
        return view('livewire.submit-order-exam');
    }

    public function requestExam($id)
    {
        $this->clearForm();
        $this->resetValidation();
        $student = Student::where('id', $id)->first();
        $this->student = $student;
        $this->modalId = $student->id;
        $this->student_name = $student->user->name;

        $this->checkLastExamStatus();
    }

    private function clearForm()
    {
        $this->modalId = '';
        $this->student = null;
        $this->student_name = null;
        $this->suggested_day = null;
        $this->quran_part_id = null;
    }

    public function checkLastExamStatus()
    {
        $exam = Exam::where('student_id', $this->modalId)
            ->whereHas('quranPart', function ($q) {
                $q->orderBy('arrangement', 'asc');
            })
            ->orderBy('datetime', 'desc')->first();

        if ($exam) {
            if ($exam->mark >= $exam->exam_success_mark->mark) {
                $this->all_Quran_Parts();
                $this->dispatchBrowserEvent('showModalSubmitOrderExam');
            } else {
                $to = Carbon::createFromFormat('Y-m-d', date('Y-m-d', Carbon::now()->timestamp));
                $from = Carbon::createFromFormat('Y-m-d', Carbon::parse($exam->datetime)->format('Y-m-d'));
                $diff_in_days = $to->diffInDays($from);
                $exam_settings = ExamSettings::find(1);
                $number_days_exam = $exam_settings->number_days_exam;
                $days = ($diff_in_days - $number_days_exam);
                if ($days > 0) {
                    $quran_part_id = $exam->quranPart->type === QuranPart::INDIVIDUAL_TYPE ? $this->student->current_part_id : $this->student->current_part_cumulative_id;
                    if ($exam->quran_part_id === $quran_part_id) {
                        $revision_count = $exam->quranPart->type === QuranPart::INDIVIDUAL_TYPE ? $this->student->current_revision_count : $this->student->current_cumulative_revision_count;
                        if ($revision_count === 3) {
                            $number_days_revision_3 = ($diff_in_days - $exam_settings->number_days_exam_two_left);
                            if ($number_days_revision_3 > 0) {
                                $this->all_Quran_Parts();
                                $this->dispatchBrowserEvent('showModalSubmitOrderExam');
                            } else {
                                $this->messagePush($number_days_revision_3, $revision_count);
                            }
                        } else if ($revision_count > 3) {
                            $number_days_revision_4 = ($diff_in_days - $exam_settings->number_days_exam_three_left);
                            if ($number_days_revision_4 > 0) {
                                $this->all_Quran_Parts();
                                $this->dispatchBrowserEvent('showModalSubmitOrderExam');
                            } else {
                                $this->messagePush($number_days_revision_4, $revision_count);
                            }
                        } else {
                            $this->all_Quran_Parts();
                            $this->dispatchBrowserEvent('showModalSubmitOrderExam');
                        }
                    } else {
                        $this->all_Quran_Parts();
                        $this->dispatchBrowserEvent('showModalSubmitOrderExam');
                    }
                } else {
                    $this->messagePush($days);
                    $this->dispatchBrowserEvent('hideDialog');
                }
            }
        } else {
            $this->dispatchBrowserEvent('showModalSubmitOrderExam');
            $this->all_Quran_Parts();
        }
    }

    public function all_Quran_Parts()
    {
        $this->quran_parts = QuranPart::query()
            ->whereDoesntHave('exams', function ($q) {
                $q->whereHas('exam_success_mark', function ($q) {
                    $q->where(DB::raw('exams.mark'), '>=', DB::raw('exam_success_mark.mark'));
                })->where('student_id', '=', $this->student->id);
            })
            ->orderBy('arrangement')->get();
    }

    public function messagePush($days, $count_revision = 1)
    {
        if (abs($days) === 0) {
            if ($count_revision === 3) {
                $message = 'عذرا متبقي لهذا الطالب يوم حتى تتمكن من طلب اختبار جديد (بسبب الرسوب مرتين في الجزء).';
            } elseif ($count_revision >= 4) {
                $message = 'عذرا متبقي لهذا الطالب يوم حتى تتمكن من طلب اختبار جديد (بسبب عدد مرات الرسوب في الجزء).';
            } else {
                $message = 'عذرا متبقي لهذا الطالب يوم حتى تتمكن من طلب اختبار جديد.';
            }
            $this->dispatchBrowserEvent('alert',
                ['type' => 'error', 'message' => $message]);
        } else if (abs($days) === 1) {
            if ($count_revision === 3) {
                $message = 'عذرا متبقي لهذا الطالب يومان حتى تتمكن من طلب اختبار جديد (بسبب الرسوب مرتين في الجزء).';
            } elseif ($count_revision >= 4) {
                $message = 'عذرا متبقي لهذا الطالب يومان حتى تتمكن من طلب اختبار جديد (بسبب عدد مرات الرسوب في الجزء).';
            } else {
                $message = 'عذرا متبقي لهذا الطالب يومان حتى تتمكن من طلب اختبار جديد.';
            }
            $this->dispatchBrowserEvent('alert',
                ['type' => 'error', 'message' => $message]);
        } else if (abs($days) === 2) {
            if ($count_revision === 3) {
                $message = 'عذرا متبقي لهذا الطالب ثلاث أيام حتى تتمكن من طلب اختبار جديد (بسبب الرسوب مرتين في الجزء).';
            } elseif ($count_revision >= 4) {
                $message = 'عذرا متبقي لهذا الطالب ثلاث أيام حتى تتمكن من طلب اختبار جديد (بسبب عدد مرات الرسوب في الجزء).';
            } else {
                $message = 'عذرا متبقي لهذا الطالب ثلاث أيام حتى تتمكن من طلب اختبار جديد.';
            }
            $this->dispatchBrowserEvent('alert',
                ['type' => 'error', 'message' => $message]);
        } else if (in_array(abs($days), range(3, 10), true)) {
            if ($count_revision === 3) {
                $message = 'عذرا متبقي لهذا الطالب ' . abs($days) . ' أيام حتى تتمكن من طلب اختبار جديد (بسبب الرسوب مرتين في الجزء).';
            } elseif ($count_revision >= 4) {
                $message = 'عذرا متبقي لهذا الطالب ' . abs($days) . ' أيام حتى تتمكن من طلب اختبار جديد (بسبب عدد مرات الرسوب في الجزء).';
            } else {
                $message = 'عذرا متبقي لهذا الطالب ' . abs($days) . ' أيام حتى تتمكن من طلب اختبار جديد';
            }
            $this->dispatchBrowserEvent('alert',
                ['type' => 'error', 'message' => $message]);
        } else if (in_array(abs($days), range(11, 50), true)) {
            if ($count_revision === 3) {
                $message = 'عذرا متبقي لهذا الطالب ' . abs($days) . ' يوم حتى تتمكن من طلب اختبار جديد (بسبب الرسوب مرتين في الجزء).';
            } elseif ($count_revision >= 4) {
                $message = 'عذرا متبقي لهذا الطالب ' . abs($days) . ' يوم حتى تتمكن من طلب اختبار جديد (بسبب عدد مرات الرسوب في الجزء).';
            } else {
                $message = 'عذرا متبقي لهذا الطالب ' . abs($days) . ' يوم حتى تتمكن من طلب اختبار جديد';
            }
            $this->dispatchBrowserEvent('alert',
                ['type' => 'error', 'message' => $message]);
        }
    }

    /**
     * @throws JsonException
     */
    public function inconsistencyCheck()
    {
        $isComplete = false;
        $this->validate([
            'quran_part_id' => 'required|numeric',
            'suggested_day' => 'required|string',
        ]);

        $examOrder = ExamOrder::query()
            ->where('student_id', $this->modalId)->first();
        if ($examOrder !== null) {
            if ($examOrder->partable_type === SunnahPart::class &&
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
            $this->checkLinksToPreviousRecords();
        }
    }

    /**
     * @throws JsonException
     */
    public function checkLinksToPreviousRecords()
    {
        if ($this->quran_part_id == 16 || $this->quran_part_id == 17 || $this->quran_part_id == 18) {
            $this->checkLinksCustomRecords();
        } else {
            $messageBag = new MessageBag();
            $quranPart = QuranPart::whereId($this->quran_part_id)->first();
            if ($quranPart->type === QuranPart::INDIVIDUAL_TYPE) {
                if ($this->all_QuranSuras(StudentDailyMemorization::MEMORIZE_TYPE, [$this->quran_part_id])->isEmpty()) {
                    $revision_count = $this->student->current_part_id == $this->quran_part_id ? $this->student->current_revision_count : 1;
                    if ($revision_count != 2) {
                        if ($this->all_QuranSuras(StudentDailyMemorization::REVIEW_TYPE, [$this->quran_part_id], $revision_count)->isEmpty()) {
                            $this->submitExamRequest();
                        } else {
                            $message = 'عذرا, الطالب لم ينتهي من مراجعة الجزء.';
                            if ($revision_count >= 3) {
                                $message = 'عذرا, الطالب لم ينتهي من مراجعة الجزء للمرة ' . $revision_count . ' .';
                            }
                            $messageBag->add('quran_part_id', $message);
                        }
                    } else {
                        $this->submitExamRequest();
                    }
                } else {
                    $messageBag->add('quran_part_id', 'عذرا, الطالب لم ينتهي من حفظ الجزء.');
                }
            } else {
                $revision_count = $this->student->current_part_cumulative_id == $this->quran_part_id ? $this->student->current_cumulative_revision_count : 1;
                if ($revision_count != 2) {
                    $arr = explode('-', $quranPart->name ?? null);
                    if (count($arr) === 2) {
                        $parts_ids = [];
                        for ($i = $arr[1]; $i <= $arr[0]; $i++) {
                            $parts_ids[] = (int)$i;
                        }
                        $parts = QuranPart::query()->where('type', '=', QuranPart::INDIVIDUAL_TYPE)
                            ->whereIn('name', $parts_ids)
                            ->orderByDesc('id')->get();

                        if ($this->all_QuranSuras(StudentDailyMemorization::CUMULATIVE_REVIEW_TYPE,
                            $parts->pluck('id')->toArray(), $revision_count,
                            QuranPart::query()->where('id', $this->student->current_part_cumulative_id)
                                ->value('total_preservation_parts'))->isEmpty()) {
                            $this->submitExamRequest();
                        } else {
                            $message = 'عذرا, الطالب لم ينتهي من مراجعة سور أجزاء التجميعي.';
                            if ($revision_count >= 3) {
                                $message = 'عذرا, الطالب لم ينتهي من مراجعة سور أجزاء التجميعي للمرة ' . $revision_count . ' .';
                            }
                            $messageBag->add('quran_part_id', $message);
                        }
                    }
                } else {
                    $this->submitExamRequest();
                }
            }
            $this->setErrorBag($messageBag);
        }
    }

    private function all_QuranParts()
    {
        $arr = explode('-', $this->partsCombined->where('id', $this->selectedPartCombinedId)->first()->name ?? null);
        if (count($arr) === 2) {
            $parts_ids = [];
            for ($i = $arr[1]; $i <= $arr[0]; $i++) {
                $parts_ids[] = (int)$i;
            }

            $this->parts = QuranPart::query()->where('type', '=', QuranPart::INDIVIDUAL_TYPE)
                ->whereIn('name', $parts_ids)
                ->orderByDesc('id')->get();
        } else {
            $this->parts = [];
            $this->selectedPartId = null;
        }
    }


    /**
     * @throws JsonException
     */
    private function checkLinksCustomRecords()
    {
        // فحص سجلات سورة البقرة والفاتحة ...
        $messageBag = new MessageBag();
        if ($this->custom_QuranSuras(StudentDailyMemorization::MEMORIZE_TYPE, 1, $this->quran_part_id != 17)->isEmpty()) {
            $revision_count = $this->student->current_part_id == $this->quran_part_id ? $this->student->current_revision_count : 1;
            if ($revision_count != 2) {
                if ($this->custom_QuranSuras(StudentDailyMemorization::REVIEW_TYPE, $revision_count, $this->quran_part_id != 17)->isEmpty()) {
                    $this->submitExamRequest();
                } else {
                    $message = 'عذرا, الطالب لم ينتهي من مراجعة الجزء.';
                    if ($revision_count >= 3) {
                        $message = 'عذرا, الطالب لم ينتهي من مراجعة الجزء للمرة ' . $revision_count . ' .';
                    }
                    $messageBag->add('quran_part_id', $message);
                }
            } else {
                $this->submitExamRequest();
            }
        } else {
            $messageBag->add('quran_part_id', 'عذرا, الطالب لم ينتهي من حفظ الجزء.');
        }
        $this->setErrorBag($messageBag);
    }

    public function custom_QuranSuras($type, $revision_count = 1, $isSuraCompleted = true): Collection
    {
        return DB::table('quran_suras')
            ->select(['id', 'name', 'quran_part_id', 'total_number_aya', DB::raw('(select aya_to from  students_daily_memorization
                   INNER JOIN daily_memorization_details ON students_daily_memorization.id = daily_memorization_details.id
                   WHERE student_id = ' . $this->modalId . ' and type="' . $type . '" and revision_count="' . $revision_count . '"
                    and sura_id = quran_suras.id ORDER by aya_to desc LIMIT 1 ) as aya_to')])
            ->whereIn('id', [1, 2])
            ->havingNull(DB::raw('aya_to'))
            ->when($isSuraCompleted, function ($q) {
                $q->orHaving(DB::raw('aya_to'), '<', DB::raw('total_number_aya'));
            })->when(!$isSuraCompleted, function ($q) {
                $q->orHaving(DB::raw('id'), '=', 2)
                    ->having(DB::raw('aya_to'), '<', 141)
                    ->orHaving(DB::raw('id'), '=', 1)
                    ->having(DB::raw('aya_to'), '<', DB::raw('total_number_aya'));
            })
            ->orderByDesc('id')
            ->get();
    }

    public
    function all_QuranSuras($type, $current_part_id = null, $revision_count = 1, $cumulative_type = 1): Collection
    {
        return DB::table('quran_suras')
            ->select(['id', 'name', 'quran_part_id', 'total_number_aya', DB::raw('(select aya_to from  students_daily_memorization
                   INNER JOIN daily_memorization_details ON students_daily_memorization.id = daily_memorization_details.id
                   WHERE student_id = ' . $this->modalId . ' and type="' . $type . '" and cumulative_type="' . $cumulative_type . '"
                   and revision_count="' . $revision_count . '"  and sura_id = quran_suras.id ORDER by aya_to desc LIMIT 1 ) as aya_to')])
            ->when($current_part_id !== null, function ($q) use ($current_part_id) {
                $q->whereIn('quran_part_id', $current_part_id);
            })
            ->havingNull(DB::raw('aya_to'))
            ->orHaving(DB::raw('aya_to'), '<', DB::raw('total_number_aya'))
            ->orderByDesc('id')
            ->get();
    }

    /**
     * @throws JsonException
     */
    public function submitExamRequest()
    {
        $teacher_id = null;
        if ($this->current_role === User::ADMIN_ROLE) {
            $teacher_id = Student::with(['group'])->where('id', $this->modalId)->first()->group->teacher_id;
        } else if ($this->current_role === User::TEACHER_ROLE) {
            $teacher_id = auth()->id();
        }

        if ($teacher_id !== null) {
            $examOrder = ExamOrder::create([
                'partable_id' => $this->quran_part_id,
                'partable_type' => QuranPart::class,
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
                $message = "لقد قام المحفظ: " . $examOrder->teacher->user->name . " بطلب اختبار جديد للطالب " . $examOrder->student->user->name . " في الجزء: " . $examOrder->partable->name . ' ' . $examOrder->partable->description;
                $this->push_notification($message, $title, $this->link . $examOrder->id, [$role_users->first()->user_fcm_token->device_token ?? null]);
            }
            // end push notifications to exams supervisor

            $this->dispatchBrowserEvent('hideDialog');

            $this->dispatchBrowserEvent('alert',
                ['type' => 'success', 'message' => 'تمت عملية طلب الإختبار بنجاح.']);
            $this->clearForm();
        }
    }

    public
    function messages()
    {
        return [
            'modalId.required' => 'حقل الطالب مطلوب',
            'quran_part_id.required' => 'حقل الجزء مطلوب',
            'quran_part_id.numeric' => 'حقل الجزء يجب أن يكون رقم',
            'suggested_day.required' => 'حقل يوم الإختبار مطلوب',
            'suggested_day.string' => 'حقل يوم الإختبار يجب أن يكون نص',
        ];
    }
}
