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
                        <select wire:model.defer="selectedType" id="type" class="form-control form-white"
                                style="padding: 1px;">
                            @foreach(\App\Models\StudentSunnahDailyMemorization::types() as $key => $value)
                                <option value="{{$key}}">{{$value}}</option>
                            @endforeach
                        </select>
                        @error('selectedType')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <label class="control-label">الكتاب</label>
                        @if ($modalId !== '')
                            <input type="text" wire:model="book_name" readonly class="form-control">
                        @else
                            <select wire:model="book_id" class="form-control form-white" style="padding: 1px;">
                                @if (isset($books))
                                    @foreach($books as $book)
                                        <option value="{{$book->id}}">{{$book->name}}</option>
                                    @endforeach
                                @endif
                            </select>
                        @endif
                        @error('book_id')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="control-label">من حديث</label>
                        @if ($modalId !== '')
                            <input type="text" wire:model="hadith_from_id" readonly class="form-control">
                        @else
                        <select wire:model.defer="hadith_from_id" class="form-control form-white" style="padding: 1px;">
                            @if (isset($hadiths_from))
                                @foreach($hadiths_from as $hadith_from)
                                    <option value="{{$hadith_from}}">{{$hadith_from}}</option>
                                @endforeach
                            @endif
                        </select>
                        @endif
                        @error('hadith_from_id')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="control-label">إلى حديث</label>
                        <select wire:model.defer="hadith_to_id" class="form-control form-white" style="padding: 1px;">
                            @if (isset($hadiths_to))
                                @foreach($hadiths_to as $hadith_to)
                                    <option value="{{$hadith_to}}">{{$hadith_to}}</option>
                                @endforeach
                            @endif
                        </select>
                        @error('hadith_to_id')
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
                @if($modalId !== '')
                    <button wire:click.prevent="delete()" type="button" class="btn btn-warning ripple">حذف التسميع
                        اليومي
                    </button>
                    <button wire:click.prevent="updateDailyMemorization()" type="button" class="btn btn-primary ripple">
                        تحديث
                        التسميع اليومي
                    </button>
                @else
                    <button wire:click.prevent="validateModal()" type="button" class="btn btn-success ripple">حفظ
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
