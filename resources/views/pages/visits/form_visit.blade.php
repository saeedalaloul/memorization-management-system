<div class="form-group">
    <label for="exampleFormControlSelect2">ملاحظات الرقابة</label>
    <input class="form-control" value="{{$visit->notes}}" readonly/>
</div>

<div class="form-group">
    <label for="exampleFormControlSelect2">الاقتراحات</label>
    <input class="form-control" value="{{$visit->suggestions}}" readonly/>
</div>

<div class="form-group">
    @if($visit->hostable_type == 'App\Models\Teacher')
        <label for="exampleFormControlSelect2">توصيات المحفظ</label>
    @elseif($visit->hostable_type == 'App\Models\Tester')
        <label for="exampleFormControlSelect2">توصيات المختبر</label>
    @elseif($visit->hostable_type == 'App\Models\ActivityMember')
        <label for="exampleFormControlSelect2">توصيات المنشط</label>
    @endif
    <input class="form-control" value="{{$visit->recommendations}}" readonly/>
</div>

<div class="form-group">
    <label for="exampleFormControlSelect2">رد أمير المركز</label>
    <input class="form-control" value="{{$visit->reply}}" readonly/>
</div>
