<div>
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
            <div class="card-body" x-data="{currentTab: $persist('home')}">
                <h5 class="card-title">إدارة الإختبارات القرآنية</h5>
                <div class="tab tab-border">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item" @click.prevent="currentTab = 'home'">
                            <a class="nav-link" href="#" :class="currentTab === 'home' ? 'active show':'' "
                               id="exams-05-tab" data-bs-toggle="tab" role="tab" aria-controls="exams-05" aria-selected="true"> <i
                                    class="fas fa-book-open"></i> قائمة
                                الإختبارات القرآنية</a>
                        </li>
                        <li class="nav-item"  @click.prevent="currentTab = 'form'">
                            <a class="nav-link" href="#" :class="currentTab === 'form' ? 'active show':'' "
                               id="add-exam-05-tab" data-bs-toggle="tab" role="tab" aria-controls="add-exam-05" aria-selected="false"><i
                                    class="fas fa-plus-square"></i>
                                إضافة اختبار</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade" :class="currentTab === 'home' ? 'active show':'' "
                             id="exams-05"
                             role="tabpanel"
                             aria-labelledby="exams-05-tab">
                            @include('pages.exams.exams_table')
                        </div>
                        <div class="tab-pane fade" id="add-exam-05" role="tabpanel" :class="currentTab === 'form' ? 'active show':'' "
                             aria-labelledby="add-exam-05-tab">
                            @can('إضافة اختبار')
                                @include('pages.exams.add_exam')
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <x-loading-indicator></x-loading-indicator>
</div>
@push('alpine-plugins')
    <!-- Alpine Plugins -->
    <script defer src="https://unpkg.com/@alpinejs/persist@3.x.x/dist/cdn.min.js"></script>
@endpush
