<div wire:ignore.self class="modal" tabindex="-1" role="dialog" id="manage-external-exam" style="display: none;"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{$process_type == 'manage-exam' ? 'إدارة الإختبار الخارجي':'رصد درجة الإختبار الخارجي'}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body p-20">
                <form>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="control-label">اسم الطالب</label>
                            <input type="text" wire:model.defer="student_name" readonly class="form-control">
                            @error('student_id')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="control-label">جزء الإختبار</label>
                            <input type="text" wire:model.defer="quran_part" readonly class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="control-label">درجة الإختبار</label>
                            <input type="number" wire:model.defer="exam_mark" class="form-control">
                            @error('exam_mark')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col">
                            <label for="title">تاريخ الإختبار</label>
                            <div class='input-group date'>
                                <input class="form-control" wire:model.defer="exam_date" type="date"
                                       data-date-format="yyyy-mm-dd">
                            </div>
                            @error('exam_date')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger ripple" data-dismiss="modal">إغلاق</button>
                @if ($process_type == 'manage_exam')
                    <button type="button" wire:click="delete_external_exam();" class="btn btn-warning ripple">
                        حذف الإختبار الخارجي
                    </button>
                    <button type="button" wire:click="update_or_assign_external_exam_mark();"
                            class="btn btn-success ripple">
                        تحديث الإختبار الخارجي
                    </button>
                @else
                    <button type="button" wire:click="update_or_assign_external_exam_mark();"
                            class="btn btn-success ripple">
                        رصد درجة الإختبار الخارجي
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
