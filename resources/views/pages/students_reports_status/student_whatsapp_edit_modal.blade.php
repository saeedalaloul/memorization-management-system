<div wire:ignore.self class="modal" tabindex="-1" role="dialog" id="student-whatsapp-edit" style="display: none;"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تحديث واتساب الطالب</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body p-20">
                <form>
                    <div class="row">
                        <div class="col-md-4">
                            <label class="control-label">اسم الطالب</label>
                            <input type="text" wire:model.defer="student_name" readonly class="form-control">
                        </div>

                        <div class="col-md-8">
                            <label class="control-label" style="font-size: 15px; color: #1e7e34">رقم الواتس اب</label>
                            <div class="input-group">
						<span class="input-group-btn">
						  <select class="custom-select my-1 mr-sm-2" wire:model.defer="country_code">
                            <option value="" selected>اختر كود الدولة...</option>
                            <option value="+970">+970</option>
                            <option value="+972">+972</option>
                        </select>
						</span>
                                <input type="number" wire:model.defer="whatsapp_number"
                                       class="form-control"/> @error('country_code')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror</div>
                            @error('whatsapp_number')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger ripple" data-dismiss="modal">إغلاق</button>
                <button type="button" wire:click="updateStudentWhatsapp();" class="btn btn-success ripple">
                    تحديث
                    واتساب الطالب
                </button>
            </div>
        </div>
    </div>
</div>
