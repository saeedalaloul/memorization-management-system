<div wire:ignore.self class="modal" tabindex="-1" role="dialog" id="refusal-exam" style="display: none;"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">الطالب لم يجري اختبار التجميعي</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body p-20">
                <form>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="control-label">اسم الطالب</label>
                            <input type="text" wire:model="student_name" readonly class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="control-label">جزء اختبار التجميعي</label>
                            <input type="text" wire:model="quran_part" readonly class="form-control">
                        </div>
                        <div class="col-md-12">
                            <label class="control-label">ملاحظات</label>
                            <input type="text" wire:model="notes"  class="form-control">
                            @error('notes')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger ripple" data-dismiss="modal">إغلاق</button>
                <button type="button" wire:click="examOrderRefusal()" class="btn btn-success ripple">الطالب لم يجري اختبار التجميعي</button>
            </div>
        </div>
    </div>
</div>
