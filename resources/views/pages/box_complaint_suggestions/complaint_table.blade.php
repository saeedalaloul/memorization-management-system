@can('إدارة صندوق الشكاوي والإقتراحات')
    @include('livewire.search')
    <div class="table-responsive mt-15">
        <table class="table center-aligned-table mb-0">
            <thead>
            <tr class="text-dark table-success">
                <th wire:click="sortBy('id')" style="cursor: pointer;">#
                    @include('livewire._sort-icon',['field'=>'id'])
                </th>
                <th>اسم المرسل</th>
                <th>اسم المستقبل</th>
                <th>دور المستقبل</th>
                <th>نوع الشكوى/الاقتراح</th>
                <th>قرئت في</th>
                <th>تاريخ الشكوى/الاقتراح</th>
                <th>العمليات</th>
            </tr>
            </thead>
            <tbody>
            @forelse($box_complaint_suggestions as $box_complaint_suggestion)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td></td>
                </tr>
            @empty
                <tr style="text-align: center">
                    <td colspan="8">No data available in table</td>
                </tr>
            @endforelse
            </tbody>
            <tfoot>
            <tr class="text-dark table-success">
                <th>#</th>
                <th>اسم المرسل</th>
                <th>اسم المستقبل</th>
                <th>دور المستقبل</th>
                <th>نوع الشكوى/الاقتراح</th>
                <th>قرئت في</th>
                <th>تاريخ الشكوى/الاقتراح</th>
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
                    Showing {{$box_complaint_suggestions->firstItem()}} to {{$box_complaint_suggestions->lastItem()}}
                    of {{$box_complaint_suggestions->total()}} entries
                </div>
            </div>
            <div class="col-sm-12 col-md-7">
                <div class="dataTables_paginate paging_simple_numbers"
                     id="datatable_paginate">
                    <ul class="pagination">
                        {{$box_complaint_suggestions->links()}}
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endcan
