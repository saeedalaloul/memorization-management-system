<!-- add_modal_group -->
<div wire:ignore.self class="modal fade" id="add_tester" tabindex="-1" role="dialog"
     aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 style="font-family: 'Cairo', sans-serif;" class="modal-title" id="exampleModalLabel">
                    اضافة مختبر
                </h5>
                <button type="button" wire:click.prevent="modalFormReset()" class="close" data-dismiss="modal"
                        aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

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

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                    wire:click.prevent="modalFormReset()"
                                    data-dismiss="modal">اغلاق
                            </button>
                            <button type="button" wire:click.prevent="store()"
                                    class="btn btn-success">حفظ
                            </button>
                        </div>
                    </div>
            </div>
        </div>

    </div>

</div>
