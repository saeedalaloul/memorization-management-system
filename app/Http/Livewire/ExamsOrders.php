<?php

namespace App\Http\Livewire;

use App\Models\ExamOrder;
use App\Models\Grade;
use App\Models\Group;
use App\Models\QuranPart;
use App\Models\Student;
use App\Models\Supervisor;
use App\Models\Teacher;
use App\Models\User;
use App\Notifications\AcceptExamOrderForTeacherNotify;
use App\Notifications\AcceptExamOrderForTesterNotify;
use App\Notifications\RejectionExamOrderForTeacherNotify;
use App\Traits\NotificationTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\MessageBag;

class ExamsOrders extends HomeComponent
{
    use NotificationTrait;

    public $grades = [], $groups = [], $students = [];
    public $selectedGradeId, $selectedTeacherId, $selectedStudentId, $selectedStatus, $current_group_type;
    public $student_name, $quran_part, $last_tester_name, $number_failure_times;
    public $tester_id, $teacher_id, $exam_date, $notes;

    protected $listeners = [
        'getTeachersByGradeId' => 'getTeachersByGradeId',
        'getStudentsByTeacherId' => 'getStudentsByTeacherId',
    ];

    public function render()
    {
        return view('livewire.exams-orders', ['exam_orders' => $this->all_Exam_Orders()]);
    }

    public function mount()
    {
        $this->current_role = auth()->user()->current_role;
        $this->link = 'manage_exams_orders/';
        $this->all_Grades();
        $this->all_Testers();
        if ($this->current_role === User::TEACHER_ROLE) {
            $this->current_group_type = Group::where('teacher_id', auth()->id())->first()->type ?? null;
        }
    }

