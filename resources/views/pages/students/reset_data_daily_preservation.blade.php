<div wire:ignore.self class="modal" tabindex="-1" role="dialog" id="reset-data-daily-preservation"
     style="display: none;" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تصفير بيانات الحفظ والمراجعة</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body p-20">
                <form class="was-validated">
                    <div class="row">
                        <div class="col">
                            <label class="control-label">اسم الطالب</label>
                            <input type="text" wire:model="student_name" readonly class="form-control">
                            @error('student_id')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col">
                            <div class="custom-control custom-radio mb-3">
                                <input type="radio" value="1" wire:model="reset_data_daily_preservation_type"
                                       class="custom-control-input" id="customControlValidation2"
                                       name="radio-stacked" required>
                                <label class="custom-control-label" for="customControlValidation2">تصفير لبداية الجزء
                                    الحالي</label>
                                <div
                                    class="badge badge-danger font-weight-bold">{{$reset_data_daily_preservation_type == 1 ? $message_warning_reset_data : ""}}</div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="custom-control custom-radio mb-3">
                                <input type="radio" value="2" wire:model="reset_data_daily_preservation_type"
                                       class="custom-control-input" id="customControlValidation3"
                                       name="radio-stacked" required>
                                <label class="custom-control-label" for="customControlValidation3">تصفير جميع
                                    البيانات</label>
                                <div
                                    class="badge badge-danger font-weight-bold">{{$reset_data_daily_preservation_type == 2 ? $message_warning_reset_data : ""}}</div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success ripple" data-dismiss="modal">إغلاق</button>
                <button type="submit" wire:click="reset_daily_preservation();"
                        class="btn btn-danger ripple">
                    تصفير البيانات
                </button>
            </div>
        </div>
    </div>
</div>
