<!-- Deleted inFormation Student -->
<div wire:ignore.self class="modal fade" id="delete_student" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 style="font-family: 'Cairo', sans-serif;" class="modal-title" id="exampleModalLabel">حذف الطالب</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                    <h5 style="font-family: 'Cairo', sans-serif;">هل أنت متأكد من عملية حذف الطالب ؟</h5>
                    <input type="text" readonly wire:model="student_name"  class="form-control">

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">إغلاق</button>
                        <button  class="btn btn-danger" wire:click="delete({{ $student->id }})">حذف</button>
                    </div>
            </div>
        </div>
    </div>
</div>
