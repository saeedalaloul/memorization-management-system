<div wire:ignore.self class="modal" tabindex="-1" role="dialog" id="remove-group-custom" style="display: none;"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">حذف حلقات من الإجراء</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <form wire:submit.prevent="approval_on_group();" autocomplete="off">

                <div class="modal-body p-20">
                    <div class="row">

                        <div class="col-md-6">
                            <label class="control-label">نوع الإجراء العقابي</label>
                            @if ($selectedPunitiveMeasure === \App\Models\PunitiveMeasure::BLOCK_TYPE)
                                <input type="text" value="حظر" readonly class="form-control">
                            @else
                                <input type="text" value="إنذار" readonly class="form-control">
                            @endif
                            @error('selectedPunitiveMeasure')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="control-label">سبب الإجراء العقابي</label>
                            @if ($selectedReason === \App\Models\PunitiveMeasure::MEMORIZE_REASON)
                                <input type="text" value="بسبب ضعف الحفظ" readonly class="form-control">
                            @elseif($selectedReason === \App\Models\PunitiveMeasure::ABSENCE_REASON)
                                <input type="text" value="بسبب الغياب" readonly class="form-control">
                            @else
                                <input type="text" value="بسبب التأخر" readonly class="form-control">
                            @endif
                            @error('selectedReason')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="control-label">اختر المرحلة</label>
                            <select style="padding: 1px;" class="form-control form-white"
                                    wire:model.defer="selectedGradeId" id="grade">
                                <option selected value="">اختر المرحلة...</option>
                                @if (isset($grades))
                                    @foreach($grades as $grade)
                                        <option value="{{$grade->id}}">{{$grade->name}}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('selectedGradeId')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">اختر الحلقات</label>
                                <select style="width: 100%;" multiple class="select2">
                                    @if (isset($groups))
                                        @foreach($groups as $group)
                                            @if (isset($group->punitive_measures) && count($group->punitive_measures) > 0 && $process_type == 'remove')
                                                @if($group->teacher_id !== null)
                                                    <option selected
                                                            value="{{$group->id}}">{{$group->teacher->user->name}}</option>
                                                @endif
                                            @else
                                                @if($group->teacher_id !== null)
                                                    <option
                                                        value="{{$group->id}}">{{$group->teacher->user->name}}</option>
                                                @endif
                                            @endif
                                        @endforeach
                                    @endif
                                </select>
                                @error('groups_ids')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-danger ripple" data-dismiss="modal">إغلاق</button>
                    <button type="submit" class="btn btn-success ripple">
                        اعتماد الإجراء على الحلقات
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@push('js')
    <script>
        $("#grade").on('change', function (e) {
            let id = $(this).val()
        @this.set('selectedGradeId', id);
            livewire.emit('getTeachersByGradeId');
        });

        $('form').submit(function () {
        @this.set('groups_ids', $('.select2').val());
        })

    </script>
@endpush
