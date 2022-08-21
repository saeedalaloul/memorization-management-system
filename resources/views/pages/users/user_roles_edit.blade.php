<div class="row">
    <div class="col-md-12 mb-30">
        <div class="card card-statistics h-100">
            <div class="card-body">
                <div class="col-xs-12">
                    <div class="col-md-12">
                        <div class="form-row">
                            <div class="col">
                                <label for="name" style="font-size: 15px; color: #1e7e34">اسم المستخدم*</label>
                                <input type="text" name="name" class="form-control" wire:model.defer="name" readonly>
                            </div>
                            <div class="form-group col">
                                <label for="inputRole" style="font-size: 15px; color: #1e7e34">نوع المستخدم*</label>
                                <select class="custom-select my-1 mr-sm-2" wire:model="role_id">
                                    <option selected>اختر نوع المستخدم...</option>
                                    @foreach($roles as $role)
                                        <option value="{{$role->id}}">{{$role->name}}</option>
                                    @endforeach
                                </select>
                                @error('role_id')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-row">
                            @if($role_id != null)
                                @if ($roles->firstWhere('name', "مشرف") != null &&
                                    $role_id == $roles->firstWhere('name', "مشرف")->id ||
                                    $roles->firstWhere('name', "محفظ") != null &&
                                    $role_id == $roles->firstWhere('name', "محفظ")->id ||
                                    $roles->firstWhere('name', "طالب") != null &&
                                    $role_id == $roles->firstWhere('name', "طالب")->id)
                                    <div class="form-group col">
                                        <script>
                                            $("#grade").on('change', function (e) {
                                                let id = $(this).val()
                                            @this.set('grade_id', id);
                                            });
                                        </script>
                                        <label for="inputGrade" style="font-size: 15px; color: #1e7e34">اسم
                                            المرحلة*</label>
                                        <select  style="width: 100%;" class="custom-select my-1 mr-sm-2 select2" id="grade" wire:model.defer="grade_id">
                                            <option selected value="">اختر المرحلة...</option>
                                            @if (isset($grades))
                                                @foreach($grades as $grade)
                                                    <option value="{{$grade->id}}">{{$grade->name}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @error('grade_id')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @endif
                            @endif

                            @if ($role_id != null &&
                                 $roles->firstWhere('name', "طالب") != null &&
                                 $role_id == $roles->firstWhere('name', "طالب")->id)
                                <div class="form-group col">
                                    <script>
                                        $("#group").on('change', function (e) {
                                            let id = $(this).val()
                                        @this.set('group_id', id);
                                        });
                                    </script>
                                    <label for="inputGroup" style="font-size: 15px; color: #1e7e34">اسم الحلقة*</label>
                                    <select  style="width: 100%;" class="custom-select my-1 mr-sm-2 select2" id="group" wire:model.defer="group_id">
                                        <option selected>اختر الحلقة...</option>
                                        @if (isset($groups))
                                            @foreach($groups as $group)
                                                <option value="{{$group->id}}">{{$group->name}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('group_id')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endif
                        </div>
                        @if ($role_id != null &&
                                 $roles->firstWhere('name', "طالب") != null &&
                                 $role_id == $roles->firstWhere('name', "طالب")->id)
                            <div class="form-row">
                                <div class="col">
                                    <label for="name" style="font-size: 15px; color: #1e7e34">اسم ولي أمر
                                        الطالب*</label>
                                    <input type="text" name="name" class="form-control" wire:model.defer="father_name"
                                           readonly>
                                </div>
                                <div class="col">
                                    <label for="inputIdentificationNumber" style="font-size: 15px; color: #1e7e34">رقم
                                        هوية
                                        ولي الأمر*</label>
                                    <input type="number" name="father_identification_number" class="form-control"
                                           wire:model.defer="father_identification_number">
                                    @error('father_identification_number')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <br>
                        @endif

                        @if ($role_id != null && $ret_Roles->where('id',$role_id)->first())
                            <button wire:click.prevent="storeOrUpdateUserRole()"
                                    class="btn btn-outline-success btn-sm nextBtn btn-lg pull-right"
                                    type="button">تحديث صلاحية المستخدم
                            </button>
                            <button style="margin-right: 10px;" wire:click.prevent="pullUserRole()"
                                    class="btn btn-outline-danger btn-sm nextBtn btn-lg pull-right"
                                    type="button">سحب صلاحية المستخدم
                            </button>
                            @if ($role_id != null &&
                                 $roles->firstWhere('name', "طالب") != null &&
                                 $role_id == $roles->firstWhere('name', "طالب")->id)
                                <button wire:click.prevent="findStudentFather()"
                                        class="btn btn-outline-primary btn-sm nextBtn btn-lg pull-right"
                                        type="button">العثور على ولي أمر الطالب
                                </button>
                            @endif
                        @else

                            <button wire:click.prevent="storeOrUpdateUserRole()"
                                    class="btn btn-outline-primary btn-sm nextBtn btn-lg pull-right"
                                    type="button">إضافة صلاحية المستخدم
                            </button>
                            @if ($role_id != null &&
                                 $roles->firstWhere('name', "طالب") != null &&
                                 $role_id == $roles->firstWhere('name', "طالب")->id)
                                <button wire:click.prevent="findStudentFather()"
                                        class="btn btn-outline-success btn-sm nextBtn btn-lg pull-right"
                                        type="button">العثور على ولي أمر الطالب
                                </button>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
