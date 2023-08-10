<div>
    @if ($current_role === \App\Models\User::ADMIN_ROLE)
        @if ($catchError)
            <div class="alert alert-danger" id="success-danger">
                <button type="button" class="close" data-dismiss="alert">x</button>
                {{ $catchError }}
            </div>
        @endif

        <div class="col-xl-12 mb-30">
            <div class="card card-statistics h-100">
                <div class="card-body" x-data="{currentTab: $persist('home')}">
                    <h5 class="card-title">إدارة المشرفين</h5>
                    <div class="tab tab-border">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item" @click.prevent="currentTab = 'home'">
                                <a class="nav-link" href="#" :class="currentTab === 'home' ? 'active show':'' "
                                   id="supervisors-05-tab" data-bs-toggle="tab" role="tab"
                                   aria-controls="supervisors-05" aria-selected="true"> <i
                                        class="fas fa-chalkboard"></i> قائمة
                                    المشرفين</a>
                            </li>
                            <li class="nav-item" @click.prevent="currentTab = 'form'">
                                <a class="nav-link" id="add_supervisor-05-tab"
                                   data-bs-toggle="tab" role="tab" href="#"
                                   :class="currentTab === 'form' ? 'active show':'' "
                                   aria-controls="add_supervisor-05" aria-selected="false"><i
                                        class="fas {{!empty($modalId) ? 'fa-edit' : 'fa-plus-square'}}"></i>
                                    {{!empty($modalId) ? ' تحديث مشرف' : ' إضافة مشرف'}}</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade" :class="currentTab === 'home' ? 'active show':'' "
                                 id="supervisors-05"
                                 role="tabpanel"
                                 aria-labelledby="supervisors-05-tab">
                                @can('إدارة مشرفي المراحل')
                                    @include('pages.supervisors.supervisors_table')
                                @endcan
                            </div>


                            <div class="tab-pane fade" :class="currentTab === 'form' ? 'active show':'' "
                                 id="add_supervisor-05"
                                 role="tabpanel"
                                 aria-labelledby="add_supervisor-05-tab">
                                @can('إضافة مشرف مرحلة')
                                    @include('pages.supervisors.supervisor_form')
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
