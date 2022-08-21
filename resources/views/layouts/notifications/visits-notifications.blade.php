@if($value->type == 'App\Notifications\NewVisitForAdminNotify')
    @if(auth()->user()->current_role == \App\Models\User::ADMIN_ROLE)
        @if ($value->data['host_type'] == 'App\Models\Teacher')
            <a href="{{route('user/notifications/read',['id' => $value->id])}}"
               class="dropdown-item">{{Str::limit("زيارة جديدة للمحفظ ".$value->data['hostname'],33,'..')}}
                <small
                    class="float-right text-muted time">{{Carbon\Carbon::parse($value->created_at)->diffForHumans()}}</small></a>
        @elseif($value->data['host_type'] == 'App\Models\Tester')
            <a href="{{route('user/notifications/read',['id' => $value->id])}}"
               class="dropdown-item">{{Str::limit("زيارة جديدة للمختبر ".$value->data['hostname'],33,'..')}}
                <small
                    class="float-right text-muted time">{{Carbon\Carbon::parse($value->created_at)->diffForHumans()}}</small></a>
        @elseif($value->data['host_type'] == 'App\Models\ActivityMember')
            <a href="{{route('user/notifications/read',['id' => $value->id])}}"
               class="dropdown-item">{{Str::limit("زيارة جديدة للمنشط ".$value->data['hostname'],33,'..')}}
                <small
                    class="float-right text-muted time">{{Carbon\Carbon::parse($value->created_at)->diffForHumans()}}</small></a>
        @endif
    @endif
@elseif($value->type == 'App\Notifications\ReplyToVisitForOversightSupervisorNotify')
    @if(auth()->user()->current_role == \App\Models\User::OVERSIGHT_SUPERVISOR_ROLE)
        @if ($value->data['host_type'] == 'App\Models\Teacher')
            <a href="{{route('user/notifications/read',['id' => $value->id])}}"
               class="dropdown-item">{{Str::limit("تم الرد على زيارة المحفظ ".$value->data['hostname'],33,'..')}}
                <small
                    class="float-right text-muted time">{{Carbon\Carbon::parse($value->created_at)->diffForHumans()}}</small></a>
        @elseif($value->data['host_type'] == 'App\Models\Tester')
            <a href="{{route('user/notifications/read',['id' => $value->id])}}"
               class="dropdown-item">{{Str::limit("تم الرد على زيارة المختبر ".$value->data['hostname'],33,'..')}}
                <small
                    class="float-right text-muted time">{{Carbon\Carbon::parse($value->created_at)->diffForHumans()}}</small></a>
        @elseif($value->data['host_type'] == 'App\Models\ActivityMember')
            <a href="{{route('user/notifications/read',['id' => $value->id])}}"
               class="dropdown-item">{{Str::limit("تم الرد على زيارة المنشط ".$value->data['hostname'],33,'..')}}
                <small
                    class="float-right text-muted time">{{Carbon\Carbon::parse($value->created_at)->diffForHumans()}}</small></a>
        @endif
    @endif
@elseif($value->type == 'App\Notifications\SolvedVisitForAdminNotify')
    @if(auth()->user()->current_role == \App\Models\User::ADMIN_ROLE)
        @if ($value->data['host_type'] == 'App\Models\Teacher')
            <a href="{{route('user/notifications/read',['id' => $value->id])}}"
               class="dropdown-item">{{Str::limit("تم معالجة زيارة المحفظ ".$value->data['hostname'],33,'..')}}
                <small
                    class="float-right text-muted time">{{Carbon\Carbon::parse($value->created_at)->diffForHumans()}}</small></a>
        @elseif($value->data['host_type'] == 'App\Models\Tester')
            <a href="{{route('user/notifications/read',['id' => $value->id])}}"
               class="dropdown-item">{{Str::limit("تم معالجة زيارة المختبر ".$value->data['hostname'],33,'..')}}
                <small
                    class="float-right text-muted time">{{Carbon\Carbon::parse($value->created_at)->diffForHumans()}}</small></a>
        @elseif($value->data['host_type'] == 'App\Models\ActivityMember')
            <a href="{{route('user/notifications/read',['id' => $value->id])}}"
               class="dropdown-item">{{Str::limit("تم معالجة زيارة المنشط ".$value->data['hostname'],33,'..')}}
                <small
                    class="float-right text-muted time">{{Carbon\Carbon::parse($value->created_at)->diffForHumans()}}</small></a>
        @endif
    @endif
