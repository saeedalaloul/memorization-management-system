<div class="row">
    @if ($catchError)
        <div class="alert alert-danger" id="success-danger">
            <button type="button" class="close" data-dismiss="alert">x</button>
            {{ $catchError }}
        </div>
    @endif
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
            @if(auth()->user()->current_role == 'أمير المركز' ||
                auth()->user()->current_role == 'مشرف الإختبارات')
                <div class="card-body">
                    @can('اضافة مختبر')
                        <button type="button" wire:click.prevent="modalFormReset()" class="button x-small"
                                data-toggle="modal"
                                data-target="#add_tester">
                            اضافة مختبر
                        </button>
                        @include('pages.testers.add')
                    @endcan
                    @can('قائمة المختبرين')
                        <br><br>
                        @include('livewire.search')
                            <div class="table-responsive mt-15">
                                <table class="table center-aligned-table mb-0">
                                <thead>
                                <tr class="text-dark table-success">
                                    <th wire:click="sortBy('id')" style="cursor: pointer;">#
                                        @include('livewire._sort-icon',['field'=>'id'])
                                    </th>
                                    <th>اسم المختبر</th>
                                    <th>عدد الإختبارات</th>
                                    <th>العمليات</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($testers as $tester)
                                    <tr>
                                        <td>{{ $tester->id }}</td>
                                        <td>{{ $tester->user->name }}</td>
                                        <td>{{ $tester->exams->count() }}</td>
                                        <td>
                                            @can('حذف مختبر')
                                                <button type="button" class="btn btn-danger btn-sm" data-toggle="modal"
                                                        data-target="#groupDeleted"
                                                        title="حذف"><i
                                                        class="fa fa-trash"></i></button>
                                            @endcan
                                        </td>
                                    </tr>
                                    @include('pages.testers.delete')
                                @empty
                                    <tr style="text-align: center">
                                        <td colspan="4">No data available in table</td>
                                    </tr>
                                @endforelse

                                </tbody>
                                <tfoot>
                                <tr class="text-dark table-success">
                                    <th>#</th>
                                    <th>اسم المختبر</th>
                                    <th>عدد الإختبارات</th>
                                    <th>العمليات</th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div id="datatable_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">
                            <div class="row">
                                <div class="col-sm-12 col-md-5">
                                    <div class="dataTables_info" id="datatable_info" role="status" aria-live="polite">
                                        Showing {{$testers->firstItem()}} to {{$testers->lastItem()}}
                                        of {{$testers->total()}} entries
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-7">
                                    <div class="dataTables_paginate paging_simple_numbers" id="datatable_paginate">
                                        <ul class="pagination">
                                            {{$testers->links()}}
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endcan
                </div>
            @endif
        </div>
    </div>
</div>
