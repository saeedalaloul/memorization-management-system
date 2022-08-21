<div class="form-group">
    <label for="exampleFormControlSelect2">ملاحظات الرقابة</label>
    <input class="form-control" value="{{$visitOrder->notes}}" readonly/>
</div>

<div class="form-group">
    <label for="exampleFormControlSelect2">الاقتراحات</label>
    <input class="form-control" value="{{$visitOrder->suggestions}}" readonly/>
</div>

<div class="form-group">
    @if($visitOrder->hostable_type == 'App\Models\Teacher')
        <label for="exampleFormControlSelect2">توصيات المحفظ</label>
    @elseif($visitOrder->hostable_type == 'App\Models\Tester')
        <label for="exampleFormControlSelect2">توصيات المختبر</label>
    @elseif($visitOrder->hostable_type == 'App\Models\ActivityMember')
        <label for="exampleFormControlSelect2">توصيات المنشط</label>
    @endif
    <input class="form-control" value="{{$visitOrder->recommendations}}" readonly/>
</div>
