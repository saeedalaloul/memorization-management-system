<div class="card-body">

    <li class="list-group-item">
        <div class="row">
            <div class="col-md-3">
                <label for="student" style="font-size: 15px; color: #1e7e34">نوع الزيارة*</label>
                <select style="width: 100%;" wire:model="selectedVisitTypeId"
                        class="custom-select mr-sm-2"
                        name="selectedStatus">
                    <option value="" selected>جميع الأنواع</option>
                    <option value="App\Models\Teacher">زيارة إلى حلقة</option>
                    <option value="App\Models\Tester">زيارة إلى مختبر</option>
                    <option value="App\Models\ActivityMember">زيارة إلى نشاط</option>
                    <option value="">زيارة إلى دورة</option>
                </select>
            </div>

            @if($current_role === \App\Models\User::OVERSIGHT_SUPERVISOR_ROLE || $current_role === \App\Models\User::ADMIN_ROLE || $current_role === \App\Models\User::OVERSIGHT_MEMBER_ROLE)
                <div class="col-md-3">
                    <label for="student" style="font-size: 15px; color: #1e7e34">حالة الزيارة*</label>
                    <select style="width: 100%;" wire:model="selectedStatusId"
                            class="custom-select mr-sm-2"
                            name="selectedStatus">
                        <option value="" selected>جميع الحالات</option>
                        @foreach(\App\Models\Visit::status() as $key => $value )
                            <option value="{{$key}}">{{$value}}</option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>
    </li>
