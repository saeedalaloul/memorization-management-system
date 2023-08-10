@if(auth()->user()->current_role == \App\Models\User::TEACHER_ROLE)
    @if($value->type == 'App\Notifications\NewStudentForTeacherNotify')
        <a href="{{route('user/notifications/read',['id' => $value->id])}}"
           class="dropdown-item">{{Str::limit("تم إضافة الطالب ".$value->data['student_name'] .' من حلقة المحفظ ' .$value->data['old_teacher_name'],35,'..')}}
            <small
                class="float-right text-muted time">{{Carbon\Carbon::parse($value->created_at)->diffForHumans()}}</small></a>
    @endif
    @if($value->type == 'App\Notifications\MoveStudentForTeacherNotify')
        <a href="{{route('user/notifications/read',['id' => $value->id])}}"
           class="dropdown-item">{{Str::limit("تم نقل الطالب ".$value->data['student_name'] .' لحلقة المحفظ ' .$value->data['new_teacher_name'],35,'..')}}
            <small
                class="float-right text-muted time">{{Carbon\Carbon::parse($value->created_at)->diffForHumans()}}</small></a>
    @endif
@endif
