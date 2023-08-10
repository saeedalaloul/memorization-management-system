<div class="card-body">
    @if (auth()->user()->current_role == \App\Models\User::ADMIN_ROLE)
        @can('إدارة الأدوار')
            <x-search></x-search>
            <div class="table-responsive mt-15">
                <table class="table center-aligned-table mb-0">
                    <thead>
                    <tr class="text-dark table-success">
                        <th wire:click="sortBy('id')" style="cursor: pointer;">#
                            @include('livewire._sort-icon',['field'=>'id'])
                        </th>
                        <th>الاسم</th>
                        <th>عدد المستخدمين</th>
                        <th>العمليات</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($roles as $role)
{{--                        @if($role->name !== \App\Models\User::ADMIN_ROLE)--}}
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{$role->name}}</td>
                                <td>{{$role->users_count}}</td>
                                <td>
                                    <button
                                        wire:click.prevent="edit_permission({{$role->id}})"
                                        class="btn btn-outline-success btn-sm">تعديل الصلاحيات
                                    </button>
                                </td>
                            </tr>
{{--                        @endif--}}
                    @empty
                        <tr style="text-align: center">
                            <td colspan="4">No data available in table</td>
                        </tr>
                    @endforelse
                    @can('تعديل دور')
                        @include('pages.roles.edit_permission_modal')
                    @endcan
                    </tbody>
                    <tfoot>
                    <tr class="text-dark table-success">
                        <th>#</th>
                        <th>الاسم</th>
                        <th>عدد المستخدمين</th>
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
                            Showing {{$roles->firstItem()}} to {{$roles->lastItem()}}
                            of {{$roles->total()}} entries
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-7">
                        <div class="dataTables_paginate paging_simple_numbers"
                             id="datatable_paginate">
                            <ul class="pagination">
                                {{$roles->links()}}
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endcan
    @endif
</div>
