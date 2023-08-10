@can('إدارة طلبات الأنشطة')
    <x-search></x-search>
    <div class="table-responsive mt-15">
        <table class="table center-aligned-table mb-0">
            <thead>
            <tr class="text-dark table-success">
                <th wire:click="sortBy('id')" style="cursor: pointer;">#
                    @include('livewire._sort-icon',['field'=>'id'])
                </th>
                <th>اسم النشاط</th>
                <th>عدد الطلاب</th>
                <th>الوقت</th>
                <th>اسم المحفظ</th>
                <th>اسم المنشط</th>
                <th>الحالة</th>
                <th>العمليات</th>
            </tr>
            </thead>
            <tbody>
            @forelse($activities_orders as $activities_order)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{$activities_order->activity_type->name}}</td>
                    <td>{{$activities_order->students_count}}</td>
                    <td>{{\Carbon\Carbon::parse($activities_order->datetime)->translatedFormat('l j F Y h:i a')}}</td>
                    <td>{{$activities_order->teacher->user->name}}</td>
                    <td>
                        @if(isset($activities_order->activity_member))
                            {{$activities_order->activity_member->user->name}}
                        @else
                            لم يتم اعتماد منشط
                        @endif
                    </td>
                    <td>
                        @if($activities_order->status == \App\Models\ActivityOrder::IN_PENDING_STATUS)
                            <label class="badge badge-primary">قيد الدراسة</label>
                        @elseif($activities_order->status == \App\Models\ActivityOrder::ACCEPTABLE_STATUS)
                            <label class="badge badge-success">معتمد</label>
                        @elseif($activities_order->status == \App\Models\ActivityOrder::REJECTED_STATUS)
                            <label class="badge badge-danger">مرفوض</label>
                        @elseif($activities_order->status == \App\Models\ActivityOrder::FAILURE_STATUS)
                            <label class="badge badge-warning">فشل إجراء النشاط</label>
                        @endif
                    </td>
                    <td>
                        @if($activities_order->status == \App\Models\ActivityOrder::REJECTED_STATUS || $activities_order->status == \App\Models\ActivityOrder::FAILURE_STATUS)
                            <button type="button" class="btn btn-outline-info btn-sm"
                                    data-toggle="popover" data-trigger="focus" title="الملاحظات"
                                    data-content="{{$activities_order->notes}}">
                                اضغط هنا
                            </button>
                        @endif

                        @if ($current_role == \App\Models\User::ACTIVITIES_SUPERVISOR_ROLE)
                            @if($activities_order->status == \App\Models\ActivityOrder::IN_PENDING_STATUS)
                                <button class="btn btn-outline-success btn-sm"
                                        data-toggle="modal"
                                        data-target="#approval-activity"
                                        wire:click="getActivityOrder('{{$activities_order->id}}')">
                                    اعتماد
                                    الطلب
                                </button>

                                <button class="btn btn-outline-danger btn-sm"
                                        data-toggle="modal"
                                        data-target="#refusal-activity"
                                        wire:click="getActivityOrder('{{$activities_order->id}}')">
                                    رفض
                                    الطلب
                                </button>
                            @endif
                        @endif

                        @if ($current_role == \App\Models\User::TEACHER_ROLE)
                            @if($activities_order->status == \App\Models\ActivityOrder::IN_PENDING_STATUS || $activities_order->status == \App\Models\ActivityOrder::REJECTED_STATUS || $activities_order->status == \App\Models\ActivityOrder::FAILURE_STATUS)
                                <button class="btn btn-outline-warning btn-sm"
                                        data-toggle="modal"
                                        data-target="#delete-activity-order"
                                        wire:click="getActivityOrder('{{$activities_order->id}}')">
                                    حذف
                                    الطلب
                                </button>
                            @endif
                        @endif

                        @if ($current_role == \App\Models\User::ACTIVITY_MEMBER_ROLE)
                            @if($activities_order->status == \App\Models\ActivityOrder::ACCEPTABLE_STATUS)
                                @if (\Carbon\Carbon::parse($activities_order->activity_date)->format('Y-m-d') == date('Y-m-d', time()))
                                    <button type="button" class="btn btn-success btn-sm"
                                            wire:click="activity_completed('{{$activities_order->id}}');"
                                            title="تم إجراء النشاط">
                                        <i class="fa fa-check"></i></button>

                                    <button type="button" class="btn btn-danger btn-sm"
                                            data-toggle="modal"
                                            data-target="#failed-activity"
                                            wire:click="getActivityOrder('{{$activities_order->id}}')"
                                            title="لم يتم إجراء النشاط">
                                        <i class="fa fa-close"></i></button>
                                @endif
                            @endif
                        @endif
                    </td>
                </tr>
                @include('pages.activities_orders.activity_order_approval')
                @include('pages.activities_orders.activity_order_refusal')
                @include('pages.activities_orders.activity_order_failed')
                @include('pages.activities_orders.delete_activity_order')
            @empty
                <tr style="text-align: center">
                    <td colspan="8">No data available in table</td>
                </tr>
            @endforelse
            </tbody>
            <tfoot>
            <tr class="text-dark table-success">
                <th>#</th>
                <th>اسم النشاط</th>
                <th>عدد الطلاب</th>
                <th>الوقت</th>
                <th>اسم المحفظ</th>
                <th>اسم المنشط</th>
                <th>الحالة</th>
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
                    Showing {{$activities_orders->firstItem()}} to {{$activities_orders->lastItem()}}
                    of {{$activities_orders->total()}} entries
                </div>
            </div>
            <div class="col-sm-12 col-md-7">
                <div class="dataTables_paginate paging_simple_numbers"
                     id="datatable_paginate">
                    <ul class="pagination">
                        {{$activities_orders->links()}}
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endcan
