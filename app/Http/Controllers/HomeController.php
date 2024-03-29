<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\QuranPart;
use App\Models\SunnahPart;
use App\Models\Teacher;
use App\Models\User;
use App\Traits\NotificationTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class HomeController extends Controller
{
    use NotificationTrait;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $this->checkRoles();
        return view('dashboard', ['statistics' => $this->getStatistics()]);
    }

    public function getStatistics(): array
    {
        if (auth()->user()->current_role === User::ADMIN_ROLE) {
            return $this->getAdminStatistics();
        }

        if (auth()->user()->current_role === User::SUPERVISOR_ROLE) {
            return $this->getSupervisorStatistics();
        }

        if (auth()->user()->current_role === User::SPONSORSHIP_SUPERVISORS_ROLE) {
            return $this->getSponsorshipSupervisorStatistics();
        }

        if (auth()->user()->current_role === User::EXAMS_SUPERVISOR_ROLE) {
            return $this->getExamsSupervisorStatistics();
        }

        if (auth()->user()->current_role === User::TESTER_ROLE) {
            return $this->getTesterStatistics();
        }

        if (auth()->user()->current_role === User::TEACHER_ROLE) {
            return $this->getTeacherStatistics();
        }

        if (auth()->user()->current_role === User::OVERSIGHT_SUPERVISOR_ROLE) {
            return $this->getOversightSupervisorStatistics();
        }

        if (auth()->user()->current_role === User::OVERSIGHT_MEMBER_ROLE) {
            return $this->getOversightMemberStatistics();
        }

        if (auth()->user()->current_role === User::ACTIVITIES_SUPERVISOR_ROLE) {
            return $this->getActivitySupervisorStatistics();
        }

        if (auth()->user()->current_role === User::ACTIVITY_MEMBER_ROLE) {
            return $this->getActivityMemberStatistics();
        }

        return [];
    }

    public function getAdminStatistics(): array
    {
        $statistics = array();
        $statistics_count = DB::select("SELECT (SELECT COUNT(*) FROM students inner join users on users.id = students.id and users.gender ='male' inner join `groups` on groups.id = students.group_id and groups.teacher_id is not null) as students_male_count,
         (SELECT COUNT(*) FROM teachers inner join users on users.id = teachers.id and users.gender ='male' and users.status=1) as teachers_male_count,(SELECT COUNT(*) FROM `groups` inner join `grades` on grades.id = groups.grade_id and grades.section='male' where teacher_id is not null) as groups_male_count,
         (SELECT COUNT(*) FROM exams inner join exam_success_mark on exams.exam_success_mark_id = exam_success_mark.id
          and exams.mark >= exam_success_mark.mark) as exams_count,
         (SELECT COUNT(*) FROM students inner join users on users.id = students.id and users.gender ='female' inner join `groups` on groups.id = students.group_id and groups.teacher_id is not null) as students_female_count,
         (SELECT COUNT(*) FROM teachers inner join users on users.id = teachers.id and users.gender ='female' and users.status=1) as teachers_female_count,(SELECT COUNT(*) FROM `groups` inner join `grades` on grades.id = groups.grade_id and grades.section='female' where teacher_id is not null) as groups_female_count,
         (SELECT COUNT(*) FROM external_exams) as external_exams_count");

        $statistics[] = $statistics_count;

        $statistics_students = DB::table('students', 's')->select(DB::raw('u.name student_name'),
            DB::raw('u.identification_number student_identification_number'), DB::raw('g.name grade_name'),
            DB::raw('gro.name group_name'), DB::raw('tea_user.name teacher_name'), DB::raw('s.created_at created_at'))
            ->join(DB::raw('users u'), 's.id', '=', 'u.id')
            ->join(DB::raw('grades g'), 's.grade_id', '=', 'g.id')
            ->join('groups as gro', 's.group_id', '=', 'gro.id')
            ->join(DB::raw('users tea_user'), 'gro.teacher_id', '=', 'tea_user.id')
            ->orderByDesc(DB::raw('s.created_at'))->limit(10)->get()->toArray();

        $statistics[] = $statistics_students;

        $statistics_teachers = DB::table('teachers', 't')->select(DB::raw('u.name teacher_name'),
            DB::raw('u.identification_number teacher_identification_number'), DB::raw('u.phone teacher_phone')
            , DB::raw('g.name grade_name'), DB::raw('t.created_at'))
            ->join(DB::raw('users u'), 't.id', '=', 'u.id')
            ->join(DB::raw('grades g'), 't.grade_id', '=', 'g.id')
            ->orderByDesc(DB::raw('t.created_at'))->limit(10)->get()->toArray();

        $statistics[] = $statistics_teachers;

        $statistics_groups = DB::table('groups', 'g')->select(DB::raw('g.name group_name'),
            DB::raw('gra.name grade_name'), DB::raw('u.name teacher_name'),
            DB::raw('(SELECT COUNT(*) FROM students where students.group_id = g.id or students.group_sunnah_id = g.id) as students_count')
            , DB::raw('g.created_at'))
            ->join(DB::raw('users u'), 'g.teacher_id', '=', 'u.id')
            ->join(DB::raw('grades gra'), 'g.grade_id', '=', 'gra.id')
            ->orderByDesc(DB::raw('g.created_at'))->limit(10)->get()->toArray();

        $statistics[] = $statistics_groups;

        $statistics_exams = DB::table('exams', 'e')->select(DB::raw('u.name student_name')
            , DB::raw("GROUP_CONCAT(q.name,'-',q.description) quran_part_name"), DB::raw('e.mark mark'),
            DB::raw('tea_user.name teacher_name'), DB::raw('tes_user.name tester_name')
            , DB::raw('e.datetime datetime'), DB::raw('e.notes notes'), DB::raw('exam_mark.mark exam_success_mark'))
            ->join(DB::raw('users u'), 'e.student_id', '=', 'u.id')
            ->join(DB::raw('quran_parts q'), 'e.quran_part_id', '=', 'q.id')
            ->join(DB::raw('users tea_user'), 'e.teacher_id', '=', 'tea_user.id')
            ->join(DB::raw('users tes_user'), 'e.tester_id', '=', 'tes_user.id')
            ->join(DB::raw('exam_success_mark exam_mark'), 'e.exam_success_mark_id', '=', 'exam_mark.id')
            ->groupBy('student_name', 'mark', 'teacher_name', 'tester_name', 'datetime', 'notes', 'exam_success_mark')
            ->orderByDesc(DB::raw('e.datetime'))->limit(10)->get()->toArray();

        $statistics[] = $statistics_exams;

        return $statistics;
    }

    public function getSupervisorStatistics(): array
    {
        $statistics = array();
        $grade_id = auth()->user()->supervisor->grade_id ?? null;
        $statistics_count = DB::select("SELECT (SELECT COUNT(*) FROM students inner join `groups` on groups.id = students.group_id and groups.teacher_id is not null where students.grade_id = '$grade_id') as students_count,
         (SELECT COUNT(*) FROM teachers inner join users on users.id = teachers.id and users.status=1 where grade_id = '$grade_id') as teachers_count,(SELECT COUNT(*) FROM `groups` where grade_id = '$grade_id' and teacher_id is not null) as groups_count,
         (SELECT COUNT(*) FROM exams inner join exam_success_mark on exams.exam_success_mark_id = exam_success_mark.id
          and exams.mark >= exam_success_mark.mark inner join students on exams.student_id = students.id and students.grade_id = '$grade_id') as exams_count");

        $statistics[] = $statistics_count;

        $statistics_students = DB::table('students', 's')->select(DB::raw('u.name student_name'),
            DB::raw('u.identification_number student_identification_number'), DB::raw('g.name grade_name'),
            DB::raw('gro.name group_name'), DB::raw('tea_user.name teacher_name'), DB::raw('s.created_at created_at'))
            ->join(DB::raw('users u'), 's.id', '=', 'u.id')
            ->join(DB::raw('grades g'), 's.grade_id', '=', 'g.id')
            ->join('groups as gro', 's.group_id', '=', 'gro.id')
            ->join(DB::raw('users tea_user'), 'gro.teacher_id', '=', 'tea_user.id')
            ->where(DB::raw('s.grade_id'), '=', $grade_id)
            ->orderByDesc(DB::raw('s.created_at'))->limit(10)->get()->toArray();

        $statistics[] = $statistics_students;

        $statistics_teachers = DB::table('teachers', 't')->select(DB::raw('u.name teacher_name'),
            DB::raw('u.identification_number teacher_identification_number'), DB::raw('u.phone teacher_phone')
            , DB::raw('g.name grade_name'), DB::raw('t.created_at'))
            ->join(DB::raw('users u'), 't.id', '=', 'u.id')
            ->join(DB::raw('grades g'), 't.grade_id', '=', 'g.id')
            ->where(DB::raw('t.grade_id'), '=', $grade_id)
            ->orderByDesc(DB::raw('t.created_at'))->limit(10)->get()->toArray();

        $statistics[] = $statistics_teachers;

        $statistics_groups = DB::table('groups', 'g')->select(DB::raw('g.name group_name'),
            DB::raw('gra.name grade_name'), DB::raw('u.name teacher_name'),
            DB::raw('(SELECT COUNT(*) FROM students where students.group_id = g.id) as students_count')
            , DB::raw('g.created_at'))
            ->join(DB::raw('users u'), 'g.teacher_id', '=', 'u.id')
            ->join(DB::raw('grades gra'), 'g.grade_id', '=', 'gra.id')
            ->where(DB::raw('g.grade_id'), '=', $grade_id)
            ->orderByDesc(DB::raw('g.created_at'))->limit(10)->get()->toArray();

        $statistics[] = $statistics_groups;

        $statistics_exams = DB::table('exams', 'e')->select(DB::raw('u.name student_name')
            , DB::raw("GROUP_CONCAT(q.name,'-',q.description) quran_part_name"), DB::raw('e.mark mark'),
            DB::raw('tea_user.name teacher_name'), DB::raw('tes_user.name tester_name')
            , DB::raw('e.datetime datetime'), DB::raw('e.notes notes'), DB::raw('exam_mark.mark exam_success_mark'))
            ->join(DB::raw('users u'), 'e.student_id', '=', 'u.id')
            ->join(DB::raw('students s'), 'e.student_id', '=', 's.id')
            ->join(DB::raw('quran_parts q'), 'e.quran_part_id', '=', 'q.id')
            ->join(DB::raw('users tea_user'), 'e.teacher_id', '=', 'tea_user.id')
            ->join(DB::raw('users tes_user'), 'e.tester_id', '=', 'tes_user.id')
            ->join(DB::raw('exam_success_mark exam_mark'), 'e.exam_success_mark_id', '=', 'exam_mark.id')
            ->where(DB::raw('s.grade_id'), '=', $grade_id)
            ->groupBy('student_name', 'mark', 'teacher_name', 'tester_name', 'datetime', 'notes', 'exam_success_mark')
            ->orderByDesc(DB::raw('e.datetime'))->limit(10)->get()->toArray();

        $statistics[] = $statistics_exams;

        return $statistics;
    }

    public function getSponsorshipSupervisorStatistics(): array
    {
        $statistics = array();

        $groups_ids = DB::table('sponsorship_groups')
            ->select(['group_id'])
            ->whereIn('sponsorship_id', auth()->user()->sponsorships->pluck('id')->toArray())
            ->distinct()
            ->pluck('group_id')->toArray();

        $arr = implode(',',$groups_ids);
        dd(var_dump($groups_ids));

        $statistics_count = DB::select("SELECT (SELECT COUNT(*) FROM students where students.group_id in ($arr)) as students_count,
         (SELECT COUNT(*) FROM teachers inner join users on users.id = teachers.id and users.status=1 inner join groups on groups.teacher_id = teachers.id and groups.id in ($arr)) as teachers_count,
         (SELECT COUNT(*) FROM exams inner join exam_success_mark on exams.exam_success_mark_id = exam_success_mark.id
          and exams.mark >= exam_success_mark.mark inner join students on exams.student_id = students.id and students.group_id in ($arr)) as exams_count");

        $statistics_count['groups_count'] = count($groups_ids);
        $statistics[] = $statistics_count;

        return $statistics;
    }


    public function getExamsSupervisorStatistics(): array
    {
        $statistics = array();
        $month = Date('m');
        $year = Date('Y');
        $statistics_count = DB::select("SELECT (SELECT COUNT(*) FROM exam_orders) as exam_orders_count,
         (SELECT COUNT(*) FROM exams inner join exam_success_mark on exams.exam_success_mark_id = exam_success_mark.id
          and exams.mark >= exam_success_mark.mark where year(datetime) = $year And month(datetime) = $month) as month_exams_count,
         (SELECT COUNT(*) FROM sunnah_exams inner join exam_success_mark on sunnah_exams.exam_success_mark_id = exam_success_mark.id
          and sunnah_exams.mark >= exam_success_mark.mark where year(datetime) = $year And month(datetime) = $month) as month_sunnah_exams_count,
         (SELECT COUNT(*) FROM external_exams where year(date) = $year And month(date) = $month) as external_exams_count,
         (SELECT COUNT(*) FROM sunnah_external_exams where year(date) = $year And month(date) = $month) as external_sunnah_exams_count,
         (SELECT name FROM users WHERE id = (select teacher_id from exams inner join users on users.id = exams.teacher_id and users.gender='male' inner join exam_success_mark
         on exams.exam_success_mark_id = exam_success_mark.id and exams.mark >= exam_success_mark.mark where year(DATETIME) = $year And month(DATETIME) = $month
         group by teacher_id having count(exams.teacher_id) = (select max(total_exams) as highest_total
         from (select teacher_id, COUNT(exams.teacher_id) as total_exams from exams inner join users on users.id = exams.teacher_id and users.gender='male' inner join exam_success_mark
         on exams.exam_success_mark_id = exam_success_mark.id and exams.mark >= exam_success_mark.mark
         where year(DATETIME) = $year And month(DATETIME) = $month group by teacher_id) AS e) LIMIT 1)) as male_teacher_name_max,
         (SELECT name FROM users WHERE id = (select tester_id from exams inner join users on users.id = exams.tester_id and users.gender='male' where year(DATETIME) = $year
          And month(DATETIME) = $month group by tester_id having count(exams.tester_id) =
          (select max(total_exams) as highest_total from (select tester_id, COUNT(exams.tester_id) as total_exams
           from exams inner join users on users.id = exams.tester_id and users.gender='male' where year(DATETIME) = $year And month(DATETIME) = $month group by tester_id ) AS e ) LIMIT 1)) as male_tester_name_max,
           (SELECT name FROM users WHERE id = (select teacher_id from exams inner join users on users.id = exams.teacher_id and users.gender='female' inner join exam_success_mark
         on exams.exam_success_mark_id = exam_success_mark.id and exams.mark >= exam_success_mark.mark
         where year(DATETIME) = $year And month(DATETIME) = $month
         group by teacher_id having count(exams.teacher_id) = (select max(total_exams) as highest_total
         from (select teacher_id, COUNT(exams.teacher_id) as total_exams from exams inner join users on users.id = exams.teacher_id and users.gender='female' inner join exam_success_mark
         on exams.exam_success_mark_id = exam_success_mark.id and exams.mark >= exam_success_mark.mark
         where year(DATETIME) = $year And month(DATETIME) = $month group by teacher_id) AS e) LIMIT 1)) as female_teacher_name_max,
         (SELECT name FROM users WHERE id = (select tester_id from exams inner join users on users.id = exams.tester_id and users.gender='female' where year(DATETIME) = $year
          And month(DATETIME) = $month group by tester_id having count(exams.tester_id) =
          (select max(total_exams) as highest_total from (select tester_id, COUNT(exams.tester_id) as total_exams
           from exams inner join users on users.id = exams.tester_id and users.gender='female' where year(DATETIME) = $year And month(DATETIME) = $month group by tester_id ) AS e ) LIMIT 1)) as female_tester_name_max");

        $statistics[] = $statistics_count;

        $statistics_exam_orders = DB::table('exam_orders', 'e')->select(DB::raw('u.name student_name')
            , DB::raw("GROUP_CONCAT(q.name,'-',q.description) quran_part_name"),
            DB::raw("GROUP_CONCAT(sunnah_part.name,' (',sunnah_part.total_hadith_parts,') حديث') sunnah_part_name"),
            DB::raw('tea_user.name teacher_name'), DB::raw('tes_user.name tester_name')
            , DB::raw('e.status status'), DB::raw('e.datetime datetime')
            , DB::raw('e.notes notes'), DB::raw('e.created_at created_at')
            , DB::raw('e.type type'))
            ->join(DB::raw('users u'), 'e.student_id', '=', 'u.id')
            ->leftJoin('quran_parts as q', function ($join) {
                $join->on('q.id', '=', 'e.partable_id')
                    ->where('e.partable_type', '=', QuranPart::class);
            })->leftJoin('sunnah_parts as sunnah_part', function ($join) {
                $join->on('sunnah_part.id', '=', 'e.partable_id')
                    ->where('e.partable_type', '=', SunnahPart::class);
            })
            ->join(DB::raw('users tea_user'), 'e.teacher_id', '=', 'tea_user.id')
            ->leftJoin(DB::raw('users tes_user'), 'e.tester_id', '=', 'tes_user.id')
            ->groupBy('student_name', 'teacher_name', 'tester_name', 'datetime', 'status', 'type', 'created_at', 'notes')
            ->orderByDesc(DB::raw('e.created_at'))->limit(10)->get()->toArray();

        $statistics[] = $statistics_exam_orders;

        $statistics_exams = DB::table('exams', 'e')->select(DB::raw('u.name student_name')
            , DB::raw("GROUP_CONCAT(q.name,'-',q.description) quran_part_name"), DB::raw('e.mark mark'),
            DB::raw('tea_user.name teacher_name'), DB::raw('tes_user.name tester_name')
            , DB::raw('e.datetime datetime'), DB::raw('e.notes notes'), DB::raw('exam_mark.mark exam_success_mark'))
            ->join(DB::raw('users u'), 'e.student_id', '=', 'u.id')
            ->join(DB::raw('quran_parts q'), 'e.quran_part_id', '=', 'q.id')
            ->join(DB::raw('users tea_user'), 'e.teacher_id', '=', 'tea_user.id')
            ->join(DB::raw('users tes_user'), 'e.tester_id', '=', 'tes_user.id')
            ->join(DB::raw('exam_success_mark exam_mark'), 'e.exam_success_mark_id', '=', 'exam_mark.id')
            ->groupBy('student_name', 'mark', 'teacher_name', 'tester_name', 'datetime', 'notes', 'exam_success_mark')
            ->orderByDesc(DB::raw('e.datetime'))->limit(10)->get()->toArray();

        $statistics[] = $statistics_exams;

        $statistics_testers = DB::table('testers', 't')->select(DB::raw('u.name tester_name'),
            DB::raw('u.identification_number tester_identification_number'), DB::raw('u.phone tester_phone'), DB::raw('t.created_at'))
            ->join(DB::raw('users u'), 't.id', '=', 'u.id')
            ->orderByDesc(DB::raw('t.created_at'))->limit(10)->get()->toArray();

        $statistics[] = $statistics_testers;

        return $statistics;
    }


    public function getTesterStatistics(): array
    {
        $statistics = array();
        $month = Date('m');
        $year = Date('Y');
        $tester_id = auth()->id();
        $statistics_count = DB::select("SELECT (SELECT COUNT(*) FROM exam_orders where tester_id = $tester_id) as exam_orders_count,
         (SELECT COUNT(*) FROM exams inner join exam_success_mark on exams.exam_success_mark_id = exam_success_mark.id
          and exams.mark >= exam_success_mark.mark where year(datetime) = $year AND month(datetime) = $month AND tester_id = $tester_id) as month_exams_count,
         (SELECT COUNT(*) FROM exams inner join exam_success_mark on exams.exam_success_mark_id = exam_success_mark.id
          and exams.mark >= exam_success_mark.mark where year(datetime) = $year AND tester_id = $tester_id) as year_exams_count,
         (SELECT COUNT(*) FROM exams inner join exam_success_mark on exams.exam_success_mark_id = exam_success_mark.id
          and exams.mark >= exam_success_mark.mark where tester_id = $tester_id) as exams_count");

        $statistics[] = $statistics_count;

        $statistics_exam_orders = DB::table('exam_orders', 'e')->select(DB::raw('u.name student_name')
            , DB::raw("GROUP_CONCAT(q.name,'-',q.description) quran_part_name"),
            DB::raw("GROUP_CONCAT(sunnah_part.name,' (',sunnah_part.total_hadith_parts,') حديث') sunnah_part_name"),
            DB::raw('tea_user.name teacher_name'), DB::raw('tes_user.name tester_name')
            , DB::raw('e.status status'), DB::raw('e.datetime datetime')
            , DB::raw('e.notes notes'), DB::raw('e.created_at created_at')
            , DB::raw('e.type type'))
            ->join(DB::raw('users u'), 'e.student_id', '=', 'u.id')
            ->leftJoin('quran_parts as q', function ($join) {
                $join->on('q.id', '=', 'e.partable_id')
                    ->where('e.partable_type', '=', QuranPart::class);
            })->leftJoin('sunnah_parts as sunnah_part', function ($join) {
                $join->on('sunnah_part.id', '=', 'e.partable_id')
                    ->where('e.partable_type', '=', SunnahPart::class);
            })
            ->join(DB::raw('users tea_user'), 'e.teacher_id', '=', 'tea_user.id')
            ->leftJoin(DB::raw('users tes_user'), 'e.tester_id', '=', 'tes_user.id')
            ->where(DB::raw('e.tester_id'), '=', $tester_id)
            ->groupBy('student_name', 'teacher_name', 'tester_name', 'datetime', 'status', 'type', 'created_at', 'notes')
            ->orderByDesc(DB::raw('e.created_at'))->limit(10)->get()->toArray();

        $statistics[] = $statistics_exam_orders;

        $statistics_exams = DB::table('exams', 'e')->select(DB::raw('u.name student_name')
            , DB::raw("GROUP_CONCAT(q.name,'-',q.description) quran_part_name"), DB::raw('e.mark mark'),
            DB::raw('tea_user.name teacher_name'), DB::raw('tes_user.name tester_name')
            , DB::raw('e.datetime datetime'), DB::raw('e.notes notes'), DB::raw('exam_mark.mark exam_success_mark'))
            ->join(DB::raw('users u'), 'e.student_id', '=', 'u.id')
            ->join(DB::raw('quran_parts q'), 'e.quran_part_id', '=', 'q.id')
            ->join(DB::raw('users tea_user'), 'e.teacher_id', '=', 'tea_user.id')
            ->join(DB::raw('users tes_user'), 'e.tester_id', '=', 'tes_user.id')
            ->join(DB::raw('exam_success_mark exam_mark'), 'e.exam_success_mark_id', '=', 'exam_mark.id')
            ->where(DB::raw('e.tester_id'), '=', $tester_id)
            ->groupBy('student_name', 'mark', 'teacher_name', 'tester_name', 'datetime', 'notes', 'exam_success_mark')
            ->orderByDesc(DB::raw('e.datetime'))->limit(10)->get()->toArray();

        $statistics[] = $statistics_exams;

        return $statistics;
    }

    public function getTeacherStatistics(): array
    {
        $statistics = array();
        $month = Date('m');
        $year = Date('Y');
        $teacher_id = auth()->id();
        $group = Group::where('teacher_id', $teacher_id)->first();
        $group_id = $group->id ?? null;
        if ($group !== null && $group->type === Group::SUNNAH_TYPE) {
            $statistics_count = DB::select("SELECT (SELECT COUNT(*) FROM students where group_sunnah_id = '$group_id') as students_count,
         (SELECT COUNT(*) FROM sunnah_exams inner join exam_success_mark on sunnah_exams.exam_success_mark_id = exam_success_mark.id
          and sunnah_exams.mark >= exam_success_mark.mark inner join students on sunnah_exams.student_id = students.id and students.group_sunnah_id = '$group_id'
             where year(datetime) = $year AND month(datetime) = $month) as month_exams_count,
         (SELECT COUNT(*) FROM sunnah_exams inner join exam_success_mark on sunnah_exams.exam_success_mark_id = exam_success_mark.id
          and sunnah_exams.mark >= exam_success_mark.mark where teacher_id = $teacher_id and year(datetime) = $year) as year_exams_count,
         (SELECT COUNT(*) FROM activities where teacher_id = $teacher_id) as activities_count");

            $statistics_students = DB::table('students', 's')->select(DB::raw('u.name student_name'),
                DB::raw('u.identification_number student_identification_number'), DB::raw('g.name grade_name'),
                DB::raw('gro.name group_name'), DB::raw('tea_user.name teacher_name'), DB::raw('s.created_at created_at'))
                ->join(DB::raw('users u'), 's.id', '=', 'u.id')
                ->join(DB::raw('grades g'), 's.grade_id', '=', 'g.id')
                ->join(DB::raw('groups gro'), 's.group_sunnah_id', '=', 'gro.id')
                ->join(DB::raw('users tea_user'), 'gro.teacher_id', '=', 'tea_user.id')
                ->where(DB::raw('s.group_sunnah_id'), '=', $group_id)
                ->orderByDesc(DB::raw('s.created_at'))->limit(10)->get()->toArray();

            $statistics_exams = DB::table('sunnah_exams', 'e')->select(DB::raw('u.name student_name')
                , DB::raw("GROUP_CONCAT(sunnah_part.name,' (',sunnah_part.total_hadith_parts,') حديث') sunnah_part_name"), DB::raw('e.mark mark'),
                DB::raw('tea_user.name teacher_name'), DB::raw('tes_user.name tester_name')
                , DB::raw('e.datetime datetime'), DB::raw('e.notes notes'), DB::raw('exam_mark.mark exam_success_mark'))
                ->join(DB::raw('users u'), 'e.student_id', '=', 'u.id')
                ->join(DB::raw('students s'), 'e.student_id', '=', 's.id')
                ->join(DB::raw('sunnah_parts sunnah_part'), 'e.sunnah_part_id', '=', 'sunnah_part.id')
                ->join(DB::raw('users tea_user'), 'e.teacher_id', '=', 'tea_user.id')
                ->join(DB::raw('users tes_user'), 'e.tester_id', '=', 'tes_user.id')
                ->join(DB::raw('exam_success_mark exam_mark'), 'e.exam_success_mark_id', '=', 'exam_mark.id')
                ->where(DB::raw('s.group_sunnah_id'), '=', $group_id)
                ->groupBy('student_name', 'mark', 'teacher_name', 'tester_name', 'datetime', 'notes', 'exam_success_mark')
                ->orderByDesc(DB::raw('e.datetime'))->limit(10)->get()->toArray();
        } else {
            $statistics_count = DB::select("SELECT (SELECT COUNT(*) FROM students where group_id = '$group_id') as students_count,
         (SELECT COUNT(*) FROM exams inner join exam_success_mark on exams.exam_success_mark_id = exam_success_mark.id
          and exams.mark >= exam_success_mark.mark inner join students on exams.student_id = students.id and students.group_id = '$group_id'
             where year(datetime) = $year AND month(datetime) = $month) as month_exams_count,
         (SELECT COUNT(*) FROM exams inner join exam_success_mark on exams.exam_success_mark_id = exam_success_mark.id
          and exams.mark >= exam_success_mark.mark where teacher_id = '$teacher_id' and year(datetime) = $year) as year_exams_count,
         (SELECT COUNT(*) FROM activities where teacher_id = $teacher_id) as activities_count");

            $statistics_students = DB::table('students', 's')->select(DB::raw('u.name student_name'),
                DB::raw('u.identification_number student_identification_number'), DB::raw('g.name grade_name'),
                DB::raw('gro.name group_name'), DB::raw('tea_user.name teacher_name'), DB::raw('s.created_at created_at'))
                ->join(DB::raw('users u'), 's.id', '=', 'u.id')
                ->join(DB::raw('grades g'), 's.grade_id', '=', 'g.id')
                ->join('groups as gro', 's.group_id', '=', 'gro.id')
                ->join(DB::raw('users tea_user'), 'gro.teacher_id', '=', 'tea_user.id')
                ->where(DB::raw('s.group_id'), '=', $group_id)
                ->orderByDesc(DB::raw('s.created_at'))->limit(10)->get()->toArray();

            $statistics_exams = DB::table('exams', 'e')->select(DB::raw('u.name student_name')
                , DB::raw("GROUP_CONCAT(q.name,'-',q.description) quran_part_name"), DB::raw('e.mark mark'),
                DB::raw('tea_user.name teacher_name'), DB::raw('tes_user.name tester_name')
                , DB::raw('e.datetime datetime'), DB::raw('e.notes notes'), DB::raw('exam_mark.mark exam_success_mark'))
                ->join(DB::raw('users u'), 'e.student_id', '=', 'u.id')
                ->join(DB::raw('students s'), 'e.student_id', '=', 's.id')
                ->join(DB::raw('quran_parts q'), 'e.quran_part_id', '=', 'q.id')
                ->join(DB::raw('users tea_user'), 'e.teacher_id', '=', 'tea_user.id')
                ->join(DB::raw('users tes_user'), 'e.tester_id', '=', 'tes_user.id')
                ->join(DB::raw('exam_success_mark exam_mark'), 'e.exam_success_mark_id', '=', 'exam_mark.id')
                ->where(DB::raw('s.group_id'), '=', $group_id)
                ->groupBy('student_name', 'mark', 'teacher_name', 'tester_name', 'datetime', 'notes', 'exam_success_mark')
                ->orderByDesc(DB::raw('e.datetime'))->limit(10)->get()->toArray();
        }
        $statistics[] = $statistics_count;


        $statistics[] = $statistics_students;

        $statistics[] = $statistics_exams;

        $statistics_activities = DB::table('activities', 'a')->select(DB::raw('act_type.name activity_type_name'),
            DB::raw('tea_user.name teacher_name'),
            DB::raw('(SELECT COUNT(*) FROM activity_students where activity_id = a.id) as students_activity_count'),
            DB::raw('a.datetime datetime'), DB::raw('act_mem_user.name activity_member_name')
            , DB::raw('a.notes notes'))
            ->join(DB::raw('activity_types act_type'), 'a.activity_type_id', '=', 'act_type.id')
            ->join(DB::raw('users tea_user'), 'a.teacher_id', '=', 'tea_user.id')
            ->join(DB::raw('users act_mem_user'), 'a.activity_member_id', '=', 'act_mem_user.id')
            ->where(DB::raw('a.teacher_id'), '=', $teacher_id)
            ->orderByDesc(DB::raw('a.updated_at'))->limit(10)->get()->toArray();

        $statistics[] = $statistics_activities;

        return $statistics;
    }

    public function getOversightSupervisorStatistics(): array
    {
        $statistics = array();
        $month = Date('m');
        $year = Date('Y');
        $statistics_count = DB::select("SELECT (SELECT COUNT(*) FROM visit_orders) as visit_orders_count,
         (SELECT COUNT(*) FROM visits where year(datetime) = $year And month(datetime) = $month) as month_visits_count,
         (SELECT COUNT(*) FROM visits where year(datetime) = $year) as year_visits_count,
            (SELECT COUNT(*) FROM oversight_members) as oversight_members_count");

        $statistics[] = $statistics_count;

        $statistics_visit_orders = DB::table('visit_orders', 'v')->select(DB::raw('u.name oversight_member_name')
            , DB::raw('v.hostable_type hostable_type'), DB::raw('v.status status')
            , DB::raw('v.datetime datetime'), DB::raw('v.created_at created_at'))
            ->join(DB::raw('users u'), 'v.oversight_member_id', '=', 'u.id')
            ->groupBy('oversight_member_name', 'hostable_type', 'status', 'datetime', 'created_at')
            ->orderByDesc(DB::raw('v.created_at'))->limit(10)->get()->toArray();

        $statistics[] = $statistics_visit_orders;

        $statistics_visit = DB::table('visits', 'v')->select(DB::raw('u.name oversight_member_name')
            , DB::raw('v.hostable_type hostable_type'), DB::raw('v.status status')
            , DB::raw('v.datetime datetime'), DB::raw('v.created_at created_at'))
            ->join(DB::raw('users u'), 'v.oversight_member_id', '=', 'u.id')
            ->groupBy('oversight_member_name', 'hostable_type', 'status', 'datetime', 'created_at')
            ->orderByDesc(DB::raw('v.created_at'))->limit(10)->get()->toArray();

        $statistics[] = $statistics_visit;

        $statistics_testers = DB::table('oversight_members', 'oversight_mem')->select(DB::raw('u.name oversight_member_name'),
            DB::raw('u.identification_number identification_number'), DB::raw('u.phone oversight_member_phone'), DB::raw('oversight_mem.created_at'))
            ->join(DB::raw('users u'), 'oversight_mem.id', '=', 'u.id')
            ->orderByDesc(DB::raw('oversight_mem.created_at'))->limit(10)->get()->toArray();

        $statistics[] = $statistics_testers;

        return $statistics;
    }

    public function getOversightMemberStatistics(): array
    {
        $statistics = array();
        $month = Date('m');
        $year = Date('Y');
        $oversight_member_id = auth()->id();
        $statistics_count = DB::select("SELECT (SELECT COUNT(*) FROM visit_orders where oversight_member_id = $oversight_member_id) as visit_orders_count,
         (SELECT COUNT(*) FROM visits where year(datetime) = $year AND month(datetime) = $month AND oversight_member_id = $oversight_member_id) as month_visits_count,
         (SELECT COUNT(*) FROM visits where year(datetime) = $year AND oversight_member_id = $oversight_member_id) as year_visits_count,
         (SELECT COUNT(*) FROM visits where oversight_member_id = $oversight_member_id) as visits_count");

        $statistics[] = $statistics_count;

        $statistics_visit_orders = DB::table('visit_orders', 'v')->select(DB::raw('u.name oversight_member_name')
            , DB::raw('v.hostable_type hostable_type'), DB::raw('v.status status')
            , DB::raw('v.datetime datetime'), DB::raw('v.created_at created_at'))
            ->join(DB::raw('users u'), 'v.oversight_member_id', '=', 'u.id')
            ->where(DB::raw('v.oversight_member_id'), '=', $oversight_member_id)
            ->groupBy('oversight_member_name', 'hostable_type', 'status', 'datetime', 'created_at')
            ->orderByDesc(DB::raw('v.created_at'))->limit(10)->get()->toArray();

        $statistics[] = $statistics_visit_orders;

        $statistics_visit = DB::table('visits', 'v')->select(DB::raw('u.name oversight_member_name')
            , DB::raw('v.hostable_type hostable_type'), DB::raw('v.status status')
            , DB::raw('v.datetime datetime'), DB::raw('v.created_at created_at'))
            ->join(DB::raw('users u'), 'v.oversight_member_id', '=', 'u.id')
            ->where(DB::raw('v.oversight_member_id'), '=', $oversight_member_id)
            ->groupBy('oversight_member_name', 'hostable_type', 'status', 'datetime', 'created_at')
            ->orderByDesc(DB::raw('v.created_at'))->limit(10)->get()->toArray();
        $statistics[] = $statistics_visit;

        return $statistics;
    }

    public function getActivitySupervisorStatistics(): array
    {
        $statistics = array();
        $month = Date('m');
        $year = Date('Y');
        $statistics_count = DB::select("SELECT (SELECT COUNT(*) FROM activity_orders) as activity_orders_count,
         (SELECT COUNT(*) FROM activities where year(datetime) = $year And month(datetime) = $month) as month_activities_count,
         (SELECT COUNT(*) FROM activities where year(datetime) = $year) as year_activities_count,
            (SELECT COUNT(*) FROM activity_members) as activity_members_count");

        $statistics[] = $statistics_count;

        $statistics_activity_orders = DB::table('activity_orders', 'a')->select(DB::raw('act_typ.name activity_name')
            , DB::raw('(SELECT COUNT(*) FROM activity_order_students where activity_order_id = a.id) as students_activity_count')
            , DB::raw('a.datetime datetime'), DB::raw('tea_user.name activity_teacher_name'),
            DB::raw('act_mem.name activity_member_name'), DB::raw('a.status status')
            , DB::raw('a.created_at created_at'))
            ->join('users as tea_user', 'a.teacher_id', '=', 'tea_user.id')
            ->leftJoin('users as act_mem', 'a.activity_member_id', '=', 'act_mem.id')
            ->join('activity_types as act_typ', 'a.activity_type_id', '=', 'act_typ.id')
            ->groupBy('activity_name', 'students_activity_count', 'datetime', 'activity_teacher_name', 'activity_member_name', 'status', 'created_at')
            ->orderByDesc(DB::raw('a.created_at'))->limit(10)->get()->toArray();

        $statistics[] = $statistics_activity_orders;

        $statistics_activities = DB::table('activities', 'a')->select(DB::raw('act_typ.name activity_name')
            , DB::raw('(SELECT COUNT(*) FROM activity_students where activity_id = a.id) as students_activity_count')
            , DB::raw('a.datetime datetime'), DB::raw('tea_user.name activity_teacher_name'),
            DB::raw('act_mem.name activity_member_name'), DB::raw('a.created_at created_at'))
            ->join(DB::raw('users tea_user'), 'a.teacher_id', '=', 'tea_user.id')
            ->join(DB::raw('users act_mem'), 'a.activity_member_id', '=', 'act_mem.id')
            ->join(DB::raw('activity_types act_typ'), 'a.activity_type_id', '=', 'act_typ.id')
            ->groupBy('activity_name', 'students_activity_count', 'datetime', 'activity_teacher_name', 'activity_member_name', 'created_at')
            ->orderByDesc(DB::raw('a.created_at'))->limit(10)->get()->toArray();

        $statistics[] = $statistics_activities;

        $statistics_activity_members = DB::table('activity_members', 'activity_mem')->select(DB::raw('u.name activity_member_name'),
            DB::raw('u.identification_number identification_number'), DB::raw('u.phone activity_member_phone'), DB::raw('activity_mem.created_at'))
            ->join(DB::raw('users u'), 'activity_mem.id', '=', 'u.id')
            ->orderByDesc(DB::raw('activity_mem.created_at'))->limit(10)->get()->toArray();

        $statistics[] = $statistics_activity_members;

        return $statistics;
    }

    public function getActivityMemberStatistics(): array
    {
        $statistics = array();
        $month = Date('m');
        $year = Date('Y');
        $activity_member_id = auth()->id();
        $statistics_count = DB::select("SELECT (SELECT COUNT(*) FROM activity_orders where activity_member_id = $activity_member_id) as activity_orders_count,
         (SELECT COUNT(*) FROM activities where year(datetime) = $year And month(datetime) = $month And activity_member_id = $activity_member_id) as month_activities_count,
         (SELECT COUNT(*) FROM activities where year(datetime) = $year And activity_member_id = $activity_member_id) as year_activities_count,
            (SELECT COUNT(*) FROM activities where activity_member_id = $activity_member_id) as activities_count");

        $statistics[] = $statistics_count;

        $statistics_activity_orders = DB::table('activity_orders', 'a')->select(DB::raw('act_typ.name activity_name')
            , DB::raw('(SELECT COUNT(*) FROM activity_order_students where activity_order_id = a.id) as students_activity_count')
            , DB::raw('a.datetime datetime'), DB::raw('tea_user.name activity_teacher_name'),
            DB::raw('act_mem.name activity_member_name'), DB::raw('a.status status')
            , DB::raw('a.created_at created_at'))
            ->join(DB::raw('users tea_user'), 'a.teacher_id', '=', 'tea_user.id')
            ->join(DB::raw('users act_mem'), 'a.activity_member_id', '=', 'act_mem.id')
            ->join(DB::raw('activity_types act_typ'), 'a.activity_type_id', '=', 'act_typ.id')
            ->where(DB::raw('a.activity_member_id'), '=', $activity_member_id)
            ->groupBy('activity_name', 'students_activity_count', 'datetime', 'activity_teacher_name', 'activity_member_name', 'status', 'created_at')
            ->orderByDesc(DB::raw('a.created_at'))->limit(10)->get()->toArray();

        $statistics[] = $statistics_activity_orders;

        $statistics_activities = DB::table('activities', 'a')->select(DB::raw('act_typ.name activity_name')
            , DB::raw('(SELECT COUNT(*) FROM activity_students where activity_id = a.id) as students_activity_count')
            , DB::raw('a.datetime datetime'), DB::raw('tea_user.name activity_teacher_name'),
            DB::raw('act_mem.name activity_member_name'), DB::raw('a.created_at created_at'))
            ->join(DB::raw('users tea_user'), 'a.teacher_id', '=', 'tea_user.id')
            ->join(DB::raw('users act_mem'), 'a.activity_member_id', '=', 'act_mem.id')
            ->join(DB::raw('activity_types act_typ'), 'a.activity_type_id', '=', 'act_typ.id')
            ->where(DB::raw('a.activity_member_id'), '=', $activity_member_id)
            ->groupBy('activity_name', 'students_activity_count', 'datetime', 'activity_teacher_name', 'activity_member_name', 'created_at')
            ->orderByDesc(DB::raw('a.created_at'))->limit(10)->get()->toArray();

        $statistics[] = $statistics_activities;
        return $statistics;
    }


    private function checkRoles()
    {
        if (auth()->user()->current_role === null || empty(auth()->user()->current_role)) {
            if (count(auth()->user()->roles) > 0) {
                if (count(auth()->user()->roles) === 1) {
                    auth()->user()->update(['current_role' => auth()->user()->roles[0]->name]);
                } else {
                    auth()->user()->update(['current_role' => auth()->user()->roles[1]->name]);
                }
            }
        }
    }

    public function switchAccountUser(Request $request)
    {
        if ($request->current_role) {
            if (Role::where('name', $request->current_role)->first()) {
                auth()->user()->update(['current_role' => $request->current_role]);
                toastSuccess('', 'تمت عملية تبديل الحساب إلى ' . $request->current_role . ' بنجاح.');
            } else {
                toastError('', 'عذرا فشلت عملية تبديل الحساب إلى ' . $request->current_role . ' بسبب عدم وجود دور بهذا الاسم.');
            }
        }
        return redirect()->back();
    }
}
