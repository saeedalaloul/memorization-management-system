<div>
    @if ($catchError)
        <div class="alert alert-danger" id="success-danger">
            <button type="button" class="close" data-dismiss="alert">x</button>
            {{ $catchError }}
        </div>
    @endif

    <div class="col-xl-12 mb-30">
        <div class="card card-statistics h-100">
            <div class="card-body" x-data="{currentTab: $persist('home')}">
                <h5 class="card-title">إدارة المحفظين</h5>
                <div class="tab tab-border">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item" @click.prevent="currentTab = 'home'">
                            <a class="nav-link" href="#" id="teachers-05-tab"
                               data-bs-toggle="tab" role="tab" :class="currentTab === 'home' ? 'active show':'' "
                               aria-controls="teachers-05" aria-selected="true"> <i
                                    class="fas fa-chalkboard-teacher"></i> قائمة
                                المحفظين</a>
                        </li>
                        <li class="nav-item" @click.prevent="currentTab = 'form'">
                            <a class="nav-link" id="add_teacher-05-tab"
                               data-bs-toggle="tab" role="tab" href="#" :class="currentTab === 'form' ? 'active show':'' "
                               aria-controls="add_teacher-05" aria-selected="false"><i
                                    class="fas {{!empty($modalId) ? 'fa-edit' : 'fa-plus-square'}}"></i>
                                {{!empty($modalId) ? ' تحديث محفظ' : ' إضافة محفظ'}}</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade" :class="currentTab === 'home' ? 'active show':'' " id="teachers-05"
                             role="tabpanel"
                             aria-labelledby="teachers-05-tab">
                            @can('إدارة المحفظين')
                            @include('pages.teachers.teachers_table')
                            @endcan
                        </div>
                        <div class="tab-pane fade" :class="currentTab === 'form' ? 'active show':'' " id="add_teacher-05"
                             role="tabpanel"
                             aria-labelledby="add_teacher-05-tab">
                            @can('إضافة محفظ')
                                @include('pages.teachers.teacher_form')
                            @endcan
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
@push('js')
    <script>
        $("#grade").on('change', function (e) {
            let id = $(this).val()
        @this.set('selectedGradeId', id);
            livewire.emit('getTeachersByGradeId', id);
        });
    </script>
@endpush
