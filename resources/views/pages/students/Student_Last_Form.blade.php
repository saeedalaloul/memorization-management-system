@if ($currentStep != 3)
    <div style="display: none"
         class="row setup-content"
         id="step-3">
        @endif
        <div class="col-xs-12">
            <div class="col-md-12"><br>
                <label style="color: red">صورة الطالب</label>
                <div class="form-group">
                    <input type="file" wire:model="photo" accept="image/*">
                </div>
                <br>

                <input type="hidden" wire:model="student_id">

                <button class="btn btn-danger btn-sm nextBtn btn-lg pull-right"
                        type="button"
                        wire:click="back(2)">السابق
                </button>

                @if($updateMode)
                    <button class="btn btn-success btn-sm nextBtn btn-lg pull-right"
                            wire:click="submitForm_edit"
                            type="button">تأكيد
                    </button>
                @else
                    <button class="btn btn-success btn-sm btn-lg pull-right"
                            wire:click="submitForm"
                            type="button">تأكيد
                    </button>
                @endif

            </div>
        </div>
    </div>
