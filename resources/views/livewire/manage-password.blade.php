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
            <div class="card-body">
                <h5 class="card-title">تغيير كلمة المرور</h5>
                <div class="tab tab-border">
                    <ul class="nav nav-tabs" role="tablist">
                        @if (auth()->user()->password !== null)
                            <li class="nav-item" @click.prevent="currentTab = 'home'">
                                <a class="nav-link active show" id="reset-password-05-tab"
                                   data-bs-toggle="tab" role="tab" href="#"
                                   aria-controls="reset-password-05" aria-selected="false"><i
                                        class="fas fa-recycle"></i> إعادة تعيين كلمة المرور</a>
                            </li>
                        @else
                            <li class="nav-item">
                                <a class="nav-link active show" id="set-password-05-tab"
                                   data-bs-toggle="tab" role="tab" href="#"
                                   aria-controls="set-password-05" aria-selected="false"><i
                                        class="fas fa-recycle"></i> تعيين كلمة المرور</a>
                            </li>
                        @endif
                    </ul>
                    <div class="tab-content">
                        @if (auth()->user()->password !== null)
                            <div class="tab-pane fade active show" id="reset-password-05"
                                 role="tabpanel"
                                 aria-labelledby="reset-password-05-tab">
                                @include('pages.manage_password.reset_password_user')
                            </div>
                        @else
                            <div class="tab-pane fade active show" id="set-password-05"
                                 role="tabpanel" aria-labelledby="set-password-05-tab">
                                @include('pages.manage_password.set_password_user')
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <x-loading-indicator></x-loading-indicator>
</div>
