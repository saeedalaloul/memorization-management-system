<div wire:ignore.self class="modal" tabindex="-1" role="dialog" id="visit-processing" style="display: none;"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">جاري معالجة الزيارة</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body p-20">
                <form>
                    <div class="row">
                        <div class="col">
                            <label for="title">تاريخ المعالجة</label>
                            <div class='input-group date'>
                                <input class="form-control" wire:model.defer="visit_processing_date" type="date"
                                       data-date-format="yyyy-mm-dd">
                            </div>
                            @error('visit_processing_date')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger ripple" data-dismiss="modal">إغلاق</button>
                <button type="button" wire:click="visitProcessing();" class="btn btn-success ripple">جاري
                    المعالجة
                </button>
            </div>
        </div>
    </div>
</div>
