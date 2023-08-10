<div>
    <div>
        @if ($catchError)
            <div class="alert alert-danger" id="success-danger">
                <button type="button" wire:click.prevent="resetMessage()" class="close" data-dismiss="alert">x</button>
                {{ $catchError }}
            </div>
        @endif
    </div>
    <div class="col-xl-12 mb-30">
        @if ($current_role === \App\Models\User::SUPERVISOR_ROLE || $current_role === \App\Models\User::TEACHER_ROLE)
            @can('إدارة متابعة الحفظ والمراجعة')
                <div class="card-body">
                    <li class="list-group-item">
                        <div class="row">
                            <div class="col-md-3">
                                <label style="font-size: 15px; color: #1e7e34">المراحل*</label>
                                <select style="width: 100%" wire:model="selectedGradeId" id="grade"
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
                                <select style="width: 100%" class="custom-select mr-sm-2 select2" id="teacher"
                                        wire:model="selectedTeacherId">
                                    <option value="">الكل</option>
                                    @foreach ($groups as $group)
                                        <option
                                            value="{{ $group->id }}">
                                            @if ($group->teacher_id == null)
                                                {{$group->name . ' (لا يوجد محفظ)'}}
                                            @else
                                                {{ $group->teacher->user->name }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </li>
                    <br>
                    <x-search></x-search>
                    <div class="table-responsive mt-15">
                        <table class="table center-aligned-table mb-0">
                            <thead>
                            <tr class="text-dark table-success">
                                <th wire:click="sortBy('id')" style="cursor: pointer;">#
                                    @include('livewire._sort-icon',['field'=>'id'])
                                </th>
                                <th class="alert-success">اسم الطالب</th>
                                <th class="alert-success">اسم المرحلة</th>
                                <th class="alert-success">اسم المحفظ</th>
                                <th class="alert-success">
                                    الحضور والغياب
                                </th>
                                <th class="alert-success">العمليات</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($students as $student)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $student->user->name }}</td>
                                    <td>{{ $student->grade->name }}</td>
                                    <td>
                                        @if (isset( $student->group_sunnah))
                                            {{ $student->group_sunnah->teacher->user->name }}
                                        @endif
                                    </td>
                                    <div hidden></div>
                                    <?php
                                    if (isset($student)) {
                                        $attendance_student = $student->attendance_sunnah_today->first();
                                    }
                                    ?>
                                    <td>
                                        <div hidden></div>
                                        <label class="block text-gray-500 font-semibold sm:border-r sm:pr-4">
                                            <input
                                                {{ isset($attendance_student->status) && $attendance_student->status === \App\Models\StudentSunnahAttendance::PRESENCE_STATUS ? 'checked' : '' }} class="leading-tight"
                                                type="radio"
                                                name="flexRadioDefault.{{$loop->iteration}}"
                                                wire:click.prevent="store_Attendance({{$student->id}},'{{\App\Models\StudentSunnahAttendance::PRESENCE_STATUS}}')">
                                            <span class="text-success">حضور</span>
                                        </label>

                                        <label class="ml-1 block text-gray-500 font-semibold">
                                            <input
                                                {{ isset($attendance_student->status) && $attendance_student->status === \App\Models\StudentSunnahAttendance::LATE_STATUS ? 'checked' : '' }} class="leading-tight"
                                                type="radio"
                                                name="flexRadioDefault.{{$loop->iteration}}"
                                                wire:click.prevent="store_Attendance({{$student->id}},'{{\App\Models\StudentSunnahAttendance::LATE_STATUS}}')">
                                            <span class="text-warning">تأخر</span>
                                        </label>

                                        <label class="ml-1 block text-gray-500 font-semibold">
                                            <input
                                                {{ isset($attendance_student->status) && $attendance_student->status === \App\Models\StudentSunnahAttendance::AUTHORIZED_STATUS ? 'checked' : '' }} class="leading-tight"
                                                type="radio"
                                                name="flexRadioDefault.{{$loop->iteration}}"
                                                wire:click.prevent="store_Attendance({{$student->id}},'{{\App\Models\StudentSunnahAttendance::AUTHORIZED_STATUS}}')">
                                            <span class="text-info">مأذون</span>
                                        </label>

                                        <label class="ml-1 block text-gray-500 font-semibold">
                                            <input
                                                {{ isset($attendance_student->status) && $attendance_student->status === \App\Models\StudentSunnahAttendance::ABSENCE_STATUS ? 'checked' : '' }} class="leading-tight"
                                                type="radio"
                                                name="flexRadioDefault.{{$loop->iteration}}"
                                                wire:click.prevent="store_Attendance({{$student->id}},'{{\App\Models\StudentSunnahAttendance::ABSENCE_STATUS}}')">
                                            <span class="text-danger">غياب</span>
                                        </label>
                                    </td>
                                    <td>
                                        @if(isset($attendance_student->status))
                                            @if ($attendance_student->status === \App\Models\StudentSunnahAttendance::PRESENCE_STATUS || $attendance_student->status === \App\Models\StudentSunnahAttendance::LATE_STATUS)
                                                <div hidden></div>
                                                <button class="btn btn-outline-danger btn-sm"
                                                        wire:click="loadModalData({{$student->id}},-1)">
                                                    إضافة
                                                </button>
                                            @else
                                                <button disabled class="btn btn-outline-danger btn-sm">إضافة
                                                </button>
                                            @endif
                                        @else
                                            <button disabled class="btn btn-outline-danger btn-sm"
                                                    wire:click="">إضافة
                                            </button>
                                        @endif
                                        <button class="btn btn-outline-primary btn-sm"
                                                wire:click="loadModalData({{$student->id}},1)">أخر حفظ
                                        </button>
                                        <button class="btn btn-outline-success btn-sm"
                                                wire:click="loadModalData({{$student->id}},2)">أخر مراجعة
                                        </button>
                                    </td>
                                </tr>
                                @include('pages.students_daily_memorization_sunnah.add_daily_memorization')
                                @include('pages.students_daily_memorization_sunnah.show_daily_memorization')
                            @empty
                                <tr style="text-align: center">
                                    <td colspan="9">No data available in table</td>
                                </tr>
                            @endforelse
                            </tbody>
                            <tfoot>
                            <tr class="text-dark table-success">
                                <th>#</th>
                                <th class="alert-success">اسم الطالب</th>
                                <th class="alert-success">اسم المرحلة</th>
                                <th class="alert-success">اسم المحفظ</th>
                                <th class="alert-success">الحضور والغياب</th>
                                <th class="alert-success">العمليات</th>
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
                                    Showing {{$students->firstItem()}} to {{$students->lastItem()}}
                                    of {{$students->total()}} entries
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-7">
                                <div class="dataTables_paginate paging_simple_numbers"
                                     id="datatable_paginate">
                                    <ul class="pagination">
                                        {{$students->links()}}
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endcan

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

        $("#type").on('change', function (e) {
            let id = $(this).val()
        @this.set('selectedType', id);
            livewire.emit('getLastDataModalByType', id);
        });
    </script>
@endpush
