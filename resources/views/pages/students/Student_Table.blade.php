@if (auth()->user()->current_role == 'أمير المركز' ||
     auth()->user()->current_role == 'مشرف' ||
     auth()->user()->current_role == 'اداري' ||
     auth()->user()->current_role == 'محفظ')
    <div class="card-body">
        @can('إدارة الطلاب')
            <div class="row">
                @if (auth()->user()->current_role == 'أمير المركز')
                    @if (isset($grades))
                        <div>
                            <label>
                                <select class="selectpicker" data-style="btn-info"
                                        wire:model="searchGradeId">
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
                @endif
                @if (auth()->user()->current_role == 'مشرف' || auth()->user()->current_role == 'اداري')
                    @if (isset($groups))
                        <div style="padding-right: 10px;">
                            <label>
                                <select class="selectpicker" data-style="btn-info"
                                        wire:model="searchGroupId">
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
                        <th>رقم الهوية</th>
                        <th>تاريخ الميلاد</th>
                        <th>اسم المرحلة</th>
                        <th>اسم المجموعة</th>
                        <th>البريد الإلكتروني</th>
                        <th>العمليات</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($students as $student)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $student->user->name }}</td>
                            <td>{{ $student->user->identification_number }}</td>
                            <td>{{ $student->user->dob }}</td>
                            <td>{{ $student->grade->name }}</td>
                            <td>{{ $student->group->name }}</td>
                            <td>{{ $student->user->email }}</td>
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
                                        <button class="dropdown-item"><i style="color: #ffc107"
                                                                         class="fa fa-eye"></i>&nbsp; عرض
                                            بيانات الطالب
                                        </button>
                                        @can('إجراء طلب اختبار')
                                            <button class="dropdown-item"
                                                    wire:click="checkLastExamStatus({{$student->id}})">
                                                <i
                                                    style="color:#0000cc" class="fa fa-first-order"></i>إجراء
                                                طلب اختبار
                                            </button>
                                        @endcan
                                        @can('تعديل طالب')
                                            <a class="dropdown-item" href="#"
                                               wire:click="edit({{ $student->id }})"><i
                                                    style="color:green" class="fa fa-edit"></i> تعديل بيانات
                                                الطالب</a>
                                        @endcan
                                        @can('حذف طالب')
                                            <button class="dropdown-item" data-toggle="modal"
                                                    data-target="#delete_student"
                                                    wire:click.prevent="getStudent({{$student->id}})"><i
                                                    style="color: red" class="fa fa-trash"></i>&nbsp; حذف
                                                بيانات
                                                الطالب
                                            </button>
                                        @endcan
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @include('pages.students.delete')
                        @include('pages.students.submit_exam_request')
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
                        <th>البريد الإلكتروني</th>
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
