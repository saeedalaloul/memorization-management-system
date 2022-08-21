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
                <h5 class="card-title">إدارة الأنشطة</h5>
                <div class="tab tab-border">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item" @click.prevent="currentTab = 'home'">
                            <a class="nav-link" :class="currentTab === 'home' ? 'active show':'' " href="#"
                               id="activities-orders-05-tab"
                               data-bs-toggle="tab" role="tab" aria-controls="activities-orders-05"
                               aria-selected="true"> <i
                                    class="fas fa-watch"></i> قائمة الأنشطة</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade" :class="currentTab === 'home' ? 'active show':'' "
                             id="activities-orders-05"
                             role="tabpanel"
                             aria-labelledby="activities-orders-05-tab">
                            @include('pages.activities.activities_table')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('alpine-plugins')
    <!-- Alpine Plugins -->
    <script defer src="https://unpkg.com/@alpinejs/persist@3.x.x/dist/cdn.min.js"></script>
@endpush
