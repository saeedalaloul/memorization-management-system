<div wire:ignore.self class="modal" tabindex="-1" role="dialog" id="approval-exam" style="display: none;"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">اعتماد الإختبار</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body p-20">
                <form>
                    <div class="row">
                        <div class="col-md-12">
                            <label class="control-label">ملاحظات</label>
                            <input type="text" wire:model.defer="exam_notes" max="50" class="form-control">
                            @error('exam_notes')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        @if ($examOrder->partable_type == 'App\Models\QuranPart')
                            <div class="col-md-12">
                                <label class="control-label">علامة أحكام الطالب*</label>
                                <input type="number" wire:model="another_mark" max="10" min="7" class="form-control">
                                @error('another_mark')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        @else
                            <div class="col-md-12">
                                <label class="control-label">علامة سؤال المعاني*</label>
                                <input type="number" wire:model="another_mark" max="3" min="0" class="form-control">
                                @error('another_mark')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif
                        <div class="col-md-12">
                            <label class="control-label">درجة الإختبار النهائية</label>
                            <input type="text" style="color: {{$exam_mark >= $success_mark ? 'green': 'red'}}"
                                   wire:model.defer="final_exam_score" readonly class="form-control">
                            @error('exam_mark')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger ripple" data-dismiss="modal">إغلاق</button>
                <button type="button" {{$exam_mark >= 90 ? 'disabled':''}} wire:click="exam_no_success()"
                        class="btn btn-warning ripple">لم يجتاز الإختبار
                </button>
                <button type="button" wire:click="examApproval()" class="btn btn-success ripple">اعتماد الإختبار
                </button>
            </div>
        </div>
    </div>
</div>
