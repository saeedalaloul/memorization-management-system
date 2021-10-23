<div class="row">
    <div>
        @if(Session::has('success_message'))
            <script>
                $(function () {
                    toastr.success("{{ Session::get('success_message') }}");
                })
            </script>
        @endif

        @if(Session::has('failure_message'))
            <script>
                $(function () {
                    toastr.error("{{ Session::get('failure_message') }}");
                })
            </script>
        @endif
    </div>
    <div class="col-xl-12 mb-30">
        <div class="card card-statistics h-100">
            @if(auth()->user()->current_role == 'أمير المركز' ||
                auth()->user()->current_role == 'مشرف الإختبارات')
                <div class="card-body">
                    <br>
                    @can('قائمة اعدادات الإختبارات')
                        <div class="col-xl-12 mb-30">
                            <div class="card card-statistics h-100">
                                <div class="card-body">
                                    <h5 class="card-title">اعدادات الإختبارات القرآنية</h5>
                                    <div class="accordion plus-icon shadow">
                                        <div class="acd-group {{$isOpenTabFirst == true ? 'acd-active':''}}">
                                            <a wire:click.prevent="setOpenTab(1)"
                                               class="acd-heading">الإعدادات
                                                العامة</a>
                                            <div class="acd-des"
                                                 style="{{$isOpenTabFirst == false ? 'display: none;':''}}">
                                                @include('pages.exams_settings.exams_settings_public')
                                            </div>
                                        </div>

                                        <div class="acd-group {{$isOpenTabSecond == true ? 'acd-active':''}}">
                                            <a wire:click.prevent="setOpenTab(2)" class="acd-heading">تخصيص عدد أسئلة
                                                الإختبار لأجزاء محددة</a>
                                            <div class="acd-des"
                                                 style="{{$isOpenTabSecond == false ? 'display: none;':''}}">
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
</div>
