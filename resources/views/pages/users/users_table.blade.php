<div class="card-body">
    @if ($current_role == \App\Models\User::ADMIN_ROLE)
        @can('إدارة المستخدمين')
            <li class="list-group-item">
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
                        <th>الاسم</th>
                        <th>رقم الهوية</th>
                        <th>البريد الإلكتروني</th>
                        <th>رقم الجوال</th>
                        <th>نوع المستخدم</th>
                        <th>حالة البريد الإلكتروني</th>
                        <th>حالة الحساب</th>
                        <th>أخر ظهور</th>
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
                                            class="btn btn-outline-success btn-sm">مفعل
                                    </button>
                                @else
                                    <button wire:click="activeEmail({{$user->id}});"
                                            class="btn btn-outline-danger btn-sm">غير مفعل
                                    </button>
                                @endif
                            </td>
                            <td>
                                @if($user->status == true)
                                    @csrf
                                    <button wire:click="activeAccount({{$user->id}});"
                                            class="btn btn-outline-success btn-sm">مفعل
                                    </button>
                                @else
                                    <button wire:click="activeAccount({{$user->id}});"
                                            class="btn btn-outline-danger btn-sm">معلق
                                    </button>
                                @endif
                            </td>
                            <td>
                                @if ($user->last_seen != null)
                                    @if(Cache::has('user-is-online-' . $user->id))
                                        <label class="badge badge-success">نشط الآن</label>
                                    @else
                                        <label
                                            class="badge badge-danger">{{ Carbon\Carbon::parse($user->last_seen)->diffForHumans() }}
                                        </label>
                                    @endif
                                @else
                                    <label class="badge badge-danger">لا ظهور</label>
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
                                        <button class="dropdown-item"
                                                @click.prevent="currentTab = 'form'"
                                                wire:click.prevent="loadModalData({{$user->id}},'edit')"><i
                                                style="color:green" class="fas fa-user-edit"></i> تعديل بيانات
                                            المستخدم
                                        </button>
                                        <button class="dropdown-item"
                                                @click.prevent="currentTab = 'form'"
                                                wire:click.prevent="loadModalData({{$user->id}},'edit_roles')"><i
                                                style="color:green" class="fa fa-edit"></i> تعديل الأدوار
                                        </button>
                                        <button class="dropdown-item"
                                                @click.prevent="currentTab = 'form'"
                                                wire:click.prevent="loadModalData({{$user->id}},'edit_permission')"><i
                                                style="color:green" class="fa fa-edit"></i> تعديل الصلاحيات
                                        </button>
                                        <button class="dropdown-item"
                                                wire:click.prevent="loadModalData({{$user->id}},'reset')"><i
                                                style="color:green" class="fa fa-recycle"></i> إعادة تعيين كلمة
                                            المرور
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @include('pages.users.reset_password_user')
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
                        <th>حالة الحساب</th>
                        <th>أخر ظهور</th>
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
