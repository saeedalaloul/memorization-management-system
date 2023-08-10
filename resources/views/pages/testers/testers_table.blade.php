@can('إدارة المختبرين')
    <x-search></x-search>
    <div class="table-responsive mt-15">
        <table class="table center-aligned-table mb-0">
            <thead>
            <tr class="text-dark table-success">
                <th wire:click="sortBy('id')" style="cursor: pointer;">#
                    @include('livewire._sort-icon',['field'=>'id'])
                </th>
                <th>اسم المختبر</th>
                <th>عدد طلبات الإختبارات</th>
                <th>عدد الإختبارات الكلي</th>
                <th>عدد اختبارات هذا الشهر</th>
                <th>عدد اختبارات هذا العام</th>
                <th>العمليات</th>
            </tr>
            </thead>
            <tbody>
            @forelse($testers_ as $tester)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $tester->user->name }}</td>
                    <td>{{$tester->exams_orders_count}}</td>
                    <td>{{ $tester->exams_count }}</td>
                    <td>{{ $tester->exams_month_count }}</td>
                    <td>{{ $tester->exams_year_count }}</td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm"
                                wire:click="getModalData({{$tester->id}});"
                                data-toggle="modal"
                                data-target="#testerDeleted"
                                title="حذف">
                            <i class="fa fa-trash"></i></button>
                    </td>
                </tr>
                @include('pages.testers.delete')
            @empty
                <tr style="text-align: center">
                    <td colspan="4">No data available in table</td>
                </tr>
            @endforelse
            </tbody>
            <tfoot>
            <tr class="text-dark table-success">
                <th>#</th>
                <th>اسم المختبر</th>
                <th>عدد طلبات الإختبارات</th>
                <th>عدد الإختبارات الكلي</th>
                <th>عدد اختبارات هذا الشهر</th>
                <th>عدد اختبارات هذا العام</th>
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
                    Showing {{$testers_->firstItem()}} to {{$testers_->lastItem()}}
                    of {{$testers_->total()}} entries
                </div>
            </div>
            <div class="col-sm-12 col-md-7">
                <div class="dataTables_paginate paging_simple_numbers"
                     id="datatable_paginate">
                    <ul class="pagination">
                        {{$testers_->links()}}
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endcan
