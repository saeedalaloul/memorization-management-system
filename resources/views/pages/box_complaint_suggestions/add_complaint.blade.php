<div class="row">
    <div class="col-xl-12 mb-30">
        <div class="card card-statistics h-100">
            <div class="col-xs-12">
                <div class="card-body">
                    <h5 class="card-title">تقديم شكوى/اقتراح </h5>
                    <div class="form-group">
                        <label for="exampleFormControlSelect1">حدد نوع الشكوى/الاقتراح</label>
                        <select style="padding: 1px;" class="form-control" id="exampleFormControlSelect1">
                            <option>1</option>
                            <option>2</option>
                            <option>3</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="exampleFormControlSelect2">حدد جهة الشكوى/الاقتراح</label>
                        <select style="padding: 1px;" multiple="" class="form-control" id="exampleFormControlSelect2">
                            <option>1</option>
                            <option>2</option>
                            <option>3</option>
                        </select>
                    </div>
                    <div wire:ignore class="form-group">
                        <label for="exampleFormControlInput1">موضوع الشكوى/الاقتراح</label>
                        <textarea wire:model="subject" class="form-control" name="summernote"
                                  id="summernote"></textarea>
                    </div>
                    <div class="form-group">
                        <a class="button button-border" wire:click="store()">تقديم الشكوى/الاقتراح</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
