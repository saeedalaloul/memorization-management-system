<?php

namespace App\Http\Livewire;

use App\Exports\ExamsExport;
use App\Exports\ExternalExamsExport;
use App\Imports\ExternalExamsImport;
use App\Models\Exam;
use App\Models\ExamOrder;
use App\Models\ExamSuccessMark;
use App\Models\ExternalExam;
use App\Models\Grade;
use App\Models\Group;
use App\Models\QuranPart;
use App\Models\Student;
use App\Models\Supervisor;
use App\Models\Teacher;
use App\Models\User;
use App\Notifications\ImproveExamOrderForExamsSupervisorNotify;
use App\Notifications\NewExamForTeacherNotify;
use App\Traits\NotificationTrait;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\MessageBag;
use Maatwebsite\Excel\Excel;
use Spatie\Permission\Models\Role;

class Exams extends HomeComponent
{
    use NotificationTrait;

    public $grades = [], $groups = [], $students = [], $quran_parts = [], $exam_success_marks = [], $student_id;
    public $quran_part_id, $exam_success_mark_id, $tester_id, $exam_date, $exam_mark, $quran_part, $student_name;
    public $selectedGradeId, $selectedTeacherId, $selectedStudentId, $selectedExternalExams, $searchDateFrom, $searchDateTo;
    public $file;

    public function render()
    {
        return view('livewire.exams', [
            'exams' => $this->all_Exams(),]);
    }

    protected $listeners = [
        'getTeachersByGradeId' => 'getTeachersByGradeId',
        'getStudentsByTeacherId' => 'getStudentsByTeacherId',
    ];

    public function mount()
    {
        $this->current_role = auth()->user()->current_role;
        $this->all_Grades();
        $this->all_Testers();
        $this->all_ExamSuccessMarks();
        $this->searchDateFrom = date('Y-m-01');
        $this->searchDateTo = date('Y-m-d');
    }

    public function rules()
    {
        return [
            'selectedGradeId' => 'required|string',
            'selectedTeacherId' => 'required|string',
            'student_id' => 'required|numeric',
            'tester_id' => 'required|numeric',
            'quran_part_id' => 'required|numeric',
            'exam_mark' => 'required|numeric|between:60,100',
            'exam_success_mark_id' => 'required|numeric',
            'exam_date' => 'required||date|date_format:Y-m-d',
        ];
    }

    public function messages()
    {
        return [
            'selectedGradeId.required' => 'حقل المرحلة مطلوب',
            'selectedGradeId.numeric' => 'يجب اختيار صالح لحقل المرحلة',
            'selectedTeacherId.required' => 'حقل المجموعة مطلوب',
            'selectedTeacherId.numeric' => 'يجب اختيار صالح لحقل المجموعة',
            'student_id.required' => 'حقل الطالب مطلوب',
            'student_id.numeric' => 'يجب اختيار صالح لحقل الطالب',
            'tester_id.required' => 'حقل المختبر مطلوب',
            'tester_id.numeric' => 'يجب اختيار صالح لحقل المختبر',
            'quran_part_id.required' => 'حقل جزء الإختبار مطلوب',
            'quran_part_id.numeric' => 'يجب اختيار صالح لحقل الجزء',
            'exam_success_mark_id.required' => 'حقل نسبة النجاح في الاختبار مطلوب',
            'exam_success_mark_id.numeric' => 'يجب اختيار صالح لحقل نسبة النجاح في الإختبار',
            'exam_mark.required' => 'علامة الاختبار مطلوبة',
            'exam_mark.numeric' => 'يجب أن يكون رقم',
            'exam_mark.between' => 'يجب أن تكون علامة الاختبار بين 60 أو 100',
            'exam_date.required' => 'حقل تاريخ الإختبار مطلوب',
            'exam_date.date' => 'حقل تاريخ الإختبار يجب أن يكون تاريخ',
            'exam_date.date_format' => 'حقل تاريخ الإختبار يجب أن يكون من نوع تاريخ',
        ];
    }

    public function show_dialog_assign_external_exam($id)
    {
        $exam = Exam::where('id', $id)->first();
        if ($exam) {
            $this->modalId = $id;
            $this->student_name = $exam->student->user->name;
            $this->quran_part = $exam->quranPart->name;
            $this->dispatchBrowserEvent('showModal');
        }
    }

    public function assign_external_exam_mark()
    {
        $this->validate(
            ['exam_mark' => 'required|numeric|between:60,100',
                'exam_date' => 'required||date|date_format:Y-m-d',]
        );
        ExternalExam::create(
            [
                'id' => $this->modalId,
                'mark' => $this->exam_mark,
                'date' => $this->exam_date,
            ]
        );
        $this->dispatchBrowserEvent('alert',
            ['type' => 'success', 'message' => 'تمت عملية رصد درجة للإختبار الخارجي بنجاح.']);
        $this->dispatchBrowserEvent('hideModal');
        $this->clearForm();
    }

