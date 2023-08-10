<?php

namespace App\Http\Livewire;

use App\Models\ExamSuccessMark;
use App\Models\Grade;
use App\Models\Group;
use App\Models\QuranPart;
use App\Models\StudentDailyMemorization;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\MessageBag;
use Ramsey\Uuid\Uuid;

class AddExam extends HomeComponent
{
    public $grades = [], $groups = [], $exam_success_marks = [], $parts = [],
        $current_part_id, $current_revision_count, $current_part_cumulative_id,
        $student_id, $student_name, $teacher_id, $teacher_name, $current_cumulative_revision_count,
        $part_id, $tester_id, $exam_success_mark_id, $exam_date, $exam_mark;
    public $selectedGradeId, $selectedTeacherId, $group_type;

    protected $listeners = [
        'getTeachersByGradeId' => 'getTeachersByGradeId',
        'getStudentsByTeacherId' => 'all_Students',
    ];

    public function mount()
    {
        $this->current_role = auth()->user()->current_role;
        $this->all_Grades();
        $this->all_Testers();
        $this->all_ExamSuccessMarks();
    }

    public function render()
    {
        if ($this->student_id !== null) {
            $this->all_parts();
        }

        return view('livewire.add-exam', [
            'students' => $this->all_Students(),]);
    }

    public function submitSearch()
    {
        $this->all_Students();
    }

    public function lunchModalAddExam($student_id, $type)
    {
        $this->student_id = $student_id;
        $this->group_type = $type;
        if ($type === 'sunnah_exam') {
            $user_info = DB::table('students')
                ->select([DB::raw('std_usr.name student_name'), DB::raw('tea_usr.name teacher_name'),
                    DB::raw('tea_usr.id teacher_id')])
                ->join('groups', 'students.group_sunnah_id', '=', 'groups.id')
                ->join('users as std_usr', 'students.id', '=', 'std_usr.id')
                ->join('users as tea_usr', 'groups.teacher_id', '=', 'tea_usr.id')
                ->where('students.id', '=', $this->student_id)->first();
        } else {
            $user_info = DB::table('students')
                ->select([DB::raw('std_usr.name student_name'), DB::raw('tea_usr.name teacher_name'),
                    DB::raw('tea_usr.id teacher_id'), DB::raw('students.current_part_id current_part_id'),
                    DB::raw('students.current_revision_count current_revision_count'),
                    DB::raw('students.current_part_cumulative_id current_part_cumulative_id'),
                    DB::raw('students.current_cumulative_revision_count current_cumulative_revision_count')])
                ->join('groups', 'students.group_id', '=', 'groups.id')
                ->join('users as std_usr', 'students.id', '=', 'std_usr.id')
                ->join('users as tea_usr', 'groups.teacher_id', '=', 'tea_usr.id')
                ->where('students.id', '=', $this->student_id)->first();
        }

        $this->teacher_id = $user_info->teacher_id;
        $this->student_name = $user_info->student_name;
        $this->teacher_name = $user_info->teacher_name;
        $this->current_part_id = $user_info->current_part_id ?? null;
        $this->current_revision_count = $user_info->current_revision_count ?? 0;
        $this->current_part_cumulative_id = $user_info->current_part_cumulative_id ?? null;
        $this->current_cumulative_revision_count = $user_info->current_cumulative_revision_count ?? 0;
        $this->dispatchBrowserEvent('showModal');
    }

