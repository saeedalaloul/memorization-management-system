<div class="card-body">
    <li class="list-group-item">
        <div class="row">
            <div class="col-md-3">
                <label for="student" style="font-size: 15px; color: #1e7e34">نوع الزيارة*</label>
                <select style="width: 100%;" wire:model="selectedVisitTypeId"
                        class="custom-select mr-sm-2"
                        name="selectedStatus">
                    <option value="" selected>جميع الأنواع</option>
                    <option value="App\Models\Teacher">زيارة إلى حلقة</option>
                    <option value="App\Models\Tester">زيارة إلى مختبر</option>
                    <option value="App\Models\ActivityMember">زيارة إلى نشاط</option>
                    <option value="">زيارة إلى دورة</option>
                </select>
            </div>

            @if($current_role == \App\Models\User::OVERSIGHT_SUPERVISOR_ROLE || $current_role == \App\Models\User::OVERSIGHT_MEMBER_ROLE)
                <div class="col-md-3">
                    <label for="student" style="font-size: 15px; color: #1e7e34">حالة الطلب*</label>
                    <select style="width: 100%;" wire:model="selectedStatusId"
                            class="custom-select mr-sm-2"
                            name="selectedStatus">
                        <option value="" selected>جميع الحالات</option>
                        @foreach(\App\Models\VisitOrder::status() as $key => $value )
                            <option value="{{$key}}">{{$value}}</option>
                        @endforeach
                    </select>
                </div>
            @endif

        </div>
    </li>
