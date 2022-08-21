<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\User;
use App\Traits\NotificationTrait;
use Illuminate\Http\Request;
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
        if (auth()->user()->current_role == User::ADMIN_ROLE) {
            return $this->getAdminStatistics();
        } else if (auth()->user()->current_role == User::SUPERVISOR_ROLE) {
            return $this->getSupervisorStatistics();
        } else if (auth()->user()->current_role == User::EXAMS_SUPERVISOR_ROLE) {
            return $this->getExamsSupervisorStatistics();
        } else if (auth()->user()->current_role == User::TESTER_ROLE) {
            return $this->getTesterStatistics();
        } else if (auth()->user()->current_role == User::TEACHER_ROLE) {
            return dd($this->getTeacherStatistics());
        }
        return [];
    }

    public function getAdminStatistics(): array
    {
        $statistics = array();
        $statistics_count = DB::select("SELECT (SELECT COUNT(*) FROM students) as students_count,
         (SELECT COUNT(*) FROM teachers) as teachers_count,(SELECT COUNT(*) FROM groups) as groups_count,
         (SELECT COUNT(*) FROM exams) as exams_count");

        array_push($statistics, $statistics_count);

        $statistics_students = DB::table('students', 's')->select(DB::raw('u.name student_name'),
            DB::raw('u.identification_number student_identification_number'), DB::raw('g.name grade_name'),
            DB::raw('gro.name group_name'), DB::raw('tea_user.name teacher_name'), DB::raw('s.created_at created_at'))
            ->join(DB::raw('users u'), 's.id', '=', 'u.id')
            ->join(DB::raw('grades g'), 's.grade_id', '=', 'g.id')
            ->join(DB::raw('groups gro'), 's.group_id', '=', 'gro.id')
            ->join(DB::raw('users tea_user'), 'gro.teacher_id', '=', 'tea_user.id')
            ->orderByDesc(DB::raw('s.created_at'))->limit(10)->get()->toArray();

        array_push($statistics, $statistics_students);

        $statistics_teachers = DB::table('teachers', 't')->select(DB::raw('u.name teacher_name'),
            DB::raw('u.identification_number teacher_identification_number'), DB::raw('u.phone teacher_phone')
            , DB::raw('g.name grade_name'), DB::raw('t.created_at'))
            ->join(DB::raw('users u'), 't.id', '=', 'u.id')
            ->join(DB::raw('grades g'), 't.grade_id', '=', 'g.id')
            ->orderByDesc(DB::raw('t.created_at'))->limit(10)->get()->toArray();

        array_push($statistics, $statistics_teachers);

        $statistics_groups = DB::table('groups', 'g')->select(DB::raw('g.name group_name'),
            DB::raw('gra.name grade_name'), DB::raw('u.name teacher_name'),
            DB::raw('(SELECT COUNT(*) FROM students where students.group_id = g.id) as students_count')
            , DB::raw('g.created_at'))
            ->join(DB::raw('users u'), 'g.teacher_id', '=', 'u.id')
            ->join(DB::raw('grades gra'), 'g.grade_id', '=', 'gra.id')
            ->orderByDesc(DB::raw('g.created_at'))->limit(10)->get()->toArray();

        array_push($statistics, $statistics_groups);

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

        array_push($statistics, $statistics_exams);

        return $statistics;
    }

    public function getSupervisorStatistics(): array
    {
        $statistics = array();
        $grade_id = auth()->user()->supervisor->grade_id ?? null;
        $statistics_count = DB::select("SELECT (SELECT COUNT(*) FROM students where grade_id = '$grade_id') as students_count,
         (SELECT COUNT(*) FROM teachers where grade_id = '$grade_id') as teachers_count,(SELECT COUNT(*) FROM groups where grade_id = '$grade_id') as groups_count,
         (SELECT COUNT(*) FROM exams inner join students on exams.student_id = students.id and students.grade_id = '$grade_id') as exams_count");

        array_push($statistics, $statistics_count);

        $statistics_students = DB::table('students', 's')->select(DB::raw('u.name student_name'),
            DB::raw('u.identification_number student_identification_number'), DB::raw('g.name grade_name'),
            DB::raw('gro.name group_name'), DB::raw('tea_user.name teacher_name'), DB::raw('s.created_at created_at'))
            ->join(DB::raw('users u'), 's.id', '=', 'u.id')
            ->join(DB::raw('grades g'), 's.grade_id', '=', 'g.id')
            ->join(DB::raw('groups gro'), 's.group_id', '=', 'gro.id')
            ->join(DB::raw('users tea_user'), 'gro.teacher_id', '=', 'tea_user.id')
            ->where(DB::raw('s.grade_id'), '=', $grade_id)
            ->orderByDesc(DB::raw('s.created_at'))->limit(10)->get()->toArray();

        array_push($statistics, $statistics_students);

        $statistics_teachers = DB::table('teachers', 't')->select(DB::raw('u.name teacher_name'),
            DB::raw('u.identification_number teacher_identification_number'), DB::raw('u.phone teacher_phone')
            , DB::raw('g.name grade_name'), DB::raw('t.created_at'))
            ->join(DB::raw('users u'), 't.id', '=', 'u.id')
            ->join(DB::raw('grades g'), 't.grade_id', '=', 'g.id')
            ->where(DB::raw('t.grade_id'), '=', $grade_id)
            ->orderByDesc(DB::raw('t.created_at'))->limit(10)->get()->toArray();

        array_push($statistics, $statistics_teachers);

        $statistics_groups = DB::table('groups', 'g')->select(DB::raw('g.name group_name'),
            DB::raw('gra.name grade_name'), DB::raw('u.name teacher_name'),
            DB::raw('(SELECT COUNT(*) FROM students where students.group_id = g.id) as students_count')
            , DB::raw('g.created_at'))
            ->join(DB::raw('users u'), 'g.teacher_id', '=', 'u.id')
            ->join(DB::raw('grades gra'), 'g.grade_id', '=', 'gra.id')
            ->where(DB::raw('g.grade_id'), '=', $grade_id)
            ->orderByDesc(DB::raw('g.created_at'))->limit(10)->get()->toArray();

        array_push($statistics, $statistics_groups);

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

        array_push($statistics, $statistics_exams);

        return $statistics;
    }

    public function getExamsSupervisorStatistics(): array
    {
        $statistics = array();
        $month = Date('m');
        $year = Date('Y');
        $statistics_count = DB::select("SELECT (SELECT COUNT(*) FROM exam_orders) as exam_orders_count,
         (SELECT COUNT(*) FROM exams where year(datetime) = $year And month(datetime) = $month) as month_exams_count,
         (SELECT COUNT(*) FROM exams where year(datetime) = $year) as year_exams_count,
            (SELECT COUNT(*) FROM testers) as testers_count");

        array_push($statistics, $statistics_count);

        $statistics_exam_orders = DB::table('exam_orders', 'e')->select(DB::raw('u.name student_name')
            , DB::raw("GROUP_CONCAT(q.name,'-',q.description) quran_part_name"),
            DB::raw('tea_user.name teacher_name'), DB::raw('tes_user.name tester_name')
            , DB::raw('e.status status'), DB::raw('e.datetime datetime')
            , DB::raw('e.notes notes'), DB::raw('e.created_at created_at')
            , DB::raw('e.type type'))
            ->join(DB::raw('users u'), 'e.student_id', '=', 'u.id')
            ->join(DB::raw('quran_parts q'), 'e.quran_part_id', '=', 'q.id')
            ->join(DB::raw('users tea_user'), 'e.teacher_id', '=', 'tea_user.id')
            ->leftJoin(DB::raw('users tes_user'), 'e.tester_id', '=', 'tes_user.id')
            ->groupBy('student_name', 'teacher_name', 'tester_name', 'datetime', 'status','type', 'created_at', 'notes')
            ->orderByDesc(DB::raw('e.created_at'))->limit(10)->get()->toArray();

        array_push($statistics, $statistics_exam_orders);

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

        array_push($statistics, $statistics_exams);

        $statistics_testers = DB::table('testers', 't')->select(DB::raw('u.name tester_name'),
            DB::raw('u.identification_number tester_identification_number'), DB::raw('u.phone tester_phone'), DB::raw('t.created_at'))
            ->join(DB::raw('users u'), 't.id', '=', 'u.id')
            ->orderByDesc(DB::raw('t.created_at'))->limit(10)->get()->toArray();

        array_push($statistics, $statistics_testers);

        return $statistics;
    }


    public function getTesterStatistics(): array
    {
        $statistics = array();
        $month = Date('m');
        $year = Date('Y');
        $tester_id = auth()->id();
        $statistics_count = DB::select("SELECT (SELECT COUNT(*) FROM exam_orders where tester_id = $tester_id) as exam_orders_count,
         (SELECT COUNT(*) FROM exams where year(datetime) = $year AND month(datetime) = $month AND tester_id = $tester_id) as month_exams_count,
         (SELECT COUNT(*) FROM exams where year(datetime) = $year AND tester_id = $tester_id) as year_exams_count,
         (SELECT COUNT(*) FROM exams where tester_id = $tester_id) as exams_count");

        array_push($statistics, $statistics_count);

        $statistics_exam_orders = DB::table('exam_orders', 'e')->select(DB::raw('u.name student_name')
            , DB::raw("GROUP_CONCAT(q.name,'-',q.description) quran_part_name"),
            DB::raw('tea_user.name teacher_name'), DB::raw('tes_user.name tester_name')
            , DB::raw('e.status status'), DB::raw('e.datetime datetime')
            , DB::raw('e.notes notes'), DB::raw('e.created_at created_at')
            , DB::raw('e.type type'))
            ->join(DB::raw('users u'), 'e.student_id', '=', 'u.id')
            ->join(DB::raw('quran_parts q'), 'e.quran_part_id', '=', 'q.id')
            ->join(DB::raw('users tea_user'), 'e.teacher_id', '=', 'tea_user.id')
            ->leftJoin(DB::raw('users tes_user'), 'e.tester_id', '=', 'tes_user.id')
            ->where(DB::raw('e.tester_id'),'=',$tester_id)
            ->groupBy('student_name', 'teacher_name', 'tester_name', 'datetime', 'status','type', 'created_at', 'notes')
            ->orderByDesc(DB::raw('e.created_at'))->limit(10)->get()->toArray();

        array_push($statistics, $statistics_exam_orders);

        $statistics_exams = DB::table('exams', 'e')->select(DB::raw('u.name student_name')
            , DB::raw("GROUP_CONCAT(q.name,'-',q.description) quran_part_name"), DB::raw('e.mark mark'),
            DB::raw('tea_user.name teacher_name'), DB::raw('tes_user.name tester_name')
            , DB::raw('e.datetime datetime'), DB::raw('e.notes notes'), DB::raw('exam_mark.mark exam_success_mark'))
            ->join(DB::raw('users u'), 'e.student_id', '=', 'u.id')
            ->join(DB::raw('quran_parts q'), 'e.quran_part_id', '=', 'q.id')
            ->join(DB::raw('users tea_user'), 'e.teacher_id', '=', 'tea_user.id')
            ->join(DB::raw('users tes_user'), 'e.tester_id', '=', 'tes_user.id')
            ->join(DB::raw('exam_success_mark exam_mark'), 'e.exam_success_mark_id', '=', 'exam_mark.id')
            ->where(DB::raw('e.tester_id'),'=',$tester_id)
            ->groupBy('student_name', 'mark', 'teacher_name', 'tester_name', 'datetime', 'notes', 'exam_success_mark')
            ->orderByDesc(DB::raw('e.datetime'))->limit(10)->get()->toArray();

        array_push($statistics, $statistics_exams);

        return $statistics;
    }

    public function getTeacherStatistics(): array
    {
        $statistics = array();
        $month = Date('m');
        $year = Date('Y');
        $group_id = Teacher::where('id',auth()->id())->first()->group->id ?? null;
        $teacher_id = auth()->id() ?? null;
        $statistics_count = DB::select("SELECT (SELECT COUNT(*) FROM students where group_id = '$group_id') as students_count,
         (SELECT COUNT(*) FROM exams inner join students on exams.student_id = students.id and students.group_id = '$group_id'
             where year(datetime) = $year AND month(datetime) = $month) as month_exams_count,
         (SELECT COUNT(*) FROM exams inner join students on exams.student_id = students.id and students.group_id = '$group_id'
             where year(datetime) = $year) as year_exams_count,
         (SELECT COUNT(*) FROM activities where teacher_id = $teacher_id) as activities_count");

        array_push($statistics, $statistics_count);

        $statistics_students = DB::table('students', 's')->select(DB::raw('u.name student_name'),
            DB::raw('u.identification_number student_identification_number'), DB::raw('g.name grade_name'),
            DB::raw('gro.name group_name'), DB::raw('tea_user.name teacher_name'), DB::raw('s.created_at created_at'))
            ->join(DB::raw('users u'), 's.id', '=', 'u.id')
            ->join(DB::raw('grades g'), 's.grade_id', '=', 'g.id')
            ->join(DB::raw('groups gro'), 's.group_id', '=', 'gro.id')
            ->join(DB::raw('users tea_user'), 'gro.teacher_id', '=', 'tea_user.id')
            ->where(DB::raw('s.group_id'), '=', $group_id)
            ->orderByDesc(DB::raw('s.created_at'))->limit(10)->get()->toArray();

        array_push($statistics, $statistics_students);


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

        array_push($statistics, $statistics_exams);

        $statistics_activities = DB::table('activities', 'a')->select(DB::raw('act_type.name activity_type_name'),
            DB::raw('tea_user.name teacher_name'),
           DB::raw('(SELECT COUNT(*) FROM activity_students where activity_id = a.id) as students_activity_count'),
        DB::raw('a.datetime datetime'),DB::raw('act_mem_user.name activity_member_name')
            ,DB::raw('a.notes notes'))
            ->join(DB::raw('activity_types act_type'), 'a.activity_type_id', '=', 'act_type.id')
            ->join(DB::raw('users tea_user'), 'a.teacher_id', '=', 'tea_user.id')
            ->join(DB::raw('users act_mem_user'), 'a.activity_member_id', '=', 'act_mem_user.id')
            ->where(DB::raw('a.teacher_id'), '=', $teacher_id)
            ->orderByDesc(DB::raw('a.updated_at'))->limit(10)->get()->toArray();

        array_push($statistics, $statistics_activities);

        return $statistics;
    }


    private function checkRoles()
    {
        if (auth()->user()->current_role == null || empty(auth()->user()->current_role)) {
            if (count(auth()->user()->roles) > 0) {
                if (count(auth()->user()->roles) == 1) {
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
