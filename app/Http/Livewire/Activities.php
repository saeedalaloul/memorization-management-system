<?php

namespace App\Http\Livewire;

use App\Models\Activity;
use App\Models\Grade;
use App\Models\Group;
use App\Models\Student;
use App\Models\Supervisor;
use App\Models\Teacher;
use Illuminate\Support\Facades\DB;

class Activities extends HomeComponent
{
    public $grades = [], $groups = [];
    public $selectedGradeId, $selectedTeacherId, $searchDateFrom, $searchDateTo;

    protected $listeners = [
        'getTeachersByGradeId' => 'getTeachersByGradeId',
    ];

    public function mount()
    {
        $this->current_role = auth()->user()->current_role;
        $this->all_Grades();
    }

    public function all_Grades()
    {
        if ($this->current_role === 'مشرف') {
            $this->grades = Grade::query()->where('id', Supervisor::where('id', auth()->id())->first()->grade_id)->get();
        } else if ($this->current_role === 'محفظ') {
            $this->grades = Grade::query()->where('id', Teacher::where('id', auth()->id())->first()->grade_id)->get();
        } elseif ($this->current_role === 'أمير المركز' || $this->current_role === 'مشرف الأنشطة' || $this->current_role === 'منشط') {
            $this->grades = Grade::all();
        }
    }

    public function getTeachersByGradeId()
    {
        $this->reset('groups', 'selectedTeacherId');

        if ($this->current_role === 'أمير المركز' || $this->current_role === 'مشرف الأنشطة' || $this->current_role === 'منشط') {
            if ($this->selectedGradeId) {
                $this->groups = Group::query()->with(['teacher.user'])->where('grade_id', $this->selectedGradeId)->get();
            }
        } else if ($this->current_role === 'مشرف') {
            if ($this->selectedGradeId) {
                $this->groups = Group::query()->with(['teacher.user'])->where('grade_id', $this->selectedGradeId)->get();
            }
        } elseif ($this->current_role === 'محفظ') {
            $this->groups = Group::query()->where('teacher_id', auth()->id())->get();
        }

    }

    public function render()
    {
//        $suras = DB::table('quran_suras')
//            ->select(['id', 'name', 'quran_part_id', 'total_number_aya'])
//            ->orderByDesc('id')
//            ->get();
//
//       $memorizations = DB::table('students_daily_memorization')
//            ->select(['id', 'created_at', 'updated_at', 'sura_from_id', 'sura_to_id', 'aya_from', 'aya_to'])
//            ->get();
//
//       foreach ($memorizations as $memorization){
//           if ($memorization->sura_from_id === $memorization->sura_to_id){
//               DB::table('daily_memorization_details')
//                   ->insert(
//                       ['id' => $memorization->id,
//                           'sura_id' => $memorization->sura_from_id,
//                           'aya_from' =>$memorization->aya_from,
//                           'aya_to' =>$memorization->aya_to,
//                           'created_at' =>$memorization->created_at,
//                           'updated_at' =>$memorization->updated_at]);
//           }else{
//               $total_aya = 0;
//               foreach ($suras as $sura){
//                   if ($sura->id === $memorization->sura_from_id){
//                       $total_aya = $sura->total_number_aya;
//                       break;
//                   }
//               }
//
//               DB::table('daily_memorization_details')
//                   ->insert(
//                       ['id' => $memorization->id,
//                           'sura_id' => $memorization->sura_from_id,
//                           'aya_from' =>$memorization->aya_from,
//                           'aya_to' =>$total_aya,
//                           'created_at' =>$memorization->created_at,
//                           'updated_at' =>$memorization->updated_at]);
//
//
//               for ($i=$suras[($memorization->sura_from_id)-1]->id;$i>$suras[($memorization->sura_to_id)]->id;$i--){
//                   DB::table('daily_memorization_details')
//                       ->insert(
//                           ['id' => $memorization->id,
//                               'sura_id' => $suras[$i]->id,
//                               'aya_from' =>1,
//                               'aya_to' =>$suras[$i]->total_number_aya,
//                               'created_at' =>$memorization->created_at,
//                               'updated_at' =>$memorization->updated_at]);
//               }
//
//               DB::table('daily_memorization_details')
//                   ->insert(
//                       ['id' => $memorization->id,
//                           'sura_id' => $memorization->sura_to_id,
//                           'aya_from' =>1,
//                           'aya_to' =>$memorization->aya_to,
//                           'created_at' =>$memorization->created_at,
//                           'updated_at' =>$memorization->updated_at]);
//           }
//       }

//        $students = DB::table('students')
//            ->select(['students.id', 'exams.quran_part_id', 'exams.mark as mark', 'exam_success_mark.mark as exam_success_mark'])
//            ->join('exams', function ($join) {
//                $join->on('students.id', '=', 'exams.student_id')
//                    ->on('exams.id', '=', DB::raw("(SELECT exams.id FROM exams
//                    INNER JOIN quran_parts ON exams.quran_part_id = quran_parts.id AND quran_parts.type = 'individual'
//                  WHERE student_id = students.id order by datetime desc LIMIT 1)"));
//            })
//            ->join('exam_success_mark', 'exams.exam_success_mark_id', '=', 'exam_success_mark.id')
//            ->get();
//
//        foreach ($students as $student) {
//            if ($student->mark >= $student->exam_success_mark) {
//                Student::query()->where('id', $student->id)->update(['current_part_id' => ($student->quran_part_id - 1)]);
//            } else {
//                Student::query()->where('id', $student->id)->update(['current_part_id' => $student->quran_part_id]);
//            }
//        }

//        dd($students);
        return view('livewire.activities', ['activities' => $this->all_Activity()]);
    }


    public function all_Activity()
    {
        return Activity::query()
            ->with(['students.user', 'activity_member.user', 'activity_type', 'teacher.user'])
            ->withCount(['students'])
            ->when(!empty($this->searchDateFrom) && !empty($this->searchDateTo), function ($q, $v) {
                $q->whereBetween(DB::raw('DATE(datetime)'), [$this->searchDateFrom, $this->searchDateTo]);
            })->search($this->search)
            ->when($this->current_role === 'محفظ', function ($q, $v) {
                $q->where('teacher_id', auth()->id());
            })->when($this->current_role === 'منشط', function ($q, $v) {
                $q->where('activity_member_id', auth()->id())
                    ->whereHas('students', function ($q) {
                        $q->when(!empty($this->selectedGradeId), function ($q, $v) {
                            $q->where('grade_id', '=', $this->selectedGradeId);
                        })->when(!empty($this->selectedTeacherId), function ($q, $v) {
                            $q->where('group_id', '=', $this->selectedTeacherId);
                        });
                    });
            })->when($this->current_role === 'أمير المركز' || $this->current_role === 'مشرف الأنشطة', function ($q, $v) {
                $q->whereHas('students', function ($q) {
                    $q->when(!empty($this->selectedGradeId), function ($q, $v) {
                        $q->where('grade_id', '=', $this->selectedGradeId);
                    })->when(!empty($this->selectedTeacherId), function ($q, $v) {
                        $q->where('group_id', '=', $this->selectedTeacherId);
                    });
                });
            })->when($this->current_role === 'مشرف', function ($q, $v) {
                $q->whereHas('students', function ($q) {
                    $q->where('grade_id', '=', Supervisor::whereId(auth()->id())->first()->grade_id)
                        ->when(!empty($this->selectedTeacherId), function ($q, $v) {
                            $q->where('group_id', '=', $this->selectedTeacherId);
                        });
                });
            })->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function submitSearch()
    {
        $this->all_Activity();
    }
}
