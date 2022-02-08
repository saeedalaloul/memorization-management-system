<!-- edit_modal_Grade -->
<div wire:ignore.self class="modal fade" id="complaintBoxCategoryEdited" tabindex="-1" role="dialog"
     aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 style="font-family: 'Cairo', sans-serif;" class="modal-title"
                    id="exampleModalLabel">
                    تعديل التصنيف
                </h5>
                <button type="button" wire:click.prevent="modalFormReset()" class="close" data-dismiss="modal"
                        aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- add_form -->
                <div class="row">
                    <div class="col">
                        <label for="name"
                               class="mr-sm-2">اسم التصنيف
                            :</label>
                        <input id="name" type="text" name="name" wire:model="name"
                               class="form-control"
                               required>
                        @error('name')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                        <input id="id" type="hidden" name="id" class="form-control" wire:model="modalId">
                    </div>
                </div>
                <br><br>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                            data-dismiss="modal" wire:click.prevent="modalFormReset()">اغلاق
                    </button>
                    <button type="button" wire:click.prevent="update()"
                            class="btn btn-success">حفظ
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>