@elseif($value->type == 'App\Notifications\FailureProcessingOfVisitForAdminNotify')
    @if(auth()->user()->current_role == \App\Models\User::ADMIN_ROLE)
        @if ($value->data['host_type'] == 'App\Models\Teacher')
            <a href="{{route('user/notifications/read',['id' => $value->id])}}"
               class="dropdown-item">{{Str::limit("فشل معالجة زيارة المحفظ ".$value->data['hostname'],32,'..')}}
                <small
                    class="float-right text-muted time">{{Carbon\Carbon::parse($value->created_at)->diffForHumans()}}</small></a>
        @elseif($value->data['host_type'] == 'App\Models\Tester')
            <a href="{{route('user/notifications/read',['id' => $value->id])}}"
               class="dropdown-item">{{Str::limit("فشل معالجة زيارة المختبر ".$value->data['hostname'],32,'..')}}
                <small
                    class="float-right text-muted time">{{Carbon\Carbon::parse($value->created_at)->diffForHumans()}}</small></a>
        @elseif($value->data['host_type'] == 'App\Models\ActivityMember')
            <a href="{{route('user/notifications/read',['id' => $value->id])}}"
               class="dropdown-item">{{Str::limit("فشل معالجة زيارة المنشط ".$value->data['hostname'],32,'..')}}
                <small
                    class="float-right text-muted time">{{Carbon\Carbon::parse($value->created_at)->diffForHumans()}}</small></a>
        @endif
    @endif
@elseif($value->type == 'App\Notifications\ReminderOfVisitForAdminNotify')
    @if(auth()->user()->current_role == \App\Models\User::ADMIN_ROLE)
        @if ($value->data['host_type'] == 'App\Models\Teacher')
            <a href="{{route('user/notifications/read',['id' => $value->id])}}"
               class="dropdown-item">{{Str::limit("تذكير بمعالجة زيارة المحفظ ".$value->data['hostname'],32,'..')}}
                <small
                    class="float-right text-muted time">{{Carbon\Carbon::parse($value->created_at)->diffForHumans()}}</small></a>
        @elseif($value->data['host_type'] == 'App\Models\Tester')
            <a href="{{route('user/notifications/read',['id' => $value->id])}}"
               class="dropdown-item">{{Str::limit("تذكير بمعالجة زيارة المختبر ".$value->data['hostname'],32,'..')}}
                <small
                    class="float-right text-muted time">{{Carbon\Carbon::parse($value->created_at)->diffForHumans()}}</small></a>
        @elseif($value->data['host_type'] == 'App\Models\ActivityMember')
            <a href="{{route('user/notifications/read',['id' => $value->id])}}"
               class="dropdown-item">{{Str::limit("تذكير بمعالجة زيارة المنشط ".$value->data['hostname'],32,'..')}}
                <small
                    class="float-right text-muted time">{{Carbon\Carbon::parse($value->created_at)->diffForHumans()}}</small></a>
        @endif
    @endif
@elseif($value->type == 'App\Notifications\ReminderOfVisitForOversightSupervisorNotify')
    @if(auth()->user()->current_role == \App\Models\User::OVERSIGHT_SUPERVISOR_ROLE)
        @if ($value->data['host_type'] == 'App\Models\Teacher')
            <a href="{{route('user/notifications/read',['id' => $value->id])}}حفظ ".$value->data['hostname'],32,'..')}}
                <small
                    class="float-right text-muted time">{{Carbon\Carbon::parse($value->created_at)->diffForHumans()}}</small></a>
        @elseif($value->data['host_type'] == 'App\Models\Tester')
            <a href="{{route('user/notifications/read',['id' => $value->id])}}"
               class="dropdown-item">{{Str::limit("تذكير بمعالجة زيارة المختبر ".$value->data['hostname'],32,'..')}}
                <small
                    class="float-right text-muted time">{{Carbon\Carbon::parse($value->created_at)->diffForHumans()}}</small></a>
        @elseif($value->data['host_type'] == 'App\Models\ActivityMember')
            <a href="{{route('user/notifications/read',['id' => $value->id])}}"
               class="dropdown-item">{{Str::limit("تذكير بمعالجة زيارة المنشط ".$value->data['hostname'],32,'..')}}
                <small
                    class="float-right text-muted time">{{Carbon\Carbon::parse($value->created_at)->diffForHumans()}}</small></a>
        @endif
    @endif
@endif
