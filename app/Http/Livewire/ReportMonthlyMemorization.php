<?php

namespace App\Http\Livewire;

use App\Exports\MonthlyMemorizationExport;
use App\Models\Grade;
use App\Models\Group;
use App\Models\Supervisor;
use App\Models\Teacher;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Excel;

class ReportMonthlyMemorization extends HomeComponent
{

    public $groups = [], $grades = [], $students = [], $years = [], $months = [],$reports = [];

    public $selectedGradeId, $selectedTeacherId, $selectedYear, $selectedMonth, $group_id, $grade_id;

    public function render()
    {
        return view('livewire.report-monthly-memorization', ['reports_monthly_memorization' => $this->getReports()]);
    }

    protected $listeners = [
        'getTeachersByGradeId' => 'getTeachersByGradeId',
        'getReports' => 'getReports',
    ];


    public function mount()
    {
        $this->current_role = auth()->user()->current_role;
        for ($i = 0; $i < 2; $i++) {
            array_push($this->years, (intval(date('Y')) + $i));
        }
        for ($i = 1; $i <= 12; $i++) {
            array_push($this->months, $i);
        }
        $this->all_Grades();
        if ($this->current_role == 'محفظ') {
            $this->selectedGradeId = Teacher::where('id', auth()->id())->first()->grade_id;
            $this->selectedTeacherId = Group::where('teacher_id', auth()->id())->first()->id ?? null;
        }
        $this->selectedMonth = intval(Date('m'));
        $this->selectedYear = Date('Y');
    }

    public function all_Grades()
    {
        if ($this->current_role == 'مشرف') {
            $this->grade_id = Supervisor::where('id', auth()->id())->first()->grade_id;
            $this->grades = Grade::query()->where('id', $this->grade_id)->get();
        } else if ($this->current_role == 'محفظ') {
            $this->group_id = Group::where('teacher_id', auth()->id())->first()->id ?? null;
            $this->grades = Grade::query()->where('id', Teacher::where('id', auth()->id())->first()->grade_id)->get();
        } else if ($this->current_role == 'أمير المركز') {
            $this->grades = Grade::all();
        }
    }

    public function getTeachersByGradeId()
    {
        $this->reset('groups', 'selectedTeacherId','reports');

        if ($this->current_role == 'مشرف') {
            if ($this->selectedGradeId) {
                $this->groups = Group::query()->with(['teacher.user'])->where('grade_id', $this->selectedGradeId)->get();
            }
        } else if ($this->current_role == 'أمير المركز') {
            if ($this->selectedGradeId) {
                $this->groups = Group::query()->with(['teacher.user'])->where('grade_id', $this->selectedGradeId)->get();
            }
        } elseif ($this->current_role == 'محفظ') {
            $this->groups = Group::query()->where('teacher_id', auth()->id())->get();
        }
    }

