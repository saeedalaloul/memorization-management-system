<div wire:ignore.self class="modal" tabindex="-1" role="dialog" id="move-student" style="display: none;"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">نقل طالب</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body p-20">
                <form>
                    <div class="row">
                        <div class="col-md-12">
                            <label class="control-label">اسم الطالب</label>
                            <input type="text" wire:model.defer="student_name" readonly class="form-control">
                            @error('student_id')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label class="control-label">اسم المرحلة الحالية</label>
                            <input type="text" wire:model.defer="grade_name" readonly class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="control-label">اسم المحفظ الحالي</label>
                            <input type="text" wire:model.defer="teacher_name" readonly class="form-control">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label class="control-label">اختر المرحلة الجديدة</label>
                            <select
                                {{$current_role === \App\Models\User::SUPERVISOR_ROLE ? 'disabled':''}} class="form-control form-white"
                                wire:model.defer="grade_id" id="new_grade" style="padding: 1px">
                                @if($current_role === \App\Models\User::SUPERVISOR_ROLE)
                                    <option selected value="{{$grade_id}}">{{$grade_name}}</option>
                                @else
                                    <option selected value="">اختر المرحلة الجديدة...</option>
                                    @if (isset($grades))
                                        @foreach($grades as $grade)
                                            <option value="{{$grade->id}}">{{$grade->name}}</option>
                                        @endforeach
                                    @endif
                                @endif
                            </select>
                            @error('grade_id')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="control-label">اختر المحفظ الجديد</label>
                            <select class="form-control form-white" wire:model.defer="group_id" style="padding: 1px;">
                                <option selected value="">اختر المحفظ الجديد...</option>
                                @foreach ($groups as $group)
                                    @if($group->id !== $ret_group_id)
                                        <option value="{{ $group->id }}">
                                            @if ($group->teacher_id === null)
                                                {{$group->name . ' (لا يوجد محفظ)'}}
                                            @else
                                                {{ $group->teacher->user->name }}
                                            @endif
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            @error('group_id')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger ripple" data-dismiss="modal">إغلاق</button>
                <button type="button" wire:click="move();" class="btn btn-success ripple">
                    نقل الطالب
                </button>
            </div>
        </div>
    </div>
</div>
@push('js')
    <script>
        $("#new_grade").on('change', function (e) {
            let id = $(this).val()
        @this.set('grade_id', id);
            livewire.emit('getGroupsByGradeId', id);
        });
    </script>
@endpush
