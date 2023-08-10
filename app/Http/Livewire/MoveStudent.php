<?php

namespace App\Http\Livewire;

use App\Models\Grade;
use App\Models\Group;
use App\Models\Student;
use App\Models\Supervisor;
use App\Models\TrackStudentTransfer;
use App\Models\User;
use App\Notifications\MoveStudentForTeacherNotify;
use App\Notifications\NewStudentForTeacherNotify;
use App\Traits\NotificationTrait;
use Illuminate\Support\MessageBag;
use Spatie\Permission\Models\Role;

class MoveStudent extends HomeComponent
{
    use NotificationTrait;

    public $student_name, $grade_name, $teacher_name, $grade_id, $group_id, $ret_group_id;
    public $grades = [], $groups = [];

    public $listeners = [
        'move_student' => 'lunchModalMoveStudent',
        'getGroupsByGradeId' => 'all_Groups',
    ];

    public function mount()
    {
        $this->current_role = auth()->user()->current_role;
        $this->link = 'manage_students/';
        $this->all_Grades();
    }

    public function render()
    {
        return view('livewire.move-student');
    }

    public function lunchModalMoveStudent($student_id)
    {
        $this->modalId = $student_id;
        $student = Student::where('id', $this->modalId)->first();

        $this->student_name = $student->user->name;
        $this->grade_name = $student->grade->name;
        $this->grade_id = $student->grade_id;
        $this->teacher_name = $student->group->teacher->user->name ?? 'لا يوجد محفظ';
        $this->ret_group_id = $student->group_id;
        $this->all_Groups();
        $this->dispatchBrowserEvent('showModalMoveStudent');
    }


    /**
     * @throws \JsonException
     */
    public function move()
    {
        $this->validate(
            ['grade_id' => 'required|string',
                'group_id' => 'required|string'
            ],
            ['grade_id.required' => 'حقل اختيار المرحلة الجديدة مطلوب',
                'grade_id.string' => 'حقل اختيار المرحلة الجديدة يجب أن يكون نص',
                'group_id.required' => 'حقل اختيار المحفظ الجديد مطلوب',
                'group_id.string' => 'حقل اختيار المحفظ الجديد يجب أن يكون نص']
        );

        $student = Student::whereId($this->modalId)->first();
        $messageBag = new MessageBag();
        if ($this->group_id === $this->ret_group_id) {
            $messageBag->add('group_id', "عذرا, لا يمكن اختيار نفس المحفظ.");
            $this->setErrorBag($messageBag);
        } elseif ($student->exam_order->count() > 0) {
            $messageBag->add('student_id', "عذرا يوجد طلبات اختبارات لهذا الطالب مسجلة باسم محفظ الحلقة يجب إجرائها أو حذفها حتى تتمكن من نقل الطالب.");
            $this->setErrorBag($messageBag);
        } else {
            $new_teacher_name = null;
            $new_teacher_id = null;
            $old_teacher_name = null;
            $old_teacher_id = null;

            $new_group = Group::whereId($this->group_id)->first();
            $old_group = Group::whereId($this->ret_group_id)->first();

            if ($new_group->teacher_id !== null) {
                $new_teacher_name = $new_group->teacher->user->name;
                $new_teacher_id = $new_group->teacher_id;
            }

            if ($old_group->teacher_id !== null) {
                $old_teacher_name = $old_group->teacher->user->name;
                $old_teacher_id = $old_group->teacher_id;
            }

            $old_grade_id = $student->grade_id;
            $student_name = $student->user->name;
            $student->update(['grade_id' => $this->grade_id, 'group_id' => $this->group_id]);

          $track_student_transfer =  TrackStudentTransfer::create([
                'student_id' => $this->modalId,
                'old_grade_id' => $old_grade_id,
                'old_teacher_id' => $old_teacher_id,
                'new_grade_id' => $this->grade_id,
                'new_teacher_id' => $new_teacher_id,
                'user_signature_id' => auth()->id(),
                'user_signature_role_id' => Role::findByName($this->current_role)->id,
            ]);

            if ($old_teacher_id !== null) {
                $user = User::whereId($old_teacher_id)->first();
                $user->notify(new MoveStudentForTeacherNotify([
                    'id' => $track_student_transfer->id,
                    'student_id' => $this->modalId,
                    'student_name' => $student_name,
                    'old_teacher_id' => $old_teacher_id,
                    'old_teacher_name' => $old_teacher_name,
                    'new_teacher_name' => $new_teacher_name,
                    'new_teacher_id' => $new_teacher_id,
                ]));
                $title = 'نقل طالب';
                $message = 'تم نقل الطالب ' . $student_name . ' لحلقة المحفظ ' . $new_teacher_name;
                $this->push_notification($message, $title, $this->link . $student->id, [$user->user_fcm_token->device_token ?? null]);
            }

            if ($new_teacher_id !== null) {
                $user = User::whereId($new_teacher_id)->first();
                $user->notify(new NewStudentForTeacherNotify([
                    'student_id' => $this->modalId,
                    'student_name' => $student_name,
                    'old_teacher_id' => $old_teacher_id,
                    'old_teacher_name' => $old_teacher_name,
                    'new_teacher_name' => $new_teacher_name,
                    'new_teacher_id' => $new_teacher_id,
                ]));
                $title = 'طالب جديد';
                $message = 'تم إضافة الطالب ' . $student_name . ' من حلقة المحفظ ' . $old_teacher_name;
                $this->push_notification($message, $title, $this->link . $student->id, [$user->user_fcm_token->device_token ?? null]);
            }

            $this->dispatchBrowserEvent('hideDialog');
            $this->clearForm();
            $this->emit('refresh');

            $this->dispatchBrowserEvent('alert',
                ['type' => 'success', 'message' => 'لقد تم نقل الطالب إلى حلقة جديدة بنجاح.']);
        }
    }

    public function all_Grades()
    {
        if ($this->current_role === 'مشرف') {
            $this->grade_id = Supervisor::where('id', auth()->id())->first()->grade_id;
            $this->all_Groups();
        } else if ($this->current_role === 'أمير المركز') {
            $this->grades = Grade::all();
        }
    }

    public function all_Groups()
    {
        $this->groups = Group::query()->with(['teacher.user'])
            ->where('grade_id', '=', $this->grade_id)
            ->where('type', '=', Group::QURAN_TYPE)->get();
    }

    private function clearForm()
    {
        $this->modalId = '';
        $this->student_name = null;
        $this->grade_name = null;
        $this->teacher_name = null;
        $this->grade_id = null;
        $this->group_id = null;
        $this->ret_group_id = null;
        $this->groups = [];
    }
}
