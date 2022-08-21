<table class="table table-striped table-hover" style="text-align:center">
    <tbody>
    <tr class="table-info">
        <th scope="row">اسم المراقب</th>
        <td class="text-dark">{{$visit->oversight_member->user->name}}</td>
        <th scope="row">نوع الزيارة</th>
        <td class="text-dark">
            @if($visit->hostable_type == 'App\Models\Teacher')
                زيارة إلى حلقة
            @elseif($visit->hostable_type == 'App\Models\Tester')
                زيارة إلى مختبر
            @elseif($visit->hostable_type == 'App\Models\ActivityMember')
                زيارة إلى منشط
            @endif
        </td>
        <th scope="row">تاريخ الزيارة</th>
        <td class="text-dark">{{\Carbon\Carbon::parse($visit->datetime)->format('Y-m-d')}}</td>
        <th scope="row">حالة الزيارة</th>
        <td class="text-dark">
            @if($visit->status == \App\Models\Visit::IN_PENDING_STATUS)
                <label class="badge badge-warning">مطلوب الرد</label>
            @elseif($visit->status == \App\Models\Visit::REPLIED_STATUS)
                <label class="badge badge-info">تم الرد</label>
            @elseif($visit->status == \App\Models\Visit::IN_PROCESS_STATUS)
                <label class="badge badge-primary">في انتظار المعالجة</label>
            @elseif($visit->status == \App\Models\Visit::FAILURE_STATUS)
                <label class="badge badge-danger">فشل المعالجة</label>
            @elseif($visit->status == \App\Models\Visit::SOLVED_STATUS)
                <label class="badge badge-success">تم الحل</label>
            @endif
        </td>
    </tr>
    <tr>
        @if($visit->hostable_type == 'App\Models\Teacher')
            <th scope="row">اسم الحلقة</th>
            <td class="text-dark">{{$visit->hostable->group->name}}</td>
            <th scope="row">اسم المحفظ</th>
            <td class="text-dark">{{$visit->hostable->user->name}}</td>
            <th scope="row">عدد طلاب الحلقة</th>
            <td class="text-dark">{{$visit->hostable->group->students->count()}}</td>
            <th scope="row"></th>
            <td class="text-dark"></td>
        @elseif($visit->hostable_type == 'App\Models\Tester')
            <th scope="row">اسم المختبر</th>
            <td class="text-dark">{{$visit->hostable->user->name}}</td>
            <th scope="row">عدد اختبارات المختبر</th>
            <td class="text-dark">{{$visit->hostable->exams()->whereDate('datetime',\Carbon\Carbon::parse($visit->datetime)->format('Y-m-d'))->count()}}</td>
            <th scope="row"></th>
            <td class="text-dark"></td>
            <th scope="row"></th>
            <td class="text-dark"></td>
        @elseif($visit->hostable_type == 'App\Models\ActivityMember')
            <th scope="row">اسم المنشط</th>
            <td class="text-dark">{{$visit->hostable->user->name}}</td>
            <th scope="row">عدد أنشطة المنشط</th>
            <td class="text-dark">{{$visit->hostable->activities()->whereDate('datetime',\Carbon\Carbon::parse($visit->datetime)->format('Y-m-d'))->count()}}</td>
            <th scope="row"></th>
            <td class="text-dark"></td>
            <th scope="row"></th>
            <td class="text-dark"></td>
        @endif
    </tr>
    </tbody>
</table>
