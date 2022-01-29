<?php

namespace App\Console\Commands;

use App\Models\Student;
use App\Models\StudentBlock;
use App\Models\StudentWarning;
use Illuminate\Console\Command;

class CheckStudentStatusCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CheckStudentStatus:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command Check Student Status';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $studentWarnings = [];
        $studentBlocks = [];
        $readable = ["isReadableTeacher" => false, "isReadableSupervisor" => false,];

        $attendance_warnings = Student::query()
            ->select('id')
            ->whereHas('attendance', function ($query) {
                $query->whereMonth('attendance_date', Date('m'))
                    ->whereYear('attendance_date', Date('Y'))
                    ->where('attendance_status', 0);
            })
            ->whereDoesntHave('student_warning', function ($query) {
                $query->orderByDesc('updated_at')
                    ->whereNull('warning_expiry_date');
            })
            ->with('student_warning', function ($query) {
                $query->whereMonth('warning_expiry_date', Date('m'))
                    ->whereYear('warning_expiry_date', Date('Y'))
                    ->orderByDesc('updated_at');
            })
            ->withCount(['attendance' => function ($query) {
                $query->whereMonth('attendance_date', Date('m'))
                    ->whereYear('attendance_date', Date('Y'))
                    ->where('attendance_status', 0);
            }])
            ->having('attendance_count', '>=', 3)
            ->get()->toArray();


        foreach ($attendance_warnings as $key => $value) {
            if ($value['student_warning'] != null) {
                $student = Student::query()
                    ->select('id')
                    ->where('id', $value['id'])
                    ->whereHas('attendance', function ($query) use ($value) {
                        $query->whereDate('attendance_date', '>=', $value['student_warning']['warning_expiry_date'])
                            ->where('attendance_status', 0);
                    })
                    ->withCount(['attendance' => function ($query) use ($value) {
                        $query->whereDate('attendance_date', '>=', $value['student_warning']['warning_expiry_date'])
                            ->where('attendance_status', 0);
                    }])
                    ->having('attendance_count', '>=', 3)
                    ->first();
                if ($student != null) {
                    array_push($studentWarnings, ['student_id' => $value['id'], 'readable' => $readable,]);
                }
            } else {
                array_push($studentWarnings, ['student_id' => $value['id'], 'readable' => $readable,]);
            }
        }

        foreach ($studentWarnings as $key => $value) {
            StudentWarning::create($value);
        }
/////////////////////////////////////////////////////////////
        $attendance_blocks = Student::query()
            ->select('id')
            ->whereHas('attendance', function ($query) {
                $query->whereMonth('attendance_date', Date('m'))
                    ->whereYear('attendance_date', Date('Y'))
                    ->where('attendance_status', 0);
            })
            ->whereDoesntHave('student_block', function ($query) {
                $query->orderByDesc('updated_at')
                    ->whereNull('block_expiry_date');
            })
            ->whereHas('student_warning', function ($query) {
                $query->orderByDesc('updated_at')
                    ->whereNull('warning_expiry_date');
            })
            ->with('student_block', function ($query) {
                $query->whereMonth('block_expiry_date', Date('m'))
                    ->whereYear('block_expiry_date', Date('Y'))
                    ->orderByDesc('updated_at');
            })
            ->withCount(['attendance' => function ($query) {
                $query->whereMonth('attendance_date', Date('m'))
                    ->whereYear('attendance_date', Date('Y'))
                    ->where('attendance_status', 0);
            }])
            ->having('attendance_count', '>=', 4)
            ->get()->toArray();


        foreach ($attendance_blocks as $key => $value) {
            if ($value['student_block'] != null) {
                $student = Student::query()
                    ->select('id')
                    ->where('id', $value['id'])
                    ->whereHas('attendance', function ($query) use ($value) {
                        $query->whereDate('attendance_date', '>=', $value['student_block']['block_expiry_date'])
                            ->where('attendance_status', 0);
                    })
                    ->withCount(['attendance' => function ($query) use ($value) {
                        $query->whereDate('attendance_date', '>=', $value['student_block']['block_expiry_date'])
                            ->where('attendance_status', 0);
                    }])
                    ->having('attendance_count', '>=', 4)
                    ->first();
                if ($student != null) {
                    array_push($studentBlocks, ['student_id' => $value['id'], 'readable' => $readable,]);
                }
            } else {
                array_push($studentBlocks, ['student_id' => $value['id'], 'readable' => $readable,]);
            }
        }


        foreach ($studentBlocks as $key => $value) {
            StudentBlock::create($value);
        }
    }
}
