@if($currentStep != 2)
    <div style="display: none" class="row setup-content" id="step-2">
        @endif
        <div class="col-xs-12">
            <div class="col-md-12">
                <br>

                <div class="form-row">
                    <div class="col">
                        <label for="title">رقم الهوية</label>
                        <input type="number" wire:model.defer="student_identification_number" class="form-control">
                        @error('student_identification_number')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col">
                        <label for="title">الإسم</label>
                        <input type="text" wire:model.defer="student_name" class="form-control">
                        @error('student_name')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="col">
                        <label for="title">تاريخ الميلاد</label>
                        <div class='input-group date'>
                            <input class="form-control" wire:model.defer="dob" type="date" data-date-format="yyyy-mm-dd">
                        </div>
                        @error('dob')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>


                <div class="form-row">
                    <div class="form-group col">
                        <label for="inputGrade">اسم المرحلة</label>
                        <select style="width: 100%;" class="custom-select my-1 mr-sm-2 select2" id="grade_" wire:model.defer="grade_id">
                            <option selected>اختر المرحلة...</option>
                            @foreach($grades as $grade)
                                <option value="{{$grade->id}}">{{$grade->name}}</option>
                            @endforeach
                        </select>
                        @error('grade_id')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group col">
                        <label for="inputGroup">اسم الحلقة</label>
                        <select style="width: 100%;" class="custom-select my-1 mr-sm-2 select2" id="group" wire:model.defer="group_id">
                            <option selected>اختر الحلقة...</option>
                            @foreach($groups as $group)
                                <option value="{{$group->id}}">{{$group->teacher->user->name}}</option>
                            @endforeach
                        </select>
                        @error('group_id')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <br>

                <button class="btn btn-danger btn-sm nextBtn btn-lg pull-right" type="button" wire:click="back(1)">
                    السابق
                </button>

                @if($student_id)
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
        <br>
    </div>
