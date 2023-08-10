<div wire:ignore.self class="modal" tabindex="-1" role="dialog" id="add-previous-daily-memorization"
     style="display: none;"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"> متابعة الطالب : {{$student_name}} بتاريخ
                    : {{$dayOfWeek}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body p-20">

                <div class="row">
                    <div class="col-md-6">
                        <label for="title">التاريخ</label>
                        <div class='input-group date'>
                            <input type="date" class="form-control" wire:model="date">
                        </div>
                        @error('date')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    @if($status !== null && $date !== null)
                        <div class="col-md-6">
                            <label class="control-label"
                                   style="display: flex;justify-content: center;">العملية</label>
                            <select wire:model="selectedType" id="type" class="form-control form-white"
                                    style="padding: 1px;">
                                <option value="">اختر العملية</option>
                                @foreach(\App\Models\StudentDailyMemorization::types() as $key => $value)
                                    <option value="{{$key}}">{{$value}}</option>
                                @endforeach
                            </select>
                            @error('selectedType')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    @else
                        <div class="col-md-6">
                            <label class="control-label"
                                   style="display: flex;justify-content: center;">العملية</label>
                            <select wire:model="selectedType" id="type" class="form-control form-white"
                                    style="padding: 1px;">
                                <option value="">اختر العملية</option>
                            </select>
                            @error('selectedType')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    @endif
                </div>
                <br>
                <li class="list-group-item">

                    <div class="row">
                        <div class="col-md-3">
                            <label class="block text-gray-500 font-semibold sm:border-r sm:pr-4">
                                <input
                                    type="radio" wire:model="status" value="presence">
                                <span class="text-success">حضور</span>
                            </label>
                        </div>
                        <div class="col-md-3">
                            <label class="ml-1 block text-gray-500 font-semibold">
                                <input class="leading-tight" type="radio" wire:model="status" value="late">
                                <span class="text-warning">تأخر</span>
                            </label>
                        </div>
                        <div class="col-md-3">
                            <label class="ml-1 block text-gray-500 font-semibold">
                                <input
                                    class="leading-tight" type="radio" wire:model="status" value="authorized">
                                <span class="text-info">مأذون</span>
                            </label>
                        </div>
                        <div class="col-md-3">
                            <label class="ml-1 block text-gray-500 font-semibold">
                                <input class="leading-tight"
                                       type="radio" wire:model="status" value="absence">
                                <span class="text-danger">غياب</span>
                            </label>
                        </div>
                    </div>
                </li>

                @if($status === \App\Models\StudentAttendance::PRESENCE_STATUS || $status === \App\Models\StudentAttendance::LATE_STATUS)
                    <div class="row">
                        <div class="col-md-12">
                            @if($selectedType === \App\Models\StudentDailyMemorization::CUMULATIVE_REVIEW_TYPE)
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="control-label" style="display: flex;justify-content: start;">حدد
                                            الأجزاء المجتمعة</label>
                                        <select wire:model="selectedPartCombinedId" class="form-control form-white"
                                                style="padding: 1px;">
                                            <option value="">حدد الأجزاء المجتمعة...</option>
                                            @foreach($partsCombined as $partCombined)
                                                <option
                                                    value="{{$partCombined->id}}">{{$partCombined->name .' '.$partCombined->description}}</option>
                                            @endforeach
                                        </select>
                                        @error('selectedPartCombinedId')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="control-label" style="display: flex;justify-content: start;">حدد
                                            الجزء
                                            المنفرد</label>
                                        <select wire:model="selectedPartId" class="form-control form-white"
                                                style="padding: 1px;">
                                            <option value="">حدد الجزء المنفرد...</option>
                                            @foreach($parts as $part)
                                                <option
                                                    value="{{$part->id}}">{{$part->name .' '.$part->description}}</option>
                                            @endforeach
                                        </select>
                                        @error('selectedPartId')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="scrollbar max-h-200" style="overflow-y: auto; outline: #0b2e13;">
                                    @foreach($rows as $index => $value)
                                        @if(isset($suras[$index]->name))
                                            <div class="row" style="width: 100%;">
                                                <div class="col-md-5">
                                                    <label class="control-label">سورة</label>
                                                    <div class="form-check">
                                                        <input wire:model="suras_selected.{{$suras[$index]->id}}"
                                                               class="form-check-input"
                                                               type="checkbox" id="suras_selected.{{ $index }}" required="">
                                                        <label class="form-check-label"
                                                               for="suras_selected.{{$index}}">{{$suras[$index]->name}}</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-5">
                                                    <label class="control-label">أية</label>
                                                    @if(isset($suras_selected[$suras[$index]->id]) && $suras_selected[$suras[$index]->id])
                                                        <select disabled wire:model="" class="form-control form-white"
                                                                id="suras_selected.{{$index}}" style="padding: 1px;">
                                                            <option value="">اختر الآية...</option>
                                                        </select>
                                                    @else
                                                        <select wire:model="suras_custom_selected.{{$suras[$index]->id}}"
                                                                class="form-control form-white"
                                                                id="suras_selected.{{$index}}" style="padding: 1px;">
                                                            <option value="">اختر الآية...</option>
                                                            @for($i = $suras[$index]->aya_from+1;$i <= $suras[$index]->total_number_aya;$i++)
                                                                <option value="{{$i}}">{{$i}}</option>
                                                            @endfor
                                                        </select>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                <div class="scrollbar max-h-200" style="overflow-y: auto; outline: #0b2e13;">
                                    @foreach($rows as $index_ => $value_)
                                        <div class="row" style="width: 100%;">
                                            <div class="col-md-5">
                                                <label class="control-label">اختر السورة</label>
                                                <select
                                                    {{$modalId !== '' ? 'disabled readonly':''}} wire:model="suras_selected.{{$index_}}.id"
                                                    class="form-control form-white"
                                                    id="suras_selected.{{$index_}}.id"
                                                    style="padding: 1px;">
                                                    <option value="">اختر السورة...</option>
                                                    @if (isset($suras) && !empty($suras))
                                                        @foreach($suras as $sura)
                                                            <option value="{{$sura->id}}">{{$sura->name}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                @error('suras_selected.'.$index_.'.id')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-5">
                                                <label class="control-label">أية</label>
                                                <select wire:model="suras_selected.{{$index_}}.aya_to"
                                                        class="form-control form-white"
                                                        id="suras_selected.{{ $index_ }}.id" style="padding: 1px;">
                                                    <option value="">اختر الآية...</option>
                                                    @if (isset($suras_selected[$index_]['id']))
                                                        @foreach($suras as $sura)
                                                            @if($sura->id == $suras_selected[$index_]['id'])
                                                                @if($modalId === '')
                                                                    @for($i =$sura->aya_from+1;$i <= $sura->total_number_aya;$i++)
                                                                        <option value="{{$i}}">{{$i}}</option>
                                                                    @endfor
                                                                @else
                                                                    @for($i =$sura->aya_from;$i <= $sura->total_number_aya;$i++)
                                                                        <option value="{{$i}}">{{$i}}</option>
                                                                    @endfor
                                                                @endif
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                </select>
                                                @error('suras_selected.'.$index_.'.aya_to')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            @if($modalId !== '')
                                                <div class="col-md-2">
                                                    <label class="mr-sm-2">العمليات:</label>
                                                    <input class="btn btn-danger btn-block"
                                                           wire:click.prevent="removeRow({{$index_}})"
                                                           type="button" value="حذف"/>
                                                </div>
                                            @else
                                                @if($index_ === count($rows) - 1)
                                                    <div class="col-md-2">
                                                        <label class="mr-sm-2">العمليات:</label>
                                                        <input class="btn btn-danger btn-block"
                                                               wire:click.prevent="removeRow({{$index_}})"
                                                               type="button" value="حذف"/>
                                                    </div>
                                                @endif
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                @if($selectedType === \App\Models\StudentDailyMemorization::CUMULATIVE_REVIEW_TYPE)
                    <div class="row mt-20">
                        <div class="col-12">
                            @if($selected_count !== 0 && $selected_count === count($suras))
                                <input class="button" wire:click.prevent="undoSelectAll" type="button"
                                       value="إلغاء تحديد الكل"/>
                            @else
                                <input class="button" wire:click.prevent="selectAll" type="button" value="تحديد الكل"/>
                            @endif
                        </div>
                    </div>
                @else
                    @if($selectedType !== null && $selectedType !== '' && $daily_memorization === null)
                        <div class="row mt-20">
                            <div class="col-12">
                                <input class="button" wire:click.prevent="addRow" type="button" value="إدراج سجل"/>
                            </div>
                        </div>
                    @endif
                @endif
                @if($selectedType !== null && $selectedType !== '')
                    <div class="row">
                        <div class="col-md-12">
                            <label class="control-label">التقييم</label>
                            <select wire:model.defer="evaluation" class="form-control form-white" style="padding: 1px;">
                                <option value="">اختر التقييم...</option>
                            @foreach(\App\Models\StudentDailyMemorization::evaluations() as $key => $evaluation)
                                    <option value="{{$key}}">{{$evaluation}}</option>
                                @endforeach
                            </select>
                            @error('evaluation')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger ripple" data-dismiss="modal">إغلاق</button>
                @if($daily_memorization === null)
                    <button wire:click.prevent="validatePreviousModal()" type="button" class="btn btn-success ripple">
                        حفظ
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
