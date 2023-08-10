<div class="row">
    <div class="col-xl-12 mb-30">
        <div class="card card-statistics h-100">
            <div class="col-xs-12">
                <div class="card-body">
                    <h5 class="card-title">تقديم شكوى/اقتراح </h5>
                    <div class="form-group">
                        <label for="exampleFormControlSelect1">حدد نوع الشكوى/الاقتراح</label>
                        <select style="padding: 1px;" wire:model.defer="category" class="form-control"
                                id="exampleFormControlSelect1">
                            <option value="">حدد نوع الشكوى/الاقتراح</option>
                            @foreach(\App\Models\BoxComplaintSuggestion::categories() as $category => $value)
                                <option value="{{$category}}">{{$value}}</option>
                            @endforeach
                        </select>
                        @error('category')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="exampleFormControlSelect2">حدد جهة الشكوى/الاقتراح</label>
                        <select style="padding: 1px;" wire:model.defer="role_id" multiple class="form-control"
                                id="exampleFormControlSelect2">
                            @foreach($roles as $role)
                                <option value="{{$role->id}}">{{$role->name}}</option>
                            @endforeach
                        </select>
                        @error('role_id')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group" wire:ignore>
                        <label>موضوع الشكوى/الاقتراح</label>
                        <textarea wire:model="subject" data-subject="@this" class="form-control"
                                  id="subject"></textarea>
                    </div>
                    @error('subject')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                    <div class="form-group">
                        <a class="button button-border" id="submit" wire:click="store()">تقديم الشكوى/الاقتراح</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('js')
    <script src="https://cdn.ckeditor.com/ckeditor5/33.0.0/classic/ckeditor.js"></script>
    <script>
        ClassicEditor
            .create(document.querySelector('#subject'))
            .then(editor => {
                document.querySelector('#submit').addEventListener('click', () => {
                    let subject = $('#subject').data('subject');
                    eval(subject).set('subject', editor.getData());
                });
            })
            .catch(error => {
                console.error(error);
            });
    </script>
@endpush
