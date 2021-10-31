<!--=================================
 header start-->

<nav class="admin-header navbar navbar-default col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
    <!-- logo -->
    <div class="text-left navbar-brand-wrapper">
        <a class="navbar-brand brand-logo" href="#"><img src="{{asset('assets/images/logo-dark.png',true)}}" alt=""></a>
        <a class="navbar-brand brand-logo-mini" href="#"><img src="{{asset('assets/images/logo-icon-dark.png',true)}}"
                                                              alt=""></a>
    </div>
    <!-- Top bar left -->
    <ul class="nav navbar-nav mr-auto">
        <li class="nav-item">
            <a id="button-toggle" class="button-toggle-nav inline-block ml-20 pull-left" href="javascript:void(0);"><i
                    class="zmdi zmdi-menu ti-align-right"></i></a>
        </li>
        <li class="nav-item">
            <div class="search">
                <a class="search-btn not_click" href="javascript:void(0);"></a>
                <div class="search-box not-click">
                    <input type="text" class="not-click form-control" placeholder="Search" value="" name="search">
                    <button class="search-button" type="submit"><i class="fa fa-search not-click"></i></button>
                </div>
            </div>
        </li>
    </ul>
    <!-- top bar right -->
    <ul class="nav navbar-nav ml-auto">
        @if (count(auth()->user()->roles) > 1)
            <li class="nav-item">
                <select class="form-select form-select mb-3" name="current_role">
                    <option disabled selected>تبديل الحساب</option>
                    @for ($i = 0; $i < count(auth()->user()->roles); $i++)
                        <option
                            {{auth()->user()->current_role != null && auth()->user()->current_role == auth()->user()->roles[$i]->name? 'selected':''}} value="{{auth()->user()->roles[$i]->name}}">{{auth()->user()->roles[$i]->name}}</option>
                    @endfor
                </select>
            </li>
        @endif
        <li class="nav-item fullscreen">
            <a id="btnFullscreen" href="#" class="nav-link"><i class="ti-fullscreen"></i></a>
        </li>
        <li class="nav-item dropdown ">
            <a class="nav-link top-nav" data-toggle="dropdown" href="#" role="button" aria-haspopup="true"
               aria-expanded="false">
                <i class="ti-bell"></i>
                <span class="badge badge-danger notification-status"> </span>
            </a>
            <div class="dropdown-menu dropdown-menu-right dropdown-big dropdown-notifications">
                <div class="dropdown-header notifications">
                    <strong>Notifications</strong>
                    <span class="badge badge-pill badge-warning">05</span>
                </div>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">New registered user <small class="float-right text-muted time">Just
                        now</small> </a>
                <a href="#" class="dropdown-item">New invoice received <small class="float-right text-muted time">22
                        mins</small> </a>
                <a href="#" class="dropdown-item">Server error report<small class="float-right text-muted time">7
                        hrs</small> </a>
                <a href="#" class="dropdown-item">Database report<small class="float-right text-muted time">1
                        days</small> </a>
                <a href="#" class="dropdown-item">Order confirmation<small class="float-right text-muted time">2
                        days</small> </a>
            </div>
        </li>
        <li class="nav-item dropdown ">
            <a class="nav-link top-nav" data-toggle="dropdown" href="#" role="button" aria-haspopup="true"
               aria-expanded="true"> <i class=" ti-view-grid"></i> </a>
            <div class="dropdown-menu dropdown-menu-right dropdown-big">
                <div class="dropdown-header">
                    <strong>Quick Links</strong>
                </div>
                <div class="dropdown-divider"></div>
                <div class="nav-grid">
                    <a href="#" class="nav-grid-item"><i class="ti-files text-primary"></i><h5>New Task</h5></a>
                    <a href="#" class="nav-grid-item"><i class="ti-check-box text-success"></i><h5>Assign Task</h5>
                    </a>
                </div>
                <div class="nav-grid">
                    <a href="#" class="nav-grid-item"><i class="ti-pencil-alt text-warning"></i><h5>Add Orders</h5>
                    </a>
                    <a href="#" class="nav-grid-item"><i class="ti-truck text-danger "></i><h5>New Orders</h5></a>
                </div>
            </div>
        </li>
        <li class="nav-item dropdown mr-30">
            <a class="nav-link nav-pill user-avatar" data-toggle="dropdown" href="#" role="button"
               aria-haspopup="true"
               aria-expanded="false">
                @if (auth()->user()->profile_photo_path == null || empty(auth()->user()->profile_photo_path))
                    <img
                        src="{{asset('https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&color=7F9CF5&background=EBF4FF')}}"
                        alt="avatar">
                @else
                    <img
                        src="{{asset('/storage/users_images/'.auth()->user()->identification_number.'/'.auth()->user()->profile_photo_path),true}}"
                        alt="avatar">
                @endif
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <div class="dropdown-header">
                    <div class="media">
                        <div class="media-body">
                            <h5 class="mt-0 mb-0">{{auth()->user()->name}}</h5>
                            <span>{{auth()->user()->email}}</span>
                            {{--                            <span>{{auth()->user()->current_role}}</span>--}}
                        </div>
                    </div>
                </div>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#"><i class="text-secondary ti-reload"></i>Activity</a>
                <a class="dropdown-item" href="#"><i class="text-success ti-email"></i>Messages</a>
                <a class="dropdown-item" href="#"><i class="text-warning ti-user"></i>Profile</a>
                <a class="dropdown-item" href="#"><i class="text-dark ti-layers-alt"></i>Projects <span
                        class="badge badge-info">6</span> </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#"><i class="text-info ti-settings"></i>Settings</a>
                <form action="{{route('logout')}}" method="POST">
                    @csrf
                    <button class="dropdown-item"><i class="text-danger ti-unlock"></i>Logout</button>
                </form>
            </div>
        </li>
    </ul>
</nav>

<!--=================================
 header End-->
