<div>
    <div>
        @if (!empty($successMessage))
            <div class="alert alert-success" id="success-alert">
                <button wire:click.prevent="resetMessage();" type="button" class="close" data-dismiss="alert">x</button>
                {{ $successMessage }}
            </div>
        @endif

        @if ($catchError)
            <div class="alert alert-danger" id="success-danger">
                <button wire:click.prevent="resetMessage();" type="button" class="close" data-dismiss="alert">x</button>
                {{ $catchError }}
            </div>
        @endif
    </div>

    <div class="col-xl-12 mb-30">
        <div class="card card-statistics h-100">
            <div class="card-body" x-data="{currentTab: $persist('home')}">
                <h5 class="card-title">إدارة الطلاب</h5>
                <div class="tab tab-border">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item" @click.prevent="currentTab = 'home'">
                            <a class="nav-link" :class="currentTab === 'home' ? 'active show':'' " href="#"
                               id="students-05-tab"
                               data-bs-toggle="tab" role="tab"
                               aria-controls="students-05" aria-selected="true"> <i class="fa fa-users"></i> قائمة
                                الطلاب</a>
                        </li>
                        <li class="nav-item" @click.prevent="currentTab = 'form'">
                            <a class="nav-link" :class="currentTab === 'form' ? 'active show':'' "
                               id="add_student-05-tab"
                               data-bs-toggle="tab" role="tab" href="#"
                               aria-controls="add_student-05" aria-selected="false">
                                @if ($process_type == "edit")
                                    <i class="fas fa-user-edit"></i>
                                    تحديث طالب
                                @elseif($process_type == "show")
                                    <i class="fas fa-user-circle"></i>
                                    عرض طالب
                                @else
                                    <i class="fas fa-user-plus"></i>
                                    إضافة طالب
                                @endif
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade" :class="currentTab === 'home' ? 'active show':'' " id="students-05"
                             role="tabpanel"
                             aria-labelledby="students-05-tab">
                            @include('pages.students.students_table')
                        </div>
                        <div class="tab-pane fade" :class="currentTab === 'form' ? 'active show':'' "
                             id="add_student-05"
                             role="tabpanel"
                             aria-labelledby="add_student-05-tab">

                            @if($process_type == "show" && $student != null && !empty($student))
                                @include('pages.students.show_student')
                            @else
                                @if ($current_role == \App\Models\User::ADMIN_ROLE ||
                                     $current_role == \App\Models\User::SUPERVISOR_ROLE ||
                                     $current_role == \App\Models\User::TEACHER_ROLE)

                                    @can('إضافة طالب')
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

                                        @include('pages.students.father_form')

                                        @include('pages.students.student_form')

                                        @include('pages.students.student_last_form')
                                    @endcan
                                @endif
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <x-loading-indicator></x-loading-indicator>
    </div>
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
            livewire.emit('getTeachersByGradeId');
        });

        $("#grade_").on('change', function (e) {
            let id = $(this).val()
        @this.set('grade_id', id);
            livewire.emit('getTeachersByGradeId');
        });

        $("#group").on('change', function (e) {
            let id = $(this).val()
        @this.set('group_id', id);
        });

        $("#teacher").on('change', function (e) {
            let id = $(this).val()
        @this.set('selectedTeacherId', id);
            livewire.emit('getStudentsByTeacherId', id);
        });

        $("#age").on('change', function (e) {
            let id = $(this).val()
        @this.set('selectedAge', id);
        });
    </script>
@endpush
