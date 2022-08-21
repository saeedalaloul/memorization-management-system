<div>
    @if ($catchError)
        <div class="alert alert-danger" id="success-danger">
            <button type="button" wire:click.prevent="resetMessage()" class="close" data-dismiss="alert">x</button>
            {{ $catchError }}
        </div>
    @endif

    <div class="col-xl-12 mb-30">
        <div class="card card-statistics h-100">
            <div class="card-body" x-data="{currentTab: $persist('home')}">
                <h5 class="card-title">إدارة أعضاء الأنشطة</h5>
                <div class="tab tab-border">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item" @click.prevent="currentTab = 'home'">
                            <a class="nav-link" :class="currentTab === 'home' ? 'active show':'' " href="#"
                               id="activity-members-05-tab" data-bs-toggle="tab" role="tab"
                               aria-controls="activity-members-05" aria-selected="true"> <i
                                    class="fas fa-book-user"></i> قائمة أعضاء الأنشطة</a>
                        </li>
                        <li class="nav-item" @click.prevent="currentTab = 'form'">
                            <a class="nav-link" :class="currentTab === 'form' ? 'active show':'' "
                               id="add-activity-member-05-tab"
                               data-bs-toggle="tab" role="tab" href="#"
                               aria-controls="add-activity-member-05" aria-selected="false"><i
                                    class="fas fa-plus-square"></i>إضافة عضو أنشطة</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade" :class="currentTab === 'home' ? 'active show':'' "
                             id="activity-members-05"
                             role="tabpanel"
                             aria-labelledby="activity-members-05-tab">
                            @include('pages.activity_members.activity_members_table')
                        </div>
                        <div class="tab-pane fade" :class="currentTab === 'form' ? 'active show':'' "
                             id="add-activity-member-05"
                             role="tabpanel"
                             aria-labelledby="add-activity-member-05-tab">
                            @can('إضافة منشط')
                                @include('pages.activity_members.add_activity_member')
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
