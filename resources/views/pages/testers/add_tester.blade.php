<div class="card-body">
    <div class="row">
        <div class="form-group">
            <div class="col">
                <label for="name"
                       class="mr-sm-2">اختر المرحلة
                    :</label>

                <div class="box">
                    <select class="fancyselect" name="grade_id" wire:model="grade_id">
                        <option selected>اختيار من القائمة...</option>
                        @foreach ($grades as $grade)
                            <option
                                value="{{ $grade->id }}">{{ $grade->name }}</option>
                        @endforeach
                    </select>
                    @error('grade_id')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>

            </div>
        </div>

        <div class="form-group">
            <div class="col">
                <label for="name"
                       class="mr-sm-2">اختر المحفظ
                    :</label>

                <div class="box">
                    <select class="fancyselect" name="teacher_id" wire:model="teacher_id">
                        <option selected>اختيار من القائمة...</option>
                        @if (!is_null($teachers))
                            @foreach ($teachers as $teacher)
                                <option
                                    value="{{ $teacher->id }}">{{ $teacher->user->name }}</option>
                            @endforeach
                        @endif
                    </select>
                    @error('teacher_id')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>

            </div>
        </div>
    </div>
    <button wire:click.prevent="store()"
            class="btn btn-success btn-sm nextBtn btn-lg pull-right"
            type="button">حفظ البيانات
    </button>
</div>
