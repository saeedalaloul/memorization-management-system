<div class="row">
    <div class="col-xl-12 mb-30">
        <div class="card card-statistics h-100">
            <div class="card-body">
                <div class="col-xs-12">
                    <div class="form-row">
                        <div class="form-punitive col">
                            <label for="inputPunitive">اختر الاجراء العقابي</label>
                            <select style="width: 100%;" class="custom-select my-1 mr-sm-2" {{!empty($modalId) && empty($process_type) ? 'readonly disabled': ''}}
                                    wire:model.defer="selectedPunitiveMeasure">
                                <option selected="">اختر الاجراء العقابي</option>
                                @foreach(\App\Models\PunitiveMeasure::types() as $key => $type)
                                    <option value="{{$key}}">{{$type}}</option>
                                @endforeach
                            </select>
                            @error('selectedPunitiveMeasure')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-punitive col">
                            <label for="inputReason">اختر السبب</label>
                            <select style="width: 100%;" class="custom-select my-1 mr-sm-2" {{!empty($modalId) && empty($process_type) ? 'readonly disabled': ''}} wire:model="selectedReason">
                                <option selected="">اختر السبب</option>
                                @foreach(\App\Models\PunitiveMeasure::reasons() as $key => $reason)
                                    <option value="{{$key}}">{{$reason}}</option>
                                @endforeach
                            </select>
                            @error('selectedReason')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group col">
                            <label for="title">حدد عدد الأيام</label>
                            <div class='input-group number'>
                                <input class="form-control" max="7" min="3" wire:model.defer="number_times"
                                       type="number">
                            </div>
                            @error('number_times')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        @if ($selectedReason == \App\Models\PunitiveMeasure::MEMORIZE_REASON)
                            <div class="form-group col">
                                <label for="title">حدد كمية الحفظ</label>
                                <div class='input-group number'>
                                    <input class="form-control" max="5" min="0.5" wire:model.defer="quantity"
                                           type="number">
                                </div>
                                @error('quantity')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif

                    </div>
                    <div class="col-md-5">
                        @if(!empty($modalId) && empty($process_type))
                            <button type="button"
                                    wire:click.prevent="update();"
                                    class="btn btn-outline-success btn-sm">تحديث الإجراء العقابي
                            </button>
                        @else
                            <button type="button"
                                    wire:click.prevent="store();"
                                    class="btn btn-outline-success btn-sm">اعتماد الإجراء العقابي
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
