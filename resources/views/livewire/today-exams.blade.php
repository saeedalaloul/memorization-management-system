<div class="row">
    <x-loading-indicator/>
    <div>
        @if(Session::has('success_message'))
            <script>
                $(function () {
                    toastr.success("{{ Session::get('success_message') }}");
                })
            </script>
        @endif
        @if(Session::has('failure_message'))
            <script>
                $(function () {
                    toastr.error("{{ Session::get('failure_message') }}");
                })
            </script>
        @endif
    </div>
    <div class="col-xl-12 mb-30">
        <div class="card card-statistics h-100">
            @if(auth()->user()->current_role == 'أمير المركز' ||
                auth()->user()->current_role == 'مشرف الإختبارات' ||
                auth()->user()->current_role == 'محفظ' ||
                auth()->user()->current_role == 'مختبر')
                <div class="card-body">
                    <br>
                    @if ($isExamOfStart == true)
                        @include('pages.today_exams.exam_of_start')
                    @else
                        @can('إدارة اختبارات اليوم')
                            @include('livewire.search')
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
                                            <td>{{ $exam_today->student->user->name }}</td>
                                            <td>{{ $exam_today->QuranPart->name }}</td>
                                            <td>{{ $exam_today->teacher->user->name }}</td>
                                            <td>{{ $exam_today->tester->user->name }}</td>
                                            <td>{{ $exam_today->exam_date }}</td>
                                            <td>
                                                @can('إجراء الإختبار')
                                                    @if($exam_today->status == 2 && $exam_today->teacher_id != auth()->id())
                                                        @if (auth()->user()->current_role == 'مختبر' ||
                                                             auth()->user()->current_role == 'مشرف الإختبارات')
                                                            <button class="btn btn-outline-success btn-sm"
                                                                    data-toggle="modal"
                                                                    wire:click.prevent="examOfStart({{$exam_today->id}})"
                                                                    data-target="#">
                                                                بدء إجراء الإختبار
                                                            </button>
                                                            <button class="btn btn-outline-danger btn-sm"
                                                                    data-toggle="modal"
                                                                    wire:click.prevent="getExamOrder({{$exam_today->id}})"
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
</div>
