<div>
    @if ($current_role === \App\Models\User::ADMIN_ROLE || $current_role === \App\Models\User::SUPERVISOR_ROLE
        ||  $current_role === \App\Models\User::TEACHER_ROLE || $current_role === \App\Models\User::TESTER_ROLE
        ||  $current_role === \App\Models\User::EXAMS_SUPERVISOR_ROLE)
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
            <div class="card-body">
                <h5 class="card-title">إدارة اختبارات السنة</h5>
                <div class="tab tab-border">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active show" href="#"
                               id="exams-05-tab" data-bs-toggle="tab" role="tab" aria-controls="exams-05"
                               aria-selected="true"> <i
                                    class="fas fa-book-open"></i> قائمة
                                اختبارات السنة</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade active show" id="exams-05" role="tabpanel"
                             aria-labelledby="exams-05-tab">
                            @include('pages.sunnah_exams.exams_table')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <x-loading-indicator></x-loading-indicator>
    @endif
</div>
