@if (auth()->user()->current_role == 'أمير المركز')
    <div class="card-body">
        @can('قائمة مشرفي المراحل')
            @include('livewire.search')
            <div class="table-responsive mt-15">
                <table class="table center-aligned-table mb-0">
                    <thead>
                    <tr class="text-dark table-success">
                        <th wire:click="sortBy('id')" style="cursor: pointer;">#
                            @include('livewire._sort-icon',['field'=>'id'])
                        </th>
                        <th>اسم المشرف</th>
                        <th>رقم الهوية</th>
                        <th>البريد الإلكتروني</th>
                        <th>رقم الجوال</th>
                        <th>اسم المرحلة</th>
                        <th>العمليات</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($supervisors as $supervisor)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{$supervisor->user->name}}</td>
                            <td>{{$supervisor->user->identification_number}}</td>
                            <td>{{$supervisor->user->email}}</td>
                            <td>{{$supervisor->user->phone}}</td>
                            <td>{{$supervisor->grade->name}}</td>
                            <td>
                                @can('تعديل مشرف مرحلة')
                                    <button
                                        class="btn btn-info btn-sm" role="button"
                                        wire:click.prevent="loadModalData({{$supervisor->id}})"
                                        aria-pressed="true"><i
                                            class="fa fa-edit"></i></button>
                                @endcan
                                @can('حذف مشرف مرحلة')
                                    <button type="button" class="btn btn-danger btn-sm"
                                            data-toggle="modal"
                                            data-target="#delete_Supervisor"
                                            title="حذف">
                                        <i class="fa fa-trash"></i></button>
                                @endcan
                            </td>
                        </tr>
                        @include('pages.supervisors.delete')
                    @empty
                        <tr style="text-align: center">
                            <td colspan="7">No data available in table</td>
                        </tr>
                    @endforelse
                    </tbody>
                    <tfoot>
                    <tr class="text-dark table-success">
                        <th>#</th>
                        <th>اسم المشرف</th>
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
                            Showing {{$supervisors->firstItem()}}
                            to {{$supervisors->lastItem()}}
                            of {{$supervisors->total()}} entries
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-7">
                        <div class="dataTables_paginate paging_simple_numbers"
                             id="datatable_paginate">
                            <ul class="pagination">
                                {{$supervisors->links()}}
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endcan
    </div>
@endif