</div>
<x-search></x-search>
<div class="table-responsive mt-15">
    <table class="table center-aligned-table mb-0">
        <thead>
        <tr class="text-dark table-success">
            <th wire:click="sortBy('id')" style="cursor: pointer;">#
                @include('livewire._sort-icon',['field'=>'id'])
            </th>
            <th>اسم المراقب</th>
            <th>نوع الزيارة</th>
            <th>تاريخ الزيارة</th>
            <th>حالة الزيارة</th>
            <th>العمليات</th>
        </tr>
        </thead>
        <tbody>
        @forelse($visits as $visit)
            @php
                $selectClass = '';
                    if (isset($visit) && $visit->visit_processing_reminder !== null) {
                        $val = \Carbon\Carbon::parse($visit->visit_processing_reminder->reminder_datetime)->format('Y-m-d') < date('Y-m-d', time());
                        $val === true ? $selectClass = 'text-dark table-danger': $selectClass ='';
                    }
            @endphp
            <tr class="{{$selectClass}}">
                <td>{{ $loop->iteration }}</td>
                <td>{{ $visit->oversight_member->user->name }}</td>
                <td>
                    @if($visit->hostable_type === 'App\Models\Teacher')
                        زيارة إلى حلقة
                    @elseif($visit->hostable_type === 'App\Models\Tester')
                        زيارة إلى مختبر
                    @elseif($visit->hostable_type === 'App\Models\ActivityMember')
                        زيارة إلى منشط
                    @endif
                </td>
                <td>{{ \Carbon\Carbon::parse($visit->datetime)->format('Y-m-d') }}</td>
                <td>
                    @if($visit->status === \App\Models\Visit::IN_PENDING_STATUS)
                        <label class="badge badge-warning">مطلوب الرد</label>
                    @elseif($visit->status === \App\Models\Visit::REPLIED_STATUS)
                        <label class="badge badge-info">تم الرد</label>
                    @elseif($visit->status === \App\Models\Visit::IN_PROCESS_STATUS)
                        <label class="badge badge-primary">في انتظار المعالجة</label>
                    @elseif($visit->status === \App\Models\Visit::FAILURE_STATUS)
                        <label class="badge badge-danger">فشل المعالجة</label>
                    @elseif($visit->status === \App\Models\Visit::SOLVED_STATUS)
                        <label class="badge badge-success">تم الحل</label>
                    @endif
                </td>
                <td>
                    @if (isset($val) && isset($visit->visit_processing_reminder))
                        @if ($val)
                            <div hidden wire:click=""></div>
                            @if ($visit->status === \App\Models\Visit::REPLIED_STATUS)
                                <button type="button" class="btn btn-outline-danger btn-sm"
                                        wire:click.prevent="sendMessage(' لقد تم الرد من قبل أمير المركز مرة أخرى على الحالة السابقة للزيارة جاري المعالجة .. حيث تاريخ {{\Carbon\Carbon::parse($visit->visit_processing_reminder->reminder_datetime)->format('Y-m-d')}} هو التاريخ الذي تم تعيينه لمعالجة الزيارة ف يرجي مراجعة رد أمير المركز واتخاذ قرار بشأن ذلك!');"
                                        data-toggle="popover" data-trigger="focus" title="تاريخ معالجة الزيارة"
                                        data-content=" لقد تم الرد من قبل أمير المركز مرة أخرى على الحالة السابقة للزيارة جاري المعالجة .. حيث تاريخ {{\Carbon\Carbon::parse($visit->visit_processing_reminder->reminder_datetime)->format('Y-m-d')}} هو التاريخ الذي تم تعيينه لمعالجة الزيارة ف يرجي مراجعة رد أمير المركز واتخاذ قرار بشأن ذلك!">
                                    اضغط هنا
                                </button>
                            @else
                                <button type="button" class="btn btn-outline-danger btn-sm"
                                        wire:click.prevent="sendMessage(' لقد فات تاريخ {{\Carbon\Carbon::parse($visit->visit_processing_reminder->reminder_datetime)->format('Y-m-d')}} لمعالجة الزيارة الحالية لذلك نرجو حل المشكلة أو تعيين تاريخ جديد!');"
                                        data-toggle="popover" data-trigger="focus" title="تاريخ معالجة الزيارة"
                                        data-content=" لقد فات تاريخ {{\Carbon\Carbon::parse($visit->visit_processing_reminder->reminder_datetime)->format('Y-m-d')}} لمعالجة الزيارة الحالية لذلك نرجو حل المشكلة أو تعيين تاريخ جديد!">
                                    اضغط هنا
                                </button>
                            @endif
                        @else
                            <div hidden wire:click=""></div>
                            @if ($visit->status === \App\Models\Visit::REPLIED_STATUS)
                                <button type="button" class="btn btn-outline-danger btn-sm"
                                        wire:click.prevent="sendMessage(' لقد تم الرد من قبل أمير المركز مرة أخرى على الحالة السابقة للزيارة جاري المعالجة .. حيث تاريخ {{\Carbon\Carbon::parse($visit->visit_processing_reminder->reminder_datetime)->format('Y-m-d')}} هو التاريخ الذي تم تعيينه لمعالجة الزيارة ف يرجي مراجعة رد أمير المركز واتخاذ قرار بشأن ذلك!');"
                                        data-toggle="popover" data-trigger="focus" title="تاريخ معالجة الزيارة"
                                        data-content=" لقد تم الرد من قبل أمير المركز مرة أخرى على الحالة السابقة للزيارة جاري المعالجة .. حيث تاريخ {{\Carbon\Carbon::parse($visit->visit_processing_reminder->reminder_datetime)->format('Y-m-d')}} هو التاريخ الذي تم تعيينه لمعالجة الزيارة ف يرجي مراجعة رد أمير المركز واتخاذ قرار بشأن ذلك!">
                                    اضغط هنا
                                </button>
                            @else
                                <div hidden wire:click=""></div>
                                <button type="button" class="btn btn-outline-danger btn-sm"
                                        wire:click.prevent="sendMessage(' لقد تم تعيين تاريخ {{\Carbon\Carbon::parse($visit->visit_processing_reminder->reminder_datetime)->format('Y-m-d')}} لمعالجة الزيارة!');"
                                        data-toggle="popover" data-trigger="focus" title="تاريخ معالجة الزيارة"
                                        data-content=" لقد تم تعيين تاريخ {{\Carbon\Carbon::parse($visit->visit_processing_reminder->reminder_datetime)->format('Y-m-d')}} لمعالجة الزيارة!">
                                    اضغط هنا
                                </button>
                            @endif
                        @endif
                    @endif
                    @if ($current_role === \App\Models\User::OVERSIGHT_MEMBER_ROLE)
                        <div hidden wire:click=""></div>
                        <button type="button" class="btn btn-success btn-sm"
                                @click.prevent="currentTab = 'form'"
                                wire:click="visitDetailsShow('{{$visit->id}}');"
                                title="عرض تفاصيل الزيارة">
                            <i class="fa fa-eye"></i></button>
                    @elseif($current_role === \App\Models\User::OVERSIGHT_SUPERVISOR_ROLE)
                        <div hidden wire:click=""></div>
                        <button type="button" class="btn btn-info btn-sm"
                                @click.prevent="currentTab = 'form'"
                                wire:click="visitDetailsShow('{{$visit->id}}');"
                                title="عرض تفاصيل الزيارة">
                            <i class="fa fa-eye"></i></button>

                        @if($visit->status === \App\Models\Visit::REPLIED_STATUS  || $visit->status === \App\Models\Visit::IN_PROCESS_STATUS)
                            <button type="button" class="btn btn-success btn-sm"
                                    wire:click="visitSolved('{{$visit->id}}');"
                                    title="تم الحل">
                                <i class="fa fa-check"></i></button>

                            <button type="button" class="btn btn-primary btn-sm"
                                    data-toggle="modal"
                                    data-target="#visit-processing"
                                    wire:click="lunchModalVisitProcessing('{{$visit->id}}');"
                                    title="جاري المعالجة">
                                <i class="fas fa-alarm-clock"></i></button>

                            <button type="button" class="btn btn-danger btn-sm"
                                    wire:click="visitFailed('{{$visit->id}}');"
                                    title="فشل المعالجة">
                                <i class="fa fa-close"></i></button>
                        @endif

                        @if($visit->status === \App\Models\Visit::FAILURE_STATUS)
                            <button type="button" class="btn btn-success btn-sm"
                                    wire:click="visitSolved('{{$visit->id}}');"
                                    title="تم الحل">
                                <i class="fa fa-check"></i></button>
                        @endif
                    @elseif($current_role === \App\Models\User::ADMIN_ROLE)
                        <div hidden wire:click=""></div>
                        <button type="button" class="btn btn-success btn-sm"
                                @click.prevent="currentTab = 'form'"
                                wire:click="visitDetailsShow('{{$visit->id}}');"
                                title="عرض تفاصيل الزيارة">
                            <i class="fa fa-eye"></i></button>
                        @if ($visit->status === \App\Models\Visit::IN_PENDING_STATUS)
                            <button type="button" class="btn btn-warning btn-sm"
                                    @click.prevent="currentTab = 'form'"
                                    wire:click="visitReply('{{$visit->id}}');"
                                    title="الرد على الزيارة">
                                <i class="fa fa-reply"></i></button>
                        @elseif($visit->status === \App\Models\Visit::IN_PROCESS_STATUS)
                            <button type="button" class="btn btn-warning btn-sm"
                                    @click.prevent="currentTab = 'form'"
                                    wire:click="visitReply('{{$visit->id}}');"
                                    title="الرد على المعالجة">
                                <i class="fa fa-reply"></i></button>
                        @endif
                    @endif
                </td>
            </tr>
            @include('pages.visits.visit_processing')
            @if ($visibleDetailsModalId !== null && $visibleDetailsModalId === $visit->id)
                @if($visit->hostable_type === 'App\Models\Teacher')
                    <tr class="fold">
                        <td colspan="7">
                            <div class="fold-content">
                                <h3>تفاصيل الحلقة</h3>
                                <table>
                                    <thead>
                                    <tr class="text-dark table-success">
                                        <th>#</th>
                                        <th>اسم الحلقة</th>
                                        <th>اسم المرحلة</th>
                                        <th>اسم المحفظ</th>
                                        <th>عدد طلاب الحلقة</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        @php
                                            if (isset($visit)) {
                                            $teacher =  $visit->hostable()->first();
                                            }
                                        @endphp
                                        <td>1</td>
                                        <td>{{$teacher->group->name }}</td>
                                        <td>{{ $teacher->group->grade->name }}</td>
                                        <td>
                                            @if ($teacher->group !== null)
                                                {{ $teacher->user->name }}
                                            @else
                                                لا يوجد محفظ
                                            @endif
                                        </td>
                                        <td>{{ $teacher->group->students->count() }}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    </tr>
                @elseif($visit->hostable_type === 'App\Models\Tester')
                    <tr class="fold">
                        <td colspan="7">
                            <div class="fold-content">
                                <h3>تفاصيل اختبارات المختبر</h3>
                                <table>
                                    <thead>
                                    <tr class="text-dark table-success">
                                        <th>#</th>
                                        <th>اسم الطالب</th>
                                        <th>جزء الإختبار</th>
                                        <th>تاريخ الإختبار</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php
                                        if (isset($visit)) {
                                        $exams =  $visit->hostable()->first()->exams->whereDate('datetime',\Carbon\Carbon::parse($visit->datetime)->format('Y-m-d'));
                                        }
                                    @endphp
                                    @forelse($exams as $exam)
                                        <tr>
                                            <td>{{$loop->iteration}}</td>
                                            <td>{{$exam->student->user->name}}</td>
                                            <td>{{$exam->quranPart->name}}</td>
                                            <td>{{\Carbon\Carbon::parse($exam->datetime)->format('Y-m-d')}}</td>
                                        </tr>
                                    @empty
                                        <tr style="text-align: center">
                                            <td colspan="4">No data available in table</td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    </tr>
                @elseif($visit->hostable_type === 'App\Models\ActivityMember')
                    <tr class="fold">
                        <td colspan="7">
                            <div class="fold-content">
                                <h3>تفاصيل أنشطة المنشط</h3>
                                <table>
                                    <thead>
                                    <tr class="text-dark table-success">
                                        <th>#</th>
                                        <th>اسم النشاط</th>
                                        <th>عدد الطلاب</th>
                                        <th>الوقت</th>
                                        <th>اسم المحفظ</th>
                                        <th>اسم المنشط</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php
                                        if (isset($visit)) {
                                        $activities =  $visit->hostable()->first()->activities
                                        ->whereDate('datetime',\Carbon\Carbon::parse($visit->datetime)
                                        ->format('Y-m-d'))->with(['activity_type','teacher.user:id,name','activity_member.user:id,name'])
                                        ->withCount(['students']);
                                        }
                                    @endphp
                                    @forelse($activities as $activity)
                                        <tr>
                                            <td>{{$loop->iteration}}</td>
                                            <td>{{$activity->activity_type->name}}</td>
                                            <td>{{$activity->students_count}}</td>
                                            <td>{{\Carbon\Carbon::parse($activity->activity_date)->translatedFormat('l j F Y h:i a')}}</td>
                                            <td>{{$activity->teacher->user->name}}</td>
                                            <td>
                                                @if(isset($activity->activity_member))
                                                    {{$activity->activity_member->user->name}}
                                                @else
                                                    لم يتم اعتماد منشط
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr style="text-align: center">
                                            <td colspan="4">No data available in table</td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    </tr>
                @endif
            @endif
        @empty
            <tr style="text-align: center">
                <td colspan="6">No data available in table</td>
            </tr>
        @endforelse
        </tbody>
        <tfoot>
        <tr class="text-dark table-success">
            <th>#</th>
            <th>اسم المراقب</th>
            <th>نوع الزيارة</th>
            <th>تاريخ الزيارة</th>
            <th>حالة الزيارة</th>
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
                Showing {{$visits->firstItem()}} to {{$visits->lastItem()}}
                of {{$visits->total()}} entries
            </div>
        </div>
        <div class="col-sm-12 col-md-7">
            <div class="dataTables_paginate paging_simple_numbers"
                 id="datatable_paginate">
                <ul class="pagination">
                    {{$visits->links()}}
                </ul>
            </div>
        </div>
    </div>
    <x-loading-indicator></x-loading-indicator>
</div>
