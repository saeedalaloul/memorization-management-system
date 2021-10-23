<div class="row">
    <div>
        @if(Session::has('message'))
            <script>
                $(function () {
                    toastr.success("{{ Session::get('message') }}");
                })
            </script>
        @endif
    </div>
                <div class="col-xl-12 mb-30">
                    <div class="card card-statistics h-100">
                        @if (auth()->user()->current_role == 'أمير المركز')
                            <div class="card-body">
                                @can('اضافة مرحلة')
                                    <button type="button" wire:click.prevent="modalFormReset()" class="button x-small"
                                            data-toggle="modal"
                                            data-target="#gradeAdded">
                                        اضافة مرحلة
                                    </button>
                                    @include('pages.grades.add')
                                @endcan
                                <br><br>
                                @include('livewire.search')
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
                                            <th>عدد حلقات المرحلة</th>
                                            <th>العمليات</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @forelse($grades as $grade)
                                            <tr>
                                                <td>{{ $grade->id }}</td>
                                                <td>{{ $grade->name }}</td>
                                                <td>{{ $grade->groups->count() }}</td>
                                                <td>
                                                    @can('تعديل مرحلة')
                                                        <button type="button" class="btn btn-info btn-sm"
                                                                data-toggle="modal"
                                                                data-target="#gradeEdited"
                                                                wire:click.prevent="loadModalData({{$grade->id}})"
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
                                                <td colspan="6">No data available in table</td>
                                            </tr>
                                        @endforelse

                                        </tbody>
                                        <tfoot>
                                        <tr class="text-dark table-success">
                                            <th>#</th>
                                            <th>اسم المرحلة</th>
                                            <th>عدد حلقات المرحلة</th>
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
            </div>