</div>
<x-search></x-search>
<div class="table-responsive mt-15">
    <table class="table center-aligned-table mb-0">
        <thead>
        <tr class="text-dark table-success">
            <th wire:click="sortBy('id')" style="cursor: pointer;">#
                @include('livewire._sort-icon',['field'=>'id'])
            </th>
            <th>اسم المراقب</th>
            <th>نوع الزيارة</th>
            <th>تاريخ الزيارة</th>
            <th>حالة الطلب</th>
            <th>العمليات</th>
        </tr>
        </thead>
        <tbody>
        @forelse($visit_orders as $visit_order)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $visit_order->oversight_member->user->name }}</td>
                <td>
                    @if($visit_order->hostable_type == 'App\Models\Teacher')
                        زيارة إلى حلقة
                    @elseif($visit_order->hostable_type == 'App\Models\Tester')
                        زيارة إلى مختبر
                    @elseif($visit_order->hostable_type == 'App\Models\ActivityMember')
                        زيارة إلى نشاط
                    @endif
                </td>
                <td>{{ \Carbon\Carbon::parse($visit_order->datetime)->format('Y-m-d') }}</td>
                <td>
                    @if($visit_order->status == \App\Models\VisitOrder::IN_PENDING_STATUS)
                        <label class="badge badge-warning">في انتظار الزيارة</label>
                    @elseif($visit_order->status == \App\Models\VisitOrder::IN_SENDING_STATUS)
                        <label class="badge badge-info">في انتظار الإرسال</label>
                    @elseif($visit_order->status == \App\Models\VisitOrder::IN_APPROVAL_STATUS)
                        <label class="badge badge-primary">في انتظار الإعتماد</label>
                    @endif
                </td>
                <td>
                    @if ($current_role == \App\Models\User::OVERSIGHT_MEMBER_ROLE)
                        @if ($visit_order->status == \App\Models\VisitOrder::IN_PENDING_STATUS)
                            @if($visit_order->hostable_type == 'App\Models\Teacher')
                                <button type="button" class="btn btn-success btn-sm"
                                        wire:click="showDetailsModal('{{$visit_order->id}}');"
                                        title="عرض تفاصيل الحلقة">
                                    <i class="fa fa-eye"></i></button>
                            @elseif($visit_order->hostable_type == 'App\Models\Tester')
                                <button type="button" class="btn btn-success btn-sm"
                                        wire:click="showDetailsModal('{{$visit_order->id}}');"
                                        title="عرض تفاصيل اختبارات المختبر">
                                    <i class="fa fa-eye"></i></button>
                            @endif
                        @elseif($visit_order->status == \App\Models\VisitOrder::IN_SENDING_STATUS)
                            <button type="button" class="btn btn-success btn-sm"
                                    @click.prevent="currentTab = 'form'"
                                    wire:click="visitDetailsShow('{{$visit_order->id}}');"
                                    title="عرض تفاصيل الزيارة">
                                <i class="fa fa-eye"></i></button>

                            <button type="button" class="btn btn-warning btn-sm"
                                    wire:click="visitDetailsEdit('{{$visit_order->id}}');"
                                    @click.prevent="currentTab = 'form'"
                                    title="تعديل تفاصيل الزيارة">
                                <i class="fa fa-pencil"></i></button>

                            <button type="button" class="btn btn-primary btn-sm"
                                    wire:click="sendVisit('{{$visit_order->id}}');"
                                    title="إرسال الزيارة">
                                <i class="fa fa-check"></i></button>
                        @endif
                    @elseif($current_role == \App\Models\User::OVERSIGHT_SUPERVISOR_ROLE)
                        @if($visit_order->status == \App\Models\VisitOrder::IN_APPROVAL_STATUS)
                            <button type="button" class="btn btn-success btn-sm"
                                    wire:click="visitDetailsShow('{{$visit_order->id}}');"
                                    @click.prevent="currentTab = 'form'"
                                    title="عرض تفاصيل الزيارة">
                                <i class="fa fa-eye"></i></button>

                            <button type="button" class="btn btn-warning btn-sm"
                                    wire:click="visitEditRequest('{{$visit_order->id}}');"
                                    title="طلب تعديل الزيارة">
                                <i class="fa fa-pencil"></i></button>

                            <button type="button" class="btn btn-primary btn-sm"
                                    wire:click="approvalVisit('{{$visit_order->id}}');"
                                    title="اعتماد الزيارة">
                                <i class="fa fa-check-square-o"></i></button>
                        @endif
                    @elseif($current_role == \App\Models\User::ADMIN_ROLE)
                        @if($visit_order->status == \App\Models\VisitOrder::IN_APPROVAL_STATUS)
                            <button type="button" class="btn btn-success btn-sm"
                                    wire:click="visitDetailsShow('{{$visit_order->id}}');"
                                    @click.prevent="currentTab = 'form'"
                                    title="عرض تفاصيل الزيارة">
                                <i class="fa fa-eye"></i></button>
                        @endif
                    @endif

                </td>
            </tr>
            @if ($visibleDetailsModalId != null && $visibleDetailsModalId == $visit_order->id)
                @if($visit_order->hostable_type == 'App\Models\Teacher')
                    <tr class="fold">
                        <td colspan="7">
                            <div class="fold-content">
                                <h3>تفاصيل الحلقة</h3>
                                <table>
                                    <thead>
                                    <tr class="text-dark table-success">
                                        <th>#</th>
                                        <th>اسم الحلقة</th>
                                        <th>اسم المرحلة</th>
                                        <th>اسم المحفظ</th>
                                        <th>عدد طلاب الحلقة</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        @php
                                            if (isset($visit_order)) {
                                            $teacher =  $visit_order->hostable;
                                            }
                                        @endphp
                                        <td>1</td>
                                        <td>{{$teacher->group->name }}</td>
                                        <td>{{ $teacher->group->grade->name }}</td>
                                        <td>
                                            @if ($teacher->group != null)
                                                {{ $teacher->user->name }}
                                            @else
                                                لا يوجد محفظ
                                            @endif
                                        </td>
                                        <td>{{ $teacher->group->students->count() }}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    </tr>
                @elseif($visit_order->hostable_type == 'App\Models\Tester')
                    <tr class="fold">
                        <td colspan="7">
                            <div class="fold-content">
                                <h3>تفاصيل اختبارات المختبر</h3>
                                <table>
                                    <thead>
                                    <tr class="text-dark table-success">
                                        <th>#</th>
                                        <th>اسم الطالب</th>
                                        <th>جزء الإختبار</th>
                                        <th>تاريخ الإختبار</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php
                                        if (isset($visit_order)) {
                                        $order_exams =  $visit_order->hostable->tester_exams()->whereDate('datetime',\Carbon\Carbon::parse($visit_order->datetime)->format('Y-m-d'))->get();
                                        }
                                    @endphp
                                    @forelse($order_exams as $order_exam)
                                        <tr>
                                            <td>{{$loop->iteration}}</td>
                                            <td>{{$order_exam->student->user->name}}</td>
                                            <td>{{$order_exam->quranPart->name}}</td>
                                            <td>{{\Carbon\Carbon::parse($order_exam->datetime)->format('Y-m-d')}}</td>
                                        </tr>
                                    @empty
                                        <tr style="text-align: center">
                                            <td colspan="4">No data available in table</td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    </tr>
                @elseif($visit_order->hostable_type == 'App\Models\ActivityMember')
                    <tr class="fold">
                        <td colspan="7">
                            <div class="fold-content">
                                <h3>تفاصيل أنشطة المنشط</h3>
                                <table>
                                    <thead>
                                    <tr class="text-dark table-success">
                                        <th>#</th>
                                        <th>اسم النشاط</th>
                                        <th>عدد الطلاب</th>
                                        <th>الوقت</th>
                                        <th>اسم المحفظ</th>
                                        <th>اسم المنشط</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php
                                        if (isset($visit_order)) {
                                        $activities_orders =  $visit_order->hostable->activities_orders_acceptable
                                        ->whereDate('datetime',\Carbon\Carbon::parse($visit_order->datetime)
                                        ->format('Y-m-d'))->with(['activity_type','teacher.user:id,name','activity_member.user:id,name'])
                                        ->withCount(['students'])->get();
                                        }
                                    @endphp
                                    @forelse($activities_orders as $activities_order)
                                        <tr>
                                            <td>{{$loop->iteration}}</td>
                                            <td>{{$activities_order->activity_type->name}}</td>
                                            <td>{{$activities_order->students_count}}</td>
                                            <td>{{\Carbon\Carbon::parse($activities_order->activity_date)->translatedFormat('l j F Y h:i a')}}</td>
                                            <td>{{$activities_order->teacher->user->name}}</td>
                                            <td>
                                                @if(isset($activities_order->activity_member))
                                                    {{$activities_order->activity_member->user->name}}
                                                @else
                                                    لم يتم اعتماد منشط
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr style="text-align: center">
                                            <td colspan="4">No data available in table</td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    </tr>
                @endif
            @endif
        @empty
            <tr style="text-align: center">
                <td colspan="6">No data available in table</td>
            </tr>
        @endforelse
        </tbody>
        <tfoot>
        <tr class="text-dark table-success">
            <th>#</th>
            <th>اسم المراقب</th>
            <th>نوع الزيارة</th>
            <th>تاريخ الزيارة</th>
            <th>حالة الطلب</th>
            <th>العمليات</th>
        </tr>
        </tfoot>
    </table>
</div>
<div id="datatable_wrapper"
     class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">
    <div class="row">
        <div class="col-sm-12 col-md-5">
            <div class="dataTables_info" id="datatable_info" role="status"
                 aria-live="polite">
                Showing {{$visit_orders->firstItem()}} to {{$visit_orders->lastItem()}}
                of {{$visit_orders->total()}} entries
            </div>
        </div>
        <div class="col-sm-12 col-md-7">
            <div class="dataTables_paginate paging_simple_numbers"
                 id="datatable_paginate">
                <ul class="pagination">
                    {{$visit_orders->links()}}
                </ul>
            </div>
        </div>
    </div>
</div>
<x-loading-indicator></x-loading-indicator>
