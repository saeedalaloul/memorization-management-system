<div>
    @if ($catchError)
        <div class="alert alert-danger" id="success-danger">
            <button type="button" class="close" data-dismiss="alert">x</button>
            {{ $catchError }}
        </div>
    @endif

    <div class="col-xl-12 mb-30">
        <div class="card card-statistics h-100">
            <div class="card-body">
                <h5 class="card-title">إدارة صندوق الشكاوي والإقتراحات</h5>
                <div class="tab tab-border">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link {{$isAddComplaint == false ? 'active show':''}}" href="#"
                               id="complaints-05-tab"
                               data-bs-toggle="tab" role="tab" wire:click.prevent="addComplaint(false);"
                               aria-controls="complaints-05" aria-selected="true"> <i
                                    class="fas fa-support"></i> قائمة
                                الشكاوي والإقتراحات</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{$isAddComplaint == true ? 'active show':''}}"
                               id="add-complaint-05-tab"
                               data-bs-toggle="tab" role="tab"
                               wire:click.prevent="addComplaint(true);" href="#"
                               aria-controls="add-complaint-05" aria-selected="false"><i
                                    class="fas fa-plus-square"></i>
                                تقديم شكوى/اقتراح</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade {{$isAddComplaint == false ? 'active show':''}}"
                             id="complaints-05"
                             role="tabpanel"
                             aria-labelledby="complaints-05-tab">
                            @include('pages.box_complaint_suggestions.complaint_table')
                        </div>
                        <div class="tab-pane fade {{$isAddComplaint == true ? 'active show':''}}"
                             id="add-complaint-05"
                             role="tabpanel"
                             aria-labelledby="add-complaint-05-tab">
                            @can('تقديم شكوى/اقتراح')
                                @include('pages.box_complaint_suggestions.add_complaint')
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <x-loading-indicator/>
</div>
