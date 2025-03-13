<!-- Left side column. contains the sidebar -->
@php
    use App\Http\Helpers\AppHelper;
@endphp
<aside class="main-sidebar shadow">
    <section class="sidebar">
        <!-- sidebar menu -->
        <ul class="sidebar-menu" data-widget="tree">
            @if (auth()->user()->role_id != AppHelper::USER_EMPLOYEE)
                <li>
                    <a href="{{ URL::route('dashboard.index') }}" class="text-decoration-none">
                        <i class="fa fa-solid fa-chart-line"></i> <span> {{ __('Dashboard') }}</span>
                    </a>
                </li>
            @endif
            @can('view report')
                <li>
                    <a href="{{ URL::route('report.index') }}" class="text-decoration-none">
                        <i class="fa fa-regular fa-folder-open"></i> <span>{{ __('Daily Sale') }}</span>
                    </a>
                </li>
            @endcan
            @if (auth()->user()->role_id != AppHelper::USER_EMPLOYEE)
                <li class="treeview">
                    <a href="#" class="text-decoration-none">
                        <i class="fa fa-solid fa-people-roof"></i>
                        <span>{{ __('Administrator') }}</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        @can('view user')
                            <li>
                                <a href="{{ URL::route('user.index') }}" class="text-decoration-none">
                                    <i class="fa fa-users"></i> <span>{{ __('Users') }}</span>
                                </a>
                            </li>
                        @endcan
                        @can('view role')
                            <li>
                                <a href="{{ URL::route('role.index') }}" class="text-decoration-none">
                                    <i class="fa fa-users"></i> <span>{{ __('Role & Permissions') }}</span>
                                </a>
                            </li>
                        @endcan
                        @if (auth()->user() && auth()->user()->role_id == AppHelper::USER_SUPER_ADMIN)
                            @can('view permission')
                                <li>
                                    <a href="{{ URL::route('permission.index') }}" class="text-decoration-none">
                                        <i class="fa fa-snowflake"></i> <span>{{ __('Permission') }}</span>
                                    </a>
                                </li>
                            @endcan
                        @endif
                    </ul>
                </li>
            @endif
            @if (auth()->user() && auth()->user()->role_id == AppHelper::USER_SUPER_ADMIN)
                @can('view setting')
                    <li class="treeview">
                        <a href="#" class="text-decoration-none">
                            <i class="fa fa-cogs"></i> <span>{{ __('Settings') }}</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            {{-- @if (auth()->user() && auth()->user()->role_id == AppHelper::USER_SUPER_ADMIN)
                        <li>
                            <a href="{{ URL::route('forget.password') }}" class="text-decoration-none">
                                <i class="fa fa-eye"></i><span>{{ __('Reset Password') }}</span>
                            </a>
                        </li>
                    @endif --}}
                            <li>
                                <a href="{{ URL::route('translation.index') }}" class="text-decoration-none">
                                    <i
                                        class="fa fa-solid fa-person-dots-from-line"></i><span>{{ __('Translations') }}</span>
                                </a>
                            </li>
                        </ul>

                    </li>
                @endcan
            @endif
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>
