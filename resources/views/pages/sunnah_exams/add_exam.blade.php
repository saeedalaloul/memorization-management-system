<div class="row">
    <div class="col-xl-12 mb-30">
        <div class="card card-statistics h-100">
            <div class="card-body">
                <div class="col-xs-12">
                    <div class="form-row">
                        <div class="form-group col">
                            <label for="inputGrade">اختر المرحلة</label>
                            <select style="width: 100%;" class="custom-select my-1 mr-sm-2 select2" id="grade_" wire:model="selectedGradeId">
                                <option selected>اختر المرحلة</option>
                                @foreach($grades as $grade)
                                    <option value="{{$grade->id}}">{{$grade->name}}</option>
                                @endforeach
                            </select>
                            @error('selectedGradeId')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group col">
                            <label for="inputGroup">اختر المحفظ</label>
                            <select style="width: 100%;" class="custom-select my-1 mr-sm-2 select2" id="teacher_" wire:model="selectedTeacherId">
                                <option selected>اختر المحفظ</option>
                                @foreach($groups as $group)
                                    <option value="{{$group->id}}">{{$group->teacher->user->name}}</option>
                                @endforeach
                            </select>
                            @error('selectedTeacherId')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group col">
                            <label for="inputStudent">اختر الطالب</label>
                            <select style="width: 100%;" class="custom-select my-1 mr-sm-2 select2" id="student_" wire:model="student_id">
                                <option selected>اختر الطالب</option>
                                @foreach($students as $student)
                                    <option value="{{$student->id}}">{{$student->user->name}}</option>
                                @endforeach
                            </select>
                            @error('student_id')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col">
                            <label for="inputQuranPart">اختر الجزء</label>
                            <select style="width: 100%;" class="custom-select my-1 mr-sm-2 select2" id="quran_part" wire:model="quran_part_id">
                                <option selected>اختر الجزء</option>
                                @if (isset($quran_parts))
                                    @foreach($quran_parts as $quran_part)
                                        <option
                                            value="{{$quran_part->id}}">{{$quran_part->name . ' ' . $quran_part->description}}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('quran_part_id')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group col">
                            <label for="inputTesterId">اختر المختبر</label>
                            <select style="width: 100%;" class="custom-select my-1 mr-sm-2 select2" id="tester" wire:model="tester_id">
                                <option selected>اختر المختبر...</option>
                                @if (isset($testers))
                                    @foreach($testers as $tester)
                                        <option value="{{$tester->id}}">{{$tester->user->name}}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('tester_id')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group col">
                            <label for="title">تاريخ الإختبار</label>
                            <div class='input-group date'>
                                <input class="form-control" wire:model.defer="exam_date" type="date"
                                       data-date-format="yyyy-mm-dd">
                            </div>
                            @error('exam_date')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col">
                            <label for="inputQuranPart">اختر نسبة النجاح في الاختبار</label>
                            <select class="custom-select my-1 mr-sm-2" wire:model.defer="exam_success_mark_id">
                                <option selected value="">اختر نسبة النجاح في الاختبار</option>
                                @foreach($exam_success_marks as $exam_success_mark)
                                    <option
                                        value="{{$exam_success_mark->id}}">{{$exam_success_mark->mark}}</option>
                                @endforeach
                            </select>
                            @error('exam_success_mark_id')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group col">
                            <label for="title">علامة الإختبار</label>
                            <div class='input-group number'>
                                <input class="form-control" max="100" min="60" wire:model.defer="exam_mark" type="number">
                            </div>
                            @error('exam_mark')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                </div>
                <div class="col-md-5">
                    <button type="button"
                            wire:click.prevent="examInformationApproval()"
                            class="btn btn-outline-success btn-sm">اعتماد معلومات الاختبار
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('js')
    <script>
        $("#grade_").on('change', function (e) {
            let id = $(this).val()
        @this.set('selectedGradeId', id);
            livewire.emit('getTeachersByGradeId');
        });

        $("#teacher_").on('change', function (e) {
            let id = $(this).val()
        @this.set('selectedTeacherId', id);
            livewire.emit('getStudentsByTeacherId', id);
        });

        $("#student_").on('change', function (e) {
            let id = $(this).val()
        @this.set('student_id', id);
        });

        $("#quran_part").on('change', function (e) {
            let id = $(this).val()
        @this.set('quran_part_id', id);
        });

        $("#tester").on('change', function (e) {
            let id = $(this).val()
        @this.set('tester_id', id);
        });
    </script>
@endpush
