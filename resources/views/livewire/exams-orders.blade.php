<div class="row">
    <div>
        @if(Session::has('success_message'))
            <script>
                $(function () {
                    toastr.success("{{ Session::get('success_message') }}");
                })
            </script>
        @endif

        @if(Session::has('failure_message'))
            <script>
                $(function () {
                    toastr.error("{{ Session::get('failure_message') }}");
                })
            </script>
        @endif
    </div>
    <div class="col-xl-12 mb-30">
        <div class="card card-statistics h-100">
            <div class="card-body">
                <br>
                @can('إدارة طلبات الإختبارات')
                    <div class="row">
                        @if (isset($grades))
                            <div>
                                <label>
                                    <select class="selectpicker" data-style="btn-info" wire:model="searchGradeId">
                                        <option value="" selected>بحث بواسطة المرحلة
                                        </option>
                                        @foreach ($grades as $grade)
                                            <option
                                                value="{{ $grade->id }}">{{ $grade->name}}</option>
                                        @endforeach
                                    </select>
                                </label>
                            </div>
                        @endif
                        @if (isset($groups))
                            <div style="padding-right: 10px;">
                                <label>
                                    <select class="selectpicker" data-style="btn-info" wire:model="searchGroupId">
                                        <option value="" selected>بحث بواسطة المحفظ
                                        </option>
                                        @foreach ($groups as $group)
                                            <option
                                                value="{{ $group->id }}">{{ $group->teacher->user->name}}</option>
                                        @endforeach
                                    </select>
                                </label>
                            </div>
                        @endif
                        @if (isset($students))
                            <div style="padding-right: 10px;">
                                <label>
                                    <select class="selectpicker" data-style="btn-info" wire:model="searchStudentId">
                                        <option value="" selected>بحث بواسطة الطالب
                                        </option>
                                        @foreach ($students as $student)
                                            <option
                                                value="{{ $student->id }}">{{ $student->user->name}}</option>
                                        @endforeach
                                    </select>
                                </label>
                            </div>
                        @endif
                    </div>
                    @include('livewire.search')
                    <div class="table-responsive mt-15">
                        <table class="table center-aligned-table mb-0">
                            <thead>
                            <tr class="text-dark table-success">
                                <th wire:click="sortBy('id')" style="cursor: pointer;">#
                                    @include('livewire._sort-icon',['field'=>'id'])
                                </th>
                                <th>اسم الطالب</th>
                                <th>جزء الإختبار</th>
                                <th>اسم المحفظ</th>
                                <th>حالة الطلب</th>
                                <th>ملاحظات/تاريخ الإختبار</th>
                                <th>العمليات</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($exam_orders as $exam_order)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $exam_order->student->user->name }}</td>
                                    <td>{{ $exam_order->QuranPart->name }}</td>
                                    <td>{{ $exam_order->teacher->user->name }}</td>
                                    <td>
                                        @if($exam_order->status == 0)
                                            <label class="badge badge-warning">قيد الطلب</label>
                                        @elseif($exam_order->status == 1)
                                            <label class="badge badge-info">قيد الإعتماد</label>
                                        @elseif($exam_order->status == -1)
                                            <label class="badge badge-danger">مرفوض</label>
                                        @elseif($exam_order->status == 2)
                                            <label class="badge badge-success">معتمد</label>
                                        @elseif($exam_order->status == -2)
                                            <label class="badge badge-danger">مرفوض</label>
                                        @elseif($exam_order->status == -3)
                                            <label class="badge badge-danger">لم يختبر</label>
                                        @endif
                                    </td>
                                    <td>
                                        @if($exam_order->status == 2)
                                            {{ $exam_order->exam_date }}
                                        @else
                                            {{ $exam_order->notes }}
                                        @endif
                                    </td>
                                    <td>
                                        @can('إجراء طلب اختبار')
                                            @if($exam_order->status == 0)
                                                @if (auth()->user()->current_role == 'محفظ')
                                                    <button class="btn btn-outline-danger btn-sm"
                                                            data-toggle="modal"
                                                            data-target="#delete-exam-order">حذف الطلب
                                                    </button>
                                                @endif
                                                @if (auth()->user()->current_role == 'مشرف' || auth()->user()->current_role == 'أمير المركز')
                                                    <button wire:click.prevent="examOrderApproval({{$exam_order->id}})"
                                                            class="btn btn-outline-success btn-sm">قبول الطلب
                                                    </button>
                                                    <button class="btn btn-outline-warning btn-sm"
                                                            data-toggle="modal"
                                                            data-target="#refusal-exam"
                                                            wire:click.prevent="getExamOrder({{$exam_order->id}})">رفض
                                                        الطلب
                                                    </button>
                                                @endif
                                            @elseif($exam_order->status == 1)
                                                @if (auth()->user()->current_role == 'مشرف' ||
                                                     auth()->user()->current_role == 'أمير المركز' ||
                                                     auth()->user()->current_role == 'مشرف الإختبارات')
                                                    <button class="btn btn-outline-success btn-sm"
                                                            data-toggle="modal"
                                                            data-target="#approval-exam"
                                                            wire:click.prevent="getExamOrder({{$exam_order->id}})">
                                                        اعتماد
                                                        الطلب
                                                    </button>
                                                    <button class="btn btn-outline-warning btn-sm"
                                                            data-toggle="modal"
                                                            data-target="#refusal-exam"
                                                            wire:click.prevent="getExamOrder({{$exam_order->id}})">رفض
                                                        اعتماد الطلب
                                                    </button>
                                                @endif
                                            @elseif($exam_order->status == -1)
                                                @if (auth()->user()->current_role == 'محفظ')
                                                    <button class="btn btn-outline-danger btn-sm"
                                                            data-toggle="modal"
                                                            data-target="#delete-exam-order">حذف الطلب
                                                    </button>
                                                @endif
                                                @if (auth()->user()->current_role == 'مشرف' || auth()->user()->current_role == 'أمير المركز')
                                                    <button wire:click.prevent="examOrderApproval({{$exam_order->id}})"
                                                            class="btn btn-outline-success btn-sm">قبول الطلب
                                                    </button>
                                                @endif
                                            @elseif($exam_order->status == 2)
                                                @if (auth()->user()->current_role == 'أمير المركز' ||
                                                   auth()->user()->current_role == 'مشرف الإختبارات')
                                                    <button class="btn btn-outline-success btn-sm"
                                                            data-toggle="modal"
                                                            data-target="#approval-exam"
                                                            wire:click.prevent="getExamOrder({{$exam_order->id}})">
                                                        اعتماد
                                                        الطلب
                                                    </button>
                                                    <button class="btn btn-outline-warning btn-sm"
                                                            data-toggle="modal"
                                                            data-target="#refusal-exam"
                                                            wire:click.prevent="getExamOrder({{$exam_order->id}})">رفض
                                                        اعتماد الطلب
                                                    </button>
                                                @endif
                                            @elseif($exam_order->status == -2)
                                                @if (auth()->user()->current_role == 'محفظ')
                                                    <button class="btn btn-outline-danger btn-sm"
                                                            data-toggle="modal"
                                                            data-target="#delete-exam-order">حذف الطلب
                                                    </button>
                                                @endif
                                                @if (auth()->user()->current_role == 'أمير المركز' ||
                                                       auth()->user()->current_role == 'مشرف الإختبارات')
                                                    <button class="btn btn-outline-success btn-sm"
                                                            data-toggle="modal"
                                                            data-target="#approval-exam"
                                                            wire:click.prevent="getExamOrder({{$exam_order->id}})">
                                                        اعتماد
                                                        الطلب
                                                    </button>
                                                @endif
                                            @elseif($exam_order->status == -3)
                                                @if (auth()->user()->current_role == 'محفظ')
                                                    <button class="btn btn-outline-danger btn-sm"
                                                            data-toggle="modal"
                                                            data-target="#delete-exam-order">حذف الطلب
                                                    </button>
                                                @endif
                                            @endif
                                        @endcan
                                    </td>
                                </tr>
                                @include('pages.exams_orders.exam_order_approval')
                                @include('pages.exams_orders.exam_order_refusal')
                                @include('pages.exams_orders.delete_exam_order')
                            @empty
                                <tr style="text-align: center">
                                    <td colspan="8">No data available in table</td>
                                </tr>
                            @endforelse
                            </tbody>
                            <tfoot>
                            <tr class="text-dark table-success">
                                <th>#</th>
                                <th>اسم الطالب</th>
                                <th>جزء الإختبار</th>
                                <th>اسم المحفظ</th>
                                <th>حالة الطلب</th>
                                <th>ملاحظات/تاريخ الإختبار</th>
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
                                    Showing {{$exam_orders->firstItem()}} to {{$exam_orders->lastItem()}}
                                    of {{$exam_orders->total()}} entries
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-7">
                                <div class="dataTables_paginate paging_simple_numbers"
                                     id="datatable_paginate">
                                    <ul class="pagination">
                                        {{$exam_orders->links()}}
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endcan
            </div>
        </div>
    </div>
</div>
