<div>
    <div>
        @if ($catchError)
            <div class="alert alert-danger" id="success-danger">
                <button wire:click.prevent="resetMessage();" type="button" class="close" data-dismiss="alert">x</button>
                {{ $catchError }}
            </div>
        @endif
    </div>

    <div class="col-xl-12 mb-30">
        <div class="card card-statistics h-100">
            <div class="card-body" x-data="{currentTab: $persist('home')}">
                <h5 class="card-title">إدارة الاجراءات العقابية</h5>
                <div class="tab tab-border">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item" @click.prevent="currentTab = 'home'">
                            <a class="nav-link" href="#" :class="currentTab === 'home' ? 'active show':'' "
                               id="punitive-measures-05-tab" data-bs-toggle="tab" role="tab" aria-controls="punitive-measures-05" aria-selected="true"> <i
                                    class="fas fa-book-open"></i> قائمة
                                الاجراءات العقابية</a>
                        </li>
                        <li class="nav-item"  @click.prevent="currentTab = 'form'">
                            <a class="nav-link" href="#" :class="currentTab === 'form' ? 'active show':'' "
                               id="add-punitive-measure-05-tab" data-bs-toggle="tab" role="tab" aria-controls="add-punitive-measure-05" aria-selected="false"><i
                                    class="fas {{!empty($modalId) && empty($process_type) ? 'fa-edit' : 'fa-plus-square'}}"></i>
                                {{!empty($modalId) && empty($process_type) ? 'تعديل إجراء عقابي' : 'إضافة إجراء عقابي'}}</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade" :class="currentTab === 'home' ? 'active show':'' "
                             id="punitive-measures-05"
                             role="tabpanel"
                             aria-labelledby="punitive-measures-05-tab">
                            @can('إدارة الاجراءات العقابية')
                                @include('pages.punitive_measures.punitive_measures_table')
                            @endcan
                        </div>
                        <div class="tab-pane fade" id="add-punitive-measure-05" role="tabpanel" :class="currentTab === 'form' ? 'active show':'' "
                             aria-labelledby="add-punitive-measure-05-tab">
                            @if (empty($modalId) && empty($process_type))
                                @can('إضافة اجراء عقابي')
                                    @include('pages.punitive_measures.add_punitive_measure')
                                @endcan
                            @else
                                @can('تعديل اجراء عقابي')
                                    @include('pages.punitive_measures.add_punitive_measure')
                                @endcan
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
