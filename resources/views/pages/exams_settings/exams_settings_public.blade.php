<div class="col-md-12">
    <div class="text-dark form-row">
        <div class="col">
            <label for="exam_questions_min">أدنى عدد أسئلة
                اختبار</label>
            <input type="number" name="exam_questions_min" max="10"
                   min="7"
                   class="form-control" wire:model.defer="exam_questions_min"
                   required>
            @error('exam_questions_min')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="col">
            <label for="exam_questions_max">أقصى عدد أسئلة
                اختبار</label>
            <input type="number" name="exam_questions_max"
                   max="10" min="7" wire:model.defer="exam_questions_max"
                   class="form-control" required>
            @error('exam_questions_max')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="col">
            <label class="control-label">اختر أيام لجنة الإختبارات</label>
            <select multiple class="form-control form-white" wire:model.defer="suggested_exam_days"
                    name="suggested_exam_days[]" style="padding: 1px">
                @foreach(\App\Models\ExamSettings::days() as $day => $value)
                    <option value="{{$day}}">{{$value}}</option>
                @endforeach
            </select>
            @error('suggested_exam_days')
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
            <input type="number" max="6" wire:model.defer="exam_questions_summative_three_part"
                   min="3" class="form-control" required>
            @error('exam_questions_summative_three_part')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="col">
            <label for="exam_questions_summative_five_part"> عدد أسئلة
                اختبار التجميعي (5) أجزاء</label>
            <input type="number" max="8" wire:model.defer="exam_questions_summative_five_part"
                   min="4" class="form-control" required>
            @error('exam_questions_summative_five_part')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="col">
            <label for="exam_questions_summative_ten_part"> عدد أسئلة
                اختبار التجميعي (10) أجزاء</label>
            <input type="number" max="10" wire:model.defer="exam_questions_summative_ten_part"
                   min="5" class="form-control" required>
            @error('exam_questions_summative_ten_part')
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
                   max="15" min="1" wire:model.defer="number_days_exam"
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
                   max="90" min="80" wire:model.defer="exam_success_rate"
                   class="form-control" required>
            @error('exam_success_rate')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="col">
            <br>
            <label for="exam_success_rate">نسبة النجاح في
                الإختبارات (التجميعي)</label>
            <input type="number" name="summative_exam_success_rate" wire:model.defer="summative_exam_success_rate"
                   max="90" min="80" class="form-control" required>
            @error('summative_exam_success_rate')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <br>
</div>
<div class="col-md-12">
    <div class="text-dark form-row">
        <div class="col">
            <br>
            <label for="number_days_exam_two_left">حدد عدد أيام التجميد في حال رسب الطالب مرتين</label>
            <input type="number" name="number_days_exam_two_left"
                   max="20" min="1" wire:model.defer="number_days_exam_two_left"
                   class="form-control" required>
            @error('number_days_exam_two_left')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="col">
            <br>
            <label for="number_days_exam_three_left">حدد عدد أيام التجميد في حال رسب الطالب ثلاث مرات أو أكثر</label>
            <input type="number" name="number_days_exam_three_left"
                   max="30" min="1" wire:model.defer="number_days_exam_three_left"
                   class="form-control" required>
            @error('number_days_exam_three_left')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <br>
</div>


<div class="col-md-12">
    <div class="text-dark form-row">
        <div class="col">
            <label for="number_days_exam">حدد عدد الأيام بين طلبات
                اختبارات السنة</label>
            <input type="number" name="number_days_exam_sunnah"
                   max="15" min="1" wire:model.defer="number_days_exam_sunnah"
                   class="form-control" required>
            @error('number_days_exam_sunnah')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="col">
            <label for="exam_sunnah_questions">عدد أسئلة اختبارات السنة (المنفردة)</label>
            <input type="number" name="exam_sunnah_questions" max="12"
                   min="7"
                   class="form-control" wire:model.defer="exam_sunnah_questions"
                   required>
            @error('exam_sunnah_questions')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="col">
            <label for="exam_sunnah_questions_summative">عدد أسئلة اختبارات السنة (التجميعي)</label>
            <input type="number" name="exam_sunnah_questions_summative"
                   max="12" min="7" wire:model.defer="exam_sunnah_questions_summative"
                   class="form-control" required>
            @error('exam_sunnah_questions_summative')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="col">
            <label for="exam_sunnah_success_rate">نسبة النجاح في
                اختبارات السنة</label>
            <input type="number" name="exam_sunnah_success_rate" wire:model.defer="exam_sunnah_success_rate"
                   max="90" min="80" class="form-control" required>
            @error('exam_sunnah_success_rate')
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
