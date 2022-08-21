@if ($currentStep != 3)
    <div style="display: none"
         class="row setup-content"
         id="step-3">
        @endif
        <div class="col-xs-12">
            <div class="col-md-12"><br>
                <div class="col">
                    <label style="color: red">صورة الطالب</label>
                    <div class="form-group">
                        <div x-data="{ isUploading: false, progress: 5 }" x-on:livewire-upload-start="isUploading = true" x-on:livewire-upload-finish="isUploading = false; progress = 5" x-on:livewire-upload-error="isUploading = false" x-on:livewire-upload-progress="progress = $event.detail.progress">
                            <input type="file" wire:model="photo" accept="image/*">
                            <div x-show.transition="isUploading" class="progress progress-sm mt-2 rounded">
                                <div class="progress-bar bg-primary progress-bar-striped" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" x-bind:style="`width: ${progress}%`">
                                    <span class="sr-only">40% Complete (success)</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        @if ($photo)
                            <img src="{{ $photo->temporaryUrl() }}" style="width: 80px;"  class="img-fluid mr-15 avatar-small">
                        @else
                            <img src="{{ $photo_ret ?? '' }}" style="width: 50px;" class="img-fluid mr-15 avatar-small">
                        @endif
                    </div>

                </div>
                <br>
                <button class="btn btn-danger btn-sm nextBtn btn-lg pull-right"
                        type="button"
                        wire:click="back(2)">السابق
                </button>

                @if($student_id)
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
        <br>
    </div>
