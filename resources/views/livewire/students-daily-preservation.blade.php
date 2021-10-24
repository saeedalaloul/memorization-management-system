<div class="row">
    <div>
        @if(Session::has('success_message'))
            <script>
                $(function () {
                    toastr.success("{{ Session::get('success_message') }}");
                })
            </script>
        @endif
    </div>
    <div class="col-xl-12 mb-30">
        @if (auth()->user()->current_role == 'أمير المركز' ||
             auth()->user()->current_role == 'مشرف' ||
             auth()->user()->current_role == 'اداري' ||
             auth()->user()->current_role == 'محفظ')
            @can('إدارة متابعة الحفظ والمراجعة')
                <div class="card-body">
                    <div class="row">
                        @if (auth()->user()->current_role == 'أمير المركز')
                            @if (isset($grades))
                                <div>
                                    <label>
                                        <select class="selectpicker" data-style="btn-info"
                                                wire:model="searchGradeId">
                                            <option value="" selected>بحث بواسطة المرحلة
                                            </option>
                                            @foreach ($grades as $grade)
                                                <option
                                                    value="{{ $grade->id }}">{{ $grade->name}}</option>
                                            @endforeach
                                        </select>
                                    </label>
                                </div>
                            @endif
                        @endif
                        @if (auth()->user()->current_role == 'مشرف' || auth()->user()->current_role == 'اداري'
                             || auth()->user()->current_role == 'أمير المركز')
                            @if (isset($groups))
                                <div style="padding-right: 10px;">
                                    <label>
                                        <select class="selectpicker" data-style="btn-info"
                                                wire:model="searchGroupId">
                                            <option value="" selected>بحث بواسطة المحفظ
                                            </option>
                                            @foreach ($groups as $group)
                                                <option
                                                    value="{{ $group->id }}">{{ $group->teacher->user->name}}</option>
                                            @endforeach
                                        </select>
                                    </label>
                                </div>
                            @endif
                        @endif
                    </div>
                    @include('livewire.search')
                    <div class="table-responsive mt-15">
                        <table class="table center-aligned-table mb-0">
                            <thead>
                            <tr class="text-dark table-success">
                                <th wire:click="sortBy('id')" style="cursor: pointer;">#
                                    @include('livewire._sort-icon',['field'=>'id'])
                                </th>
                                <th class="alert-success">اسم الطالب</th>
                                <th class="alert-success">اسم المرحلة</th>
                                <th class="alert-success">اسم المحفظ</th>
                                <th class="alert-success">العمليات</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($students as $student)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $student->user->name }}</td>
                                    <td>{{ $student->grade->name }}</td>
                                    <td>{{ $student->group->teacher->user->name }}</td>
                                    <td>
                                        <button class="btn btn-outline-danger btn-sm" wire:click.prevent="loadModalData({{$student->id}},-1)">إضافة</button>
                                        <button class="btn btn-outline-primary btn-sm" wire:click.prevent="loadModalData({{$student->id}},1)">أخر حفظ</button>
                                        <button class="btn btn-outline-success btn-sm" wire:click.prevent="loadModalData({{$student->id}},2)">أخر مراجعة</button>
                                    </td>
                                </tr>
                                @include('pages.students_daily_preservation.add_daily_preservation')
                                @include('pages.students_daily_preservation.show_daily_preservation')
                            @empty
                                <tr style="text-align: center">
                                    <td colspan="8">No data available in table</td>
                                </tr>
                            @endforelse
                            </tbody>
                            <tfoot>
                            <tr class="text-dark table-success">
                                <th>#</th>
                                <th class="alert-success">اسم الطالب</th>
                                <th class="alert-success">اسم المرحلة</th>
                                <th class="alert-success">اسم المحفظ</th>
                                <th class="alert-success">العمليات</th>
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
                                    Showing {{$students->firstItem()}} to {{$students->lastItem()}}
                                    of {{$students->total()}} entries
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-7">
                                <div class="dataTables_paginate paging_simple_numbers"
                                     id="datatable_paginate">
                                    <ul class="pagination">
                                        {{$students->links()}}
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endcan

    </div>
    @endif
</div>
