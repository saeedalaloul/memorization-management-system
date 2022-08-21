@can('إدارة أعضاء الأنشطة')
    <x-search></x-search>
    <div class="table-responsive mt-15">
        <table class="table center-aligned-table mb-0">
            <thead>
            <tr class="text-dark table-success">
                <th wire:click="sortBy('id')" style="cursor: pointer;">#
                    @include('livewire._sort-icon',['field'=>'id'])
                </th>
                <th>اسم عضو الأنشطة</th>
                <th>عدد الأنشطة</th>
                <th>العمليات</th>
            </tr>
            </thead>
            <tbody>
            @forelse($activity_members as $activity_member)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $activity_member->user->name }}</td>
                    <td>{{ $activity_member->activities_count }}</td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm"
                                wire:click="getModalData({{$activity_member->id}});"
                                data-toggle="modal"
                                data-target="#activityMemberDeleted"
                                title="حذف">
                            <i class="fa fa-trash"></i></button>
                    </td>
                </tr>
                @include('pages.activity_members.delete')
            @empty
                <tr style="text-align: center">
                    <td colspan="4">No data available in table</td>
                </tr>
            @endforelse
            </tbody>
            <tfoot>
            <tr class="text-dark table-success">
                <th>#</th>
                <th>اسم عضو الأنشطة</th>
                <th>عدد الأنشطة</th>
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
                    Showing {{$activity_members->firstItem()}} to {{$activity_members->lastItem()}}
                    of {{$activity_members->total()}} entries
                </div>
            </div>
            <div class="col-sm-12 col-md-7">
                <div class="dataTables_paginate paging_simple_numbers"
                     id="datatable_paginate">
                    <ul class="pagination">
                        {{$activity_members->links()}}
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endcan
