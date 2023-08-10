<div wire:ignore.self class="modal" tabindex="-1" role="dialog" id="approval-exam" style="display: none;"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">اعتماد طلب الإختبار</h5>
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
                            @error('student_id')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="control-label">اسم آخر مختبر</label>
                            <input type="text" wire:model.defer="last_tester_name" readonly class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="control-label">عدد مرات الإعادة</label>
                            <input type="text" wire:model.defer="number_failure_times" readonly class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="control-label">اختر المختبر</label>
                            <select wire:model.defer="tester_id" class="form-control form-white" style="padding: 10px;">
                                <option selected value="">اختر المختبر...</option>
                                @foreach($testers as $tester)
                                    @if($tester->id !== $teacher_id)
                                        <option
                                            {{$tester_id !== null && $tester_id === $tester->id?'selected':''}}  value="{{$tester->id}}">{{$tester->user->name}}</option>
                                    @endif
                                @endforeach
                            </select>
                            @error('tester_id')
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
                <button type="button" wire:click="examOrderApproval('{{$modalId}}')" class="btn btn-success ripple">
                    اعتماد
                    طلب الإختبار
                </button>
            </div>
        </div>
    </div>
</div>
