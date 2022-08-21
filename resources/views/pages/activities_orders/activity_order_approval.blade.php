<div wire:ignore.self class="modal" tabindex="-1" role="dialog" id="approval-activity" style="display: none;"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">اعتماد طلب النشاط</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body p-20">
                <form>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="control-label">اسم المحفظ</label>
                            <input type="text" wire:model.defer="teacher_name" readonly class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="control-label">نوع النشاط</label>
                            <input type="text" wire:model.defer="activity_type" readonly class="form-control">
                        </div>
                        <div class="col-md-12">
                            <label class="control-label">اختر المنشط</label>
                            <select class="form-control form-white" wire:model.defer="activity_member_id"
                                    data-placeholder="اختر المنشط..." name="activity_member_id" style="padding: 10px;">
                                <option selected value="{{0}}">اختر المنشط...</option>
                                @foreach($activity_members as $activity_member)
                                    <option {{$activity_member_id != null && $activity_member_id == $activity_member->id?'selected':''}}  value="{{$activity_member->id}}">{{$activity_member->user->name}}</option>
                                @endforeach
                            </select>
                            @error('activity_member_id')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger ripple" data-dismiss="modal">إغلاق</button>
                <button type="button" wire:click="activityOrderApproval('{{$modalId}}')" class="btn btn-success ripple">اعتماد
                    طلب النشاط
                </button>
            </div>
        </div>
    </div>
</div>
