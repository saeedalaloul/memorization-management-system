<div class="card-body">
    <x-search></x-search>
    <div class="table-responsive mt-15">
        <table class="table center-aligned-table mb-0">
            <thead>
            <tr class="text-dark table-success">
                <th wire:click="sortBy('id')" style="cursor: pointer;">#
                    @include('livewire._sort-icon',['field'=>'id'])
                </th>
                <th>اسم المنشط</th>
                <th>عدد أنشطة المنشط</th>
                <th>العمليات</th>
            </tr>
            </thead>
            <tbody>
            @forelse($activity_members as $activity_member)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $activity_member->user->name }}</td>
                    <td>
                        @if ($activity_member->activities_orders_acceptable_count != null && $activity_member->activities_orders_acceptable_count > 0)
                            {{ $activity_member->activities_orders_acceptable_count }}
                        @else
                            لا يوجد أنشطة معتمدة
                        @endif
                    </td>
                    <td>
                        @if ($activity_member->visit_orders->first() == null)
                            @if ($activity_member->activities_orders_acceptable_count != null && $activity_member->activities_orders_acceptable_count > 0)
                                <button class="btn btn-outline-success btn-sm"
                                        data-toggle="modal"
                                        wire:click="loadModalData('{{$activity_member->id}}')"
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
                @include('pages.select_visit_activity_member.select_visit_activity_member')
            @empty
                <tr style="text-align: center">
                    <td colspan="4">No data available in table</td>
                </tr>
            @endforelse

            </tbody>
            <tfoot>
            <tr class="text-dark table-success">
                <th>#</th>
                <th>اسم المنشط</th>
                <th>عدد أنشطة المنشط</th>
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
                    Showing {{$activity_members->firstItem()}} to {{$activity_members->lastItem()}}
                    of {{$activity_members->total()}} entries
                </div>
            </div>
            <div class="col-sm-12 col-md-7">
                <div class="dataTables_paginate paging_simple_numbers"
                     id="datatable_paginate">
                    <ul class="pagination">
                        {{$activity_members->links()}}
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
