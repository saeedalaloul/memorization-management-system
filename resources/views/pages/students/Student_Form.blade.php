@if($currentStep != 2)
    <div style="display: none" class="row setup-content" id="step-2">
        @endif
        <div class="col-xs-12">
            <div class="col-md-12">
                <br>

                <div class="form-row">
                    <div class="col">
                        <label for="title">رقم الهوية</label>
                        <input type="number" wire:model="student_identification_number" class="form-control">
                        @error('student_identification_number')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col">
                        <label for="title">الإسم</label>
                        <input type="text" wire:model="student_name" class="form-control">
                        @error('student_name')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="col">
                        <label for="title">البريد الإلكتروني</label>
                        <input type="text" wire:model="student_email" class="form-control">
                        @error('student_email')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    @if (!$updateMode)
                        <div class="col">
                            <label for="title">كلمة المرور</label>
                            <input type="password" wire:model="student_password" class="form-control">
                            @error('student_password')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    @endif
                </div>

                <div class="form-row">
                    <div class="col">
                        <label for="title">رقم الجوال</label>
                        <input type="number" wire:model="student_phone" class="form-control">
                        @error('student_phone')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col">
                        <label for="title">تاريخ الميلاد</label>
                        <div class='input-group date'>
                            <input class="form-control" wire:model="dob" type="date" id="datepicker-action"
                                   data-date-format="yyyy-mm-dd">
                        </div>
                        @error('dob')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>

                </div>


                <div class="form-row">
                    <div class="form-group col">
                        <label for="inputGrade">اسم المرحلة</label>
                        <select class="custom-select my-1 mr-sm-2" wire:model="grade_id">
                            <option selected>اختر المرحلة...</option>
                            @foreach($grades as $grade)
                                <option value="{{$grade->id}}">{{$grade->name}}</option>
                            @endforeach
                        </select>
                        @error('grade_id')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    @if (!is_null($groups))
                        <div class="form-group col">
                            <label for="inputGroup">اسم الحلقة</label>
                            <select class="custom-select my-1 mr-sm-2" wire:model="group_id">
                                <option selected>اختر الحلقة...</option>
                                @foreach($groups as $group)
                                    <option value="{{$group->id}}">{{$group->name}}</option>
                                @endforeach
                            </select>
                            @error('group_id')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    @endif
                </div>

                <div class="form-group">
                    <label for="address">عنوان الطالب</label>
                    <textarea class="form-control" wire:model="student_address" id="exampleFormControlTextarea1"
                              rows="1"></textarea>
                    @error('student_address')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>

                <button class="btn btn-danger btn-sm nextBtn btn-lg pull-right" type="button" wire:click="back(1)">
                    السابق
                </button>

                @if($updateMode)
                    <button class="btn btn-success btn-sm nextBtn btn-lg pull-right" wire:click="secondStepSubmit_edit"
                            type="button">التالي
                    </button>
                @else
                    <button class="btn btn-success btn-sm nextBtn btn-lg pull-right" type="button"
                            wire:click="secondStepSubmit">التالي
                    </button>
                @endif

            </div>
        </div>
    </div>
