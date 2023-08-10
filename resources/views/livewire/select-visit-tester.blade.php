<div class="row">
    @if ($current_role === \App\Models\User::OVERSIGHT_SUPERVISOR_ROLE)
        <div>
            @if ($catchError)
                <div class="alert alert-danger" id="success-danger">
                    <button type="button" wire:click.prevent="resetMessage()" class="close" data-dismiss="alert">x
                    </button>
                    {{ $catchError }}
                </div>
            @endif
        </div>
        <div class="col-xl-12 mb-30">
            <div class="card card-statistics h-100">
                <div class="card-body">
                    <h5 class="card-title">إقرار زيارة على الإختبارات</h5>
                    <div class="tab tab-border">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active show" href="#"
                                   id="select-visit-tester-05-tab" data-bs-toggle="tab" role="tab"
                                   aria-controls="select-visit-tester-05" aria-selected="true"> <i
                                        class="fas fa-book-open"></i>
                                    قائمة
                                    الإختبارات</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade active show" id="select-visit-tester-05"
                                 role="tabpanel"
                                 aria-labelledby="select-visit-tester-05-tab">
                                @include('pages.select_visit_tester.select_visit_tester_table')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <x-loading-indicator></x-loading-indicator>
    @endif
</div>
