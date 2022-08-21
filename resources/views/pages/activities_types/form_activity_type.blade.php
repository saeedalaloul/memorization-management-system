<div class="row">
    <div class="col-md-12 mb-30">
        <div class="card card-statistics h-100">
            <div class="card-body">
                <div class="col-xs-12">
                    <div class="col-md-12">
                        <div class="form-row">
                            <div class="col">
                                <label for="name">اسم نوع النشاط</label>
                                <input type="text" name="name" class="form-control" wire:model.defer="name" required>
                                @error('name')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col">
                                <label for="place">مكان النشاط</label>
                                <input type="text" name="place" class="form-control" wire:model.defer="place" required>
                                @error('place')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="card-body datepicker-form">
                                <h5 class="card-title">تاريخ النشاط</h5>
                                <div class="input-group">
                                    <input type="date" wire:model.defer="start_date" class="form-control">
                                    <span class="input-group-addon">إلى</span>
                                    <input class="form-control" type="date" wire:model.defer="end_date">
                                </div>
                                @error('start_date')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                                @error('end_date')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <br>

                        @if (!empty($modalId))
                            <button wire:click.prevent="update()"
                                    class="btn btn-success btn-sm nextBtn btn-lg pull-right"
                                    type="button">تحديث نوع النشاط
                            </button>
                        @else
                            <button wire:click.prevent="store()"
                                    class="btn btn-success btn-sm nextBtn btn-lg pull-right"
                                    type="button">حفظ نوع النشاط
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
