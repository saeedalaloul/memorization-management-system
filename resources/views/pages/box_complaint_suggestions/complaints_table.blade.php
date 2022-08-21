@can('إدارة صندوق الشكاوي والإقتراحات')
    <x-search></x-search>
    <div class="table-responsive mt-15">
        <table class="table center-aligned-table mb-0">
            <thead>
            <tr class="text-dark table-success">
                <th wire:click="sortBy('id')" style="cursor: pointer;">#
                    @include('livewire._sort-icon',['field'=>'id'])
                </th>
                <th>اسم المرسل</th>
                <th>اسم المستقبل</th>
                <th>نوع الشكوى/الاقتراح</th>
                <th>قرئت منذ</th>
                <th>قرء الرد منذ</th>
                <th>تاريخ الشكوى/الاقتراح</th>
                <th>العمليات</th>
            </tr>
            </thead>
            <tbody>
            @forelse($box_complaint_suggestions as $box_complaint_suggestion)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{$box_complaint_suggestion->sender->name}}</td>
                    <td>{{$box_complaint_suggestion->receiver->name}}</td>
                    <td>
                        @if ($box_complaint_suggestion->category == \App\Models\BoxComplaintSuggestion::COMPLAINT_CATEGORY)
                            شكوى
                        @elseif($box_complaint_suggestion->category == \App\Models\BoxComplaintSuggestion::SUGGESTION_CATEGORY)
                            اقتراح
                        @elseif($box_complaint_suggestion->category == \App\Models\BoxComplaintSuggestion::IDEA_CATEGORY)
                            فكرة
                        @endif
                    </td>
                    <td>
                        @if ($box_complaint_suggestion->subject_read_at != null)
                            <label
                                class="badge badge-success">{{Carbon\Carbon::parse($box_complaint_suggestion->subject_read_at)->diffForHumans()}}</label>
                        @else
                            <label class="badge badge-danger">لا قراءة</label>
                        @endif
                    </td>
                    <td>
                        @if ($box_complaint_suggestion->reply_read_at != null)
                            <label
                                class="badge badge-success">{{Carbon\Carbon::parse($box_complaint_suggestion->reply_read_at)->diffForHumans()}}</label>
                        @else
                            <label class="badge badge-danger">لا قراءة</label>
                        @endif
                    </td>
                    <td>{{\Carbon\Carbon::parse($box_complaint_suggestion->datetime)->format('Y-m-d')}}</td>
                    <td>
                        <button type="button" class="btn btn-success btn-sm"
                                @click.prevent="currentTab = 'form'"
                                wire:click="detailsShow('{{$box_complaint_suggestion->id}}');"
                                title="عرض التفاصيل">
                            <i class="fa fa-eye"></i></button>
                        @if($box_complaint_suggestion->reply == null)
                            @if($box_complaint_suggestion->receiver_id == auth()->id())
                                <button type="button" class="btn btn-warning btn-sm"
                                        @click.prevent="currentTab = 'form'"
                                        wire:click="complaintReply('{{$box_complaint_suggestion->id}}');"
                                        title="إجراء رد">
                                    <i class="fa fa-reply"></i></button>
                            @endif
                        @endif
                    </td>
                </tr>
            @empty
                <tr style="text-align: center">
                    <td colspan="8">No data available in table</td>
                </tr>
            @endforelse
            </tbody>
            <tfoot>
            <tr class="text-dark table-success">
                <th>#</th>
                <th>اسم المرسل</th>
                <th>اسم المستقبل</th>
                <th>نوع الشكوى/الاقتراح</th>
                <th>قرئت في</th>
                <th>قرء الرد منذ</th>
                <th>تاريخ الشكوى/الاقتراح</th>
                <th>العمليات</th>
            </tr>
            </tfoot>
        </table>
    </div>
    <div id="datatable_wrapper"
         class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">
        <div class="row">
            <div class="col-sm-12 col-md-5">
                <div class="dataTables_info" id="datatable_info" role="status"
                     aria-live="polite">
                    Showing {{$box_complaint_suggestions->firstItem()}} to {{$box_complaint_suggestions->lastItem()}}
                    of {{$box_complaint_suggestions->total()}} entries
                </div>
            </div>
            <div class="col-sm-12 col-md-7">
                <div class="dataTables_paginate paging_simple_numbers"
                     id="datatable_paginate">
                    <ul class="pagination">
                        {{$box_complaint_suggestions->links()}}
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endcan
