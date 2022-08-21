@if($value->type == 'App\Notifications\NewExamOrderForExamsSupervisorNotify')
    @if(auth()->user()->current_role == \App\Models\User::EXAMS_SUPERVISOR_ROLE)
        <a href="{{route('user/notifications/read',['id' => $value->id])}}"
           class="dropdown-item">{{Str::limit("طلب اختبار جديد للطالب ".$value->data['student_name'],35,'..')}}
            <small
                class="float-right text-muted time">{{Carbon\Carbon::parse($value->created_at)->diffForHumans()}}</small></a>
    @endif
@elseif($value->type == 'App\Notifications\ImproveExamOrderForExamsSupervisorNotify')
    @if(auth()->user()->current_role == \App\Models\User::EXAMS_SUPERVISOR_ROLE)
        <a href="{{route('user/notifications/read',['id' => $value->id])}}"
           class="dropdown-item">{{Str::limit("طلب تحسين درجة اختبار للطالب ".$value->data['student_name'],35,'..')}}
            <small
                class="float-right text-muted time">{{Carbon\Carbon::parse($value->created_at)->diffForHumans()}}</small></a>
    @endif
@elseif($value->type == 'App\Notifications\AcceptExamOrderForTeacherNotify')
    @if(auth()->user()->current_role == \App\Models\User::TEACHER_ROLE)
        <a href="{{route('user/notifications/read',['id' => $value->id])}}"
           class="dropdown-item">{{Str::limit("لقد تم اعتماد طلب اختبار الطالب ".$value->data['student_name'],38,'..')}}
            <small
                class="float-right text-muted time">{{Carbon\Carbon::parse($value->created_at)->diffForHumans()}}</small></a>
    @endif
@elseif($value->type == 'App\Notifications\AcceptExamOrderForTesterNotify')
    @if(auth()->user()->current_role == \App\Models\User::TESTER_ROLE)
        <a href="{{route('user/notifications/read',['id' => $value->id])}}"
           class="dropdown-item">{{Str::limit("لقد تم تعيينك مختبر الطالب ".$value->data['student_name'],35,'..')}}
            <small
                class="float-right text-muted time">{{Carbon\Carbon::parse($value->created_at)->diffForHumans()}}</small></a>
    @endif
@elseif($value->type == 'App\Notifications\RejectionExamOrderForTeacherNotify')
    @if(auth()->user()->current_role == \App\Models\User::TEACHER_ROLE)
        <a href="{{route('user/notifications/read',['id' => $value->id])}}"
           class="dropdown-item">{{Str::limit("لقد تم رفض طلب اختبار الطالب ".$value->data['student_name'],35,'..')}}
            <small
                class="float-right text-muted time">{{Carbon\Carbon::parse($value->created_at)->diffForHumans()}}</small></a>
    @endif
@elseif($value->type == 'App\Notifications\FailureExamOrderForTeacherNotify')
    @if(auth()->user()->current_role == \App\Models\User::TEACHER_ROLE)
        <a href="{{route('user/notifications/read',['id' => $value->id])}}"
           class="dropdown-item">{{Str::limit("لقد تم اعتماد عدم إجراء اختبار الطالب ".$value->data['student_name'],37,'..')}}
            <small
                class="float-right text-muted time">{{Carbon\Carbon::parse($value->created_at)->diffForHumans()}}</small></a>
    @endif
@endif
