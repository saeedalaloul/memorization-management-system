<div class="row">
    <div class="col-xl-12 mb-30">
        <div class="card card-statistics h-100">
            <div class="card-body">
                <div class="col-xs-12">
                    <div class="form-row">
                        @if (isset($grades))
                            <div class="form-group col">
                                <label for="inputGrade">اختر المرحلة</label>
                                <select
                                    {{$isExamOfStart == true ? 'disabled':''}} class="custom-select my-1 mr-sm-2"
                                    wire:model="grade_id">
                                    <option selected>اختر المرحلة...</option>
                                    @foreach($grades as $grade)
                                        <option value="{{$grade->id}}">{{$grade->name}}</option>
                                    @endforeach
                                </select>
                                @error('grade_id')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif

                        @if (isset($groups))
                            <div class="form-group col">
                                <label for="inputGroup">اختر المحفظ</label>
                                <select
                                    {{$isExamOfStart == true ? 'disabled':''}} class="custom-select my-1 mr-sm-2"
                                    wire:model="group_id">
                                    <option selected>اختر المحفظ...</option>
                                    @foreach($groups as $group)
                                        <option value="{{$group->id}}">{{$group->teacher->user->name}}</option>
                                    @endforeach
                                </select>
                                @error('group_id')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif

                        @if (isset($students))
                            <div class="form-group col">
                                <label for="inputStudent">اختر الطالب</label>
                                <select
                                    {{$isExamOfStart == true ? 'disabled':''}} class="custom-select my-1 mr-sm-2"
                                    wire:model="student_id">
                                    <option selected>اختر الطالب...</option>
                                    @foreach($students as $student)
                                        <option value="{{$student->id}}">{{$student->user->name}}</option>
                                    @endforeach
                                </select>
                                @error('student_id')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif

                        @if (isset($quran_parts))
                            <div class="form-group col">
                                <label for="inputQuranPart">اختر الجزء</label>
                                <select
                                    {{$isExamOfStart == true ? 'disabled':''}} class="custom-select my-1 mr-sm-2"
                                    wire:model="quran_part_id">
                                    <option selected>اختر الجزء...</option>
                                    @if (isset($quran_parts) && count($quran_parts) == 2)
                                        <option value="{{$quran_parts['id']}}">{{$quran_parts['name']}}</option>
                                    @elseif (isset($quran_parts) && isset($quran_parts['id']))
                                        <option value="{{$quran_parts['id']}}">{{$quran_parts['name']}}</option>
                                    @elseif (isset($quran_parts))
                                        @foreach($quran_parts as $quran_part)
                                            <option value="{{$quran_part->id}}">{{$quran_part->name}}</option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('quran_part_id')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif
                    </div>
                    <div class="form-row">
                        @if (isset($testers))
                            <div class="form-group col">
                                <label for="inputTesterId">اختر المختبر</label>
                                <select
                                    {{$isExamOfStart == true ? 'disabled':''}} class="custom-select my-1 mr-sm-2"
                                    wire:model="tester_id">
                                    <option selected>اختر المختبر...</option>
                                    @foreach($testers as $tester)
                                        <option value="{{$tester->id}}">{{$tester->user->name}}</option>
                                    @endforeach
                                </select>
                                @error('tester_id')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif
                        <div class="col">
                            <label for="title">تاريخ الإختبار</label>
                            <div class='input-group date'>
                                <input {{$isExamOfStart == true ? 'disabled':''}} class="form-control"
                                       wire:model="exam_date" type="date"
                                       data-date-format="yyyy-mm-dd">
                            </div>
                            @error('exam_date')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        @if (isset($exam_questions_min))
                            <div class="col">
                                <label class="control-label">اختر عدد أسئلة الإختبار</label>
                                <select {{$isExamOfStart == true ? 'disabled':''}} class="form-control form-white"
                                        wire:model="exam_questions_count"
                                        data-placeholder="اختر عدد أسئلة الإختبار..." name="exam_questions_count"
                                        style="padding: 10px;">
                                    <option selected value="{{0}}">اختر عدد أسئلة الإختبار...</option>
                                    <option value="{{$exam_questions_min}}">{{$exam_questions_min}}</option>
                                </select>
                                @error('exam_questions_count')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif
                    </div>
                </div>
                @if ($isExamOfStart == true)
                    @include('pages.today_exams_summative.form_exam')
                    @include('pages.today_exams_summative.exam_approval')
                @endif
                @if ($isExamOfStart == false)
                    <div class="col-md-5">
                        <button type="button"
                                wire:click.prevent="examInformationApproval()"
                                class="btn btn-outline-success btn-sm">اعتماد معلومات اختبار التجميعي
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
