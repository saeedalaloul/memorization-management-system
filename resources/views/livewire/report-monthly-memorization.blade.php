<div class="row">
    <div class="col-xl-12 mb-30">
        @if ($current_role === \App\Models\User::ADMIN_ROLE || $current_role === \App\Models\User::SUPERVISOR_ROLE
             || $current_role === \App\Models\User::TEACHER_ROLE || $current_role === \App\Models\User::SPONSORSHIP_SUPERVISORS_ROLE)
            @can('إدارة التقارير الشهرية')
                <div class="card-body">
                    <li class="list-group-item">
                        <div class="row">
                            <div class="col-md-3">
                                <label style="font-size: 15px; color: #1e7e34">المراحل*</label>
                                <select style="width: 100%;" wire:model.defer="selectedGradeId" id="grade"
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
                                        wire:model.defer="selectedTeacherId">
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
                                <label style="font-size: 15px; color: #1e7e34">السنة*</label>
                                <select style="width: 100%;" class="custom-select mr-sm-2 select2" id="year"
                                        wire:model.defer="selectedYear">
                                    <option value="">___</option>
                                    @foreach ($years as $year)
                                        <option
                                            value="{{ $year }}">{{ $year}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label style="font-size: 15px; color: #1e7e34">الشهر*</label>
                                <select style="width: 100%;" class="custom-select mr-sm-2 select2" id="month"
                                        wire:model="selectedMonth">
                                    <option value="">___</option>
                                    @foreach ($months as $month)
                                        <option
                                            value="{{ $month }}">{{ $month}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="row">
                            <div class="col-md-2">
                                <button class="btn btn-success float-right" wire:click.prevent="export();">تصدير اكسل
                                </button>
                            </div>
                        </div>
                    </li>
                </div>
                @if($group_type == \App\Models\Group::QURAN_TYPE)
                    <div class="table-responsive">
                        <table class="mb-0 table table-bordered table-3 text-center table-striped">
                            <thead class="thead-dark">
                            <tr class="text-dark text-center">
                                <th rowspan="2" wire:click="sortBy('id')" style="cursor: pointer;">#
                                    @include('livewire._sort-icon',['field'=>'id'])
                                </th>
                                <th rowspan="2">اسم الطالب</th>
                                <th colspan="2">بداية الحفظ</th>
                                <th colspan="2">نهاية الحفظ</th>
                                <th rowspan="2">عدد صفحات الحفظ</th>
                                <th rowspan="2">عدد صفحات المراجعة</th>
                                <th rowspan="2">عدد صفحات مراجعة التجميعي</th>
                                <th rowspan="2">عدد أيام الحضور</th>
                                <th rowspan="2">عدد أيام الغياب</th>
                                <th rowspan="2">عدد أجزاء الحفظ</th>
                                <th rowspan="2">اختبارات التجميعي</th>
                                <th rowspan="2">اختبارات المنفردة</th>
                            </tr>
                            <tr class="text-dark text-center">
                                <th>سورة</th>
                                <th>آية</th>
                                <th>سورة</th>
                                <th>أية</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($reports_monthly_memorization as $report)
                                <tr>
                                    <td>{{$loop->iteration}}</td>
                                    <td>{{$report->student_name}}</td>
                                    <td>
                                        {{$report->sura_start}}
                                    </td>
                                    <td>
                                        {{$report->aya_from}}
                                    </td>
                                    <td>
                                        {{$report->sura_end}}
                                    </td>
                                    <td>
                                        {{$report->aya_to}}
                                    </td>
                                    <td>
                                        @if (isset($report->number_memorize_pages))
                                            {{$report->number_memorize_pages}}
                                        @endif
                                    </td>
                                    <td>
                                        @if (isset($report->number_review_pages))
                                            {{$report->number_review_pages}}
                                        @endif
                                    </td>
                                    <td>
                                        @if (isset($report->number_cumulative_review_pages))
                                            {{$report->number_cumulative_review_pages}}
                                        @endif
                                    </td>
                                    <td>
                                        @if (isset($report->presence_count))
                                            {{$report->presence_count}}
                                        @endif
                                    </td>
                                    <td>
                                        @if (isset($report->attendance_count))
                                            {{$report->attendance_count}}
                                        @endif
                                    </td>
                                    <td>
                                        @if (isset($report->total_preservation_parts))
                                            {{$report->total_preservation_parts}}
                                        @endif
                                    </td>
                                    <td>
                                        @if (isset($report->exams_deserved))
                                            {{$report->exams_deserved}}
                                        @endif
                                    </td>
                                    <td>
                                        @if (isset($report->exams_individual))
                                            {{$report->exams_individual}}
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr style="text-align: center">
                                    <td colspan="12">No data available in table</td>
                                </tr>
                            @endforelse
                            </tbody>
                            <tfoot class="thead-dark">
                            <tr class="text-dark text-center">
                                <th>#</th>
                                <th>اسم الطالب</th>
                                <th colspan="2">بداية الحفظ</th>
                                <th colspan="2">نهاية الحفظ</th>
                                <th>عدد صفحات الحفظ</th>
                                <th>عدد صفحات المراجعة</th>
                                <th>عدد صفحات مراجعة التجميعي</th>
                                <th>عدد أيام الحضور</th>
                                <th>عدد أيام الغياب</th>
                                <th>عدد أجزاء الحفظ</th>
                                <th>اختبارات التجميعي</th>
                                <th>اختبارات المنفردة</th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="mb-0 table table-bordered table-3 text-center table-striped">
                            <thead class="thead-dark">
                            <tr class="text-dark text-center">
                                <th rowspan="2" wire:click="sortBy('id')" style="cursor: pointer;">#
                                    @include('livewire._sort-icon',['field'=>'id'])
                                </th>
                                <th rowspan="2">اسم الطالب</th>
                                <th rowspan="2">اسم الكتاب</th>
                                <th colspan="2">أحاديث الحفظ</th>
                                <th colspan="2">أحاديث المراجعة</th>
                                <th rowspan="2">عدد أحاديث الحفظ</th>
                                <th rowspan="2">عدد أحاديث المراجعة</th>
                                <th rowspan="2">الاختبارات</th>
                                <th rowspan="2">عدد أيام الحضور</th>
                                <th rowspan="2">عدد أيام الغياب</th>
                            </tr>
                            <tr class="text-dark text-center">
                                <th>من</th>
                                <th>إلى</th>
                                <th>من</th>
                                <th>إلى</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($reports_monthly_memorization as $report)
                                <tr>
                                    <td>{{$loop->iteration}}</td>
                                    <td>{{$report->student_name}}</td>
                                    <td>{{$report->book_name}}</td>
                                    <td>
                                        @if($report->memorize_hadith_from)
                                            {{$report->memorize_hadith_from}}
                                        @else
                                            _
                                        @endif
                                    </td>
                                    <td>
                                        @if($report->memorize_hadith_to)
                                            {{$report->memorize_hadith_to}}
                                        @else
                                            _
                                        @endif
                                    </td>
                                    <td>
                                        @if($report->review_hadith_from)
                                            {{$report->review_hadith_from}}
                                        @else
                                            _
                                        @endif
                                    </td>
                                    <td>
                                        @if($report->review_hadith_to)
                                            {{$report->review_hadith_to}}
                                        @else
                                            _
                                        @endif
                                    </td>
                                    <td>
                                        @if ($report->memorize_hadith_from != null &&$report->memorize_hadith_to != null)
                                            {{($report->memorize_hadith_to - $report->memorize_hadith_from) + 1}}
                                        @else
                                            _
                                        @endif
                                    </td>
                                    <td>
                                        @if ($report->review_hadith_from != null &&$report->review_hadith_to != null)
                                            {{($report->review_hadith_to - $report->review_hadith_from) + 1}}
                                        @else
                                            _
                                        @endif
                                    </td>
                                    <td>
                                        @if ($report->sunnah_part_name != null)
                                            {{$report->sunnah_part_name}}
                                        @else
                                            _
                                        @endif
                                    </td>
                                    <td>
                                        @if ($report->presence_days_count != null)
                                            {{$report->presence_days_count}}
                                        @else
                                            _
                                        @endif
                                    </td>
                                    <td>
                                        @if ($report->absence_days_count != null)
                                            {{$report->absence_days_count}}
                                        @else
                                            _
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr style="text-align: center">
                                    <td colspan="12">No data available in table</td>
                                </tr>
                            @endforelse
                            </tbody>
                            <tfoot class="thead-dark">
                            <tr class="text-dark text-center">
                                <th>#</th>
                                <th>اسم الطالب</th>
                                <th>اسم الكتاب</th>
                                <th colspan="2">أحاديث الحفظ</th>
                                <th colspan="2">أحاديث المراجعة</th>
                                <th>عدد أحاديث الحفظ</th>
                                <th>عدد أحاديث المراجعة</th>
                                <th>الاختبارات</th>
                                <th>عدد أيام الحضور</th>
                                <th>عدد أيام الغياب</th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                @endif
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
        });

        $("#year").on('change', function (e) {
            let id = $(this).val()
        @this.set('selectedYear', id);
        });

        $("#month").on('change', function (e) {
            let id = $(this).val()
        @this.set('selectedMonth', id);
            livewire.emit('getReports');
        });
    </script>
@endpush
