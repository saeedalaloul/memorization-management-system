<div>
    @if(Session::has('success_message'))
        <script>
            $(function () {
                toastr.success("{{ Session::get('success_message') }}");
            })
        </script>
    @endif
</div>
@if (auth()->user()->current_role == 'أمير المركز')
    <div class="card-body">
        @can('قائمة المستخدمين')
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
            </div>
            @include('livewire.search')
            <div class="table-responsive mt-15">
                <table class="table center-aligned-table mb-0">
                    <thead>
                    <tr class="text-dark table-success">
                        <th wire:click="sortBy('id')" style="cursor: pointer;">#
                            @include('livewire._sort-icon',['field'=>'id'])
                        </th>
                        <th>الاسم</th>
                        <th>رقم الهوية</th>
                        <th>البريد الإلكتروني</th>
                        <th>رقم الجوال</th>
                        <th>نوع المستخدم</th>
                        <th>حالة البريد الإلكتروني</th>
                        <th>العمليات</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{$user->name}}</td>
                            <td>{{$user->identification_number}}</td>
                            <td>{{$user->email}}</td>
                            <td>{{$user->phone}}</td>
                            <td>
                                @if(!empty($user->getRoleNames()))
                                    @foreach($user->getRoleNames() as $r)
                                        <label class="badge badge-success">{{ $r }}</label>
                                    @endforeach
                                @endif
                            </td>
                            <td>
                                @if($user->email_verified_at != null)
                                    @csrf
                                    <button wire:click="activeEmail({{$user->id}});"
                                            class="btn btn-outline-danger btn-sm">إلغاء تفعيل
                                    </button>
                                @else
                                    <button wire:click="activeEmail({{$user->id}});"
                                            class="btn btn-outline-success btn-sm">تفعيل
                                    </button>
                                @endif
                            </td>
                            <td class="embed-responsive-item">
                                <div class="btn-group mb-1 embed-responsive-item">
                                    <button type="button" class="btn btn-success">العمليات</button>
                                    <button type="button"
                                            class="btn btn-success dropdown-toggle dropdown-toggle-split"
                                            data-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                        <span class="sr-only">العمليات</span>
                                    </button>
                                    <div class="dropdown-menu embed-responsive-item" x-placement="top-end"
                                         style="position: absolute; transform: translate3d(0px, 32px, 0px); top: 0px; left: 0px; will-change: transform;">
                                        <button class="dropdown-item"><i style="color: #ffc107"
                                                                         class="fa fa-eye"></i>&nbsp; عرض بيانات
                                            المستخدم
                                        </button>
                                        <button class="dropdown-item"><i
                                                style="color:green" class="fas fa-user-edit"></i> تعديل بيانات المستخدم
                                        </button>
                                        <button class="dropdown-item"><i
                                                style="color:green" class="fa fa-edit"></i> تعديل الصلاحيات
                                        </button>
                                        <button class="dropdown-item"><i
                                                style="color:green" class="fa fa-recycle"></i> إعادة تعيين كلمة المرور
                                        </button>
                                    </div>
                                </div>
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
                        <th>الاسم</th>
                        <th>رقم الهوية</th>
                        <th>البريد الإلكتروني</th>
                        <th>رقم الجوال</th>
                        <th>نوع المستخدم</th>
                        <th>حالة البريد الإلكتروني</th>
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
        @endcan
    </div>
@endif
