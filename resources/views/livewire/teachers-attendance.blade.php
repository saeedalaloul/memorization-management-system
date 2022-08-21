<div class="row">
    <div>
        @if ($catchError)
            <div class="alert alert-danger" id="success-danger">
                <button type="button" wire:click.prevent="resetMessage()" class="close" data-dismiss="alert">x</button>
                {{ $catchError }}
            </div>
        @endif
    </div>

    <div class="col-xl-12 mb-30">
        @if ($current_role == \App\Models\User::ADMIN_ROLE ||$current_role == \App\Models\User::SUPERVISOR_ROLE)
            @can('إدارة حضور وغياب المحفظين')
                <h5 style="font-family: 'Cairo', sans-serif;color: red"> تاريخ اليوم : {{ date('Y-m-d') }}</h5>
                <div class="card-body">
                    <li class="list-group-item">
                        <div class="row">
                            <div class="col-md-3">
                                <label style="font-size: 15px; color: #1e7e34">المراحل*</label>
                                <select style="width: 100%;" wire:model="selectedGradeId" id="grade"
                                        class="custom-select mr-sm-2">
                                    <option value="">الكل</option>
                                    @foreach ($grades as $grade)
                                        <option
                                            value="{{ $grade->id }}">{{ $grade->name}}</option>
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
                            <tr class="table-success">
                                <th wire:click="sortBy('id')" style="cursor: pointer;">#
                                    @include('livewire._sort-icon',['field'=>'id'])
                                </th>
                                <th class="alert-success">اسم المحفظ</th>
                                <th class="alert-success">اسم المرحلة</th>
                                <th class="alert-success">
                                    <label class="block text-gray-500 font-semibold sm:border-r sm:pr-4">
                                        <input class="leading-tight"
                                               type="radio"
                                               name="flexRadioDefault"
                                               {{$isSelectedRadioBtn1 == true ? 'checked':''}}
                                               wire:click="checkAllRadioBtn('{{\App\Models\TeacherAttendance::PRESENCE_STATUS}}');">
                                        <span class="text-success">حضور</span>
                                    </label>
                                    <label class="ml-1 block text-gray-500 font-semibold">
                                        <input class="leading-tight"
                                               type="radio"
                                               name="flexRadioDefault"
                                               {{$isSelectedRadioBtn2 == true ? 'checked':''}}
                                               wire:click="checkAllRadioBtn('{{\App\Models\TeacherAttendance::LATE_STATUS}}');">
                                        <span class="text-success">تأخر</span>
                                    </label>
                                    <label class="ml-1 block text-gray-500 font-semibold">
                                        <input class="leading-tight"
                                               type="radio"
                                               name="flexRadioDefault"
                                               {{$isSelectedRadioBtn3 == true ? 'checked':''}}
                                               wire:click="checkAllRadioBtn('{{\App\Models\TeacherAttendance::AUTHORIZED_STATUS}}');">
                                        <span class="text-success">مأذون</span>
                                    </label>
                                    <label class="ml-1 block text-gray-500 font-semibold">
                                        <input class="leading-tight"
                                               type="radio"
                                               name="flexRadioDefault"
                                               {{$isSelectedRadioBtn0 == true ? 'checked':''}}
                                               wire:click="checkAllRadioBtn('{{\App\Models\TeacherAttendance::ABSENCE_STATUS}}');">
                                        <span class="text-danger">غياب</span>
                                    </label>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($teachers as $teacher)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $teacher->user->name }}</td>
                                    <td>{{ $teacher->grade->name }}</td>
                                    <?php
                                    if (isset($teacher)) {
                                        $attendance_teacher = $teacher->attendance_today->first();
                                    }
                                    ?>
                                    <td>
                                        @if(isset($attendance_teacher->teacher_id))
                                            <label class="block text-gray-500 font-semibold sm:border-r sm:pr-4">
                                                <input disabled
                                                       {{ $attendance_teacher->status == \App\Models\TeacherAttendance::PRESENCE_STATUS ? 'checked' : '' }}
                                                       class="leading-tight" type="radio" value="presence">
                                                <span class="text-success">حضور</span>
                                            </label>

                                            <label class="ml-1 block text-gray-500 font-semibold">
                                                <input disabled
                                                       {{ $attendance_teacher->status == \App\Models\TeacherAttendance::LATE_STATUS ? 'checked' : '' }}
                                                       class="leading-tight" type="radio" value="presence">
                                                <span class="text-success">تأخر</span>
                                            </label>

                                            <label class="ml-1 block text-gray-500 font-semibold">
                                                <input disabled
                                                       {{ $attendance_teacher->status == \App\Models\TeacherAttendance::AUTHORIZED_STATUS ? 'checked' : '' }}
                                                       class="leading-tight" type="radio" value="presence">
                                                <span class="text-success">مأذون</span>
                                            </label>

                                            <label class="ml-1 block text-gray-500 font-semibold">
                                                <input disabled
                                                       {{ $attendance_teacher->status == \App\Models\TeacherAttendance::ABSENCE_STATUS ? 'checked' : '' }}
                                                       class="leading-tight" type="radio" value="absent">
                                                <span class="text-danger">غياب</span>
                                            </label>

                                        @else

                                            <label class="block text-gray-500 font-semibold sm:border-r sm:pr-4">
                                                <input class="leading-tight"
                                                       type="radio"
                                                       name="flexRadioDefault.{{$loop->iteration}}"
                                                       {{$isSelectedRadioBtn1 == true ? 'checked':''}}
                                                       wire:click="teacherStatusChange({{$teacher->id}},'{{\App\Models\TeacherAttendance::PRESENCE_STATUS}}')">
                                                <span class="text-success">حضور</span>
                                            </label>

                                            <label class="ml-1 block text-gray-500 font-semibold">
                                                <input class="leading-tight"
                                                       type="radio"
                                                       name="flexRadioDefault.{{$loop->iteration}}"
                                                       {{$isSelectedRadioBtn2 == true ? 'checked':''}}
                                                       wire:click="teacherStatusChange({{$teacher->id}},'{{\App\Models\TeacherAttendance::LATE_STATUS}}')">
                                                <span class="text-success">تأخر</span>
                                            </label>

                                            <label class="ml-1 block text-gray-500 font-semibold">
                                                <input class="leading-tight"
                                                       type="radio"
                                                       name="flexRadioDefault.{{$loop->iteration}}"
                                                       {{$isSelectedRadioBtn3 == true ? 'checked':''}}
                                                       wire:click="teacherStatusChange({{$teacher->id}},'{{\App\Models\TeacherAttendance::AUTHORIZED_STATUS}}')">
                                                <span class="text-success">مأذون</span>
                                            </label>

                                            <label class="ml-1 block text-gray-500 font-semibold">
                                                <input class="leading-tight"
                                                       type="radio"
                                                       name="flexRadioDefault.{{$loop->iteration}}"
                                                       {{$isSelectedRadioBtn0 == true ? 'checked':''}}
                                                       wire:click="teacherStatusChange({{$teacher->id}},'{{\App\Models\TeacherAttendance::ABSENCE_STATUS}}')">
                                                <span class="text-danger">غياب</span>
                                            </label>

                                        @endif

                                    </td>
                                </tr>
                            @empty
                                <tr style="text-align: center">
                                    <td colspan="8">No data available in table</td>
                                </tr>
                            @endforelse
                            </tbody>
                            <tfoot>
                            <tr class="table-success">
                                <th>#</th>
                                <th class="alert-success">اسم المحفظ</th>
                                <th class="alert-success">اسم المرحلة</th>
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
                                    Showing {{$teachers->firstItem()}} to {{$teachers->lastItem()}}
                                    of {{$teachers->total()}} entries
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-7">
                                <div class="dataTables_paginate paging_simple_numbers"
                                     id="datatable_paginate">
                                    <ul class="pagination">
                                        {{$teachers->links()}}
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <P>
                        <button class="btn btn-success" type="submit" wire:click.prevent="store()">تأكيد</button>
                    </P>
                </div>
            @endcan

    </div>
    @endif
    <x-loading-indicator></x-loading-indicator>
</div>
