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
                            data-target="#complaintBoxCategoryAdded">
                        اضافة تصنيف
                    </button>
                    @include('pages.complaint_box_categories.add')
                    <br><br>
                    @include('livewire.search')
                    <div class="table-responsive mt-15">
                        <table class="table center-aligned-table mb-0">
                            <thead>
                            <tr class="text-dark table-success">
                                <th wire:click="sortBy('id')" style="cursor: pointer;">#
                                    @include('livewire._sort-icon',['field'=>'id'])
                                </th>
                                <th wire:click="sortBy('name')" style="cursor: pointer;">اسم التصنيف
                                    @include('livewire._sort-icon',['field'=>'name'])
                                </th>
                                <th>العمليات</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($complaint_box_categories as $complaint_box_category)
                                <tr>
                                    <td>{{ $complaint_box_category->id }}</td>
                                    <td>{{ $complaint_box_category->name }}</td>
                                    <td>
                                            <button type="button" class="btn btn-info btn-sm"
                                                    data-toggle="modal"
                                                    data-target="#complaintBoxCategoryEdited"
                                                    wire:click.prevent="loadModalData({{$complaint_box_category->id}})"
                                                    title="تعديل"><i class="fa fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm"
                                                    data-toggle="modal"
                                                    data-target="#complaintBoxCategoryDeleted"
                                                    title="حذف"><i
                                                    class="fa fa-trash"></i></button>
                                    </td>
                                </tr>

                                @include('pages.complaint_box_categories.edit')
                                @include('pages.complaint_box_categories.delete')

                            @empty
                                <tr style="text-align: center">
                                    <td colspan="5">No data available in table</td>
                                </tr>
                            @endforelse

                            </tbody>
                            <tfoot>
                            <tr class="text-dark table-success">
                                <th>#</th>
                                <th>اسم التصنيف</th>
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
                                    Showing {{$complaint_box_categories->firstItem()}} to {{$complaint_box_categories->lastItem()}}
                                    of {{$complaint_box_categories->total()}} entries
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-7">
                                <div class="dataTables_paginate paging_simple_numbers"
                                     id="datatable_paginate">
                                    <ul class="pagination">
                                        {{$complaint_box_categories->links()}}
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
