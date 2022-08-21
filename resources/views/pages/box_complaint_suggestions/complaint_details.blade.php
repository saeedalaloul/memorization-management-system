<table class="table table-striped table-hover" style="text-align:center">
    <tbody>
    <tr class="table-info">
        <th scope="row">اسم المرسل</th>
        <td class="text-dark">{{$boxComplaintSuggestion->sender->name}}</td>
        <th scope="row">اسم المستقبل</th>
        <td class="text-dark">{{$boxComplaintSuggestion->receiver->name}}</td>
        <th scope="row">نوع الشكوى/الاقتراح</th>
        <td class="text-dark">
            @if ($boxComplaintSuggestion->category == \App\Models\BoxComplaintSuggestion::COMPLAINT_CATEGORY)
                شكوى
            @elseif($boxComplaintSuggestion->category == \App\Models\BoxComplaintSuggestion::SUGGESTION_CATEGORY)
                اقتراح
            @elseif($boxComplaintSuggestion->category == \App\Models\BoxComplaintSuggestion::IDEA_CATEGORY)
                فكرة
            @endif
        </td>
        <th scope="row"></th>
        <td class="text-dark"></td>
    </tr>
    <tr>
        <th scope="row">قرئت منذ</th>
        <td class="text-dark">
            @if ($boxComplaintSuggestion->subject_read_at != null)
                <label
                    class="badge badge-success">{{Carbon\Carbon::parse($boxComplaintSuggestion->subject_read_at)->diffForHumans()}}</label>
            @else
                <label class="badge badge-danger">لا قراءة</label>
            @endif
        </td>
        <th scope="row">قرء الرد منذ</th>
        <td class="text-dark">
            @if ($boxComplaintSuggestion->reply_read_at != null)
                <label
                    class="badge badge-success">{{Carbon\Carbon::parse($boxComplaintSuggestion->reply_read_at)->diffForHumans()}}</label>
            @else
                <label class="badge badge-danger">لا قراءة</label>
            @endif
        </td>
        <th scope="row">تاريخ الشكوى/الاقتراح</th>
        <td class="text-dark">{{\Carbon\Carbon::parse($boxComplaintSuggestion->datetime)->format('Y-m-d')}}</td>
        <th scope="row"></th>
        <td class="text-dark"></td>
    </tr>
    </tbody>
</table>
