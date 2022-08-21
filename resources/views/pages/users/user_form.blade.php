<div class="row">
    <div class="col-md-12 mb-30">
        <div class="card card-statistics h-100">
            <div class="card-body">
                <div class="col-xs-12">
                    <div class="col-md-12">
                        <div class="form-row">
                            <div class="col">
                                <label for="name" style="font-size: 15px; color: #1e7e34">اسم المستخدم*</label>
                                <input type="text" name="name" class="form-control" wire:model.defer="name" required>
                                @error('name')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col">
                                <label for="email" style="font-size: 15px; color: #1e7e34">البريد الإلكتروني*</label>
                                <input type="email" name="email" class="form-control" wire:model.defer="email" required>
                                @error('email')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <br>

                        <div class="form-row">
                            <div class="col">
                                <label for="phone" style="font-size: 15px; color: #1e7e34">رقم الجوال*</label>
                                <input type="number" name="phone" class="form-control" wire:model.defer="phone" required>
                                @error('phone')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col">
                                <label for="identification_number" style="font-size: 15px; color: #1e7e34">رقم
                                    الهوية*</label>
                                <input type="number" name="identification_number" class="form-control"
                                       wire:model.defer="identification_number" required>
                                @error('identification_number')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <br>

                        <div class="form-row">
                            <div class="col">
                                <label for="dob" style="font-size: 15px; color: #1e7e34">تاريخ الميلاد*</label>
                                <div class='input-group date'>
                                    <input class="form-control" wire:model.defer="dob" type="date" id="datepicker-action"
                                           data-date-format="yyyy-mm-dd">
                                </div>
                                @error('dob')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col">
                                <label style="color: red">صورة المستخدم</label>
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
                        </div>

                        @if (!empty($modalId))
                            <button wire:click.prevent="update()"
                                    class="btn btn-success btn-sm nextBtn btn-lg pull-right"
                                    type="button">تحديث البيانات
                            </button>
                        @else

                            <button wire:click.prevent="store()"
                                    class="btn btn-success btn-sm nextBtn btn-lg pull-right"
                                    type="button">حفظ البيانات
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
