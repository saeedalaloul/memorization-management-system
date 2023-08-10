<div>
    @if ($current_role === \App\Models\User::EXAMS_SUPERVISOR_ROLE)
        <div>
            @if ($catchError)
                <div class="alert alert-danger" id="success-danger">
                    <button wire:click.prevent="resetMessage();" type="button" class="close" data-dismiss="alert">x
                    </button>
                    {{ $catchError }}
                </div>
            @endif
        </div>

        <div class="col-xl-12 mb-30">
            <div class="card card-statistics h-100">
                <div class="card-body" x-data="{currentTab: $persist('home')}">
                    <h5 class="card-title">إدارة المختبرين</h5>
                    <div class="tab tab-border">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item" @click.prevent="currentTab = 'home'">
                                <a class="nav-link" href="#" id="testers-05-tab"
                                   :class="currentTab === 'home' ? 'active show':'' "
                                   data-bs-toggle="tab" role="tab"
                                   aria-controls="testers-05" aria-selected="true"> <i
                                        class="fas fa-book-user"></i> قائمة المختبرين</a>
                            </li>
                            <li class="nav-item" @click.prevent="currentTab = 'form'">
                                <a class="nav-link" id="add-tester-05-tab"
                                   :class="currentTab === 'form' ? 'active show':'' "
                                   data-bs-toggle="tab" role="tab" href="#"
                                   aria-controls="add-tester-05" aria-selected="false"><i
                                        class="fas fa-plus-square"></i>إضافة مختبر</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade" :class="currentTab === 'home' ? 'active show':'' "
                                 id="testers-05"
                                 role="tabpanel"
                                 aria-labelledby="testers-05-tab">
                                @include('pages.testers.testers_table')
                            </div>
                            <div class="tab-pane fade" :class="currentTab === 'form' ? 'active show':'' "
                                 id="add-tester-05"
                                 role="tabpanel"
                                 aria-labelledby="add-tester-05-tab">
                                @can('إضافة مختبر')
                                    @include('pages.testers.add_tester')
                                @endcan
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
