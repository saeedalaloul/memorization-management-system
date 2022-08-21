<?php

namespace App\Http\Livewire;

use App\Models\BoxComplaintSuggestion;
use App\Models\Teacher;
use App\Models\User;
use App\Notifications\NewBoxComplaintSuggestionNotify;
use App\Notifications\ReplayBoxComplaintSuggestionNotify;
use App\Traits\NotificationTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\MessageBag;
use Spatie\Permission\Models\Role;

class BoxComplaintSuggestions extends HomeComponent
{
    use NotificationTrait;

    public $boxComplaintSuggestion, $subject, $reply, $category, $role_id;
    public $categories, $roles;

    public function render()
    {
        return view('livewire.box-complaint-suggestions', [
            'box_complaint_suggestions' => $this->all_Box_Complaint_Suggestions(),]);
    }

    public function mount()
    {
        $this->current_role = auth()->user()->current_role;
        $this->all_Roles();
    }

    public function detailsShow($id)
    {
        $this->process_type = 'detailsShow';
        $this->boxComplaintSuggestion = BoxComplaintSuggestion::where('id', $id)->first();
        if ($this->boxComplaintSuggestion->receiver_id == auth()->id()
            && $this->boxComplaintSuggestion->subject_read_at == null) {
            $this->boxComplaintSuggestion->update(['subject_read_at' => Carbon::now()]);
        } elseif ($this->boxComplaintSuggestion->sender_id == auth()->id()
            && $this->boxComplaintSuggestion->reply != null
            && $this->boxComplaintSuggestion->reply_read_at == null) {
            $this->boxComplaintSuggestion->update(['reply_read_at' => Carbon::now()]);
        }
    }


    public function complaintReply($id)
    {
        $this->boxComplaintSuggestion = BoxComplaintSuggestion::where('id', $id)->first();
        if ($this->boxComplaintSuggestion->receiver_id == auth()->id()) {
            if ($this->boxComplaintSuggestion->subject_read_at == null) {
                $this->boxComplaintSuggestion->update(['subject_read_at' => Carbon::now()]);
            }
            $this->process_type = 'complaintReply';
        }
    }

    public function storeReplyComplaint()
    {
        $this->validate([
            'reply' => 'required|string',
        ]);

        $this->boxComplaintSuggestion->update([
            'reply' => $this->reply,
        ]);

        $this->dispatchBrowserEvent('alert',
            ['type' => 'success', 'message' => 'تمت عملية الرد على الشكوى/الاقتراح بنجاح.']);

        $this->boxComplaintSuggestion->sender->notify(new ReplayBoxComplaintSuggestionNotify($this->boxComplaintSuggestion));
        if ($this->boxComplaintSuggestion->category == BoxComplaintSuggestion::COMPLAINT_CATEGORY) {
            $title = "الرد على شكوى";
            $message = "لقد تم الرد على الشكوى التي قدمتها إلى: " . $this->boxComplaintSuggestion->receiver->name . " يرجى مراجعة الرد.";
        } elseif ($this->boxComplaintSuggestion->category == BoxComplaintSuggestion::SUGGESTION_CATEGORY) {
            $title = "الرد على اقتراح";
            $message = "لقد تم الرد على الاقتراح التي قدمته إلى: " . $this->boxComplaintSuggestion->receiver->name . " يرجى مراجعة الرد.";
        } else {
            $title = "الرد على فكرة";
            $message = "لقد تم الرد على الفكرة التي قدمتها إلى: " . $this->boxComplaintSuggestion->receiver->name . " يرجى مراجعة الرد.";
        }
        $this->push_notification($message, $title, [$this->boxComplaintSuggestion->sender->user_fcm_token->device_token]);

        $this->clearForm();
        $this->process_type = '';
    }


    public function rules()
    {
        return [
            'category' => 'required|string',
            'role_id' => 'required|array|min:1',
            'subject' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'category.required' => 'حقل التصنيف مطلوب',
            'category.string' => 'يجب اختيار صالح لحقل التصنيف',
            'role_id.required' => 'حقل جهة الشكوى مطلوب',
            'role_id.array' => 'يجب اختيار صالح لحقل الشكوى',
            'role_id.min' => 'يجب اختيار جهة واحدة على الأقل',
            'subject.required' => 'حقل الموضوع مطلوب',
            'subject.string' => 'يجب إدخال صالح لحقل الموضوع',
            'reply.required' => 'حقل الرد مطلوب',
            'reply.string' => 'يجب إدخال صالح لحقل الرد',
        ];
    }

