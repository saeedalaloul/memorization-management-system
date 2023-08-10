<div class="row">
    @if ($current_role === \App\Models\User::ADMIN_ROLE || $current_role === \App\Models\User::SUPERVISOR_ROLE ||  $current_role === \App\Models\User::TEACHER_ROLE
        || $current_role === \App\Models\User::TESTER_ROLE ||  $current_role === \App\Models\User::EXAMS_SUPERVISOR_ROLE
        || $current_role === \App\Models\User::SPONSORSHIP_SUPERVISORS_ROLE)
        <div class="col-xl-12 mb-30">
            <div class="card card-statistics h-100">
                <div class="card-body">
                    @can('إدارة طلبات الإختبارات')
                        <div class="card-body">
                            <li class="list-group-item">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label style="font-size: 15px; color: #1e7e34">المراحل*</label>
                                        <select style="width: 100%;" wire:model="selectedGradeId" id="grade"
                                                class="custom-select mr-sm-2 select2">
                                            <option value="">الكل</option>
                                            @foreach ($grades as $grade)
                                                <option
                                                    value="{{ $grade->id }}">{{ $grade->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <label style="font-size: 15px; color: #1e7e34">المحفظين*</label>
                                        <select style="width: 100%;" class="custom-select mr-sm-2 select2" id="teacher"
                                                wire:model="selectedTeacherId">
                                            <option value="">الكل</option>
                                            @foreach ($groups as $group)
                                                <option value="{{ $group->id }}">
                                                    @if ($group->teacher_id === null)
                                                        {{$group->name . ' (لا يوجد محفظ)'}}
                                                    @else
                                                        {{ $group->teacher->user->name }}
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>


                                    <div class="col-md-3">
                                        <label for="student" style="font-size: 15px; color: #1e7e34">الطلاب*</label>
                                        <select style="width: 100%;" class="custom-select mr-sm-2 select2"
                                                id="student"
                                                wire:model="selectedStudentId">
                                            <option value="">الكل</option>
                                            @foreach($students as $student)
                                                <option
                                                    value="{{ $student->id }}">{{ $student->user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="student" style="font-size: 15px; color: #1e7e34">حالة الطلب*</label>
                                        <select style="width: 100%;" wire:model="selectedStatus"
                                                class="custom-select mr-sm-2"
                                                name="selectedStatus">
                                            <option value="" selected>الكل</option>
                                            @foreach(\App\Models\ExamOrder::status() as $key => $value)
                                                @if ($current_role === \App\Models\User::TESTER_ROLE)
                                                    @if ($key === \App\Models\ExamOrder::ACCEPTABLE_STATUS || $key === \App\Models\ExamOrder::FAILURE_STATUS)
                                                        <option value="{{$key}}">{{$value}}</option>
                                                    @endif
                                                @else
                                                    <option value="{{$key}}">{{$value}}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
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
                                    <th>اسم الطالب</th>
                                    <th>جزء الإختبار</th>
                                    <th>اسم المحفظ</th>
                                    <th>حالة الطلب</th>
                                    <th>تاريخ الطلب</th>
                                    <th>يوم الإختبار المقترح</th>
                                    <th>ملاحظات/تاريخ الإختبار</th>
                                    @if($current_role === \App\Models\User::EXAMS_SUPERVISOR_ROLE)
                                        <th>اسم المختبر</th>
                                    @endif
                                    <th>العمليات</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($exam_orders as $exam_order)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td class="text-success">{{ $exam_order->student->user->name }}</td>
                                        <td class="text-success">
                                            @if ($exam_order->type === \App\Models\ExamOrder::IMPROVEMENT_TYPE)
                                                <label class="badge badge-success">
                                                    @if($exam_order->partable_type === 'App\Models\QuranPart')
                                                        {{$exam_order->partable->name .' '.$exam_order->partable->description . ' (طلب تحسين درجة)' }}
                                                    @else
                                                        {{$exam_order->partable->name .' ('.$exam_order->partable->total_hadith_parts.') حديث' . ' (طلب تحسين درجة)' }}
                                                    @endif
                                                </label>
                                            @else
                                                @if($exam_order->partable_type === 'App\Models\QuranPart')
                                                    {{$exam_order->partable->name .' '.$exam_order->partable->description }}
                                                @else
                                                    {{$exam_order->partable->name .' ('.$exam_order->partable->total_hadith_parts.') حديث'}}
                                                @endif
                                            @endif
                                        </td>
                                        <td class="text-success">{{ $exam_order->teacher->user->name }}</td>
                                        <td>
                                            @if($exam_order->status === \App\Models\ExamOrder::IN_PENDING_STATUS)
                                                <label class="badge badge-warning">قيد الطلب</label>
                                            @elseif($exam_order->status === \App\Models\ExamOrder::REJECTED_STATUS)
                                                <label class="badge badge-danger">مرفوض</label>
                                            @elseif($exam_order->status === \App\Models\ExamOrder::ACCEPTABLE_STATUS)
                                                <label class="badge badge-success">معتمد</label>
                                            @elseif($exam_order->status === \App\Models\ExamOrder::FAILURE_STATUS)
                                                <label class="badge badge-danger">لم يختبر</label>
                                            @endif
                                        </td>
                                        <td class="text-success">
                                            {{ \Carbon\Carbon::parse($exam_order->created_at)->translatedFormat('l j F Y h:i a')}}
                                        </td>
                                        <td class="text-success">
                                            {{\App\Models\ExamSettings::days()[$exam_order->suggested_day]}}
                                        </td>
                                        @if($exam_order->status === \App\Models\ExamOrder::ACCEPTABLE_STATUS)
                                            <td class="text-success">
                                                {{ \Carbon\Carbon::parse($exam_order->datetime)->format('Y-m-d') }}
                                            </td>
                                        @else
                                            <td class="text-danger">
                                                {{ $exam_order->notes }}
                                            </td>
                                        @endif
                                        @if ($current_role === \App\Models\User::EXAMS_SUPERVISOR_ROLE)
                                            <td class="text-success">
                                                {{ $exam_order->tester->user->name?? null}}
                                            </td>
                                        @endif
                                        <td>
                                            @can('إجراء طلب اختبار')
                                                @if($exam_order->status === \App\Models\ExamOrder::IN_PENDING_STATUS)
                                                    @if ($current_role === \App\Models\User::ADMIN_ROLE || $current_role === \App\Models\User::TEACHER_ROLE && $exam_order->teacher_id === auth()->id())
                                                        <button class="btn btn-outline-danger btn-sm"
                                                                wire:click="lunchModalDelete('{{$exam_order->id}}');">
                                                            حذف الطلب
                                                        </button>
                                                    @endif
                                                    @if ($current_role === \App\Models\User::ADMIN_ROLE || $current_role === \App\Models\User::EXAMS_SUPERVISOR_ROLE)
                                                        <button data-toggle="modal"
                                                                data-target="#approval-exam"
                                                                wire:click="getExamOrder('{{$exam_order->id}}')"
                                                                class="btn btn-outline-success btn-sm">قبول الطلب
                                                        </button>
                                                        <button class="btn btn-outline-warning btn-sm"
                                                                data-toggle="modal"
                                                                data-target="#refusal-exam"
                                                                wire:click="getExamOrder('{{$exam_order->id}}')">رفض
                                                            الطلب
                                                        </button>
                                                    @endif
                                                @elseif($exam_order->status === \App\Models\ExamOrder::ACCEPTABLE_STATUS)
                                                    @if ($current_role === \App\Models\User::ADMIN_ROLE || $current_role === \App\Models\User::EXAMS_SUPERVISOR_ROLE)
                                                        <button data-toggle="modal"
                                                                data-target="#approval-exam"
                                                                wire:click="getExamOrder('{{$exam_order->id}}')"
                                                                class="btn btn-outline-success btn-sm">تحديث قبول الطلب
                                                        </button>
                                                        <button class="btn btn-outline-warning btn-sm"
                                                                data-toggle="modal"
                                                                data-target="#refusal-exam"
                                                                wire:click="getExamOrder('{{$exam_order->id}}')">رفض
                                                            الطلب
                                                        </button>
                                                    @endif
                                                @elseif($exam_order->status === \App\Models\ExamOrder::REJECTED_STATUS)
                                                    @if ($current_role === \App\Models\User::ADMIN_ROLE || $current_role === \App\Models\User::TEACHER_ROLE && $exam_order->teacher_id === auth()->id())
                                                        <button class="btn btn-outline-danger btn-sm"
                                                                wire:click="lunchModalDelete('{{$exam_order->id}}');">
                                                            حذف الطلب
                                                        </button>
                                                    @endif
                                                    @if ($current_role === \App\Models\User::ADMIN_ROLE || $current_role === \App\Models\User::EXAMS_SUPERVISOR_ROLE)
                                                        <button data-toggle="modal"
                                                                data-target="#approval-exam"
                                                                wire:click="getExamOrder('{{$exam_order->id}}')"
                                                                class="btn btn-outline-success btn-sm">قبول الطلب
                                                        </button>
                                                    @endif
                                                @elseif($exam_order->status === \App\Models\ExamOrder::FAILURE_STATUS)
                                                    @if ($current_role === \App\Models\User::ADMIN_ROLE || $current_role === \App\Models\User::TEACHER_ROLE && $exam_order->teacher_id === auth()->id())
                                                        <button class="btn btn-outline-danger btn-sm"
                                                                wire:click="lunchModalDelete('{{$exam_order->id}}');">
                                                            حذف الطلب
                                                        </button>
                                                    @endif
                                                @endif
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr style="text-align: center">
                                        <td colspan="8">No data available in table</td>
                                    </tr>
                                @endforelse
                                @include('pages.exams_orders.exam_order_approval')
                                @include('pages.exams_orders.exam_order_refusal')
                                @include('pages.exams_orders.delete_exam_order')
                                </tbody>
                                <tfoot>
                                <tr class="text-dark table-success">
                                    <th>#</th>
                                    <th>اسم الطالب</th>
                                    <th>جزء الإختبار</th>
                                    <th>اسم المحفظ</th>
                                    <th>حالة الطلب</th>
                                    <th>تاريخ الطلب</th>
                                    <th>يوم الإختبار المقترح</th>
                                    <th>ملاحظات/تاريخ الإختبار</th>
                                    @if($current_role === \App\Models\User::EXAMS_SUPERVISOR_ROLE)
                                        <th>اسم المختبر</th>
                                    @endif
                                    <th>العمليات</th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>

                        @if (isset($exam_orders) && !empty($exam_orders))
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
                        @endif

                    @endcan
                </div>
            </div>
        </div>
    @endif
    <x-loading-indicator></x-loading-indicator>
</div>
@push('js')
    <script>
        $("#grade").on('change', function (e) {
            let id = $(this).val()
        @this.set('selectedGradeId', id);
            livewire.emit('getTeachersByGradeId');
        });

        $("#teacher").on('change', function (e) {
            let id = $(this).val()
        @this.set('selectedTeacherId', id);
            livewire.emit('getStudentsByTeacherId', id);
        });

        $("#student").on('change', function (e) {
            let id = $(this).val()
        @this.set('selectedStudentId', id);
        });
    </script>
@endpush
