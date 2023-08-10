<div class="row">
    <div class="col-xl-12 mb-30">
        @if ($current_role === \App\Models\User::ADMIN_ROLE || $current_role === \App\Models\User::SUPERVISOR_ROLE ||
             $current_role === \App\Models\User::TEACHER_ROLE)
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
                                            value="{{ $group->id }}">
                                            @if ($group->teacher_id == null)
                                                {{$group->name . ' (لا يوجد محفظ)'}}
                                            @else
                                                {{ $group->teacher->user->name }}
                                            @endif
                                        </option>
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
                                    @foreach(\App\Models\StudentSunnahDailyMemorization::types() as $key => $value)
                                        <option value="{{$key}}">{{$value}}</option>
                                    @endforeach
                                    <option value="no-memorize">لم يحفظ</option>
                                    <option value="exam">اختبار</option>
                                    <option
                                        value="{{\App\Models\StudentSunnahAttendance::AUTHORIZED_STATUS}}">{{\App\Models\StudentSunnahAttendance::status()[\App\Models\StudentSunnahAttendance::AUTHORIZED_STATUS]}}</option>
                                    <option
                                        value="{{\App\Models\StudentSunnahAttendance::ABSENCE_STATUS}}">{{\App\Models\StudentSunnahAttendance::status()[\App\Models\StudentSunnahAttendance::ABSENCE_STATUS]}}</option>
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
                            <th wire:click="sortBy('datetime')" style="cursor: pointer;"
                                class="alert-success">التاريخ
                                @include('livewire._sort-icon',['field'=>'datetime'])
                            </th>
                            <th class="alert-success">الطالب</th>
                            @if ($current_role != \App\Models\User::TEACHER_ROLE)
                                <th class="alert-success">اسم المحفظ</th>
                            @endif
                            <th class="alert-success">النوع</th>
                            <th class="alert-success">الكتاب</th>
                            <th class="alert-success">من حديث</th>
                            <th class="alert-success">إلى حديث</th>
                            <th class="alert-success">التقييم</th>
                            <th class="alert-success">عدد الأحاديث</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $numberHadithsSaved = 0; $numberHadithsReview = 0;?>
                        @forelse($reports_daily_memorization as $report)
                            @if ($report->daily_memorization_type != null)
                                @if ($report->daily_memorization_type == \App\Models\StudentSunnahDailyMemorization::MEMORIZE_TYPE)
                                    <?php $numberHadithsSaved += ($report->hadith_to - $report->hadith_from) + 1 ;?>
                                @elseif($report->daily_memorization_type == \App\Models\StudentSunnahDailyMemorization::REVIEW_TYPE)
                                    <?php $numberHadithsReview += ($report->hadith_to - $report->hadith_from) + 1;?>
                                @endif
                            @endif
                            <tr style="font-size: 15px; color: #1e7e34">
                                <td>{{ $loop->iteration }}</td>
                                <td>{{\Carbon\Carbon::parse($report->datetime)
                                      ->translatedFormat('l') . '  ' . \Carbon\Carbon::parse($report->datetime)->format('Y-m-d') }}</td>
                                <td>{{ $report->student_name }}</td>
                                @if ($current_role != \App\Models\User::TEACHER_ROLE)
                                    <td>{{ $report->teacher_name }}</td>
                                @endif
                                <td>
                                    @if ($report->daily_memorization_type != null)
                                        {{ \App\Models\StudentSunnahDailyMemorization::types()[$report->daily_memorization_type] }}
                                    @else
                                        @if($report->attendance_status == \App\Models\StudentSunnahAttendance::PRESENCE_STATUS
                                             || $report->attendance_status == \App\Models\StudentSunnahAttendance::LATE_STATUS)
                                            @if ($report->sunnah_part_name != null)
                                                اختبار
                                            @else
                                                لم يحفظ
                                            @endif
                                        @else
                                            {{\App\Models\StudentSunnahAttendance::status()[$report->attendance_status]}}
                                        @endif
                                    @endif
                                </td>
                                <td style="text-align: center; align-content: center">
                                    @if (isset($report->book_name))
                                        {{ $report->book_name }}
                                    @elseif ($report->sunnah_part_name != null)
                                        @if ($report->mark >= $report->success_mark)
                                            <div
                                                class="badge-success">{{' '.$report->sunnah_part_name.' '}}  {{$report->mark.'%' }}</div>
                                        @else
                                            <div
                                                class="badge-danger">{{' '.$report->sunnah_part_name.' '}}  {{$report->mark.'%' }}</div>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    @if(isset($report->hadith_from))
                                        {{ $report->total_number_hadith == $report->hadith_from ? 'كامل' : $report->hadith_from }}
                                    @endif
                                </td>
                                <td>
                                    @if (isset($report->hadith_to))
                                        {{ $report->total_number_hadith == $report->hadith_to ? 'كامل' : $report->hadith_to }}
                                    @endif
                                </td>
                                <td>
                                    @if(isset($report->evaluation))
                                        {{ \App\Models\StudentSunnahDailyMemorization::evaluations()[$report->evaluation] }}
                                    @endif
                                </td>
                                <td>
                                    @if (isset($report->hadith_from) && isset($report->hadith_to))
                                        {{ ($report->hadith_to - $report->hadith_from) + 1}}
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr style="text-align: center">
                                <td colspan="9">No data available in table</td>
                            </tr>
                        @endforelse
                        </tbody>
                        <tfoot>
                        <tr class="text-dark table-success">
                            @if ($current_role != \App\Models\User::TEACHER_ROLE)
                                <th class="alert-success"></th>
                            @endif
                            <th class="alert-success"></th>
                            <th class="alert-success">عدد أحاديث الحفظ</th>
                            <th class="alert-success">{{$numberHadithsSaved ?? 0}}</th>
                            <th class="alert-success"></th>
                            <th class="alert-success"></th>
                            <th class="alert-success">عدد أحاديث المراجعة</th>
                            <th class="alert-success">{{$numberHadithsReview ?? 0}}</th>
                            <th class="alert-success"></th>
                            <th class="alert-success"></th>
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
