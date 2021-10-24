@if (auth()->user()->current_role == 'أمير المركز' ||
     auth()->user()->current_role == 'مشرف' ||
     auth()->user()->current_role == 'اداري')
    <div class="card-body">
        @can('إدارة المحفظين')
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
            </div>
            @include('livewire.search')
            <div class="table-responsive mt-15">
                <table class="table center-aligned-table mb-0">
                <thead>
                <tr class="text-dark table-success">
                        <th wire:click="sortBy('id')" style="cursor: pointer;">#
                            @include('livewire._sort-icon',['field'=>'id'])
                        </th>
                        <th>اسم المحفظ</th>
                        <th>رقم الهوية</th>
                        <th>البريد الإلكتروني</th>
                        <th>رقم الجوال</th>
                        <th>اسم المرحلة</th>
                        <th>العمليات</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($teachers as $teacher)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{$teacher->user->name}}</td>
                            <td>{{$teacher->user->identification_number}}</td>
                            <td>{{$teacher->user->email}}</td>
                            <td>{{$teacher->user->phone}}</td>
                            <td>{{$teacher->grade->name}}</td>
                            <td>
                                @can('تعديل محفظ')
                                    <button
                                        class="btn btn-info btn-sm" role="button"
                                        wire:click.prevent="loadModalData({{$teacher->id}})"
                                        aria-pressed="true"><i
                                            class="fa fa-edit"></i></button>
                                @endcan
                                @can('حذف محفظ')
                                    <button type="button" class="btn btn-danger btn-sm"
                                            data-toggle="modal"
                                            data-target="#delete_Teacher"
                                            title="حذف">
                                        <i class="fa fa-trash"></i></button>
                                @endcan
                            </td>
                        </tr>
                        @include('pages.teachers.delete')
                    @empty
                        <tr style="text-align: center">
                            <td colspan="7">No data available in table</td>
                        </tr>
                    @endforelse
                    </tbody>
                    <tfoot>
                    <tr class="text-dark table-success">
                        <th>#</th>
                        <th>اسم المحفظ</th>
                        <th>رقم الهوية</th>
                        <th>البريد الإلكتروني</th>
                        <th>رقم الجوال</th>
                        <th>اسم المرحلة</th>
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
        @endcan
    </div>
@endif