    public function all_parts()
    {
        if ($this->group_type === 'sunnah_exam') {
            $this->parts = DB::table('sunnah_parts')
                ->select([DB::raw('sunnah_parts.id id'), DB::raw("GROUP_CONCAT(sunnah_parts.name,' (',sunnah_parts.total_hadith_parts,') حديث') name")])
                ->whereNotExists(function ($query) {
                    $query->from('sunnah_exams')
                        ->where('sunnah_exams.student_id', '=', $this->student_id)
                        ->where('sunnah_exams.sunnah_part_id', '=', DB::raw('sunnah_parts.id'))
                        ->join('exam_success_mark', DB::raw('sunnah_exams.exam_success_mark_id'), '=', DB::raw('exam_success_mark.id'))
                        ->where('sunnah_exams.mark', '>=', DB::raw('exam_success_mark.mark'));
                })
                ->orderBy('sunnah_parts.arrangement')
                ->groupBy(['id'])
                ->get();
        } else {
            $this->parts = DB::table('quran_parts')
                ->select([DB::raw('quran_parts.id id'), DB::raw("(GROUP_CONCAT(quran_parts.name,' ',quran_parts.description SEPARATOR '')) as `name`")])
                ->whereNotExists(function ($query) {
                    $query->from('exams')
                        ->where('exams.student_id', '=', $this->student_id)
                        ->where('exams.quran_part_id', '=', DB::raw('quran_parts.id'))
                        ->join('exam_success_mark', DB::raw('exams.exam_success_mark_id'), '=', DB::raw('exam_success_mark.id'))
                        ->where('exams.mark', '>=', DB::raw('exam_success_mark.mark'));
                })
                ->orderBy('quran_parts.arrangement')
                ->groupBy(['id'])
                ->get();
        }
    }

    public function validateModal()
    {
        $this->validate([
            'student_id' => 'required|numeric',
            'teacher_id' => 'required|numeric',
            'part_id' => 'required|numeric',
            'tester_id' => 'required|numeric',
            'exam_success_mark_id' => 'required|numeric',
            'exam_date' => 'required||date|date_format:Y-m-d',
            'exam_mark' => 'required|numeric|between:60,100',
        ]);

//        if ($this->group_type === 'sunnah_exam') {
//            $this->addExam();
//        } else {
//            $this->checkLinksToPreviousRecords();
        $this->addExam();
//        }
    }

