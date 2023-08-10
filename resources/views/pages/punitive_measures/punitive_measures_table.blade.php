<x-search></x-search>
<div class="table-responsive mt-15">
    <table class="table center-aligned-table mb-0">
        <thead>
        <tr class="text-dark table-success">
            <th wire:click="sortBy('id')" style="cursor: pointer;">#
                @include('livewire._sort-icon',['field'=>'id'])
            </th>
            <th>نوع الإجراء العقابي</th>
            <th>سبب الإجراء العقابي</th>
            <th>عدد الأيام</th>
            <th>كمية الحفظ</th>
            <th>العمليات</th>
        </tr>
        </thead>
        <tbody>
        @forelse($punitive_measures as $punitive_measure)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>
                    @if ($punitive_measure->type === \App\Models\PunitiveMeasure::BLOCK_TYPE)
                        حظر
                    @else
                        إنذار
                    @endif
                </td>
                <td>
                    @if ($punitive_measure->reason === \App\Models\PunitiveMeasure::MEMORIZE_REASON)
                        ضعف الحفظ
                    @elseif($punitive_measure->reason === \App\Models\PunitiveMeasure::ABSENCE_REASON)
                        الغياب
                    @elseif($punitive_measure->reason === \App\Models\PunitiveMeasure::DID_NOT_MEMORIZE_REASON)
                        لم يحفظ
                    @elseif($punitive_measure->reason === \App\Models\PunitiveMeasure::LATE_REASON)
                        بسبب التأخر
                    @endif
                </td>
                <td>{{$punitive_measure->number_times}}</td>
                <td>{{$punitive_measure->quantity}}</td>
                <td>
                    <button type="button" wire:click="showDialogGroupsCustom('{{$punitive_measure->id}}','add');"
                            class="btn btn-outline-success btn-sm" title="إضافة حلقات">
                        <i class="fa fa-edit"></i></button>
                    <button type="button" wire:click="showDialogGroupsCustom('{{$punitive_measure->id}}','remove');"
                            class="btn btn-outline-dark btn-sm" title="حذف حلقات">
                        <i class="fa fa-remove"></i></button>
                    <button type="button" @click.prevent="currentTab = 'form'"
                            wire:click="edit('{{$punitive_measure->id}}')" class="btn btn-outline-primary btn-sm"
                            title="تعديل">
                        <i class="fa fa-pencil"></i></button>
                    <button type="button" wire:click="showDialogDelete('{{$punitive_measure->id}}');"
                            class="btn btn-outline-danger btn-sm" title="حذف">
                        <i class="fa fa-trash"></i></button>
                </td>
            </tr>
        @empty
            <tr style="text-align: center">
                <td colspan="4">No data available in table</td>
            </tr>
        @endforelse
        @include('pages.punitive_measures.select_group_custom')
        @include('pages.punitive_measures.remove_group_custom')
        @include('pages.punitive_measures.delete_punitive_measure')
        </tbody>
        <tfoot>
        <tr class="text-dark table-success">
            <th>#</th>
            <th>نوع الإجراء العقابي</th>
            <th>سبب الإجراء العقابي</th>
            <th>عدد الأيام</th>
            <th>كمية الحفظ</th>
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
                Showing {{$punitive_measures->firstItem()}} to {{$punitive_measures->lastItem()}}
                of {{$punitive_measures->total()}} entries
            </div>
        </div>
        <div class="col-sm-12 col-md-7">
            <div class="dataTables_paginate paging_simple_numbers"
                 id="datatable_paginate">
                <ul class="pagination">
                    {{$punitive_measures->links()}}
                </ul>
            </div>
        </div>
    </div>
</div>
