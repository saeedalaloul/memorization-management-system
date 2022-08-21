<div class="card-body">
    <li class="list-group-item">
        @if (isset($roles))
            <div class="row">
                <div class="col-md-3">
                    <label style="font-size: 15px; color: #1e7e34">الأدوار*</label>
                    <select style="width: 100%;" wire:model="selectedRoleId" id="role"
                            class="custom-select mr-sm-2 select2">
                        <option value="">الكل</option>
                        @foreach ($roles as $role)
                            <option
                                value="{{ $role->id }}">{{ $role->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        @endif
    </li>
    <br>
    <x-search_></x-search_>
    <div class="table-responsive mt-15">
        <table class="table center-aligned-table mb-0">
            <thead>
            <tr class="text-dark table-success">
                <th>#</th>
                <th>الاسم</th>
                <th>العمليات</th>
            </tr>
            </thead>
            <tbody>
            @forelse($users as $user)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{$user->name}}</td>
                    <td>
                        @if ($user->oversight_member == null)
                            <button class="btn btn-outline-success btn-sm" wire:click="store({{$user->id}})">تعيين كعضو
                                رقابة
                            </button>
                        @else
                            <button class="btn btn-outline-danger btn-sm" wire:click="destroy({{$user->id}})">إزالته
                                كعضو رقابة
                            </button>
                        @endif
                    </td>
                </tr>
            @empty
                <tr style="text-align: center">
                    <td colspan="3">No data available in table</td>
                </tr>
            @endforelse
            </tbody>
            <tfoot>
            <tr class="text-dark table-success">
                <th>#</th>
                <th>الاسم</th>
                <th>العمليات</th>
            </tr>
            </tfoot>
        </table>
    </div>
    @if (!empty($users))
        <div id="datatable_wrapper"
             class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">
            <div class="row">
                <div class="col-sm-12 col-md-5">
                    <div class="dataTables_info" id="datatable_info" role="status"
                         aria-live="polite">
                        Showing {{$users->firstItem()}} to {{$users->lastItem()}}
                        of {{$users->total()}} entries
                    </div>
                </div>
                <div class="col-sm-12 col-md-7">
                    <div class="dataTables_paginate paging_simple_numbers"
                         id="datatable_paginate">
                        <ul class="pagination">
                            {{$users->links()}}
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@push('js')
    <script>
        $("#role").on('change', function (e) {
            let id = $(this).val()
        @this.set('selectedRoleId', id);
        });
    </script>
@endpush
