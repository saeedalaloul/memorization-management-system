@if($currentStep !== 1)
    <div style="display: none" class="row setup-content" id="step-1">
        @endif
        <form autocomplete="off">
            <div class="col-xs-12">
                <div class="col-md-12">
                    <br>
                    @if($father_id === null)
                        <div class="form-row">
                            <div class="col">
                                <label for="title" style="font-size: 15px; color: #1e7e34">الإسم الأول</label>
                                <input type="text" wire:model.defer="father_first_name"
                                       class="form-control" {{$father_id !== null?'disabled':''}}>
                                @error('father_first_name')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                                @error('father_name')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col">
                                <label for="title" style="font-size: 15px; color: #1e7e34">اسم الأب</label>
                                <input type="text" wire:model.defer="father_second_name"
                                       class="form-control" {{$father_id !== null?'disabled':''}}>
                                @error('father_second_name')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                                @error('father_name')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col">
                                <label for="title" style="font-size: 15px; color: #1e7e34">اسم الجد</label>
                                <input type="text" wire:model.defer="father_third_name"
                                       class="form-control" {{$father_id !== null?'disabled':''}}>
                                @error('father_third_name')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                                @error('father_name')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col">
                                <label for="title" style="font-size: 15px; color: #1e7e34">اسم العائلة</label>
                                <input type="text" wire:model.defer="father_last_name"
                                       class="form-control" {{$father_id !== null?'disabled':''}}>
                                @error('father_last_name')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                                @error('father_name')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    @else
                        <div class="form-row">
                            <div class="col">
                                <label for="title" style="font-size: 15px; color: #1e7e34">رقم الهوية</label>
                                <input type="number" wire:keydown.enter="fatherFound();"
                                       wire:model.defer="father_identification_number"
                                       class="form-control" {{$father_id !== null?'disabled':''}}>
                                @error('father_identification_number')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col">
                                <label for="title" style="font-size: 15px; color: #1e7e34">الإسم رباعي</label>
                                <input type="text" wire:model.defer="father_name"
                                       class="form-control" {{$father_id !== null?'disabled':''}}>
                                @error('father_name')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    @endif
                    <div class="form-row">
                        @if($father_id === null)
                            <div class="col">
                                <label for="title" style="font-size: 15px; color: #1e7e34">رقم الهوية</label>
                                <input type="number" wire:keydown.enter="fatherFound();"
                                       wire:model.defer="father_identification_number"
                                       class="form-control" {{$father_id !== null?'disabled':''}}>
                                @error('father_identification_number')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif

                        <div class="col">
                            <label for="title" style="font-size: 15px; color: #1e7e34">رقم الجوال</label>
                            <input type="number" wire:model.defer="father_phone" class="form-control"
                                {{$father_id !== null?'disabled':''}}>
                            @error('father_phone')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group col">
                            <label for="inputGrade" style="font-size: 15px; color: #1e7e34">الوضع المادي</label>
                            <select class="custom-select my-1 mr-sm-2" name="economic_situation"
                                    wire:model.defer="economic_situation" {{$father_id !== null?'disabled':''}}>
                                <option selected value="">اختيار من القائمة...</option>
                                @foreach(\App\Models\UserInfo::status() as $status => $value)
                                    <option value="{{$status}}">{{$value}}</option>
                                @endforeach
                            </select>
                            @error('economic_situation')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                            <div class="form-group col">
                                <label for="inputGender" style="font-size: 15px; color: #1e7e34">الجنس</label>
                                <select class="custom-select my-1 mr-sm-2" name="father_gender"
                                        wire:model.defer="father_gender" {{$father_id !== null?'disabled':''}}>
                                    <option selected value="">اختيار من القائمة...</option>
                                    @foreach(\App\Models\User::genders() as $gender => $value)
                                        <option value="{{$gender}}">{{$value}}</option>
                                    @endforeach
                                </select>
                                @error('father_gender')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                    </div>
                </div>
                <br>
                <button class="btn btn-success btn-sm nextBtn btn-lg pull-right" wire:click="firstStepSubmit"
                        type="button">التالي
                </button>
                <button class="btn btn-primary btn-sm nextBtn btn-lg pull-right" type="button"
                        wire:click="fatherFound();">
                    العثور على ولي أمر الطالب
                </button>

            </div>
            <br>
        </form>
    </div>
