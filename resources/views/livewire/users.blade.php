<div>
    <x-loading-indicator/>
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

    <div>
        @if(Session::has('message'))
            <script>
                $(function () {
                    toastr.success("{{ Session::get('message') }}");
                })
            </script>
        @endif
    </div>

    <div class="col-xl-12 mb-30">
        <div class="card card-statistics h-100">
            <div class="card-body">
                <h5 class="card-title">إدارة المستخدمين</h5>
                <div class="tab tab-border">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link {{$show_table == true ? 'active show':''}}" href="#" id="users-05-tab"
                               data-bs-toggle="tab" role="tab" wire:click="showformadd(true);"
                               aria-controls="users-05" aria-selected="true"> <i
                                    class="fas fa-chalkboard-teacher"></i> قائمة
                                المستخدمين</a>
                        </li>
                        <li class="nav-item">
                            @if ($process_type == 'reset')
                                <a class="nav-link {{$show_table == false ? 'active show':''}}" id="add_user-05-tab"
                                   data-bs-toggle="tab" role="tab"
                                   wire:click="showformadd(false);" href="#"
                                   aria-controls="add_user-05" aria-selected="false"><i
                                        class="fas fa-recycle"></i> إعادة تعيين كلمة المرور</a>
                            @elseif($process_type == 'edit_roles')
                                <a class="nav-link {{$show_table == false ? 'active show':''}}" id="add_user-05-tab"
                                   data-bs-toggle="tab" role="tab"
                                   wire:click="showformadd(false);" href="#"
                                   aria-controls="add_user-05" aria-selected="false"><i
                                        class="fas fa-edit"></i> تعديل الأدوار</a>
                            @else
                                <a class="nav-link {{$show_table == false ? 'active show':''}}" id="add_user-05-tab"
                                   data-bs-toggle="tab" role="tab"
                                   wire:click="showformadd(false);" href="#"
                                   aria-controls="add_user-05" aria-selected="false"><i
                                        class="fas {{!empty($modalId) ? 'fa-edit' : 'fa-plus-square'}}"></i>
                                    {{!empty($modalId) ? ' تحديث مستخدم' : ' إضافة مستخدم'}}</a>
                            @endif
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade {{$show_table == true ? 'active show':''}}" id="users-05"
                             role="tabpanel"
                             aria-labelledby="users-05-tab">
                            @include('pages.users.user_table')
                        </div>
                        <div class="tab-pane fade {{$show_table == false ? 'active show':''}}" id="add_user-05"
                             role="tabpanel"
                             aria-labelledby="add_user-05-tab">
                            @if ($process_type == 'reset')
                                @include('pages.users.reset_password_user')
                            @elseif($process_type == 'edit_roles')
                                @include('pages.users.user_roles_edit')
                            @else
                                @include('pages.users.user_form')
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
