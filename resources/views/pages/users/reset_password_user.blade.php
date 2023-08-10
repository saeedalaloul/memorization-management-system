<!-- Deleted inFormation Student -->
<div wire:ignore.self class="modal fade" id="reset-user-password" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 style="font-family: 'Cairo', sans-serif;" class="modal-title" id="exampleModalLabel">إعادة تعيين كلمة المرور</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <h5 style="font-family: 'Cairo', sans-serif;">هل أنت متأكد من عملية إعادة تعيين كلمة المرور ؟</h5>
                <input type="text" readonly wire:model="name"  class="form-control">

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إغلاق</button>
                    <button  class="btn btn-danger" wire:click="resetPasswordUser();">إعادة تعيين كلمة المرور</button>
                </div>
            </div>
        </div>
    </div>
</div>
