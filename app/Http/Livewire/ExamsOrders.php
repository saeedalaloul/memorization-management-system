<?php

namespace App\Http\Livewire;

use App\Models\ExamOrder;
use App\Models\Grade;
use App\Models\Group;
use App\Models\Student;
use App\Models\Supervisor;
use App\Models\Teacher;
use App\Notifications\AcceptExamOrderForTeacherNotify;
use App\Notifications\AcceptExamOrderForTesterNotify;
use App\Notifications\RejectionExamOrderForTeacherNotify;
use App\Traits\NotificationTrait;
use Carbon\Carbon;
use Illuminate\Support\MessageBag;

class ExamsOrders extends HomeComponent
{
    use NotificationTrait;

    public $grades = [], $groups = [], $students = [];
    public $selectedGradeId, $selectedTeacherId, $selectedStudentId, $selectedStatus;
    public $student_name, $quran_part;
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
        $this->all_Grades();
        $this->all_Testers();
    }

    public function all_Exam_Orders()
    {
        return ExamOrder::query()
            ->with(['student.user', 'QuranPart', 'teacher.user'])
            ->search($this->search)
            ->when($this->current_role == 'محفظ', function ($q, $v) {
                $q->when(!empty($this->selectedStudentId), function ($q, $v) {
                    $q->whereHas('student', function ($q) {
                        $q->where('id', '=', $this->selectedStudentId);
                    });
                })->whereHas('student', function ($q) {
                    $q->where('group_id', '=', $this->selectedTeacherId);
                });
            })
            ->when($this->current_role == 'مختبر', function ($q, $v) {
                $q->where('tester_id', auth()->id());
            })
            ->when($this->current_role == 'مشرف', function ($q, $v) {
                $q->whereHas('student', function ($q) {
                    $q->where('grade_id', '=', $this->selectedGradeId);
                })->when(!empty($this->selectedTeacherId), function ($q, $v) {
                    $q->where('group_id', '=', $this->selectedTeacherId);
                })->when(!empty($this->selectedStudentId), function ($q, $v) {
                    $q->where('id', '=', $this->selectedStudentId);
                });
            })
            ->when($this->current_role == 'أمير المركز' || $this->current_role == 'مشرف الإختبارات', function ($q, $v) {
                $q->when(!empty($this->selectedGradeId), function ($q, $v) {
                    $q->whereHas('student', function ($q) {
                        $q->where('grade_id', '=', $this->selectedGradeId);
                    });
                })->when(!empty($this->selectedTeacherId), function ($q, $v) {
                    $q->whereHas('student', function ($q) {
                        $q->where('group_id', '=', $this->selectedTeacherId);
                    });
                })->when(!empty($this->selectedStudentId), function ($q, $v) {
                    $q->whereHas('student', function ($q) {
                        $q->where('id', '=', $this->selectedStudentId);
                    });
                });
            })->when(!empty($this->selectedStatus), function ($q, $v) {
                $q->where('status', '=', $this->selectedStatus);
            })
            ->when(!empty(strval(\Request::segment(2)) && strval(\Request::segment(2)) != 'message'), function ($q, $v) {
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
        if ($this->current_role == 'مشرف') {
            $this->selectedGradeId = Supervisor::where('id', auth()->id())->first()->grade_id;
            $this->grades = Grade::query()->where('id', $this->selectedGradeId)->get();
        } else if ($this->current_role == 'محفظ') {
            $this->selectedTeacherId = Group::where('teacher_id', auth()->id())->first()->id ?? null;
            $this->grades = Grade::query()->where('id', Teacher::where('id', auth()->id())->first()->grade_id)->get();
        } else if ($this->current_role == 'أمير المركز' || $this->current_role == 'مشرف الإختبارات') {
            $this->grades = Grade::all();
        }
    }

    public function getTeachersByGradeId()
    {
        $this->reset('students', 'groups', 'selectedTeacherId', 'selectedStudentId');

        if ($this->current_role == 'مشرف') {
            if ($this->selectedGradeId) {
                $this->groups = Group::query()->with(['teacher.user'])->where('grade_id', $this->selectedGradeId)->get();
            }
        } else if ($this->current_role == 'أمير المركز' || $this->current_role == 'مشرف الإختبارات') {
            if ($this->selectedGradeId) {
                $this->groups = Group::query()->with(['teacher.user'])->where('grade_id', $this->selectedGradeId)->get();
            }
        } elseif ($this->current_role == 'محفظ') {
            $this->groups = Group::query()->where('teacher_id', auth()->id())->get();
        }
    }

    public function getStudentsByTeacherId()
    {
        $this->reset('students', 'selectedStudentId');

        if ($this->current_role == 'أمير المركز' || $this->current_role == 'مشرف الإختبارات') {
            if ($this->selectedTeacherId) {
                $this->students = Student::query()->with(['user'])->where('group_id', $this->selectedTeacherId)->get();
            }
        } else if ($this->current_role == 'مشرف') {
            $this->students = Student::query()->with(['user'])->where('group_id', $this->selectedTeacherId)->get();
        } else if ($this->current_role == 'محفظ') {
            $this->students = Student::query()->with(['user'])->where('group_id', $this->selectedTeacherId)->get();
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

    public function examOrderApproval($id)
    {
        $examOrder = ExamOrder::where('id', $id)->first();
        if ($examOrder) {
            if ($examOrder->status != ExamOrder::FAILURE_STATUS) {
                $this->validate();
                $messageBag = new MessageBag;
                $messageBag->add('tester_id', 'يجب أن لا يكون المختبر هو نفس المحفظ');
                if ($this->teacher_id == $this->tester_id) {
                    $this->setErrorBag($messageBag);
                } else {
                    if ($this->current_role == 'مشرف الإختبارات' || $this->current_role == 'أمير المركز') {
                        $examOrder->update([
                            'status' => ExamOrder::ACCEPTABLE_STATUS,
                            'tester_id' => $this->tester_id,
                            'datetime' => $this->exam_date . ' ' . date('H:i:s', time()),
                            'user_signature_id' => auth()->id(),
                            'notes' => null,
                        ]);

                        // start push notifications to teacher
                        $examOrder->teacher->user->notify(new AcceptExamOrderForTeacherNotify($examOrder));
                        $title = "طلب اختبار معتمد";
                        $message = "لقد قام مشرف الإختبارات باعتماد طلب اختبار الطالب: " . $examOrder->student->user->name . " في الجزء: " . $examOrder->quranPart->name . ' ' . $examOrder->quranPart->description . " بتاريخ " . Carbon::parse($examOrder->datetime)->format('d-m-Y');
                        $this->push_notification($message, $title, [$examOrder->teacher->user->user_fcm_token->device_token ?? null]);
                        // end push notifications to teacher

                        // start push notifications to tester
                        $examOrder->tester->user->notify(new AcceptExamOrderForTesterNotify($examOrder));
                        $message = "لقد قام مشرف الإختبارات بتعيينك مختبر طلب اختبار الطالب: " . $examOrder->student->user->name . " في الجزء: " . $examOrder->quranPart->name . ' ' . $examOrder->quranPart->description . " بتاريخ " . Carbon::parse($examOrder->datetime)->format('d-m-Y');
                        $this->push_notification($message, $title, [$examOrder->tester->user->user_fcm_token->device_token ?? null]);
                        // end push notifications to tester

                        $this->dispatchBrowserEvent('alert',
                            ['type' => 'success', 'message' => 'تمت عملية اعتماد طلب الإختبار بنجاح.']);
                        $this->dispatchBrowserEvent('hideDialog');
                        $this->clearForm();
                    }
                }
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
        $this->notes = null;
        $this->resetValidation();
    }

    public function examOrderRefusal($id)
    {
        $examOrder = ExamOrder::where('id', $id)->first();
        if ($examOrder) {
            $this->validate(['notes' => 'required|string',]);
            if ($examOrder->status != ExamOrder::FAILURE_STATUS) {
                if ($this->current_role == 'مشرف الإختبارات' || $this->current_role == 'أمير المركز') {
                    $examOrder->update([
                        'status' => ExamOrder::REJECTED_STATUS,
                        'notes' => $this->notes,
                        'user_signature_id' => auth()->id(),
                        'tester_id' => null,
                        'datetime' => null,
                    ]);

                    // start push notifications to teacher
                    $examOrder->teacher->user->notify(new RejectionExamOrderForTeacherNotify($examOrder));
                    $title = "طلب اختبار مرفوض";
                    $message = "لقد قام مشرف الإختبارات برفض طلب اختبار الطالب: " . $examOrder->student->user->name . " في الجزء: " . $examOrder->quranPart->name . ' ' . $examOrder->quranPart->description . " وذلك بسبب: " . $examOrder->notes;
                    $this->push_notification($message, $title, [$examOrder->teacher->user->user_fcm_token->device_token]);
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
        $examOrder = ExamOrder::with(['student.user', 'quranPart'])->where('id', $id)->first();
        if ($examOrder) {
            $this->modalId = $examOrder->id;
            $this->student_name = $examOrder->student->user->name;
            $this->quran_part = $examOrder->quranPart->name . ' ' . $examOrder->quranPart->description;
            $this->teacher_id = $examOrder->teacher_id;
            $this->tester_id = $examOrder->tester_id;
            $this->exam_date = Carbon::parse($examOrder->datetime)->format('Y-m-d');
        }
    }

    public function destroy($id)
    {
        $exam_order = ExamOrder::find($id);
        if ($exam_order->status == ExamOrder::IN_PENDING_STATUS ||
            $exam_order->status == ExamOrder::REJECTED_STATUS ||
            $exam_order->status == ExamOrder::FAILURE_STATUS) {
            $exam_order->delete();
            $this->dispatchBrowserEvent('hideDialog');
            $this->dispatchBrowserEvent('alert',
                ['type' => 'error', 'message' => 'تم حذف طلب الإختبار بنجاح.']);
        }
    }
}
