@if($currentStep != 1)
    <div style="display: none" class="row setup-content" id="step-1">
        @endif
        <form autocomplete="off">
            <div class="col-xs-12">
                <div class="col-md-12">
                    <br>
                    <div class="form-row">
                        <div class="col">
                            <label for="title">رقم الهوية</label>
                            <input type="number" wire:keydown.enter="fatherFound();"
                                   wire:model.defer="father_identification_number" class="form-control" {{$father_id != null?'disabled':''}}>
                            @error('father_identification_number')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col">
                            <label for="title">الإسم</label>
                            <input type="text" wire:model.defer="father_name"
                                   class="form-control" {{$father_id != null?'disabled':''}}>
                            @error('father_name')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col">
                            <label for="title">رقم الجوال</label>
                            <input type="number" wire:model.defer="father_phone" class="form-control"
                                {{$father_id != null?'disabled':''}}>
                            @error('father_phone')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group col">
                            <label for="inputGrade">الوضع المادي</label>
                            <select class="custom-select my-1 mr-sm-2" name="economic_situation"
                                    wire:model.defer="economic_situation" {{$father_id != null?'disabled':''}}>
                                <option selected>اختيار من القائمة...</option>
                                @foreach(\App\Models\UserInfo::status() as $status => $value)
                                    <option value="{{$status}}">{{$value}}</option>
                                @endforeach
                            </select>
                            @error('economic_situation')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <br>
                @if($student_id)
                    <button class="btn btn-success btn-sm nextBtn btn-lg pull-right" wire:click="firstStepSubmit_edit"
                            type="button">التالي
                    </button>
                @else
                    <button class="btn btn-success btn-sm nextBtn btn-lg pull-right" wire:click="firstStepSubmit"
                            type="button">التالي
                    </button>
                @endif

            </div>
            <br>
        </form>
    </div>
