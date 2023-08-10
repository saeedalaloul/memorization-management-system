<?php

namespace App\Http\Livewire;

use App\Exports\SunnahExamsExport;
use App\Exports\SunnahExternalExamsExport;
use App\Imports\ExternalExamsImport;
use App\Models\ExamOrder;
use App\Models\ExamSuccessMark;
use App\Models\Grade;
use App\Models\Group;
use App\Models\Student;
use App\Models\SunnahExam;
use App\Models\SunnahExternalExam;
use App\Models\SunnahImprovementExam;
use App\Models\SunnahPart;
use App\Models\Supervisor;
use App\Models\Teacher;
use App\Models\User;
use App\Notifications\ImproveExamOrderForExamsSupervisorNotify;
use App\Traits\NotificationTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\MessageBag;
use Maatwebsite\Excel\Excel;
use Spatie\Permission\Models\Role;

class SunnahExams extends HomeComponent
{
    use NotificationTrait;

    public $grades = [], $groups = [], $students = [], $exam_success_marks = [];
    public $ret_exam, $exam_date, $exam_mark, $sunnah_part, $student_name, $teacher_name, $teacher_id,
        $tester_id, $exam_success_mark_id;
    public $selectedGradeId, $selectedTeacherId, $selectedStudentId, $selectedExternalExams,$selectedTypeExams,
        $selectedMarkExams, $searchDateFrom, $searchDateTo;
    public $file;

