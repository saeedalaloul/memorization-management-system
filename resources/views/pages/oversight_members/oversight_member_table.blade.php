@can('إدارة أعضاء الرقابة')
    <x-search></x-search>
    <div class="table-responsive mt-15">
        <table class="table center-aligned-table mb-0">
            <thead>
            <tr class="text-dark table-success">
                <th wire:click="sortBy('id')" style="cursor: pointer;">#
                    @include('livewire._sort-icon',['field'=>'id'])
                </th>
                <th>اسم عضو الرقابة</th>
                <th>عدد الزيارات</th>
                <th>العمليات</th>
            </tr>
            </thead>
            <tbody>
            @forelse($oversight_members as $oversight_member)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $oversight_member->user->name }}</td>
                    <td>{{ $oversight_member->visits_count}}</td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm"
                                wire:click.prevent="getModalData({{$oversight_member->id}});"
                                data-toggle="modal"
                                data-target="#oversightMemberDeleted"
                                title="حذف">
                            <i class="fa fa-trash"></i></button>
                    </td>
                </tr>
                @include('pages.oversight_members.delete')
            @empty
                <tr style="text-align: center">
                    <td colspan="4">No data available in table</td>
                </tr>
            @endforelse
            </tbody>
            <tfoot>
            <tr class="text-dark table-success">
                <th>#</th>
                <th>اسم عضو الرقابة</th>
                <th>عدد الزيارات</th>
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
                    Showing {{$oversight_members->firstItem()}} to {{$oversight_members->lastItem()}}
                    of {{$oversight_members->total()}} entries
                </div>
            </div>
            <div class="col-sm-12 col-md-7">
                <div class="dataTables_paginate paging_simple_numbers"
                     id="datatable_paginate">
                    <ul class="pagination">
                        {{$oversight_members->links()}}
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endcan
