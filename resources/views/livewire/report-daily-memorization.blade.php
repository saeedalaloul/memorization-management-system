<div class="row">
    <div class="col-xl-12 mb-30">
        @if ($current_role == \App\Models\User::ADMIN_ROLE || $current_role == \App\Models\User::SUPERVISOR_ROLE ||
             $current_role == \App\Models\User::TEACHER_ROLE)
            @can('إدارة تقرير الحفظ والمراجعة')
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
                                            value="{{ $group->id }}">{{ $group->teacher->user->name}}</option>
                                    @endforeach
                                </select>
                            </div>


                            <div class="col-md-3">
                                <label for="student" style="font-size: 15px; color: #1e7e34">الطلاب*</label>
                                <select style="width: 100%;" class="custom-select mr-sm-2 select2" id="student"
                                        wire:model="selectedStudentId">
                                    <option value="">الكل</option>
                                    @foreach($students as $student)
                                        <option value="{{ $student->id }}">{{ $student->user->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label for="student" style="font-size: 15px; color: #1e7e34">الأنواع*</label>
                                <select style="width: 100%;" wire:model="searchReportType"
                                        class="custom-select mr-sm-2"
                                        name="searchReportType">
                                    <option value="" selected>الكل</option>
                                    @foreach(\App\Models\StudentDailyMemorization::types() as $key => $value)
                                        <option value="{{$key}}">{{$value}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="row">
                            <div class="col-md-5">
                                <div class="datepicker-form">
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
                            <div class="col-md-2">
                                <button type="button" class="btn btn-success float-right"
                                        wire:click.prevent="export();">تصدير اكسل
                                </button>
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
                        $numberPagesSaved = $reports_daily_memorization->sum(function ($report) {
                            if ($report->type == \App\Models\StudentDailyMemorization::MEMORIZE_TYPE) {
                                return $report->number_pages;
                            }
                        });
                        $numberPagesReview = $reports_daily_memorization->sum(function ($report) {
                            if ($report->type == \App\Models\StudentDailyMemorization::REVIEW_TYPE) {
                                return $report->number_pages;
                            }
                        });

                        $numberPagesCumulativeReview = $reports_daily_memorization->sum(function ($report) {
                            if ($report->type == \App\Models\StudentDailyMemorization::CUMULATIVE_REVIEW_TYPE) {
                                return $report->number_pages;
                            }
                            return 0;
                        });
                        ?>
                        <tbody>
                        @forelse($reports_daily_memorization as $report)
                            <tr style="font-size: 15px; color: #1e7e34">
                                <td>{{ $loop->iteration }}</td>
                                <td>{{\Carbon\Carbon::parse($report->datetime)
                                      ->translatedFormat('l') . '  ' . \Carbon\Carbon::parse($report->datetime)->format('Y-m-d') }}</td>
                                <td>{{ $report->student->user->name }}</td>
                                <td>{{ $report->TypeName() }}</td>
                                <td>{{ $report->quranSuraFrom->name }}</td>
                                <td>{{ $report->AyaFrom() }}</td>
                                <td>{{ $report->quranSuraTo->name }}</td>
                                <td>{{ $report->AyaTo() }}</td>
                                <td>{{ $report->evaluation() }}</td>
                                <td>{{ $report->number_pages}}</td>
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
                            <th class="alert-success">عدد صفحات المراجعة</th>
                            <th class="alert-success">{{$numberPagesReview}}</th>
                            <th class="alert-success"></th>
                            <th class="alert-success"></th>
                            <th class="alert-success">عدد صفحات مراجعة التجميعي</th>
                            <th class="alert-success">{{$numberPagesCumulativeReview}}</th>
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
                                Showing {{$reports_daily_memorization->firstItem()}}
                                to {{$reports_daily_memorization->lastItem()}}
                                of {{$reports_daily_memorization->total()}} entries
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-7">
                            <div class="dataTables_paginate paging_simple_numbers"
                                 id="datatable_paginate">
                                <ul class="pagination">
                                    {{$reports_daily_memorization->links()}}
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
    </div>
    @endcan
    <x-loading-indicator></x-loading-indicator>
</div>
@endif
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
            livewire.emit('getStudentsByTeacherId', id);
        });

        $("#student").on('change', function (e) {
            let id = $(this).val()
        @this.set('selectedStudentId', id);
            livewire.emit('getReportsByStudentId', id);
        });
    </script>
@endpush
