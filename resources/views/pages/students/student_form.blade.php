@if($currentStep !== 2)
    <div style="display: none" class="row setup-content" id="step-2">
        @endif
        <div class="col-xs-12">
            <div class="col-md-12">
                <br>
                <div class="form-row">
                    <div class="col">
                        <label for="title" style="font-size: 15px; color: #1e7e34">الإسم الأول</label>
                        <input type="text" wire:model.defer="student_first_name" class="form-control">
                        @error('student_first_name')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                        @error('student_name')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col">
                        <label for="title" style="font-size: 15px; color: #1e7e34">اسم الأب</label>
                        <input type="text" wire:model.defer="student_second_name" class="form-control">
                        @error('student_second_name')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                        @error('student_name')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col">
                        <label for="title" style="font-size: 15px; color: #1e7e34">اسم الجد</label>
                        <input type="text" wire:model.defer="student_third_name" class="form-control">
                        @error('student_third_name')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                        @error('student_name')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col">
                        <label for="title" style="font-size: 15px; color: #1e7e34">اسم العائلة</label>
                        <input type="text" wire:model.defer="student_last_name" class="form-control">
                        @error('student_last_name')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                        @error('student_name')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="col">
                        <label for="title" style="font-size: 15px; color: #1e7e34">رقم الهوية</label>
                        <input type="number" wire:model.defer="student_identification_number" class="form-control">
                        @error('student_identification_number')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col">
                        <label for="title" style="font-size: 15px; color: #1e7e34">تاريخ الميلاد</label>
                        <div class='input-group date'>
                            <input class="form-control" wire:model.defer="dob" type="date"
                                   data-date-format="yyyy-mm-dd">
                        </div>
                        @error('dob')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col">
                        <label class="control-label" style="font-size: 15px; color: #1e7e34">رقم الواتس اب</label>
                        <div class="input-group">
						<span class="input-group-btn">
						  <select class="custom-select my-1 mr-sm-2" wire:model.defer="country_code">
                            <option value="" selected>اختر كود الدولة...</option>
                            <option value="+970">+970</option>
                            <option value="+972">+972</option>
                        </select>
						</span>
                            <input type="number" wire:model.defer="whatsapp_number"
                                   class="form-control"/> @error('country_code')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror</div>
                        @error('whatsapp_number')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>

                </div>


                <div class="form-row">
                    <div class="form-group col">
                        <label for="inputGrade" style="font-size: 15px; color: #1e7e34">اسم المرحلة</label>
                        <select style="width: 100%;" class="custom-select my-1 mr-sm-2 select2" id="grade_"
                                wire:model.defer="grade_id">
                            <option selected value="">اختر المرحلة...</option>
                            @foreach($grades as $grade)
                                <option value="{{$grade->id}}">{{$grade->name}}</option>
                            @endforeach
                        </select>
                        @error('grade_id')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group col">
                        <label for="inputGroup" style="font-size: 15px; color: #1e7e34">اسم الحلقة</label>
                        <select style="width: 100%;" class="custom-select my-1 mr-sm-2 select2" id="group"
                                wire:model.defer="group_id">
                            <option selected value="">اختر الحلقة...</option>
                            @foreach($groups as $group)
                                <option value="{{$group->id}}">
                                    @if ($group->teacher_id === null)
                                        {{$group->name . ' (لا يوجد محفظ)'}}
                                    @else
                                        {{ $group->teacher->user->name }}
                                    @endif
                                </option>
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

                <button class="btn btn-success btn-sm nextBtn btn-lg pull-right" type="button"
                        wire:click="secondStepSubmit">التالي
                </button>
            </div>
        </div>
        <br>
    </div>
