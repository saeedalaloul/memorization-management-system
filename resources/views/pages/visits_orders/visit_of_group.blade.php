<table class="table table-striped table-hover" style="text-align:center">
    <tbody>
    <tr class="table-info">
        <th scope="row">اسم المراقب</th>
        <td class="text-dark">{{$visitOrder->oversight_member->user->name}}</td>
        <th scope="row">نوع الزيارة</th>
        <td class="text-dark">
            @if($visitOrder->hostable_type == 'App\Models\Teacher')
                زيارة إلى حلقة
            @elseif($visitOrder->hostable_type == 'App\Models\Tester')
                زيارة إلى مختبر
            @elseif($visitOrder->hostable_type == 'App\Models\ActivityMember')
                زيارة إلى نشاط
            @endif
        </td>
        <th scope="row">تاريخ الزيارة</th>
        <td class="text-dark">{{\Carbon\Carbon::parse($visitOrder->datetime)->format('Y-m-d')}}</td>
        <th scope="row">حالة الزيارة</th>
        <td class="text-dark">
            @if($visitOrder->status == \App\Models\VisitOrder::IN_PENDING_STATUS)
                <label class="badge badge-warning">في انتظار الزيارة</label>
            @elseif($visitOrder->status == \App\Models\VisitOrder::IN_SENDING_STATUS)
                <label class="badge badge-info">في انتظار الإرسال</label>
            @elseif($visitOrder->status == \App\Models\VisitOrder::IN_APPROVAL_STATUS)
                <label class="badge badge-primary">في انتظار الإعتماد</label>
            @endif
        </td>
    </tr>
    <tr>
        @if($visitOrder->hostable_type == 'App\Models\Teacher')
            <th scope="row">اسم الحلقة</th>
            <td class="text-dark">{{$visitOrder->hostable->group->name}}</td>
            <th scope="row">اسم المحفظ</th>
            <td class="text-dark">{{$visitOrder->hostable->user->name}}</td>
            <th scope="row">عدد طلاب الحلقة</th>
            <td class="text-dark">{{$visitOrder->hostable->group->students->count()}}</td>
            <th scope="row"></th>
            <td class="text-dark"></td>
        @elseif($visitOrder->hostable_type == 'App\Models\Tester')
            <th scope="row">اسم المختبر</th>
            <td class="text-dark">{{$visitOrder->hostable->user->name}}</td>
            <th scope="row">عدد اختبارات المختبر</th>
            <td class="text-dark">{{$visitOrder->hostable->tester_exams()->whereDate('datetime',\Carbon\Carbon::parse($visitOrder->datetime)->format('Y-m-d'))->count()}}</td>
            <th scope="row"></th>
            <td class="text-dark"></td>
        @elseif($visitOrder->hostable_type == 'App\Models\ActivityMember')
            <th scope="row">اسم المنشط</th>
            <td class="text-dark">{{$visitOrder->hostable->user->name}}</td>
            <th scope="row">عدد أنشطة المنشط</th>
            <td class="text-dark">{{$visitOrder->hostable->activities_orders_acceptable()->whereDate('datetime',\Carbon\Carbon::parse($visitOrder->datetime)->format('Y-m-d'))->count()}}</td>
            <th scope="row"></th>
            <td class="text-dark"></td>
        @endif
    </tr>
    </tbody>
</table>
