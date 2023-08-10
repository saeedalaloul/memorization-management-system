<?php

namespace App\Http\Livewire;

use App\Exports\AllUsersExport;
use App\Models\ActivityMember;
use App\Models\Father;
use App\Models\Grade;
use App\Models\Group;
use App\Models\OversightMember;
use App\Models\Sponsorship;
use App\Models\Student;
use App\Models\Supervisor;
use App\Models\Teacher;
use App\Models\Tester;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\MessageBag;
use Maatwebsite\Excel\Excel;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class Users extends HomeComponent
{
    public $name, $gender;
    public $email;
    public $phone;
    public $identification_number;
    public $dob;
    public $recitation_level, $economic_situation, $academic_qualification;
    public $photo, $photo_ret;
    public $permissions = [];
    public $roles = [], $ret_Roles = [], $grades = [], $groups = [],$sponsorships = [], $sponsorships_ids;
    public $role_id, $selectedRoleId, $selectedEmailStatus, $selectedAccountStatus, $selectedActivityStatus, $category_permission_id, $permission_id;
    public $grade_id, $group_id, $father_id, $whatsapp_number, $country_code, $father_name,
        $father_identification_number;
    public $minute, $hour, $day, $week, $month, $current_timeStamp, $isWithoutTime = false, $isFoundPermission = false;

    public function mount()
    {
        $this->current_role = auth()->user()->current_role;
        $this->all_Grades();
        $this->getSponsorships();
        $this->all_Roles(-1);
    }

    public function render()
    {
        $this->calcCurrentTimeStamp();
        return view('livewire.users', ['users' => $this->all_Users()]);
    }

    public function updatedRoleId()
    {
        if ($this->role_id && $this->roles->firstWhere('id', $this->role_id)) {
            $role_name = $this->roles->firstWhere('id', $this->role_id)->name;
            if ($role_name === User::SUPERVISOR_ROLE) {
                $supervisor = Supervisor::find($this->modalId);
                if ($supervisor && $this->grade_id === null) {
                    $this->grade_id = $supervisor->grade_id;
                }
            } elseif ($role_name === User::SPONSORSHIP_SUPERVISORS_ROLE) {
                $supervisor = User::find($this->modalId);
                if ($supervisor) {
                    $this->sponsorships_ids = $supervisor->sponsorships()->pluck('id')->toArray();
                }
            } else if ($role_name === User::TEACHER_ROLE) {
                $teacher = Teacher::find($this->modalId);
                if ($teacher && $this->grade_id === null) {
                    $this->grade_id = $teacher->grade_id;
                }
            } else if ($role_name === User::STUDENT_ROLE) {
                $student = Student::find($this->modalId);
                if ($student) {
                    if ($this->grade_id === null) {
                        $this->grade_id = $student->grade_id;
                    }
                    if ($this->group_id === null) {
                        $this->group_id = $student->group_id;
                    }
                    if ($this->whatsapp_number === null) {
                        $this->whatsapp_number = '0' . substr($student->whatsapp_number, 4, 12);
                    }
                    if ($this->country_code === null) {
                        $this->country_code = substr($student->whatsapp_number, 0, 4);
                    }
                    if ($this->father_id === null) {
                        $this->father_id = $student->father_id;
                    }
                    if ($this->father_name === null) {
                        $this->father_name = $student->father->user->name;
                    }
                    if ($this->father_identification_number === null) {
                        $this->father_identification_number = $student->father->user->identification_number;
                    }
                }
            }
        }
    }

    public function storeOrUpdateUserRole()
    {
        if ($this->role_id && $this->roles->firstWhere('id', $this->role_id)) {
            $role_name = $this->roles->firstWhere('id', $this->role_id)->name;
            if ($role_name === User::ADMIN_ROLE) {
                $user = User::find($this->modalId);
                if ($user) {
                    $user->assignRole([$this->role_id]);
                    $this->dispatchBrowserEvent('alert',
                        ['type' => 'success', 'message' => 'تمت عملية تعيين دور أمير المركز إلى المستخدم بنجاح.']);
                    $this->modalFormReset();
                }
            } else if ($role_name === User::EXAMS_SUPERVISOR_ROLE) {
                $user = User::find($this->modalId);
                if ($user) {
                    $user->assignRole([$this->role_id]);
                    $this->dispatchBrowserEvent('alert',
                        ['type' => 'success', 'message' => 'تمت عملية تعيين دور مشرف الإختبارات إلى المستخدم بنجاح.']);
                    $this->modalFormReset();
                }
            } else if ($role_name === "مشرف الدورات") {
                $user = User::find($this->modalId);
                if ($user) {
                    $user->assignRole([$this->role_id]);
                    $this->dispatchBrowserEvent('alert',
                        ['type' => 'success', 'message' => 'تمت عملية تعيين دور مشرف الدورات إلى المستخدم بنجاح.']);
                    $this->modalFormReset();
                }
            } else if ($role_name === User::ACTIVITIES_SUPERVISOR_ROLE) {
                $user = User::find($this->modalId);
                if ($user) {
                    $user->assignRole([$this->role_id]);
                    $this->dispatchBrowserEvent('alert',
                        ['type' => 'success', 'message' => 'تمت عملية تعيين دور مشرف الأنشطة إلى المستخدم بنجاح.']);
                    $this->modalFormReset();
                }
            } else if ($role_name === User::OVERSIGHT_SUPERVISOR_ROLE) {
                $user = User::find($this->modalId);
                if ($user) {
                    $user->assignRole([$this->role_id]);
                    $this->dispatchBrowserEvent('alert',
                        ['type' => 'success', 'message' => 'تمت عملية تعيين دور مشرف الرقابة إلى المستخدم بنجاح.']);
                    $this->modalFormReset();
                }
            } else if ($role_name === User::OVERSIGHT_MEMBER_ROLE) {
                $user = User::find($this->modalId);
                if ($user) {
                    OversightMember::updateOrCreate(['id' => $this->modalId]);
                    $user->assignRole([$this->role_id]);
                    $this->dispatchBrowserEvent('alert',
                        ['type' => 'success', 'message' => 'تمت عملية تعيين دور مراقب إلى المستخدم بنجاح.']);
                    $this->modalFormReset();
                }
            } else if ($role_name === User::TESTER_ROLE) {
                $user = User::find($this->modalId);
                if ($user) {
                    Tester::updateOrCreate(['id' => $this->modalId]);
                    $user->assignRole([$this->role_id]);
                    $this->dispatchBrowserEvent('alert',
                        ['type' => 'success', 'message' => 'تمت عملية تعيين دور مختبر إلى المستخدم بنجاح.']);
                    $this->modalFormReset();
                    Cache::forget(Tester::CACHE_KEY);
                }
            } else if ($role_name === User::SUPERVISOR_ROLE) {
                $user = User::find($this->modalId);
                if ($user) {
                    $supervisor = Supervisor::find($this->modalId);
                    if ($supervisor === null) {
                        Supervisor::create(['id' => $this->modalId, 'grade_id' => $this->grade_id]);
                        $this->dispatchBrowserEvent('alert',
                            ['type' => 'success', 'message' => 'تمت عملية تعيين دور مشرف إلى المستخدم بنجاح.']);
                    } else {
                        $supervisor->update(['grade_id' => $this->grade_id]);
                        $this->dispatchBrowserEvent('alert',
                            ['type' => 'success', 'message' => 'تمت عملية تحديث دور مشرف إلى المستخدم بنجاح.']);
                    }
                    $user->assignRole([$this->role_id]);
                    $this->modalFormReset();
                }
            } else if ($role_name === User::SPONSORSHIP_SUPERVISORS_ROLE) {
                $this->validate(
                    [
                        'sponsorships_ids' => 'required',
                    ]);
                $user = User::find($this->modalId);
                if ($user) {
                    $user->assignRole([$this->role_id]);
                    $user->sponsorships()->sync($this->sponsorships_ids);
                    $this->dispatchBrowserEvent('alert',
                        ['type' => 'success', 'message' => 'تمت عملية تعيين دور مشرف حلقات مكفولة إلى المستخدم بنجاح.']);
                    $this->modalFormReset();
                }
            } else if ($role_name === User::TEACHER_ROLE) {
                $this->validate(
                    [
                        'grade_id' => 'required',
                    ]);
                $messageBag = new MessageBag;
                $teacher = Teacher::find($this->modalId);
                if ($teacher !== null) {
                    if ($this->grade_id !== $teacher->grade_id) {
                        if ($teacher->group !== null) {
                            $messageBag->add('grade_id', 'عذرا, لم يتم تحديث دور المحفظ لأن لديه حلقة');
                            $this->setErrorBag($messageBag);
                        } else {
                            $teacher->update(['grade_id' => $this->grade_id]);
                            $teacher->user->assignRole([$this->role_id]);
                            $this->dispatchBrowserEvent('alert',
                                ['type' => 'success', 'message' => 'تمت عملية تحديث دور محفظ إلى المستخدم بنجاح.']);
                            $this->modalFormReset();
                        }
                    } else {
                        $teacher->user->assignRole([$this->role_id]);
                        $this->dispatchBrowserEvent('alert',
                            ['type' => 'success', 'message' => 'تمت عملية تحديث دور محفظ إلى المستخدم بنجاح.']);
                        $this->modalFormReset();
                    }
                } else {
                    Teacher::create(['id' => $this->modalId, 'grade_id' => $this->grade_id]);
                    $user = User::find($this->modalId);
                    $user->assignRole([$this->role_id]);
                    $this->dispatchBrowserEvent('alert',
                        ['type' => 'success', 'message' => 'تمت عملية تعيين دور محفظ إلى المستخدم بنجاح.']);
                    $this->modalFormReset();
                }

            } else if ($role_name === User::STUDENT_ROLE) {
                $this->validate(
                    [
                        'grade_id' => 'required',
                        'group_id' => 'required',
                        'whatsapp_number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:10',
                        'country_code' => 'required|string',
                        'father_identification_number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:9|max:9',
                        'father_id' => 'required',
                    ]);

                $messageBag = new MessageBag;
                $student = Student::find($this->modalId);
                if ($student !== null) {
                    if ($this->group_id !== $student->group_id) {
                        $teacher_found = Teacher::find($this->modalId);
                        if ($teacher_found !== null && $teacher_found->group !== null
                            && $teacher_found->group->id === $this->group_id) {
                            $messageBag->add('group_id', 'عذرا, لا يمكن تغيير الحلقة للطالب لأنه محفظ في نفس الحلقة');
                            $this->setErrorBag($messageBag);
                        } else if ($student->exam_order->count() > 0) {
                            $messageBag->add('group_id', 'عذرا, لا يمكن تغيير الحلقة للطالب بسبب وجود طلبات اختبارات يجب إجرائها أو حذفها');
                            $this->setErrorBag($messageBag);
                        } else {
                            $student->update([
                                'grade_id' => $this->grade_id,
                                'group_id' => $this->group_id,
                                'father_id' => $this->father_id,
                                'whatsapp_number' => $this->country_code . intval($this->whatsapp_number),
                            ]);

                            $student->user->assignRole([$this->role_id]);
                            $this->dispatchBrowserEvent('alert',
                                ['type' => 'success', 'message' => 'تمت عملية تحديث دور طالب إلى المستخدم بنجاح.']);
                            $this->modalFormReset();
                        }
                    } else if ($student->group->grade_id === $this->grade_id) {
                        $student->update([
                            'grade_id' => $this->grade_id,
                            'group_id' => $this->group_id,
                            'father_id' => $this->father_id,
                            'whatsapp_number' => $this->country_code . intval($this->whatsapp_number)
                        ]);
                        $student->user->assignRole([$this->role_id]);
                        $this->dispatchBrowserEvent('alert',
                            ['type' => 'success', 'message' => 'تمت عملية تحديث دور طالب إلى المستخدم بنجاح.']);
                        $this->modalFormReset();
                    } else {
                        $messageBag->add('grade_id', 'عذرا, لا يمكن تغيير المرحلة لأن الحلقة ليست في نفس المرحلة');
                        $this->setErrorBag($messageBag);
                    }
                } else {
                    $teacher_found = Teacher::find($this->modalId);
                    if ($teacher_found !== null && $teacher_found->group !== null
                        && $teacher_found->group->id === $this->group_id) {
                        $messageBag->add('group_id', 'عذرا, لا يمكن اختيار الحلقة للطالب لأنه محفظ في نفس الحلقة');
                        $this->setErrorBag($messageBag);
                    } else {
                        Student::create([
                            'id' => $this->modalId,
                            'grade_id' => $this->grade_id,
                            'group_id' => $this->group_id,
                            'father_id' => $this->father_id,
                            'whatsapp_number' => $this->country_code . intval($this->whatsapp_number),
                        ]);
                        $user = User::find($this->modalId);
                        $user->assignRole([$this->role_id]);
                        $this->dispatchBrowserEvent('alert',
                            ['type' => 'success', 'message' => 'تمت عملية تعيين دور طالب إلى المستخدم بنجاح.']);
                        $this->modalFormReset();
                    }
                }
            } else if ($role_name === User::FATHER_ROLE) {
                $father = Father::find($this->modalId);
                if ($father === null) {
                    Father::create([
                        'id' => $this->modalId,
                    ]);
                }
                $user = User::find($this->modalId);
                $user->assignRole([$this->role_id]);
                $this->dispatchBrowserEvent('alert',
                    ['type' => 'success', 'message' => 'تمت عملية تحديث دور ولي أمر طالب إلى المستخدم بنجاح.']);
                $this->modalFormReset();
            }
        }
    }

    public function pullUserRole()
    {
        $messageBag = new MessageBag();

        if ($this->role_id && $this->roles->firstWhere('id', $this->role_id)) {
            $role_name = $this->roles->firstWhere('id', $this->role_id)->name;
            if ($role_name === User::ADMIN_ROLE) {
                if (auth()->id() !== $this->modalId) {
                    $user = User::find($this->modalId);
                    $user?->removeRole($this->role_id);
                    $this->dispatchBrowserEvent('alert',
                        ['type' => 'success', 'message' => 'تمت عملية سحب دور أمير المركز من المستخدم بنجاح.']);
                    $this->modalFormReset();
                } else {
                    $this->catchError = 'عذرا لا يمكنك سحب صلاحيتك أمير المركز ..';
                }
            } else if ($role_name === User::EXAMS_SUPERVISOR_ROLE) {
                $user = User::find($this->modalId);
                $user?->removeRole($this->role_id);
                $this->dispatchBrowserEvent('alert',
                    ['type' => 'success', 'message' => 'تمت عملية سحب دور مشرف الإختبارات من المستخدم بنجاح.']);
                $this->modalFormReset();
            } else if ($role_name === "مشرف الدورات") {
                $user = User::find($this->modalId);
                $user?->removeRole($this->role_id);
                $this->dispatchBrowserEvent('alert',
                    ['type' => 'success', 'message' => 'تمت عملية سحب دور مشرف الدورات من المستخدم بنجاح.']);
                $this->modalFormReset();
            } else if ($role_name === User::ACTIVITIES_SUPERVISOR_ROLE) {
                $user = User::find($this->modalId);
                $user?->removeRole($this->role_id);
                $this->dispatchBrowserEvent('alert',
                    ['type' => 'success', 'message' => 'تمت عملية سحب دور مشرف الأنشطة من المستخدم بنجاح.']);
                $this->modalFormReset();
            } else if ($role_name === User::OVERSIGHT_SUPERVISOR_ROLE) {
                $user = User::find($this->modalId);
                $user?->removeRole($this->role_id);
                $this->dispatchBrowserEvent('alert',
                    ['type' => 'success', 'message' => 'تمت عملية سحب دور مشرف الرقابة من المستخدم بنجاح.']);
                $this->modalFormReset();
            } else if ($role_name === User::OVERSIGHT_MEMBER_ROLE) {
                $user = User::find($this->modalId);
                if ($user->oversight_member->visits->count() > 0) {
                    $messageBag->add('role_id', 'عذرا لا يمكن حذف المراقب بسبب وجود زيارات مسجلة باسم المراقب');
                    $this->setErrorBag($messageBag);
                } else if ($user->oversight_member->visits_orders->count() > 0) {
                    $messageBag->add('role_id', 'عذرا لا يمكن حذف المراقب بسبب وجود طلبات زيارات لديه يرجى إجرائها أو حذفها');
                    $this->setErrorBag($messageBag);
                } else {
                    $user?->removeRole($this->role_id);
                    OversightMember::destroy($this->modalId);
                    $this->dispatchBrowserEvent('alert',
                        ['type' => 'success', 'message' => 'تمت عملية سحب دور مراقب من المستخدم بنجاح.']);
                    $this->modalFormReset();
                }
            } else if ($role_name === User::TESTER_ROLE) {
                $user = User::find($this->modalId);
                if ($user->tester->exams->count() > 0) {
                    $messageBag->add('role_id', 'عذرا لا يمكن حذف المختبر بسبب وجود اختبارات مسجلة باسم المختبر');
                    $this->setErrorBag($messageBag);
                } else if ($user->tester->exams_orders->count() > 0) {
                    $messageBag->add('role_id', 'عذرا لا يمكن حذف المختبر بسبب وجود طلبات اختبارات لديه يرجى إجرائها أو حذفها');
                    $this->setErrorBag($messageBag);
                } else {
                    $user?->removeRole($this->role_id);
                    Tester::destroy($this->modalId);
                    $this->dispatchBrowserEvent('alert',
                        ['type' => 'success', 'message' => 'تمت عملية سحب دور مختبر من المستخدم بنجاح.']);
                    $this->modalFormReset();
                    Cache::forget(Tester::CACHE_KEY);
                }
            } else if ($role_name === User::ACTIVITY_MEMBER_ROLE) {
                $user = User::find($this->modalId);
                if ($user->activity_member->activities->count() > 0) {
                    $messageBag->add('role_id', 'عذرا لا يمكن حذف المنشط بسبب وجود أنشطة مسجلة باسم المنشط');
                    $this->setErrorBag($messageBag);
                } else if ($user->activity_member->activities_orders->count() > 0) {
                    $messageBag->add('role_id', 'عذرا لا يمكن حذف المنشط بسبب وجود طلبات أنشطة لديه يرجى إجرائها أو حذفها');
                    $this->setErrorBag($messageBag);
                } else {
                    $user?->removeRole($this->role_id);
                    ActivityMember::destroy($this->modalId);
                    $this->dispatchBrowserEvent('alert',
                        ['type' => 'success', 'message' => 'تمت عملية سحب دور منشط من المستخدم بنجاح.']);
                    $this->modalFormReset();
                }
            } else if ($role_name === User::SUPERVISOR_ROLE) {
                $user = User::find($this->modalId);
                $user?->removeRole($this->role_id);
                Supervisor::destroy($this->modalId);
                $this->dispatchBrowserEvent('alert',
                    ['type' => 'success', 'message' => 'تمت عملية سحب دور مشرف من المستخدم بنجاح.']);
                $this->modalFormReset();
            } else if ($role_name === User::SPONSORSHIP_SUPERVISORS_ROLE) {
                $user = User::find($this->modalId);
                $user?->removeRole($this->role_id);
                $user?->sponsorships()->detach();
                $this->dispatchBrowserEvent('alert',
                    ['type' => 'success', 'message' => 'تمت عملية سحب دور مشرف حلقات مكفولة من المستخدم بنجاح.']);
                $this->modalFormReset();
            } else if ($role_name === User::TEACHER_ROLE) {
                $this->pullTeacherRole();
            } else if ($role_name === User::STUDENT_ROLE) {
                $this->pullStudentRole();
            } else if ($role_name === User::FATHER_ROLE) {
                $messageBag = new MessageBag();
                $father = Father::find($this->modalId);
                if ($father->students->count() > 0) {
                    $messageBag->add('role_id', 'عذرا, لا يمكن سحب دور ولي أمر الطالب بسبب وجود أبناء لديه');
                    $this->setErrorBag($messageBag);
                } else {
                    $user = User::find($this->modalId);
                    $user?->removeRole($this->role_id);
                    Father::destroy($this->modalId);
                    $this->dispatchBrowserEvent('alert',
                        ['type' => 'success', 'message' => 'تمت عملية سحب دور ولي أمر الطالب من المستخدم بنجاح.']);
                    $this->modalFormReset();
                }
            }
        }
    }

    public function pullTeacherRole()
    {
        $messageBag = new MessageBag();
        $teacher = Teacher::find($this->modalId);
        if ($teacher->group !== null) {
            $messageBag->add('role_id', 'عذرا, لم يتم سحب دور المحفظ لأن لديه حلقة');
            $this->setErrorBag($messageBag);
        } else if ($teacher->exam_order->count() > 0) {
            $messageBag->add('role_id', 'عذرا, لا يمكن سحب دور المحفظ بسبب وجود طلبات اختبارات يجب إجرائها أو حذفها');
            $this->setErrorBag($messageBag);
        } else if ($teacher->exam->count() > 0) {
            $messageBag->add('role_id', 'عذرا, لا يمكن سحب دور المحفظ إطلاقا بسبب وجود اختبارات قرآنية مسجلة باسمه');
            $this->setErrorBag($messageBag);
        } else if ($teacher->attendance->count() > 0) {
            $messageBag->add('role_id', 'عذرا, لا يمكن سحب دور المحفظ بسبب وجود سجل حضور وغياب لديه');
            $this->setErrorBag($messageBag);
        } else if ($teacher->attendance_student->count() > 0) {
            $messageBag->add('role_id', 'عذرا, لا يمكن سحب دور المحفظ بسبب وجود سجل حضور وغياب مسجل باسمه لدى طلابه');
            $this->setErrorBag($messageBag);
        } else if ($teacher->student_daily_memorization->count() > 0) {
            $messageBag->add('role_id', 'عذرا, لا يمكن سحب دور المحفظ بسبب وجود سجل متابعة الحفظ والمراجعة مسجل باسمه لدى طلابه');
            $this->setErrorBag($messageBag);
        } else {
            $user = User::find($this->modalId);
            $user?->removeRole($this->role_id);
            Teacher::destroy($this->modalId);
            $this->dispatchBrowserEvent('alert',
                ['type' => 'success', 'message' => 'تمت عملية سحب دور محفظ من المستخدم بنجاح.']);
            $this->modalFormReset();
        }
    }

    public function pullStudentRole()
    {
        $messageBag = new MessageBag();
        $student = Student::find($this->modalId);
        if ($student->exam_order->count() > 0) {
            $messageBag->add('role_id', 'عذرا, لا يمكن سحب دور الطالب بسبب وجود طلبات اختبارات يجب إجرائها أو حذفها');
            $this->setErrorBag($messageBag);
        } else if ($student->exams->count() > 0) {
            $messageBag->add('role_id', 'عذرا, لا يمكن سحب دور الطالب إطلاقا بسبب وجود اختبارات قرآنية مسجلة باسمه');
            $this->setErrorBag($messageBag);
        } else if ($student->attendance->count() > 0) {
            $messageBag->add('role_id', 'عذرا, لا يمكن سحب دور الطالب بسبب وجود سجل حضور وغياب لديه');
            $this->setErrorBag($messageBag);
        } else if ($student->daily_memorization->count() > 0) {
            $messageBag->add('role_id', 'عذرا, لا يمكن سحب دور الطالب بسبب وجود سجل متابعة الحفظ والمراجعة مسجل باسمه');
            $this->setErrorBag($messageBag);
        } else {
            $user = User::find($this->modalId);
            $user?->removeRole($this->role_id);
            Student::destroy($this->modalId);
            $this->dispatchBrowserEvent('alert',
                ['type' => 'success', 'message' => 'تمت عملية سحب دور طالب من المستخدم بنجاح.']);
            $this->modalFormReset();
        }
    }

    public function findStudentFather()
    {
        if ($this->father_identification_number) {
            if (strlen($this->father_identification_number) === 9) {
                $father = Father::query()
                    ->whereRelation('user', 'identification_number', '=', $this->father_identification_number)
                    ->first();
                if ($father !== null) {
                    $this->father_id = $father->id;
                    $this->father_name = $father->user->name;
                    $this->successMessage = 'لقد تم العثور على رقم هوية ولي الأمر بنجاح...';
                } else {
                    $this->father_id = null;
                    $this->father_name = null;
                    $this->successMessage = '';
                }
            } else {
                $this->father_id = null;
                $this->father_name = null;
                $this->successMessage = '';
            }
        }
    }

    public function rules()
    {
        return [
            'password' => 'required|min:8|max:10',
            'password_confirm' => 'required|same:password',
        ];
    }

    public function resetPasswordUser()
    {
        DB::beginTransaction();
        try {
            if ($this->modalId !== auth()->id()) {
                User::find($this->modalId)->update([
                    'password' => null,
                ]);
                $this->dispatchBrowserEvent('alert',
                    ['type' => 'success', 'message' => 'تمت عملية إعادة تعيين كلمة المرور بنجاح.']);
                DB::commit();
            } else {
                $this->dispatchBrowserEvent('alert',
                    ['type' => 'error', 'message' => 'عذرا لا يمكنك إعادة تعيين كلمة مرور حسابك.']);
            }
            $this->modalFormReset();
            $this->dispatchBrowserEvent('hideModal');
        } catch (Exception $e) {
            DB::rollback();
            $this->catchError = $e->getMessage();
        }
    }

    public function modelUser()
    {
        return [
            'name' => $this->name,
            'gender' => $this->gender,
            'email' => $this->email,
            'phone' => $this->phone,
            'identification_number' => $this->identification_number,
            'dob' => $this->dob,
        ];
    }

    public function modelUserInfo($user_id)
    {
        return [
            'id' => $user_id,
            'economic_situation' => $this->economic_situation,
            'recitation_level' => $this->recitation_level,
            'academic_qualification' => $this->academic_qualification,
        ];
    }


    public function loadModalData($id, $process_type)
    {
        $this->modalFormReset();
        $this->process_type = $process_type;
        $data = User::find($id);
        $this->modalId = $data->id;
        $this->name = $data->name;
        if ($process_type === 'edit') {
            $this->gender = $data->gender;
            $this->email = $data->email;
            $this->phone = $data->phone;
            $this->identification_number = $data->identification_number;
            $this->economic_situation = $data->user_info->economic_situation ?? null;
            $this->recitation_level = $data->user_info->recitation_level ?? null;
            $this->academic_qualification = $data->user_info->academic_qualification ?? null;
            $this->dob = $data->dob;
            $this->photo_ret = $data->profile_photo_url;
        } else if ($process_type === 'edit_permission') {
            $this->dispatchBrowserEvent('showDialogEditPermission');
        } else if ($process_type === 'reset') {
            $this->dispatchBrowserEvent('showDialogResetPassword');
        } else if ($data->roles->count() === 1) {
            if ($data->roles[0]->name === "طالب") {
                $this->all_Roles(2); // سيتم اظهار صلاحية الطالب والمحفظ
                $this->ret_Roles = $data->roles;
            } else if ($data->roles[0]->name === User::FATHER_ROLE) {
                $this->all_Roles(3); // سيتم اظهار صلاحية ولي الأمر
                $this->ret_Roles = $data->roles;
            } else if ($data->roles[0]->name === "محفظ") {
                $this->all_Roles(1); // سيتم اظهار كافة الصلاحيات ما عدا صلاحية ولي الأمر
                $this->ret_Roles = $data->roles;
            } else {
                $this->all_Roles(1); // سيتم اظهار كافة الصلاحيات ما عدا صلاحية ولي الأمر والطالب
                $this->ret_Roles = $data->roles;
            }
        } else if ($data->roles->count() > 1) {
            $this->all_Roles(1); // سيتم اظهار كافة الصلاحيات ما عدا صلاحية ولي الأمر
            $this->ret_Roles = $data->roles;
        } else {
            $this->all_Roles(-1); // سيتم اظهار كافة الصلاحيات
            $this->ret_Roles = $data->roles;
        }
    }

    public function activeEmail($id)
    {
        $user = User::find($id);
        if ($user !== null) {
            if ($user->roles !== null && $user->roles->where('name', 'أمير المركز')->first() !== null
                && $id === auth()->id()) {
                $this->catchError = 'عذرا لا يمكن لأمير المركز تعطيل التحقق من بريده الإلكتروني';
            } else if ($user->hasVerifiedEmail()) {
                $user->update(['email_verified_at' => null]);
                $this->dispatchBrowserEvent('alert',
                    ['type' => 'error', 'message' => 'تمت عملية إلغاء تفعيل البريد الإلكتروني بنجاح.']);
            } else {
                $user->markEmailAsVerified();
                event(new Verified($user));
                $this->dispatchBrowserEvent('alert',
                    ['type' => 'success', 'message' => 'تمت عملية تفعيل البريد الإلكتروني بنجاح.']);
            }
        }
    }

    public function activeAccount($id)
    {
        $user = User::find($id);
        if ($user !== null) {
            if ($user->roles !== null && $user->roles->where('name', 'أمير المركز')->first() !== null
                && $id === auth()->id()) {
                $this->catchError = 'عذرا لا يمكن لأمير المركز تعطيل حسابه في النظام';
            } else if ($user->status) {
                $user->update(['status' => 0]);
                $this->dispatchBrowserEvent('alert',
                    ['type' => 'error', 'message' => 'تمت عملية تعليق الحساب بنجاح.']);
            } else {
                $user->update(['status' => 1]);
                $this->dispatchBrowserEvent('alert',
                    ['type' => 'success', 'message' => 'تمت عملية تفعيل الحساب بنجاح.']);
            }
        }
    }

    public function store()
    {
        $this->validate(
            [
                'name' => 'required|string|unique:users,name,' . $this->modalId,
                'gender' => 'required|string',
                'email' => 'required|email|unique:users,email,' . $this->modalId,
                'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:10|unique:users,phone,' . $this->modalId,
                'identification_number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:9|max:9|unique:users,identification_number,' . $this->modalId,
                'recitation_level' => 'required',
                'academic_qualification' => 'required',
                'economic_situation' => 'required',
                'dob' => 'required|date|date_format:Y-m-d',
            ]
        );

        if (!empty($this->photo)) {
            $this->validate([
                'photo' => 'required|image|mimes:jpg,jpeg,png|max:1024|unique:users,profile_photo,' . $this->modalId,
            ]);
        }

        DB::beginTransaction();
        try {
            $user = User::create($this->modelUser());
            $user->user_info()->create($this->modelUserInfo($user->id));
            if (!empty($this->photo)) {
                $this->uploadImage($this->photo,
                    $this->identification_number . Carbon::now()->timestamp . '.' . $this->photo->getClientOriginalExtension(),
                    $user->id);
            }
            $this->modalFormReset();
            $this->dispatchBrowserEvent('alert',
                ['type' => 'success', 'message' => 'تم حفظ معلومات المستخدم بنجاح.']);
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $this->catchError = $e->getMessage();
        }
    }

    public function update()
    {
        $this->validate(
            [
                'name' => 'required|string|unique:users,name,' . $this->modalId,
                'gender' => 'required|string',
                'email' => 'required|email|unique:users,email,' . $this->modalId,
                'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:10|unique:users,phone,' . $this->modalId,
                'identification_number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:9|max:9|unique:users,identification_number,' . $this->modalId,
                'recitation_level' => 'required',
                'academic_qualification' => 'required',
                'economic_situation' => 'required',
                'dob' => 'required|date|date_format:Y-m-d',
            ]
        );

        if (!empty($this->photo)) {
            $this->validate([
                'photo' => 'required|image|mimes:jpg,jpeg,png|max:1024|unique:users,profile_photo,' . $this->modalId,
            ]);
        }

        DB::beginTransaction();
        try {
            $user = User::where('id', $this->modalId)->first();
            $user->update($this->modelUser());
            if ($user->user_info === null) {
                $user->user_info()->create($this->modelUserInfo($this->modalId));
            } else {
                $user->user_info->update($this->modelUserInfo($this->modalId));
            }
            if (!empty($this->photo)) {
                $this->deleteImage($user->profile_photo);
                $this->uploadImage($this->photo,
                    $user->identification_number . Carbon::now()->timestamp . '.' . $this->photo->getClientOriginalExtension(),
                    $this->modalId);
            }

            $this->modalFormReset();
            $this->dispatchBrowserEvent('alert',
                ['type' => 'success', 'message' => 'تم تحديث معلومات المستخدم بنجاح.']);
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $this->catchError = $e->getMessage();
        }
    }

    public function all_Users()
    {
        return User::query()
            ->with(['roles'])
            ->when(!empty($this->selectedRoleId), function ($q, $v) {
                $q->whereRelation('roles', 'id', '=', $this->selectedRoleId);
            })->when($this->selectedEmailStatus !== null && (int)$this->selectedEmailStatus === 1, function ($q, $v) {
                $q->whereNotNull('email_verified_at');
            })
            ->when($this->selectedEmailStatus !== null && (int)$this->selectedEmailStatus === 0, function ($q, $v) {
                $q->whereNull('email_verified_at');
            })->when($this->selectedAccountStatus !== null, function ($q, $v) {
                $q->where('status', '=', (int)$this->selectedAccountStatus);
            })->when($this->selectedActivityStatus !== null && (int)$this->selectedActivityStatus === 1, function ($q, $v) {
                $q->where('last_seen', '>=', Carbon::now()->addMinutes(-2));
            })->when($this->selectedActivityStatus !== null && (int)$this->selectedActivityStatus === 0, function ($q, $v) {
                $q->where('last_seen', '<=', Carbon::now()->addMinutes(-2));
            })
            ->search($this->search)
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function submitSearch()
    {
        $this->all_Users();
    }

    public function all_Grades()
    {
        $this->grades = Grade::all();
    }

    public function getSponsorships()
    {
        $this->reset('sponsorships_ids', 'sponsorships');

        if ($this->current_role === 'أمير المركز') {
            $this->sponsorships = Sponsorship::query()->get();
        }
        return [];
    }

    public function updatedGradeId()
    {
        if ($this->grade_id) {
            $this->groups = Group::query()->where('grade_id', $this->grade_id)->with(['teacher.user:id,name'])->get();
        }
    }

    public function updatedCategoryPermissionId()
    {
        if ($this->category_permission_id === 'المراحل') {
            $this->permissions = Permission::query()->whereIn('id', [27, 28, 29, 30])->get();
        } elseif ($this->category_permission_id === 'المجموعات') {
            $this->permissions = Permission::query()->whereIn('id', [21, 22, 23, 24, 25, 26])->get();
        } elseif ($this->category_permission_id === 'الكفالات') {
            $this->permissions = Permission::query()->whereIn('id', [66, 67, 68, 69, 70, 71, 72,73])->get();
        } elseif ($this->category_permission_id === 'مشرفي المراحل') {
            $this->permissions = Permission::query()->whereIn('id', [17, 18, 19, 20])->get();
        } elseif ($this->category_permission_id === 'المحفظين') {
            $this->permissions = Permission::query()->whereIn('id', [12, 13, 14, 15, 16])->get();
        } elseif ($this->category_permission_id === 'الطلاب') {
            $this->permissions = Permission::query()->whereIn('id', [3, 4, 5, 6, 7, 8, 9, 10, 11, 63, 64])->get();
        } elseif ($this->category_permission_id === 'الإختبارات') {
            $this->permissions = Permission::query()->whereIn('id', [31, 32, 33, 34,65])->get();
        } elseif ($this->category_permission_id === 'طلبات الإختبارات') {
            $this->permissions = Permission::query()->whereIn('id', [35, 36, 37])->get();
        } elseif ($this->category_permission_id === 'المختبرين') {
            $this->permissions = Permission::query()->whereIn('id', [38, 39, 40])->get();
        } elseif ($this->category_permission_id === 'الأنشطة') {
            $this->permissions = Permission::query()->whereIn('id', [47, 48, 49, 50, 51, 52])->get();
        } elseif ($this->category_permission_id === 'أعضاء الأنشطة') {
            $this->permissions = Permission::query()->whereIn('id', [45, 46])->get();
        } elseif ($this->category_permission_id === 'أعضاء الرقابة') {
            $this->permissions = Permission::query()->whereIn('id', [43, 44])->get();
        } elseif ($this->category_permission_id === 'صندوق الشكاوي والرقابة') {
            $this->permissions = Permission::query()->whereIn('id', [41, 42])->get();
        } elseif ($this->category_permission_id === 'الإجراءات العقابية') {
            $this->permissions = Permission::query()->whereIn('id', [57, 58, 59, 60])->get();
        } elseif ($this->category_permission_id === 'إدارة التقارير') {
            $this->permissions = Permission::query()->whereIn('id', [61, 62])->get();
        } elseif ($this->category_permission_id === 'المستخدمين') {
            $this->permissions = Permission::query()->whereIn('id', [1, 2, 53, 54, 55, 56])->get();
        } else {
            $this->permissions = [];
            $this->permission_id = null;
        }
    }

    public function updateUserPermission()
    {
        $this->validate([
            'category_permission_id' => 'required|string',
            'permission_id' => 'required|string',
        ]);

        $messageBag = new MessageBag();
        $isComplete = true;
        if (!$this->isWithoutTime && $this->current_timeStamp === null) {
            $messageBag->add('minute', 'يجب تحديد خيار واحد على الأقل.');
            $messageBag->add('hour', 'يجب تحديد خيار واحد على الأقل.');
            $messageBag->add('day', 'يجب تحديد خيار واحد على الأقل.');
            $messageBag->add('week', 'يجب تحديد خيار واحد على الأقل.');
            $messageBag->add('month', 'يجب تحديد خيار واحد على الأقل.');
            $this->setErrorBag($messageBag);
            $isComplete = false;
        }

        if ($isComplete) {
            $current_time = Carbon::now()->addMinutes($this->minute)
                ->addHours($this->hour)
                ->addDays($this->day)
                ->addWeeks($this->week)
                ->addMonths($this->month)->toDateTime();

            DB::table('model_has_permissions')
                ->updateOrInsert(
                    ['permission_id' => $this->permission_id,
                        'model_type' => User::class,
                        'model_id' => $this->modalId],
                    ['expiration_datetime' => !$this->isWithoutTime ? $current_time : null]);
            $this->dispatchBrowserEvent('alert',
                ['type' => 'success', 'message' => 'تم تعيين صلاحية للمستخدم بنجاح.']);
            $this->dispatchBrowserEvent('hideModal');
            $this->modalFormReset();
        }
    }

    public function deleteUserPermission()
    {
        if ($this->permission_id !== null && $this->modalId !== '') {
            DB::table('model_has_permissions')
                ->where('permission_id', $this->permission_id)
                ->where('model_type', User::class)
                ->where('model_id', $this->modalId)->delete();
            $this->dispatchBrowserEvent('alert',
                ['type' => 'success', 'message' => 'تم سحب الصلاحية من المستخدم بنجاح.']);
            $this->dispatchBrowserEvent('hideModal');
            $this->modalFormReset();
        }
    }

    public function updatedPermissionId()
    {
        $this->isFoundPermission = false;
        $this->isWithoutTime = false;
        if ($this->permission_id !== null && $this->modalId !== '') {
            $permission = DB::table('model_has_permissions')
                ->where('permission_id', $this->permission_id)
                ->where('model_type', User::class)
                ->where('model_id', $this->modalId)->first();
            $this->isFoundPermission = $permission !== null;
            if ($permission !== null) {
                $this->isWithoutTime = $permission->expiration_datetime === null;
            }
        }
    }

    public function updatedIsWithoutTime($value)
    {
        if ($value === true) {
            $this->reset('current_timeStamp', 'minute', 'hour', 'day', 'week', 'month');
            $this->resetValidation([$this->current_timeStamp, $this->minute, $this->hour, $this->day, $this->week, $this->month]);
        }
    }

    private function calcCurrentTimeStamp()
    {
        if ($this->minute !== null || $this->hour !== null || $this->day !== null || $this->week !== null || $this->month !== null) {
            $this->current_timeStamp = Carbon::parse(Carbon::now()->addMinutes($this->minute)
                ->addHours($this->hour)
                ->addDays($this->day)
                ->addWeeks($this->week)
                ->addMonths($this->month)->toDateTime())->translatedFormat('l j F Y h:i a');
        }
    }

    public function all_Roles($status)
    {
        if ($status === 0) {
            $this->roles = Role::query()->whereNotIn('name', [User::STUDENT_ROLE, User::FATHER_ROLE])->get();
        } else if ($status === 1) {
            $this->roles = Role::query()->whereNotIn('name', [User::FATHER_ROLE])->get();
        } else if ($status === 2) {
            $this->roles = Role::query()->whereIn('name', [User::STUDENT_ROLE, User::TEACHER_ROLE])->get();
        } else if ($status === 3) {
            $this->roles = Role::query()->whereIn('name', [User::FATHER_ROLE])->get();
        } else {
            $this->roles = Role::query()->orderBy('name')->get();
        }
    }


    public
    function modalFormReset()
    {
        $this->resetValidation();
        $this->ret_Roles = [];
        $this->modalId = '';
        $this->name = null;
        $this->gender = null;
        $this->email = null;
        $this->phone = null;
        $this->identification_number = null;
        $this->academic_qualification = null;
        $this->economic_situation = null;
        $this->recitation_level = null;
        $this->country_code = null;
        $this->whatsapp_number = null;
        $this->dob = null;
        $this->photo = null;
        $this->photo_ret = null;
        $this->process_type = '';
        $this->catchError = '';
        $this->grade_id = null;
        $this->group_id = null;
        $this->father_name = null;
        $this->father_id = null;
        $this->father_identification_number = null;
        $this->role_id = null;
        $this->sponsorships_ids = null;
        $this->category_permission_id = null;
        $this->permission_id = null;
        $this->current_timeStamp = null;
        $this->minute = null;
        $this->hour = null;
        $this->day = null;
        $this->week = null;
        $this->month = null;
        $this->isWithoutTime = false;
        $this->isFoundPermission = false;
    }


    public function messages()
    {
        return [
            'password.required' => 'حقل كلمة المرور مطلوب',
            'password.min' => 'يجب أن لا يقل طول كلمة المرور عن 8 حروف',
            'password.max' => 'يجب أن لا يزيد طول كلمة المرور عن 10 حروف',
            'password_confirm.required' => 'حقل تأكيد كلمة المرور مطلوب',
            'password_confirm.same' => 'يجب أن تكون كلمة المرور متطابقة',
            'email.required' => 'حقل البريد الإلكتروني مطلوب',
            'email.email' => 'يجب إدخال بريد إلكتروني صالح',
            'email.unique' => 'البريد الإلكتروني المدخل موجود مسبقا',
            'name.required' => 'حقل الاسم مطلوب',
            'name.string' => 'يجب إدخال نص في حقل الاسم',
            'name.unique' => 'الاسم المدخل موجود مسبقا',
            'gender.required' => 'حقل الجنس مطلوب',
            'gender.string' => 'يجب إدخال نص في حقل الجنس',
            'phone.required' => 'حقل رقم الجوال مطلوب',
            'phone.unique' => 'رقم الجوال المدخل موجود مسبقا',
            'phone.min' => 'يجب أن لا يقل طول رقم الجوال عن 10 أرقام',
            'phone.max' => 'يجب أن لا يزيد طول رقم الجوال عن 10 أرقام',
            'identification_number.required' => 'حقل رقم الهوية مطلوب',
            'identification_number.regex' => 'حقل رقم الهوية يجب أن يكون رقم',
            'identification_number.unique' => 'رقم الهوية المدخل موجود مسبقا',
            'identification_number.min' => 'يجب أن لا يقل طول رقم الهوية عن 9 أرقام',
            'identification_number.max' => 'يجب أن لا يزيد طول رقم الهوية عن 9 أرقام',
            'recitation_level.required' => 'أخر دورة أحكام مطلوب',
            'economic_situation.required' => 'الوضع المادي مطلوب',
            'academic_qualification.required' => 'المؤهل العلمي مطلوب',
            'dob.required' => 'حقل تاريخ الميلاد مطلوب',
            'dob.date' => 'حقل تاريخ الميلاد يجب أن يكون من نوع تاريخ',
            'dob.date_format' => 'حقل تاريخ الميلاد يجب أن يكون من نوع تاريخ',
            'grade_id.required' => 'اسم المرحلة مطلوب',
            'group_id.required' => 'اسم الحلقة مطلوب',
            'father_id.required' => 'اسم ولي أمر الطالب مطلوب',
            'father_identification_number.required' => 'حقل رقم الهوية مطلوب',
            'father_identification_number.regex' => 'حقل رقم الهوية يجب أن يكون رقم',
            'father_identification_number.min' => 'يجب أن لا يقل طول رقم الهوية عن 9 أرقام',
            'father_identification_number.max' => 'يجب أن لا يزيد طول رقم الهوية عن 9 أرقام',
            'photo.image' => 'حقل الصورة يجب أن يحتوي على صورة',
            'photo.mimes' => 'يجب أن تكون صيغة الصورة إما jpeg أو png أو jpg',
            'photo.max' => 'يجب أن لا يزيد حجم الصورة عن 1024 كيلو بايت',
            'photo.unique' => 'عذرا يوجد صورة بهذا الاسم مسبقا',
            'country_code.required' => 'كود الدولة مطلوب',
            'country_code.string' => 'كود الدولة يجب أن يكون نص',
            'whatsapp_number.required' => 'حقل رقم الواتس اب مطلوب',
            'whatsapp_number.regex' => 'حقل رقم الواتس اب يجب أن يكون رقم',
            'whatsapp_number.min' => 'يجب أن لا يقل طول رقم الواتس اب عن 10 أرقام',
            'whatsapp_number.max' => 'يجب أن لا يزيد طول رقم الواتس اب عن 10 أرقام',
            'category_permission_id.required' => 'حقل تصنيف الصلاحية مطلوب',
            'category_permission_id.string' => 'يجب إدخال نص في حقل تصنيف الصلاحية',
            'permission_id.required' => 'حقل الصلاحية مطلوب',
            'permission_id.string' => 'يجب إدخال نص في حقل الصلاحية',
            'sponsorships_ids.required' => 'حقل أقسام الكفالات مطلوب',
        ];
    }


    public function all_users_export()
    {
        $users = DB::table('users')
            ->select(['users.name as name', 'identification_number', 'phone', 'email', 'dob', 'economic_situation',
                'recitation_level', 'academic_qualification',
                DB::raw("(GROUP_CONCAT(roles.name,'' SEPARATOR '-')) as `role_name`")])
            ->leftJoin('user_infos', 'users.id', '=', 'user_infos.id')
            ->leftJoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('model_has_roles.model_type', '=', 'App\Models\User')
            ->where('roles.name', '!=', User::STUDENT_ROLE)
            ->where('roles.name', '!=', User::FATHER_ROLE)
            ->where('users.status', '=', true)
            ->orderBy('users.id')
            ->groupBy(['name'])
            ->get();
        return (new AllUsersExport($users))->download('Database of all users of the center.xlsx', Excel::XLSX);
    }
}
