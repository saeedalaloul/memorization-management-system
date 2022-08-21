@if(auth()->user()->current_role == \App\Models\User::TEACHER_ROLE)
    @if($value->type == 'App\Notifications\NewStudentWarningForTeacherNotify')
        <a href="{{route('user/notifications/read',['id' => $value->id])}}"
           class="dropdown-item">{{Str::limit("إنذار نهائي جديد للطالب ".$value->data['student_name'],35,'..')}}
            <small class="float-right text-muted time">{{Carbon\Carbon::parse($value->created_at)->diffForHumans()}}</small></a>
    @endif
    @if($value->type == 'App\Notifications\ExpiredStudentWarningForTeacherNotify')
        <a href="{{route('user/notifications/read',['id' => $value->id])}}"
           class="dropdown-item">{{Str::limit("تم إلغاء إنذار الطالب ".$value->data['student_name'],35,'..')}}
            <small class="float-right text-muted time">{{Carbon\Carbon::parse($value->created_at)->diffForHumans()}}</small></a>
    @endif
@endif
