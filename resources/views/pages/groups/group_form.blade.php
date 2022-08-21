<div class="col-md-6">
    <div class="form-row">
        <div class="col">
            <label for="title">اسم الحلقة</label>
            <input type="text" name="name" class="form-control" wire:model.defer="name" required>
            @error('name')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <br>
    <div class="form-row">
        <div class="col">
            <label for="inputGrade">اختر المرحلة</label>
            <select style="width: 100%;" wire:model="grade_id" class="custom-select my-1 mr-sm-2 select2" id="grade_">
                <option value="" selected>اختر المرحلة</option>
                @foreach($grades as $grade)
                    <option value="{{$grade->id}}">{{$grade->name}}</option>
                @endforeach
            </select>
            @error('grade_id')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="col">
            <label for="inputGroup">اختر المحفظ</label>
            <select style="width: 100%;" wire:model="teacher_id" class="custom-select my-1 mr-sm-2 select2" id="teacher_">
                <option value="" selected>اختر المحفظ</option>
                @if (isset($teachers))
                    @foreach($teachers as $teacher)
                        <option value="{{$teacher->id}}">{{$teacher->user->name}}</option>
                    @endforeach
                @endif
            </select>
            @error('teacher_id')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <br>
    @if (!empty($modalId))
        <button wire:click.prevent="update()"
                class="btn btn-success btn-sm nextBtn btn-lg pull-right"
                type="button">تحديث البيانات
        </button>
    @else

        <button wire:click.prevent="store()"
                class="btn btn-success btn-sm nextBtn btn-lg pull-right"
                type="button">حفظ البيانات
        </button>
    @endif
</div>
