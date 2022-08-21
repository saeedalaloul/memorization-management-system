<div class="row">
    <div class="col-xl-12 mb-30">
        <div class="card card-statistics h-100">
            @if ($current_role == \App\Models\User::ADMIN_ROLE || $current_role == \App\Models\User::EXAMS_SUPERVISOR_ROLE)
                <div class="card-body">
                    <br>
                    @can('إعدادات الإختبارات')
                        <div class="col-xl-12 mb-30">
                            <div class="card card-statistics h-100">
                                <div class="card-body" x-data="{currentTab: $persist('home')}">
                                    <h5 class="card-title">اعدادات الإختبارات القرآنية</h5>
                                    <div class="accordion plus-icon shadow">
                                        <div  class="acd-group" :class="currentTab === 'home' ? 'acd-active':'' " @click.prevent="currentTab = 'home'">
                                            <a class="acd-heading">الإعدادات
                                                العامة</a>
                                            <div class="acd-des" :style="currentTab === 'form' ? 'display: none;':'' ">
                                                @include('pages.exams_settings.exams_settings_public')
                                            </div>
                                        </div>

                                        <div class="acd-group" :class="currentTab === 'form' ? 'acd-active':'' " @click.prevent="currentTab = 'form'">
                                            <a  class="acd-heading">تخصيص عدد أسئلة
                                                الإختبار لأجزاء محددة</a>
                                            <div class="acd-des" :style="currentTab === 'home' ? 'display: none;':'' ">
                                                @include('pages.exams_settings.exams_custom_question')
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endcan
                </div>
            @endif
        </div>
    </div>
    <x-loading-indicator></x-loading-indicator>
</div>