    public function all_Exams()
    {
        return Exam::query()
            ->with(['student.user:id,name', 'QuranPart:id,name,description', 'examSuccessMark', 'external_exam', 'exam_improvement', 'teacher.user:id,name', 'tester.user:id,name'])
            ->search($this->search)
            ->when(!empty($this->selectedExternalExams), function ($q, $v) {
                $q->whereHas('examSuccessMark', function ($q) {
                    $q->where(DB::raw('exams.mark'), '>=', DB::raw('exam_success_mark.mark'));
                })->when($this->selectedExternalExams == 1, function ($q, $v) {
                    $q->whereHas('external_exam');
                })->when($this->selectedExternalExams == 2, function ($q, $v) {
                    $q->whereDoesntHave('external_exam');
                });
            })
            ->whereBetween(DB::raw('DATE(datetime)'), [$this->searchDateFrom, $this->searchDateTo])
            ->when($this->current_role == 'محفظ', function ($q, $v) {
                $q->whereHas('student', function ($q) {
                    $q->where('group_id', '=', Group::where('teacher_id', auth()->id())->first()->id ?? null)
                        ->when(!empty($this->selectedStudentId), function ($q, $v) {
                            $q->where('id', '=', $this->selectedStudentId);
                        });
                });
            })
            ->when($this->current_role == 'مختبر', function ($q, $v) {
                $q->where('tester_id', auth()->id());
            })
            ->when($this->current_role == 'مشرف', function ($q, $v) {
                $q->whereHas('student', function ($q) {
                    return $q
                        ->where('grade_id', '=', Supervisor::find(auth()->id())->grade_id)
                        ->when(!empty($this->selectedTeacherId), function ($q, $v) {
                            $q->where('group_id', '=', $this->selectedTeacherId);
                        })
                        ->when(!empty($this->selectedStudentId), function ($q, $v) {
                            $q->where('id', '=', $this->selectedStudentId);
                        });
                });
            })
            ->when($this->current_role == 'أمير المركز' || $this->current_role == 'مشرف الإختبارات', function ($q, $v) {
                $q->whereHas('student', function ($q) {
                    return $q
                        ->when(!empty($this->selectedGradeId), function ($q, $v) {
                            $q->where('grade_id', '=', $this->selectedGradeId);
                        })
                        ->when(!empty($this->selectedTeacherId), function ($q, $v) {
                            $q->where('group_id', '=', $this->selectedTeacherId);
                        })
                        ->when(!empty($this->selectedStudentId), function ($q, $v) {
                            $q->where('id', '=', $this->selectedStudentId);
                        });
                });
            })
            ->when(!empty(strval(\Request::segment(2)) && strval(\Request::segment(2)) != 'message'), function ($q, $v) {
                $q->where('id', \Request::segment(2));
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function submitSearch()
    {
        $this->all_Exams();
    }

    public function all_External_Exams()
    {
        return Exam::query()
            ->with(['student.user:id,name', 'QuranPart:id,name,description', 'examSuccessMark', 'external_exam', 'exam_improvement', 'teacher.user:id,name', 'tester.user:id,name'])
            ->whereHas('examSuccessMark', function ($q) {
                $q->where(DB::raw('exams.mark'), '>=', DB::raw('exam_success_mark.mark'));
            })
            ->whereDoesntHave('external_exam')
            ->get();
    }


    public function all_Grades()
    {
        if ($this->current_role == 'مشرف') {
            $this->grades = Grade::query()->where('id', Supervisor::where('id', auth()->id())->first()->grade_id)->get();
        } else if ($this->current_role == 'محفظ') {
            $this->grades = Grade::query()->where('id', Teacher::where('id', auth()->id())->first()->grade_id)->get();
        } else if ($this->current_role == 'أمير المركز' || $this->current_role == 'مشرف الإختبارات') {
            $this->grades = Grade::all();
        }
    }

    public function getTeachersByGradeId()
    {
        $this->reset('students', 'groups', 'selectedTeacherId', 'selectedStudentId', 'quran_parts', 'student_id', 'quran_part_id', 'tester_id', 'exam_mark', 'exam_success_mark_id', 'exam_date');

        if ($this->current_role == 'أمير المركز' || $this->current_role == 'مشرف الإختبارات') {
            if ($this->selectedGradeId) {
                $this->groups = Group::query()->with(['teacher.user'])->where('grade_id', $this->selectedGradeId)->get();
            }
        } else if ($this->current_role == 'مشرف') {
            if ($this->selectedGradeId) {
                $this->groups = Group::query()->with(['teacher.user'])->where('grade_id', $this->selectedGradeId)->get();
            }
        } else if ($this->current_role == 'محفظ') {
            if ($this->selectedGradeId) {
                $this->groups = Group::query()->where('teacher_id', auth()->id())->get();
            }
        }

    }

    public function getStudentsByTeacherId()
    {
        $this->reset('students', 'selectedStudentId', 'quran_parts', 'student_id', 'quran_part_id');

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

    public function all_ExamSuccessMarks()
    {
        if ($this->current_role == 'أمير المركز' || $this->current_role == 'مشرف الإختبارات') {
            $this->exam_success_marks = ExamSuccessMark::get();
        }
    }

    public function updatedStudentId($id)
    {
        $exam = Exam::with(['quranPart'])->where('student_id', $id)->orderBy('created_at', 'desc')->first();
        if ($exam) {
            $part_to = $exam->quranPart->arrangement;
            if ($exam->mark >= $exam->examSuccessMark->mark) {
                $part_to = $exam->quranPart->arrangement + 1;
            }
            $this->quran_parts = QuranPart::query()->whereBetween('arrangement', [1, $part_to])->whereDoesntHave('exams', function ($query) use ($id) {
                $query->where('student_id', $id);
            })->orderBy('arrangement')->get();
        } else {
            $this->quran_parts = QuranPart::query()->orderBy('arrangement')->get();
        }
    }

    public function examInformationApproval()
    {
        $this->validate();
        DB::beginTransaction();
        try {
            $teacher_id = $this->groups->firstWhere('id', $this->selectedTeacherId)->teacher_id;
            if ($teacher_id != $this->tester_id) {
                $exam = Exam::create([
                    'mark' => $this->exam_mark,
                    'quran_part_id' => $this->quran_part_id,
                    'student_id' => $this->student_id,
                    'teacher_id' => $teacher_id,
                    'tester_id' => $this->tester_id,
                    'exam_success_mark_id' => $this->exam_success_mark_id,
                    'datetime' => $this->exam_date . ' ' . date('H:i:s', time()),
                    'notes' => null,
                ]);

                $exam->teacher->user->notify(new NewExamForTeacherNotify($exam));
                $title = "اختبار جديد معتمد";
                $message = "لقد تم اعتماد درجة: " . $exam->mark . "%" . " في الجزء: " . $exam->quranPart->name . ' ' . $exam->quranPart->description . " للطالب: " . $exam->student->user->name;
                $this->push_notification($message, $title, [$exam->teacher->user->user_fcm_token->device_token]);


                $this->dispatchBrowserEvent('alert',
                    ['type' => 'success', 'message' => 'تمت عملية اعتماد اختبار الطالب بنجاح.']);
                DB::commit();
                $this->clearForm();
            } else {
                $messageBag = new MessageBag();
                $messageBag->add('tester_id', 'عذرا, لا يمكن أن بكون المختبر هو نفس المحفظ!');
                $this->setErrorBag($messageBag);
            }
        } catch (Exception $e) {
            DB::rollback();
            $this->catchError = $e->getMessage();
        }
    }

    public function submitExamImprovementRequest($student_id, $quran_part_id)
    {
        if ($this->current_role == 'محفظ') {
            if ($student_id != null && !empty($student_id) &&
                $quran_part_id != null && !empty($quran_part_id)) {
                $exam_order = ExamOrder::where('student_id', $student_id)->first();
                if (!$exam_order) {
                    $exam_order = ExamOrder::create([
                        'type' => ExamOrder::IMPROVEMENT_TYPE,
                        'status' => ExamOrder::IN_PENDING_STATUS,
                        'quran_part_id' => $quran_part_id,
                        'student_id' => $student_id,
                        'teacher_id' => auth()->id(),
                        'user_signature_id' => auth()->id(),
                    ]);

                    // start push notifications to exams supervisor
                    $role = Role::where('name', User::EXAMS_SUPERVISOR_ROLE)->first();
                    $role_users = $role->users();
                    if ($role_users->first()) {
                        $role_users->first()->notify(new ImproveExamOrderForExamsSupervisorNotify($exam_order));
                        $title = "طلب تحسبن درجة اختبار";
                        $message = "لقد قام المحفظ: " . $exam_order->teacher->user->name . " بطلب تحسين درجة اختبار للطالب " . $exam_order->student->user->name . " في الجزء: " . $exam_order->quranPart->name . ' ' . $exam_order->quranPart->description;
                        $this->push_notification($message, $title, [$role_users->first()->user_fcm_token->device_token]);
                    }
                    // end push notifications to exams supervisor

                    $this->dispatchBrowserEvent('alert',
                        ['type' => 'success', 'message' => 'تمت عملية طلب تحسين درجة الإختبار بنجاح.']);
                } else {
                    $this->dispatchBrowserEvent('alert',
                        ['type' => 'error', 'message' => 'عذرا يوجد طلب تحسين درجة مسبق لهذا الاختبار.']);
                }
            }
        }
    }

    public function import()
    {
        $this->validate(
            ['file' => 'required|mimes:xlsx,xls']
        );
        \Maatwebsite\Excel\Facades\Excel::import(new ExternalExamsImport(), $this->file);
    }


    public function clearForm()
    {
        $this->groups = [];
        $this->students = [];
        $this->quran_parts = [];
        $this->exam_success_mark_id = null;
        $this->selectedGradeId = null;
        $this->selectedTeacherId = null;
        $this->quran_part_id = null;
        $this->tester_id = null;
        $this->catchError = '';
        $this->exam_date = null;
        $this->exam_mark = null;
    }

    public function export()
    {
        return (new ExamsExport($this->all_Exams()))->download('Exams Report.xlsx', Excel::XLSX);
    }

    public function export_external_exams()
    {
        return (new ExternalExamsExport($this->all_External_Exams()))->download('External Exams Report.xlsx', Excel::XLSX);
    }
}
