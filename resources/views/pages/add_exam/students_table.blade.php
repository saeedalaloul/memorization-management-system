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
            <th>الإسم رباعي</th>
            <th>اسم المرحلة</th>
            <th>اسم المحفظ</th>
            <th>اسم أخر اختبار</th>
            <th>علامة أخر اختبار</th>
            <th>العمليات</th>
        </tr>
        </thead>
        <tbody wire:loading.class="text-muted">
        @forelse($students as $student)
            <tr class="text-dark">
                <td>{{ $loop->iteration }}</td>
                <td>
                    @if ($student->profile_photo && Storage::disk('users_images')->exists($student->profile_photo))
                        <img src="{{Storage::disk('users_images')->url($student->profile_photo)}}"
                             style="width: 50px; height: 50px;"
                             class="img-fluid mr-15 avatar-small" alt="">
                    @else
                        <img src="{{asset('assets/images/teacher.png')}}" style="width: 50px; height: 50px;"
                             class="img-fluid mr-15 avatar-small" alt="">
                    @endif
                    {{$student->student_name}}
                </td>
                <td>{{ $student->grade_name }}</td>
                <td>{{ $student->teacher_name }}</td>
                <td>{{ $student->quran_part }}</td>
                <td style="text-align: center; align-content: center">
                    @if ($student->exam_mark != null)
                        @if ($student->exam_mark >= $student->success_mark)
                            @if ($student->improvement_mark != null && $student->improvement_mark > $student->exam_mark)
                                <div class="badge-success" style="width: 40px;">
                                    {{ $student->improvement_mark.'%' }}
                                </div>
                            @else
                                <div class="badge-success" style="width: 40px;">
                                    {{ $student->exam_mark.'%' }}
                                </div>
                            @endif
                        @else
                            <div class="badge-danger" style="width: 40px;">
                                {{ $student->exam_mark.'%' }}
                            </div>
                        @endif
                    @endif
                </td>
                <td class="embed-responsive-item">
                    <div class="btn-group mb-1 embed-responsive-item">
                        <button type="button" class="btn btn-success">العمليات</button>
                        <button type="button"
                                class="btn btn-success dropdown-toggle dropdown-toggle-split"
                                data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                            <span class="sr-only">العمليات</span>
                        </button>
                        <div class="dropdown-menu embed-responsive-item" x-placement="top-end"
                             style="position: absolute; transform: translate3d(0px, 32px, 0px); top: 0px; left: 0px; will-change: transform;">
                            <button class="dropdown-item"
                                    wire:click="lunchModalAddExam({{ $student->id }},'')"><i
                                    style="color: green"
                                    class="fa fa-plus"></i>&nbsp; إضافة
                                اختبار قرآني
                            </button>
                            @if ($student->group_sunnah_id !== null)
                                <button class="dropdown-item"
                                        wire:click="lunchModalAddExam({{ $student->id }},'sunnah_exam')"><i
                                        style="color: blue"
                                        class="fa fa-plus-circle"></i>&nbsp; إضافة
                                    اختبار سنة
                                </button>
                            @endif
                        </div>
                    </div>
                </td>
            </tr>
        @empty
            <tr style="text-align: center">
                <td colspan="8">No data available in table</td>
            </tr>
        @endforelse
        @include('pages.add_exam.add_exam_modal')
        </tbody>
        <tfoot>
        <tr class="text-dark table-success">
            <th>#</th>
            <th>اسم الطالب</th>
            <th>اسم المرحلة</th>
            <th>اسم المحفظ</th>
            <th>اسم أخر اختبار</th>
            <th>علامة أخر اختبار</th>
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
                Showing {{$students->firstItem()}} to {{$students->lastItem()}}
                of {{$students->total()}} entries
            </div>
        </div>
        <div class="col-sm-12 col-md-7">
            <div class="dataTables_paginate paging_simple_numbers"
                 id="datatable_paginate">
                <ul class="pagination">
                    {{$students->links()}}
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
    </script>
@endpush
