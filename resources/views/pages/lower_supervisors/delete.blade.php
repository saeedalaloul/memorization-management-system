<div class="modal fade" id="delete_LowerSupervisor" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                <div class="modal-header">
                    <h5 style="font-family: 'Cairo', sans-serif;" class="modal-title" id="exampleModalLabel">حذف الإداري</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p> هل أنت متأكد من عملية حذف الإداري ؟</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">اغلاق</button>
                    <button type="button" wire:click.prevent = "destroy({{$lower_supervisor->id}})"
                            class="btn btn-danger">حذف</button>
                </div>
            </div>
    </div>
</div>
