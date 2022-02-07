<!-- add_modal -->
<div wire:ignore.self class="modal fade" id="complaintBoxRoleAdded" tabindex="-1" role="dialog"
     aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 style="font-family: 'Cairo', sans-serif;" class="modal-title"
                    id="exampleModalLabel">
                    إضافة دور صندوق الشكاوي والإقتراحات
                </h5>
                <button type="button" class="close" wire:click.prevent="modalFormReset()" data-dismiss="modal"
                        aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- add_form -->
                <div class="row">
                    <div class="col">
                        <label for="inputRole">اسم الدور</label>
                        <select class="custom-select my-1 mr-sm-2" name="modalId" wire:model="modalId">
                            <option selected>اختيار من القائمة...</option>
                            @foreach($roles as $role)
                                <option value="{{$role->id}}">{{$role->name}}</option>
                            @endforeach
                        </select>
                        @error('modalId')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
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
