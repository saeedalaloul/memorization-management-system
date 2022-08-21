<div class="table-responsive mt-15">
    <table class="table center-aligned-table mb-0">
        <thead>
        <tr class="text-dark table-success">
            <th wire:click="sortBy('id')" style="cursor: pointer;">#
                @include('livewire._sort-icon',['field'=>'id'])
            </th>
            <th>نوع النشاط</th>
            <th>مكان النشاط</th>
            <th>تاريخ بداية النشاط</th>
            <th>تاريخ نهاية النشاط</th>
            <th>العمليات</th>
        </tr>
        </thead>
        <tbody>
        @forelse($activities_types as $activities_type)
            @php
                $selectClass = 'text-dark';
                $isActive = true;
                    if (isset($activities_type) &&  \Carbon\Carbon::parse($activities_type->end_datetime)->format('Y-m-d') < date('Y-m-d', time())) {
                       $selectClass = 'text-dark table-danger';
                       $isActive = false;
                    }
            @endphp
            <tr class="{{$selectClass}}">
                <td>{{ $loop->iteration }}</td>
                <td>{{$activities_type->name}}</td>
                <td>{{$activities_type->place}}</td>
                <td>{{\Carbon\Carbon::parse($activities_type->start_datetime)->format('Y-m-d')}}</td>
                <td>{{\Carbon\Carbon::parse($activities_type->end_datetime)->format('Y-m-d')}}</td>
                <td>
                    @if ($current_role == \App\Models\User::TEACHER_ROLE)
                        @if ($isActive)
                            @if ($activities_type->activities_orders != null && $activities_type->activities_orders->count() != 0)
                                يوجد طلب مسبق لهذا النوع من النشاط
                            @else
                                <button class="btn btn-outline-success btn-sm"
                                        wire:click="activityRequest('{{$activities_type->id}}');">طلب النشاط
                                </button>
                            @endif
                        @endif
                    @endif
                </td>
            </tr>
            @include('pages.activities_orders.activity_request')
        @empty
            <tr style="text-align: center">
                <td colspan="5">No data available in table</td>
            </tr>
        @endforelse
        </tbody>
        <tfoot>
        <tr class="text-dark table-success">
            <th>#</th>
            <th>نوع النشاط</th>
            <th>مكان النشاط</th>
            <th>تاريخ بداية النشاط</th>
            <th>تاريخ نهاية النشاط</th>
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
                Showing {{$activities_types->firstItem()}} to {{$activities_types->lastItem()}}
                of {{$activities_types->total()}} entries
            </div>
        </div>
        <div class="col-sm-12 col-md-7">
            <div class="dataTables_paginate paging_simple_numbers"
                 id="datatable_paginate">
                <ul class="pagination">
                    {{$activities_types->links()}}
                </ul>
            </div>
        </div>
    </div>
</div>
