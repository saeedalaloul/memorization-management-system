<div wire:ignore.self class="modal" tabindex="-1" role="dialog" id="manage-exam" style="display: none;"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">إدارة اختبار السنة</h5>
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
                            <input type="text" wire:model.defer="sunnah_part" readonly class="form-control">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label class="control-label">اسم المحفظ</label>
                            <input type="text" wire:model.defer="teacher_name" readonly class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="control-label">اختر المختبر</label>
                            <select wire:model.defer="tester_id" class="form-control form-white" style="padding: 10px;">
                                <option selected value="">اختر المختبر...</option>
                                @foreach($testers as $tester)
                                    @if($tester->id != $teacher_id)
                                        <option value="{{$tester->id}}">{{$tester->user->name}}</option>
                                    @endif
                                @endforeach
                            </select>
                            @error('tester_id')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <label for="title">تاريخ الإختبار</label>
                            <div class='input-group date'>
                                <input class="form-control" wire:model.defer="exam_date" type="date"
                                       data-date-format="yyyy-mm-dd">
                            </div>
                            @error('exam_date')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="inputSuccessMark">اختر نسبة النجاح</label>
                            <select class="form-control form-white" wire:model.defer="exam_success_mark_id"
                                    style="padding: 1px">
                                <option selected value="">اختر نسبة النجاح</option>
                                @foreach($exam_success_marks as $exam_success_mark)
                                    <option
                                        value="{{$exam_success_mark->id}}">{{$exam_success_mark->mark}}</option>
                                @endforeach
                            </select>
                            @error('exam_success_mark_id')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="control-label">درجة الإختبار</label>
                            <input type="number" wire:model.defer="exam_mark" class="form-control">
                            @error('exam_mark')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger ripple" data-dismiss="modal">إغلاق</button>
                <button type="button" wire:click="delete_exam();" class="btn btn-warning ripple">
                    حذف اختبار السنة
                </button>
                <button type="button" wire:click="update_exam();"
                        class="btn btn-success ripple">
                    تحديث اختبار السنة
                </button>
            </div>
        </div>
    </div>
</div>
