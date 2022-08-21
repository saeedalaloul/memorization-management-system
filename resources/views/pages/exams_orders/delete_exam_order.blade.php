<!-- delete_modal_Group -->
<div class="modal fade" id="delete-exam-order" tabindex="-1" role="dialog"
     aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 style="font-family: 'Cairo', sans-serif;" class="modal-title"
                    id="exampleModalLabel">
                    حذف طلب الإختبار
                </h5>
                <button type="button" class="close" data-dismiss="modal"
                        aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="text-dark modal-body">
                هل أنت متاكد من عملية الحذف ؟
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">اغلاق
                    </button>
                    <button type="button" wire:click = "destroy('{{$exam_order->id}}')"
                            class="btn btn-danger">حذف
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
