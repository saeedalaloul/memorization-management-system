@if($currentStep != 1)
    <div style="display: none" class="row setup-content" id="step-1">
        @endif
        <div class="col-xs-12">
            <div class="col-md-12">
                <br>
                <div class="form-row">
                    <div class="col">
                        <label for="title">رقم الهوية</label>
                        <input type="number" wire:model="father_identification_number" class="form-control">
                        @error('father_identification_number')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col">
                        <label for="title">الإسم</label>
                        <input type="text" wire:model="father_name"
                               class="form-control" {{$isFoundFather == true?'disabled':''}}>
                        @error('father_name')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="col">
                        <label for="title">رقم الجوال</label>
                        <input type="number" wire:model="father_phone" class="form-control"
                            {{$isFoundFather == true?'disabled':''}}>
                        @error('father_phone')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col">
                        <label for="title">تاريخ الميلاد</label>
                        <div class='input-group date'>
                            <input class="form-control" wire:model="father_dob" type="date" id="datepicker-action"
                                   data-date-format="yyyy-mm-dd" {{$isFoundFather == true?'disabled':''}}>
                        </div>
                        @error('father_dob')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="form-row">
                    <div class="col">
                        <label for="title">البريد الإلكتروني</label>
                        <input type="email" wire:model="father_email" class="form-control"
                            {{$isFoundFather == true?'disabled':''}}>
                        @error('father_email')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col">
                        <label for="title">كلمة المرور</label>
                        <input type="password" wire:model="father_password" class="form-control"
                            {{$isFoundFather == true?'disabled':''}}>
                        @error('father_password')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="form-row">
                    <div class="col">
                        <label for="address">عنوان الأب</label>
                        <input type="text" name="father_address" class="form-control"
                               wire:model="father_address" {{$isFoundFather == true?'disabled':''}}>
                        @error('father_address')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                @if($updateMode)
                    <button class="btn btn-success btn-sm nextBtn btn-lg pull-right" wire:click="firstStepSubmit_edit"
                            type="button">التالي
                    </button>
                @else
                    <button class="btn btn-success btn-sm nextBtn btn-lg pull-right" wire:click="firstStepSubmit"
                            type="button">التالي
                    </button>
                @endif

            </div>
        </div>
    </div>
