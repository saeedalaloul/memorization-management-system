<div class="row">
    <div class="col-md-12 mb-30">
        <div class="card card-statistics h-100">
            <div class="card-body" x-data="{currentPermissionTab: $persist('grade')}">
                <h5 class="card-title" style="font-size: 15px; color: #1e7e34">إدارة صلاحيات المستخدم ({{$name ?? ''}})</h5>
                <div class="tab nav-border">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item" @click.prevent="currentPermissionTab = 'grade'">
                            <a class="nav-link" :class="currentPermissionTab === 'grade' ? 'active show':'' "
                               id="grade-02-tab"
                               data-toggle="tab" href="#grade-02" role="tab" aria-controls="grade-02"
                               aria-selected="true">المراحل</a>
                        </li>
                        <li class="nav-item" @click.prevent="currentPermissionTab = 'group'">
                            <a class="nav-link" :class="currentPermissionTab === 'group' ? 'active show':'' "
                               id="group-02-tab" data-toggle="tab" href="#group-02" role="tab" aria-controls="group-02"
                               aria-selected="false">المجموعات</a>
                        </li>
                        <li class="nav-item" @click.prevent="currentPermissionTab = 'supervisor'">
                            <a class="nav-link" :class="currentPermissionTab === 'supervisor' ? 'active show':'' "
                               id="supervisor-02-tab" data-toggle="tab" href="#supervisor-02" role="tab"
                               aria-controls="supervisor-02"
                               aria-selected="false">مشرفي المراحل</a>
                        </li>
                        <li class="nav-item" @click.prevent="currentPermissionTab = 'teacher'">
                            <a class="nav-link" id="teacher-02-tab"
                               :class="currentPermissionTab === 'teacher' ? 'active show':'' "
                               data-toggle="tab" href="#teacher-02" role="tab" aria-controls="teacher-02"
                               aria-selected="false">المحفظين</a>
                        </li>
                        <li class="nav-item" @click.prevent="currentPermissionTab = 'student'">
                            <a class="nav-link" id="student-02-tab"
                               :class="currentPermissionTab === 'student' ? 'active show':'' "
                               data-toggle="tab" href="#student-02" role="tab" aria-controls="student-02"
                               aria-selected="false">الطلاب</a>
                        </li>
                        <li class="nav-item" @click.prevent="currentPermissionTab = 'exam'">
                            <a class="nav-link" id="exam-02-tab"
                               :class="currentPermissionTab === 'exam' ? 'active show':'' "
                               data-toggle="tab" href="#exam-02" role="tab" aria-controls="exam-02"
                               aria-selected="false">الإختبارات</a>
                        </li>
                        <li class="nav-item" @click.prevent="currentPermissionTab = 'order-exam'">
                            <a class="nav-link" id="order-exam-02-tab"
                               :class="currentPermissionTab === 'order-exam' ? 'active show':'' "
                               data-toggle="tab" href="#order-exam-02" role="tab" aria-controls="order-exam-02"
                               aria-selected="false">طلبات الإختبارات</a>
                        </li>
                        <li class="nav-item" @click.prevent="currentPermissionTab = 'tester'">
                            <a class="nav-link" id="tester-02-tab"
                               :class="currentPermissionTab === 'tester' ? 'active show':'' "
                               data-toggle="tab" href="#tester-02" role="tab" aria-controls="tester-02"
                               aria-selected="false">المختبرين</a>
                        </li>
                        <li class="nav-item" @click.prevent="currentPermissionTab = 'activity'">
                            <a class="nav-link" id="activity-02-tab"
                               :class="currentPermissionTab === 'activity' ? 'active show':'' "
                               data-toggle="tab" href="#activity-02" role="tab" aria-controls="activity-02"
                               aria-selected="false">الأنشطة</a>
                        </li>
                        <li class="nav-item" @click.prevent="currentPermissionTab = 'activity-member'">
                            <a class="nav-link" id="activity-member-02-tab"
                               :class="currentPermissionTab === 'activity-member' ? 'active show':'' "
                               data-toggle="tab" href="#activity-member-02" role="tab" aria-controls="activity-member-02"
                               aria-selected="false">أعضاء الأنشطة</a>
                        </li>
                        <li class="nav-item" @click.prevent="currentPermissionTab = 'oversight-member'">
                            <a class="nav-link" id="oversight-member-02-tab"
                               :class="currentPermissionTab === 'oversight-member' ? 'active show':'' "
                               data-toggle="tab" href="#oversight-member-02" role="tab" aria-controls="oversight-member-02"
                               aria-selected="false">أعضاء الرقابة</a>
                        </li>
                        <li class="nav-item" @click.prevent="currentPermissionTab = 'box'">
                            <a class="nav-link" id="box-02-tab"
                               :class="currentPermissionTab === 'box' ? 'active show':'' "
                               data-toggle="tab" href="#box-02" role="tab" aria-controls="box-02"
                               aria-selected="false">صندوق الشكاوي والرقابة</a>
                        </li>
                        <li class="nav-item" @click.prevent="currentPermissionTab = 'punitive-measures'">
                            <a class="nav-link" id="punitive-measures-02-tab"
                               :class="currentPermissionTab === 'punitive-measures' ? 'active show':'' "
                               data-toggle="tab" href="#punitive-measures-02" role="tab" aria-controls="punitive-measures-02"
                               aria-selected="false">الإجراءات العقابية</a>
                        </li>
                        <li class="nav-item" @click.prevent="currentPermissionTab = 'user'">
                            <a class="nav-link" id="user-02-tab"
                               :class="currentPermissionTab === 'user' ? 'active show':'' "
                               data-toggle="tab" href="#user-02" role="tab" aria-controls="user-02"
                               aria-selected="false">المستخدمين</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade" :class="currentPermissionTab === 'grade' ? 'active show':'' "
                             id="grade-02"
                             role="tabpanel"
                             aria-labelledby="grade-02-tab">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="checkbox checbox-switch switch-success">
                                            <label>
                                                @php
                                                    $isCheck = false;
                                                @endphp
                                                @foreach($user_permissions as $key => $value)
                                                    @if ($value['name'] == 'إدارة المراحل')
                                                        @php
                                                            $isCheck = true;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                <input type="checkbox" name="switch"
                                                       wire:click="update_permission('إدارة المراحل')"
                                                    {{ $isCheck== true ? "checked" : ""}}>
                                                <span></span>
                                                إدارة المراحل
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="checkbox checbox-switch switch-success">
                                            <label>
                                                @php
                                                    $isCheck = false;
                                                @endphp
                                                @foreach($user_permissions as $key => $value)
                                                    @if ($value['name'] == 'إضافة مرحلة')
                                                        @php
                                                            $isCheck = true;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                <input type="checkbox" name="switch"
                                                       wire:click="update_permission('إضافة مرحلة')"
                                                    {{ $isCheck== true ? "checked" : ""}}>
                                                <span></span>
                                                إضافة مرحلة
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="checkbox checbox-switch switch-success">
                                            <label>
                                                @php
                                                    $isCheck = false;
                                                @endphp
                                                @foreach($user_permissions as $key => $value)
                                                    @if ($value['name'] == 'تعديل مرحلة')
                                                        @php
                                                            $isCheck = true;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                <input type="checkbox" name="switch"
                                                       wire:click="update_permission('تعديل مرحلة')"
                                                    {{ $isCheck== true ? "checked" : ""}}>
                                                <span></span>
                                                تعديل مرحلة
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="checkbox checbox-switch switch-success">
                                            <label>
                                                @php
                                                    $isCheck = false;
                                                @endphp
                                                @foreach($user_permissions as $key => $value)
                                                    @if ($value['name'] == 'حذف مرحلة')
                                                        @php
                                                            $isCheck = true;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                <input type="checkbox" name="switch"
                                                       wire:click="update_permission('حذف مرحلة')"
                                                    {{ $isCheck== true ? "checked" : ""}}>
                                                <span></span>
                                                حذف مرحلة
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" :class="currentPermissionTab === 'group' ? 'active show':'' "
                             id="group-02"
                             role="tabpanel"
                             aria-labelledby="group-02-tab">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="checkbox checbox-switch switch-success">
                                            <label>
                                                @php
                                                    $isCheck = false;
                                                @endphp
                                                @foreach($user_permissions as $key => $value)
                                                    @if ($value['name'] == 'إدارة المجموعات')
                                                        @php
                                                            $isCheck = true;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                <input type="checkbox" name="switch"
                                                       wire:click="update_permission('إدارة المجموعات')"
                                                    {{ $isCheck== true ? "checked" : ""}}>
                                                <span></span>
                                                إدارة المجموعات
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="checkbox checbox-switch switch-success">
                                            <label>
                                                @php
                                                    $isCheck = false;
                                                @endphp
                                                @foreach($user_permissions as $key => $value)
                                                    @if ($value['name'] == 'إضافة مجموعة')
                                                        @php
                                                            $isCheck = true;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                <input type="checkbox" name="switch"
                                                       wire:click="update_permission('إضافة مجموعة')"
                                                    {{ $isCheck== true ? "checked" : ""}}>
                                                <span></span>
                                                إضافة مجموعة
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="checkbox checbox-switch switch-success">
                                            <label>
                                                @php
                                                    $isCheck = false;
                                                @endphp
                                                @foreach($user_permissions as $key => $value)
                                                    @if ($value['name'] == 'نقل مجموعة')
                                                        @php
                                                            $isCheck = true;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                <input type="checkbox" name="switch"
                                                       wire:click="update_permission('نقل مجموعة')"
                                                    {{ $isCheck== true ? "checked" : ""}}>
                                                <span></span>
                                                نقل مجموعة
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="checkbox checbox-switch switch-success">
                                            <label>
                                                @php
                                                    $isCheck = false;
                                                @endphp
                                                @foreach($user_permissions as $key => $value)
                                                    @if ($value['name'] == 'تعديل مجموعة')
                                                        @php
                                                            $isCheck = true;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                <input type="checkbox" name="switch"
                                                       wire:click="update_permission('تعديل مجموعة')"
                                                    {{ $isCheck== true ? "checked" : ""}}>
                                                <span></span>
                                                تعديل مجموعة
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="checkbox checbox-switch switch-success">
                                            <label>
                                                @php
                                                    $isCheck = false;
                                                @endphp
                                                @foreach($user_permissions as $key => $value)
                                                    @if ($value['name'] == 'حذف مجموعة')
                                                        @php
                                                            $isCheck = true;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                <input type="checkbox" name="switch"
                                                       wire:click="update_permission('حذف مجموعة')"
                                                    {{ $isCheck== true ? "checked" : ""}}>
                                                <span></span>
                                                حذف مجموعة
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="checkbox checbox-switch switch-success">
                                            <label>
                                                @php
                                                    $isCheck = false;
                                                @endphp
                                                @foreach($user_permissions as $key => $value)
                                                    @if ($value['name'] == 'سحب محفظ من مجموعة')
                                                        @php
                                                            $isCheck = true;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                <input type="checkbox" name="switch"
                                                       wire:click="update_permission('سحب محفظ من مجموعة')"
                                                    {{ $isCheck== true ? "checked" : ""}}>
                                                <span></span>
                                                سحب محفظ من مجموعة
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" :class="currentPermissionTab === 'supervisor' ? 'active show':'' "
                             id="supervisor-02"
                             role="tabpanel"
                             aria-labelledby="supervisor-02-tab">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="checkbox checbox-switch switch-success">
                                            <label>
                                                @php
                                                    $isCheck = false;
                                                @endphp
                                                @foreach($user_permissions as $key => $value)
                                                    @if ($value['name'] == 'إدارة مشرفي المراحل')
                                                        @php
                                                            $isCheck = true;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                <input type="checkbox" name="switch"
                                                       wire:click="update_permission('إدارة مشرفي المراحل')"
                                                    {{ $isCheck== true ? "checked" : ""}}>
                                                <span></span>
                                                إدارة مشرفي المراحل
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="checkbox checbox-switch switch-success">
                                            <label>
                                                @php
                                                    $isCheck = false;
                                                @endphp
                                                @foreach($user_permissions as $key => $value)
                                                    @if ($value['name'] == 'إضافة مشرف مرحلة')
                                                        @php
                                                            $isCheck = true;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                <input type="checkbox" name="switch"
                                                       wire:click="update_permission('إضافة مشرف مرحلة')"
                                                    {{ $isCheck== true ? "checked" : ""}}>
                                                <span></span>
                                                إضافة مشرف مرحلة
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="checkbox checbox-switch switch-success">
                                            <label>
                                                @php
                                                    $isCheck = false;
                                                @endphp
                                                @foreach($user_permissions as $key => $value)
                                                    @if ($value['name'] == 'تعديل مشرف مرحلة')
                                                        @php
                                                            $isCheck = true;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                <input type="checkbox" name="switch"
                                                       wire:click="update_permission('تعديل مشرف مرحلة')"
                                                    {{ $isCheck== true ? "checked" : ""}}>
                                                <span></span>
                                                تعديل مشرف مرحلة
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="checkbox checbox-switch switch-success">
                                            <label>
                                                @php
                                                    $isCheck = false;
                                                @endphp
                                                @foreach($user_permissions as $key => $value)
                                                    @if ($value['name'] == 'حذف مشرف مرحلة')
                                                        @php
                                                            $isCheck = true;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                <input type="checkbox" name="switch"
                                                       wire:click="update_permission('حذف مشرف مرحلة')"
                                                    {{ $isCheck== true ? "checked" : ""}}>
                                                <span></span>
                                                حذف مشرف مرحلة
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" :class="currentPermissionTab === 'teacher' ? 'active show':'' "
                             id="teacher-02"
                             role="tabpanel"
                             aria-labelledby="teacher-02-tab">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="checkbox checbox-switch switch-success">
                                            <label>
                                                @php
                                                    $isCheck = false;
                                                @endphp
                                                @foreach($user_permissions as $key => $value)
                                                    @if ($value['name'] == 'إدارة المحفظين')
                                                        @php
                                                            $isCheck = true;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                <input type="checkbox" name="switch"
                                                       wire:click="update_permission('إدارة المحفظين')"
                                                    {{ $isCheck== true ? "checked" : ""}}>
                                                <span></span>
                                                إدارة المحفظين
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="checkbox checbox-switch switch-success">
                                            <label>
                                                @php
                                                    $isCheck = false;
                                                @endphp
                                                @foreach($user_permissions as $key => $value)
                                                    @if ($value['name'] == 'إضافة محفظ')
                                                        @php
                                                            $isCheck = true;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                <input type="checkbox" name="switch"
                                                       wire:click="update_permission('إضافة محفظ')"
                                                    {{ $isCheck== true ? "checked" : ""}}>
                                                <span></span>
                                                إضافة محفظ
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="checkbox checbox-switch switch-success">
                                            <label>
                                                @php
                                                    $isCheck = false;
                                                @endphp
                                                @foreach($user_permissions as $key => $value)
                                                    @if ($value['name'] == 'إدارة حضور وغياب المحفظين')
                                                        @php
                                                            $isCheck = true;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                <input type="checkbox" name="switch"
                                                       wire:click="update_permission('إدارة حضور وغياب المحفظين')"
                                                    {{ $isCheck== true ? "checked" : ""}}>
                                                <span></span>
                                                إدارة حضور وغياب المحفظين
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="checkbox checbox-switch switch-success">
                                            <label>
                                                @php
                                                    $isCheck = false;
                                                @endphp
                                                @foreach($user_permissions as $key => $value)
                                                    @if ($value['name'] == 'تعديل محفظ')
                                                        @php
                                                            $isCheck = true;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                <input type="checkbox" name="switch"
                                                       wire:click="update_permission('تعديل محفظ')"
                                                    {{ $isCheck== true ? "checked" : ""}}>
                                                <span></span>
                                                تعديل محفظ
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="checkbox checbox-switch switch-success">
                                            <label>
                                                @php
                                                    $isCheck = false;
                                                @endphp
                                                @foreach($user_permissions as $key => $value)
                                                    @if ($value['name'] == 'حذف محفظ')
                                                        @php
                                                            $isCheck = true;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                <input type="checkbox" name="switch"
                                                       wire:click="update_permission('حذف محفظ')"
                                                    {{ $isCheck== true ? "checked" : ""}}>
                                                <span></span>
                                                حذف محفظ
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" :class="currentPermissionTab === 'student' ? 'active show':'' "
                             id="student-02"
                             role="tabpanel"
                             aria-labelledby="student-02-tab">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="checkbox checbox-switch switch-success">
                                            <label>
                                                @php
                                                    $isCheck = false;
                                                @endphp
                                                @foreach($user_permissions as $key => $value)
                                                    @if ($value['name'] == 'إدارة الطلاب')
                                                        @php
                                                            $isCheck = true;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                <input type="checkbox" name="switch"
                                                       wire:click="update_permission('إدارة الطلاب')"
                                                    {{ $isCheck== true ? "checked" : ""}}>
                                                <span></span>
                                                إدارة الطلاب
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="checkbox checbox-switch switch-success">
                                            <label>
                                                @php
                                                    $isCheck = false;
                                                @endphp
                                                @foreach($user_permissions as $key => $value)
                                                    @if ($value['name'] == 'إضافة طالب')
                                                        @php
                                                            $isCheck = true;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                <input type="checkbox" name="switch"
                                                       wire:click="update_permission('إضافة طالب')"
                                                    {{ $isCheck== true ? "checked" : ""}}>
                                                <span></span>
                                                إضافة طالب
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="checkbox checbox-switch switch-success">
                                            <label>
                                                @php
                                                    $isCheck = false;
                                                @endphp
                                                @foreach($user_permissions as $key => $value)
                                                    @if ($value['name'] == 'عرض طالب')
                                                        @php
                                                            $isCheck = true;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                <input type="checkbox" name="switch"
                                                       wire:click="update_permission('عرض طالب')"
                                                    {{ $isCheck== true ? "checked" : ""}}>
                                                <span></span>
                                                عرض طالب
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="checkbox checbox-switch switch-success">
                                            <label>
                                                @php
                                                    $isCheck = false;
                                                @endphp
                                                @foreach($user_permissions as $key => $value)
                                                    @if ($value['name'] == 'تعديل طالب')
                                                        @php
                                                            $isCheck = true;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                <input type="checkbox" name="switch"
                                                       wire:click="update_permission('تعديل طالب')"
                                                    {{ $isCheck== true ? "checked" : ""}}>
                                                <span></span>
                                                تعديل طالب
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="checkbox checbox-switch switch-success">
                                            <label>
                                                @php
                                                    $isCheck = false;
                                                @endphp
                                                @foreach($user_permissions as $key => $value)
                                                    @if ($value['name'] == 'حذف طالب')
                                                        @php
                                                            $isCheck = true;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                <input type="checkbox" name="switch"
                                                       wire:click="update_permission('حذف طالب')"
                                                    {{ $isCheck== true ? "checked" : ""}}>
                                                <span></span>
                                                حذف طالب
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="checkbox checbox-switch switch-success">
                                            <label>
                                                @php
                                                    $isCheck = false;
                                                @endphp
                                                @foreach($user_permissions as $key => $value)
                                                    @if ($value['name'] == 'إدارة تقرير الحفظ والمراجعة')
                                                        @php
                                                            $isCheck = true;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                <input type="checkbox" name="switch"
                                                       wire:click="update_permission('إدارة تقرير الحفظ والمراجعة')"
                                                    {{ $isCheck== true ? "checked" : ""}}>
                                                <span></span>
                                                إدارة تقرير الحفظ والمراجعة
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="checkbox checbox-switch switch-success">
                                            <label>
                                                @php
                                                    $isCheck = false;
                                                @endphp
                                                @foreach($user_permissions as $key => $value)
                                                    @if ($value['name'] == 'إدارة متابعة الحفظ والمراجعة')
                                                        @php
                                                            $isCheck = true;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                <input type="checkbox" name="switch"
                                                       wire:click="update_permission('إدارة متابعة الحفظ والمراجعة')"
                                                    {{ $isCheck== true ? "checked" : ""}}>
                                                <span></span>
                                                إدارة متابعة الحفظ والمراجعة
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="checkbox checbox-switch switch-success">
                                            <label>
                                                @php
                                                    $isCheck = false;
                                                @endphp
                                                @foreach($user_permissions as $key => $value)
                                                    @if ($value['name'] == 'تصفير بيانات الحفظ والمراجعة')
                                                        @php
                                                            $isCheck = true;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                <input type="checkbox" name="switch"
                                                       wire:click="update_permission('تصفير بيانات الحفظ والمراجعة')"
                                                    {{ $isCheck== true ? "checked" : ""}}>
                                                <span></span>
                                                تصفير بيانات الحفظ والمراجعة
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="checkbox checbox-switch switch-success">
                                            <label>
                                                @php
                                                    $isCheck = false;
                                                @endphp
                                                @foreach($user_permissions as $key => $value)
                                                    @if ($value['name'] == 'إدارة التقارير الشهرية')
                                                        @php
                                                            $isCheck = true;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                <input type="checkbox" name="switch"
                                                       wire:click="update_permission('إدارة التقارير الشهرية')"
                                                    {{ $isCheck== true ? "checked" : ""}}>
                                                <span></span>
                                                إدارة التقارير الشهرية
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" :class="currentPermissionTab === 'exam' ? 'active show':'' "
                             id="exam-02"
                             role="tabpanel"
                             aria-labelledby="exam-02-tab">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="checkbox checbox-switch switch-success">
                                            <label>
                                                @php
                                                    $isCheck = false;
                                                @endphp
                                                @foreach($user_permissions as $key => $value)
                                                    @if ($value['name'] == 'إدارة الإختبارات')
                                                        @php
                                                            $isCheck = true;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                <input type="checkbox" name="switch"
                                                       wire:click="update_permission('إدارة الإختبارات')"
                                                    {{ $isCheck== true ? "checked" : ""}}>
                                                <span></span>
                                                إدارة الإختبارات
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="checkbox checbox-switch switch-success">
                                            <label>
                                                @php
                                                    $isCheck = false;
                                                @endphp
                                                @foreach($user_permissions as $key => $value)
                                                    @if ($value['name'] == 'إضافة اختبار')
                                                        @php
                                                            $isCheck = true;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                <input type="checkbox" name="switch"
                                                       wire:click="update_permission('إضافة اختبار')"
                                                    {{ $isCheck== true ? "checked" : ""}}>
                                                <span></span>
                                                إضافة اختبار
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="checkbox checbox-switch switch-success">
                                            <label>
                                                @php
                                                    $isCheck = false;
                                                @endphp
                                                @foreach($user_permissions as $key => $value)
                                                    @if ($value['name'] == 'إعدادات الإختبارات')
                                                        @php
                                                            $isCheck = true;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                <input type="checkbox" name="switch"
                                                       wire:click="update_permission('إعدادات الإختبارات')"
                                                    {{ $isCheck== true ? "checked" : ""}}>
                                                <span></span>
                                                إعدادات الإختبارات
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="checkbox checbox-switch switch-success">
                                            <label>
                                                @php
                                                    $isCheck = false;
                                                @endphp
                                                @foreach($user_permissions as $key => $value)
                                                    @if ($value['name'] == 'إدارة اختبارات اليوم')
                                                        @php
                                                            $isCheck = true;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                <input type="checkbox" name="switch"
                                                       wire:click="update_permission('إدارة اختبارات اليوم')"
                                                    {{ $isCheck== true ? "checked" : ""}}>
                                                <span></span>
                                                إدارة اختبارات اليوم
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" :class="currentPermissionTab === 'order-exam' ? 'active show':'' "
                             id="order-exam-02"
                             role="tabpanel"
                             aria-labelledby="order-exam-02-tab">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="checkbox checbox-switch switch-success">
                                            <label>
                                                @php
                                                    $isCheck = false;
                                                @endphp
                                                @foreach($user_permissions as $key => $value)
                                                    @if ($value['name'] == 'إدارة طلبات الإختبارات')
                                                        @php
                                                            $isCheck = true;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                <input type="checkbox" name="switch"
                                                       wire:click="update_permission('إدارة طلبات الإختبارات')"
                                                    {{ $isCheck== true ? "checked" : ""}}>
                                                <span></span>
                                                إدارة طلبات الإختبارات
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="checkbox checbox-switch switch-success">
                                            <label>
                                                @php
                                                    $isCheck = false;
                                                @endphp
                                                @foreach($user_permissions as $key => $value)
                                                    @if ($value['name'] == 'إجراء طلب اختبار')
                                                        @php
                                                            $isCheck = true;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                <input type="checkbox" name="switch"
                                                       wire:click="update_permission('إجراء طلب اختبار')"
                                                    {{ $isCheck== true ? "checked" : ""}}>
                                                <span></span>
                                                إجراء طلب اختبار
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="checkbox checbox-switch switch-success">
                                            <label>
                                                @php
                                                    $isCheck = false;
                                                @endphp
                                                @foreach($user_permissions as $key => $value)
                                                    @if ($value['name'] == 'إجراء الإختبار')
                                                        @php
                                                            $isCheck = true;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                <input type="checkbox" name="switch"
                                                       wire:click="update_permission('إجراء الإختبار')"
                                                    {{ $isCheck== true ? "checked" : ""}}>
                                                <span></span>
                                                إجراء الإختبار
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" :class="currentPermissionTab === 'tester' ? 'active show':'' "
                             id="tester-02"
                             role="tabpanel"
                             aria-labelledby="tester-02-tab">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="checkbox checbox-switch switch-success">
                                            <label>
                                                @php
                                                    $isCheck = false;
                                                @endphp
                                                @foreach($user_permissions as $key => $value)
                                                    @if ($value['name'] == 'إدارة المختبرين')
                                                        @php
                                                            $isCheck = true;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                <input type="checkbox" name="switch"
                                                       wire:click="update_permission('إدارة المختبرين')"
                                                    {{ $isCheck== true ? "checked" : ""}}>
                                                <span></span>
                                                إدارة المختبرين
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="checkbox checbox-switch switch-success">
                                            <label>
                                                @php
                                                    $isCheck = false;
                                                @endphp
                                                @foreach($user_permissions as $key => $value)
                                                    @if ($value['name'] == 'إضافة مختبر')
                                                        @php
                                                            $isCheck = true;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                <input type="checkbox" name="switch"
                                                       wire:click="update_permission('إضافة مختبر')"
                                                    {{ $isCheck== true ? "checked" : ""}}>
                                                <span></span>
                                                إضافة مختبر
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="checkbox checbox-switch switch-success">
                                            <label>
                                                @php
                                                    $isCheck = false;
                                                @endphp
                                                @foreach($user_permissions as $key => $value)
                                                    @if ($value['name'] == 'حذف مختبر')
                                                        @php
                                                            $isCheck = true;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                <input type="checkbox" name="switch"
                                                       wire:click="update_permission('حذف مختبر')"
                                                    {{ $isCheck== true ? "checked" : ""}}>
                                                <span></span>
                                                حذف مختبر
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" :class="currentPermissionTab === 'activity' ? 'active show':'' "
                             id="activity-02"
                             role="tabpanel"
                             aria-labelledby="activity-02-tab">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="checkbox checbox-switch switch-success">
                                            <label>
                                                @php
                                                    $isCheck = false;
                                                @endphp
                                                @foreach($user_permissions as $key => $value)
                                                    @if ($value['name'] == 'إدارة الأنشطة')
                                                        @php
                                                            $isCheck = true;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                <input type="checkbox" name="switch"
                                                       wire:click="update_permission('إدارة الأنشطة')"
                                                    {{ $isCheck== true ? "checked" : ""}}>
                                                <span></span>
                                                إدارة الأنشطة
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="checkbox checbox-switch switch-success">
                                            <label>
                                                @php
                                                    $isCheck = false;
                                                @endphp
                                                @foreach($user_permissions as $key => $value)
                                                    @if ($value['name'] == 'إدارة طلبات الأنشطة')
                                                        @php
                                                            $isCheck = true;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                <input type="checkbox" name="switch"
                                                       wire:click="update_permission('إدارة طلبات الأنشطة')"
                                                    {{ $isCheck== true ? "checked" : ""}}>
                                                <span></span>
                                                إدارة طلبات الأنشطة
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="checkbox checbox-switch switch-success">
                                            <label>
                                                @php
                                                    $isCheck = false;
                                                @endphp
                                                @foreach($user_permissions as $key => $value)
                                                    @if ($value['name'] == 'إجراء طلب نشاط')
                                                        @php
                                                            $isCheck = true;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                <input type="checkbox" name="switch"
                                                       wire:click="update_permission('إجراء طلب نشاط')"
                                                    {{ $isCheck== true ? "checked" : ""}}>
                                                <span></span>
                                                إجراء طلب نشاط
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="checkbox checbox-switch switch-success">
                                            <label>
                                                @php
                                                    $isCheck = false;
                                                @endphp
                                                @foreach($user_permissions as $key => $value)
                                                    @if ($value['name'] == 'إدارة أنواع الأنشطة')
                                                        @php
                                                            $isCheck = true;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                <input type="checkbox" name="switch"
                                                       wire:click="update_permission('إدارة أنواع الأنشطة')"
                                                    {{ $isCheck== true ? "checked" : ""}}>
                                                <span></span>
                                                إدارة أنواع الأنشطة
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="checkbox checbox-switch switch-success">
                                            <label>
                                                @php
                                                    $isCheck = false;
                                                @endphp
                                                @foreach($user_permissions as $key => $value)
                                                    @if ($value['name'] == 'إضافة نوع نشاط')
                                                        @php
                                                            $isCheck = true;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                <input type="checkbox" name="switch"
                                                       wire:click="update_permission('إضافة نوع نشاط')"
                                                    {{ $isCheck== true ? "checked" : ""}}>
                                                <span></span>
                                                إضافة نوع نشاط
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="checkbox checbox-switch switch-success">
                                            <label>
                                                @php
                                                    $isCheck = false;
                                                @endphp
                                                @foreach($user_permissions as $key => $value)
                                                    @if ($value['name'] == 'تعديل نوع نشاط')
                                                        @php
                                                            $isCheck = true;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                <input type="checkbox" name="switch"
                                                       wire:click="update_permission('تعديل نوع نشاط')"
                                                    {{ $isCheck== true ? "checked" : ""}}>
                                                <span></span>
                                                تعديل نوع نشاط
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="tab-pane fade" :class="currentPermissionTab === 'activity-member' ? 'active show':'' "
                             id="activity-member-02"
                             role="tabpanel"
                             aria-labelledby="activity-member-02-tab">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="checkbox checbox-switch switch-success">
                                            <label>
                                                @php
                                                    $isCheck = false;
                                                @endphp
                                                @foreach($user_permissions as $key => $value)
                                                    @if ($value['name'] == 'إدارة أعضاء الأنشطة')
                                                        @php
                                                            $isCheck = true;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                <input type="checkbox" name="switch"
                                                       wire:click="update_permission('إدارة أعضاء الأنشطة')"
                                                    {{ $isCheck== true ? "checked" : ""}}>
                                                <span></span>
                                                إدارة أعضاء الأنشطة
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="checkbox checbox-switch switch-success">
                                            <label>
                                                @php
                                                    $isCheck = false;
                                                @endphp
                                                @foreach($user_permissions as $key => $value)
                                                    @if ($value['name'] == 'إضافة منشط')
                                                        @php
                                                            $isCheck = true;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                <input type="checkbox" name="switch"
                                                       wire:click="update_permission('إضافة منشط')"
                                                    {{ $isCheck== true ? "checked" : ""}}>
                                                <span></span>
                                                إضافة منشط
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" :class="currentPermissionTab === 'oversight-member' ? 'active show':'' "
                             id="oversight-member-02"
                             role="tabpanel"
                             aria-labelledby="oversight-member-02-tab">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="checkbox checbox-switch switch-success">
                                            <label>
                                                @php
                                                    $isCheck = false;
                                                @endphp
                                                @foreach($user_permissions as $key => $value)
                                                    @if ($value['name'] == 'إدارة أعضاء الرقابة')
                                                        @php
                                                            $isCheck = true;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                <input type="checkbox" name="switch"
                                                       wire:click="update_permission('إدارة أعضاء الرقابة')"
                                                    {{ $isCheck== true ? "checked" : ""}}>
                                                <span></span>
                                                إدارة أعضاء الرقابة
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="checkbox checbox-switch switch-success">
                                            <label>
                                                @php
                                                    $isCheck = false;
                                                @endphp
                                                @foreach($user_permissions as $key => $value)
                                                    @if ($value['name'] == 'إضافة مراقب')
                                                        @php
                                                            $isCheck = true;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                <input type="checkbox" name="switch"
                                                       wire:click="update_permission('إضافة مراقب')"
                                                    {{ $isCheck== true ? "checked" : ""}}>
                                                <span></span>
                                                إضافة مراقب
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" :class="currentPermissionTab === 'box' ? 'active show':'' "
                             id="box-02"
                             role="tabpanel"
                             aria-labelledby="box-02-tab">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="checkbox checbox-switch switch-success">
                                            <label>
                                                @php
                                                    $isCheck = false;
                                                @endphp
                                                @foreach($user_permissions as $key => $value)
                                                    @if ($value['name'] == 'إدارة صندوق الشكاوي والإقتراحات')
                                                        @php
                                                            $isCheck = true;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                <input type="checkbox" name="switch"
                                                       wire:click="update_permission('إدارة صندوق الشكاوي والإقتراحات')"
                                                    {{ $isCheck== true ? "checked" : ""}}>
                                                <span></span>
                                                إدارة صندوق الشكاوي والإقتراحات
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="checkbox checbox-switch switch-success">
                                            <label>
                                                @php
                                                    $isCheck = false;
                                                @endphp
                                                @foreach($user_permissions as $key => $value)
                                                    @if ($value['name'] == 'تقديم شكوى/اقتراح')
                                                        @php
                                                            $isCheck = true;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                <input type="checkbox" name="switch"
                                                       wire:click="update_permission('تقديم شكوى/اقتراح')"
                                                    {{ $isCheck== true ? "checked" : ""}}>
                                                <span></span>
                                                تقديم شكوى/اقتراح
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" :class="currentPermissionTab === 'punitive-measures' ? 'active show':'' "
                             id="punitive-measures-02"
                             role="tabpanel"
                             aria-labelledby="punitive-measures-02-tab">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="checkbox checbox-switch switch-success">
                                            <label>
                                                @php
                                                    $isCheck = false;
                                                @endphp
                                                @foreach($user_permissions as $key => $value)
                                                    @if ($value['name'] == 'إدارة الاجراءات العقابية')
                                                        @php
                                                            $isCheck = true;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                <input type="checkbox" name="switch"
                                                       wire:click="update_permission('إدارة الاجراءات العقابية')"
                                                    {{ $isCheck== true ? "checked" : ""}}>
                                                <span></span>
                                                إدارة الاجراءات العقابية
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="checkbox checbox-switch switch-success">
                                            <label>
                                                @php
                                                    $isCheck = false;
                                                @endphp
                                                @foreach($user_permissions as $key => $value)
                                                    @if ($value['name'] == 'إضافة اجراء عقابي')
                                                        @php
                                                            $isCheck = true;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                <input type="checkbox" name="switch"
                                                       wire:click="update_permission('إضافة اجراء عقابي')"
                                                    {{ $isCheck== true ? "checked" : ""}}>
                                                <span></span>
                                                إضافة اجراء عقابي
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="checkbox checbox-switch switch-success">
                                            <label>
                                                @php
                                                    $isCheck = false;
                                                @endphp
                                                @foreach($user_permissions as $key => $value)
                                                    @if ($value['name'] == 'تعديل اجراء عقابي')
                                                        @php
                                                            $isCheck = true;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                <input type="checkbox" name="switch"
                                                       wire:click="update_permission('تعديل اجراء عقابي')"
                                                    {{ $isCheck== true ? "checked" : ""}}>
                                                <span></span>
                                                تعديل اجراء عقابي
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="checkbox checbox-switch switch-success">
                                            <label>
                                                @php
                                                    $isCheck = false;
                                                @endphp
                                                @foreach($user_permissions as $key => $value)
                                                    @if ($value['name'] == 'حذف اجراء عقابي')
                                                        @php
                                                            $isCheck = true;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                <input type="checkbox" name="switch"
                                                       wire:click="update_permission('حذف اجراء عقابي')"
                                                    {{ $isCheck== true ? "checked" : ""}}>
                                                <span></span>
                                                حذف اجراء عقابي
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>


                        <div class="tab-pane fade" :class="currentPermissionTab === 'user' ? 'active show':'' "
                             id="user-02"
                             role="tabpanel"
                             aria-labelledby="user-02-tab">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="checkbox checbox-switch switch-success">
                                            <label>
                                                @php
                                                    $isCheck = false;
                                                @endphp
                                                @foreach($user_permissions as $key => $value)
                                                    @if ($value['name'] == 'إدارة المستخدمين')
                                                        @php
                                                            $isCheck = true;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                <input type="checkbox" name="switch"
                                                       wire:click="update_permission('إدارة المستخدمين')"
                                                    {{ $isCheck== true ? "checked" : ""}}>
                                                <span></span>
                                                إدارة المستخدمين
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="checkbox checbox-switch switch-success">
                                            <label>
                                                @php
                                                    $isCheck = false;
                                                @endphp
                                                @foreach($user_permissions as $key => $value)
                                                    @if ($value['name'] == 'إضافة مستخدم')
                                                        @php
                                                            $isCheck = true;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                <input type="checkbox" name="switch"
                                                       wire:click="update_permission('إضافة مستخدم')"
                                                    {{ $isCheck== true ? "checked" : ""}}>
                                                <span></span>
                                                إضافة مستخدم
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="checkbox checbox-switch switch-success">
                                            <label>
                                                @php
                                                    $isCheck = false;
                                                @endphp
                                                @foreach($user_permissions as $key => $value)
                                                    @if ($value['name'] == 'تعديل مستخدم')
                                                        @php
                                                            $isCheck = true;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                <input type="checkbox" name="switch"
                                                       wire:click="update_permission('تعديل مستخدم')"
                                                    {{ $isCheck== true ? "checked" : ""}}>
                                                <span></span>
                                                تعديل مستخدم
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="checkbox checbox-switch switch-success">
                                            <label>
                                                @php
                                                    $isCheck = false;
                                                @endphp
                                                @foreach($user_permissions as $key => $value)
                                                    @if ($value['name'] == 'حذف مستخدم')
                                                        @php
                                                            $isCheck = true;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                <input type="checkbox" name="switch"
                                                       wire:click="update_permission('حذف مستخدم')"
                                                    {{ $isCheck== true ? "checked" : ""}}>
                                                <span></span>
                                                حذف مستخدم
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="checkbox checbox-switch switch-success">
                                            <label>
                                                @php
                                                    $isCheck = false;
                                                @endphp
                                                @foreach($user_permissions as $key => $value)
                                                    @if ($value['name'] == 'إدارة الأدوار')
                                                        @php
                                                            $isCheck = true;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                <input type="checkbox" name="switch"
                                                       wire:click="update_permission('إدارة الأدوار')"
                                                    {{ $isCheck== true ? "checked" : ""}}>
                                                <span></span>
                                                إدارة الأدوار
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="checkbox checbox-switch switch-success">
                                            <label>
                                                @php
                                                    $isCheck = false;
                                                @endphp
                                                @foreach($user_permissions as $key => $value)
                                                    @if ($value['name'] == 'تعديل دور')
                                                        @php
                                                            $isCheck = true;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                <input type="checkbox" name="switch"
                                                       wire:click="update_permission('تعديل دور')"
                                                    {{ $isCheck== true ? "checked" : ""}}>
                                                <span></span>
                                                تعديل دور
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
