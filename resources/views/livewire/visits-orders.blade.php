<div>
    @if ($current_role === \App\Models\User::OVERSIGHT_SUPERVISOR_ROLE ||  $current_role === \App\Models\User::OVERSIGHT_MEMBER_ROLE)
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
                <div class="card-body" x-data="{currentTab: $persist('home')}">
                    <h5 class="card-title">إدارة طلبات زيارات الرقابة</h5>
                    <div class="tab tab-border">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item" @click.prevent="currentTab = 'home'">
                                <a class="nav-link {{$process_type === '' ? 'active show':''}}" href="#"
                                   :class="currentTab === 'home' ? 'active show':'' "
                                   id="visits-orders-05-tab"
                                   data-bs-toggle="tab" role="tab" aria-controls="visits-orders-05"
                                   aria-selected="true"> <i
                                        class="fas fa-watch"></i> قائمة
                                    طلبات زيارات الرقابة</a>
                            </li>
                            <li class="nav-item" @click.prevent="currentTab = 'form'">
                                @if ($process_type == 'visitDetailsShow')
                                    <a class="nav-link" id="visit-show-05-tab"
                                       :class="currentTab === 'form' ? 'active show':'' "
                                       data-bs-toggle="tab" role="tab" href="#"
                                       aria-controls="visit-show-05" aria-selected="false"><i
                                            class="fas fa-eye"></i> عرض تفاصيل الزيارة</a>
                                @elseif($process_type == 'visitDetailsEdit')
                                    <a class="nav-link" id="visit-show-05-tab"
                                       :class="currentTab === 'form' ? 'active show':'' "
                                       data-bs-toggle="tab" role="tab" href="#"
                                       aria-controls="visit-show-05" aria-selected="false"><i
                                            class="fas fa-eye"></i> تعديل تفاصيل الزيارة</a>
                                @endif
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade {{$process_type == '' ? 'active show':''}}"
                                 :class="currentTab === 'home' ? 'active show':'' " id="visits-orders-05"
                                 role="tabpanel"
                                 aria-labelledby="visits-orders-05-tab">
                                @include('pages.visits_orders.visits_orders_table')
                            </div>
                            <div class="tab-pane fade" :class="currentTab === 'form' ? 'active show':'' "
                                 id="visit-show-05"
                                 role="tabpanel"
                                 aria-labelledby="visit-show-05-tab">
                                @if ($process_type == 'visitDetailsShow')
                                    @include('pages.visits_orders.visit_details_show')
                                @elseif($process_type == 'visitDetailsEdit')
                                    @include('pages.visits_orders.visit_details_edit')
                                @endif
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
