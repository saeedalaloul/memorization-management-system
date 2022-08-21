<div>
    <div>
        @if (!empty($successMessage))
            <div class="alert alert-success" id="success-alert">
                <button type="button" wire:click.prevent="resetMessage()" class="close" data-dismiss="alert">x</button>
                {{ $successMessage }}
            </div>
        @endif

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
                <h5 class="card-title">إدارة المستخدمين</h5>
                <div class="tab tab-border">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item" @click.prevent="currentTab = 'home'">
                            <a class="nav-link" href="#" id="users-05-tab"
                               :class="currentTab === 'home' ? 'active show':'' "
                               data-bs-toggle="tab" role="tab" aria-controls="users-05" aria-selected="true"> <i
                                    class="fas fa-chalkboard-teacher"></i> قائمة
                                المستخدمين</a>
                        </li>
                        <li class="nav-item" @click.prevent="currentTab = 'form'">
                            @if ($process_type == 'reset')
                                <a class="nav-link" id="add_user-05-tab"
                                   :class="currentTab === 'form' ? 'active show':'' "
                                   data-bs-toggle="tab" role="tab" href="#"
                                   aria-controls="add_user-05" aria-selected="false"><i
                                        class="fas fa-recycle"></i> إعادة تعيين كلمة المرور</a>
                            @elseif($process_type == 'edit_roles')
                                <a class="nav-link" id="add_user-05-tab"
                                   :class="currentTab === 'form' ? 'active show':'' "
                                   data-bs-toggle="tab" role="tab" href="#"
                                   aria-controls="add_user-05" aria-selected="false"><i
                                        class="fas fa-user-edit"></i> تعديل الأدوار</a>
                            @elseif($process_type == 'edit_permission')
                                <a class="nav-link" id="add_user-05-tab"
                                   :class="currentTab === 'form' ? 'active show':'' "
                                   data-bs-toggle="tab" role="tab" href="#"
                                   aria-controls="add_user-05" aria-selected="false"><i
                                        class="fas fa-edit"></i> تعديل الصلاحيات</a>
                            @else
                                <a class="nav-link" id="add_user-05-tab"
                                   :class="currentTab === 'form' ? 'active show':'' "
                                   data-bs-toggle="tab" role="tab" href="#"
                                   aria-controls="add_user-05" aria-selected="false"><i
                                        class="fas {{!empty($modalId) ? 'fa-edit' : 'fa-plus-square'}}"></i>
                                    {{!empty($modalId) ? ' تحديث مستخدم' : ' إضافة مستخدم'}}</a>
                            @endif
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade" :class="currentTab === 'home' ? 'active show':'' " id="users-05"
                             role="tabpanel"
                             aria-labelledby="users-05-tab">
                            @include('pages.users.users_table')
                        </div>
                        <div class="tab-pane fade" :class="currentTab === 'form' ? 'active show':'' " id="add_user-05"
                             role="tabpanel"
                             aria-labelledby="add_user-05-tab">
                            @if ($process_type == 'reset')
                                @include('pages.users.reset_password_user')
                            @elseif($process_type == 'edit_roles')
                                @include('pages.users.user_roles_edit')
                            @elseif($process_type == 'edit_permission')
                                @include('pages.users.user_permission_edit')
                            @else
                                @include('pages.users.user_form')
                            @endif
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
