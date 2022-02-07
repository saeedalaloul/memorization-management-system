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
                    <button type="button" wire:click.prevent="modalFormReset()" class="button x-small"
                            data-toggle="modal"
                            data-target="#complaintBoxRoleAdded">
                        اضافة دور
                    </button>
                    @include('pages.complaint_box_roles.add')
                    <br><br>
                    @include('livewire.search')
                    <div class="table-responsive mt-15">
                        <table class="table center-aligned-table mb-0">
                            <thead>
                            <tr class="text-dark table-success">
                                <th wire:click="sortBy('id')" style="cursor: pointer;">#
                                    @include('livewire._sort-icon',['field'=>'id'])
                                </th>
                                <th>اسم الدور</th>
                                <th>العمليات</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($complaint_box_roles as $complaint_box_role)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $complaint_box_role->role->name }}</td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm"
                                                data-toggle="modal"
                                                data-target="#complaintBoxRoleDeleted"
                                                title="حذف"><i
                                                class="fa fa-trash"></i></button>
                                    </td>
                                </tr>
                                @include('pages.complaint_box_roles.delete')
                            @empty
                                <tr style="text-align: center">
                                    <td colspan="5">No data available in table</td>
                                </tr>
                            @endforelse
                            </tbody>
                            <tfoot>
                            <tr class="text-dark table-success">
                                <th>#</th>
                                <th>اسم الدور</th>
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
                                    Showing {{$complaint_box_roles->firstItem()}}
                                    to {{$complaint_box_roles->lastItem()}}
                                    of {{$complaint_box_roles->total()}} entries
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-7">
                                <div class="dataTables_paginate paging_simple_numbers"
                                     id="datatable_paginate">
                                    <ul class="pagination">
                                        {{$complaint_box_roles->links()}}
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
    <x-loading-indicator/>
</div>
