<div>
    <x-loading-indicator/>
@if ($catchError)
        <div class="alert alert-danger" id="success-danger">
            <button type="button" class="close" data-dismiss="alert">x</button>
            {{ $catchError }}
        </div>
    @endif

    @if(Session::has('message'))
        <script>
            $(function () {
                toastr.success("{{ Session::get('message') }}");
            })
        </script>
    @endif

    <div class="col-xl-12 mb-30">
        <div class="card card-statistics h-100">
            <div class="card-body">
                <h5 class="card-title">إدارة المختبرين</h5>
                <div class="tab tab-border">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link {{$show_table == true ? 'active show':''}}" href="#"
                               id="testers-05-tab"
                               data-bs-toggle="tab" role="tab" wire:click.prevent="showformadd(true);"
                               aria-controls="testers-05" aria-selected="true"> <i
                                    class="fas fa-book-user"></i> قائمة المختبرين</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{$show_table == false ? 'active show':''}}"
                               id="add-tester-05-tab"
                               data-bs-toggle="tab" role="tab"
                               wire:click.prevent="showformadd(false);" href="#"
                               aria-controls="add-tester-05" aria-selected="false"><i
                                    class="fas {{$show_exams == true ? 'fa-book-open' : 'fa-plus-square'}}"></i>
                                {{$show_exams == true ? 'عرض إختبارات المختبر' : 'إضافة مختبر'}}</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade {{$show_table == true ? 'active show':''}}"
                             id="testers-05"
                             role="tabpanel"
                             aria-labelledby="testers-05-tab">
                            @include('pages.testers.tester_table')
                        </div>
                        <div class="tab-pane fade {{$show_table == false ? 'active show':''}}"
                             id="add-tester-05"
                             role="tabpanel"
                             aria-labelledby="add-tester-05-tab">
                            @if ($show_exams == true)
                                @include('pages.testers.show_exams_table')
                            @else
                                @can('إضافة مختبر')
                                    @include('pages.testers.add_tester')
                                @endcan
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