    public function getReports()
    {

        if (!empty($this->selectedTeacherId) && !empty($this->selectedYear) && !empty($this->selectedMonth)) {

            $query = "SELECT users.name student_name ,sura_start.name sura_start,b.aya_from,sura_end.name sura_end,c.aya_to,

(SELECT SUM(number_pages) FROM students_daily_memorization
WHERE student_id = s.id and month(datetime) = '$this->selectedMonth' and year(datetime) = '$this->selectedYear' and type = 'memorize') AS 'number_memorize_pages',

(SELECT SUM(number_pages) FROM students_daily_memorization
WHERE student_id = s.id and month(datetime) = '$this->selectedMonth' and year(datetime) = '$this->selectedYear' and type = 'review') AS 'number_review_pages',

(select count(id) from student_attendances WHERE s.id = student_id and month(datetime) = '$this->selectedMonth' and year(datetime) = '$this->selectedYear' AND status = 'absence') AS 'attendance_count',
quran_part_count.total_preservation_parts,GROUP_CONCAT(DISTINCT parts_exams_individual.description,'  ',parts_exams_individual.name ORDER BY  parts_exams_individual.name ASC SEPARATOR ' - ') AS exams_individual,
GROUP_CONCAT(DISTINCT parts_exams_deserved.description,'  ',parts_exams_deserved.name ORDER BY  parts_exams_deserved.name ASC SEPARATOR '-') AS exams_deserved

FROM students s JOIN users ON s.id = users.id

LEFT JOIN students_daily_memorization b ON b.student_id = s.id
AND b.id = (SELECT id FROM students_daily_memorization  WHERE students_daily_memorization.student_id = s.id
And type ='memorize' AND YEAR(datetime) = '$this->selectedYear'
AND MONTH(datetime) = '$this->selectedMonth' ORDER BY datetime ASC LIMIT 1)
JOIN quran_suras sura_start ON b.sura_from_id = sura_start.id

LEFT JOIN students_daily_memorization c ON c.student_id = s.id
AND c.id = (SELECT id FROM students_daily_memorization  WHERE student_id = s.id
And type ='memorize' AND YEAR(datetime) = '$this->selectedYear'
AND MONTH(datetime) = '$this->selectedMonth' ORDER BY datetime DESC LIMIT 1)
JOIN quran_suras sura_end ON c.sura_to_id = sura_end.id

LEFT JOIN exams e_count ON s.id = e_count.student_id
AND e_count.id = (SELECT exams.id FROM exams JOIN quran_parts ON quran_part_id = quran_parts.id AND quran_parts.type = 'individual'
JOIN exam_success_mark ON exam_success_mark_id = exam_success_mark.id AND exams.mark >= exam_success_mark.mark
WHERE student_id = s.id AND YEAR(DATETIME) <= '$this->selectedYear' AND MONTH(DATETIME) <= '$this->selectedMonth' ORDER BY datetime DESC LIMIT 1)
LEFT JOIN quran_parts quran_part_count ON e_count.quran_part_id = quran_part_count.id


LEFT JOIN exams exams_individual ON s.id = exams_individual.student_id
LEFT JOIN quran_parts parts_exams_individual ON exams_individual.quran_part_id = parts_exams_individual.id AND parts_exams_individual.type = 'individual'
LEFT JOIN exam_success_mark mark_individual ON exams_individual.exam_success_mark_id = mark_individual.id AND exams_individual.mark >= mark_individual.mark


LEFT JOIN exams exams_deserved ON s.id = exams_deserved.student_id
LEFT JOIN quran_parts parts_exams_deserved ON exams_deserved.quran_part_id = parts_exams_deserved.id AND parts_exams_deserved.type = 'deserved'
LEFT JOIN exam_success_mark mark_deserved ON exams_deserved.exam_success_mark_id = mark_deserved.id AND exams_deserved.mark >= mark_deserved.mark


WHERE YEAR(exams_individual.datetime) = '$this->selectedYear' AND MONTH(exams_individual.datetime) = '$this->selectedMonth'
AND YEAR(exams_deserved.datetime) = '$this->selectedYear' AND MONTH(exams_deserved.datetime) = '$this->selectedMonth'


AND s.group_id = '$this->selectedTeacherId'

GROUP BY users.name,sura_start.name,b.aya_from,sura_end.name,c.aya_to,quran_part_count.total_preservation_parts";

           return $this->reports =  DB::select($query);
        }
        return [];
    }

    public function export()
    {
        if (!empty($this->selectedTeacherId) && !empty($this->selectedYear) && !empty($this->selectedMonth)) {
            $teacher_name = Group::with('teacher.user:id,name')->where('id', $this->selectedTeacherId)->first()->teacher->user->name;
            return (new MonthlyMemorizationExport($this->reports, $this->selectedMonth, $teacher_name == null ? '' : $teacher_name))->download('Report on the month of ' . $this->selectedMonth . '-' . $this->selectedYear . ' for the teacher ' . $teacher_name . ' group' . '.xlsx', Excel::XLSX);

        }
        $this->dispatchBrowserEvent('alert',
            ['type' => 'error', 'message' => 'عذرا يجب تحديد جميع الفلاتر!']);
        return;
    }
}
