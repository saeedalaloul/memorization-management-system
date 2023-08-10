<div wire:ignore.self class="modal" tabindex="-1" role="dialog" id="submit-order-exam-sunnah" style="display: none;"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">اجراء طلب اختبار</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body p-20">
                <form>
                    <div class="row">
                        <div class="col-md-12">
                            <label class="control-label">اسم الطالب</label>
                            <input type="text" wire:model.defer="student_name" readonly class="form-control">
                            @error('modalId')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="control-label">اختر الجزء</label>
                            <select class="form-control form-white" wire:model.defer="sunnah_part_id"
                                    data-placeholder="اختر الجزء..." style="padding: 1px">
                                <option selected value="">اختر الجزء...</option>
                                @if (isset($sunnah_parts))
                                    @foreach($sunnah_parts as $sunnah_part)
                                        <option value="{{$sunnah_part->id}}">{{$sunnah_part->name.' ('.$sunnah_part->total_hadith_parts.') حديث'}}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('sunnah_part_id')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="control-label">اختر يوم الإختبار</label>
                            <select class="form-control form-white" wire:model.defer="suggested_day"
                                    style="padding: 1px">
                                <option selected value="">اختر يوم الإختبار...</option>
                                @if (isset($suggested_exam_days))
                                    @foreach($suggested_exam_days as $suggested_exam_day)
                                        <option
                                            value="{{$suggested_exam_day}}">{{\App\Models\ExamSettings::days()[$suggested_exam_day]}}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('suggested_day')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger ripple" data-dismiss="modal">إغلاق</button>
                <button type="button" wire:click="submitExamRequest();"
                        class="btn btn-success ripple">
                    طلب اختبار
                </button>
            </div>
        </div>
    </div>
</div>
