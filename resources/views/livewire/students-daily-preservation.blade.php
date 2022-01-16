<div class="row">
    <div>
        @if(Session::has('success_message'))
            <script>
                $(function () {
                    toastr.success("{{ Session::get('success_message') }}");
                })
            </script>
        @endif
    </div>
    <div class="col-xl-12 mb-30">
        @if (auth()->user()->current_role == 'أمير المركز' ||
             auth()->user()->current_role == 'مشرف' ||
             auth()->user()->current_role == 'اداري' ||
             auth()->user()->current_role == 'محفظ')
            @can('إدارة متابعة الحفظ والمراجعة')
                <div class="card-body">
                    <div class="row">
                        @if (auth()->user()->current_role == 'أمير المركز')
                            @if (isset($grades))
                                <div>
                                    <label style="font-size: 15px; color: #1e7e34">المراحل*</label>
                                    <div>
                                        <select class="selectpicker" data-style="btn-info"
                                                wire:model="searchGradeId">
                                            <option value="" selected>جميع المراحل
                                            </option>
                                            @foreach ($grades as $grade)
                                                <option
                                                    value="{{ $grade->id }}">{{ $grade->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endif
                        @endif
                        @if (auth()->user()->current_role == 'مشرف' || auth()->user()->current_role == 'اداري'
                             || auth()->user()->current_role == 'أمير المركز')
                            @if (isset($groups))
                                <div style="padding-right: 10px;">
                                    <label style="font-size: 15px; color: #1e7e34">المحفظين*</label>
                                    <div>
                                        <select class="selectpicker" data-style="btn-info"
                                                wire:model="searchGroupId">
                                            <option value="" selected>جميع المحفظين
                                            </option>
                                            @foreach ($groups as $group)
                                                <option
                                                    value="{{ $group->id }}">{{ $group->teacher->user->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                    <br>
                    @include('livewire.search')
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
                                    <td>{{ $student->group->teacher->user->name }}</td>
                                    <?php
                                    if (isset($student)) {
                                        $attendance_student = $student->attendance()->where('attendance_date', date('Y-m-d'))->first();
                                    }
                                    ?>
                                    <td>
                                        @if(isset($attendance_student->student_id))
                                            <label class="block text-gray-500 font-semibold sm:border-r sm:pr-4">
                                                <input disabled
                                                       {{ $attendance_student->attendance_status == 1 ? 'checked' : '' }}
                                                       class="leading-tight" type="radio" value="presence">
                                                <span class="text-success">حضور</span>
                                            </label>

                                            <label class="ml-4 block text-gray-500 font-semibold">
                                                <input disabled
                                                       {{ $attendance_student->attendance_status == 2 ? 'checked' : '' }}
                                                       class="leading-tight" type="radio" value="presence">
                                                <span class="text-success">تأخر</span>
                                            </label>

                                            <label class="ml-4 block text-gray-500 font-semibold">
                                                <input disabled
                                                       {{ $attendance_student->attendance_status == 0 ? 'checked' : '' }}
                                                       class="leading-tight" type="radio" value="absent">
                                                <span class="text-danger">غياب</span>
                                            </label>

                                        @else

                                            <label class="block text-gray-500 font-semibold sm:border-r sm:pr-4">
                                                <input class="leading-tight"
                                                       type="radio"
                                                       name="flexRadioDefault.{{$loop->iteration}}"
                                                       wire:click="store_Attendance({{$student->id}},true)">
                                                <span class="text-success">حضور</span>
                                            </label>

                                            <label class="ml-4 block text-gray-500 font-semibold">
                                                <input class="leading-tight"
                                                       type="radio"
                                                       name="flexRadioDefault.{{$loop->iteration}}"
                                                       wire:click="store_Attendance({{$student->id}},2)">
                                                <span class="text-success">تأخر</span>
                                            </label>

                                            <label class="ml-4 block text-gray-500 font-semibold">
                                                <input class="leading-tight"
                                                       type="radio"
                                                       name="flexRadioDefault.{{$loop->iteration}}"
                                                       wire:click="store_Attendance({{$student->id}},false)">
                                                <span class="text-danger">غياب</span>
                                            </label>

                                        @endif

                                    </td>
                                    <td>
                                        @if(isset($attendance_student->attendance_status))
                                            @if ($attendance_student->attendance_status == 1 || $attendance_student->attendance_status == 2)
                                                <button class="btn btn-outline-danger btn-sm"
                                                        wire:click.prevent="loadModalData({{$student->id}},-1)">إضافة
                                                </button>
                                            @else
                                                <button disabled class="btn btn-outline-danger btn-sm">إضافة</button>
                                            @endif
                                        @else
                                            <button disabled class="btn btn-outline-danger btn-sm">إضافة</button>
                                        @endif
                                        <button class="btn btn-outline-primary btn-sm"
                                                wire:click.prevent="loadModalData({{$student->id}},1)">أخر حفظ
                                        </button>
                                        <button class="btn btn-outline-success btn-sm"
                                                wire:click.prevent="loadModalData({{$student->id}},2)">أخر مراجعة
                                        </button>
                                    </td>
                                </tr>
                                @include('pages.students_daily_preservation.add_daily_preservation')
                                @include('pages.students_daily_preservation.show_daily_preservation')
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
    <x-loading-indicator/>
</div>
