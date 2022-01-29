<?php

namespace App\Http\Livewire;

use App\Models\DailyPreservationType;
use App\Models\Grade;
use App\Models\Group;
use App\Models\LowerSupervisor;
use App\Models\Student;
use App\Models\StudentDailyPreservation;
use App\Models\Supervisor;
use Livewire\Component;
use Livewire\WithPagination;

class ReportDailyPreservation extends Component
{
    use WithPagination;

    public $successMessage = '';

    public $catchError, $groups, $grades, $students, $types;
    public $sortBy = 'id', $sortDirection = 'desc', $perPage = 10, $search = '';

    public $searchGradeId, $searchGroupId, $searchStudentId, $searchReportTypeId, $searchDateFrom, $searchDateTo;
    protected $paginationTheme = 'bootstrap';

    public function render()
    {
        $this->all_Groups();
        $this->all_Students();
        return view('livewire.report-daily-preservation', ['reports_daily_preservation' => $this->all_Reports_Daily_Preservation(),]);
    }

    public function mount()
    {
        $this->searchDateFrom = date('Y-m-d');
        $this->searchDateTo = date('Y-m-d');
        $this->searchReportTypeId = 1;
        $this->all_Grades();
        $this->all_Report_Types();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function all_Report_Types()
    {
        $this->types = DailyPreservationType::all();
    }

    public function all_Grades()
    {
        if (auth()->user()->current_role == 'مشرف') {
            $this->searchGradeId = Supervisor::where('id', auth()->id())->first()->grade_id;
        } else if (auth()->user()->current_role == 'اداري') {
            $this->searchGradeId = LowerSupervisor::where('id', auth()->id())->first()->grade_id;
        } else {
            $this->grades = Grade::all();
        }
    }

    public function all_Groups()
    {
        if (auth()->user()->current_role == 'مشرف' || auth()->user()->current_role == 'اداري') {
            if ($this->searchGradeId) {
                $this->groups = Group::query()->where('grade_id', $this->searchGradeId)->get();
            }
        } else if (auth()->user()->current_role == 'أمير المركز') {
            if ($this->searchGradeId) {
                $this->groups = Group::query()->where('grade_id', $this->searchGradeId)->get();
            }
        } elseif (auth()->user()->current_role == 'محفظ') {
            $this->searchGroupId = Group::query()->where('teacher_id', auth()->id())->first()->id;
        }
    }

    public function all_Students()
    {
        if (auth()->user()->current_role == 'أمير المركز') {
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

    public function all_Reports_Daily_Preservation()
    {
        if (auth()->user()->current_role == 'محفظ') {
            if (empty($this->searchStudentId)) {
                if ($this->searchReportTypeId == null) {
                    if ($this->searchDateFrom != null && $this->searchDateTo != null) {
                        return StudentDailyPreservation::query()
                            ->search($this->search)
                            ->whereBetween('daily_preservation_date', [$this->searchDateFrom, $this->searchDateTo])
                            ->whereHas('student', function ($q) {
                                return $q->where('group_id', '=', Group::where('teacher_id', auth()->id())->first()->id);
                            })
                            ->orderBy($this->sortBy, $this->sortDirection)
                            ->paginate($this->perPage);
                    } else {
                        return StudentDailyPreservation::query()
                            ->search($this->search)
                            ->whereHas('student', function ($q) {
                                return $q->where('group_id', '=', Group::where('teacher_id', auth()->id())->first()->id);
                            })
                            ->orderBy($this->sortBy, $this->sortDirection)
                            ->paginate($this->perPage);
                    }
                } else {
                    if ($this->searchDateFrom != null && $this->searchDateTo != null) {
                        return StudentDailyPreservation::query()
                            ->search($this->search)
                            ->where('type', $this->searchReportTypeId)
                            ->whereBetween('daily_preservation_date', [$this->searchDateFrom, $this->searchDateTo])
                            ->whereHas('student', function ($q) {
                                return $q->where('group_id', '=', Group::where('teacher_id', auth()->id())->first()->id);
                            })
                            ->orderBy($this->sortBy, $this->sortDirection)
                            ->paginate($this->perPage);
                    } else {
                        return StudentDailyPreservation::query()
                            ->search($this->search)
                            ->where('type', $this->searchReportTypeId)
                            ->whereHas('student', function ($q) {
                                return $q->where('group_id', '=', Group::where('teacher_id', auth()->id())->first()->id);
                            })
                            ->orderBy($this->sortBy, $this->sortDirection)
                            ->paginate($this->perPage);
                    }
                }
            } else {
                if ($this->searchReportTypeId == null) {
                    if ($this->searchDateFrom != null && $this->searchDateTo != null) {
                        return StudentDailyPreservation::query()
                            ->search($this->search)
                            ->whereBetween('daily_preservation_date', [$this->searchDateFrom, $this->searchDateTo])
                            ->whereHas('student', function ($q) {
                                return $q->where('group_id', '=', $this->searchGroupId)
                                    ->where('id', '=', $this->searchStudentId);
                            })
                            ->where('type', $this->searchReportTypeId)
                            ->orderBy($this->sortBy, $this->sortDirection)
                            ->paginate($this->perPage);
                    } else {
                        return StudentDailyPreservation::query()
                            ->search($this->search)
                            ->whereHas('student', function ($q) {
                                return $q->where('group_id', '=', $this->searchGroupId)
                                    ->where('id', '=', $this->searchStudentId);
                            })
                            ->orderBy($this->sortBy, $this->sortDirection)
                            ->paginate($this->perPage);
                    }
                } else {
                    if ($this->searchDateFrom != null && $this->searchDateTo != null) {
                        return StudentDailyPreservation::query()
                            ->search($this->search)
                            ->where('type', $this->searchReportTypeId)
                            ->whereBetween('daily_preservation_date', [$this->searchDateFrom, $this->searchDateTo])
                            ->whereHas('student', function ($q) {
                                return $q->where('group_id', '=', $this->searchGroupId)
                                    ->where('id', '=', $this->searchStudentId);
                            })
                            ->where('type', $this->searchReportTypeId)
                            ->orderBy($this->sortBy, $this->sortDirection)
                            ->paginate($this->perPage);
                    } else {
                        return StudentDailyPreservation::query()
                            ->search($this->search)
                            ->where('type', $this->searchReportTypeId)
                            ->whereHas('student', function ($q) {
                                return $q->where('group_id', '=', $this->searchGroupId)
                                    ->where('id', '=', $this->searchStudentId);
                            })
                            ->where('type', $this->searchReportTypeId)
                            ->orderBy($this->sortBy, $this->sortDirection)
                            ->paginate($this->perPage);
                    }
                }
            }

        } elseif (auth()->user()->current_role == 'مشرف') {
            return $this->getReportsByGrade(Supervisor::where('id', auth()->id())->first()->grade_id);
        } elseif (auth()->user()->current_role == 'اداري') {
            return $this->getReportsByGrade(LowerSupervisor::where('id', auth()->id())->first()->grade_id);
        } elseif (auth()->user()->current_role == 'أمير المركز') {
            if (empty($this->searchGradeId)) {
                if ($this->searchReportTypeId == null) {
                    if ($this->searchDateFrom != null && $this->searchDateTo != null) {
                        return StudentDailyPreservation::query()
                            ->search($this->search)
                            ->whereBetween('daily_preservation_date', [$this->searchDateFrom, $this->searchDateTo])
                            ->orderBy($this->sortBy, $this->sortDirection)
                            ->paginate($this->perPage);
                    } else {
                        return StudentDailyPreservation::query()
                            ->search($this->search)
                            ->orderBy($this->sortBy, $this->sortDirection)
                            ->paginate($this->perPage);
                    }
                } else {
                    if ($this->searchDateFrom != null && $this->searchDateTo != null) {
                        return StudentDailyPreservation::query()
                            ->search($this->search)
                            ->whereBetween('daily_preservation_date', [$this->searchDateFrom, $this->searchDateTo])
                            ->where('type', $this->searchReportTypeId)
                            ->orderBy($this->sortBy, $this->sortDirection)
                            ->paginate($this->perPage);
                    } else {
                        return StudentDailyPreservation::query()
                            ->search($this->search)
                            ->where('type', $this->searchReportTypeId)
                            ->orderBy($this->sortBy, $this->sortDirection)
                            ->paginate($this->perPage);
                    }
                }
            } else {
                if (empty($this->searchGroupId)) {
                    if ($this->searchReportTypeId == null) {
                        if ($this->searchDateFrom != null && $this->searchDateTo != null) {
                            return StudentDailyPreservation::query()
                                ->search($this->search)
                                ->whereBetween('daily_preservation_date', [$this->searchDateFrom, $this->searchDateTo])
                                ->whereHas('student', function ($q) {
                                    return $q->where('grade_id', '=', $this->searchGradeId);
                                })
                                ->orderBy($this->sortBy, $this->sortDirection)
                                ->paginate($this->perPage);
                        } else {
                            return StudentDailyPreservation::query()
                                ->search($this->search)
                                ->whereHas('student', function ($q) {
                                    return $q->where('grade_id', '=', $this->searchGradeId);
                                })
                                ->orderBy($this->sortBy, $this->sortDirection)
                                ->paginate($this->perPage);
                        }
                    } else {
                        if ($this->searchDateFrom != null && $this->searchDateTo != null) {
                            return StudentDailyPreservation::query()
                                ->search($this->search)
                                ->where('type', $this->searchReportTypeId)
                                ->whereBetween('daily_preservation_date', [$this->searchDateFrom, $this->searchDateTo])
                                ->whereHas('student', function ($q) {
                                    return $q->where('grade_id', '=', $this->searchGradeId);
                                })
                                ->orderBy($this->sortBy, $this->sortDirection)
                                ->paginate($this->perPage);
                        } else {
                            return StudentDailyPreservation::query()
                                ->search($this->search)
                                ->where('type', $this->searchReportTypeId)
                                ->whereHas('student', function ($q) {
                                    return $q->where('grade_id', '=', $this->searchGradeId);
                                })
                                ->orderBy($this->sortBy, $this->sortDirection)
                                ->paginate($this->perPage);
                        }
                    }
                } else {
                    if (empty($this->searchStudentId)) {
                        if ($this->searchReportTypeId == null) {
                            if ($this->searchDateFrom != null && $this->searchDateTo != null) {
                                return StudentDailyPreservation::query()
                                    ->search($this->search)
                                    ->whereBetween('daily_preservation_date', [$this->searchDateFrom, $this->searchDateTo])
                                    ->whereHas('student', function ($q) {
                                        return $q->where('grade_id', '=', $this->searchGradeId)
                                            ->where('group_id', '=', $this->searchGroupId);
                                    })
                                    ->orderBy($this->sortBy, $this->sortDirection)
                                    ->paginate($this->perPage);
                            } else {
                                return StudentDailyPreservation::query()
                                    ->search($this->search)
                                    ->whereHas('student', function ($q) {
                                        return $q->where('grade_id', '=', $this->searchGradeId)
                                            ->where('group_id', '=', $this->searchGroupId);
                                    })
                                    ->orderBy($this->sortBy, $this->sortDirection)
                                    ->paginate($this->perPage);
                            }
                        } else {
                            if ($this->searchDateFrom != null && $this->searchDateTo != null) {
                                return StudentDailyPreservation::query()
                                    ->search($this->search)
                                    ->where('type', $this->searchReportTypeId)
                                    ->whereBetween('daily_preservation_date', [$this->searchDateFrom, $this->searchDateTo])
                                    ->whereHas('student', function ($q) {
                                        return $q->where('grade_id', '=', $this->searchGradeId)
                                            ->where('group_id', '=', $this->searchGroupId);
                                    })
                                    ->orderBy($this->sortBy, $this->sortDirection)
                                    ->paginate($this->perPage);
                            } else {
                                return StudentDailyPreservation::query()
                                    ->search($this->search)
                                    ->where('type', $this->searchReportTypeId)
                                    ->whereHas('student', function ($q) {
                                        return $q->where('grade_id', '=', $this->searchGradeId)
                                            ->where('group_id', '=', $this->searchGroupId);
                                    })
                                    ->orderBy($this->sortBy, $this->sortDirection)
                                    ->paginate($this->perPage);
                            }
                        }
                    } else {
                        if ($this->searchReportTypeId == null) {
                            if ($this->searchDateFrom != null && $this->searchDateTo != null) {
                                return StudentDailyPreservation::query()
                                    ->search($this->search)
                                    ->whereBetween('daily_preservation_date', [$this->searchDateFrom, $this->searchDateTo])
                                    ->whereHas('student', function ($q) {
                                        return $q
                                            ->where('grade_id', '=', $this->searchGradeId)
                                            ->where('group_id', '=', $this->searchGroupId)
                                            ->where('id', '=', $this->searchStudentId);
                                    })
                                    ->orderBy($this->sortBy, $this->sortDirection)
                                    ->paginate($this->perPage);
                            } else {
                                return StudentDailyPreservation::query()
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
                        } else {
                            if ($this->searchDateFrom != null && $this->searchDateTo != null) {
                                return StudentDailyPreservation::query()
                                    ->search($this->search)
                                    ->where('type', $this->searchReportTypeId)
                                    ->whereBetween('daily_preservation_date', [$this->searchDateFrom, $this->searchDateTo])
                                    ->whereHas('student', function ($q) {
                                        return $q
                                            ->where('grade_id', '=', $this->searchGradeId)
                                            ->where('group_id', '=', $this->searchGroupId)
                                            ->where('id', '=', $this->searchStudentId);
                                    })
                                    ->orderBy($this->sortBy, $this->sortDirection)
                                    ->paginate($this->perPage);
                            } else {
                                return StudentDailyPreservation::query()
                                    ->search($this->search)
                                    ->where('type', $this->searchReportTypeId)
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
        }
        return [];
    }

    private function getReportsByGrade($grade_id)
    {
        if (empty($this->searchGroupId)) {
            if ($this->searchReportTypeId == null) {
                if ($this->searchDateFrom != null && $this->searchDateTo != null) {
                    return StudentDailyPreservation::query()
                        ->search($this->search)
                        ->whereBetween('daily_preservation_date', [$this->searchDateFrom, $this->searchDateTo])
                        ->whereHas('student', function ($q) use ($grade_id) {
                            return $q->where('grade_id', '=', $grade_id);
                        })
                        ->orderBy($this->sortBy, $this->sortDirection)
                        ->paginate($this->perPage);
                } else {
                    return StudentDailyPreservation::query()
                        ->search($this->search)
                        ->whereHas('student', function ($q) use ($grade_id) {
                            return $q->where('grade_id', '=', $grade_id);
                        })
                        ->orderBy($this->sortBy, $this->sortDirection)
                        ->paginate($this->perPage);
                }
            } else {
                if ($this->searchDateFrom != null && $this->searchDateTo != null) {
                    return StudentDailyPreservation::query()
                        ->search($this->search)
                        ->where('type', $this->searchReportTypeId)
                        ->whereBetween('daily_preservation_date', [$this->searchDateFrom, $this->searchDateTo])
                        ->whereHas('student', function ($q) use ($grade_id) {
                            return $q->where('grade_id', '=', $grade_id);
                        })
                        ->orderBy($this->sortBy, $this->sortDirection)
                        ->paginate($this->perPage);
                } else {
                    return StudentDailyPreservation::query()
                        ->search($this->search)
                        ->where('type', $this->searchReportTypeId)
                        ->whereHas('student', function ($q) use ($grade_id) {
                            return $q->where('grade_id', '=', $grade_id);
                        })
                        ->orderBy($this->sortBy, $this->sortDirection)
                        ->paginate($this->perPage);
                }
            }
        } else {
            if (empty($this->searchStudentId)) {
                if ($this->searchReportTypeId == null) {
                    if ($this->searchDateFrom != null && $this->searchDateTo != null) {
                        return StudentDailyPreservation::query()
                            ->search($this->search)
                            ->whereBetween('daily_preservation_date', [$this->searchDateFrom, $this->searchDateTo])
                            ->whereHas('student', function ($q) use ($grade_id) {
                                return $q->where('grade_id', '=', $grade_id)
                                    ->where('group_id', '=', $this->searchGroupId);
                            })
                            ->orderBy($this->sortBy, $this->sortDirection)
                            ->paginate($this->perPage);
                    } else {
                        return StudentDailyPreservation::query()
                            ->search($this->search)
                            ->whereHas('student', function ($q) use ($grade_id) {
                                return $q->where('grade_id', '=', $grade_id)
                                    ->where('group_id', '=', $this->searchGroupId);
                            })
                            ->orderBy($this->sortBy, $this->sortDirection)
                            ->paginate($this->perPage);
                    }
                } else {
                    if ($this->searchDateFrom != null && $this->searchDateTo != null) {
                        return StudentDailyPreservation::query()
                            ->search($this->search)
                            ->where('type', $this->searchReportTypeId)
                            ->whereBetween('daily_preservation_date', [$this->searchDateFrom, $this->searchDateTo])
                            ->whereHas('student', function ($q) use ($grade_id) {
                                return $q->where('grade_id', '=', $grade_id)
                                    ->where('group_id', '=', $this->searchGroupId);
                            })
                            ->orderBy($this->sortBy, $this->sortDirection)
                            ->paginate($this->perPage);
                    } else {
                        return StudentDailyPreservation::query()
                            ->search($this->search)
                            ->where('type', $this->searchReportTypeId)
                            ->whereHas('student', function ($q) use ($grade_id) {
                                return $q->where('grade_id', '=', $grade_id)
                                    ->where('group_id', '=', $this->searchGroupId);
                            })
                            ->orderBy($this->sortBy, $this->sortDirection)
                            ->paginate($this->perPage);
                    }
                }
            } else {
                if ($this->searchReportTypeId == null) {
                    if ($this->searchDateFrom != null && $this->searchDateTo != null) {
                        return StudentDailyPreservation::query()
                            ->search($this->search)
                            ->whereBetween('daily_preservation_date', [$this->searchDateFrom, $this->searchDateTo])
                            ->whereHas('student', function ($q) use ($grade_id) {
                                return $q
                                    ->where('grade_id', '=', $grade_id)
                                    ->where('group_id', '=', $this->searchGroupId)
                                    ->where('id', '=', $this->searchStudentId);
                            })
                            ->orderBy($this->sortBy, $this->sortDirection)
                            ->paginate($this->perPage);
                    } else {
                        return StudentDailyPreservation::query()
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
                } else {
                    if ($this->searchDateFrom != null && $this->searchDateTo != null) {
                        return StudentDailyPreservation::query()
                            ->search($this->search)
                            ->where('type', $this->searchReportTypeId)
                            ->whereBetween('daily_preservation_date', [$this->searchDateFrom, $this->searchDateTo])
                            ->whereHas('student', function ($q) use ($grade_id) {
                                return $q
                                    ->where('grade_id', '=', $grade_id)
                                    ->where('group_id', '=', $this->searchGroupId)
                                    ->where('id', '=', $this->searchStudentId);
                            })
                            ->orderBy($this->sortBy, $this->sortDirection)
                            ->paginate($this->perPage);
                    } else {
                        return StudentDailyPreservation::query()
                            ->search($this->search)
                            ->where('type', $this->searchReportTypeId)
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
        }
    }

    public
    function sortBy($field)
    {
        if ($this->sortDirection == 'asc') {
            $this->sortDirection = 'desc';
        } else {
            $this->sortDirection = 'asc';
        }

        return $this->sortBy = $field;
    }
}
