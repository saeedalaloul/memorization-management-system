<div>
    @if ($current_role === \App\Models\User::OVERSIGHT_SUPERVISOR_ROLE)
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
                <h5 class="card-title">إدارة أعضاء الرقابة</h5>
                <div class="tab tab-border">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item" @click.prevent="currentTab = 'home'">
                            <a class="nav-link" :class="currentTab === 'home' ? 'active show':'' " href="#"
                               id="oversight-members-05-tab" data-bs-toggle="tab" role="tab"
                               aria-controls="oversight-members-05" aria-selected="true"> <i
                                    class="fas fa-book-user"></i> قائمة أعضاء الرقابة</a>
                        </li>
                        <li class="nav-item" @click.prevent="currentTab = 'form'">
                            <a class="nav-link" :class="currentTab === 'form' ? 'active show':'' "
                               id="add-oversight-member-05-tab" data-bs-toggle="tab" role="tab" href="#"
                               aria-controls="add-oversight-member-05" aria-selected="false"><i
                                    class="fas fa-plus-square"></i> إضافة عضو رقابة</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade" :class="currentTab === 'home' ? 'active show':'' "
                             id="oversight-members-05"
                             role="tabpanel"
                             aria-labelledby="oversight-members-05-tab">
                            @include('pages.oversight_members.oversight_member_table')
                        </div>
                        <div class="tab-pane fade" :class="currentTab === 'form' ? 'active show':'' "
                             id="add-oversight-member-05"
                             role="tabpanel"
                             aria-labelledby="add-oversight-member-05-tab">
                            @can('إضافة مراقب')
                                @include('pages.oversight_members.add_oversight_member')
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
