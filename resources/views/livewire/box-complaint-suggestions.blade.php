<div>
    @if ($current_role === \App\Models\User::ADMIN_ROLE || $current_role === \App\Models\User::OVERSIGHT_SUPERVISOR_ROLE
            ||  $current_role === \App\Models\User::TEACHER_ROLE || $current_role === \App\Models\User::SUPERVISOR_ROLE)
    <div>
        @if ($catchError)
            <div class="alert alert-danger" id="success-danger">
                <button type="button" wire:click.prevent="resetMessage();" class="close" data-dismiss="alert">x</button>
                {{ $catchError }}
            </div>
        @endif
    </div>

    <div class="col-xl-12 mb-30">
        <div class="card card-statistics h-100">
            <div class="card-body" x-data="{currentTab: $persist('home')}">
                <h5 class="card-title">إدارة صندوق الشكاوي والإقتراحات</h5>
                <div class="tab tab-border">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item" @click.prevent="currentTab = 'home'">
                            <a class="nav-link" href="#"
                               :class="currentTab === 'home' ? 'active show':'' "
                               id="complaints-05-tab" data-bs-toggle="tab" role="tab" aria-controls="complaints-05"
                               aria-selected="true"> <i
                                    class="fas fa-support"></i> قائمة
                                الشكاوي والإقتراحات</a>
                        </li>
                        <li class="nav-item" @click.prevent="currentTab = 'form'">
                            @if ($process_type == 'detailsShow')
                                <a class="nav-link" :class="currentTab === 'form' ? 'active show':'' "
                                   id="complaint-show-05-tab" data-bs-toggle="tab" role="tab" href="#"
                                   aria-controls="complaint-show-05" aria-selected="false"><i
                                        class="fas fa-eye"></i> عرض تفاصيل الشكوى/الاقتراح</a>
                            @elseif($process_type == 'complaintReply')
                                <a class="nav-link" :class="currentTab === 'form' ? 'active show':'' "
                                   id="complaint-show-05-tab" data-bs-toggle="tab" role="tab" href="#"
                                   aria-controls="complaint-show-05" aria-selected="false"><i
                                        class="fa fa-reply"></i> الرد على الشكوى/الاقتراح</a>
                            @else
                                <a class="nav-link" :class="currentTab === 'form' ? 'active show':'' "
                                   id="add-complaint-05-tab" data-bs-toggle="tab" role="tab" href="#"
                                   aria-controls="add-complaint-05" aria-selected="false"><i
                                        class="fas fa-plus-square"></i>
                                    تقديم شكوى/اقتراح</a>
                            @endif
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade" :class="currentTab === 'home' ? 'active show':'' "
                             id="complaints-05"
                             role="tabpanel"
                             aria-labelledby="complaints-05-tab">
                            @include('pages.box_complaint_suggestions.complaints_table')
                        </div>
                        <div class="tab-pane fade" :class="currentTab === 'form' ? 'active show':'' "
                             id="add-complaint-05"
                             role="tabpanel"
                             aria-labelledby="add-complaint-05-tab">
                            @if ($process_type == 'detailsShow')
                                @include('pages.box_complaint_suggestions.details_show')
                            @elseif($process_type == 'complaintReply')
                                @include('pages.box_complaint_suggestions.complaint_reply')
                            @else
                                @if($current_role == \App\Models\User::TEACHER_ROLE)
                                    @can('تقديم شكوى/اقتراح')
                                        @include('pages.box_complaint_suggestions.add_complaint')
                                    @endcan
                                @endif
                            @endif
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
