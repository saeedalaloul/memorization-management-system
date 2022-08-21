<div class="row">
    <div>
        @if ($catchError)
            <div class="alert alert-danger" id="success-danger">
                <button wire:click.prevent="resetMessage();" type="button" class="close" data-dismiss="alert">x</button>
                {{ $catchError }}
            </div>
        @endif
    </div>
    <div class="col-xl-12 mb-30">
        <div class="card card-statistics h-100">
            @if($current_role == \App\Models\User::ADMIN_ROLE || $current_role == \App\Models\User::EXAMS_SUPERVISOR_ROLE ||
                $current_role == \App\Models\User::TEACHER_ROLE || $current_role == \App\Models\User::TESTER_ROLE
                || $current_role == \App\Models\User::SUPERVISOR_ROLE)
                <div class="card-body">
                    <br>
                    @if ($isExamOfStart == true)
                        @include('pages.today_exams.exam_of_start')
                    @else
                        @can('إدارة اختبارات اليوم')
                            <x-search></x-search>
                            <div class="table-responsive mt-15">
                                <table class="table center-aligned-table mb-0">
                                    <thead>
                                    <tr class="text-dark table-success">
                                        <th wire:click="sortBy('id')" style="cursor: pointer;">#
                                            @include('livewire._sort-icon',['field'=>'id'])
                                        </th>
                                        <th>اسم الطالب</th>
                                        <th>جزء الإختبار</th>
                                        <th>اسم المحفظ</th>
                                        <th>اسم المختبر</th>
                                        <th>تاريخ الإختبار</th>
                                        <th>العمليات</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($exams_today as $exam_today)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $exam_today->student->user->name}}</td>
                                            <td>
                                                @if ($exam_today->type == \App\Models\ExamOrder::IMPROVEMENT_TYPE)
                                                    <label class="badge badge-success">
                                                        {{ $exam_today->QuranPart->name .' '.$exam_today->QuranPart->description . ' (طلب تحسين درجة)' }}
                                                    </label>
                                                @else
                                                    {{ $exam_today->QuranPart->name .' '.$exam_today->QuranPart->description }}
                                                @endif
                                            </td>
                                            <td>{{ $exam_today->teacher->user->name }}</td>
                                            <td>{{ $exam_today->tester->user->name }}</td>
                                            <td>{{ \Carbon\Carbon::parse($exam_today->datetime)->format('Y-m-d') }}</td>
                                            <td>
                                                @can('إجراء الإختبار')
                                                    @if($exam_today->status == \App\Models\ExamOrder::ACCEPTABLE_STATUS && $exam_today->teacher_id != auth()->id())
                                                        @if ($current_role == 'مختبر' ||$current_role == 'مشرف الإختبارات')
                                                            <button class="btn btn-outline-success btn-sm"
                                                                    wire:click.prevent="examOfStart('{{$exam_today->id}}')">
                                                                بدء إجراء الإختبار
                                                            </button>
                                                            <button class="btn btn-outline-danger btn-sm"
                                                                    data-toggle="modal"
                                                                    wire:click.prevent="getExamOrder('{{$exam_today->id}}')"
                                                                    data-target="#refusal-exam">الطالب لم يختبر
                                                            </button>
                                                        @endif
                                                    @endif
                                                @endcan
                                            </td>
                                        </tr>
                                        @include('pages.today_exams.exam_refusal')
                                    @empty
                                        <tr style="text-align: center">
                                            <td colspan="8">No data available in table</td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                    <tfoot>
                                    <tr class="text-dark table-success">
                                        <th>#</th>
                                        <th>اسم الطالب</th>
                                        <th>جزء الإختبار</th>
                                        <th>اسم المحفظ</th>
                                        <th>اسم المختبر</th>
                                        <th>تاريخ الإختبار</th>
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
                                            Showing {{$exams_today->firstItem()}} to {{$exams_today->lastItem()}}
                                            of {{$exams_today->total()}} entries
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-7">
                                        <div class="dataTables_paginate paging_simple_numbers"
                                             id="datatable_paginate">
                                            <ul class="pagination">
                                                {{$exams_today->links()}}
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endcan
                    @endif
                    @include('pages.today_exams.exam_question_count_select')
                </div>
            @endif
        </div>
    </div>
    <x-loading-indicator></x-loading-indicator>
</div>
