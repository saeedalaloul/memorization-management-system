<div wire:ignore.self class="modal" tabindex="-1" role="dialog" id="activity-request" style="display: none;"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">إجراء طلب النشاط</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body p-20">
                <form>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="control-label">نوع النشاط</label>
                            <input type="text" wire:model.defer="activity_type_name" readonly class="form-control">
                            @error('activity_type_name')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="control-label">اختر الطلاب</label>
                            <select style="padding: 1px;" multiple class="form-control form-white"
                                    wire:model.defer="students_ids"
                                    data-placeholder="اختر الطلاب..." name="students_ids" style="padding: 10px;">
                                @if (isset($students))
                                    @foreach($students as $student)
                                        <option value="{{$student['id']}}">{{$student['user']['name']}}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('students_ids')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col">
                            <label for="title">تاريخ النشاط</label>
                            <div class='input-group date'>
                                <input class="form-control" wire:model.defer="activity_date" type="datetime-local">
                            </div>
                            @error('activity_date')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger ripple" data-dismiss="modal">إغلاق</button>
                <button type="button" wire:click="storeActivityRequest();" class="btn btn-success ripple">إجراء طلب النشاط</button>
            </div>
        </div>
    </div>
</div>
