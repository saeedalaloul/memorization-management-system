<!-- delete_modal_Group -->
<div class="modal fade" id="groupDeleted" tabindex="-1" role="dialog"
     aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 style="font-family: 'Cairo', sans-serif;" class="modal-title"
                    id="exampleModalLabel">
                    حذف الحلقة
                </h5>
                <button type="button" class="close" data-dismiss="modal"
                        aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                هل انت متاكد من عملية الحذف ؟
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">اغلاق
                    </button>
                    <button type="button" wire:click.prevent = "destroy()"
                            class="btn btn-danger">حذف
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
