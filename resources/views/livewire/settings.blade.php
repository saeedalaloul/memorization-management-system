<div>
    @if ($catchError)
        <div class="alert alert-danger" id="success-danger">
            <button type="button" wire:click.prevent="resetMessage()" class="close" data-dismiss="alert">x</button>
            {{ $catchError }}
        </div>
    @endif
    <div class="col-xl-12 mb-30">
        <div class="card card-statistics h-100">
            <div class="card-body">
                <h5 class="card-title">الإعدادات</h5>
                <div class="tab tab-border">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active show" href="#" id="settings-05-tab"
                               data-bs-toggle="tab" role="tab"
                               aria-controls="settings-05" aria-selected="true"> <i
                                    class="fas fa-cogs"></i> قائمة
                                الإعدادات</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade active show" id="settings-05"
                             role="tabpanel"
                             aria-labelledby="settings-05-tab">
                            <!-- row -->
                            <div class="row">
                                <div class="col-md-12 mb-30">
                                    <div class="card-body">
                                        <form enctype="multipart/form-data">
                                            <div class="row">
                                                <div class="col-md-6 border-right-2 border-right-blue-400">
                                                    <div class="form-group row">
                                                        <label class="col-lg-2 col-form-label font-weight-semibold">اسم
                                                            المركز<span class="text-danger">*</span></label>
                                                        <div class="col-lg-9">
                                                            <input wire:model="center_name" type="text"
                                                                   class="form-control"
                                                                   placeholder="Name of Center">
                                                            @error('center_name')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label for="current_session"
                                                               class="col-lg-2 col-form-label font-weight-semibold">العام
                                                            الحالي<span class="text-danger">*</span></label>
                                                        <div class="col-lg-9">
                                                            <select wire:model="current_session"
                                                                    class="select-search form-control"
                                                                    style="padding: 1px;">
                                                                <option value=""></option>
                                                                @for($y=date('Y', strtotime('- 3 years')); $y<=date('Y', strtotime('+ 1 years')); $y++)
                                                                    <option {{ ($current_session == (($y-=1).'-'.($y+=1))) ? 'selected' : '' }}>{{ ($y-=1).'-'.($y+=1) }}</option>
                                                                @endfor
                                                            </select>
                                                            @error('current_session')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-lg-2 col-form-label font-weight-semibold">اسم
                                                            المركز المختصر</label>
                                                        <div class="col-lg-9">
                                                            <input wire:model="center_title"
                                                                   type="text" class="form-control"
                                                                   placeholder="Center Acronym">
                                                            @error('center_title')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-lg-2 col-form-label font-weight-semibold">رقم
                                                            الجوال</label>
                                                        <div class="col-lg-9">
                                                            <input wire:model="phone" type="text"
                                                                   class="form-control" placeholder="Phone">
                                                            @error('phone')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-lg-2 col-form-label font-weight-semibold">البريد
                                                            الالكتروني</label>
                                                        <div class="col-lg-9">
                                                            <input wire:model="center_email"
                                                                   type="email" class="form-control"
                                                                   placeholder="Center Email">
                                                            @error('center_email')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-lg-2 col-form-label font-weight-semibold">عنوان
                                                            المركز<span class="text-danger">*</span></label>
                                                        <div class="col-lg-9">
                                                            <input wire:model="address" type="text"
                                                                   class="form-control"
                                                                   placeholder="Center Address">
                                                            @error('address')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-lg-2 col-form-label font-weight-semibold">نهاية
                                                            الفصل الأول </label>
                                                        <div class="col-lg-9">
                                                            <input wire:model="end_first_term" type="text"
                                                                   class="form-control date-pick"
                                                                   placeholder="Date Term Ends">
                                                            @error('end_first_term')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-lg-2 col-form-label font-weight-semibold">نهاية
                                                            الفصل الثاني</label>
                                                        <div class="col-lg-9">
                                                            <input wire:model="end_second_term" type="text"
                                                                   class="form-control date-pick"
                                                                   placeholder="Date Term Ends">
                                                            @error('end_second_term')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label class="col-lg-2 col-form-label font-weight-semibold">شعار
                                                            المركز</label>
                                                        <div class="col-lg-9">
                                                            <div class="mb-3">
                                                                <img style="width: 100px" height="100px"
                                                                     src="{{ URL::asset('attachments/logo/'.$logo_ret) }}"
                                                                     alt="">
                                                            </div>
                                                            <input wire:model="logo" name="logo" accept="image/*"
                                                                   type="file"
                                                                   class="file-input" data-show-caption="false"
                                                                   data-show-upload="false" data-fouc>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                            <button wire:click.prevent="update();"
                                                    class="btn btn-success btn-sm nextBtn btn-lg pull-right">تأكيد
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <!-- row closed -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <x-loading-indicator/>
</div>
