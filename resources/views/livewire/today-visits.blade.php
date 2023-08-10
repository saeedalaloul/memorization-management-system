<div class="row">
    <div class="col-xl-12 mb-30">
        <div class="card card-statistics h-100">
            @if($current_role === \App\Models\User::OVERSIGHT_SUPERVISOR_ROLE || $current_role === \App\Models\User::OVERSIGHT_MEMBER_ROLE)
                <div class="card-body">
                    <br>
                    @if ($isVisitOfStart === true)
                        @include('pages.today_visits.visit_of_start')
                    @else
                        <x-search></x-search>
                        <div class="table-responsive mt-15">
                            <table class="table center-aligned-table mb-0">
                                <thead>
                                <tr class="text-dark table-success">
                                    <th wire:click="sortBy('id')" style="cursor: pointer;">#
                                        @include('livewire._sort-icon',['field'=>'id'])
                                    </th>
                                    <th>اسم المراقب</th>
                                    <th>نوع الزيارة</th>
                                    <th>تاريخ الزيارة</th>
                                    <th>حالة الطلب</th>
                                    <th>العمليات</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($visits_today as $visit_today)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $visit_today->oversight_member->user->name }}</td>
                                        <td>
                                            @if($visit_today->hostable_type == 'App\Models\Teacher')
                                                زيارة إلى حلقة
                                            @elseif($visit_today->hostable_type == 'App\Models\Tester')
                                                زيارة إلى مختبر
                                            @elseif($visit_today->hostable_type == 'App\Models\ActivityMember')
                                                زيارة إلى منشط
                                            @endif
                                        </td>
                                        <td>{{\Carbon\Carbon::parse($visit_today->datetime)->format('Y-m-d')}}</td>
                                        <td>
                                            @if($visit_today->status == \App\Models\VisitOrder::IN_PENDING_STATUS)
                                                <label class="badge badge-warning">في انتظار الزيارة</label>
                                            @elseif($visit_today->status == \App\Models\VisitOrder::IN_SENDING_STATUS)
                                                <label class="badge badge-info">في انتظار الإرسال</label>
                                            @elseif($visit_today->status == \App\Models\VisitOrder::IN_APPROVAL_STATUS)
                                                <label class="badge badge-primary">في انتظار الإعتماد</label>
                                            @endif
                                        </td>
                                        <td>
                                            <button class="btn btn-outline-success btn-sm"
                                                    wire:click.prevent="visitOfStart('{{$visit_today->id}}')">بدء إجراء
                                                الزيارة
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr style="text-align: center">
                                        <td colspan="6">No data available in table</td>
                                    </tr>
                                @endforelse
                                </tbody>
                                <tfoot>
                                <tr class="text-dark table-success">
                                    <th>#</th>
                                    <th>اسم المراقب</th>
                                    <th>نوع الزيارة</th>
                                    <th>تاريخ الزيارة</th>
                                    <th>حالة الطلب</th>
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
                                        Showing {{$visits_today->firstItem()}} to {{$visits_today->lastItem()}}
                                        of {{$visits_today->total()}} entries
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-7">
                                    <div class="dataTables_paginate paging_simple_numbers"
                                         id="datatable_paginate">
                                        <ul class="pagination">
                                            {{$visits_today->links()}}
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
    <x-loading-indicator></x-loading-indicator>
</div>
