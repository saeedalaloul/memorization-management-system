<div class="col-md-6">
    <div class="form-row">
        <div class="col">
            <label for="title">اسم الحلقة</label>
            <input type="text" name="name" class="form-control" wire:model="name" required>
            @error('name')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <br>
    <div class="form-row">
        <div class="col">
            <label for="inputGrade">اسم المرحلة</label>
            <select class="custom-select my-1 mr-sm-2" name="grade_id" wire:model="grade_id">
                <option selected>اختيار من القائمة...</option>
                @foreach($grades as $grade)
                    <option value="{{$grade->id}}">{{$grade->name}}</option>
                @endforeach
            </select>
            @error('grade_id')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="col">
            <label for="inputGrade">اسم المحفظ</label>
            <select class="custom-select my-1 mr-sm-2" name="teacher_id" wire:model="teacher_id">
                <option selected>اختيار من القائمة...</option>
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
