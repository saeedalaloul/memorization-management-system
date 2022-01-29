<?php

namespace App\Http\Livewire;

use App\Models\ExamOrder;
use App\Models\Grade;
use App\Models\Group;
use App\Models\LowerSupervisor;
use App\Models\Student;
use App\Models\Supervisor;
use App\Models\Teacher;
use App\Models\Tester;
use Illuminate\Support\MessageBag;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class ExamsOrders extends Component
{
    use WithPagination;

    public $sortBy = 'id';
    public $sortDirection = 'desc';
    public $perPage = 10;
    public $search = '';
    public $grades, $groups, $students;
    public $searchGradeId, $searchGroupId, $searchStudentId;
    public $student_name, $quran_part;
    public $tester_id, $teacher_id, $modalId, $exam_date, $notes;
    protected $paginationTheme = 'bootstrap';

    public function render()
    {
        $this->all_Grades();
        $this->all_Groups();
        $this->all_Students();
        $this->read_All_Exams_Orders();

        return view('livewire.exams-orders', [
            'exam_orders' => $this->all_Exam_Orders(),
            'testers' => $this->all_Testers(),
        ]);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function all_Exam_Orders()
    {
        if (auth()->user()->current_role == 'محفظ') {
            if (empty($this->searchStudentId)) {
                return ExamOrder::query()
                    ->search($this->search)
                    ->whereHas('student', function ($q) {
                        return $q->where('group_id', '=', Group::where('teacher_id', auth()->id())->first()->id);
                    })
                    ->orderBy($this->sortBy, $this->sortDirection)
                    ->paginate($this->perPage);
            } else {
                return ExamOrder::query()
                    ->search($this->search)
                    ->whereHas('student', function ($q) {
                        return $q->where('group_id', '=', $this->searchGroupId)
                            ->where('id', '=', $this->searchStudentId);
                    })
                    ->orderBy($this->sortBy, $this->sortDirection)
                    ->paginate($this->perPage);
            }

        } elseif (auth()->user()->current_role == 'مشرف') {
            return $this->getExamsByGrade(Supervisor::where('id', auth()->id())->first()->grade_id);
        } elseif (auth()->user()->current_role == 'اداري') {
            return $this->getExamsByGrade(LowerSupervisor::where('id', auth()->id())->first()->grade_id);
        } else if (auth()->user()->current_role == 'مختبر') {
            return ExamOrder::query()
                ->search($this->search)
                ->whereIn('status', [2, -3])
                ->where('tester_id', auth()->id())
                ->orderBy($this->sortBy, $this->sortDirection)
                ->paginate($this->perPage);
        } else if (auth()->user()->current_role == 'أمير المركز' ||
            auth()->user()->current_role == 'مشرف الإختبارات') {
            return $this->getExamsByAdmin();
        }
        return [];
    }

    private function getExamsByAdmin()
    {
        if (empty($this->searchGradeId)) {
            if (auth()->user()->current_role == 'أمير المركز') {
                return ExamOrder::query()
                    ->search($this->search)
                    ->orderBy($this->sortBy, $this->sortDirection)
                    ->paginate($this->perPage);
            } else {
                return ExamOrder::query()
                    ->whereIn('status', [1, 2, -2, -3])
                    ->search($this->search)
                    ->orderBy($this->sortBy, $this->sortDirection)
                    ->paginate($this->perPage);
            }
        } else {
            if (empty($this->searchGroupId)) {
                if (auth()->user()->current_role == 'أمير المركز') {
                    return ExamOrder::query()
                        ->search($this->search)
                        ->whereHas('student', function ($q) {
                            return $q->where('grade_id', '=', $this->searchGradeId);
                        })
                        ->orderBy($this->sortBy, $this->sortDirection)
                        ->paginate($this->perPage);
                } else {
                    return ExamOrder::query()
                        ->whereIn('status', [1, 2, -2, -3])
                        ->search($this->search)
                        ->whereHas('student', function ($q) {
                            return $q->where('grade_id', '=', $this->searchGradeId);
                        })
                        ->orderBy($this->sortBy, $this->sortDirection)
                        ->paginate($this->perPage);
                }
            } else {
                if (empty($this->searchStudentId)) {
                    if (auth()->user()->current_role == 'أمير المركز') {
                        return ExamOrder::query()
                            ->search($this->search)
                            ->whereHas('student', function ($q) {
                                return $q->where('grade_id', '=', $this->searchGradeId)
                                    ->where('group_id', '=', $this->searchGroupId);
                            })
                            ->orderBy($this->sortBy, $this->sortDirection)
                            ->paginate($this->perPage);
                    } else {
                        return ExamOrder::query()
                            ->whereIn('status', [1, 2, -2, -3])
                            ->search($this->search)
                            ->whereHas('student', function ($q) {
                                return $q->where('grade_id', '=', $this->searchGradeId)
                                    ->where('group_id', '=', $this->searchGroupId);
                            })
                            ->orderBy($this->sortBy, $this->sortDirection)
                            ->paginate($this->perPage);
                    }
                } else {
                    if (auth()->user()->current_role == 'أمير المركز') {
                        return ExamOrder::query()
                            ->search($this->search)
                            ->whereHas('student', function ($q) {
                                return $q
                                    ->where('grade_id', '=', $this->searchGradeId)
                                    ->where('group_id', '=', $this->searchGroupId)
                                    ->where('id', '=', $this->searchStudentId);
                            })
                            ->orderBy($this->sortBy, $this->sortDirection)
                            ->paginate($this->perPage);
                    } else {
                        return ExamOrder::query()
                            ->whereIn('status', [1, 2, -2, -3])
                            ->search($this->search)
                            ->whereHas('student', function ($q) {
                                return $q
                                    ->where('grade_id', '=', $this->searchGradeId)
                                    ->where('group_id', '=', $this->searchGroupId)
                                    ->where('id', '=', $this->searchStudentId);
                            })
                            ->orderBy($this->sortBy, $this->sortDirection)
                            ->paginate($this->perPage);
                    }
                }
            }
        }
    }

    private function getExamsByGrade($grade_id)
    {
        if (empty($this->searchGroupId)) {
            return ExamOrder::query()
                ->search($this->search)
                ->whereHas('student', function ($q) use ($grade_id) {
                    return $q->where('grade_id', '=', $grade_id);
                })
                ->orderBy($this->sortBy, $this->sortDirection)
                ->paginate($this->perPage);
        } else {
            if (empty($this->searchStudentId)) {
                return ExamOrder::query()
                    ->search($this->search)
                    ->whereHas('student', function ($q) use ($grade_id) {
                        return $q->where('grade_id', '=', $grade_id)
                            ->where('group_id', '=', $this->searchGroupId);
                    })
                    ->orderBy($this->sortBy, $this->sortDirection)
                    ->paginate($this->perPage);
            } else {
                return ExamOrder::query()
                    ->search($this->search)
                    ->whereHas('student', function ($q) use ($grade_id) {
                        return $q
                            ->where('grade_id', '=', $grade_id)
                            ->where('group_id', '=', $this->searchGroupId)
                            ->where('id', '=', $this->searchStudentId);
                    })
                    ->orderBy($this->sortBy, $this->sortDirection)
                    ->paginate($this->perPage);
            }
        }
    }

    public function all_Testers()
    {
        if (auth()->user()->current_role == 'أمير المركز' ||
            auth()->user()->current_role == 'مشرف الإختبارات') {
            return Tester::all();
        }
        return [];
    }


    public function all_Grades()
    {
        if (auth()->user()->current_role == 'أمير المركز' ||
            auth()->user()->current_role == 'مشرف الإختبارات') {
            $this->grades = Grade::all();
        } else if (auth()->user()->current_role == 'مشرف') {
            $this->searchGradeId = Supervisor::where('id', auth()->id())->first()->grade_id;
        } else if (auth()->user()->current_role == 'اداري') {
            $this->searchGradeId = LowerSupervisor::where('id', auth()->id())->first()->grade_id;
        } else if (auth()->user()->current_role == 'محفظ') {
            $this->searchGradeId = Teacher::where('id', auth()->id())->first()->grade_id;
        }
    }

    public function all_Groups()
    {
        if (auth()->user()->current_role == 'أمير المركز' ||
            auth()->user()->current_role == 'مشرف الإختبارات') {
            if ($this->searchGradeId) {
                $this->groups = Group::query()->where('grade_id', $this->searchGradeId)->get();
            }
        } else if (auth()->user()->current_role == 'مشرف') {
            $this->groups = Group::query()
                ->where('grade_id', '=', $this->searchGradeId)->get();
        } else if (auth()->user()->current_role == 'اداري') {
            $this->groups = Group::query()
                ->where('grade_id', '=', $this->searchGradeId)->get();
        } else if (auth()->user()->current_role == 'محفظ') {
            $this->searchGroupId = Group::query()->where('teacher_id', auth()->id())->first()->id;
        }

    }

    public function all_Students()
    {
        if (auth()->user()->current_role == 'أمير المركز' ||
            auth()->user()->current_role == 'مشرف الإختبارات') {
            if ($this->searchGroupId) {
                $this->students = Student::query()->where('group_id', $this->searchGroupId)->get();
            }
        } else if (auth()->user()->current_role == 'مشرف') {
            $this->students = Student::query()->where('group_id', $this->searchGroupId)->get();
        } else if (auth()->user()->current_role == 'اداري') {
            $this->students = Student::query()->where('group_id', $this->searchGroupId)->get();
        } else if (auth()->user()->current_role == 'محفظ') {
            $this->students = Student::query()->where('group_id', $this->searchGroupId)->get();
        }
    }

    private function read_All_Exams_Orders()
    {
        $exams_orders = $this->all_Exam_Orders();

        if ($exams_orders != null && !empty($exams_orders)) {
            for ($i = 0; $i < count($exams_orders); $i++) {
                if (auth()->user()->current_role == 'محفظ') {
                    if ($exams_orders[$i]->readable['isReadableTeacher'] == false) {
                        $examOrder = ExamOrder::find($exams_orders[$i]->id);
                        $array = $examOrder->readable;
                        $array['isReadableTeacher'] = true;
                        $examOrder->update(['readable' => $array]);
                    }
                } else if (auth()->user()->current_role == 'مشرف') {
                    if ($exams_orders[$i]->readable['isReadableSupervisor'] == false) {
                        $examOrder = ExamOrder::find($exams_orders[$i]->id);
                        $array = $examOrder->readable;
                        $array['isReadableSupervisor'] = true;
                        $examOrder->update(['readable' => $array]);
                    }
                } else if (auth()->user()->current_role == 'مختبر') {
                    if ($exams_orders[$i]->readable['isReadableTester'] == false) {
                        $examOrder = ExamOrder::find($exams_orders[$i]->id);
                        $array = $examOrder->readable;
                        $array['isReadableTester'] = true;
                        $examOrder->update(['readable' => $array]);
                    }
                } else if (auth()->user()->current_role == 'مشرف الإختبارات') {
                    if ($exams_orders[$i]->readable['isReadableSupervisorExams'] == false) {
                        $examOrder = ExamOrder::find($exams_orders[$i]->id);
                        $array = $examOrder->readable;
                        $array['isReadableSupervisorExams'] = true;
                        $examOrder->update(['readable' => $array]);
                    }
                }
            }
        }
    }

    public function sortBy($field)
    {
        if ($this->sortDirection == 'asc') {
            $this->sortDirection = 'desc';
        } else {
            $this->sortDirection = 'asc';
        }

        return $this->sortBy = $field;
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName, [
            'tester_id' => 'required|string',
            'exam_date' => 'required|date|date_format:Y-m-d',
            'notes' => 'required|string',
        ]);
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
        $array = ["isReadableTeacher" => false, "isReadableSupervisor" => false,
            "isReadableTester" => false, "isReadableSupervisorExams" => false];
        if ($examOrder) {
            if ($examOrder->status == 0 || $examOrder->status == -1) {
                if (auth()->user()->current_role == 'مشرف' ||
                    auth()->user()->current_role == 'أمير المركز') {
                    $examOrder->update([
                        'status' => 1,
                        'notes' => null,
                        'readable' => $array,
                    ]);
                    // push notification
                    $arr_external_user_ids = [];

                    $user_role_supervisor_exams = Role::where('name', 'مشرف الإختبارات')->first();
                    if ($user_role_supervisor_exams != null && $user_role_supervisor_exams->users != null
                        && $user_role_supervisor_exams->users[0] != null) {
                        array_push($arr_external_user_ids, "" . $user_role_supervisor_exams->users[0]->id);
                    }

                    array_push($arr_external_user_ids, "" . $examOrder->teacher_id);

                    if (auth()->user()->current_role != 'مشرف') {
                        $user_role_supervisor_id = Supervisor::where('grade_id', $examOrder->student->grade_id)->first()->id;
                        array_push($arr_external_user_ids, "" . $user_role_supervisor_id);
                    }

                    $this->process_notification($arr_external_user_ids, 1, $examOrder->student->user->name, $examOrder->quranPart->name);
                    $this->dispatchBrowserEvent('alert',
                        ['type' => 'success', 'message' => 'تمت عملية قبول طلب الإختبار بنجاح.']);
                }
            } elseif ($examOrder->status == 1 || $examOrder->status == 2 || $examOrder->status == -2) {
                $this->validate();
                $messageBag = new MessageBag;
                $messageBag->add('tester_id', 'يجب أن لا يكون المختبر هو نفس المحفظ');
                if ($this->teacher_id == $this->tester_id) {
                    $this->setErrorBag($messageBag);
                } else {
                    if (auth()->user()->current_role == 'مشرف الإختبارات' ||
                        auth()->user()->current_role == 'أمير المركز') {
                        $examOrder->update([
                            'status' => 2,
                            'tester_id' => $this->tester_id,
                            'exam_date' => $this->exam_date,
                            'readable' => $array,
                            'notes' => null,
                        ]);
                        // push notification
                        $arr_external_user_ids = [];

                        if (auth()->user()->current_role != 'مشرف الإختبارات') {
                            $user_role_supervisor_exams = Role::where('name', 'مشرف الإختبارات')->first();
                            if ($user_role_supervisor_exams != null && $user_role_supervisor_exams->users != null
                                && $user_role_supervisor_exams->users[0] != null) {
                                array_push($arr_external_user_ids, "" . $user_role_supervisor_exams->users[0]->id);
                            }
                        }

                        array_push($arr_external_user_ids, "" . $examOrder->teacher_id);

                        $this->process_notification($arr_external_user_ids, 2, $examOrder->student->user->name, $examOrder->quranPart->name);

                        // push notification to tester
                        if (auth()->user()->current_role == 'أمير المركز') {
                            $message = "لقد قام أمير المركز بتعيينك مختبر طلب اختبار " . $examOrder->quranPart->name . " للطالب: " . $examOrder->student->user->name . " يرجى مراجعة الموعد المحدد للطلب ...";
                        } else if (auth()->user()->current_role == 'مشرف الإختبارات') {
                            $message = "لقد قام مشرف الإختبارات بتعيينك مختبر طلب اختبار " . $examOrder->quranPart->name . " للطالب: " . $examOrder->student->user->name . " يرجى مراجعة الموعد المحدد للطلب ...";
                        }
                        $response = $this->push_notifications([$examOrder->tester_id], $message);
                        $return["allresponses"] = $response;
                        $return = json_encode($return);
                        $this->dispatchBrowserEvent('alert',
                            ['type' => 'success', 'message' => 'تمت عملية اعتماد طلب الإختبار بنجاح.']);
                        $this->emit('approval-exam');
                        $this->clearForm();
                    }
                }
            }
        }
    }

    public function process_notification($arr_external_user_ids, $status, $student_name, $part_name)
    {
        if ($arr_external_user_ids != null && $status != null) {
            $message = "";
            if (auth()->user()->current_role == 'أمير المركز') {
                if ($status == 1) {
                    $message = "لقد قام أمير المركز بقبول طلب اختبار " . $part_name . " للطالب: " . $student_name . " يرجى مراجعة طلب الاختبار ...";
                } else if ($status == -1) {
                    $message = "لقد قام أمير المركز برفض طلب اختبار " . $part_name . " للطالب: " . $student_name . " يرجى مراجعة طلب الاختبار ...";
                } else if ($status == 2) {
                    $message = "لقد قام أمير المركز بحجز طلب اختبار " . $part_name . " للطالب: " . $student_name . " يرجى مراجعة الموعد المحدد للطلب ...";
                } elseif ($status == -2) {
                    $message = "لقد قام أمير المركز برفض حجز طلب اختبار " . $part_name . " للطالب: " . $student_name . " يرجى مراجعة طلب الاختبار ...";
                }
            } else if (auth()->user()->current_role == 'مشرف الإختبارات') {
                if ($status == 2) {
                    $message = "لقد قام مشرف الإختبارات بحجز طلب اختبار " . $part_name . " للطالب: " . $student_name . " يرجى مراجعة الموعد المحدد للطلب ...";
                } elseif ($status == -2) {
                    $message = "لقد قام مشرف الإختبارات برفض حجز طلب اختبار " . $part_name . " للطالب: " . $student_name . " يرجى مراجعة طلب الاختبار ...";
                }
            } else if (auth()->user()->current_role == 'مشرف') {
                if ($status == 1) {
                    $message = "لقد قام مشرف المرحلة بقبول طلب اختبار " . $part_name . " للطالب: " . $student_name . " يرجى مراجعة طلب الاختبار ...";
                } else if ($status == -1) {
                    $message = "لقد قام مشرف المرحلة برفض طلب اختبار " . $part_name . " للطالب: " . $student_name . " يرجى مراجعة طلب الاختبار ...";
                }
            }
            $response = $this->push_notifications($arr_external_user_ids, $message);
            $return["allresponses"] = $response;
            $return = json_encode($return);
        }
    }

    public function push_notifications($arr_external_user_ids, $message)
    {
        $fields = array(
            'app_id' => env("ONE_SIGNAL_APP_ID"),
            'include_external_user_ids' => $arr_external_user_ids,
            'channel_for_external_user_ids' => 'push',
            'data' => array("foo" => "bar"),
            'headings' => array(
                "en" => 'حالة طلب الاختبار',
                "ar" => 'حالة طلب الاختبار',
            ),
            'url' => 'https://memorization-management-system.herokuapp.com/manage_exams_orders',
            'contents' => array(
                "en" => $message,
                "ar" => $message,
            )
        );

        $fields = json_encode($fields);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
            'Authorization: Basic ' . env('ONE_SIGNAL_AUTHORIZE')));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }


    public function clearForm()
    {
        $this->modalId = null;
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
        $array = ["isReadableTeacher" => false, "isReadableSupervisor" => false,
            "isReadableTester" => false, "isReadableSupervisorExams" => false];

        if ($examOrder) {
            if ($examOrder->status == 0) {
                $this->validate(['notes' => 'required|string',]);
                if (auth()->user()->current_role == 'مشرف' ||
                    auth()->user()->current_role == 'أمير المركز') {
                    $examOrder->update(['status' => -1, 'notes' => $this->notes, 'readable' => $array,]);
                    $this->emit('refusal-exam');
                    $this->dispatchBrowserEvent('alert',
                        ['type' => 'error', 'message' => 'تمت عملية رفض طلب الإختبار بنجاح.']);
                    $this->clearForm();
                    $this->process_notification([$examOrder->teacher_id], -1, $examOrder->student->user->name, $examOrder->quranPart->name);
                }
            } elseif ($examOrder->status == 2) {
                $this->validate(['notes' => 'required|string',]);
                if (auth()->user()->current_role == 'مشرف الإختبارات' ||
                    auth()->user()->current_role == 'أمير المركز') {
                    $examOrder->update([
                        'status' => -2,
                        'notes' => $this->notes,
                        'tester_id' => null,
                        'exam_date' => null,
                        'readable' => $array,
                    ]);
                    $this->emit('refusal-exam');
                    $this->dispatchBrowserEvent('alert',
                        ['type' => 'error', 'message' => 'تمت عملية رفض طلب الإختبار بنجاح.']);
                    $this->clearForm();
                    $this->process_notification([$examOrder->teacher_id], -2, $examOrder->student->user->name, $examOrder->quranPart->name);
                }
            } elseif ($examOrder->status == 1) {
                $this->validate(['notes' => 'required|string',]);
                if (auth()->user()->current_role == 'مشرف الإختبارات' || auth()->user()->current_role == 'أمير المركز') {
                    $examOrder->update([
                        'status' => -2,
                        'notes' => $this->notes,
                        'readable' => $array,
                    ]);
                    $this->process_notification([$examOrder->teacher_id], -2, $examOrder->student->user->name, $examOrder->quranPart->name);
                } elseif (auth()->user()->current_role == 'مشرف') {
                    $examOrder->update([
                        'status' => -1,
                        'notes' => $this->notes,
                        'readable' => $array,
                    ]);
                    $this->process_notification([$examOrder->teacher_id], -1, $examOrder->student->user->name, $examOrder->quranPart->name);
                }
                $this->emit('refusal-exam');
                $this->dispatchBrowserEvent('alert',
                    ['type' => 'error', 'message' => 'تمت عملية رفض طلب الإختبار بنجاح.']);
                $this->clearForm();
            }
        }
    }

    public function getExamOrder($id)
    {
        $this->clearForm();
        $examOrder = ExamOrder::where('id', $id)->first();
        if ($examOrder) {
            $this->modalId = $examOrder->id;
            $this->student_name = $examOrder->student->user->name;
            $this->quran_part = $examOrder->quranPart->name;
            $this->teacher_id = $examOrder->teacher_id;
            if ($examOrder->tester_id != null) {
                $this->tester_id = $examOrder->tester_id;
            }
            $this->exam_date = $examOrder->exam_date;
        }
    }

    public function destroy($id)
    {
        $exam_order = ExamOrder::find($id);
        if ($exam_order->status == 0 ||
            $exam_order->status == -1 ||
            $exam_order->status == -2 ||
            $exam_order->status == -3) {
            $exam_order->delete();
            $this->emit('delete-exam-order');
            $this->dispatchBrowserEvent('alert',
                ['type' => 'error', 'message' => 'تم حذف طلب الإختبار بنجاح.']);
        }
    }
}
