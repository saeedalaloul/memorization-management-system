<div class="row">
    @if ($current_role === \App\Models\User::SUPERVISOR_ROLE ||  $current_role === \App\Models\User::ADMIN_ROLE)
        <div class="col-xl-12 mb-30">
            <div class="card card-statistics h-100">
                <div class="card-body">
                    @can('إدارة حالة تقارير الطلاب')
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
                                        <label for="student" style="font-size: 15px; color: #1e7e34">حالة
                                            التقارير*</label>
                                        <select style="width: 100%;" wire:model="selectedStatus"
                                                class="custom-select mr-sm-2"
                                                name="selectedStatus">
                                            <option value="" selected>الكل</option>
                                            @foreach(\App\Models\StudentReportsStatus::status() as $key => $value)
                                                <option value="{{$key}}">{{$value}}</option>
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
                                    <th>رقم الواتساب</th>
                                    <th>اسم المحفظ</th>
                                    <th>حالة التقرير</th>
                                    <th>تاريخ التقرير</th>
                                    <th>العمليات</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($failure_reports as $failure_report)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td class="text-success">{{ $failure_report->student->user->name }}</td>
                                        <td class="text-success">
                                            <label
                                                class="badge badge-{{$failure_report->status === \App\Models\StudentReportsStatus::SEND_FAILURE_STATUS ? 'danger':'success'}}">
                                                {{ $failure_report->student->whatsapp_number}}
                                            </label>
                                        </td>
                                        <td class="text-success">
                                            @if ($failure_report->student->group->teacher_id !== null)
                                                {{ $failure_report->student->group->teacher->user->name }}
                                            @else
                                                <label class="badge badge-warning">لا يوجد محفظ</label>
                                            @endif
                                        </td>
                                        <td>
                                            <label
                                                class="badge badge-{{$failure_report->status === \App\Models\StudentReportsStatus::SEND_FAILURE_STATUS ? 'danger':'success'}}">
                                                {{ \App\Models\StudentReportsStatus::status()[$failure_report->status]}}
                                            </label>
                                        </td>
                                        <td class="text-success">
                                            {{ \Carbon\Carbon::parse($failure_report->created_at)->translatedFormat('l j F Y h:i a')}}
                                        </td>
                                        <td>
                                            @if ($failure_report->status === \App\Models\StudentReportsStatus::READY_TO_SEND_STATUS)
                                                <button type="button" class="btn btn-outline-success btn-sm"
                                                        wire:click="sendReportStudentWhatsapp('{{$failure_report->id}}');"
                                                        title="إرسال التقرير">
                                                    <i class="fa fa-check"></i></button>
                                            @endif

                                            <button type="button" class="btn btn-outline-warning btn-sm"
                                                    wire:click="lunchModalEdit('{{$failure_report->id}}');"
                                                    title="تحديث رقم الواتساب">
                                                <i class="fas fa-edit"></i></button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr style="text-align: center">
                                        <td colspan="7">No data available in table</td>
                                    </tr>
                                @endforelse
                                @include('pages.students_reports_status.student_whatsapp_edit_modal')
                                </tbody>
                                <tfoot>
                                <tr class="text-dark table-success">
                                    <th>#</th>
                                    <th>اسم الطالب</th>
                                    <th>رقم الواتساب</th>
                                    <th>اسم المحفظ</th>
                                    <th>حالة التقرير</th>
                                    <th>تاريخ التقرير</th>
                                    <th>العمليات</th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>

                        @if (isset($failure_reports) && !empty($failure_reports))
                            <div id="datatable_wrapper"
                                 class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">
                                <div class="row">
                                    <div class="col-sm-12 col-md-5">
                                        <div class="dataTables_info" id="datatable_info" role="status"
                                             aria-live="polite">
                                            Showing {{$failure_reports->firstItem()}}
                                            to {{$failure_reports->lastItem()}}
                                            of {{$failure_reports->total()}} entries
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-7">
                                        <div class="dataTables_paginate paging_simple_numbers"
                                             id="datatable_paginate">
                                            <ul class="pagination">
                                                {{$failure_reports->links()}}
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
        <x-loading-indicator></x-loading-indicator>
    @endif
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
        });
    </script>
@endpush
