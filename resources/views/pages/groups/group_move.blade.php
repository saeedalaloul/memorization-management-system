<div class="col-md-6">
    <div class="form-row">
        <div class="col">
            <label for="title">اسم الحلقة</label>
            <input type="text" name="name" class="form-control" wire:model.defer="name" readonly>
            @error('name')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <br>
    <div class="form-row">
        <div class="col">
            <label for="inputGrade">المرحلة الحالية</label>
            <input type="text" class="form-control" name="grade_id" value="{{$retGroup->grade->name}}" readonly>
            @error('grade_id')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group col">
            <label for="inputGrade">المرحلة الجديدة</label>
            <select style="width: 100%;" class="custom-select my-1 mr-sm-2" id="new_grade" wire:model="new_grade_id">
                <option selected value="">اختيار من القائمة...</option>
                @foreach($grades as $grade)
                    @if ($grade->id != $grade_id)
                    <option value="{{$grade->id}}">{{$grade->name}}</option>
                    @endif
                @endforeach
            </select>
            @error('new_grade_id')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <button wire:click.prevent="validateMoveGroup()"
            class="btn btn-success btn-sm nextBtn btn-lg pull-right"
            type="button">نقل الحلقة
    </button>
</div>
