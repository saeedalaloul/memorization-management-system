@if($value->type == 'App\Notifications\ReplayBoxComplaintSuggestionNotify')
    @if($value->data['category'] == \App\Models\BoxComplaintSuggestion::COMPLAINT_CATEGORY)
        <a href="{{route('user/notifications/read',['id' => $value->id])}}"
           class="dropdown-item">{{Str::limit("لقد تم الرد على الشكوى التي قدمتها من قبل, راجع الرد. ",35,'..')}}
            <small
                class="float-right text-muted time">{{Carbon\Carbon::parse($value->created_at)->diffForHumans()}}</small></a>
    @elseif($value->data['category'] == \App\Models\BoxComplaintSuggestion::SUGGESTION_CATEGORY)
        <a href="{{route('user/notifications/read',['id' => $value->id])}}"
           class="dropdown-item">{{Str::limit("لقد تم الرد على الاقتراح التي قدمته من قبل, راجع الرد. ",35,'..')}}
            <small
                class="float-right text-muted time">{{Carbon\Carbon::parse($value->created_at)->diffForHumans()}}</small></a>
    @else
        <a href="{{route('user/notifications/read',['id' => $value->id])}}"
           class="dropdown-item">{{Str::limit("لقد تم الرد على الفكرة التي قدمتها من قبل, راجع الرد. ",35,'..')}}
            <small
                class="float-right text-muted time">{{Carbon\Carbon::parse($value->created_at)->diffForHumans()}}</small></a>
    @endif
@elseif($value->type == 'App\Notifications\NewBoxComplaintSuggestionNotify')
    @if($value->data['category'] == \App\Models\BoxComplaintSuggestion::COMPLAINT_CATEGORY)
        <a href="{{route('user/notifications/read',['id' => $value->id])}}"
           class="dropdown-item">{{Str::limit("لقد تم تقديم شكوى بواسطة: ".$value->data['sender_name'],40,'..')}}
            <small
                class="float-right text-muted time">{{Carbon\Carbon::parse($value->created_at)->diffForHumans()}}</small></a>
    @elseif($value->data['category'] == \App\Models\BoxComplaintSuggestion::SUGGESTION_CATEGORY)
        <a href="{{route('user/notifications/read',['id' => $value->id])}}"
           class="dropdown-item">{{Str::limit("لقد تم تقديم اقتراح بواسطة: ".$value->data['sender_name'],33,'..')}}
            <small
                class="float-right text-muted time">{{Carbon\Carbon::parse($value->created_at)->diffForHumans()}}</small></a>
    @else
        <a href="{{route('user/notifications/read',['id' => $value->id])}}"
           class="dropdown-item">{{Str::limit("لقد تم تقديم فكرة بواسطة: ".$value->data['sender_name'],33,'..')}}
            <small
                class="float-right text-muted time">{{Carbon\Carbon::parse($value->created_at)->diffForHumans()}}</small></a>
    @endif
@endif
