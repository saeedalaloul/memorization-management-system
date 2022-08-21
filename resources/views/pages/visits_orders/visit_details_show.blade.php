<div class="row">
    <div class="col-md-12 mb-30">
        <div class="card card-statistics h-100">
            <div class="card-body">
                <div class="card-body">
                    <div class="tab nav-border">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active show" id="home-02-tab" data-toggle="tab" href="#home-02"
                                   role="tab" aria-controls="home-02"
                                   aria-selected="true">معلومات الزيارة</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade active show" id="home-02" role="tabpanel"
                                 aria-labelledby="home-02-tab">
                                @include('pages.visits_orders.visit_of_group')
                            </div>
                        </div>
                    </div>
                </div>
                @include('pages.visits_orders.form_visit')
            </div>
        </div>
    </div>
</div>
