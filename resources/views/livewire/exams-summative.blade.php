<div>
    @if ($catchError)
        <div class="alert alert-danger" id="success-danger">
            <button type="button" class="close" data-dismiss="alert">x</button>
            {{ $catchError }}
        </div>
    @endif

    <div class="col-xl-12 mb-30">
        <div class="card card-statistics h-100">
            <div class="card-body">
                <h5 class="card-title">إدارة اختبارات التجميعي القرآنية</h5>
                <div class="tab tab-border">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link {{$isAddExam == false ? 'active show':''}}" href="#"
                               id="exams-05-tab"
                               data-bs-toggle="tab" role="tab" wire:click.prevent="addExam(false);"
                               aria-controls="exams-05" aria-selected="true"> <i
                                    class="fas fa-book-open"></i> قائمة
                                اختبارات التجميعي القرآنية</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{$isAddExam == true ? 'active show':''}}"
                               id="add-exam-05-tab"
                               data-bs-toggle="tab" role="tab"
                               wire:click.prevent="addExam(true);" href="#"
                               aria-controls="add-exam-05" aria-selected="false"><i
                                    class="fas fa-plus-square"></i>
                                إضافة اختبار تجميعي</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade {{$isAddExam == false ? 'active show':''}}"
                             id="exams-05"
                             role="tabpanel"
                             aria-labelledby="exams-05-tab">
                            @include('pages.exams_summative.exam_table')
                        </div>
                        <div class="tab-pane fade {{$isAddExam == true ? 'active show':''}}"
                             id="add-exam-05"
                             role="tabpanel"
                             aria-labelledby="add-exam-05-tab">
                            @can('إضافة اختبار تجميعي')
                                @include('pages.exams_summative.add_exam')
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <x-loading-indicator/>
</div>