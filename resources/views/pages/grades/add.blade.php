<!-- add_modal_Grade -->
<div wire:ignore.self class="modal fade" id="gradeAdded" tabindex="-1" role="dialog"
     aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 style="font-family: 'Cairo', sans-serif;" class="modal-title"
                    id="exampleModalLabel">
                    إضافة مرحلة
                </h5>
                <button type="button" class="close" wire:click.prevent = "modalFormReset()" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- add_form -->
                <form>
                    <div class="row">
                        <div class="col">
                            <label for="name"
                                   class="mr-sm-2">اسم المرحلة
                                :</label>
                            <input id="name" type="text" name="name" wire:model.defer="name" class="form-control">
                            @error('name')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col">
                            <label for="inputSection">اختر قسم المرحلة</label>
                            <select class="form-control form-white" wire:model.defer="section"
                                    style="padding: 1px">
                                <option selected value="">اختر قسم المرحلة</option>
                                @foreach(\App\Models\Grade::sections() as $section => $value)
                                    <option
                                        value="{{$section}}">{{$value}}</option>
                                @endforeach
                            </select>
                            @error('section')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <br><br>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                        wire:click.prevent = "modalFormReset()"
                        data-dismiss="modal">اغلاق</button>
                <button type="button" wire:click.prevent="store()"
                        class="btn btn-success">حفظ</button>
            </div>
            </form>

        </div>
    </div>
</div>
</div>
