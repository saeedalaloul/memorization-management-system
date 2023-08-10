@can('إدارة الأنشطة')
    <div class="card-body">
        <li class="list-group-item">
            <div class="row">
                <div class="col-md-3">
                    <label style="font-size: 15px; color: #1e7e34">المراحل*</label>
                    <select style="width: 100%;" wire:model="selectedGradeId" id="grade"
                            class="custom-select mr-sm-2 select2">
                        <option value="">الكل</option>
                        @foreach ($grades as $grade)
                            <option
                                value="{{ $grade->id }}">{{ $grade->name}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label style="font-size: 15px; color: #1e7e34">المحفظين*</label>
                    <select style="width: 100%;" class="custom-select mr-sm-2 select2" id="teacher"
                            wire:model="selectedTeacherId">
                        <option value="">الكل</option>
                        @foreach ($groups as $group)
                            <option
                                value="{{ $group->id }}">
                                @if ($group->teacher_id === null)
                                    {{$group->name . ' (لا يوجد محفظ)'}}
                                @else
                                    {{ $group->teacher->user->name }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="co-md-3">
                    <div class="card-body datepicker-form">
                        <div class="input-group">
                            <input type="date" wire:model="searchDateFrom"
                                   class="form-control" placeholder="تاريخ البداية"
                                   required>
                            <span class="input-group-addon">الي تاريخ</span>
                            <input class="form-control" wire:model="searchDateTo"
                                   placeholder="تاريخ النهاية" type="date" required>
                        </div>
                    </div>
                </div>

            </div>
        </li>
    </div>
    <x-search></x-search>
    <div class="table-responsive mt-15">
        <table class="table center-aligned-table mb-0">
            <thead>
            <tr class="text-dark table-success">
                <th wire:click="sortBy('id')" style="cursor: pointer;">#
                    @include('livewire._sort-icon',['field'=>'id'])
                </th>
                <th>اسم النشاط</th>
                <th>عدد الطلاب</th>
                <th>الوقت</th>
                <th>اسم المحفظ</th>
                <th>اسم المنشط</th>
            </tr>
            </thead>
            <tbody wire:loading.class="text-muted">
            @forelse($activities as $activity)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{$activity->activity_type->name}}</td>
                    <td>{{$activity->students_count}}</td>
                    <td>{{\Carbon\Carbon::parse($activity->datetime)->translatedFormat('l j F Y h:i a')}}</td>
                    <td>{{$activity->teacher->user->name}}</td>
                    <td>
                        {{$activity->activity_member->user->name}}
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
                <th>اسم النشاط</th>
                <th>عدد الطلاب</th>
                <th>الوقت</th>
                <th>اسم المحفظ</th>
                <th>اسم المنشط</th>
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
                    Showing {{$activities->firstItem()}} to {{$activities->lastItem()}}
                    of {{$activities->total()}} entries
                </div>
            </div>
            <div class="col-sm-12 col-md-7">
                <div class="dataTables_paginate paging_simple_numbers"
                     id="datatable_paginate">
                    <ul class="pagination">
                        {{$activities->links()}}
                    </ul>
                </div>
            </div>
        </div>
        <x-loading-indicator></x-loading-indicator>
    </div>
@endcan

@push('js')
    <script>
        $("#grade").on('change', function (e) {
            let id = $(this).val()
        @this.set('selectedGradeId', id);
            livewire.emit('getTeachersByGradeId');
        });

        $("#teacher").on('change', function (e) {
            let id = $(this).val()
        @this.set('selectedTeacherId', id);
        });
    </script>
@endpush
