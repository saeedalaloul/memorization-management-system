<div wire:ignore.self class="modal" tabindex="-1" role="dialog" id="show-daily-memorization" style="display: none;"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="color: #1e7e34"> متابعة الطالب : {{$student_name}} بتاريخ
                    : {{$dayOfWeek}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body p-20">

                <div class="row">
                    <div class="col-md-12" style="display: flex;justify-content: center;">
                        <label class="control-label badge badge-success"
                               style="padding: 7px 10px;">العملية</label>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12" style="display: flex;justify-content: center;">
                        <h4 style="padding: 10px; font-size: 20px; color: #1e7e34">{{$type_name}}</h4>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12" style="display: flex;justify-content: center;">
                        <label class="control-label badge badge-success"
                               style="padding: 7px 10px;">الكتاب</label>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12" style="display: flex;justify-content: center;">
                        <h4 style="padding: 10px; font-size: 20px; color: #1e7e34">{{$book_name}}</h4>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12" style="display: flex;justify-content: center;">
                        <label class="control-label badge badge-success"
                               style="padding: 7px 10px;">من حديث</label>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12" style="display: flex;justify-content: center;">
                        <h4 style="padding: 10px; font-size: 20px; color: #1e7e34">{{$hadith_from_name}}</h4>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12" style="display: flex;justify-content: center;">
                        <label class="control-label badge badge-success"
                               style="padding: 7px 10px;">إلى حديث</label>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12" style="display: flex;justify-content: center;">
                        <h4 style="padding: 10px; font-size: 20px; color: #1e7e34">{{$hadith_to_name}}</h4>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12" style="display: flex;justify-content: center;">
                        <label class="control-label badge badge-success"
                               style="padding: 7px 10px;">التقييم</label>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12" style="display: flex;justify-content: center;">
                        <h4 style="padding: 10px; font-size: 20px; color: #1e7e34">{{$evaluation_name}}</h4>
                    </div>
                </div>

            </div>
            <div class="modal-footer" style="display: flex;justify-content: center;">
                <button type="button" class="btn btn-outline-success ripple" data-dismiss="modal">إغلاق</button>
            </div>
        </div>
    </div>
</div>
