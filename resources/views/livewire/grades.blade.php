<div class="row">
    <div class="col-xl-12 mb-30">
        <div class="card card-statistics h-100">
            @if (auth()->user()->current_role === \App\Models\User::ADMIN_ROLE)
                <div class="card-body">
                    <li class="list-group-item">
                        <div class="row">
                            <div class="col-3">
                                @can('إضافة مرحلة')
                                    <button type="button" wire:click="modalFormReset()"
                                            class="button x-small"
                                            data-toggle="modal"
                                            data-target="#gradeAdded">
                                        اضافة مرحلة
                                    </button>
                                @endcan
                            </div>
                            <div class="col-md-3">
                                <button class="btn btn-outline-success"
                                        wire:click.prevent="all_students_export();">تصدير بيانات طلاب المركز
                                </button>
                            </div>
                            <div class="col-md-3">
                                <button class="btn btn-outline-success"
                                        wire:click.prevent="all_teachers_export();">تصدير بيانات محفظي المركز
                                </button>
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
                                <th wire:click="sortBy('name')" style="cursor: pointer;">اسم المرحلة
                                    @include('livewire._sort-icon',['field'=>'name'])
                                </th>
                                <th>القسم</th>
                                <th>عدد محفظي المرحلة</th>
                                <th>عدد حلقات المرحلة</th>
                                <th>عدد طلاب المرحلة</th>
                                <th>العمليات</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($grades as $grade)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $grade->name }}</td>
                                    <td>{{ \App\Models\Grade::sections()[$grade->section] }}</td>
                                    <td>{{ $grade->teachers_count }}</td>
                                    <td>{{ $grade->groups_count }}</td>
                                    <td>{{ $grade->students_count }}</td>
                                    <td>
                                        @if ($grade->teachers_count > 0)
                                            <button type="button"
                                                    wire:click.prevent="grade_teachers_export('{{$grade->id}}');"
                                                    class="btn btn-success btn-sm"
                                                    title="تصدير بيانات المحفظين"><i
                                                    class="fa fa-download"></i></button>
                                        @endif
                                        @if ($grade->students_count > 0)
                                            <button type="button"
                                                    wire:click.prevent="grade_students_export('{{$grade->id}}');"
                                                    class="btn btn-primary btn-sm"
                                                    title="تصدير بيانات الطلاب"><i
                                                    class="fa fa-download"></i></button>
                                        @endif
                                        @can('تعديل مرحلة')
                                            <button type="button" class="btn btn-info btn-sm"
                                                    data-toggle="modal"
                                                    data-target="#gradeEdited"
                                                    wire:click.prevent="loadModalData('{{$grade->id}}')"
                                                    title="تعديل"><i class="fa fa-edit"></i>
                                            </button>
                                        @endcan
                                        @can('حذف مرحلة')
                                            <button type="button" class="btn btn-danger btn-sm"
                                                    data-toggle="modal"
                                                    data-target="#gradeDeleted"
                                                    title="حذف"><i
                                                    class="fa fa-trash"></i></button>
                                        @endcan
                                    </td>
                                </tr>

                                @include('pages.grades.edit')
                                @include('pages.grades.delete')

                            @empty
                                <tr style="text-align: center">
                                    <td colspan="8">No data available in table</td>
                                </tr>
                            @endforelse

                            </tbody>
                            <tfoot>
                            <tr class="text-dark table-success">
                                <th>#</th>
                                <th>اسم المرحلة</th>
                                <th>القسم</th>
                                <th>عدد محفظي المرحلة</th>
                                <th>عدد حلقات المرحلة</th>
                                <th>عدد طلاب المرحلة</th>
                                <th>العمليات</th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                    @include('pages.grades.add')
                    <div id="datatable_wrapper"
                         class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">
                        <div class="row">
                            <div class="col-sm-12 col-md-5">
                                <div class="dataTables_info" id="datatable_info" role="status"
                                     aria-live="polite">
                                    Showing {{$grades->firstItem()}} to {{$grades->lastItem()}}
                                    of {{$grades->total()}} entries
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-7">
                                <div class="dataTables_paginate paging_simple_numbers"
                                     id="datatable_paginate">
                                    <ul class="pagination">
                                        {{$grades->links()}}
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
    <x-loading-indicator></x-loading-indicator>
</div>