    public function all_Box_Complaint_Suggestions()
    {
        return BoxComplaintSuggestion::query()
            ->with(['sender', 'receiver'])
            ->when($this->current_role == 'مشرف الرقابة' || $this->current_role == 'أمير المركز' || $this->current_role == 'مشرف', function ($q, $v) {
                $q->where('sender_id', auth()->id())
                    ->OrWhere('receiver_id', auth()->id());
            })
            ->when($this->current_role == 'محفظ', function ($q, $v) {
                $q->where('sender_id', auth()->id());
            })
            ->when(!empty(strval(\Request::segment(2)) && strval(\Request::segment(2)) != 'message'), function ($q, $v) {
                $q->where('id', \Request::segment(2));
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function store()
    {
        $this->validate();

        $boxComplaintSuggestions = [];
        $arr_external_user_ids = [];

        foreach ($this->role_id as $key => $role_id) {
            $role = $this->roles->where('id', $this->role_id[$key])
                ->first();
            if ($role != null) {
                if ($role->name == 'مشرف' && $this->current_role == 'محفظ') {
                    $user = $role->users()
                        ->whereRelation('supervisor', 'grade_id', '=', Teacher::find(auth()->id())->grade_id)
                        ->select('id')->first();
                } else if ($role->name != 'مشرف') {
                    $user = $role->users()
                        ->select('id')->first();
                }
                if (isset($user)) {
                    $receiver_id = $user->id;
                    if ($receiver_id != auth()->id()) {
                        array_push($arr_external_user_ids, $receiver_id);
                        array_push($boxComplaintSuggestions, [
                            'datetime' => Carbon::now(),
                            'category' => $this->category,
                            'subject' => $this->subject,
                            'sender_id' => auth()->id(),
                            'receiver_id' => $receiver_id,
                        ]);
                    }
                }
            }
        }
        DB::beginTransaction();
        try {
            if (empty($boxComplaintSuggestions)) {
                $messageBag = new MessageBag();
                $messageBag->add('role_id', 'عذرا لا يمكن تقديم الشكوى إلى نفسك أو ليس لديك الصلاحية للقيام بذلك!');
                $this->setErrorBag($messageBag);
            } else {
                foreach ($boxComplaintSuggestions as $boxComplaintSuggestion) {
                    $boxComplaintSuggestion = BoxComplaintSuggestion::create($boxComplaintSuggestion);
                    $boxComplaintSuggestion->receiver->notify(new NewBoxComplaintSuggestionNotify($boxComplaintSuggestion));
                    if ($boxComplaintSuggestion->category == BoxComplaintSuggestion::COMPLAINT_CATEGORY) {
                        $title = "شكوى جديدة";
                        $message = "لقد تم تقديم شكوى جديدة من قبل المحفظ: " . $boxComplaintSuggestion->sender->name . " يرجى مراجعة الشكوى والرد عليها.";
                    } elseif ($boxComplaintSuggestion->category == BoxComplaintSuggestion::SUGGESTION_CATEGORY) {
                        $title = "اقتراح جديد";
                        $message = "لقد تم تقديم اقتراح جديد من قبل المحفظ: " . $boxComplaintSuggestion->sender->name . " يرجى مراجعة الاقتراح والرد عليه.";
                    } else {
                        $title = "فكرة جديدة";
                        $message = "لقد تم تقديم فكرة جديدة من قبل المحفظ: " . $boxComplaintSuggestion->sender->name . " يرجى مراجعة الفكرة والرد عليها.";
                    }
                    $this->push_notification($message, $title, [$boxComplaintSuggestion->receiver->user_fcm_token->device_token]);
                }

                $this->dispatchBrowserEvent('alert',
                    ['type' => 'success', 'message' => 'تمت عملية تقديم الشكوى/الاقتراح بنجاح.']);
                DB::commit();
                $this->clearForm();
            }
        } catch (\Exception $e) {
            DB::rollback();
            $this->catchError = $e->getMessage();
        }
    }

    public function all_Roles()
    {
        $this->roles = Role::query()->whereIn('name', [User::ADMIN_ROLE, User::OVERSIGHT_SUPERVISOR_ROLE, User::SUPERVISOR_ROLE])->get();
    }

    private function clearForm()
    {
        $this->subject = null;
        $this->category = null;
        $this->role_id = null;
        $this->reply = null;
        $this->boxComplaintSuggestion = null;
    }
}
