@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Facades\Auth;
    use App\Http\Helpers\AppHelper;
    use App\Models\Report;
    $authUser = Auth::user();
    $photoPath = $authUser->photo ? asset('storage/' . $authUser->photo) : asset('images/avatar.png');
@endphp
<header class="main-header shadow-sm">
    <!-- Logo -->
    <a href="{{ URL::route('dashboard.index') }}" class="logo hidden-xs logo-hitech">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini">
            <img src="{{ asset('images/Background1.jpg') }}" alt="logo-mini" style="border-radius: 50%; margin-top: 14px">
        </span>
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg" style="width: 60%; height:100%; margin: auto">
            <img src="{{ asset('images/Hi-Tech_Water_Logo.png') }}" alt="logo-md">
        </span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-expand-lg p-0 inline-flex justify-content-between">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle text-decoration-none fas " data-toggle="push-menu" role="button">

        </a>

        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <!-- Site Start -->
                {{-- @if ($frontend_website)
                    <li class="dropdown site-menu mx-2">
                        <a target="_blank" title="Site" href="{{ URL::route('home') }}" class="dropdown-toggle"
                            data-toggle="tooltip" title="" data-placement="bottom"
                            data-original-title="Visit Site">
                            <i class="fa fa-globe"></i>
                        </a>
                    </li>
                @endif --}}
                <!-- Site Close -->
                <!-- Notifications: style can be found in dropdown.less-->
                {{-- <li class="dropdown messages-menu mx-2">
                    <a href="#" class="dropdown-toggle text-decoration-none" data-bs-toggle="dropdown" aria-expanded="false"    >
                        <i class="fa fa-bell-o"></i>
                        <span class="label label-danger"><lable class="alert-image notification_badge">0</lable></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="header notificaton_header">You have 0 recent notifications</li>
                        <li>
                            <ul class="menu notification_top"></ul>
                        </li>
                        <li class="footer"><a href="{{route('user.notification_unread')}}">See All Notifications</a></li>
                    </ul>
                </li> --}}
                {{-- @if ($show_language)
                <li class="dropdown lang-menu mx-2">
                    <a href="#" class="dropdown-toggle text-decoration-none" data-bs-toggle="dropdown">
                        <img class="language-img" src="{{ asset('images/lang/'.$locale.'.png') }}">
                        <span class="label label-warning">2</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="header"> Language</li>
                        @foreach ($languages as $key => $lang)
                            <li class="language" id="bangla">
                                <a href="#">
                                    <div class="pull-left">
                                        <img src="{{ asset('images/lang/'.$key.'.png') }}">
                                    </div>
                                    <h4>
                                        {{$lang}} @if ($locale == $key) <i class="glyphicon glyphicon-ok green pull-right"></i> @endif
                                    </h4>
                                </a>
                            </li>
                        @endforeach
                        <li class="footer"></li>
                    </ul>
                </li>
                @endif
                <!-- User Account: style can be found in dropdown.less -->
                <li class="dropdown user user-menu mx-2">
                    <a href="#" class="dropdown-toggle text-decoration-none" data-bs-toggle="dropdown">
                        <i class="fa fa-user"></i>
                        <span class="hidden-xs">{{getAuthUser()->username}}</span><i class="caret"></i>
                    </a>

                    <ul class="dropdown-menu">
                        <!-- Menu Body -->
                        <li class="user-body">
                            <div class="col-xs-6 text-center">
                                <a href="{{ URL::route('profile') }}">
                                    <div><i class="fa fa-briefcase"></i></div>
                                    Profile
                                </a>
                            </div>
                            <div class="col-xs-6 text-center password">
                                <a href="{{ URL::route('change_password') }}">
                                    <div><i class="fa fa-lock"></i></div>
                                Password
                                </a>
                            </div>
                        </li>

                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="col-xs-6 text-center">
                                <a href="{{ URL::route('logout') }}">
                                    <div><i class="fa fa-power-off"></i></div>
                                    Log out
                                </a>
                            </div>
                            <div class="col-xs-6 text-center password">
                                <a href="{{ URL::route('lockscreen') }}">
                                    <div><i class="fa fa-eye-slash"></i></div>
                                    Lock Screen
                                </a>
                            </div>
                        </li>
                    </ul>
                </li>
                <!-- Control Sidebar Toggle Button -->
                <li>
                    <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears mx-2"></i></a>
                </li>  --}}


                {{--    Upgrade Stype NavItems --}}
                <div class="d-flex align-items-center">

                    <!-- Languages -->
                    <div class="dropdown mx-3 language">
                        <div data-mdb-dropdown-init class="main-language text-reset dropdown-toggle hidden-dropdow-xs"
                            data-bs-toggle="dropdown" href="#" id="navbarDropdownMenuLink" role="button"
                            aria-expanded="false">
                            <a href="javascript:void(0);">
                                <span class="icon-language">
                                    <img src="{{ asset('./images/' . session('user_lang', 'kh') . '.png') }}"
                                        alt="{{ session('user_lang', 'kh') == 'kh' ? 'Khmer' : 'English' }}"
                                        loading="lazy" />
                                </span>
                                <span class="label-language">
                                    <small>{{ session('user_lang', 'kh') == 'kh' ? 'KH' : 'EN' }}</small>
                                </span>
                            </a>
                        </div>
                        <ul class="dropdown-menu dropdown-menu-end position-absolute"
                            aria-labelledby="navbarDropdownMenuLink">
                            <li>
                                <a class="dropdown-item" href="{{ route('user.set_lang', 'en') }}">
                                    <img src="{{ asset('./images/en.png') }}" alt="English" loading="lazy" />
                                    English
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('user.set_lang', 'kh') }}">
                                    <img src="{{ asset('./images/kh.png') }}" alt="Khmer" loading="lazy" />
                                    ភាសាខ្មែរ
                                </a>
                            </li>
                        </ul>
                    </div>


                    <div class="sepa-menu-header"></div>
                    <!-- Messages -->
                    <div class="dropdown mx-2">
                        <a data-mdb-dropdown-init class="notifi-icon text-reset dropdown-toggle" href="#"
                            id="navbarDropdownMenuLink" data-bs-toggle="dropdown" role="button" aria-expanded="false">
                            <i class="fa-regular fa-envelope"><small class="d-none">1</small></i>
                        </a>
                        <ul
                            class="dropdown-menu dropdown-menu-end position-absolute"aria-labelledby="navbarDropdownMenuLink">
                            <li>
                                <a class="dropdown-item" href="#">{{ __('No mail available.') }}</a>
                            </li>

                        </ul>
                    </div>
                    <!-- Notifications -->
                    <div class="dropdown mx-2">
                        <?php
                        $user = auth()->user();
                        $isAdminOrManager = in_array($user->role_id, [AppHelper::USER_SUPER_ADMIN, AppHelper::USER_ADMIN, AppHelper::USER_MANAGER]);
                        
                        $totalReports = 0; // Default to 0 for regular users
                        
                        if ($isAdminOrManager) {
                            if (in_array($user->role_id, [AppHelper::USER_SUPER_ADMIN, AppHelper::USER_ADMIN])) {
                                // Admins & Super Admins see all reports
                                $totalReports = Report::whereNull('deleted_at')->count();
                            } elseif ($user->role_id == AppHelper::USER_MANAGER) {
                                // Managers only see reports from employees they manage
                                $totalReports = Report::whereNull('deleted_at')
                                    ->whereIn('user_id', \App\Models\User::where('manager_id', $user->id)->pluck('id'))
                                    ->count();
                            }
                        }
                        
                        $badgeText = $totalReports > 5 ? '<span style="font-size: 9px;">5+</span>' : ($totalReports > 0 ? $totalReports : '');
                        ?>

                        @if ($isAdminOrManager)
                            <a data-mdb-dropdown-init class="notifi-icon text-reset dropdown-toggle show-notification"
                                id="navbarDropdownMenuLink" data-bs-toggle="dropdown" role="button"
                                aria-expanded="false">
                                <i class="fa-bell-o fa-regular fa-bell">
                                    <small class="notification_badge">
                                        <?= $badgeText ?>
                                    </small>
                                </i>
                            </a>
                        @endif

                        <ul class="dropdown-menu dropdown-menu-end notifications position-absolute mt-2 p-2"
                            aria-labelledby="navbarDropdownMenuLink">
                            <li>
                                <a class="dropdown-item notificaton_header" href="#">
                                    {{ __('Notifications') }}
                                </a>
                            </li>
                            <li class="dropdown-divider"></li>
                            <li>
                                <ul class="notification_top">
                                    <span class="notification-item" style="color: #777">
                                        <div class="notification-subject"></div>
                                    </span>
                                </ul>
                            </li>

                            @if ($totalReports != 0)
                                <li class="dropdown-divider"></li>
                            @endif
                            <li>
                                <a class="dropdown-item text-primary" href="{{ route('report.index') }}">
                                    {{ __('See All Reports') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="sepa-menu-header"></div>
                    <!-- Avatar -->
                    <div class="dropdown avatar me-3 ps-4">
                        <a data-mdb-dropdown-init class="dropdown-toggle d-flex align-items-center hidden-dropdow-xs"
                            href="#" id="navbarDropdownMenuAvatar" role="button" aria-expanded="false"
                            data-bs-toggle="dropdown">
                            <img style="border: 1.5px solid #6c6c6c"
                                class="bg-dark bg-opacity-10 object-fit-cover rounded-circle" width="40px"
                                height="40px" alt="user" loading="lazy" src="{{ $photoPath }}">

                            <span class="hidden-xs mx-3">
                                {{ $authUser->username ?? 'Guest' }}
                                <br>
                                <small>{{ $authUser->role->name ?? 'Guest' }}</small>
                            </span>

                        </a>
                        <ul class="dropdown-menu dropdown-menu-end position-absolute mt-2 p-2"
                            aria-labelledby="navbarDropdownMenuAvatar">

                            <li>
                                <a class="dropdown-item" href="{{ URL::route('profile') }}">
                                    <i class="fa fa-solid fa-user"></i>
                                    {{ __('My Profile') }}
                                </a>
                            </li>

                            <li>
                                <a class="dropdown-item" href="{{ URL::route('change_password') }}">
                                    <i class="fa fa-solid fa-lock"></i>
                                    {{ __('Password') }}
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ URL::route('lockscreen') }}">
                                    <i class="fa fa-solid fa-sharp fa-eye-slash"></i>
                                    {{ __('Lock Screen') }}
                                </a>
                            </li>
                            <li class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="{{ route('logout') }}">
                                    <i class="fa fa-solid fa-right-from-bracket"></i>
                                    {{ __('Logout') }}
                                </a>
                            </li>

                        </ul>
                    </div>
                </div>
            </ul>
        </div>
    </nav>
