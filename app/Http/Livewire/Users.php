<?php

namespace App\Http\Livewire;

use App\Exports\UsersExport;
use App\Models\Father;
use App\Models\Grade;
use App\Models\Group;
use App\Models\LowerSupervisor;
use App\Models\Student;
use App\Models\Supervisor;
use App\Models\Teacher;
use App\Models\Tester;
use App\Models\User;
use Exception;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\MessageBag;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Excel;
use Spatie\Permission\Models\Role;

class Users extends Component
{
    use WithPagination, WithFileUploads;

    public $email;
    public $phone;
    public $identification_number;
    public $dob;
    public $address;
    public $photo;
    public $user_permissions;
    public $roles, $ret_Roles, $grades, $groups;
    public $role_id, $searchRoleId;
    public $grade_id, $group_id, $father_id, $father_name, $father_identification_number;
    public $show_table = true, $catchError, $successMessage, $tab_id;
    public $modalId, $name, $password, $password_confirm, $process_type;
    public $sortBy = 'id', $sortDirection = 'desc', $perPage = 10, $search = '';
    protected $paginationTheme = 'bootstrap';

    public function render()
    {
        $this->getDataByRoleUser();
        $this->all_Groups();
        return view('livewire.users', ['users' => $this->all_Users()]);
    }


    public function update_permission($permission)
    {
        $user = User::find($this->modalId);
        if ($user->hasDirectPermission($permission)) {
            $user->revokePermissionTo($permission);
        } else {
            $user->givePermissionTo($permission);
        }
    }

    public function update_index_tab($id)
    {
        $this->tab_id = $id;
    }

    public function getDataByRoleUser()
    {
        if ($this->role_id) {
            if ($this->roles->firstWhere('id', $this->role_id)) {
                $role_name = $this->roles->firstWhere('id', $this->role_id)->name;
                if ($role_name == "مشرف") {
                    toastr()->success('ejiorejo');
                    $supervisor = Supervisor::find($this->modalId);
                    if ($supervisor) {
                        if ($this->grade_id == null) {
                            $this->grade_id = $supervisor->grade_id;
                        }
                    }
                } else if ($role_name == "إداري") {
                    $lower_supervisor = LowerSupervisor::find($this->modalId);
                    if ($lower_supervisor) {
                        if ($this->grade_id == null) {
                            $this->grade_id = $lower_supervisor->grade_id;
                        }
                    }
                } else if ($role_name == "محفظ") {
                    $teacher = Teacher::find($this->modalId);
                    if ($teacher) {
                        if ($this->grade_id == null) {
                            $this->grade_id = $teacher->grade_id;
                        }
                    }
                } else if ($role_name == "طالب") {
                    $student = Student::find($this->modalId);
                    if ($student) {
                        if ($this->grade_id == null) {
                            $this->grade_id = $student->grade_id;
                        }
                        if ($this->group_id == null) {
                            $this->group_id = $student->group_id;
                        }
                        if ($this->father_id == null) {
                            $this->father_id = $student->father_id;
                        }
                        if ($this->father_name == null) {
                            $this->father_name = $student->father->user->name;
                        }
                        if ($this->father_identification_number == null) {
                            $this->father_identification_number = $student->father->user->identification_number;
                        }
                    }
                }
            }
        }
    }

