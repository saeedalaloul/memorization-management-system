<div wire:ignore.self class="modal" tabindex="-1" role="dialog" id="select-visit" style="display: none;"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">إقرار زيارة</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body p-20">
                <form>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="control-label">اسم المنشط</label>
                            <input type="text" wire:model.defer="activity_member_name" readonly class="form-control">
                            @error('activity_member_name')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="control-label">عدد أنشطة المنشط</label>
                            <input type="text" wire:model.defer="activities_orders_count" readonly class="form-control">
                            @error('activities_orders_count')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="control-label">اختر المراقب</label>
                            <select class="form-control form-white" wire:model.defer="oversight_member_id"
                                    data-placeholder="اختر المراقب..." name="oversight_member_id" style="padding: 10px;">
                                <option selected value="">اختر المراقب...</option>
                                @foreach($oversight_members as $oversight_member)
                                    @if($oversight_member->id != $activity_member_id)
                                        <option value="{{$oversight_member->id}}">{{$oversight_member->user->name}}</option>
                                    @endif
                                @endforeach
                            </select>
                            @error('oversight_member_id')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col">
                            <label for="title">تاريخ الزيارة</label>
                            <div class='input-group date'>
                                <input class="form-control" wire:model.defer="visit_date" type="date"
                                       data-date-format="yyyy-mm-dd">
                            </div>
                            @error('visit_date')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger ripple" data-dismiss="modal">إغلاق</button>
                <button type="button" wire:click="visitApproval()" class="btn btn-success ripple">اعتماد الزيارة</button>
            </div>
        </div>
    </div>
</div>
