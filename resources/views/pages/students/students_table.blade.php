@if ($current_role == \App\Models\User::ADMIN_ROLE || $current_role == \App\Models\User::SUPERVISOR_ROLE || $current_role == \App\Models\User::TEACHER_ROLE)
    <div class="card-body">
        @can('إدارة الطلاب')
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
                                <option
                                    value="{{ $group->id }}">{{ $group->teacher->user->name}}</option>
                            @endforeach
                        </select>
                    </div>

                    @if($current_role == \App\Models\User::TEACHER_ROLE)
                        <div class="col-md-2">
                            <button class="btn btn-success float-right"
                                    wire:click.prevent="export();">تصدير بيانات طلاب الحلقة
                            </button>
                        </div>
                    @endif
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
                        <th>اسم الطالب</th>
                        <th>رقم الهوية</th>
                        <th>تاريخ الميلاد</th>
                        <th>اسم المرحلة</th>
                        <th>اسم المجموعة</th>
                        <th>حالة الطالب</th>
                        <th>العمليات</th>
                    </tr>
                    </thead>
                    <tbody wire:loading.class="text-muted">
                    @forelse($students as $student)
                        @php
                            $warning = false;
                            $block = false;
                            $selectClass = '';
                            if (isset($student)) {
                                if ($student->student_is_block != null) {
                                          $block = true;
                                          $selectClass = 'text-dark table-danger';
                                } else if ($student->student_is_warning != null) {
                                  $warning = true;
                                  $selectClass = 'text-dark table-warning';
                                }}
                        @endphp
                        <tr class="{{$selectClass}}">
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <img src="{{$student->user->profile_photo_url}}" style="width: 50px; height: 50px;"
                                     class="img-fluid mr-15 avatar-small" alt="">
                                {{$student->user->name}}
                            </td>
                            <td>{{ $student->user->identification_number }}</td>
                            <td>{{ $student->user->dob }}</td>
                            <td>{{ $student->grade->name }}</td>
                            <td>{{ $student->group->name }}</td>
                            <td>
                                @if($block == true)
                                    @if($current_role == \App\Models\User::ADMIN_ROLE)
                                        <button class="btn btn-outline-danger btn-sm"
                                                data-toggle="modal"
                                                wire:click="getStudent('{{$student->id}}')"
                                                data-target="#block_cancel">
                                            فك الحظر
                                        </button>
                                    @else
                                        <button class="btn btn-outline-danger btn-sm">محظور</button>
                                    @endif
                                @elseif($warning == true)
                                    @if($current_role == \App\Models\User::ADMIN_ROLE || \App\Models\User::SUPERVISOR_ROLE)
                                        <button class="btn btn-outline-warning btn-sm"
                                                data-toggle="modal"
                                                wire:click="getStudent('{{$student->id}}')"
                                                data-target="#warning_cancel">
                                            إلغاء الإنذار
                                        </button>
                                    @endif
                                @endif
                            </td>
                            <td class="embed-responsive-item">
                                <div class="btn-group mb-1 embed-responsive-item">
                                    <button type="button" class="btn btn-success">العمليات</button>
                                    <button type="button"
                                            class="btn btn-success dropdown-toggle dropdown-toggle-split"
                                            data-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                        <span class="sr-only">العمليات</span>
                                    </button>
                                    <div class="dropdown-menu embed-responsive-item" x-placement="top-end"
                                         style="position: absolute; transform: translate3d(0px, 32px, 0px); top: 0px; left: 0px; will-change: transform;">
                                        <button class="dropdown-item"
                                                @click.prevent="currentTab = 'form'"
                                                wire:click="process_data('{{ $student->id }}','show')"><i
                                                style="color: #ffc107"
                                                class="fa fa-eye"></i>&nbsp; عرض
                                            بيانات الطالب
                                        </button>
                                        @if($block == false)
                                            @can('إجراء طلب اختبار')
                                                <button class="dropdown-item"
                                                        wire:click="requestExam('{{$student->id}}')">
                                                    <i
                                                        style="color:#0000cc" class="fa fa-first-order"></i>إجراء
                                                    طلب اختبار
                                                </button>
                                            @endcan
                                            @can('تصفير بيانات الحفظ والمراجعة')
                                                <a class="dropdown-item" href="#"
                                                   wire:click="process_data('{{ $student->id }}','reset')"><i
                                                        style="color:green" class="fa fa-recycle"></i> تصفير بيانات
                                                    الحفظ والمراجعة</a>
                                            @endcan
                                            @can('تعديل طالب')
                                                <a class="dropdown-item" href="#"
                                                   @click.prevent="currentTab = 'form'"
                                                   wire:click="process_data('{{ $student->id }}','edit')"><i
                                                        style="color:green" class="fa fa-edit"></i> تعديل بيانات
                                                    الطالب</a>
                                            @endcan
                                        @endif
                                    </div>
                                </div>
                                @if($block == true)
                                    <button
                                        wire:click="setMessage('{{$student->student_is_warning->details}}');"
                                        class="btn btn-outline-danger btn-sm" title="الطالب محظور">
                                        تفاصيل أكثر
                                    </button>
                                @elseif($warning == true)
                                    <button
                                        wire:click="setMessage('{{$student->student_is_warning->details}}');"
                                        class="btn btn-outline-warning btn-sm"
                                        title="إنذار نهائي">
                                        تفاصيل أكثر
                                    </button>
                                @endif
                            </td>
                        </tr>
                        {{--                        @include('pages.students.delete')--}}
                        @include('pages.students.warning_cancel')
                        @include('pages.students.block_cancel')
                        @include('pages.students.submit_exam_request')
                        @include('pages.students.reset_data_daily_memorization')
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
                        <th>رقم الهوية</th>
                        <th>تاريخ الميلاد</th>
                        <th>اسم المرحلة</th>
                        <th>اسم المجموعة</th>
                        <th>حالة الطالب</th>
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
        @endcan
    </div>
@endif