    public function all_Exam_Orders()
    {
        if ($this->current_role === 'محفظ' && $this->current_group_type === null) {
            return [];
        }

        return ExamOrder::query()
            ->with(['student.user:id,name', 'partable', 'teacher.user:id,name', 'tester.user:id,name'])
            ->search($this->search)
            ->when($this->current_role === 'محفظ' && $this->current_group_type === Group::QURAN_TYPE || $this->current_group_type === Group::MONTADA_TYPE, function ($q, $v) {
                $q->whereHas('student', function ($q) {
                    $q->where('group_id', '=', $this->selectedTeacherId)
                        ->when($this->selectedStudentId != null, function ($q, $v) {
                            $q->where('id', '=', $this->selectedStudentId);
                        });
                });
            })->when($this->current_role === 'محفظ' && $this->current_group_type === Group::SUNNAH_TYPE, function ($q, $v) {
                $q->whereHas('student', function ($q) {
                    $q->where('group_sunnah_id', '=', $this->selectedTeacherId)
                        ->when($this->selectedStudentId != null, function ($q, $v) {
                            $q->where('id', '=', $this->selectedStudentId);
                        });
                });
            })
            ->when($this->current_role === 'مختبر', function ($q, $v) {
                $q->where('tester_id', auth()->id())
                    ->when($this->selectedGradeId != null, function ($q, $v) {
                        $q->whereHas('student', function ($q) {
                            $q->where('grade_id', '=', $this->selectedGradeId)
                                ->when($this->selectedStudentId != null, function ($q, $v) {
                                    $q->where('id', '=', $this->selectedStudentId);
                                })
                                ->when($this->selectedTeacherId != null, function ($q, $v) {
                                    $q->where('group_id', '=', $this->selectedTeacherId)
                                        ->orWhere('group_sunnah_id', '=', $this->selectedTeacherId);
                                });
                        });
                    });
            })
            ->when($this->current_role === 'مشرف', function ($q, $v) {
                $q->whereHas('student', function ($q) {
                    $q->where('grade_id', '=', $this->selectedGradeId)
                        ->when($this->selectedStudentId != null, function ($q, $v) {
                            $q->where('id', '=', $this->selectedStudentId);
                        })
                        ->when($this->selectedTeacherId != null, function ($q, $v) {
                            $q->where('group_id', '=', $this->selectedTeacherId)
                                ->orWhere('group_sunnah_id', '=', $this->selectedTeacherId);
                        });
                });
            })
            ->when($this->current_role === 'أمير المركز' || $this->current_role === 'مشرف الإختبارات', function ($q, $v) {
                $q->whereHas('student', function ($q) {
                    $q->when($this->selectedGradeId != null, function ($q) {
                        $q->where('grade_id', '=', $this->selectedGradeId);
                    })->when($this->selectedStudentId != null, function ($q) {
                        $q->where('id', '=', $this->selectedStudentId);
                    })->when($this->selectedTeacherId != null, function ($q) {
                        $q->where('group_id', '=', $this->selectedTeacherId)
                            ->orWhere('group_sunnah_id', '=', $this->selectedTeacherId);
                    });
                });
            })->when($this->current_role === User::SPONSORSHIP_SUPERVISORS_ROLE, function ($q) {
                $q->whereHas('student', function ($q) {
                    $groups_ids = DB::table('sponsorship_groups')
                        ->select(['group_id'])
                        ->whereIn('sponsorship_id', auth()->user()->sponsorships->pluck('id')->toArray())
                        ->distinct()
                        ->pluck('group_id')->toArray();
                    $q->whereIn('group_id', $groups_ids)
                        ->when($this->selectedGradeId != null, function ($q) {
                            $q->where('grade_id', '=', $this->selectedGradeId);
                        })->when($this->selectedStudentId != null, function ($q) {
                            $q->where('id', '=', $this->selectedStudentId);
                        })->when($this->selectedTeacherId != null, function ($q) {
                            $q->where('group_id', '=', $this->selectedTeacherId);
                        });
                });
            })
            ->when($this->selectedStatus != null, function ($q, $v) {
                $q->where('status', '=', $this->selectedStatus);
            })
            ->when(!empty((string)\Request::segment(2) && strval(\Request::segment(2)) !== 'message'), function ($q, $v) {
                $q->where('id', \Request::segment(2));
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function submitSearch()
    {
        $this->all_Exam_Orders();
    }


    public function all_Grades()
    {
        if ($this->current_role === 'مشرف') {
            $this->selectedGradeId = Supervisor::where('id', auth()->id())->first()->grade_id;
            $this->grades = Grade::query()->where('id', $this->selectedGradeId)->get();
        } else if ($this->current_role === 'محفظ') {
            $this->selectedTeacherId = Group::where('teacher_id', auth()->id())->first()->id ?? null;
            $this->grades = Grade::query()->where('id', Teacher::where('id', auth()->id())->first()->grade_id)->get();
        } else if ($this->current_role === 'أمير المركز' || $this->current_role === 'مشرف الإختبارات' || $this->current_role === User::SPONSORSHIP_SUPERVISORS_ROLE || $this->current_role === 'مختبر') {
            $this->grades = Grade::all();
        }
    }

    public function getTeachersByGradeId()
    {
        $this->reset('students', 'groups', 'selectedTeacherId', 'selectedStudentId');

        if ($this->current_role === 'مشرف') {
            if ($this->selectedGradeId) {
                $this->groups = Group::query()->with(['teacher.user'])->where('grade_id', $this->selectedGradeId)->get();
            }
        } else if ($this->current_role === 'أمير المركز' || $this->current_role === 'مشرف الإختبارات' || $this->current_role === 'مختبر') {
            if ($this->selectedGradeId) {
                $this->groups = Group::query()->with(['teacher.user'])->where('grade_id', $this->selectedGradeId)->get();
            }
        } else if ($this->current_role === User::SPONSORSHIP_SUPERVISORS_ROLE) {
            if ($this->selectedGradeId) {
                $groups_ids = DB::table('sponsorship_groups')
                    ->select(['group_id'])
                    ->whereIn('sponsorship_id', auth()->user()->sponsorships->pluck('id')->toArray())
                    ->distinct()
                    ->pluck('group_id')->toArray();
                $this->groups = Group::query()->with(['teacher.user'])->whereIn('id', $groups_ids)->get();
            }
        } elseif ($this->current_role === 'محفظ') {
            $this->groups = Group::query()->where('teacher_id', auth()->id())->get();
        }
    }

    public function getStudentsByTeacherId()
    {
        $this->reset('students', 'selectedStudentId');

        if ($this->current_role === 'أمير المركز' || $this->current_role === 'مشرف الإختبارات' || $this->current_role === User::SPONSORSHIP_SUPERVISORS_ROLE || $this->current_role === 'مختبر') {
            if ($this->selectedTeacherId) {
                $this->students = Student::query()->with(['user'])->where('group_id', $this->selectedTeacherId)
                    ->orWhere('group_sunnah_id', $this->selectedTeacherId)->get();
            }
        } else if ($this->current_role === 'مشرف') {
            $this->students = Student::query()->with(['user'])->where('group_id', $this->selectedTeacherId)
                ->orWhere('group_sunnah_id', $this->selectedTeacherId)->get();
        } else if ($this->current_role === 'محفظ') {
            $this->students = Student::query()->with(['user'])->where('group_id', $this->selectedTeacherId)
                ->orWhere('group_sunnah_id', $this->selectedTeacherId)->get();
        }
    }

    public function rules()
    {
        return [
            'tester_id' => 'required|numeric',
            'exam_date' => 'required|date|date_format:Y-m-d',
        ];
    }

    public function messages()
    {
        return [
            'tester_id.required' => 'اسم المختبر مطلوب',
            'tester_id.numeric' => 'حقل المختبر يجب أن يكون رقم',
            'exam_date.required' => 'حقل تاريخ الإختبار مطلوب',
            'exam_date.date' => 'حقل تاريخ الإختبار يجب أن يكون تاريخ',
            'exam_date.date_format' => 'حقل تاريخ الإختبار يجب أن يكون من نوع تاريخ',
            'notes.required' => 'حقل الملاحظات مطلوب',
            'notes.string' => 'حقل الملاحظات يجب أن يحتوي على نص',
        ];
    }

    /**
     * @throws \JsonException
     */
    public function examOrderApproval($id)
    {
        $examOrder = ExamOrder::where('id', $id)->first();
        if ($examOrder && $examOrder->status !== ExamOrder::FAILURE_STATUS) {
            $this->validate();
            $messageBag = new MessageBag;
            $messageBag->add('tester_id', 'يجب أن لا يكون المختبر هو نفس المحفظ');
            if ($this->teacher_id === $this->tester_id) {
                $this->setErrorBag($messageBag);
            } else if ($this->current_role === 'مشرف الإختبارات' || $this->current_role === 'أمير المركز') {
                $examOrder->update([
                    'status' => ExamOrder::ACCEPTABLE_STATUS,
                    'tester_id' => $this->tester_id,
                    'datetime' => $this->exam_date . ' ' . date('H:i:s'),
                    'user_signature_id' => auth()->id(),
                    'notes' => null,
                ]);

                // start push notifications to teacher
                $examOrder->teacher->user->notify(new AcceptExamOrderForTeacherNotify($examOrder));
                $title = "طلب اختبار معتمد";
                if ($examOrder->partable_type === 'App\Models\QuranPart') {
                    $message = "لقد قام مشرف الإختبارات باعتماد طلب اختبار الطالب: " . $examOrder->student->user->name . " في الجزء: " . $examOrder->partable->name . ' ' . $examOrder->partable->description . " بتاريخ " . Carbon::parse($examOrder->datetime)->format('d-m-Y');
                } else {
                    $message = "لقد قام مشرف الإختبارات باعتماد طلب اختبار الطالب: " . $examOrder->student->user->name . " في الجزء: " . $examOrder->partable->name . ' (' . $examOrder->partable->total_hadith_parts . ') حديث' . " بتاريخ " . Carbon::parse($examOrder->datetime)->format('d-m-Y');
                }
                $this->push_notification($message, $title, $this->link . $examOrder->id, [$examOrder->teacher->user->user_fcm_token->device_token ?? null]);
                // end push notifications to teacher

                // start push notifications to tester
                $examOrder->tester->user->notify(new AcceptExamOrderForTesterNotify($examOrder));
                if ($examOrder->partable_type === 'App\Models\QuranPart') {
                    $message = "لقد قام مشرف الإختبارات بتعيينك مختبر طلب اختبار الطالب: " . $examOrder->student->user->name . " في الجزء: " . $examOrder->partable->name . ' ' . $examOrder->partable->description . " بتاريخ " . Carbon::parse($examOrder->datetime)->format('d-m-Y');
                } else {
                    $message = "لقد قام مشرف الإختبارات بتعيينك مختبر طلب اختبار الطالب: " . $examOrder->student->user->name . " في الجزء: " . $examOrder->partable->name . ' (' . $examOrder->partable->total_hadith_parts . ') حديث' . " بتاريخ " . Carbon::parse($examOrder->datetime)->format('d-m-Y');
                }

                $this->push_notification($message, $title, $this->link . $examOrder->id, [$examOrder->tester->user->user_fcm_token->device_token ?? null]);
                // end push notifications to tester

                $this->dispatchBrowserEvent('alert',
                    ['type' => 'success', 'message' => 'تمت عملية اعتماد طلب الإختبار بنجاح.']);
                $this->dispatchBrowserEvent('hideDialog');
                $this->clearForm();
            }
        }
    }

    public function clearForm()
    {
        $this->modalId = '';
        $this->tester_id = null;
        $this->teacher_id = null;
        $this->exam_date = null;
        $this->student_name = null;
        $this->quran_part = null;
        $this->number_failure_times = null;
        $this->last_tester_name = null;
        $this->notes = null;
        $this->resetValidation();
    }

    public function examOrderRefusal($id)
    {
        $examOrder = ExamOrder::where('id', $id)->first();
        if ($examOrder) {
            $this->validate(['notes' => 'required|string',]);
            if ($examOrder->status !== ExamOrder::FAILURE_STATUS) {
                if ($this->current_role === 'مشرف الإختبارات' || $this->current_role === 'أمير المركز') {
                    $examOrder->update([
                        'status' => ExamOrder::REJECTED_STATUS,
                        'notes' => $this->notes,
                        'user_signature_id' => auth()->id(),
                        'tester_id' => null,
                        'datetime' => null,
                    ]);

                    Cache::forget('exam_order_id_' . $examOrder->id);

                    // start push notifications to teacher
                    $examOrder->teacher->user->notify(new RejectionExamOrderForTeacherNotify($examOrder));
                    $title = "طلب اختبار مرفوض";
                    if ($examOrder->partable_type === 'App\Models\QuranPart') {
                        $message = "لقد قام مشرف الإختبارات برفض طلب اختبار الطالب: " . $examOrder->student->user->name . " في الجزء: " . $examOrder->partable->name . ' ' . $examOrder->partable->description . " وذلك بسبب: " . $examOrder->notes;
                    } else {
                        $message = "لقد قام مشرف الإختبارات برفض طلب اختبار الطالب: " . $examOrder->student->user->name . " في الجزء: " . $examOrder->partable->name . ' (' . $examOrder->partable->total_hadith_parts . ') حديث' . " وذلك بسبب: " . $examOrder->notes;
                    }
                    $this->push_notification($message, $title, $this->link . $examOrder->id, [$examOrder->teacher->user->user_fcm_token->device_token ?? null]);
                    // end push notifications to teacher

                    $this->dispatchBrowserEvent('hideDialog');
                    $this->dispatchBrowserEvent('alert',
                        ['type' => 'error', 'message' => 'تمت عملية رفض طلب الإختبار بنجاح.']);
                    $this->clearForm();
                }
            }
        }
    }

    public function getExamOrder($id)
    {
        $this->clearForm();
        $examOrder = ExamOrder::with(['student.user:id,name', 'partable'])->where('id', $id)->first();
        if ($examOrder) {
            $this->modalId = $examOrder->id;
            $this->student_name = $examOrder->student->user->name;
            if ($examOrder->partable_type === QuranPart::class) {
                $this->quran_part = $examOrder->partable->name . ' ' . $examOrder->partable->description;
                $status_exam_order = DB::table('exams')->select(DB::raw('last_exam_tes_users.name last_tester_name'), DB::raw("(SELECT count(id) FROM exams
                  WHERE student_id = $examOrder->student_id and quran_part_id = $examOrder->partable_id order by datetime) as number_failure_times"))
                    ->leftJoin('exams as count_exams', function ($join) use ($examOrder) {
                        $join->on('count_exams.student_id', '=', DB::raw($examOrder->student_id))
                            ->on('count_exams.id', '=', DB::raw("(SELECT id FROM exams
                  WHERE student_id = $examOrder->student_id and quran_part_id = $examOrder->partable_id order by datetime desc LIMIT 1)"));
                    })
                    ->leftJoin('users as last_exam_tes_users', 'count_exams.tester_id', '=', 'last_exam_tes_users.id')
                    ->first();
            } else {
                $this->quran_part = $examOrder->partable->name . ' (' . $examOrder->partable->total_hadith_parts . ') حديث';
                $status_exam_order = DB::table('sunnah_exams')->select(DB::raw('last_exam_tes_users.name last_tester_name'), DB::raw("(SELECT count(id) FROM sunnah_exams
                  WHERE student_id = $examOrder->student_id and sunnah_part_id = $examOrder->partable_id order by datetime) as number_failure_times"))
                    ->leftJoin('sunnah_exams as count_exams', function ($join) use ($examOrder) {
                        $join->on('count_exams.student_id', '=', DB::raw($examOrder->student_id))
                            ->on('count_exams.id', '=', DB::raw("(SELECT id FROM sunnah_exams
                  WHERE student_id = $examOrder->student_id and sunnah_part_id = $examOrder->partable_id order by datetime desc LIMIT 1)"));
                    })
                    ->leftJoin('users as last_exam_tes_users', 'count_exams.tester_id', '=', 'last_exam_tes_users.id')
                    ->first();
            }
            if ($status_exam_order != null) {
                $this->number_failure_times = $status_exam_order->number_failure_times > 0 ? $status_exam_order->number_failure_times : 'لم يختبر';
            } else {
                $this->number_failure_times = 0;
            }
            $this->last_tester_name = $status_exam_order->last_tester_name ?? 'لم يختبر';
            $this->teacher_id = $examOrder->teacher_id;
            $this->tester_id = $examOrder->tester_id;
            $this->exam_date = Carbon::parse($examOrder->datetime)->format('Y-m-d');
        }
    }

    public function lunchModalDelete($id)
    {
        $this->modalId = $id;
        $this->dispatchBrowserEvent('showModalDelete');
    }

    public function destroy()
    {
        $exam_order = ExamOrder::whereId($this->modalId)->first();
        if ($exam_order->status === ExamOrder::IN_PENDING_STATUS ||
            $exam_order->status === ExamOrder::REJECTED_STATUS ||
            $exam_order->status === ExamOrder::FAILURE_STATUS) {
            $exam_order->delete();
            Cache::forget('exam_order_id_' . $exam_order->id);
            $this->dispatchBrowserEvent('hideDialog');
            $this->dispatchBrowserEvent('alert',
                ['type' => 'error', 'message' => 'تم حذف طلب الإختبار بنجاح.']);
        }
    }
}
