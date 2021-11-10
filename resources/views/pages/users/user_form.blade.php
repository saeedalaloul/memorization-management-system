<div class="row">
    <div class="col-md-12 mb-30">
        <div class="card card-statistics h-100">
            <div class="card-body">
                <div class="col-xs-12">
                    <div class="col-md-12">
                        <div class="form-row">
                            <div class="col">
                                <label for="name" style="font-size: 15px; color: #1e7e34">اسم المستخدم*</label>
                                <input type="text" name="name" class="form-control" wire:model="name" required>
                                @error('name')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col">
                                <label for="email" style="font-size: 15px; color: #1e7e34">البريد الإلكتروني*</label>
                                <input type="email" name="email" class="form-control" wire:model="email" required>
                                @error('email')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            @if ($modalId == null)
                                <div class="col">
                                    <label for="password" style="font-size: 15px; color: #1e7e34">كلمة المرور*</label>
                                    <input type="password" name="password" class="form-control" wire:model="password"
                                           required>
                                    @error('password')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endif
                        </div>
                        <br>

                        <div class="form-row">
                            <div class="col">
                                <label for="phone" style="font-size: 15px; color: #1e7e34">رقم الجوال*</label>
                                <input type="number" name="phone" class="form-control" wire:model="phone" required>
                                @error('phone')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col">
                                <label for="identification_number" style="font-size: 15px; color: #1e7e34">رقم الهوية*</label>
                                <input type="number" name="identification_number" class="form-control"
                                       wire:model="identification_number" required>
                                @error('identification_number')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col">
                                <label for="address" style="font-size: 15px; color: #1e7e34">العنوان*</label>
                                <input type="text" name="address" class="form-control"
                                       wire:model="address" required>
                                @error('address')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <br>

                        <div class="form-row">
                            <div class="col">
                                <label for="dob" style="font-size: 15px; color: #1e7e34">تاريخ الميلاد*</label>
                                <div class='input-group date'>
                                    <input class="form-control" wire:model="dob" type="date" id="datepicker-action"
                                           data-date-format="yyyy-mm-dd">
                                </div>
                                @error('dob')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col">
                                <label style="font-size: 15px; color: #1e7e34">صورة المستخدم</label>
                                <div class="form-group">
                                    <input class="form-control-file" type="file" wire:model="photo" accept="image/*">
                                </div>
                                <br>
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
