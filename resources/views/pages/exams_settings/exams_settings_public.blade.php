<div class="col-md-12">
    <div class="text-dark form-row">
        <div class="col">
            <div class="custom-control custom-checkbox mb-3">
                <input type="checkbox" class="custom-control-input"
                       wire:model="allow_exams_update"
                       id="customControlValidation1" required>
                <label class="custom-control-label"
                       for="customControlValidation1">السماح للمختبرين
                    بالتعديل على الإختبارات</label>
            </div>
        </div>
    </div>
</div>
<div class="col-md-12">
    <div class="text-dark form-row">
        <div class="col">
            <label for="exam_questions_min">أدنى عدد أسئلة
                اختبار</label>
            <input type="number" name="exam_questions_min" max="10"
                   min="7"
                   class="form-control" wire:model="exam_questions_min"
                   required>
            @error('exam_questions_min')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="col">
            <label for="exam_questions_max">أقصى عدد أسئلة
                اختبار</label>
            <input type="number" name="exam_questions_max"
                   max="10" min="7" wire:model="exam_questions_max"
                   class="form-control" required>
            @error('exam_questions_max')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
<br>
<div class="col-md-12">
    <div class="text-dark form-row">
        <div class="col">
            <label for="exam_questions_summative_three_part"> عدد أسئلة
                اختبار التجميعي (3) أجزاء</label>
            <input type="number" max="4" wire:model="exam_questions_summative_three_part"
                   min="3" class="form-control" required>
            @error('exam_questions_summative_three_part')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="col">
            <label for="exam_questions_summative_five_part"> عدد أسئلة
                اختبار التجميعي (5) أجزاء</label>
            <input type="number" max="6" wire:model="exam_questions_summative_five_part"
                   min="5" class="form-control" required>
            @error('exam_questions_summative_five_part')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="col">
            <label for="exam_questions_summative_ten_part"> عدد أسئلة
                اختبار التجميعي (10) أجزاء</label>
            <input type="number" max="8" wire:model="exam_questions_summative_ten_part"
                   min="7" class="form-control" required>
            @error('exam_questions_summative_ten_part')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="col">
            <label for="exam_questions_summative_fifteen_part"> عدد أسئلة
                اختبار التجميعي (15) أجزاء</label>
            <input type="number" max="11" wire:model="exam_questions_summative_fifteen_part"
                   min="10" class="form-control" required>
            @error('exam_questions_summative_fifteen_part')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="col-md-12">
    <div class="text-dark form-row">
        <div class="col">
            <br>
            <label for="number_days_exam">حدد عدد الأيام بين طلبات
                الإختبارات للطالب الذي لم يجتاز</label>
            <input type="number" name="number_days_exam"
                   max="15" min="1" wire:model="number_days_exam"
                   class="form-control" required>
            @error('number_days_exam')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="col">
            <br>
            <label for="exam_success_rate">نسبة النجاح في
                الإختبارات (المنفردة)</label>
            <input type="number" name="exam_success_rate"
                   max="90" min="80" wire:model="exam_success_rate"
                   class="form-control" required>
            @error('exam_success_rate')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="col">
            <br>
            <label for="exam_success_rate">نسبة النجاح في
                الإختبارات (التجميعي)</label>
            <input type="number" name="exam_success_rate"
                   max="90" min="80" class="form-control" required>
            @error('exam_success_rate')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <br>
    <button wire:click="store()"
            class="btn btn-success btn-sm nextBtn btn-lg pull-right"
            type="button">حفظ اعدادات الإختبارات
    </button>
    <br>
</div>
