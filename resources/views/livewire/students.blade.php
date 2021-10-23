<div>
    @if (!empty($successMessage))
        <div class="alert alert-success" id="success-alert">
            <button type="button" class="close" data-dismiss="alert">x</button>
            {{ $successMessage }}
        </div>
    @endif

    @if ($catchError)
        <div class="alert alert-danger" id="success-danger">
            <button type="button" class="close" data-dismiss="alert">x</button>
            {{ $catchError }}
        </div>
    @endif
    <div>
        @if(Session::has('message'))
            <script>
                $(function () {
                    toastr.success("{{ Session::get('message') }}");
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
            <div class="card-body">
                <h5 class="card-title">إدارة الطلاب</h5>
                <div class="tab tab-border">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link {{$show_table == true ? 'active show':''}}" href="#" id="students-05-tab"
                               data-bs-toggle="tab" role="tab" wire:click="showformadd(true);"
                               aria-controls="students-05" aria-selected="true"> <i class="fa fa-users"></i> قائمة
                                الطلاب</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{$show_table == false ? 'active show':''}}" id="add_student-05-tab"
                               data-bs-toggle="tab" role="tab"
                               wire:click="showformadd(false);" href="#"
                               aria-controls="add_student-05" aria-selected="false"><i
                                    class="fas {{!empty($updateMode) ? 'fa-user-edit' : 'fa-user-plus'}}"></i>
                                {{!empty($updateMode) ? ' تحديث طالب' : ' إضافة طالب'}}</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade {{$show_table == true ? 'active show':''}}" id="students-05"
                             role="tabpanel"
                             aria-labelledby="students-05-tab">
                            @include('pages.students.Student_Table')
                        </div>
                        <div class="tab-pane fade {{$show_table == false ? 'active show':''}}" id="add_student-05"
                             role="tabpanel"
                             aria-labelledby="add_student-05-tab">
                            <div class="stepwizard">
                                <div class="stepwizard-row setup-panel">
                                    <div class="stepwizard-step">
                                        <a href="#step-1" type="button"
                                           class="btn btn-circle {{ $currentStep != 1 ? 'btn-default' : 'btn-success' }}">1</a>
                                        <p>معلومات الأب</p>
                                    </div>
                                    <div class="stepwizard-step">
                                        <a href="#step-2" type="button"
                                           class="btn btn-circle {{ $currentStep != 2 ? 'btn-default' : 'btn-success' }}">2</a>
                                        <p>معلومات الطالب</p>
                                    </div>
                                    <div class="stepwizard-step">
                                        <a href="#step-3" type="button"
                                           class="btn btn-circle {{ $currentStep != 3 ? 'btn-default' : 'btn-success' }}"
                                           disabled="disabled">3</a>
                                        <p>تأكيد المعلومات</p>
                                    </div>
                                </div>
                            </div>

                            @include('pages.students.Father_Form')

                            @include('pages.students.Student_Form')

                            @include('pages.students.Student_Last_Form')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