</header>
<div class="modal modal-lg fade" id="showContact" tabindex="-1" role="dialog" aria-labelledby="showContactLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content rounded-0">
            <div class="modal-header">
                <h5 class="modal-title" id="showContactLabel">{{ __('Supporter') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" id="btnClose"
                    aria-label="Close"></button>
            </div>

            <div class="modal-body d-flex justify-content-center gap-3 flex-wrap" id="modal-body-content">
                <!-- Contacts will be loaded dynamically -->
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    {{ __('Close') }}
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(document).ready(function() {
            let isAdminOrManager =
                "{{ in_array(auth()->user()->role_id, [AppHelper::USER_SUPER_ADMIN, AppHelper::USER_ADMIN, AppHelper::USER_MANAGER]) ? 'true' : 'false' }}";

            if (isAdminOrManager === 'false') {
                $(".show-notification").remove();
                return;
            }

            $('.show-notification').on('click', function() {
                $.ajax({
                    url: "{{ route('get-reports') }}",
                    method: "GET",
                    dataType: "json",
                    success: function(data) {
                        let notificationList = $(".notification_top");
                        notificationList.empty();

                        if (data.length > 0) {
                            data.forEach(function(report) {
                                let listItem = `
                            <li class="notification-item d-flex align-items-center p-2 border-bottom">
                                <img src="${report.photo}" class="rounded-circle" style="height: 35px; width: 35px; object-fit: cover; margin-right: 10px;">
                                <div class="d-flex flex-column">
                                    <span><strong>${report.family_name} ${report.name}</strong> (${report.area})</span>
                                </div>
                            </li>`;
                                notificationList.append(listItem);
                            });
                        } else {
                            notificationList.append(
                                `<li class="notification-item text-muted p-2">No new reports</li>`
                            );
                        }
                    },
                    error: function() {
                        console.error("Failed to fetch reports.");
                    }
                });
            });
        });
    </script>
@endpush
