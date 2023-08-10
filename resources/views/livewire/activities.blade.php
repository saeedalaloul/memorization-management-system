<div>
    @if ($current_role === \App\Models\User::ADMIN_ROLE || $current_role === \App\Models\User::ACTIVITIES_SUPERVISOR_ROLE
            || $current_role === \App\Models\User::SUPERVISOR_ROLE
            ||  $current_role === \App\Models\User::TEACHER_ROLE || $current_role === \App\Models\User::ACTIVITY_MEMBER_ROLE)
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
                    <h5 class="card-title">إدارة الأنشطة</h5>
                    <div class="tab tab-border">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active show" href="#"
                                   id="activities-orders-05-tab"
                                   data-bs-toggle="tab" role="tab" aria-controls="activities-orders-05"
                                   aria-selected="true"> <i
                                        class="fas fa-watch"></i> قائمة الأنشطة</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade active show"
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
    @endif
</div>
