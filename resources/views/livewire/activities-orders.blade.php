<div>
    <div>
        @if ($catchError)
            <div class="alert alert-danger" id="success-danger">
                <button type="button" wire:click.prevent="resetMessage()" class="close" data-dismiss="alert">x</button>
                {{ $catchError }}
            </div>
        @endif
    </div>

    <div class="col-xl-12 mb-30">
        <div class="card card-statistics h-100">
            <div class="card-body" x-data="{currentTab: $persist('home')}">
                <h5 class="card-title">إدارة طلبات الأنشطة</h5>
                <div class="tab tab-border">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item" @click.prevent="currentTab = 'home'">
                            <a class="nav-link" href="#" :class="currentTab === 'home' ? 'active show':'' "
                               id="activities-orders-05-tab"
                               data-bs-toggle="tab" role="tab" aria-controls="activities-orders-05" aria-selected="true"> <i
                                    class="fas fa-watch"></i> قائمة طلبات الأنشطة</a>
                        </li>
                        <li class="nav-item" @click.prevent="currentTab = 'form'">
                            <a class="nav-link "
                               id="add-activity-order-05-tab" data-bs-toggle="tab" role="tab" :class="currentTab === 'form' ? 'active show':'' " href="#"
                               aria-controls="add-activity-order-05" aria-selected="false"><i
                                    class="fas fa-edit"></i> طلب نشاط</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade" :class="currentTab === 'home' ? 'active show':'' "
                             id="activities-orders-05"
                             role="tabpanel"
                             aria-labelledby="activities-orders-05-tab">
                            @include('pages.activities_orders.activities_orders_table')
                        </div>
                        <div class="tab-pane fade" :class="currentTab === 'form' ? 'active show':'' "
                             id="add-activities-orders-05"
                             role="tabpanel"
                             aria-labelledby="add-activity-order-05-tab">
                            @can('إجراء طلب نشاط')
                                @include('pages.activities_orders.form_activity_order')
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
