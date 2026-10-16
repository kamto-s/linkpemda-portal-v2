<!-- ========== Topbar Start ========== -->
<div class="navbar-custom">
    <div class="topbar">
        <div class="topbar-menu d-flex align-items-center gap-lg-2 gap-1">

            <div class="logo-box">
                <a class='logo-light' href="{{ route('admin.dashboard') }}">
                    <img src="{{ asset('assets/admin/images/logo-light.png') }}" alt="logo" class="logo-lg"
                        height="22">
                    <img src="{{ asset('assets/admin/images/logo-sm.png') }}" alt="small logo" class="logo-sm"
                        height="22">
                </a>

                <a class='logo-dark' href="{{ route('admin.dashboard') }}">
                    <img src="{{ asset('assets/admin/images/logo-dark.png') }}" alt="dark logo" class="logo-lg"
                        height="22">
                    <img src="{{ asset('assets/admin/images/logo-sm.png') }}" alt="small logo" class="logo-sm"
                        height="22">
                </a>
            </div>

            <button class="button-toggle-menu">
                <i class="mdi mdi-menu"></i>
            </button>
        </div>

        <ul class="topbar-menu d-flex align-items-center gap-4">

            <li class="d-none d-md-inline-block">
                <a class="nav-link" href="#" data-bs-toggle="fullscreen">
                    <i class="mdi mdi-fullscreen font-size-24"></i>
                </a>
            </li>

            <li class="dropdown notification-list">
                <a class="nav-link dropdown-toggle waves-effect waves-light arrow-none" data-bs-toggle="dropdown"
                    href="#" role="button" aria-haspopup="false" aria-expanded="false">
                    <i class="mdi mdi-bell font-size-24"></i>
                    <span class="badge bg-danger rounded-circle noti-icon-badge">1</span>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated dropdown-lg py-0">
                    <div class="p-2 border-top-0 border-start-0 border-end-0 border-dashed border">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="m-0 font-size-16 fw-semibold"> Notification</h6>
                            </div>
                        </div>
                    </div>

                    <div class="px-1" style="max-height: 300px;" data-simplebar>

                        <h5 class="text-muted font-size-13 fw-normal mt-2">Today</h5>

                        <!-- item-->
                        <a href="javascript:void(0);"
                            class="dropdown-item p-0 notify-item card read-noti shadow-none mb-1">
                            <div class="card-body">
                                <span class="float-end noti-close-btn text-muted"><i class="mdi mdi-close"></i></span>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="notify-icon bg-info">
                                            <i class="mdi mdi-account-plus"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 text-truncate ms-2">
                                        <h5 class="noti-item-title fw-semibold font-size-14">Admin <small
                                                class="fw-normal text-muted ms-1">1 hours ago</small></h5>
                                        <small class="noti-item-subtitle text-muted">New user
                                            registered</small>
                                    </div>
                                </div>
                            </div>
                        </a>

                        <div class="text-center">
                            <i class="mdi mdi-dots-circle mdi-spin text-muted h3 mt-0"></i>
                        </div>
                    </div>

                    <!-- All-->
                    <a href="javascript:void(0);"
                        class="dropdown-item text-center text-primary notify-item border-top border-light py-2">
                        View All
                    </a>

                </div>
            </li>

            <li class="nav-link" id="theme-mode">
                <i class="bx bx-moon font-size-24"></i>
            </li>

            <li class="dropdown">
                <a class="nav-link dropdown-toggle nav-user me-0 waves-effect waves-light" data-bs-toggle="dropdown"
                    href="javascript:void(0);" role="button" aria-haspopup="false" aria-expanded="false">

                    <span
                        class="rounded-circle bg-info text-white d-inline-flex align-items-center justify-content-center fw-bold text-uppercase shadow-sm"
                        style="width: 32px; height: 32px; font-size: 13px;">
                        {{ auth()->user()?->initials() ?? 'U' }}
                    </span>

                    <span class="ms-1 d-none d-md-inline-block">
                        {{ auth()->user()->name ?? 'User' }} <i class="mdi mdi-chevron-down"></i>
                    </span>
                </a>

                <div class="dropdown-menu dropdown-menu-end profile-dropdown ">
                    <div class="text-center my-3 px-3">
                        <span
                            class="rounded-circle bg-info text-white d-inline-flex align-items-center justify-content-center fw-bold text-uppercase mb-2"
                            style="width: 54px; height: 54px; font-size: 22px;">
                            {{ auth()->user()?->initials() ?? 'U' }}
                        </span>

                        <h4 class="text-dark fw-bold mb-0 text-truncate px-1">
                            {{ auth()->user()?->name ?? 'User' }}

                        </h4>

                        <p class="text-muted mb-1 fs-12 text-truncate px-1">
                            {{ auth()->user()?->email ?? '-' }}
                        </p>

                        <span class="badge bg-success rounded-pill text-capitalize">
                            {{ auth()->user()?->getRoleNames()->first() ?? 'Staff' }}
                        </span>
                    </div>

                    <div class="dropdown-divider"></div>

                    <a href="javascript:void(0);" class="dropdown-item notify-item">
                        <i class="mdi mdi-account-circle"></i>
                        <span>Profile</span>
                    </a>

                    <div class="dropdown-divider"></div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="dropdown-item notify-item text-danger border-0 bg-transparent w-100 text-start">
                            <i class="mdi mdi-logout"></i>
                            <span>Keluar</span>
                        </button>
                    </form>

                </div>
            </li>
        </ul>
    </div>
</div>
