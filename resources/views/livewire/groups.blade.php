<div class="row">
    <div>
        @if(Session::has('message'))
            <script>
                $(function () {
                    toastr.success("{{ Session::get('message') }}");
                })
            </script>
        @endif
    </div>

    <div class="col-xl-12 mb-30">
        <div class="card card-statistics h-100">
            <div class="card-body">
                <h5 class="card-title">إدارة الحلقات</h5>
                <div class="tab tab-border">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link {{$show_table == true ? 'active show':''}}" href="#" id="groups-05-tab"
                               data-bs-toggle="tab" role="tab" wire:click="showformadd(true);"
                               aria-controls="groups-05" aria-selected="true"> <i class="fa fa-group"></i> قائمة
                                الحلقات</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{$show_table == false ? 'active show':''}}" id="add_group-05-tab"
                               data-bs-toggle="tab" role="tab"
                               wire:click="showformadd(false);" href="#"
                               aria-controls="add_group-05" aria-selected="false"><i
                                    class="fas {{!empty($modalId) ? 'fa-edit' : 'fa-plus-square'}}"></i>
                                @if(!empty($modalId))
                                    @if ($is_moving)
                                        نقل حلقة
                                    @else
                                        تحديث حلقة
                                    @endif
                                @else
                                    إضافة حلقة
                                @endif
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade {{$show_table == true ? 'active show':''}}" id="groups-05"
                             role="tabpanel"
                             aria-labelledby="groups-05-tab">
                            @if (auth()->user()->current_role == 'أمير المركز' ||
               auth()->user()->current_role == 'مشرف' ||
               auth()->user()->current_role == 'اداري')
                                <div class="card-body">
                                    @can('إدارة المجموعات')
                                        @if(auth()->user()->current_role == 'أمير المركز')
                                            <form>
                                                <label>
                                                    <select class="selectpicker" data-style="btn-info"
                                                            wire:model="searchGradeId">
                                                        <option value="" selected>بحث بواسطة المرحلة</option>
                                                        @foreach ($grades as $grade)
                                                            <option
                                                                value="{{ $grade->id }}">{{ $grade->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </label>
                                            </form>
                                        @endif
                                        @include('livewire.search')
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
                                                    <th>اسم المرحلة</th>
                                                    <th>اسم المحفظ</th>
                                                    <th>عدد طلاب الحلقة</th>
                                                    <th>العمليات</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @forelse($groups as $group)
                                                    <tr>
                                                        <td>{{ $group->id }}</td>
                                                        <td>{{ $group->name }}</td>
                                                        <td>{{ $group->grade->name }}</td>
                                                        <td>{{ $group->teacher->user->name }}</td>
                                                        <td>{{ $group->students->count() }}</td>
                                                        <td>
                                                            @can('تعديل مجموعة')
                                                                <button type="button" class="btn btn-info btn-sm"
                                                                        wire:click.prevent="loadModalData({{$group->id}},false)"
                                                                        title="تعديل"><i class="fa fa-edit"></i>
                                                                </button>
                                                            @endcan
                                                            @can('نقل مجموعة')
                                                                <button type="button" class="btn btn-info btn-sm"
                                                                        wire:click.prevent="loadModalData({{$group->id}},true)"
                                                                        title="نقل الحلقة"><i class="fa fa-cut"></i>
                                                                </button>
                                                            @endcan
                                                            @can('حذف مجموعة')
                                                                <button type="button" class="btn btn-danger btn-sm"
                                                                        data-toggle="modal"
                                                                        data-target="#groupDeleted"
                                                                        title="حذف"><i
                                                                        class="fa fa-trash"></i></button>
                                                            @endcan
                                                        </td>
                                                    </tr>

                                                    @include('pages.groups.delete')


                                                @empty
                                                    <tr style="text-align: center">
                                                        <td colspan="6">No data available in table</td>
                                                    </tr>
                                                @endforelse

                                                </tbody>
                                                <tfoot>
                                                <tr class="text-dark table-success">
                                                    <th>#</th>
                                                    <th>اسم الحلقة</th>
                                                    <th>اسم المرحلة</th>
                                                    <th>اسم المحفظ</th>
                                                    <th>عدد طلاب الحلقة</th>
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
                                    @endcan
                                </div>
                            @endif
                        </div>
                        <div class="tab-pane fade {{$show_table == false ? 'active show':''}}" id="add_group-05"
                             role="tabpanel"
                             aria-labelledby="add_group-05-tab">
                            @if ($is_moving)
                                @include('pages.groups.group_move')
                                @include('pages.groups.move_warning')
                            @else
                                @can('إضافة مجموعة')
                                    @include('pages.groups.group_form')
                                @endcan
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
