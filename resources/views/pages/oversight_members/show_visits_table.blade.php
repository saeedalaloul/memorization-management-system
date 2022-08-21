<div class="card-body">
    <div class="row">
        <div>
            <label style="font-size: 15px; color: #1e7e34">نوع الزيارة*</label>
            <div>
                <select class="selectpicker" data-style="btn-info"
                        id="searchVisitTypeId"
                        wire:model="searchVisitTypeId">
                    <option value="" selected>جميع الأنواع</option>
                    <option value="App\Models\Group">زيارة إلى حلقة</option>
                    <option value="App\Models\Tester">زيارة إلى مختبر</option>
                    <option value="">زيارة إلى نشاط</option>
                    <option value="">زيارة إلى دورة</option>
                </select>
            </div>
        </div>
        @if(auth()->user()->current_role == \App\Models\User::OVERSIGHT_SUPERVISOR_ROLE ||
            auth()->user()->current_role == \App\Models\User::ADMIN_ROLE ||
            auth()->user()->current_role == \App\Models\User::OVERSIGHT_MEMBER_ROLE)
            <div style="padding-right: 10px;">
                <label style="font-size: 15px; color: #1e7e34">حالة الطلب*</label>
                <div>
                    <select class="selectpicker" data-style="btn-info"
                            wire:model="searchStatusId">
                        <option value="" selected>جميع الحالات</option>
                        <option value="1">مطلوب الرد</option>
                        <option value="2">تم الرد</option>
                        <option value="3">جاري المعالجة</option>
                        <option value="4">فشل المعالجة</option>
                        <option value="5">تم الحل</option>
                    </select>
                </div>
            </div>
        @endif
    </div>
    <br>
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
                <th>حالة الزيارة</th>
            </tr>
            </thead>
            <tbody>
            @forelse($visits as $visit)
                @php
                    $selectClass = '';
                        if (isset($visit) && $visit->visit_processing_reminder != null) {
                            $val = $visit->visit_processing_reminder->reminder_date < date('Y-m-d', time());
                            $val == true ? $selectClass = 'text-dark table-danger': $selectClass ='';
                        }
                @endphp
                <tr class="{{$selectClass}}">
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $visit->oversight_member->user->name }}</td>
                    <td>
                        @if($visit->hostable_type == 'App\Models\Group')
                            زيارة إلى حلقة
                        @elseif($visit->hostable_type == 'App\Models\Tester')
                            زيارة إلى مختبر
                        @endif
                    </td>
                    <td>{{ $visit->visit_date }}</td>
                    <td>
                        @if($visit->status == 1)
                            <label class="badge badge-warning">مطلوب الرد</label>
                        @elseif($visit->status == 2)
                            <label class="badge badge-info">تم الرد</label>
                        @elseif($visit->status == 3)
                            <label class="badge badge-primary">جاري المعالجة</label>
                        @elseif($visit->status == 4)
                            <label class="badge badge-danger">فشل المعالجة</label>
                        @elseif($visit->status == 5)
                            <label class="badge badge-success">تم الحل</label>
                        @endif
                    </td>
                </tr>
            @empty
                <tr style="text-align: center">
                    <td colspan="5">No data available in table</td>
                </tr>
            @endforelse
            </tbody>
            <tfoot>
            <tr class="text-dark table-success">
                <th>#</th>
                <th>اسم المراقب</th>
                <th>نوع الزيارة</th>
                <th>تاريخ الزيارة</th>
                <th>حالة الزيارة</th>
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
                    Showing {{$visits->firstItem()}} to {{$visits->lastItem()}}
                    of {{$visits->total()}} entries
                </div>
            </div>
            <div class="col-sm-12 col-md-7">
                <div class="dataTables_paginate paging_simple_numbers"
                     id="datatable_paginate">
                    <ul class="pagination">
                        {{$visits->links()}}
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <x-loading-indicator/>
</div>

@push('js')

    <script>
        $("#searchVisitTypeId").on('change', function (e) {
            let id = $(this).val()
        @this.set('searchVisitTypeId', id);
        })
    </script>


@endpush
