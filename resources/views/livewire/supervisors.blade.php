<div>
    @if ($catchError)
        <div class="alert alert-danger" id="success-danger">
            <button type="button" class="close" data-dismiss="alert">x</button>
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
                <h5 class="card-title">إدارة المشرفين</h5>
                <div class="tab tab-border">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link {{$show_table == true ? 'active show':''}}" href="#" id="supervisors-05-tab"
                               data-bs-toggle="tab" role="tab" wire:click="showformadd(true);"
                               aria-controls="supervisors-05" aria-selected="true"> <i
                                    class="fas fa-chalkboard"></i> قائمة
                                المشرفين</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{$show_table == false ? 'active show':''}}" id="add_supervisor-05-tab"
                               data-bs-toggle="tab" role="tab"
                               wire:click="showformadd(false);" href="#"
                               aria-controls="add_supervisor-05" aria-selected="false"><i
                                    class="fas {{!empty($modalId) ? 'fa-edit' : 'fa-plus-square'}}"></i>
                                {{!empty($modalId) ? ' تحديث مشرف' : ' إضافة مشرف'}}</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade {{$show_table == true ? 'active show':''}}" id="supervisors-05"
                             role="tabpanel"
                             aria-labelledby="supervisors-05-tab">
                            @include('pages.supervisors.supervisor_table')
                        </div>


                        <div class="tab-pane fade {{$show_table == false ? 'active show':''}}" id="add_supervisor-05"
                             role="tabpanel"
                             aria-labelledby="add_supervisor-05-tab">
                            @include('pages.supervisors.supervisor_form')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