    public function render()
    {
        return view('livewire.sunnah-exams', [
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
        $this->searchDateFrom = date('Y-m-01');
        $this->searchDateTo = date('Y-m-d');
        $this->sortBy = 'datetime';
        $this->all_Testers();
        $this->all_ExamSuccessMarks();
    }

    public function manage_exam($id)
    {
        $this->clearForm();
        $exam = SunnahExam::with(['student.user:id,name', 'sunnahPart:id,name,total_hadith_parts', 'exam_success_mark:id,mark', 'exam_improvement:id'])
            ->where('id', $id)->first();
        if ($exam) {
            $this->ret_exam = $exam;
            $this->modalId = $id;
            $this->student_name = $exam->student->user->name;
            $this->sunnah_part = $exam->sunnahPart->name . ' (' . $exam->sunnahPart->total_hadith_parts . ') حديث';
            $this->teacher_name = $exam->teacher->user->name;
            $this->teacher_id = $exam->teacher_id;
            $this->tester_id = $exam->tester_id;
            $this->exam_date = Carbon::parse($exam->datetime)->format('Y-m-d');
            $this->exam_success_mark_id = $exam->exam_success_mark_id;
            $this->exam_mark = $exam->mark;
            $this->dispatchBrowserEvent('showModalManageExam');
        }
    }

    public function update_exam()
    {
        $this->validate([
            'tester_id' => 'required|numeric',
            'exam_success_mark_id' => 'required|numeric',
            'exam_date' => 'required||date|date_format:Y-m-d',
            'exam_mark' => 'required|numeric|between:60,100',
        ]);

        $isCompleteUpdate = true;
        $messageBag = new MessageBag();

        if ($this->ret_exam->exam_success_mark_id !== $this->exam_success_mark_id) {
            $exam = SunnahExam::query()->where('student_id', '=', $this->ret_exam->student_id)
                ->whereRelation('exam_success_mark', DB::raw('sunnah_exams.mark'), '>=', DB::raw('exam_success_mark.mark'))
                ->where('sunnah_part_id', '=', $this->ret_exam->sunnah_part_id)
                ->where('id', '!=', $this->ret_exam->id)
                ->first();

            if ($exam !== null) {
                $messageBag->add('exam_success_mark_id', 'عذرا لا يمكن قبول نسبة النجاح في الإختبار بسبب وجود اختبار مجاز به الطالب لنفس الجزء');
                $this->setErrorBag($messageBag);
                $isCompleteUpdate = false;
            } else if (($this->exam_mark >= $this->ret_exam->exam_success_mark->mark) && $this->exam_mark < ExamSuccessMark::query()->where('id', $this->exam_success_mark_id)->first()->mark) {
                $messageBag->add('exam_success_mark_id', 'عذرا لا يمكن قبول رسوب الطالب في الإختبار لأن الطالب مجاز من خلال نسبة النجاح الحالية');
                $this->setErrorBag($messageBag);
                $isCompleteUpdate = false;
            }
        }

        if ($this->ret_exam->mark !== $this->exam_mark) {
            if ($this->ret_exam->exam_improvement !== null) {
                $messageBag->add('exam_mark', 'عذرا لا يمكن قبول علامة اختبار الطالب بسبب وجود اختبار تحسين لهذا الإختبار');
                $this->setErrorBag($messageBag);
                $isCompleteUpdate = false;
            } elseif ($this->exam_mark < $this->ret_exam->exam_success_mark->mark) {
                $messageBag->add('exam_mark', 'عذرا لا يمكن قبول علامة الطالب لأنه مجاز من قبل');
                $this->setErrorBag($messageBag);
                $isCompleteUpdate = false;
            } else {
                $exam = SunnahExam::query()->where('student_id', '=', $this->ret_exam->student_id)
                    ->whereRelation('exam_success_mark', DB::raw('sunnah_exams.mark'), '>=', DB::raw('exam_success_mark.mark'))
                    ->where('sunnah_part_id', '=', $this->ret_exam->sunnah_part_id)
                    ->where('id', '!=', $this->ret_exam->id)
                    ->first();

                if ($exam !== null) {
                    $messageBag->add('exam_mark', 'عذرا لا يمكن قبول علامة اختبار الطالب بسبب وجود اختبار مجاز به');
                    $this->setErrorBag($messageBag);
                    $isCompleteUpdate = false;
                }
            }
        }

        if ($isCompleteUpdate) {
            $this->ret_exam->update([
                'tester_id' => $this->tester_id,
                'exam_success_mark_id' => $this->exam_success_mark_id,
                'mark' => $this->exam_mark,
                'datetime' => $this->exam_date !== Carbon::parse($this->ret_exam->datetime)->format('Y-m-d')
                    ? $this->exam_date . ' ' . date('H:i:s')
                    : $this->ret_exam->datetime,
            ]);

            $this->dispatchBrowserEvent('hideModal');
            $this->dispatchBrowserEvent('alert',
                ['type' => 'success', 'message' => 'تمت عملية تحديث اختبار الطالب بنجاح.']);
            $this->clearForm();
        }
    }

    public function delete_exam()
    {
        if ($this->modalId !== '') {
            SunnahExternalExam::destroy($this->modalId);
            SunnahImprovementExam::destroy($this->modalId);
            SunnahExam::destroy($this->modalId);
            $this->dispatchBrowserEvent('alert',
                ['type' => 'success', 'message' => 'تمت عملية حذف الإختبار بنجاح.']);
            $this->dispatchBrowserEvent('hideModal');
            $this->clearForm();
        }
    }


    public function manage_external_exam($id, $type)
    {
        $this->clearForm();
        $this->process_type = $type;
        $exam = SunnahExam::with(['student.user:id,name', 'sunnahPart:id,name,total_hadith_parts', 'external_exam'])
            ->where('id', $id)->first();
        if ($exam) {
            $this->modalId = $id;
            $this->student_name = $exam->student->user->name;
            $this->sunnah_part = $exam->sunnahPart->name . ' (' . $exam->sunnahPart->total_hadith_parts . ') حديث';

            if ($this->process_type === 'manage_exam') {
                $this->exam_mark = $exam->external_exam->mark;
                $this->exam_date = $exam->external_exam->date;
            }
            $this->dispatchBrowserEvent('showModalManageExternalExam');
        }
    }

    public function update_or_assign_external_exam_mark()
    {
        $this->validate(
            ['exam_mark' => 'required|numeric|between:60,100',
                'exam_date' => 'required||date|date_format:Y-m-d',]
        );

        SunnahExternalExam::updateOrCreate(['id' => $this->modalId ?? null], [
                'mark' => $this->exam_mark,
                'date' => $this->exam_date,
            ]
        );

        if ($this->process_type === 'manage_exam') {
            $this->dispatchBrowserEvent('alert',
                ['type' => 'success', 'message' => 'تمت عملية تحديث درجة الإختبار الخارجي بنجاح.']);
        } else {
            $this->dispatchBrowserEvent('alert',
                ['type' => 'success', 'message' => 'تمت عملية رصد درجة للإختبار الخارجي بنجاح.']);
        }

        $this->dispatchBrowserEvent('hideModal');
        $this->clearForm();
    }


    public function delete_external_exam()
    {
        if ($this->modalId !== '') {
            SunnahExternalExam::destroy($this->modalId);
            $this->dispatchBrowserEvent('alert',
                ['type' => 'success', 'message' => 'تمت عملية حذف الإختبار الخارجي بنجاح.']);
            $this->dispatchBrowserEvent('hideModal');
            $this->clearForm();
        }
    }


    public function show_dialog_assign_external_exam($id)
    {
        $exam = SunnahExam::where('id', $id)->first();
        if ($exam) {
            $this->modalId = $id;
            $this->student_name = $exam->student->user->name;
            $this->sunnah_part = $exam->sunnahPart->name . ' (' . $exam->sunnahPart->total_hadith_parts . ') حديث';
            $this->dispatchBrowserEvent('showModal');
        }
    }

    public function assign_external_exam_mark()
    {
        $this->validate(
            ['exam_mark' => 'required|numeric|between:60,100',
                'exam_date' => 'required||date|date_format:Y-m-d',]
        );

        SunnahExternalExam::create(
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
        return SunnahExam::query()
            ->with(['student.user:id,name', 'SunnahPart:id,name,total_hadith_parts', 'exam_success_mark', 'external_exam', 'exam_improvement', 'teacher.user:id,name', 'tester.user:id,name'])
            ->search($this->search)
            ->when($this->selectedExternalExams != null, function ($q, $v) {
                $q->when((int)$this->selectedExternalExams == 1, function ($q, $v) {
                    $q->whereHas('external_exam');
                })->when((int)$this->selectedExternalExams == 2, function ($q, $v) {
                    $q->whereDoesntHave('external_exam');
                });
            })
            ->when($this->selectedMarkExams != null, function ($q, $v) {
                $q->when((int)$this->selectedMarkExams == 1, function ($q, $v) {
                    $q->whereHas('exam_success_mark', function ($q) {
                        $q->where(DB::raw('sunnah_exams.mark'), '>=', DB::raw('exam_success_mark.mark'));
                    });
                })->when((int)$this->selectedMarkExams == 2, function ($q, $v) {
                    $q->whereHas('exam_success_mark', function ($q) {
                        $q->where(DB::raw('sunnah_exams.mark'), '<', DB::raw('exam_success_mark.mark'));
                    });
                });
            })
            ->when($this->selectedTypeExams != null, function ($q, $v) {
                $q->whereHas('SunnahPart', function ($q) {
                    $q->where('type', '=', $this->selectedTypeExams);
                });
            })
            ->whereBetween(DB::raw('DATE(datetime)'), [$this->searchDateFrom, $this->searchDateTo])
            ->when($this->current_role === 'محفظ', function ($q, $v) {
                $q->whereHas('student', function ($q) {
                    $q->where('group_sunnah_id', '=', Group::where('teacher_id', auth()->id())->first()->id ?? null)
                        ->when($this->selectedStudentId != null, function ($q, $v) {
                            $q->where('id', '=', $this->selectedStudentId);
                        });
                });
            })
            ->when($this->current_role === 'مختبر', function ($q, $v) {
                $q->where('tester_id', auth()->id())
                    ->whereHas('student', function ($q) {
                        $q->when($this->selectedTeacherId != null, function ($q, $v) {
                            $q->where('group_sunnah_id', '=', $this->selectedTeacherId);
                        })
                            ->when($this->selectedStudentId != null, function ($q, $v) {
                                $q->where('id', '=', $this->selectedStudentId);
                            });
                    });
            })
            ->when($this->current_role === 'مشرف', function ($q, $v) {
                $q->whereHas('student', function ($q) {
                    return $q
                        ->where('grade_id', Supervisor::whereId(auth()->id())->first()->grade_id ?? null)
                        ->when($this->selectedTeacherId != null, function ($q, $v) {
                            $q->where('group_sunnah_id', '=', $this->selectedTeacherId);
                        })
                        ->when($this->selectedStudentId != null, function ($q, $v) {
                            $q->where('id', '=', $this->selectedStudentId);
                        });
                });
            })
            ->when($this->current_role === 'أمير المركز' || $this->current_role === 'مشرف الإختبارات', function ($q, $v) {
                $q->whereHas('student', function ($q) {
                    $q->when($this->selectedTeacherId != null, function ($q, $v) {
                        $q->where('group_sunnah_id', '=', $this->selectedTeacherId);
                    })
                        ->when($this->selectedStudentId != null, function ($q, $v) {
                            $q->where('id', '=', $this->selectedStudentId);
                        });
                });
            })
            ->when(!empty(strval(\Request::segment(2)) && strval(\Request::segment(2)) !== 'message'), function ($q, $v) {
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
        return SunnahExam::query()
            ->with(['student.user:id,name', 'sunnahPart:id,name,total_hadith_parts', 'exam_success_mark', 'external_exam', 'exam_improvement', 'teacher.user:id,name', 'tester.user:id,name'])
            ->whereHas('exam_success_mark', function ($q) {
                $q->where(DB::raw('sunnah_exams.mark'), '>=', DB::raw('exam_success_mark.mark'));
            })
            ->whereDoesntHave('external_exam')
            ->get();
    }


    public function all_Grades()
    {
        if ($this->current_role === 'مشرف') {
            $this->grades = Grade::query()->where('id', Supervisor::where('id', auth()->id())->first()->grade_id)->get();
        } else if ($this->current_role === 'محفظ') {
            $this->grades = Grade::query()->where('id', Teacher::where('id', auth()->id())->first()->grade_id)->get();
        } else if ($this->current_role === 'أمير المركز' || $this->current_role === 'مشرف الإختبارات' || $this->current_role === 'مختبر') {
            $this->grades = Grade::all();
        }
    }

    public function getTeachersByGradeId()
    {
        $this->reset('students', 'groups', 'selectedTeacherId', 'selectedStudentId');

        if ($this->current_role === 'أمير المركز' || $this->current_role === 'مشرف الإختبارات' || $this->current_role === 'مختبر') {
            if ($this->selectedGradeId) {
                $this->groups = Group::query()->with(['teacher.user'])
                    ->where('type', Group::SUNNAH_TYPE)
                    ->where('grade_id', $this->selectedGradeId)->get();
            }
        } else if ($this->current_role === 'مشرف') {
            if ($this->selectedGradeId) {
                $this->groups = Group::query()->with(['teacher.user'])
                    ->where('type', Group::SUNNAH_TYPE)
                    ->where('grade_id', $this->selectedGradeId)->get();
            }
        } else if (($this->current_role === 'محفظ') && $this->selectedGradeId) {
            $this->groups = Group::query()
                ->where('type', Group::SUNNAH_TYPE)
                ->where('teacher_id', auth()->id())->get();
        }
    }

    public function getStudentsByTeacherId()
    {
        $this->reset('students', 'selectedStudentId');

        if ($this->current_role === 'أمير المركز' || $this->current_role === 'مشرف الإختبارات' || $this->current_role === 'مختبر') {
            if ($this->selectedTeacherId) {
                $this->students = Student::query()->with(['user'])->where('group_sunnah_id', $this->selectedTeacherId)->get();
            }
        } else if ($this->current_role === 'مشرف') {
            $this->students = Student::query()->with(['user'])->where('group_sunnah_id', $this->selectedTeacherId)->get();
        } else if ($this->current_role === 'محفظ') {
            $this->students = Student::query()->with(['user'])->where('group_sunnah_id', $this->selectedTeacherId)->get();
        }
    }

    public function submitExamImprovementRequest($student_id, $sunnah_part_id)
    {
        if (($this->current_role === 'محفظ') && !empty($student_id) && !empty($sunnah_part_id)) {
            $exam_order = ExamOrder::where('student_id', $student_id)->first();
            if (!$exam_order) {
                $exam_order = ExamOrder::create([
                    'type' => ExamOrder::IMPROVEMENT_TYPE,
                    'status' => ExamOrder::IN_PENDING_STATUS,
                    'partable_id' => $sunnah_part_id,
                    'partable_type' => SunnahPart::class,
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
                    $message = "لقد قام المحفظ: " . $exam_order->teacher->user->name . " بطلب تحسين درجة اختبار للطالب " . $exam_order->student->user->name . " في الجزء: " . $exam_order->partable->name . ' (' . $exam_order->partable->total_hadith_parts . ') حديث';
                    $this->push_notification($message, $title, 'manage_exams_orders/' . $exam_order->id, [$role_users->first()->user_fcm_token->device_token ?? null]);
                }
                // end push notifications to exams supervisor

                $this->dispatchBrowserEvent('alert',
                    ['type' => 'success', 'message' => 'تمت عملية طلب تحسين درجة الإختبار بنجاح.']);
            } else {
                $this->dispatchBrowserEvent('alert',
                    ['type' => 'error', 'message' => 'عذرا يوجد طلب مسبق لهذا الطالب.']);
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
        $this->modalId = '';
        $this->exam_date = null;
        $this->exam_mark = null;
        $this->student_name = null;
        $this->sunnah_part = null;
        $this->teacher_name = null;
        $this->teacher_id = null;
        $this->tester_id = null;
        $this->exam_success_mark_id = null;
        $this->ret_exam = null;
    }

    public function messages()
    {
        return [
            'exam_mark.required' => 'علامة الاختبار مطلوبة',
            'exam_mark.numeric' => 'يجب أن يكون رقم',
            'exam_mark.between' => 'يجب أن تكون علامة الاختبار بين 60 أو 100',
            'exam_date.required' => 'حقل تاريخ الإختبار مطلوب',
            'exam_date.date' => 'حقل تاريخ الإختبار يجب أن يكون تاريخ',
            'exam_date.date_format' => 'حقل تاريخ الإختبار يجب أن يكون من نوع تاريخ',
            'tester_id.required' => 'حقل المختبر مطلوب',
            'tester_id.numeric' => 'يجب اختيار صالح لحقل المختبر',
            'exam_success_mark_id.required' => 'حقل نسبة النجاح في الاختبار مطلوب',
            'exam_success_mark_id.numeric' => 'يجب اختيار صالح لحقل نسبة النجاح في الإختبار',
        ];
    }

    public function all_ExamSuccessMarks()
    {
        $this->exam_success_marks = ExamSuccessMark::get();
    }

    public function export()
    {
        return (new SunnahExamsExport($this->all_Exams()))->download('Sunnah Exams Report.xlsx', Excel::XLSX);
    }

    public function export_external_exams()
    {
        return (new SunnahExternalExamsExport($this->all_External_Exams()))->download('Sunnah External Exams Report.xlsx', Excel::XLSX);
    }
}
