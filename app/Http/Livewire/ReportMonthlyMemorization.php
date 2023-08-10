<?php

namespace App\Http\Livewire;

use App\Exports\MonthlyMemorizationExport;
use App\Exports\MonthlySunnahMemorizationExport;
use App\Models\Grade;
use App\Models\Group;
use App\Models\StudentDailyMemorization;
use App\Models\Supervisor;
use App\Models\Teacher;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Excel;

class ReportMonthlyMemorization extends HomeComponent
{

    public $groups = [], $grades = [], $students = [], $years = [], $months = [], $reports = [];

    public $selectedGradeId, $selectedTeacherId, $selectedYear,
        $selectedMonth, $group_type;

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
            $this->years[] = (intval(date('Y')) + $i);
        }
        for ($i = 1; $i <= 12; $i++) {
            $this->months[] = $i;
        }

        $this->selectedMonth = intval(Date('m'));
        $this->selectedYear = Date('Y');
        $this->all_Grades();
        $this->getTeachersByGradeId();
    }

    public function all_Grades()
    {
        $this->reset('groups', 'selectedTeacherId', 'reports');

        if ($this->current_role === User::SUPERVISOR_ROLE) {
            $grade_id = Supervisor::where('id', auth()->id())->first()->grade_id;
            $this->grades = Grade::query()->where('id', $grade_id)->get();
            $this->selectedGradeId = $grade_id;
        } else if ($this->current_role === User::TEACHER_ROLE) {
            $grade_id = Teacher::where('id', auth()->id())->first()->grade_id;
            $this->grades = Grade::query()->where('id', $grade_id)->get();
            $this->selectedGradeId = $grade_id;
        } else if ($this->current_role === User::ADMIN_ROLE || User::SPONSORSHIP_SUPERVISORS_ROLE) {
            $this->grades = Grade::all();
        }
    }

    public function getTeachersByGradeId()
    {
        $this->reset('groups', 'selectedTeacherId', 'reports');

        if ($this->current_role === User::SUPERVISOR_ROLE) {
            if ($this->selectedGradeId) {
                $this->groups = Group::query()->with(['teacher.user'])->where('grade_id', $this->selectedGradeId)->get();
            }
        } else if ($this->current_role === User::ADMIN_ROLE) {
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
        } elseif ($this->current_role === User::TEACHER_ROLE) {
            $this->groups = Group::query()->where('teacher_id', auth()->id())->get();
            $group = Group::where('teacher_id', auth()->id())->first();
            $this->selectedTeacherId = $group->id ?? null;
            $this->group_type = $group->type ?? null;
        }
    }

    public function updatedSelectedTeacherId($id)
    {
        if ($id !== null) {
            $this->group_type = Group::where('id', $id)->first()->type ?? null;
        }
    }

    public function getReports()
    {
        if ($this->selectedTeacherId !== null && $this->selectedYear !== null && $this->selectedMonth !== null && $this->group_type !== null) {
            if ($this->group_type === Group::QURAN_TYPE) {
                return $this->getReportsQuran();
            }

            if ($this->group_type === Group::SUNNAH_TYPE) {
                return $this->getReportsSunnah();
            }
        }
        return [];
    }

    public function getReportsQuran()
    {
        $date = $this->selectedYear.'-'.$this->selectedMonth.'-'.'31';
        $query = "SELECT users.name student_name,
(select name from daily_memorization_details inner join quran_suras
on daily_memorization_details.sura_id = quran_suras.id where daily_memorization_details.id = start_daily_memorization.id
order by sura_id desc limit 1) as sura_start,
(select aya_from from daily_memorization_details where daily_memorization_details.id = start_daily_memorization.id
order by sura_id desc limit 1) as aya_from,
(select name from daily_memorization_details inner join quran_suras
on sura_id = quran_suras.id where daily_memorization_details.id = end_daily_memorization.id
order by daily_memorization_details.sura_id asc limit 1) as sura_end,
(select aya_to from daily_memorization_details where daily_memorization_details.id = end_daily_memorization.id
order by sura_id asc limit 1) as aya_to,
(SELECT SUM(number_pages) FROM students_daily_memorization
WHERE student_id = s.id and month(datetime) = '$this->selectedMonth' and year(datetime) = '$this->selectedYear' and type = 'memorize') AS 'number_memorize_pages',

(SELECT SUM(number_pages) FROM students_daily_memorization
WHERE student_id = s.id and month(datetime) = '$this->selectedMonth' and year(datetime) = '$this->selectedYear' and type = 'review') AS 'number_review_pages',

(SELECT SUM(number_pages) FROM students_daily_memorization
WHERE student_id = s.id and month(datetime) = '$this->selectedMonth' and year(datetime) = '$this->selectedYear' and type = 'cumulative-review') AS 'number_cumulative_review_pages',

(select count(id) from student_attendances WHERE s.id = student_id and month(datetime) = '$this->selectedMonth' and year(datetime) = '$this->selectedYear' and status IN ('presence', 'late')) AS 'presence_count',
(select count(id) from student_attendances WHERE s.id = student_id and month(datetime) = '$this->selectedMonth' and year(datetime) = '$this->selectedYear' and status IN ('absence', 'authorized')) AS 'attendance_count',
(select total_preservation_parts from exams JOIN quran_parts ON quran_part_id = quran_parts.id AND quran_parts.type = 'individual'
JOIN exam_success_mark ON exam_success_mark_id = exam_success_mark.id AND exams.mark >= exam_success_mark.mark WHERE student_id = s.id
AND date(exams.datetime) <= '$date' ORDER BY exams.datetime DESC LIMIT 1) As total_preservation_parts,
GROUP_CONCAT(DISTINCT IF(mark_individual.id IS NOT NULL,parts_exams_individual.description,null) ,'  ',IF(mark_individual.id IS NOT NULL,parts_exams_individual.name,null) ORDER BY  parts_exams_individual.name ASC SEPARATOR '-') AS exams_individual,
GROUP_CONCAT(DISTINCT IF(mark_deserved.id IS NOT NULL, parts_exams_deserved.description,null),'  ',IF(mark_deserved.id IS NOT NULL, parts_exams_deserved.name,null) ORDER BY  parts_exams_deserved.name ASC SEPARATOR '-') AS exams_deserved

FROM students s JOIN users ON s.id = users.id

LEFT JOIN students_daily_memorization start_daily_memorization ON s.id = start_daily_memorization.student_id
AND start_daily_memorization.id = (SELECT id FROM students_daily_memorization  WHERE students_daily_memorization.student_id = s.id
And type ='memorize' AND YEAR(datetime) = '$this->selectedYear'
AND MONTH(datetime) = '$this->selectedMonth' ORDER BY datetime ASC LIMIT 1)

LEFT JOIN students_daily_memorization end_daily_memorization ON s.id = end_daily_memorization.student_id
AND end_daily_memorization.id = (SELECT id FROM students_daily_memorization  WHERE student_id = s.id
And type ='memorize' AND YEAR(datetime) = '$this->selectedYear'
AND MONTH(datetime) = '$this->selectedMonth' ORDER BY datetime DESC LIMIT 1)

LEFT JOIN exams exams_individual ON s.id = exams_individual.student_id AND YEAR(exams_individual.datetime) = '$this->selectedYear' AND MONTH(exams_individual.datetime) = '$this->selectedMonth'
LEFT JOIN exam_success_mark mark_individual ON exams_individual.exam_success_mark_id = mark_individual.id AND exams_individual.mark >= mark_individual.mark
LEFT JOIN quran_parts parts_exams_individual ON exams_individual.quran_part_id = parts_exams_individual.id AND parts_exams_individual.type = 'individual'


LEFT JOIN exams exams_deserved ON s.id = exams_deserved.student_id AND YEAR(exams_deserved.datetime) = '$this->selectedYear' AND MONTH(exams_deserved.datetime) = '$this->selectedMonth'
LEFT JOIN exam_success_mark mark_deserved ON exams_deserved.exam_success_mark_id = mark_deserved.id AND exams_deserved.mark >= mark_deserved.mark
LEFT JOIN quran_parts parts_exams_deserved ON exams_deserved.quran_part_id = parts_exams_deserved.id AND parts_exams_deserved.type = 'deserved'

WHERE s.group_id = '$this->selectedTeacherId'


GROUP BY student_name,sura_start,aya_from,sura_end,aya_to,total_preservation_parts";

        return $this->reports = DB::select($query);
    }

    public function getReportsSunnah()
    {
        return $this->reports = DB::table('students')
            ->select([DB::raw('users.name student_name'), DB::raw('sunnah_books.name book_name'),
                DB::raw('start_daily_memorization.hadith_from memorize_hadith_from'), DB::raw('daily_memorization.hadith_to memorize_hadith_to'),
                DB::raw('start_daily_review.hadith_from review_hadith_from'), DB::raw('daily_review.hadith_to review_hadith_to'),
                DB::raw("GROUP_CONCAT(DISTINCT IF(exam_success_mark.id IS NOT NULL,'اختبار (لجنة المركز)',null),' ',IF(exam_success_mark.id IS NOT NULL,sunnah_parts.total_hadith_parts,null),IF(exam_success_mark.id IS NOT NULL,' حديث',null),IF(exam_success_mark.id IS NOT NULL,IF(sunnah_external_exams.id is not null, ' + اختبار (لجنة دار القرآن)', ''),null)) sunnah_part_name"),
                DB::raw("(select count(id) from student_sunnah_attendances WHERE students.id = student_id and month(datetime) = '$this->selectedMonth' and year(datetime) = '$this->selectedYear' AND status = 'presence') AS 'presence_days_count'"),
                DB::raw("(select count(id) from student_sunnah_attendances WHERE students.id = student_id and month(datetime) = '$this->selectedMonth' and year(datetime) = '$this->selectedYear' AND status = 'absence') AS 'absence_days_count'")])
            ->join('users', 'students.id', '=', 'users.id')
            ->leftJoin('students_sunnah_daily_memorization as daily_memorization', function ($join) {
                $join->on('students.id', '=', 'daily_memorization.student_id')
                    ->on('daily_memorization.id', '=', DB::raw("(SELECT id FROM students_sunnah_daily_memorization WHERE student_id = students.id
                   And type ='memorize' AND YEAR(datetime) = '$this->selectedYear' AND MONTH(datetime) = '$this->selectedMonth' ORDER BY datetime DESC LIMIT 1)"));
            })
            ->leftJoin('students_sunnah_daily_memorization as start_daily_memorization', function ($join) {
                $join->on('students.id', '=', 'start_daily_memorization.student_id')
                    ->on('start_daily_memorization.id', '=', DB::raw("(SELECT id FROM students_sunnah_daily_memorization WHERE student_id = students.id
                   And type ='memorize' AND YEAR(datetime) = '$this->selectedYear' AND MONTH(datetime) = '$this->selectedMonth' ORDER BY datetime ASC LIMIT 1)"));
            })
            ->leftJoin('sunnah_books', 'daily_memorization.book_id', '=', 'sunnah_books.id')
            ->leftJoin('students_sunnah_daily_memorization as daily_review', function ($join) {
                $join->on('students.id', '=', 'daily_review.student_id')
                    ->on('daily_review.id', '=', DB::raw("(SELECT id FROM students_sunnah_daily_memorization WHERE student_id = students.id
                   And type ='review' AND YEAR(datetime) = '$this->selectedYear' AND MONTH(datetime) = '$this->selectedMonth' ORDER BY datetime DESC LIMIT 1)"));
            })
            ->leftJoin('students_sunnah_daily_memorization as start_daily_review', function ($join) {
                $join->on('students.id', '=', 'start_daily_review.student_id')
                    ->on('start_daily_review.id', '=', DB::raw("(SELECT id FROM students_sunnah_daily_memorization WHERE student_id = students.id
                   And type ='review' AND YEAR(datetime) = '$this->selectedYear' AND MONTH(datetime) = '$this->selectedMonth' ORDER BY datetime ASC LIMIT 1)"));
            })
            ->leftJoin('sunnah_exams', function ($join) {
                $join->on('students.id', '=', 'sunnah_exams.student_id')
                    ->on(DB::raw('year(sunnah_exams.datetime)'), '=', DB::raw($this->selectedYear))
                    ->on(DB::raw('month(sunnah_exams.datetime)'), '=', DB::raw($this->selectedMonth));
            })
            ->leftJoin('sunnah_external_exams', 'sunnah_exams.id', '=', 'sunnah_external_exams.id')
            ->leftJoin('sunnah_parts', 'sunnah_exams.sunnah_part_id', '=', 'sunnah_parts.id')
            ->leftJoin('exam_success_mark', function ($join) {
                $join->on('sunnah_exams.exam_success_mark_id', '=', 'exam_success_mark.id')
                    ->on('sunnah_exams.mark', '>=', DB::raw('exam_success_mark.mark'));
            })
            ->where('students.group_sunnah_id', '=', $this->selectedTeacherId)
            ->groupBy(['student_name', 'book_name', 'memorize_hadith_from', 'memorize_hadith_to',
                'review_hadith_from', 'review_hadith_to', 'presence_days_count', 'absence_days_count'])
            ->get();
    }


    public function export()
    {
        if (!empty($this->selectedTeacherId) && !empty($this->selectedYear) && !empty($this->selectedMonth)) {
            $teacher_name = Group::with('teacher.user:id,name')->where('id', $this->selectedTeacherId)->first()->teacher->user->name;
            if ($this->group_type !== null) {
                if ($this->group_type === Group::QURAN_TYPE) {
                    return (new MonthlyMemorizationExport($this->reports, $this->selectedMonth, $teacher_name === null ? '' : $teacher_name))->download('Report on the month of ' . $this->selectedMonth . '-' . $this->selectedYear . ' for the teacher ' . $teacher_name . ' group' . '.xlsx', Excel::XLSX);
                }

                if ($this->group_type === Group::SUNNAH_TYPE) {
                    return (new MonthlySunnahMemorizationExport($this->reports, $this->selectedMonth, $teacher_name === null ? '' : $teacher_name))->download('Report on the month of ' . $this->selectedMonth . '-' . $this->selectedYear . ' for the teacher ' . $teacher_name . ' group' . '.xlsx', Excel::XLSX);
                }
            }
        }

        $this->dispatchBrowserEvent('alert',
            ['type' => 'error', 'message' => 'عذرا يجب تحديد جميع الفلاتر!']);
        return;
    }
}
