<div class="form-group">
    <label for="exampleFormControlSelect2">موضوع الشكوى/الاقتراح</label>
    <input class="form-control" value="{{$boxComplaintSuggestion->subject}}" readonly/>
</div>

<div class="form-group">
    <label for="exampleFormControlSelect2">الرد على الشكوى/الاقتراح</label>
    <textarea wire:model.defer="reply" class="form-control"
              id="summernote"></textarea>
    @error('reply')
    <div class="alert alert-danger">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <a class="button button-border" wire:click="storeReplyComplaint();">الرد على الشكوى/الاقتراح</a>
</div>