    public function checkLinksToPreviousRecords()
    {
        if ($this->part_id == 16 || $this->part_id == 17 || $this->part_id == 18) {
            $this->checkLinksCustomRecords();
        } else {
            $messageBag = new MessageBag();
            $quranPart = QuranPart::whereId($this->part_id)->first();
            if ($quranPart->type === QuranPart::INDIVIDUAL_TYPE) {
                if ($this->all_QuranSuras(StudentDailyMemorization::MEMORIZE_TYPE, [$this->part_id])->isEmpty()) {
                    $revision_count = $this->current_part_id == $this->part_id ? $this->current_revision_count : 1;
                    if ($revision_count != 2) {
                        if ($this->all_QuranSuras(StudentDailyMemorization::REVIEW_TYPE, [$this->part_id], $revision_count)->isEmpty()) {
                            $this->addExam();
                        } else {
                            $message = 'عذرا, الطالب لم ينتهي من مراجعة الجزء.';
                            if ($revision_count >= 3) {
                                $message = 'عذرا, الطالب لم ينتهي من مراجعة الجزء للمرة ' . $revision_count . ' .';
                            }
                            $messageBag->add('part_id', $message);
                        }
                    } else {
                        $this->addExam();
                    }
                } else {
                    $messageBag->add('part_id', 'عذرا, الطالب لم ينتهي من حفظ الجزء.');
                }
            } else {
                $revision_count = $this->current_part_cumulative_id == $this->part_id ? $this->current_cumulative_revision_count : 1;
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

                        if ($this->all_QuranSuras(StudentDailyMemorization::CUMULATIVE_REVIEW_TYPE, $parts->pluck('id')->toArray(), $revision_count, count($parts))->isEmpty()) {
                            $this->addExam();
                        } else {
                            $message = 'عذرا, الطالب لم ينتهي من مراجعة سور أجزاء التجميعي.';
                            if ($revision_count >= 3) {
                                $message = 'عذرا, الطالب لم ينتهي من مراجعة سور أجزاء التجميعي للمرة ' . $revision_count . ' .';
                            }
                            $messageBag->add('part_id', $message);
                        }
                    }
                } else {
                    $this->addExam();
                }
            }
            $this->setErrorBag($messageBag);
        }
    }

    /**
     * @throws JsonException
     */
    private function checkLinksCustomRecords()
    {
        // فحص سجلات سورة البقرة والفاتحة ...
        $messageBag = new MessageBag();
        if ($this->custom_QuranSuras(StudentDailyMemorization::MEMORIZE_TYPE, 1, $this->part_id != 17)->isEmpty()) {
            $revision_count = $this->current_part_id == $this->part_id ? $this->current_revision_count : 1;
            if ($revision_count != 2) {
                if ($this->custom_QuranSuras(StudentDailyMemorization::REVIEW_TYPE, $revision_count, $this->part_id != 17)->isEmpty()) {
                    $this->addExam();
                } else {
                    $message = 'عذرا, الطالب لم ينتهي من مراجعة الجزء.';
                    if ($revision_count >= 3) {
                        $message = 'عذرا, الطالب لم ينتهي من مراجعة الجزء للمرة ' . $revision_count . ' .';
                    }
                    $messageBag->add('part_id', $message);
                }
            } else {
                $this->addExam();
            }
        } else {
            $messageBag->add('part_id', 'عذرا, الطالب لم ينتهي من حفظ الجزء.');
        }
        $this->setErrorBag($messageBag);
    }

    public function addExam()
    {
        DB::table($this->group_type === 'sunnah_exam' ? 'sunnah_exams' : 'exams')->insert(
            [
                'id' => Uuid::uuid6()->toString(),
                'mark' => $this->exam_mark,
                $this->group_type === 'sunnah_exam' ? 'sunnah_part_id' : 'quran_part_id' => $this->part_id,
                'student_id' => $this->student_id,
                'teacher_id' => $this->teacher_id,
                'tester_id' => $this->tester_id,
                'exam_success_mark_id' => $this->exam_success_mark_id,
                'datetime' => $this->exam_date . ' ' . date('H:i:s', time()),
                'updated_at' => $this->exam_date . ' ' . date('H:i:s', time()),
                'created_at' => $this->exam_date . ' ' . date('H:i:s', time()),
                'notes' => null,
            ]
        );

        $this->dispatchBrowserEvent('hideModal');
        $this->dispatchBrowserEvent('alert',
            ['type' => 'success', 'message' => 'تمت عملية اعتماد اختبار الطالب بنجاح.']);
        $this->clearForm();
    }


    public function custom_QuranSuras($type, $revision_count = 1, $isSuraCompleted = true): Collection
    {
        return DB::table('quran_suras')
            ->select(['id', 'name', 'quran_part_id', 'total_number_aya', DB::raw('(select aya_to from  students_daily_memorization
                   INNER JOIN daily_memorization_details ON students_daily_memorization.id = daily_memorization_details.id
                   WHERE student_id = ' . $this->student_id . ' and type="' . $type . '" and revision_count="' . $revision_count . '"
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
                   WHERE student_id = ' . $this->student_id . ' and type="' . $type . '" and cumulative_type="' . $cumulative_type . '"
                   and revision_count="' . $revision_count . '"  and sura_id = quran_suras.id ORDER by aya_to desc LIMIT 1 ) as aya_to')])
            ->when($current_part_id !== null, function ($q) use ($current_part_id) {
                $q->whereIn('quran_part_id', $current_part_id);
            })
            ->havingNull(DB::raw('aya_to'))
            ->orHaving(DB::raw('aya_to'), '<', DB::raw('total_number_aya'))
            ->orderByDesc('id')
            ->get();
    }

    public function clearForm()
    {
        $this->parts = [];
        $this->exam_success_mark_id = null;
        $this->part_id = null;
        $this->tester_id = null;
        $this->student_id = null;
        $this->teacher_id = null;
        $this->exam_date = null;
        $this->exam_mark = null;
        $this->student_name = null;
        $this->teacher_name = null;
        $this->current_part_id = null;
        $this->current_revision_count = null;
        $this->current_part_cumulative_id = null;
        $this->current_cumulative_revision_count = null;
    }

    public function messages()
    {
        return [
            'teacher_id.required' => 'حقل المحفظ مطلوب',
            'teacher_id.numeric' => 'يجب اختيار صالح لحقل المحفظ',
            'student_id.required' => 'حقل الطالب مطلوب',
            'student_id.numeric' => 'يجب اختيار صالح لحقل الطالب',
            'tester_id.required' => 'حقل المختبر مطلوب',
            'tester_id.numeric' => 'يجب اختيار صالح لحقل المختبر',
            'part_id.required' => 'حقل جزء الإختبار مطلوب',
            'part_id.numeric' => 'يجب اختيار صالح لحقل الجزء',
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

    public function all_Students()
    {
        return DB::table('students')
            ->when(!empty($this->search), function ($q, $v) {
                $q->where('users.name', 'LIKE', "%$this->search%")
                    ->orWhere('users.identification_number', 'LIKE', "%$this->search%");
            })->when($this->selectedGradeId !== null, function ($q, $v) {
                $q->where('students.grade_id', '=', $this->selectedGradeId);
            })->when($this->selectedTeacherId !== null, function ($q, $v) {
                $q->where('students.group_id', '=', $this->selectedTeacherId)
                    ->orWhere('students.group_sunnah_id', '=', $this->selectedTeacherId);
            })->select(['students.id', 'students.group_sunnah_id', 'users.name as student_name', 'users.identification_number as student_identification_number',
                'users.profile_photo', 'grades.name as grade_name', 'users_teacher.name as teacher_name',
                DB::raw("(GROUP_CONCAT(part_last_exam.name,' ',part_last_exam.description SEPARATOR '')) as `quran_part`"),
                DB::raw('last_exam.mark exam_mark'), DB::raw('improvement_exams.mark improvement_mark'), DB::raw('exam_success_mark.mark success_mark')])
            ->join('users', 'students.id', '=', 'users.id')
            ->join('groups', 'students.group_id', '=', 'groups.id')
            ->join('users as users_teacher', 'groups.teacher_id', '=', 'users_teacher.id')
            ->join('grades', 'students.grade_id', '=', 'grades.id')
            ->leftJoin('exams as last_exam', function ($join) {
                $join->on('students.id', '=', 'last_exam.student_id')
                    ->on('last_exam.id', '=', DB::raw("(SELECT exams.id FROM exams
                  WHERE student_id = students.id ORDER BY datetime DESC LIMIT 1)"));
            })
            ->leftJoin('quran_parts as part_last_exam', 'last_exam.quran_part_id', '=', 'part_last_exam.id')
            ->leftJoin('improvement_exams', 'last_exam.id', '=', 'improvement_exams.id')
            ->leftJoin('exam_success_mark', 'last_exam.exam_success_mark_id', '=', 'exam_success_mark.id')
            ->orderBy('students.' . $this->sortBy, $this->sortDirection)
            ->groupBy(['students.id', 'exam_mark', 'improvement_mark', 'success_mark'])
            ->paginate($this->perPage);
    }

    public function all_Grades()
    {
        $this->grades = Grade::all();
    }

    public function getTeachersByGradeId()
    {
        $this->reset('groups', 'selectedTeacherId');

        if ($this->selectedGradeId) {
            $this->groups = Group::query()->with(['teacher.user'])->where('grade_id', $this->selectedGradeId)->get();
        }
    }

    public function all_ExamSuccessMarks()
    {
        $this->exam_success_marks = ExamSuccessMark::get();
    }
}
