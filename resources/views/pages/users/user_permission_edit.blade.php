<div class="row">
    <div class="col-md-12 mb-30">
        <div class="card card-statistics h-100">
            <div class="card-body">
                <h5 class="card-title" style="font-size: 15px; color: #1e7e34">إدارة صلاحيات المستخدم ({{$name}})</h5>
                <div class="tab nav-border">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link {{$tab_id == "grade-02"? "active show":""}}" id="grade-02-tab"
                               data-toggle="tab" href="#grade-02"
                               role="tab" aria-controls="grade-02" wire:click.prevent="update_index_tab('grade-02');"
                               aria-selected="true">المراحل</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{$tab_id == "group-02"? "active show":""}}" id="group-02-tab"
                               data-toggle="tab" href="#group-02"
                               role="tab" aria-controls="group-02" wire:click.prevent="update_index_tab('group-02');"
                               aria-selected="false">المجموعات</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{$tab_id == "supervisor-02"? "active show":""}}" id="supervisor-02-tab"
                               data-toggle="tab" href="#supervisor-02"
                               role="tab" aria-controls="supervisor-02"
                               wire:click.prevent="update_index_tab('supervisor-02');"
                               aria-selected="false">مشرفي المراحل</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{$tab_id == "low-supervisor-02"? "active show":""}}"
                               id="low-supervisor-02-tab" data-toggle="tab" href="#low-supervisor-02"
                               role="tab" aria-controls="low-supervisor-02"
                               wire:click.prevent="update_index_tab('low-supervisor-02');"
                               aria-selected="false">إداريي المراحل</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{$tab_id == "teacher-02"? "active show":""}}" id="teacher-02-tab"
                               data-toggle="tab" href="#teacher-02"
                               role="tab" aria-controls="teacher-02"
                               wire:click.prevent="update_index_tab('teacher-02');"
                               aria-selected="false">المحفظين</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{$tab_id == "student-02"? "active show":""}}" id="student-02-tab"
                               data-toggle="tab" href="#student-02"
                               role="tab" aria-controls="student-02"
                               wire:click.prevent="update_index_tab('student-02');"
                               aria-selected="false">الطلاب</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{$tab_id == "exam-02"? "active show":""}}" id="exam-02-tab"
                               data-toggle="tab" href="#exam-02"
                               role="tab" aria-controls="exam-02" wire:click.prevent="update_index_tab('exam-02');"
                               aria-selected="false">الإختبارات</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{$tab_id == "order-exam-02"? "active show":""}}" id="order-exam-02-tab"
                               data-toggle="tab" href="#order-exam-02"
                               role="tab" aria-controls="order-exam-02"
                               wire:click.prevent="update_index_tab('order-exam-02');"
                               aria-selected="false">طلبات الإختبارات</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{$tab_id == "tester-02"? "active show":""}}" id="tester-02-tab"
                               data-toggle="tab" href="#tester-02"
                               role="tab" aria-controls="tester-02" wire:click.prevent="update_index_tab('tester-02');"
                               aria-selected="false">المختبرين</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade {{$tab_id == "grade-02"? "active show":""}}" id="grade-02"
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

                        <div class="tab-pane fade {{$tab_id == "group-02"? "active show":""}}" id="group-02"
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
                            </div>
                        </div>

                        <div class="tab-pane fade {{$tab_id == "supervisor-02"? "active show":""}}" id="supervisor-02"
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
                        <div class="tab-pane fade {{$tab_id == "low-supervisor-02"? "active show":""}}"
                             id="low-supervisor-02" role="tabpanel"
                             aria-labelledby="low-supervisor-02-tab">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="checkbox checbox-switch switch-success">
                                            <label>
                                                @php
                                                    $isCheck = false;
                                                @endphp
                                                @foreach($user_permissions as $key => $value)
                                                    @if ($value['name'] == 'إدارة الإداريين')
                                                        @php
                                                            $isCheck = true;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                <input type="checkbox" name="switch"
                                                       wire:click="update_permission('إدارة الإداريين')"
                                                    {{ $isCheck== true ? "checked" : ""}}>
                                                <span></span>
                                                إدارة الإداريين
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
                                                    @if ($value['name'] == 'إضافة إداري')
                                                        @php
                                                            $isCheck = true;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                <input type="checkbox" name="switch"
                                                       wire:click="update_permission('إضافة إداري')"
                                                    {{ $isCheck== true ? "checked" : ""}}>
                                                <span></span>
                                                إضافة إداري
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
                                                    @if ($value['name'] == 'تعديل إداري')
                                                        @php
                                                            $isCheck = true;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                <input type="checkbox" name="switch"
                                                       wire:click="update_permission('تعديل إداري')"
                                                    {{ $isCheck== true ? "checked" : ""}}>
                                                <span></span>
                                                تعديل إداري
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
                                                    @if ($value['name'] == 'حذف إداري')
                                                        @php
                                                            $isCheck = true;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                <input type="checkbox" name="switch"
                                                       wire:click="update_permission('حذف إداري')"
                                                    {{ $isCheck== true ? "checked" : ""}}>
                                                <span></span>
                                                حذف إداري
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade {{$tab_id == "teacher-02"? "active show":""}}" id="teacher-02"
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
                        <div class="tab-pane fade {{$tab_id == "student-02"? "active show":""}}" id="student-02"
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
                        </div>

                        <div class="tab-pane fade {{$tab_id == "exam-02"? "active show":""}}" id="exam-02"
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
                                                    @if ($value['name'] == 'تعديل اختبار')
                                                        @php
                                                            $isCheck = true;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                <input type="checkbox" name="switch"
                                                       wire:click="update_permission('تعديل اختبار')"
                                                    {{ $isCheck== true ? "checked" : ""}}>
                                                <span></span>
                                                تعديل اختبار
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
                                                    @if ($value['name'] == 'حذف اختبار')
                                                        @php
                                                            $isCheck = true;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                <input type="checkbox" name="switch"
                                                       wire:click="update_permission('حذف اختبار')"
                                                    {{ $isCheck== true ? "checked" : ""}}>
                                                <span></span>
                                                حذف اختبار
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
                        <div class="tab-pane fade {{$tab_id == "order-exam-02"? "active show":""}}" id="order-exam-02"
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
                                                    @if ($value['name'] == 'إدارة طلبات اختبارات التجميعي')
                                                        @php
                                                            $isCheck = true;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                <input type="checkbox" name="switch"
                                                       wire:click="update_permission('إدارة طلبات اختبارات التجميعي')"
                                                    {{ $isCheck== true ? "checked" : ""}}>
                                                <span></span>
                                                إدارة طلبات اختبارات التجميعي
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

                        <div class="tab-pane fade {{$tab_id == "tester-02"? "active show":""}}" id="tester-02"
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
