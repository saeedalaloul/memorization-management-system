@can('إدارة اختبارات التجميعي')
    <div class="row">
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
    </div>
    <br>
    @include('livewire.search')
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
                <th>ملاحظات</th>
                <th>العمليات</th>
            </tr>
            </thead>
            <tbody>
            @forelse($exams as $exam)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $exam->student->user->name }}</td>
                    <td>{{ $exam->QuranPart->QuranSummativePartName() }}</td>
                    <td style="text-align: center; align-content: center">
                        @if ($exam->calcmarkexam() >= $exam->examSuccessMark->mark)
                            <div class="badge-success" style="width: 40px;">
                                {{ $exam->calcmarkexam().'%' }}
                            </div>
                        @else
                            <div class="badge-danger" style="width: 40px;">
                                {{ $exam->calcmarkexam().'%' }}
                            </div>
                        @endif
                    </td>
                    <td>{{ $exam->teacher->user->name }}</td>
                    <td>{{ $exam->tester->user->name }}</td>
                    <td>{{ $exam->exam_date }}</td>
                    <td>{{ $exam->notes }}</td>
                    <td></td>
                </tr>
            @empty
                <tr style="text-align: center">
                    <td colspan="8">No data available in table</td>
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
