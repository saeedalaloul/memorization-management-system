@can('إدارة الإختبارات')
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
                    <label for="external-exams" style="font-size: 15px; color: #1e7e34">درجة الإختبار
                        (الخارجية)*</label>
                    <div>
                        <select style="width: 100%;" class="custom-select mr-sm-2 select2" id="external-exams"
                                wire:model="selectedExternalExams">
                            <option value="">الكل</option>
                            <option value="1">اختبر</option>
                            <option value="2">لم يختبر</option>
                        </select>
                    </div>
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

                @if ($current_role == \App\Models\User::EXAMS_SUPERVISOR_ROLE)
                    <div class="col-md-2">
                        <button type="button" class="btn btn-success float-right" data-toggle="modal"
                                data-target="#import-file">استيراد
                        </button>
                    </div>
                @endif

            </div>
        </li>
    </div>
    @include('pages.exams.import_file')

    <x-search></x-search>
    <div class="table-responsive mt-15">
        <table class="table center-aligned-table mb-0">
            <thead>
            <tr class="text-dark table-success">
                <th wire:click="sortBy('id')" style="cursor: pointer;">#
                    @include('livewire._sort-icon',['field'=>'id'])
                </th>
                <th>اسم الطالب</th>
                <th>جزء الإختبار</th>
                <th>درجة الإختبار</th>
                <th>اسم المحفظ</th>
                <th>اسم المختبر</th>
                <th>تاريخ الإختبار</th>
                <th>درجة الإختبار (الخارجية)</th>
                <th>ملاحظات</th>
                <th>العمليات</th>
            </tr>
            </thead>
            <tbody>
            @forelse($exams as $exam)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $exam->student->user->name }}</td>
                    <td>{{ $exam->QuranPart->name . ' ' . $exam->QuranPart->description }}</td>
                    <td style="text-align: center; align-content: center">
                        @if ($exam->mark >= $exam->examSuccessMark->mark)
                            @if ($exam->exam_improvement != null && $exam->exam_improvement->mark > $exam->mark)
                                <div class="badge-success" style="width: 40px;">
                                    {{ $exam->exam_improvement->mark.'%' }}
                                </div>
                            @else
                                <div class="badge-success" style="width: 40px;">
                                    {{ $exam->mark.'%' }}
                                </div>
                            @endif
                        @else
                            <div class="badge-danger" style="width: 40px;">
                                {{ $exam->mark.'%' }}
                            </div>
                        @endif
                    </td>
                    <td>{{ $exam->teacher->user->name }}</td>
                    <td>{{ $exam->tester->user->name }}</td>
                    <td>{{ \Carbon\Carbon::parse($exam->datetime)->format('Y-m-d') }}</td>
                    <td style="text-align: center; align-content: center">
                        @if ($exam->external_exam != null)
                            <div class="badge-success" style="width: 40px;">
                                {{ $exam->external_exam->mark.'%' }}
                            </div>
                        @endif
                    </td>
                    <td>{{ $exam->notes }}</td>
                    <td>
                        @if ($exam->quran_part_id != 17 && $exam->quran_part_id != 18)
                            @if($current_role == \App\Models\User::TEACHER_ROLE)
                                @if ($exam->mark >= $exam->examSuccessMark->mark && $exam->exam_improvement == null)
                                    <button
                                        wire:click.prevent="submitExamImprovementRequest('{{$exam->student_id}}',{{$exam->quran_part_id}});"
                                        class="btn btn-outline-success btn-sm">تحسين درجة الإختبار
                                    </button>
                                @endif
                            @elseif($current_role == \App\Models\User::EXAMS_SUPERVISOR_ROLE)
                                @if ($exam->mark >= $exam->examSuccessMark->mark && $exam->external_exam == null)
                                    <button wire:click="show_dialog_assign_external_exam('{{$exam->id}}');"
                                            class="btn btn-outline-success btn-sm">رصد درجة الإختبار (الخارجية)
                                    </button>
                                @endif
                            @endif
                        @endif
                    </td>
                </tr>
                @include('pages.exams.assign_external_exam_mark')
            @empty
                <tr style="text-align: center">
                    <td colspan="9">No data available in table</td>
                </tr>
            @endforelse
            </tbody>
            <tfoot>
            <tr class="text-dark table-success">
                <th>#</th>
                <th>اسم الطالب</th>
                <th>جزء الإختبار</th>
                <th>درجة الإختبار</th>
                <th>اسم المحفظ</th>
                <th>اسم المختبر</th>
                <th>تاريخ الإختبار</th>
                <th>درجة الإختبار (الخارجية)</th>
                <th>ملاحظات</th>
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
                    Showing {{$exams->firstItem()}} to {{$exams->lastItem()}}
                    of {{$exams->total()}} entries
                </div>
            </div>
            <div class="col-sm-12 col-md-7">
                <div class="dataTables_paginate paging_simple_numbers"
                     id="datatable_paginate">
                    <ul class="pagination">
                        {{$exams->links()}}
                    </ul>
                </div>
            </div>
        </div>
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
            livewire.emit('getStudentsByTeacherId', id);
        });

        $("#student").on('change', function (e) {
            let id = $(this).val()
        @this.set('selectedStudentId', id);
        });

        $("#external-exams").on('change', function (e) {
            let id = $(this).val()
        @this.set('selectedExternalExams', id);
        });
    </script>
@endpush
