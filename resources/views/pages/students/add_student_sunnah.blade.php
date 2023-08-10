<div wire:ignore.self class="modal" tabindex="-1" role="dialog" id="add-student-sunnah" style="display: none;"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{$isUpdate ? 'تحديث حلقة السنة للطالب':'إضافة الطالب إلى حلقة السنة'}}</h5>
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
                            <label class="control-label">اختر الحلقة</label>
                            <select class="form-control form-white" wire:model.defer="group_id" style="padding: 1px">
                                <option selected value="">اختر الحلقة...</option>
                                @if (isset($groups))
                                    @foreach($groups as $group)
                                        <option value="{{$group->id}}">{{$group->teacher->user->name ??  $group->name . ' (لا يوجد محفظ)' }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('group_id')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger ripple" data-dismiss="modal">إغلاق</button>
                @if($isUpdate)
                    <button type="button" wire:click="pull_student();" class="btn btn-warning ripple">
                         سحب الطالب من الحلقة
                    </button>

                    <button type="button" wire:click="update();" class="btn btn-primary ripple">
                        تحديث الطالب
                    </button>
                @else
                    <button type="button" wire:click="store();" class="btn btn-success ripple">
                        إضافة الطالب
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>