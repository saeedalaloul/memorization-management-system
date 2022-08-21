@if($value->type == 'App\Notifications\NewVisitOrderForOversightMemberNotify')
    @if(auth()->user()->current_role == \App\Models\User::OVERSIGHT_MEMBER_ROLE)
        @if ($value->data['host_type'] == 'App\Models\Teacher')
            <a href="{{route('user/notifications/read',['id' => $value->id])}}"
               class="dropdown-item">{{Str::limit("طلب زيارة جديد للمحفظ ".$value->data['hostname'],33,'..')}}
                <small
                    class="float-right text-muted time">{{Carbon\Carbon::parse($value->created_at)->diffForHumans()}}</small></a>
        @elseif($value->data['host_type'] == 'App\Models\Tester')
            <a href="{{route('user/notifications/read',['id' => $value->id])}}"
               class="dropdown-item">{{Str::limit("طلب زيارة جديد للمختبر ".$value->data['hostname'],33,'..')}}
                <small
                    class="float-right text-muted time">{{Carbon\Carbon::parse($value->created_at)->diffForHumans()}}</small></a>
        @elseif($value->data['host_type'] == 'App\Models\ActivityMember')
            <a href="{{route('user/notifications/read',['id' => $value->id])}}"
               class="dropdown-item">{{Str::limit("طلب زيارة جديد للمنشط ".$value->data['hostname'],33,'..')}}
                <small
                    class="float-right text-muted time">{{Carbon\Carbon::parse($value->created_at)->diffForHumans()}}</small></a>
        @endif
    @endif
@elseif($value->type == 'App\Notifications\UpdateVisitOrderForOversightMemberNotify')
    @if(auth()->user()->current_role == \App\Models\User::OVERSIGHT_MEMBER_ROLE)
        @if ($value->data['host_type'] == 'App\Models\Teacher')
            <a href="{{route('user/notifications/read',['id' => $value->id])}}"
               class="dropdown-item">{{Str::limit("طلب تعديل زيارة المحفظ ".$value->data['hostname'],33,'..')}}
                <small
                    class="float-right text-muted time">{{Carbon\Carbon::parse($value->created_at)->diffForHumans()}}</small></a>
        @elseif($value->data['host_type'] == 'App\Models\Tester')
            <a href="{{route('user/notifications/read',['id' => $value->id])}}"
               class="dropdown-item">{{Str::limit("طلب تعديل زيارة المختبر ".$value->data['hostname'],33,'..')}}
                <small
                    class="float-right text-muted time">{{Carbon\Carbon::parse($value->created_at)->diffForHumans()}}</small></a>
        @elseif($value->data['host_type'] == 'App\Models\ActivityMember')
            <a href="{{route('user/notifications/read',['id' => $value->id])}}"
               class="dropdown-item">{{Str::limit("طلب تعديل زيارة المنشط ".$value->data['hostname'],33,'..')}}
                <small
                    class="float-right text-muted time">{{Carbon\Carbon::parse($value->created_at)->diffForHumans()}}</small></a>
        @endif
    @endif
@elseif($value->type == 'App\Notifications\SendVisitOrderForOversightSupervisorNotify')
    @if(auth()->user()->current_role == \App\Models\User::OVERSIGHT_SUPERVISOR_ROLE)
        @if ($value->data['host_type'] == 'App\Models\Teacher')
            <a href="{{route('user/notifications/read',['id' => $value->id])}}"
               class="dropdown-item">{{Str::limit("أرسل المراقب زيارة المحفظ ".$value->data['hostname'],33,'..')}}
                <small
                    class="float-right text-muted time">{{Carbon\Carbon::parse($value->created_at)->diffForHumans()}}</small></a>
        @elseif($value->data['host_type'] == 'App\Models\Tester')
            <a href="{{route('user/notifications/read',['id' => $value->id])}}"
               class="dropdown-item">{{Str::limit("أرسل المراقب زيارة المختبر ".$value->data['hostname'],33,'..')}}
                <small
                    class="float-right text-muted time">{{Carbon\Carbon::parse($value->created_at)->diffForHumans()}}</small></a>
        @elseif($value->data['host_type'] == 'App\Models\ActivityMember')
            <a href="{{route('user/notifications/read',['id' => $value->id])}}"
               class="dropdown-item">{{Str::limit("أرسل المراقب زيارة المنشط ".$value->data['hostname'],33,'..')}}
                <small
                    class="float-right text-muted time">{{Carbon\Carbon::parse($value->created_at)->diffForHumans()}}</small></a>
        @endif
    @endif
@endif