    public function storeOrUpdateUserRole()
    {
        if ($this->role_id) {
            if ($this->roles->firstWhere('id', $this->role_id)) {
                $role_name = $this->roles->firstWhere('id', $this->role_id)->name;
                if ($role_name == "أمير المركز") {
                    $user = User::find($this->modalId);
                    if ($user) {
                        $user->assignRole([$this->role_id]);
                        $this->dispatchBrowserEvent('alert',
                            ['type' => 'success', 'message' => 'تمت عملية تعيين دور أمير المركز إلى المستخدم بنجاح.']);
                        $this->modalFormReset();
                        $this->show_table = true;
                    }
                } else if ($role_name == "مشرف الإختبارات") {
                    $user = User::find($this->modalId);
                    if ($user) {
                        $user->assignRole([$this->role_id]);
                        $this->dispatchBrowserEvent('alert',
                            ['type' => 'success', 'message' => 'تمت عملية تعيين دور مشرف الإختبارات إلى المستخدم بنجاح.']);
                        $this->modalFormReset();
                        $this->show_table = true;
                    }
                } else if ($role_name == "مختبر") {
                    $user = User::find($this->modalId);
                    if ($user) {
                        Tester::updateOrCreate(['id' => $this->modalId]);
                        $user->assignRole([$this->role_id]);
                        $this->dispatchBrowserEvent('alert',
                            ['type' => 'success', 'message' => 'تمت عملية تعيين دور مختبر إلى المستخدم بنجاح.']);
                        $this->modalFormReset();
                        $this->show_table = true;
                    }
                } else if ($role_name == "مشرف") {
                    $user = User::find($this->modalId);
                    if ($user) {
                        $supervisor = Supervisor::find($this->modalId);
                        if ($supervisor == null) {
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
                        $this->show_table = true;
                    }
                } else if ($role_name == "إداري") {
                    $user = User::find($this->modalId);
                    if ($user) {
                        $lowerSupervisor = LowerSupervisor::find($this->modalId);
                        if ($lowerSupervisor == null) {
                            LowerSupervisor::create(['id' => $this->modalId, 'grade_id' => $this->grade_id]);
                            $this->dispatchBrowserEvent('alert',
                                ['type' => 'success', 'message' => 'تمت عملية تعيين دور إداري إلى المستخدم بنجاح.']);
                        } else {
                            $lowerSupervisor->update(['grade_id' => $this->grade_id]);
                            $this->dispatchBrowserEvent('alert',
                                ['type' => 'success', 'message' => 'تمت عملية تحديث دور إداري إلى المستخدم بنجاح.']);
                        }
                        $user->assignRole([$this->role_id]);
                        $this->modalFormReset();
                        $this->show_table = true;
                    }
                } else if ($role_name == "محفظ") {
                    $this->validate(
                        [
                            'grade_id' => 'required',
                        ]);
                    $messageBag = new MessageBag;
                    $teacher = Teacher::find($this->modalId);

                    if ($teacher != null) {
                        if ($this->grade_id != $teacher->grade_id) {
                            if ($teacher->group != null) {
                                $messageBag->add('grade_id', 'عذرا, لم يتم تحديث دور المحفظ لأن لديه حلقة');
                                $this->setErrorBag($messageBag);
                            } else {
                                $teacher->update(['grade_id' => $this->grade_id]);
                                $teacher->user->assignRole([$this->role_id]);
                                $this->dispatchBrowserEvent('alert',
                                    ['type' => 'success', 'message' => 'تمت عملية تحديث دور محفظ إلى المستخدم بنجاح.']);
                                $this->modalFormReset();
                                $this->show_table = true;
                            }
                        } else {
                            $teacher->user->assignRole([$this->role_id]);
                            $this->dispatchBrowserEvent('alert',
                                ['type' => 'success', 'message' => 'تمت عملية تحديث دور محفظ إلى المستخدم بنجاح.']);
                            $this->modalFormReset();
                            $this->show_table = true;
                        }
                    } else {
                        Teacher::create(['id' => $this->modalId, 'grade_id' => $this->grade_id]);
                        $user = User::find($this->modalId);
                        $user->assignRole([$this->role_id]);
                        $this->dispatchBrowserEvent('alert',
                            ['type' => 'success', 'message' => 'تمت عملية تعيين دور محفظ إلى المستخدم بنجاح.']);
                        $this->modalFormReset();
                        $this->show_table = true;
                    }

                } else if ($role_name == "طالب") {
                    $this->validate(
                        [
                            'grade_id' => 'required',
                            'group_id' => 'required',
                            'father_identification_number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:9|max:9',
                            'father_id' => 'required',
                        ]);

                    $messageBag = new MessageBag;
                    $student = Student::find($this->modalId);
                    if ($student != null) {
                        if ($this->group_id != $student->group_id) {
                            $teacher_found = Teacher::find($this->modalId);
                            if ($teacher_found != null && $teacher_found->group != null
                                && $teacher_found->group->id == $this->group_id) {
                                $messageBag->add('group_id', 'عذرا, لا يمكن تغيير الحلقة للطالب لأنه محفظ في نفس الحلقة');
                                $this->setErrorBag($messageBag);
                            } else {
                                if ($student->exam_order->count() > 0) {
                                    $messageBag->add('group_id', 'عذرا, لا يمكن تغيير الحلقة للطالب بسبب وجود طلبات اختبارات يجب إجرائها أو حذفها');
                                    $this->setErrorBag($messageBag);
                                } else {
                                    $student->update(['grade_id' => $this->grade_id, 'group_id' => $this->group_id, 'father_id' => $this->father_id,]);
                                    $student->user->assignRole([$this->role_id]);
                                    $this->dispatchBrowserEvent('alert',
                                        ['type' => 'success', 'message' => 'تمت عملية تحديث دور طالب إلى المستخدم بنجاح.']);
                                    $this->modalFormReset();
                                    $this->show_table = true;
                                }
                            }
                        } else {
                            if ($student->group->grade_id == $this->grade_id) {
                                $student->update(['grade_id' => $this->grade_id, 'group_id' => $this->group_id, 'father_id' => $this->father_id,]);
                                $student->user->assignRole([$this->role_id]);
                                $this->dispatchBrowserEvent('alert',
                                    ['type' => 'success', 'message' => 'تمت عملية تحديث دور طالب إلى المستخدم بنجاح.']);
                                $this->modalFormReset();
                                $this->show_table = true;
                            } else {
                                $messageBag->add('grade_id', 'عذرا, لا يمكن تغيير المرحلة لأن الحلقة ليست في نفس المرحلة');
                                $this->setErrorBag($messageBag);
                            }
                        }
                    } else {
                        $teacher_found = Teacher::find($this->modalId);
                        if ($teacher_found != null && $teacher_found->group != null
                            && $teacher_found->group->id == $this->group_id) {
                            $messageBag->add('group_id', 'عذرا, لا يمكن اختيار الحلقة للطالب لأنه محفظ في نفس الحلقة');
                            $this->setErrorBag($messageBag);
                        } else {
                            Student::create([
                                'id' => $this->modalId,
                                'grade_id' => $this->grade_id,
                                'group_id' => $this->group_id,
                                'father_id' => $this->father_id,
                            ]);
                            $user = User::find($this->modalId);
                            $user->assignRole([$this->role_id]);
                            $this->dispatchBrowserEvent('alert',
                                ['type' => 'success', 'message' => 'تمت عملية تعيين دور طالب إلى المستخدم بنجاح.']);
                            $this->modalFormReset();
                            $this->show_table = true;
                        }
                    }
                }
            }
        }
    }

    public function pullUserRole()
    {
        if ($this->role_id) {
            if ($this->roles->firstWhere('id', $this->role_id)) {
                $role_name = $this->roles->firstWhere('id', $this->role_id)->name;
                if ($role_name == "أمير المركز") {
                    if (auth()->id() != $this->modalId) {
                        $user = User::find($this->modalId);
                        $user?->removeRole($this->role_id);
                        $this->dispatchBrowserEvent('alert',
                            ['type' => 'success', 'message' => 'تمت عملية سحب دور أمير المركز من المستخدم بنجاح.']);
                        $this->modalFormReset();
                        $this->show_table = true;
                    } else {
                        $this->catchError = 'عذرا لا يمكنك سحب صلاحيتك أمير المركز ..';
                    }
                } else if ($role_name == "مشرف الإختبارات") {
                    $user = User::find($this->modalId);
                    $user?->removeRole($this->role_id);
                    $this->dispatchBrowserEvent('alert',
                        ['type' => 'success', 'message' => 'تمت عملية سحب دور مشرف الإختبارات من المستخدم بنجاح.']);
                    $this->modalFormReset();
                    $this->show_table = true;
                } else if ($role_name == "مختبر") {
                    $user = User::find($this->modalId);
                    $user?->removeRole($this->role_id);
                    Tester::destroy($this->modalId);
                    $this->dispatchBrowserEvent('alert',
                        ['type' => 'success', 'message' => 'تمت عملية سحب دور مختبر من المستخدم بنجاح.']);
                    $this->modalFormReset();
                    $this->show_table = true;
                } else if ($role_name == "مشرف") {
                    $user = User::find($this->modalId);
                    $user?->removeRole($this->role_id);
                    Supervisor::destroy($this->modalId);
                    $this->dispatchBrowserEvent('alert',
                        ['type' => 'success', 'message' => 'تمت عملية سحب دور مشرف من المستخدم بنجاح.']);
                    $this->modalFormReset();
                    $this->show_table = true;
                } else if ($role_name == "إداري") {
                    $user = User::find($this->modalId);
                    $user?->removeRole($this->role_id);
                    LowerSupervisor::destroy($this->modalId);
                    $this->dispatchBrowserEvent('alert',
                        ['type' => 'success', 'message' => 'تمت عملية سحب دور إداري من المستخدم بنجاح.']);
                    $this->modalFormReset();
                    $this->show_table = true;
                } else if ($role_name == "محفظ") {
                    $this->pullTeacherRole();
                } else if ($role_name == "طالب") {
                    $this->pullStudentRole();
                }
            }
        }
    }

    public function pullTeacherRole()
    {
        $messageBag = new MessageBag();
        $teacher = Teacher::find($this->modalId);
        if ($teacher->group != null) {
            $messageBag->add('role_id', 'عذرا, لم يتم سحب دور المحفظ لأن لديه حلقة');
            $this->setErrorBag($messageBag);
        } else {
            if ($teacher->exam_order->count() > 0) {
                $messageBag->add('role_id', 'عذرا, لا يمكن سحب دور المحفظ بسبب وجود طلبات اختبارات يجب إجرائها أو حذفها');
                $this->setErrorBag($messageBag);
            } else {
                if ($teacher->exam->count() > 0) {
                    $messageBag->add('role_id', 'عذرا, لا يمكن سحب دور المحفظ إطلاقا بسبب وجود اختبارات قرآنية مسجلة باسمه');
                    $this->setErrorBag($messageBag);
                } else {
                    if ($teacher->attendance->count() > 0) {
                        $messageBag->add('role_id', 'عذرا, لا يمكن سحب دور المحفظ بسبب وجود سجل حضور وغياب لديه');
                        $this->setErrorBag($messageBag);
                    } else {
                        if ($teacher->attendance_student->count() > 0) {
                            $messageBag->add('role_id', 'عذرا, لا يمكن سحب دور المحفظ بسبب وجود سجل حضور وغياب مسجل باسمه لدى طلابه');
                            $this->setErrorBag($messageBag);
                        } else {
                            if ($teacher->student_daily_preservation->count() > 0) {
                                $messageBag->add('role_id', 'عذرا, لا يمكن سحب دور المحفظ بسبب وجود سجل متابعة الحفظ والمراجعة مسجل باسمه لدى طلابه');
                                $this->setErrorBag($messageBag);
                            } else {
                                $user = User::find($this->modalId);
                                $user?->removeRole($this->role_id);
                                Teacher::destroy($this->modalId);
                                $this->dispatchBrowserEvent('alert',
                                    ['type' => 'success', 'message' => 'تمت عملية سحب دور محفظ من المستخدم بنجاح.']);
                                $this->modalFormReset();
                                $this->show_table = true;
                            }
                        }
                    }
                }
            }
        }
    }

    public function pullStudentRole()
    {
        $messageBag = new MessageBag();
        $student = Student::find($this->modalId);
        if ($student->exam_order->count() > 0) {
            $messageBag->add('role_id', 'عذرا, لا يمكن سحب دور الطالب بسبب وجود طلبات اختبارات يجب إجرائها أو حذفها');
            $this->setErrorBag($messageBag);
        } else {
            if ($student->exam->count() > 0) {
                $messageBag->add('role_id', 'عذرا, لا يمكن سحب دور الطالب إطلاقا بسبب وجود اختبارات قرآنية مسجلة باسمه');
                $this->setErrorBag($messageBag);
            } else {
                if ($student->attendance->count() > 0) {
                    $messageBag->add('role_id', 'عذرا, لا يمكن سحب دور الطالب بسبب وجود سجل حضور وغياب لديه');
                    $this->setErrorBag($messageBag);
                } else {
                    if ($student->daily_preservation->count() > 0) {
                        $messageBag->add('role_id', 'عذرا, لا يمكن سحب دور الطالب بسبب وجود سجل متابعة الحفظ والمراجعة مسجل باسمه');
                        $this->setErrorBag($messageBag);
                    } else {
                        $user = User::find($this->modalId);
                        $user?->removeRole($this->role_id);
                        Student::destroy($this->modalId);
                        $this->dispatchBrowserEvent('alert',
                            ['type' => 'success', 'message' => 'تمت عملية سحب دور طالب من المستخدم بنجاح.']);
                        $this->modalFormReset();
                        $this->show_table = true;
                    }
                }
            }
        }
    }


    public function resetMessage()
    {
        $this->successMessage = null;
        $this->catchError = null;
    }

    public function findStudentFather()
    {
        if ($this->father_identification_number) {
            if (strlen($this->father_identification_number) == 9) {
                $father = Father::query()
                    ->whereRelation('user', 'identification_number', '=', $this->father_identification_number)
                    ->first();
                if ($father != null) {
                    $this->father_id = $father->id;
                    $this->father_name = $father->user->name;
                    $this->successMessage = 'لقد تم العثور على رقم هوية ولي الأمر بنجاح...';
                } else {
                    $this->father_id = null;
                    $this->father_name = null;
                    $this->successMessage = null;
                }
            } else {
                $this->father_id = null;
                $this->father_name = null;
                $this->successMessage = null;
            }
        }
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName, [
            'password' => 'required|min:8|max:10',
            'password_confirm' => 'required|same:password',
            'email' => 'required|email|unique:users,email,' . $this->modalId,
            'name' => 'required|string|unique:users,name,' . $this->modalId,
            'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:10|unique:users,phone,' . $this->modalId,
            'identification_number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:9|max:9|unique:users,identification_number,' . $this->modalId,
            'father_identification_number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:9|max:9',
            'dob' => 'required|date|date_format:Y-m-d',
            'address' => 'required',
            'photo' => 'image|mimes:jpeg,png,jpg|max:2048',
            'grade_id' => 'required',
            'group_id' => 'required',
            'father_id' => 'required',
        ]);
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
            'phone.required' => 'حقل رقم الجوال مطلوب',
            'phone.unique' => 'رقم الجوال المدخل موجود مسبقا',
            'phone.min' => 'يجب أن لا يقل طول رقم الجوال عن 10 أرقام',
            'phone.max' => 'يجب أن لا يزيد طول رقم الجوال عن 10 أرقام',
            'identification_number.required' => 'حقل رقم الهوية مطلوب',
            'identification_number.regex' => 'حقل رقم الهوية يجب أن يكون رقم',
            'identification_number.unique' => 'رقم الهوية المدخل موجود مسبقا',
            'identification_number.min' => 'يجب أن لا يقل طول رقم الهوية عن 9 أرقام',
            'identification_number.max' => 'يجب أن لا يزيد طول رقم الهوية عن 9 أرقام',
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
            'address.required' => 'حقل العنوان مطلوب',
            'photo.image' => 'حقل الصورة يجب أن يحتوي على صورة',
            'photo.mimes' => 'يجب أن تكون صيغة الصورة إما jpeg أو png أو jpg',
            'photo.max' => 'يجب أن لا يزيد حجم الصورة عن 2048 كيلو بايت',
        ];
    }

    public function rules()
    {
        return [
            'password' => 'required|min:8|max:10',
            'password_confirm' => 'required|same:password'
        ];
    }

    public function resetPasswordUser()
    {
        $this->validate();
        DB::beginTransaction();
        try {
            User::find($this->modalId)->update([
                'password' => Hash::make($this->password)
            ]);
            $this->modalFormReset();
            $this->show_table = true;
            $this->dispatchBrowserEvent('alert',
                ['type' => 'success', 'message' => 'تمت عملية إعادة تعيين كلمة المرور بنجاح.']);
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $this->catchError = $e->getMessage();
        }
    }

    public function modelUser()
    {
        if ($this->modalId == null) {
            return [
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'phone' => $this->phone,
                'identification_number' => $this->identification_number,
                'address' => $this->address,
                'dob' => $this->dob,
            ];
        } else {
            return [
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'identification_number' => $this->identification_number,
                'address' => $this->address,
                'dob' => $this->dob,
            ];
        }
    }

    public function export()
    {
        $users = [];
        foreach ($this->all_Users()->toArray()['data'] as $key => $item) {
            $user = ['id' => $key + 1, 'name' => $item['name']
                , 'email' => $item['email'], 'phone' => intval($item['phone'])
                , 'identification_number' => intval($item['identification_number']), 'address' => $item['address']
                , 'dob' => $item['dob']];
            $users[$key] = $user;
//            if ($item['status'] == 0) {
//                $users[$key]['status'] = "معلق";
//            } else {
//                $users[$key]['status'] = "مفعل";
//            }
        }
        return (new UsersExport($users))->download('users.xlsx', Excel::XLSX);
    }


    public function loadModalData($id, $process_type)
    {
        $this->modalFormReset();
        $this->process_type = $process_type;
        $data = User::find($id);
        $this->modalId = $data->id;
        $this->name = $data->name;
        $this->show_table = false;
        if ($process_type == 'edit') {
            $this->email = $data->email;
            $this->phone = $data->phone;
            $this->identification_number = $data->identification_number;
            $this->address = $data->address;
            $this->dob = $data->dob;
        } else if ($process_type == 'edit_permission') {
            $this->user_permissions = $data->getDirectPermissions()->toArray();
        } else {
            if ($data->roles->count() == 1) {
                if ($data->roles[0]->name != "ولي أمر الطالب") {
                    if ($data->roles[0]->name == "طالب") {
                        $this->all_Roles(2); // سيتم اظهار صلاحية الطالب والمحفظ
                        $this->ret_Roles = $data->roles;
                    } else if ($data->roles[0]->name == "محفظ") {
                        $this->all_Roles(2); // سيتم اظهار صلاحية الطالب والمحفظ
                        $this->ret_Roles = $data->roles;
                    } else {
                        $this->all_Roles(0); // سيتم اظهار كافة الصلاحيات ما عدا صلاحية ولي الأمر والطالب
                        $this->ret_Roles = $data->roles;
                    }
                } else {
                    $this->modalFormReset();
                    $this->show_table = true;
                }
            } else if ($data->roles->count() > 1) {
                $this->all_Roles(1); // سيتم اظهار كافة الصلاحيات ما عدا صلاحية ولي الأمر
                $this->ret_Roles = $data->roles;
            } else {
                $this->all_Roles(1); // سيتم اظهار كافة الصلاحيات ما عدا صلاحية ولي الأمر
                $this->ret_Roles = $data->roles;
            }
        }
    }

    public function mount()
    {
        $this->all_Grades();
        $this->all_Roles(-1);
        $this->tab_id = "grade-02";
    }

    public function activeEmail($id)
    {
        $user = User::find($id);
        if ($user != null) {
            if ($user->roles != null && $user->roles->where('name', 'أمير المركز')->first() != null
                && $id == auth()->id()) {
                $this->catchError = 'عذرا لا يمكن لأمير المركز تعطيل التحقق من بريده الإلكتروني';
            } else {
                if ($user->hasVerifiedEmail()) {
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
    }

    public function activeAccount($id)
    {
        $user = User::find($id);
        if ($user != null) {
            if ($user->roles != null && $user->roles->where('name', 'أمير المركز')->first() != null
                && $id == auth()->id()) {
                $this->catchError = 'عذرا لا يمكن لأمير المركز تعطيل حسابه في النظام';
            } else {
                if ($user->status) {
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
    }

    public
    function modalFormReset()
    {
        $this->resetValidation();
        $this->roles = null;
        $this->ret_Roles = null;
        $this->user_permissions = null;
        $this->tab_id = "grade-02";
        $this->modalId = null;
        $this->name = null;
        $this->email = null;
        $this->phone = null;
        $this->identification_number = null;
        $this->dob = null;
        $this->address = null;
        $this->photo = null;
        $this->show_table = false;
        $this->process_type = null;
        $this->catchError = null;
        $this->password = null;
        $this->password_confirm = null;
        $this->search = null;
        $this->grade_id = null;
        $this->group_id = null;
        $this->father_name = null;
        $this->father_id = null;
        $this->father_identification_number = null;
        $this->role_id = null;
    }

    public
    function showformadd($isShow)
    {
        $this->show_table = $isShow;
    }

    public
    function store()
    {
        $this->validate(
            [
                'name' => 'required|string|unique:users,name,' . $this->modalId,
                'email' => 'required|email|unique:users,email,' . $this->modalId,
                'password' => 'required|min:8|max:10',
                'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:10|unique:users,phone,' . $this->modalId,
                'identification_number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:9|max:9|unique:users,identification_number,' . $this->modalId,
                'address' => 'required',
                'dob' => 'required|date|date_format:Y-m-d',
            ]
        );

        DB::beginTransaction();
        try {
            $user = User::create($this->modelUser());
            if (!empty($this->photo)) {
                $this->photo->storeAs($this->identification_number, $this->photo->getClientOriginalName(), $disk = 'users_images');
                $user->update([
                    'profile_photo_path' => $this->photo->getClientOriginalName(),
                ]);
            }
            $this->modalFormReset();
            $this->dispatchBrowserEvent('alert',
                ['type' => 'error', 'message' => 'تم حفظ معلومات المستخدم بنجاح.']);
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $this->catchError = $e->getMessage();
        }
    }

    public
    function update()
    {
        $this->validate(
            [
                'name' => 'required|string|unique:users,name,' . $this->modalId,
                'email' => 'required|email|unique:users,email,' . $this->modalId,
                'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:10|unique:users,phone,' . $this->modalId,
                'identification_number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:9|max:9|unique:users,identification_number,' . $this->modalId,
                'address' => 'required',
                'dob' => 'required|date|date_format:Y-m-d',
            ]
        );

        DB::beginTransaction();
        try {
            $user = User::where('id', $this->modalId)->first();
            $user->update($this->modelUser());
            if (!empty($this->photo)) {
                $this->photo->storeAs($this->identification_number, $this->photo->getClientOriginalName(), $disk = 'users_images');
                $user->update([
                    'profile_photo_path' => $this->photo->getClientOriginalName(),
                ]);
            }
            $this->modalFormReset();
            $this->show_table = true;
            $this->dispatchBrowserEvent('alert',
                ['type' => 'error', 'message' => 'تم تحديث معلومات المستخدم بنجاح.']);
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $this->catchError = $e->getMessage();
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

    public
    function all_Users()
    {
        if ($this->searchRoleId) {
            return User::query()
                ->whereRelation('roles', 'id', '=', $this->searchRoleId)
                ->search($this->search)
                ->orderBy($this->sortBy, $this->sortDirection)
                ->paginate($this->perPage);
        } else {
            return User::query()
                ->search($this->search)
                ->orderBy($this->sortBy, $this->sortDirection)
                ->paginate($this->perPage);
        }
    }

    public
    function all_Grades()
    {
        $this->grades = Grade::all();
    }

    public
    function all_Groups()
    {
        if ($this->grade_id) {
            $this->groups = Group::query()->where('grade_id', $this->grade_id)->get();
        }
    }

    public function all_Roles($status)
    {
        if ($status == 0) {
            $this->roles = Role::query()->whereNotIn('name', ['طالب', 'ولي أمر الطالب'])->get();
        } else if ($status == 1) {
            $this->roles = Role::query()->whereNotIn('name', ['ولي أمر الطالب'])->get();
        } else if ($status == 2) {
            $this->roles = Role::query()->whereIn('name', ['طالب', 'محفظ'])->get();
        } else {
            $this->roles = Role::all();
        }
    }
}
