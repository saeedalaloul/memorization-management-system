<div class="row">
    @if ($current_role === \App\Models\User::ADMIN_ROLE || $current_role === \App\Models\User::SUPERVISOR_ROLE
          || $current_role === \App\Models\User::SPONSORSHIP_SUPERVISORS_ROLE)
        <div class="col-xl-12 mb-30">
            <div>
                @if ($catchError)
                    <div class="alert alert-danger" id="success-danger">
                        <button type="button" wire:click.prevent="resetMessage()" class="close" data-dismiss="alert">x
                        </button>
                        {{ $catchError }}
                    </div>
                @endif
            </div>
            <div class="card card-statistics h-100">
                <div class="card-body" x-data="{currentTab: $persist('home')}">
                    <h5 class="card-title">إدارة الحلقات</h5>
                    <div class="tab tab-border">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item" @click.prevent="currentTab = 'home'">
                                <a class="nav-link" href="#" id="groups-05-tab"
                                   data-bs-toggle="tab" role="tab" :class="currentTab === 'home' ? 'active show':'' "
                                   aria-controls="groups-05" aria-selected="true"> <i class="fa fa-group"></i> قائمة
                                    الحلقات</a>
                            </li>
                            <li class="nav-item" @click.prevent="currentTab = 'form'">
                                <a class="nav-link" id="add_group-05-tab"
                                   :class="currentTab === 'form' ? 'active show':'' "
                                   data-bs-toggle="tab" role="tab" href="#"
                                   aria-controls="add_group-05" aria-selected="false"><i
                                        class="fas {{!empty($modalId) ? 'fa-edit' : 'fa-plus-square'}}"></i>
                                    @if(!empty($modalId))
                                        @if ($is_moving)
                                            نقل حلقة
                                        @else
                                            تحديث حلقة
                                        @endif
                                    @else
                                        إضافة حلقة
                                    @endif
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade" :class="currentTab === 'home' ? 'active show':'' " id="groups-05"
                                 role="tabpanel"
                                 aria-labelledby="groups-05-tab">
                                <div class="card-body">
                                    @can('إدارة المجموعات')
                                        <li class="list-group-item">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <label style="font-size: 15px; color: #1e7e34">المراحل*</label>
                                                    <select style="width: 100%;" id="grade"
                                                            wire:model.defer="selectedGradeId"
                                                            class="custom-select mr-sm-2 select2">
                                                        <option value="">الكل</option>
                                                        @foreach ($grades as $grade)
                                                            <option
                                                                value="{{ $grade->id }}">{{ $grade->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-md-3">
                                                    <label style="font-size: 15px; color: #1e7e34">نوع الحلقة*</label>
                                                    <select style="width: 100%;" id="type"
                                                            wire:model.defer="selectedType"
                                                            class="custom-select mr-sm-2 select2">
                                                        <option value="">الكل</option>
                                                        @foreach (\App\Models\Group::types() as $type => $value)
                                                            <option
                                                                value="{{ $type }}">{{ $value}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                @if ($current_role === \App\Models\User::ADMIN_ROLE || $current_role === \App\Models\User::SUPERVISOR_ROLE)
                                                    <div class="col-md-3">
                                                        <label style="font-size: 15px; color: #1e7e34">نوع
                                                            الكفالة*</label>
                                                        <select style="width: 100%;" id="sponsorship"
                                                                wire:model.defer="selectedSponsorshipId"
                                                                class="custom-select mr-sm-2 select2">
                                                            <option value="">الكل</option>
                                                            @foreach ($sponsorships as $sponsorship)
                                                                <option
                                                                    value="{{ $sponsorship->id }}">{{ $sponsorship->name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                @endif

                                                @if($current_role == 'مشرف')
                                                    <div class="col-md-2">
                                                        <button class="btn btn-success float-right"
                                                                wire:click.prevent="grade_students_export();">تصدير
                                                            بيانات
                                                            طلاب المرحلة
                                                        </button>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <button class="btn btn-success float-right"
                                                                wire:click.prevent="all_teachers_export();">تصدير بيانات
                                                            محفظي المرحلة
                                                        </button>
                                                    </div>
                                                @endif

                                                @if($current_role == \App\Models\User::SPONSORSHIP_SUPERVISORS_ROLE)
                                                    <div class="col-md-2">
                                                        <button class="btn btn-success float-right"
                                                                wire:click.prevent="grade_students_export();">تصدير
                                                            بيانات الطلاب
                                                        </button>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <button class="btn btn-success float-right"
                                                                wire:click.prevent="all_teachers_export();">تصدير بيانات
                                                            المحفظين
                                                        </button>
                                                    </div>
                                                @endif
                                            </div>
                                        </li>
                                        <br>
                                        <x-search></x-search>
                                        <div class="table-responsive mt-15">
                                            <table class="table center-aligned-table mb-0">
                                                <thead>
                                                <tr class="text-dark table-success">
                                                    <th wire:click="sortBy('id')" style="cursor: pointer;">#
                                                        @include('livewire._sort-icon',['field'=>'id'])
                                                    </th>
                                                    <th wire:click="sortBy('name')" style="cursor: pointer;">اسم الحلقة
                                                        @include('livewire._sort-icon',['field'=>'name'])
                                                    </th>
                                                    <th>اسم المرحلة</th>
                                                    <th>اسم المحفظ</th>
                                                    <th>نوع الحلقة</th>
                                                    <th>نوع الكفالة</th>
                                                    <th>عدد طلاب الحلقة</th>
                                                    <th>العمليات</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @forelse($groups as $group)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $group->name }}</td>
                                                        <td>{{ $group->grade->name }}</td>
                                                        <td>
                                                            @if ($group->teacher_id != null)
                                                                {{ $group->teacher->user->name }}
                                                            @else
                                                                <label class="badge badge-danger">لا يوجد محفظ</label>
                                                            @endif
                                                        </td>
                                                        <td>{{ \App\Models\Group::types()[$group->type] }}</td>
                                                        <td>
                                                            @if(count($group->sponsorships) === 0)
                                                                <label class="badge badge-danger">غير مكفولة</label>
                                                            @else
                                                                @foreach($group->sponsorships as $sponsorship)
                                                                    <label
                                                                        class="badge badge-success">{{ $sponsorship->name }}</label>
                                                                @endforeach
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($group->type == \App\Models\Group::SUNNAH_TYPE)
                                                                {{ $group->students_sunnah_count }}
                                                            @else
                                                                {{ $group->students_count }}
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($group->students_count > 0 || $group->students_sunnah_count > 0)
                                                                <button type="button"
                                                                        wire:click.prevent="export('{{$group->id}}','{{$group->type}}');"
                                                                        class="btn btn-success btn-sm"
                                                                        title="تصدير بيانات الطلاب"><i
                                                                        class="fa fa-download"></i></button>
                                                            @endif
                                                            @can('تعديل مجموعة')
                                                                <button type="button" class="btn btn-info btn-sm"
                                                                        @click.prevent="currentTab = 'form'"
                                                                        wire:click.prevent="loadModalData('{{$group->id}}',false)"
                                                                        title="تعديل"><i class="fa fa-edit"></i>
                                                                </button>
                                                            @endcan
                                                            @if ($current_role == \App\Models\User::ADMIN_ROLE)
                                                                @if($group->type == \App\Models\Group::QURAN_TYPE)
                                                                    @can('نقل مجموعة')
                                                                        <button type="button"
                                                                                class="btn btn-primary btn-sm"
                                                                                @click.prevent="currentTab = 'form'"
                                                                                wire:click.prevent="loadModalData('{{$group->id}}',true)"
                                                                                title="نقل الحلقة"><i
                                                                                class="fa fa-cut"></i>
                                                                        </button>
                                                                    @endcan
                                                                @endif
                                                            @endif
                                                            @can('سحب محفظ من مجموعة')
                                                                @if ($group->teacher_id != null)
                                                                    <button type="button" class="btn btn-warning btn-sm"
                                                                            wire:click.prevent="lunchBoxOfModalData('pull','{{$group->id}}',{{$group->teacher_id}})"
                                                                            title="سحب المحفظ من الحلقة"><i
                                                                            class="fa fa-remove"></i></button>
                                                                @endif
                                                            @endcan
                                                            @can('حذف مجموعة')
                                                                <button type="button" class="btn btn-danger btn-sm"
                                                                        wire:click.prevent="lunchBoxOfModalData('delete','{{$group->id}}','')"
                                                                        title="حذف"><i
                                                                        class="fa fa-trash"></i></button>
                                                            @endcan
                                                        </td>
                                                    </tr>
                                                    @include('pages.groups.pull_teacher')
                                                    @include('pages.groups.delete')
                                                @empty
                                                    <tr style="text-align: center">
                                                        <td colspan="6">No data available in table</td>
                                                    </tr>
                                                @endforelse

                                                </tbody>
                                                <tfoot>
                                                <tr class="text-dark table-success">
                                                    <th>#</th>
                                                    <th>اسم الحلقة</th>
                                                    <th>اسم المرحلة</th>
                                                    <th>اسم المحفظ</th>
                                                    <th>نوع الحلقة</th>
                                                    <th>نوع الكفالة</th>
                                                    <th>عدد طلاب الحلقة</th>
                                                    <th>العمليات</th>
                                                </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                        <div id="datatable_wrapper"
                                             class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">
                                            <div class="row">
                                                <div class="col-sm-12 col-md-5">
                                                    <div class="dataTables_info" id="datatable_info" role="status"
                                                         aria-live="polite">
                                                        Showing {{$groups->firstItem()}} to {{$groups->lastItem()}}
                                                        of {{$groups->total()}} entries
                                                    </div>
                                                </div>
                                                <div class="col-sm-12 col-md-7">
                                                    <div class="dataTables_paginate paging_simple_numbers"
                                                         id="datatable_paginate">
                                                        <ul class="pagination">
                                                            {{$groups->links()}}
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endcan
                                </div>
                            </div>
                            <div class="tab-pane fade" :class="currentTab === 'form' ? 'active show':'' "
                                 id="add_group-05"
                                 role="tabpanel"
                                 aria-labelledby="add_group-05-tab">
                                @if ($is_moving)
                                    @include('pages.groups.group_move')
                                    @include('pages.groups.move_warning')
                                @else
                                    @can('إضافة مجموعة')
                                        @include('pages.groups.group_form')
                                    @endcan
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <x-loading-indicator></x-loading-indicator>
    @endif
</div>
@push('alpine-plugins')
    <!-- Alpine Plugins -->
    <script defer src="https://unpkg.com/@alpinejs/persist@3.x.x/dist/cdn.min.js"></script>
@endpush
@push('js')
    <script>

        $("#grade").on('change', function (e) {
            let id = $(this).val()
        @this.set('selectedGradeId', id);
            livewire.emit('getGroupsByGradeId', id);
        });

        $("#type").on('change', function (e) {
            let id = $(this).val()
        @this.set('selectedType', id);
            livewire.emit('getGroupsByGradeId', id);
        });

        $("#grade_").on('change', function (e) {
            let id = $(this).val()
        @this.set('grade_id', id);
            livewire.emit('getTeachersByGradeId', id);
        });

        $("#teacher_").on('change', function (e) {
            let id = $(this).val()
        @this.set('teacher_id', id);
        });

        $("#type_").on('change', function (e) {
            let id = $(this).val()
        @this.set('type', id);
        });

        $("#sponsorship").on('change', function (e) {
            let id = $(this).val()
        @this.set('selectedSponsorshipId', id);
            livewire.emit('getGroupsByGradeId');
        });
    </script>
@endpush
