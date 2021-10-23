<div class="table-responsive mt-15">
    <table class="table center-aligned-table mb-0">
        <thead>
        <tr class="table-success">
            <th>#</th>
            <th>اسم الجزء</th>
            <th>عدد أسئلة الإختبار</th>
            <th>العمليات</th>
        </tr>
        </thead>
        <tbody>
        @forelse($exam_custom_questions as $exam_custom_question)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $exam_custom_question->quranPart->name }}</td>
                <td>
                    @if ($updateMode == true && $modalId == $exam_custom_question->id)
                        <input type="number" wire:keydown.enter="update()" style="width: 100px;" min="7"
                               wire:model="exam_question_count_update"
                               class="form-control">
                        @error('exam_question_count_update')
                        <div style="width: 100px;" class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    @else
                        {{ $exam_custom_question->exam_question_count }}
                    @endif
                </td>
                <td>
                    @if ($updateMode == true && $modalId == $exam_custom_question->id)
                        <button type="button" class="btn btn-dark btn-sm"
                                wire:click.prevent="resetInputFieldsUpdated()"
                                title="إلغاء"><i class="fa fa-close"></i>
                            @else
                                <button type="button" class="btn btn-info btn-sm"
                                        wire:click.prevent="edit({{$exam_custom_question->id}},{{$exam_custom_question->exam_question_count}})"
                                        title="تعديل"><i class="fa fa-edit"></i>
                                    @endif
                                </button>
                                <button type="button" wire:click.prevent="destroy({{$exam_custom_question->id}})"
                                        class="btn btn-danger btn-sm" title="حذف"><i class="fa fa-trash"></i>
                                </button>
                </td>
            </tr>
        @empty
            <tr style="text-align: center">
                <td colspan="3">No data available in table</td>
            </tr>
        @endforelse
        </tbody>
        <tfoot>
        <tr class="table-success">
            <th>#</th>
            <th>اسم الجزء</th>
            <th>عدد أسئلة الإختبار</th>
            <th>العمليات</th>
        </tr>
        </tfoot>
    </table>
</div>

<form>
    <div class="col-xs-12">
        <div class="col-md-12">
            <br>
            <div class="form-row">
                <div class="col">
                    <label for="quran_part_id">اختر الجزء</label>
                    <select class="form-control form-white" style="padding: 10px" wire:model="quran_part_id">
                        <option selected>اختر الجزء...</option>
                        @foreach($quran_parts as $quran_part)
                            <option value="{{$quran_part->id}}">{{$quran_part->name}}</option>
                        @endforeach
                    </select>
                    @error('quran_part_id')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col">
                    <label for="exam_question_count">عدد أسئلة الإختبار</label>
                    <input type="number" min="7" wire:model="exam_question_count" class="form-control">
                    @error('exam_question_count')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <button type="button" wire:click.prevent="storeExamsCustomQuestion()" class="btn btn-success btn-sm">حفظ
                </button>
            </div>
        </div>
    </div>
</form>
