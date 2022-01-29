<div wire:ignore.self class="modal" tabindex="-1" role="dialog" id="exam-question-count-select" style="display: none;"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تحديد عدد أسئلة اختبار التجميعي</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body p-20">
                <form>
                    <div class="row">
                        <div class="col-md-12">
                            <label class="control-label">اختر عدد أسئلة اختبار التجميعي</label>
                            <select class="form-control form-white" wire:model="exam_questions_count"
                                    data-placeholder="اختر عدد أسئلة اختبار التجميعي..." name="exam_questions_count"
                                    style="padding: 10px;">
                                <option selected value="{{0}}">اختر عدد أسئلة اختبار التجميعي...</option>
                                <option value="{{$exam_questions_min}}">{{$exam_questions_min}}</option>
                            </select>
                            @error('exam_questions_count')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger ripple" data-dismiss="modal">إغلاق</button>
                <button type="button" wire:click.prevent="examQuestionsNumberApproval" class="btn btn-success ripple">اعتماد عدد أسئلة اختبار التجميعي</button>
            </div>
        </div>
    </div>
</div>
