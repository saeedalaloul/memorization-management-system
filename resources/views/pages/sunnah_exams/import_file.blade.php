<div wire:ignore.self class="modal" tabindex="-1" role="dialog" id="import-file" style="display: none;"
     aria-hidden="true">
    <div class="modal-dialog">
       <form>
           <div class="modal-content">
               <div class="modal-header">
                   <h5 class="modal-title">استيراد ملف اكسل</h5>
                   <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
               </div>
               <div class="modal-body p-20">
                   <div class="mb-3">
                       <label class="form-label">ملف اكسل</label>
                       <input type="file" class="form-control @error('file') is-invalid @enderror" wire:model="file">
                       <small>ملاحظة<b class="text-danger">*</b> : نوع الملف xlsx,xls</small>
                       @error('file')
                       <div id="validation" class="invalid-feedback">{{ $message }}</div>
                       @enderror
                   </div>
                   <a href="" class="btn btn-primary" wire:click.prevent="export_external_exams();">تصدير الإختبارات
                       (الخارجية)</a>
               </div>
               <div class="modal-footer">
                   <button type="button" class="btn btn-danger ripple" data-dismiss="modal">إغلاق</button>
                   <button type="submit" class="btn btn-success ripple">
                       استيراد
                   </button>
               </div>
           </div>
       </form>
    </div>
</div>
