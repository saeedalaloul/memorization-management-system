@if ($current_role == \App\Models\User::ADMIN_ROLE)
    <div class="card-body">
        @can('إدارة مشرفي الحلقات المكفولة')
            <x-search></x-search>
            <div class="table-responsive mt-15">
                <table class="table center-aligned-table mb-0">
                    <thead>
                    <tr class="text-dark table-success">
                        <th wire:click="sortBy('id')" style="cursor: pointer;">#
                            @include('livewire._sort-icon',['field'=>'id'])
                        </th>
                        <th>اسم المشرف</th>
                        <th>رقم الهوية</th>
                        <th>البريد الإلكتروني</th>
                        <th>رقم الجوال</th>
                        <th>أقسام الكفالة</th>
                        <th>العمليات</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($sponsorship_supervisors as $supervisor)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <img src="{{$supervisor->profile_photo_url}}" style="width: 50px; height: 50px;"
                                     class="img-fluid mr-15 avatar-small" alt="">
                                {{$supervisor->name}}
                            </td>
                            <td>{{$supervisor->identification_number}}</td>
                            <td>{{$supervisor->email}}</td>
                            <td>{{$supervisor->phone}}</td>
                            <td>
                                @if(count($supervisor->sponsorships) === 0)
                                    <label class="badge badge-danger">لا يتبع لأي قسم</label>
                                @else
                                    @foreach($supervisor->sponsorships as $sponsorship)
                                        <label
                                            class="badge badge-success">{{ $sponsorship->name }}</label>
                                    @endforeach
                                @endif
                            </td>
                            <td>
                                @can('تعديل مشرف حلقات مكفولة')
                                    <button
                                        class="btn btn-info btn-sm" role="button"
                                        @click.prevent="currentTab = 'form'"
                                        wire:click.prevent="loadModalData({{$supervisor->id}})"
                                        aria-pressed="true"><i
                                            class="fa fa-edit"></i></button>
                                @endcan
                                @can('حذف مشرف حلقات مكفولة')
                                    <button type="button" class="btn btn-danger btn-sm"
                                            data-toggle="modal"
                                            data-target="#delete_Supervisor"
                                            title="حذف">
                                        <i class="fa fa-trash"></i></button>
                                        @include('pages.sponsorship_supervisors.delete')
                                    @endcan
                            </td>
                        </tr>
                    @empty
                        <tr style="text-align: center">
                            <td colspan="7">No data available in table</td>
                        </tr>
                    @endforelse
                    </tbody>
                    <tfoot>
                    <tr class="text-dark table-success">
                        <th>#</th>
                        <th>اسم المشرف</th>
                        <th>رقم الهوية</th>
                        <th>البريد الإلكتروني</th>
                        <th>رقم الجوال</th>
                        <th>أقسام الكفالة</th>
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
                            Showing {{$sponsorship_supervisors->firstItem()}}
                            to {{$sponsorship_supervisors->lastItem()}}
                            of {{$sponsorship_supervisors->total()}} entries
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-7">
                        <div class="dataTables_paginate paging_simple_numbers"
                             id="datatable_paginate">
                            <ul class="pagination">
                                {{$sponsorship_supervisors->links()}}
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endcan
    </div>
@endif
