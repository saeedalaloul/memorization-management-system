<div class="card-body">
    <x-search></x-search>
    <div class="table-responsive mt-15">
        <table class="table center-aligned-table mb-0">
            <thead>
            <tr class="text-dark table-success">
                <th wire:click="sortBy('id')" style="cursor: pointer;">#
                    @include('livewire._sort-icon',['field'=>'id'])
                </th>
                <th>اسم المختبر</th>
                <th>عدد اختبارات المختبر</th>
                <th>العمليات</th>
            </tr>
            </thead>
            <tbody>
            @forelse($testers_ as $tester)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $tester->user->name }}</td>
                    <td>
                        @if ($tester->tester_exams_count != null && $tester->tester_exams_count > 0)
                            {{ $tester->tester_exams_count }}
                        @else
                            لا يوجد اختبارات معتمدة
                        @endif
                    </td>
                    <td>
                        @if ($tester->visit_orders->first() == null)
                            @if ($tester->tester_exams_count != null && $tester->tester_exams_count > 0)
                                <button class="btn btn-outline-success btn-sm"
                                        data-toggle="modal"
                                        wire:click="loadModalData('{{$tester->id}}')"
                                        data-target="#select-visit">
                                    إقرار
                                    زيارة
                                </button>
                            @endif

                        @else
                            <div class="badge badge-info">
                                يوجد طلبات زيارات سابقة
                            </div>
                        @endif
                    </td>
                </tr>
                @include('pages.select_visit_tester.select_visit_tester')
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
                <th>عدد اختبارات المختبر</th>
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
</div>
