@if(auth()->user()->current_role == \App\Models\User::TEACHER_ROLE)
    @if($value->type == 'App\Notifications\NewExamForTeacherNotify')
        <a href="{{route('user/notifications/read',['id' => $value->id])}}"
           class="dropdown-item">{{Str::limit("اعتماد اختبار جديد للطالب ".$value->data['student_name'],35,'..')}}
            <small
                class="float-right text-muted time">{{Carbon\Carbon::parse($value->created_at)->diffForHumans()}}</small></a>
    @elseif($value->type == 'App\Notifications\ImproveExamForTeacherNotify')
        <a href="{{route('user/notifications/read',['id' => $value->id])}}"
           class="dropdown-item">{{Str::limit("اعتماد اختبار تحسين درجة للطالب ".$value->data['student_name'],35,'..')}}
            <small
                class="float-right text-muted time">{{Carbon\Carbon::parse($value->created_at)->diffForHumans()}}</small></a>
    @endif
@endif
