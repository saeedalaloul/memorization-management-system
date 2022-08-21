<div class="form-group">
    <label for="exampleFormControlSelect2">ملاحظات الرقابة</label>
    <textarea wire:model.defer="notes" class="form-control"
              id="summernote"></textarea>
    @error('notes')
    <div class="alert alert-danger">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="exampleFormControlSelect2">الاقتراحات</label>
    <textarea wire:model.defer="suggestions" class="form-control"
              id="summernote1"></textarea>
    @error('suggestions')
    <div class="alert alert-danger">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    @if($visit->hostable_type == 'App\Models\Teacher')
        <label for="exampleFormControlSelect2">توصيات المحفظ</label>
    @elseif($visit->hostable_type == 'App\Models\Tester')
        <label for="exampleFormControlSelect2">توصيات المختبر</label>
    @elseif($visit->hostable_type == 'App\Models\ActivityMember')
        <label for="exampleFormControlSelect2">توصيات المنشط</label>
    @endif
    <textarea wire:model.defer="recommendations" class="form-control"
              id="summernote2"></textarea>
    @error('recommendations')
    <div class="alert alert-danger">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="exampleFormControlSelect2">رد أمير المركز</label>
    <textarea wire:model.defer="reply" class="form-control"
              id="summernote3"></textarea>
    @error('reply')
    <div class="alert alert-danger">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <a class="button button-border" wire:click="storeReplyVisit();">الرد على الزيارة</a>
</div>
