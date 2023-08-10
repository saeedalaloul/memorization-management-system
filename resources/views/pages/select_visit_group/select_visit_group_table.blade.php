<div class="card-body">
    <li class="list-group-item">
        <div class="row">
            <div class="col-md-3">
                <label style="font-size: 15px; color: #1e7e34">المراحل*</label>
                <select style="width: 100%;" wire:model="selectedGradeId" id="grade"
                        class="custom-select mr-sm-2 select2">
                    <option value="">الكل</option>
                    @foreach ($grades as $grade)
                        <option
                            value="{{ $grade->id }}">{{ $grade->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </li>
    <br>
    <x-search></x-search>
    <div class="table-responsive mt-15">
        <table class="table center-aligned-table mb-0">
            <thead>
            <tr class="text-dark table-success">
                <th wire:click="sortBy('id')" style="cursor: pointer;">#
                    @include('livewire._sort-icon',['field'=>'id'])
                </th>
                <th wire:click="sortBy('name')" style="cursor: pointer;">اسم الحلقة
                    @include('livewire._sort-icon',['field'=>'name'])
                </th>
                <th>اسم المحفظ</th>
                <th>العمليات</th>
            </tr>
            </thead>
            <tbody>
            @forelse($groups as $group)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $group->name }}</td>
                    <td>
                        @if ($group->teacher_id != null)
                            {{ $group->teacher->user->name }}
                        @else
                            لا يوجد محفظ
                        @endif
                    </td>
                    <td>
                        @if ($group->teacher_id != null && $group->teacher->visit_orders->first() == null)
                            <button class="btn btn-outline-success btn-sm"
                                    data-toggle="modal"
                                    wire:click.prevent="loadModalData('{{$group->id}}')"
                                    data-target="#select-visit">
                                إقرار
                                زيارة
                            </button>
                        @else
                            <div class="badge badge-info">
                                يوجد طلبات زيارات سابقة
                            </div>
                        @endif
                    </td>
                </tr>
                @include('pages.select_visit_group.select_visit_group')
            @empty
                <tr style="text-align: center">
                    <td colspan="4">No data available in table</td>
                </tr>
            @endforelse

            </tbody>
            <tfoot>
            <tr class="text-dark table-success">
                <th>#</th>
                <th>اسم الحلقة</th>
                <th>اسم المحفظ</th>
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
                    Showing {{$groups->firstItem()}} to {{$groups->lastItem()}}
                    of {{$groups->total()}} entries
                </div>
            </div>
            <div class="col-sm-12 col-md-7">
                <div class="dataTables_paginate paging_simple_numbers"
                     id="datatable_paginate">
                    <ul class="pagination">
                        {{$groups->links()}}
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@push('js')
    <script>
        $("#grade").on('change', function (e) {
            let id = $(this).val()
        @this.set('selectedGradeId', id);
            livewire.emit('getTeachersByGradeId');
        });
    </script>
@endpush
