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
            <th>اسم المرحلة القديمة</th>
            <th>اسم المحفظ القديم</th>
            <th>اسم المرحلة الجديدة</th>
            <th>اسم المحفظ الجديد</th>
            <th>اسم مشرف العملية</th>
            <th>دور مشرف العملية</th>
            <th wire:click="sortBy('created_at')" style="cursor: pointer;">تاريخ العملية
                @include('livewire._sort-icon',['field'=>'created_at'])</th>
        </tr>
        </thead>
        <tbody>
        @forelse($track_student_transfers as $track_student_transfer)
            <tr>
                <td class="text-success">{{ $loop->iteration }}</td>
                <td class="text-success">{{ $track_student_transfer->student_name }}</td>
                <td class="text-success">{{ $track_student_transfer->old_grade_name }}</td>
                <td class="text-success">{{ $track_student_transfer->old_teacher_name }}</td>
                <td class="text-success">{{ $track_student_transfer->new_grade_name }}</td>
                <td class="text-success">{{ $track_student_transfer->new_teacher_name }}</td>
                <td class="text-success">{{ $track_student_transfer->user_signature_name }}</td>
                <td class="text-success">{{ $track_student_transfer->user_role_name }}</td>
                <td class="text-success">{{ \Carbon\Carbon::parse($track_student_transfer->created_at)->translatedFormat('l j F Y h:i a') }}</td>
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
            <th>اسم المرحلة القديمة</th>
            <th>اسم المحفظ القديم</th>
            <th>اسم المرحلة الجديدة</th>
            <th>اسم المحفظ الجديد</th>
            <th>اسم مشرف العملية</th>
            <th>دور مشرف العملية</th>
            <th>تاريخ العملية</th>
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
                Showing {{$track_student_transfers->firstItem()}} to {{$track_student_transfers->lastItem()}}
                of {{$track_student_transfers->total()}} entries
            </div>
        </div>
        <div class="col-sm-12 col-md-7">
            <div class="dataTables_paginate paging_simple_numbers"
                 id="datatable_paginate">
                <ul class="pagination">
                    {{$track_student_transfers->links()}}
                </ul>
            </div>
        </div>
    </div>
</div>
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
    </script>
@endpush
