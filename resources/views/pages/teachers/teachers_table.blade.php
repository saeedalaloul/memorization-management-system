<div class="card-body">
    @can('إدارة المحفظين')
        <li class="list-group-item">
            <div class="row">
                <div class="col-md-3">
                    <label style="font-size: 15px; color: #1e7e34">المراحل*</label>
                    <select style="width: 100%;" id="grade" class="custom-select mr-sm-2 select2"
                            wire:model="selectedGradeId">
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
                    <th>اسم المحفظ</th>
                    <th>رقم الهوية</th>
                    <th>تاريخ الميلاد</th>
                    <th>البريد الإلكتروني</th>
                    <th>رقم الجوال</th>
                    <th>اسم المرحلة</th>
                    <th>العمليات</th>
                </tr>
                </thead>
                <tbody>
                @forelse($teachers as $teacher)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <img src="{{$teacher->user->profile_photo_url}}" style="width: 50px; height: 50px;"
                                 class="img-fluid mr-15 avatar-small" alt="">
                            {{$teacher->user->name}}
                        </td>
                        <td>{{$teacher->user->identification_number}}</td>
                        <td>{{$teacher->user->dob}}</td>
                        <td>{{$teacher->user->email}}</td>
                        <td>{{$teacher->user->phone}}</td>
                        <td>{{$teacher->grade->name}}</td>
                        <td>
                            @can('تعديل محفظ')
                                <button
                                    @click.prevent="currentTab = 'form'"
                                    class="btn btn-info btn-sm" role="button"
                                    wire:click.prevent="loadModalData({{$teacher->id}})"
                                    aria-pressed="true"><i
                                        class="fa fa-edit"></i></button>
                            @endcan
                            @can('حذف محفظ')
                                <button type="button" class="btn btn-danger btn-sm"
                                        data-toggle="modal"
                                        data-target="#delete_Teacher"
                                        title="حذف">
                                    <i class="fa fa-trash"></i></button>
                                @include('pages.teachers.delete')
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr style="text-align: center">
                        <td colspan="8">No data available in table</td>
                    </tr>
                @endforelse
                </tbody>
                <tfoot>
                <tr class="text-dark table-success">
                    <th>#</th>
                    <th>اسم المحفظ</th>
                    <th>رقم الهوية</th>
                    <th>تاريخ الميلاد</th>
                    <th>البريد الإلكتروني</th>
                    <th>رقم الجوال</th>
                    <th>اسم المرحلة</th>
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
                        Showing {{$teachers->firstItem()}} to {{$teachers->lastItem()}}
                        of {{$teachers->total()}} entries
                    </div>
                </div>
                <div class="col-sm-12 col-md-7">
                    <div class="dataTables_paginate paging_simple_numbers"
                         id="datatable_paginate">
                        <ul class="pagination">
                            {{$teachers->links()}}
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endcan
</div>
