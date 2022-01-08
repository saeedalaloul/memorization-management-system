<div class="row">
    <div>
        @if(Session::has('success_message'))
            <script>
                $(function () {
                    toastr.success("{{ Session::get('success_message') }}");
                })
            </script>
        @endif
    </div>
    <div class="col-xl-12 mb-30">
        @if (auth()->user()->current_role == 'أمير المركز' ||
             auth()->user()->current_role == 'مشرف' ||
             auth()->user()->current_role == 'اداري' ||
             auth()->user()->current_role == 'محفظ')
            @can('إدارة تقرير الحفظ والمراجعة')
                <div class="card-body">
                    <div class="row">
                        @if (auth()->user()->current_role == 'أمير المركز')
                            @if (isset($grades))
                                <div>
                                    <label style="font-size: 15px; color: #1e7e34">المراحل*</label>
                                    <div>
                                        <select class="selectpicker" data-style="btn-info"
                                                wire:model="searchGradeId">
                                            <option value="" selected>جميع المراحل
                                            </option>
                                            @foreach ($grades as $grade)
                                                <option
                                                    value="{{ $grade->id }}">{{ $grade->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endif
                        @endif
                        @if (auth()->user()->current_role == 'مشرف' || auth()->user()->current_role == 'اداري'
                             || auth()->user()->current_role == 'أمير المركز')
                            @if (isset($groups))
                                <div style="padding-right: 10px;">
                                    <label style="font-size: 15px; color: #1e7e34">المحفظين*</label>
                                    <div>
                                        <select class="selectpicker" data-style="btn-info"
                                                wire:model="searchGroupId">
                                            <option value="" selected>جميع المحفظين
                                            </option>
                                            @foreach ($groups as $group)
                                                <option
                                                    value="{{ $group->id }}">{{ $group->teacher->user->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endif
                        @endif
                        @if (isset($students))
                            <div style="padding-right: 10px;">
                                <label style="font-size: 15px; color: #1e7e34">الطالب*</label>
                                <div>
                                    <select class="selectpicker" data-style="btn-info" wire:model="searchStudentId">
                                        <option value="" selected>جميع الطلاب
                                        </option>
                                        @foreach ($students as $student)
                                            <option
                                                value="{{ $student->id }}">{{ $student->user->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endif
                        @if (isset($types))
                            <div style="padding-right: 10px;">
                                <label style="font-size: 15px; color: #1e7e34">النوع*</label>
                                <div>
                                    <select class="selectpicker" data-style="btn-info" wire:model="searchReportTypeId">
                                        <option value="" selected>جميع الأنواع
                                        </option>
                                        @foreach ($types as $type)
                                            <option
                                                value="{{ $type->id }}">{{ $type->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endif
                        <div style="padding-right: 10px;">
                            <label style="font-size: 15px; color: #1e7e34">من تاريخ*</label>
                            <div class='input-group date'>
                                <input class="form-control" type="date" wire:model="searchDateFrom"
                                       data-date-format="yyyy-mm-dd">
                            </div>
                        </div>
                        <div style="padding-right: 10px;">
                            <label style="font-size: 15px; color: #1e7e34">إلى تاريخ*</label>
                            <div class='input-group date'>
                                <input class="form-control" type="date" wire:model="searchDateTo"
                                       data-date-format="yyyy-mm-dd">
                            </div>
                        </div>
                    </div>
                    @include('livewire.search')
                    <div class="table-responsive mt-15">
                        <table id="example" class="table center-aligned-table mb-0">
                            <thead>
                            <tr class="text-dark table-success">
                                <th wire:click="sortBy('id')" style="cursor: pointer;">#
                                    @include('livewire._sort-icon',['field'=>'id'])
                                </th>
                                <th wire:click="sortBy('daily_preservation_date')" style="cursor: pointer;"
                                    class="alert-success">التاريخ
                                    @include('livewire._sort-icon',['field'=>'daily_preservation_date'])
                                </th>
                                <th class="alert-success">الطالب</th>
                                <th class="alert-success">النوع</th>
                                <th class="alert-success">من سورة</th>
                                <th class="alert-success">آية</th>
                                <th class="alert-success">إلى سورة</th>
                                <th class="alert-success">آية</th>
                                <th class="alert-success">التقييم</th>
                                <th class="alert-success">عدد الصفحات</th>
                            </tr>
                            </thead>
                            <?php
                            $numberPagesSaved = 0;
                            $numberPagesReview = 0;
                            ?>
                            <tbody>
                            @forelse($reports_daily_preservation as $report)
                                <?php
                                if (isset($report)) {
                                    if ($report->type == 1) {
                                        $numberPagesSaved += $report->calcnumberpages();
                                    } else {
                                        $numberPagesReview += $report->calcnumberpages();
                                    }
                                }
                                ?>
                                <tr style="font-size: 15px; color: #1e7e34">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{\Carbon\Carbon::parse($report->daily_preservation_date)
                                      ->translatedFormat('l') . '  ' . $report->daily_preservation_date }}</td>
                                    <td>{{ $report->student->user->name }}</td>
                                    <td>{{ $report->dailyPreservationType->name }}</td>
                                    <td>{{ $report->quranSuraFrom->name }}</td>
                                    <td>{{ $report->fromaya() }}</td>
                                    <td>{{ $report->quranSuraTo->name }}</td>
                                    <td>{{ $report->toaya() }}</td>
                                    <td>{{ $report->dailyPreservationEvaluation->name }}</td>
{{--                                    <td>{{ $report->calcnumberpages()}}</td>--}}
                                </tr>
                            @empty
                                <tr style="text-align: center">
                                    <td colspan="8">No data available in table</td>
                                </tr>
                            @endforelse
                            </tbody>
                            <tfoot>
                            <tr class="text-dark table-success">
                                <th class="alert-success"></th>
                                <th class="alert-success">عدد صفحات الحفظ</th>
                                <th class="alert-success">{{$numberPagesSaved}}</th>
                                <th class="alert-success"></th>
                                <th class="alert-success"></th>
                                <th class="alert-success"></th>
                                <th class="alert-success"></th>
                                <th class="alert-success"></th>
                                <th class="alert-success">عدد صفحات المراجعة</th>
                                <th class="alert-success">{{$numberPagesReview}}</th>
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
                                    Showing {{$reports_daily_preservation->firstItem()}}
                                    to {{$reports_daily_preservation->lastItem()}}
                                    of {{$reports_daily_preservation->total()}} entries
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-7">
                                <div class="dataTables_paginate paging_simple_numbers"
                                     id="datatable_paginate">
                                    <ul class="pagination">
                                        {{$reports_daily_preservation->links()}}
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endcan

    </div>
    @endif
    <x-loading-indicator/>
</div>
