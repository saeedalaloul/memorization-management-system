<div wire:ignore.self class="modal" tabindex="-1" role="dialog" id="add-exam" style="display: none;" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">اجراء طلب اختبار</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body p-20">
                <form>
                    <div class="row">
                        <div class="col">
                            <label class="control-label">اختر نوع طلب الاختبار</label>
                            <select class="form-control form-white" wire:model="quran_part_type"
                                    data-placeholder="اختر نوع طلب الاختبار..." name="quran_part_type" style="padding: 1px">
                                <option selected value="">اختر نوع طلب الاختبار...</option>
                                <option value="1">اختبار منفرد</option>
                                <option value="2">اختبار تجميعي</option>
                            </select>
                            @error('quran_part_type')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="control-label">اسم الطالب</label>
                            <input type="text" wire:model="student_name" readonly class="form-control">
                            @error('student_id')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="control-label">اختر الجزء</label>
                            <select class="form-control form-white" wire:model="quran_part_id"
                                    data-placeholder="اختر الجزء..." name="quran_part" style="padding: 1px">
                                <option selected value="">اختر الجزء...</option>
                                @if (isset($quran_parts) && count($quran_parts) == 2)
                                    @if ($quran_part_type == 1)
                                        <option value="{{$quran_parts['id']}}">{{$quran_parts['name']}}</option>
                                    @elseif($quran_part_type == 2)
                                        <option
                                            value="{{$quran_parts['id']}}">{{$quran_parts->quransummativepartname()}}</option>
                                    @endif
                                @elseif (isset($quran_parts))
                                    @foreach($quran_parts as $quran_part)
                                        @if ($quran_part_type == 1)
                                            <option value="{{$quran_part->id}}">{{$quran_part->name}}</option>
                                        @elseif($quran_part_type == 2)
                                            <option
                                                value="{{$quran_part->id}}">{{$quran_part->quransummativepartname()}}</option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                            @error('quran_part_id')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger ripple" data-dismiss="modal">إغلاق</button>
                @if ($quran_part_type == 1)
                    <button type="button" wire:click="submitExamRequest({{$student->id}})"
                            class="btn btn-success ripple">
                        طلب اختبار
                    </button>
                @elseif($quran_part_type == 2)
                    <button type="button" wire:click="submitSummativeExamRequest({{$student->id}})"
                            class="btn btn-success ripple">
                         طلب اختبار تجميعي
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
