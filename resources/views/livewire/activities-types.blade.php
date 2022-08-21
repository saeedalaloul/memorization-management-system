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
                <h5 class="card-title">إدارة أنواع الأنشطة</h5>
                <div class="tab tab-border">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item" @click.prevent="currentTab = 'home'">
                            <a class="nav-link" href="#" :class="currentTab === 'home' ? 'active show':'' "
                               id="activities-types-05-tab"
                               data-bs-toggle="tab" role="tab" aria-controls="activities-types-05" aria-selected="true"> <i
                                    class="fas fa-book-user"></i> قائمة أنواع الأنشطة</a>
                        </li>
                        <li class="nav-item" @click.prevent="currentTab = 'form'">
                            <a class="nav-link"
                               id="add-activity-type-05-tab"
                               data-bs-toggle="tab" role="tab" href="#" :class="currentTab === 'form' ? 'active show':'' "
                               aria-controls="add-activity-type-05" aria-selected="false"><i
                                    class="fas {{$process_type == 'edit' ? 'fa-edit' : 'fa-plus-square'}}"></i>
                                {{$process_type == 'edit' ? 'تعديل نوع النشاط' : 'إضافة نوع النشاط'}}</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade" :class="currentTab === 'home' ? 'active show':'' "
                             id="activities-types-05"
                             role="tabpanel"
                             aria-labelledby="activities-types-05-tab">
                            @include('pages.activities_types.activities_types_table')
                        </div>
                        <div class="tab-pane fade" :class="currentTab === 'form' ? 'active show':'' "
                             id="add-activity-type-05"
                             role="tabpanel"
                             aria-labelledby="add-activity-type-05-tab">
                            @include('pages.activities_types.form_activity_type')
                        </div>
                    </div>
                </div>
                @if ($process_type == '')
                    <div hidden currentTab = 'home' ></div>
                @endif
            </div>
        </div>
    </div>
    <x-loading-indicator/>
</div>
@push('alpine-plugins')
    <!-- Alpine Plugins -->
    <script defer src="https://unpkg.com/@alpinejs/persist@3.x.x/dist/cdn.min.js"></script>
@endpush
