<div class="row">
    <div class="col-md-12 mb-30">
        <div class="card card-statistics h-100">
            <div class="card-body">
                <div class="col-xs-12">
                    <div class="col-md-12">
                        <div class="form-row">
                            <div class="col">
                                <label for="title">اسم الإداري</label>
                                <input type="text" name="name" class="form-control" wire:model="name" required>
                                @error('name')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col">
                                <label for="title">البريد الإلكتروني</label>
                                <input type="email" name="email" class="form-control" wire:model="email" required>
                                @error('email')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col">
                                <label for="title">كلمة المرور</label>
                                <input type="password" name="password" class="form-control" wire:model="password"
                                       required>
                                @error('password')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <br>

                        <div class="form-row">
                            <div class="col">
                                <label for="title">رقم الجوال</label>
                                <input type="number" name="phone" class="form-control" wire:model="phone" required>
                                @error('phone')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col">
                                <label for="title">رقم الهوية</label>
                                <input type="number" name="identification_number" class="form-control"
                                       wire:model="identification_number" required>
                                @error('identification_number')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col">
                                <label for="address">العنوان</label>
                                <input type="text" name="address" class="form-control"
                                       wire:model="address" required>
                                @error('address')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <br>

                        <div class="form-row">
                            <div class="form-group col">
                                <label for="inputGrade">اسم المرحلة</label>
                                <select class="custom-select my-1 mr-sm-2" name="grade_id" wire:model="grade_id">
                                    <option selected>اختيار من القائمة...</option>
                                    @foreach($grades as $grade)
                                        <option value="{{$grade->id}}">{{$grade->name}}</option>
                                    @endforeach
                                </select>
                                @error('grade_id')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col">
                                <label for="title">تاريخ الميلاد</label>
                                <div class='input-group date'>
                                    <input class="form-control" wire:model="dob" type="date" id="datepicker-action"
                                           data-date-format="yyyy-mm-dd">
                                </div>
                                @error('dob')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col">
                                <label style="color: red">صورة الإداري</label>
                                <div class="form-group">
                                    <input type="file" wire:model="photo" accept="image/*">
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
