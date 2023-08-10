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

                @if ($current_role === \App\Models\User::ADMIN_ROLE)
                    <div class="col-md-3">
                        <label style="font-size: 15px; color: #1e7e34">حسب العمر*</label>
                        <select style="width: 100%;" class="custom-select mr-sm-2 select2" id="age"
                                wire:model="selectedAge">
                            <option value="">الكل</option>
                            @foreach ($ages as $age)
                                <option
                                    value="{{ $age }}">{{ $age}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label style="font-size: 15px; color: #1e7e34">حسب عدد أجزاء الحفظ*</label>
                        <select style="width: 100%;" class="custom-select mr-sm-2 select2" id="number_quran_part"
                                wire:model="selectedNumberQuranPart">
                            <option value="">الكل</option>
                            @for($i = 1;$i <= 30; $i++)
                                <option
                                    value="{{ $i }}">{{ $i}}</option>
                            @endfor
                        </select>
                    </div>

                @endif

                @if($current_role === \App\Models\User::TEACHER_ROLE)
                    <div class="col-md-2">
                        <button class="btn btn-success float-right"
                                wire:click.prevent="export();">تصدير بيانات طلاب الحلقة
                        </button>
                    </div>
                @endif
            </div>
        </li>
        @if($current_role === \App\Models\User::ADMIN_ROLE)
            <li class="list-group-item">
                <div class="row">
                    <div class="col-md-2">
                        <button class="btn btn-success float-right"
                                wire:click.prevent="export_selected_student();">تصدير بيانات الطلاب
                        </button>
                    </div>
                </div>
            </li>
        @endif
        <br>
        <x-search></x-search>
        <div class="table-responsive mt-15">
            <table class="table center-aligned-table mb-0">
                <thead>
                <tr class="text-dark table-success">
                    <th wire:click="sortBy('id')" style="cursor: pointer;">#
                        @include('livewire._sort-icon',['field'=>'id'])
                    </th>
                    <th>الإسم رباعي</th>
                    <th>رقم الهوية</th>
                    <th>رقم الواتساب</th>
                    <th>تاريخ الميلاد</th>
                    <th>اسم المرحلة</th>
                    <th>اسم المحفظ</th>
                    <th>آخر اختبار</th>
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
                            if ($student->student_block_reason !== null) {
                                      $block = true;
                                      $selectClass = 'text-dark table-danger';
                            } else if ($student->student_warning_reason !== null) {
                              $warning = true;
                              $selectClass = 'text-dark table-warning';
                            }}
                    @endphp
                    <tr class="{{$selectClass}}">
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            @if ($student->profile_photo && Storage::disk('users_images')->exists($student->profile_photo))
                                <img src="{{Storage::disk('users_images')->url($student->profile_photo)}}"
                                     style="width: 50px; height: 50px;"
                                     class="img-fluid mr-15 avatar-small" alt="">
                            @else
                                <img src="{{asset('assets/images/teacher.png')}}" style="width: 50px; height: 50px;"
                                     class="img-fluid mr-15 avatar-small" alt="">
                            @endif
                            {{$student->student_name}}
                        </td>
                        <td>{{ $student->student_identification_number }}</td>
                        <td>{{ (int)$student->student_whatsapp_number }}</td>
                        <td>{{ $student->dob }}</td>
                        <td>{{ $student->grade_name }}</td>
                        <td>
                            @if ($student->teacher_name === null)
                                <span class="badge badge-danger ml-2">لا يوجد محفظ</span>
                            @else
                                @if($current_group_type === \App\Models\Group::SUNNAH_TYPE)
                                    <span class="badge badge-success ml-2">{{$student->teacher_sunnah_name}}</span>
                                @else
                                    <span class="badge badge-success ml-2">{{$student->teacher_name}}</span>
                                @endif
                            @endif
                        </td>
                        <td>
                            @if($student->quran_part_last !== null)
                                <span class="badge badge-success ml-2"> {{ $student->quran_part_last}}</span>
                            @endif
                        </td>
                        <td>
                            @if($block === true)
                                @if($current_role === \App\Models\User::ADMIN_ROLE)
                                    <button class="btn btn-outline-danger btn-sm"
                                            data-toggle="modal"
                                            wire:click="getStudent('{{$student->id}}')"
                                            data-target="#block_cancel">
                                        فك الحظر
                                    </button>
                                @else
                                    <button class="btn btn-outline-danger btn-sm">محظور</button>
                                @endif
                            @elseif($warning === true)
                                @if($current_role === \App\Models\User::ADMIN_ROLE || $current_role === \App\Models\User::SUPERVISOR_ROLE)
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
                                    @can('تعديل طالب')
                                        <button class="dropdown-item" href="#"
                                                @click.prevent="currentTab = 'form'"
                                                wire:click="process_data('{{ $student->id }}','edit')"><i
                                                style="color:darkorange" class="fa fa-edit"></i> تعديل بيانات
                                            الطالب
                                        </button>
                                    @endcan
                                    @can('نقل طالب')
                                        <button class="dropdown-item" href="#"
                                                wire:click="process_data('{{ $student->id }}','move_student')"><i
                                                style="color:#0cd468" class="fas fa-cut"></i> نقل
                                            الطالب
                                        </button>
                                    @endcan
                                    @if($current_role === \App\Models\User::ADMIN_ROLE || $current_role === \App\Models\User::SUPERVISOR_ROLE)
                                        @if ($student->group_sunnah_id === null)
                                            <button class="dropdown-item"
                                                    wire:click="process_data('{{ $student->id }}','add_to_sunnah')">
                                                <i
                                                    style="color: #218838"
                                                    class="fa fa-user-plus"></i> إضافة إلى حلقة السنة
                                            </button>
                                        @else
                                            <button class="dropdown-item"
                                                    wire:click="process_data('{{ $student->id }}','update_to_sunnah')">
                                                <i style="color: #218838"
                                                   class="fas fa-user-edit"></i> تحديث حلقة السنة للطالب
                                            </button>
                                        @endif
                                    @endif
                                    @if($block === false)
                                        @can('إجراء طلب اختبار')
                                            @if($current_role === \App\Models\User::TEACHER_ROLE)
                                                @if ($current_group_type === \App\Models\Group::QURAN_TYPE)
                                                    <button class="dropdown-item"
                                                            wire:click="process_data('{{ $student->id }}','submit_exam_order')">
                                                        <i style="color:#0000cc" class="fa fa-first-order"></i>
                                                        إجراء طلب
                                                        اختبار قرآن
                                                    </button>
                                                @else
                                                    <button class="dropdown-item"
                                                            wire:click="process_data('{{ $student->id }}','submit_exam_sunnah_order')">
                                                        <i style="color:#0000cc" class="fa fa-first-order"></i>
                                                        إجراء طلب
                                                        اختبار سنة
                                                    </button>
                                                @endif
                                            @else
                                                <button class="dropdown-item"
                                                        wire:click="process_data('{{ $student->id }}','submit_exam_order')">
                                                    <i style="color:#0000cc" class="fa fa-first-order"></i>
                                                    إجراء طلب
                                                    اختبار قرآن
                                                </button>
                                                @if ($student->group_sunnah_id !== null)
                                                    <button class="dropdown-item"
                                                            wire:click="process_data('{{ $student->id }}','submit_exam_sunnah_order')">
                                                        <i style="color:#0000cc" class="fa fa-first-order"></i>
                                                        إجراء طلب اختبار سنة
                                                    </button>
                                                @endif
                                            @endif
                                        @endcan
                                        @can('تصفير بيانات الحفظ والمراجعة')
                                            <a class="dropdown-item" href="#"
                                               wire:click="process_data('{{ $student->id }}','reset_daily_memorization')"><i
                                                    style="color:darkred" class="fa fa-recycle"></i> تصفير بيانات
                                                الحفظ والمراجعة</a>
                                        @endcan
                                    @endif
                                </div>
                                @if($block === true)
                                    <button
                                        wire:click="setMessage('{{$student->student_block_reason}}',{{$student->student_block_details}},'block');"
                                        class="btn btn-outline-danger btn-sm" title="الطالب محظور">
                                        تفاصيل أكثر
                                    </button>
                                @elseif($warning === true)
                                    <button
                                        wire:click="setMessage('{{$student->student_warning_reason}}',{{$student->student_warning_details}},'warning');"
                                        class="btn btn-outline-warning btn-sm"
                                        title="إنذار نهائي">
                                        تفاصيل أكثر
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @include('pages.students.warning_cancel')
                    @include('pages.students.block_cancel')
                @empty
                    <tr style="text-align: center">
                        <td colspan="9">No data available in table</td>
                    </tr>
                @endforelse
                </tbody>
                <tfoot>
                <tr class="text-dark table-success">
                    <th>#</th>
                    <th>اسم الطالب</th>
                    <th>رقم الهوية</th>
                    <th>رقم الواتساب</th>
                    <th>تاريخ الميلاد</th>
                    <th>اسم المرحلة</th>
                    <th>اسم المحفظ</th>
                    <th>آخر اختبار</th>
                    <th>حالة الطالب</th>
                    <th>العمليات</th>
                </tr>
                </tfoot>
            </table>
        </div>
        @if (isset($students) && !empty($students))
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
        @endif
    @endcan
</div>
