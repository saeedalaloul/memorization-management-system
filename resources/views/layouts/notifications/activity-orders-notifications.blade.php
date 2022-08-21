@if($value->type == 'App\Notifications\NewActivityOrderForActivitiesSupervisorNotify')
    @if(auth()->user()->current_role == \App\Models\User::ACTIVITIES_SUPERVISOR_ROLE)
        <a href="{{route('user/notifications/read',['id' => $value->id])}}"
           class="dropdown-item">{{Str::limit("طلب نشاط جديد للمحفظ ".$value->data['teacher_name'],35,'..')}}
            <small
                class="float-right text-muted time">{{Carbon\Carbon::parse($value->created_at)->diffForHumans()}}</small></a>
    @endif
@elseif($value->type == 'App\Notifications\AcceptActivityOrderForTeacherNotify')
    @if(auth()->user()->current_role == \App\Models\User::TEACHER_ROLE)
        <a href="{{route('user/notifications/read',['id' => $value->id])}}"
           class="dropdown-item">{{Str::limit("لقد تم اعتماد طلب نشاط ".$value->data['activity_type_name'],38,'..')}}
            <small
                class="float-right text-muted time">{{Carbon\Carbon::parse($value->created_at)->diffForHumans()}}</small></a>
    @endif
@elseif($value->type == 'App\Notifications\AcceptActivityOrderForActivityMemberNotify')
    @if(auth()->user()->current_role == \App\Models\User::ACTIVITY_MEMBER_ROLE)
        <a href="{{route('user/notifications/read',['id' => $value->id])}}"
           class="dropdown-item">{{Str::limit("لقد تم تعيينك منشط لحلقة المحفظ ".$value->data['teacher_name'],35,'..')}}
            <small
                class="float-right text-muted time">{{Carbon\Carbon::parse($value->created_at)->diffForHumans()}}</small></a>
    @endif
@elseif($value->type == 'App\Notifications\RejectionActivityOrderForTeacherNotify')
    @if(auth()->user()->current_role == \App\Models\User::TEACHER_ROLE)
        <a href="{{route('user/notifications/read',['id' => $value->id])}}"
           class="dropdown-item">{{Str::limit("لقد تم رفض طلب نشاط ".$value->data['activity_type_name'],38,'..')}}
            <small
                class="float-right text-muted time">{{Carbon\Carbon::parse($value->created_at)->diffForHumans()}}</small></a>
    @endif
@elseif($value->type == 'App\Notifications\FailureActivityOrderForTeacherNotify')
    @if(auth()->user()->current_role == \App\Models\User::TEACHER_ROLE)
        <a href="{{route('user/notifications/read',['id' => $value->id])}}"
           class="dropdown-item">{{Str::limit("لقد تم اعتماد عدم إجراء نشاط ".$value->data['activity_type_name'],37,'..')}}
            <small
                class="float-right text-muted time">{{Carbon\Carbon::parse($value->created_at)->diffForHumans()}}</small></a>
    @endif
@endif
