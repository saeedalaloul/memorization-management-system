<div wire:ignore.self class="modal" tabindex="-1" role="dialog" id="add-daily-memorization" style="display: none;"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"> متابعة الطالب : {{$student_name}} بتاريخ
                    : {{$dayOfWeek}} {{date('Y-m-d')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body p-20">

                <div class="row">
                    <div class="col-md-12">
                        <label class="control-label" style="display: flex;justify-content: center;">العملية</label>
                        <select wire:model.defer="selectedType" id="type" class="form-control form-white" style="padding: 1px;">
                            @foreach(\App\Models\StudentDailyMemorization::types() as $key => $value)
                            <option value="{{$key}}">{{$value}}</option>
                            @endforeach
                        </select>
                        @error('selectedType')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <label class="control-label">من سورة</label>
                        <select wire:model="sura_from_id" class="form-control form-white" style="padding: 1px;">
                            @if (isset($suras_from))
                                @foreach($suras_from as $sura)
                                    <option value="{{$sura->id}}">{{$sura->name}}</option>
                                @endforeach
                            @endif
                        </select>
                        @error('sura_from_id')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="control-label">أية</label>
                        <select wire:model.defer="aya_from_id" class="form-control form-white" style="padding: 1px;">
                            @if (isset($ayas_from))
                                @foreach($ayas_from as $aya_from)
                                    <option value="{{$aya_from}}">{{$aya_from}}</option>
                                @endforeach
                            @endif
                        </select>
                        @error('aya_from_id')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <label class="control-label">إلى سورة</label>
                        <select wire:model="sura_to_id" class="form-control form-white" style="padding: 1px;">
                            @if (isset($suras_to))
                                @foreach($suras_to as $sura)
                                    <option value="{{$sura->id}}">{{$sura->name}}</option>
                                @endforeach
                            @endif
                        </select>
                        @error('sura_to_id')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="control-label">أية</label>
                        <select wire:model.defer="aya_to_id" class="form-control form-white" style="padding: 1px;">
                            @if (isset($ayas_to))
                                @foreach($ayas_to as $aya_to)
                                    <option value="{{$aya_to}}">{{$aya_to}}</option>
                                @endforeach
                            @endif
                        </select>
                        @error('aya_to_id')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <label class="control-label">التقييم</label>
                        <select wire:model.defer="evaluation" class="form-control form-white" style="padding: 1px;">
                            @foreach(\App\Models\StudentDailyMemorization::evaluations() as $key => $evaluation)
                                <option value="{{$key}}">{{$evaluation}}</option>
                            @endforeach
                        </select>
                        @error('evaluation')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger ripple" data-dismiss="modal">إغلاق</button>
                <button wire:click.prevent="validateModal()" type="button" class="btn btn-success ripple">حفظ</button>
            </div>
        </div>
    </div>
</div>
