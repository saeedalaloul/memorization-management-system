<div style="width: 50%; height: 40%; transform: translate(-30%)" class="table-responsive mt-15">
    <table class="table center-aligned-table mb-0">
        <thead>
        <tr class="table-success">
            <th>رقم السؤال</th>
            <th>أخطاء السؤال</th>
            <th>درجة أخطاء السؤال</th>
        </tr>
        </thead>
        <tbody>
        @for($i = 1; $i <= $exam_questions_count; $i++)
            <tr>
                <td>{{ $i }}</td>
                <td><input type="text" wire:click.prevent="getFocusId({{$i}})"
                           id="signs_questions_{{$i}}" onkeydown="return false;"
                           wire:model="signs_questions.{{$i}}" style="width: 100px;" max="10"
                           class="form-control">
                    @if (isset($signs_questions[$i]))
                        @if ($marks_questions[$i] >= 90 && $marks_questions[$i] <= 94)
                            <div style="width: 100px;" class="badge-success">
                                {{strlen($signs_questions[$i])}}
                            </div>
                        @elseif($marks_questions[$i] >= 80 && $marks_questions[$i] <= 90)
                            <div style="width: 100px;" class="badge-info">
                                {{strlen($signs_questions[$i])}}
                            </div>
                        @elseif($marks_questions[$i] >= 70 && $marks_questions[$i] <= 80)
                            <div style="width: 100px;" class="badge-warning">
                                {{strlen($signs_questions[$i])}}
                            </div>
                        @elseif($marks_questions[$i] >= 60 && $marks_questions[$i] <= 70)
                            <div style="width: 100px;" class="badge-danger">
                                {{strlen($signs_questions[$i])}}
                            </div>
                        @endif
                    @endif
                </td>
                <td><input type="number" wire:model="marks_questions.{{$i}}" readonly
                           style="width: 100px;" class="form-control"></td>
            </tr>
        @endfor
        </tbody>
        <tfoot>
        <tr class="table-success">
            <th>#</th>
            <th>أخطاء السؤال</th>
            <th>درجة أخطاء السؤال</th>
        </tr>
        </tfoot>
    </table>
</div>
<div class="row" style="transform: translate(-30%); padding: 10px;">
    <div class="card-body">
        <div class="col-md-12">
            <button wire:click.prevent="minus_1()" style="width: 50px;" type="button"
                    class="btn btn-outline-danger btn-sm">/
            </button>
            <button style="width: 50px;" wire:click.prevent="remove()" type="button"
                    class="btn btn-outline-primary btn-sm">مسح
            </button>
            <button wire:click.prevent="minus_0_5()" style="width: 50px;" type="button"
                    class="btn btn-outline-danger btn-sm">-
            </button>
            <button type="button"
                    class="btn btn-outline-success btn-sm"
                    data-toggle="modal"
                    data-target="#approval-exam">اعتماد درجة الإختبار
            </button>
        </div>
    </div>
</div>
