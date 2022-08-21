<div wire:ignore.self class="modal" tabindex="-1" role="dialog" id="block_cancel" style="display: none;"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">فك حظر الطالب</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body p-20">
                <form>
                    <div class="row">
                        <div class="col-md-12">
                            <label class="control-label">اسم الطالب</label>
                            <input type="text" wire:model.defer="student_name" readonly class="form-control">
                        </div>
                        <div class="col-md-12">
                            <label class="control-label">ملاحظات</label>
                            <input type="text" wire:model.defer="block_cancel_notes" class="form-control">
                            @error('block_cancel_notes')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger ripple" data-dismiss="modal">إغلاق</button>
                <button type="button"
                        wire:click="blockCancel()"
                        class="btn btn-success ripple">فك
                    حظر الطالب
                </button>
            </div>
        </div>
    </div>
</div>
