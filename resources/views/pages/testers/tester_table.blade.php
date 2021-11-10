@can('إدارة المختبرين')
    @include('livewire.search')
    <div class="table-responsive mt-15">
        <table class="table center-aligned-table mb-0">
            <thead>
            <tr class="text-dark table-success">
                <th wire:click="sortBy('id')" style="cursor: pointer;">#
                    @include('livewire._sort-icon',['field'=>'id'])
                </th>
                <th>اسم المختبر</th>
                <th>عدد الإختبارات</th>
                <th>العمليات</th>
            </tr>
            </thead>
            <tbody>
            @forelse($testers as $tester)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $tester->user->name }}</td>
                    <td>{{ $tester->exams->count() }}</td>
                    <td>
                        <button type="button" class="btn btn-success btn-sm"
                                wire:click.prevent="show_exams_table({{$tester->id}})"
                                title="عرض اختبارات المختبر">
                            <i class="fa fa-eye"></i></button>

                        <button type="button" class="btn btn-danger btn-sm"
                                data-toggle="modal"
                                data-target="#testerDeleted"
                                title="حذف">
                            <i class="fa fa-trash"></i></button>
                    </td>
                </tr>
            @empty
                <tr style="text-align: center">
                    <td colspan="4">No data available in table</td>
                </tr>
            @endforelse
            @include('pages.testers.delete')
            </tbody>
            <tfoot>
            <tr class="text-dark table-success">
                <th>#</th>
                <th>اسم المختبر</th>
                <th>عدد الإختبارات</th>
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
                    Showing {{$testers->firstItem()}} to {{$testers->lastItem()}}
                    of {{$testers->total()}} entries
                </div>
            </div>
            <div class="col-sm-12 col-md-7">
                <div class="dataTables_paginate paging_simple_numbers"
                     id="datatable_paginate">
                    <ul class="pagination">
                        {{$testers->links()}}
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endcan
