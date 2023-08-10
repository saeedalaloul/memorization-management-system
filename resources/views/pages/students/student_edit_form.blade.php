<div class="row">
    <div class="col-md-12 mb-30">
        <div class="card card-statistics h-100">
            <div class="card-body">
                <form autocomplete="off">
                    <div class="col-xs-12">
                        <div class="col-md-12">
                            <div class="form-row">
                                <div class="col">
                                    <label for="title" style="font-size: 15px; color: #1e7e34">اسم الطالب</label>
                                    <input type="text" name="student_name" class="form-control" wire:model.defer="student_name"
                                           required>
                                    @error('student_name')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col">
                                    <label for="title" style="font-size: 15px; color: #1e7e34">رقم الهوية</label>
                                    <input type="number" name="student_identification_number" class="form-control"
                                           wire:model.defer="student_identification_number" required>
                                    @error('student_identification_number')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col">
                                    <label for="title" style="font-size: 15px; color: #1e7e34">رقم جوال ولى
                                        الأمر</label>
                                    <input type="number" name="father_phone" class="form-control" wire:model.defer="father_phone"
                                           required>
                                    @error('father_phone')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <br>

                            <div class="form-row">
                                <div class="form-group col">
                                    <label for="title" style="font-size: 15px; color: #1e7e34">تاريخ الميلاد</label>
                                    <div class='input-group date'>
                                        <input class="form-control" wire:model.defer="dob" type="date"
                                               data-date-format="yyyy-mm-dd">
                                    </div>
                                    @error('dob')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group col">
                                    <label for="inputGrade" style="font-size: 15px; color: #1e7e34">الوضع المادي</label>
                                    <select class="custom-select my-1 mr-sm-2" name="economic_situation"
                                            wire:model.defer="economic_situation">
                                        <option selected>اختيار من القائمة...</option>
                                        @foreach(\App\Models\UserInfo::status() as $status => $value)
                                            <option value="{{$status}}">{{$value}}</option>
                                        @endforeach
                                    </select>
                                    @error('economic_situation')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group col">
                                    <label class="control-label" style="font-size: 15px; color: #1e7e34">رقم الواتس
                                        اب</label>
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
                            <br>

                            <div class="form-row">

                                <div class="col">
                                    <label style="color: red">صورة الطالب</label>
                                    <div class="form-group">
                                        <div x-data="{ isUploading: false, progress: 5 }"
                                             x-on:livewire-upload-start="isUploading = true"
                                             x-on:livewire-upload-finish="isUploading = false; progress = 5"
                                             x-on:livewire-upload-error="isUploading = false"
                                             x-on:livewire-upload-progress="progress = $event.detail.progress">
                                            <input type="file" wire:model="photo" accept="image/*">
                                            @error('photo')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                            <div x-show.transition="isUploading"
                                                 class="progress progress-sm mt-2 rounded">
                                                <div class="progress-bar bg-primary progress-bar-striped"
                                                     role="progressbar" aria-valuenow="40" aria-valuemin="0"
                                                     aria-valuemax="100" x-bind:style="`width: ${progress}%`">
                                                    <span class="sr-only">40% Complete (success)</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        @if ($photo)
                                            <img src="{{ $photo->temporaryUrl() }}" style="width: 80px;"
                                                 class="img-fluid mr-15 avatar-small">
                                        @else
                                            <img src="{{ $photo_ret ?? '' }}" style="width: 50px;"
                                                 class="img-fluid mr-15 avatar-small">
                                        @endif
                                    </div>

                                </div>
                            </div>

                            <button wire:click.prevent="validate_edit()"
                                    class="btn btn-success btn-sm nextBtn btn-lg pull-right"
                                    type="button">تحديث البيانات
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
