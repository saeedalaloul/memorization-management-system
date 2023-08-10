@can('إدارة الحفظة')
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
                <th>اسم الطالب</th>
                <th>رقم الهوية</th>
                <th>تاريخ الميلاد</th>
                <th>اسم المحفظ</th>
                <th>تاريخ إتمام الحفظ</th>
                <th>درجة الإختبار</th>
                <th>تاريخ الإختبار (الخارجي)</th>
                <th>درجة الإختبار (الخارجي)</th>
            </tr>
            </thead>
            <tbody>
            @forelse($quran_memorizers as $quran_memorizer)
                <tr>
                    <td class="text-success">{{ $loop->iteration }}</td>
                    <td class="text-success">{{ $quran_memorizer->student->user->name }}</td>
                    <td class="text-success">{{ $quran_memorizer->student->user->identification_number }}</td>
                    <td class="text-success">{{ $quran_memorizer->student->user->dob }}</td>
                    <td class="text-success">{{ $quran_memorizer->teacher->user->name }}</td>
                    <td class="text-success">{{ \Carbon\Carbon::parse($quran_memorizer->datetime)->format('Y-m-d') }}</td>
                    <td style="text-align: center; align-content: center">
                        @if ($quran_memorizer->mark >= $quran_memorizer->exam_success_mark->mark)
                            @if ($quran_memorizer->exam_improvement !== null && $quran_memorizer->exam_improvement->mark > $quran_memorizer->mark)
                                <div class="badge-success" style="width: 40px;">
                                    {{ $quran_memorizer->exam_improvement->mark.'%' }}
                                </div>
                            @else
                                <div class="badge-success" style="width: 40px;">
                                    {{ $quran_memorizer->mark.'%' }}
                                </div>
                            @endif
                        @else
                            <div class="badge-danger" style="width: 40px;">
                                {{ $quran_memorizer->mark.'%' }}
                            </div>
                        @endif
                    </td>
                    <td class="text-success">{{ \Carbon\Carbon::parse($quran_memorizer->external_exam->date)->format('Y-m-d') }}</td>
                    <td style="text-align: center; align-content: center">
                        @if ($quran_memorizer->external_exam !== null)
                            <div class="badge-success" style="width: 40px;">
                                {{ $quran_memorizer->external_exam->mark.'%' }}
                            </div>
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
                <th>#</th>
                <th>اسم الطالب</th>
                <th>رقم الهوية</th>
                <th>تاريخ الميلاد</th>
                <th>اسم المحفظ</th>
                <th>تاريخ إتمام الحفظ</th>
                <th>درجة الإختبار</th>
                <th>تاريخ الإختبار (الخارجي)</th>
                <th>درجة الإختبار (الخارجية)</th>
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
                    Showing {{$quran_memorizers->firstItem()}} to {{$quran_memorizers->lastItem()}}
                    of {{$quran_memorizers->total()}} entries
                </div>
            </div>
            <div class="col-sm-12 col-md-7">
                <div class="dataTables_paginate paging_simple_numbers"
                     id="datatable_paginate">
                    <ul class="pagination">
                        {{$quran_memorizers->links()}}
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
    </script>
@endpush
