<!-- Left side column. contains the sidebar -->
@php
    use App\Http\Helpers\AppHelper;
    $roleId = auth()->user()->role_id;
    $type = auth()->user()->type ?? null;
@endphp
<aside class="main-sidebar shadow">
    <section class="sidebar">
        <!-- sidebar menu -->
        <ul class="sidebar-menu" data-widget="tree">
            @hasTypePermission('view dashboard')
                <li>
                    <a href="{{ route('dashboard.index') }}" class="text-decoration-none">
                        <i class="fa fa-solid fa-chart-line"></i> <span>{{ __('Dashboard') }}</span>
                    </a>
                </li>
            @endHasTypePermission

            @hasTypePermission('view customer')
                <li>
                    <a href="{{ route('customer.index') }}" class="text-decoration-none">
                        <i class="fa fa-solid fa-user"></i> <span>{{ __('Customer') }}</span>
                    </a>
                </li>
            @endHasTypePermission

            @hasTypePermission('view report')
                <li>
                    <a href="{{ route('report.index') }}" class="text-decoration-none">
                        <i class="fa fa-regular fa-folder-open"></i> <span>{{ __('Daily Sale') }}</span>
                    </a>
                </li>
            @endHasTypePermission

            @if (auth()->user()->role_id === AppHelper::USER_SUPER_ADMIN || auth()->user()->role_id === AppHelper::USER_ADMINISTRATOR || auth()->user()->role_id === AppHelper::USER_ADMIN || auth()->user()->role_id === AppHelper::USER_DIRECTOR || auth()->user()->role_id === AppHelper::USER_MANAGER)
                <li class="treeview">
                    <a href="#" class="text-decoration-none">
                        <i class="fa fa-solid fa-people-roof"></i>
                        <span>{{ __('Administrator') }}</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        @hasTypePermission('view user')
                            <li>
                                <a href="{{ route('user.index') }}" class="text-decoration-none">
                                    <i class="fa fa-users"></i> <span>{{ __('Users') }}</span>
                                </a>
                            </li>
                        @endHasTypePermission
                        @hasTypePermission('view role')
                            <li>
                                <a href="{{ route('role.index') }}" class="text-decoration-none">
                                    <i class="fa fa-users"></i> <span>{{ __('Role & Permissions') }}</span>
                                </a>
                            </li>
                        @endHasTypePermission
                        @if (auth()->user()->role_id == AppHelper::USER_SUPER_ADMIN)
                            @hasTypePermission('view permission')
                                <li>
                                    <a href="{{ route('permission.index') }}" class="text-decoration-none">
                                        <i class="fa fa-snowflake"></i> <span>{{ __('Permission') }}</span>
                                    </a>
                                </li>
                            @endHasTypePermission
                        @endif
                    </ul>
                </li>
            @endif

            {{-- SE section start --}}
            @if (in_array(auth()->user()->type, [AppHelper::SE, AppHelper::ALL]))
                <li class="treeview">
                    <a href="#" class="text-decoration-none">
                        <i class="fas fa-object-group"></i>
                        <span>{{ __('Display program') }}</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        @hasTypePermission('view sub-wholesale')
                            <li>
                                <a href="{{ route('sub-wholesale.index') }}" class="text-decoration-none">
                                    <span>- {{ __('Sub wholesale') }}</span>
                                </a>
                            </li>
                        @endHasTypePermission
                        @hasTypePermission('view retail')
                            <li>
                                <a href="{{ route('retail.index') }}" class="text-decoration-none">
                                    <span>- {{ __('Retail') }}</span>
                                </a>
                            </li>
                        @endHasTypePermission
                    </ul>
                </li>

                <li class="treeview">
                    <a href="#" class="text-decoration-none">
                        <i class="fas fa-bullhorn"></i>
                        <span>{{ __('Sale promotion') }}</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        @hasTypePermission('view asm')
                            <li>
                                <a href="{{ route('asm.index') }}" class="text-decoration-none">
                                    <span>- {{ __('ASM program') }}</span>
                                </a>
                            </li>
                        @endHasTypePermission
                        @hasTypePermission('view se')
                            <li>
                                <a href="{{ route('se.index') }}" class="text-decoration-none">
                                    <span>- {{ __('SE program') }}</span>
                                </a>
                            </li>
                        @endHasTypePermission
                    </ul>
                </li>

                <li class="treeview">
                    <a href="#" class="text-decoration-none">
                        <i class="fas fa-thumbs-up"></i>
                        <span>{{ __('Exclusive') }}</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        @hasTypePermission('view school')
                            <li>
                                <a href="{{ route('school.index') }}" class="text-decoration-none">
                                    <span>- {{ __('School') }}</span>
                                </a>
                            </li>
                        @endHasTypePermission
                        @hasTypePermission('view restaurant')
                            <li>
                                <a href="{{ route('restaurant.index') }}" class="text-decoration-none">
                                    <span>- {{ __('Restaurant') }}</span>
                                </a>
                            </li>
                        @endHasTypePermission
                        @hasTypePermission('view sport club')
                            <li>
                                <a href="{{ route('sport-club.index') }}" class="text-decoration-none">
                                    <span>- {{ __('Sport club') }}</span>
                                </a>
                            </li>
                        @endHasTypePermission
                    </ul>
                </li>
            @endif
            {{-- SE section end --}}

            @if (auth()->user()->role_id == AppHelper::USER_SUPER_ADMIN)
                @hasTypePermission('view setting')
                    <li class="treeview">
                        <a href="#" class="text-decoration-none">
                            <i class="fa fa-cogs"></i> <span>{{ __('Settings') }}</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li>
                                <a href="{{ route('translation.index') }}" class="text-decoration-none">
                                    <i class="fa fa-solid fa-person-dots-from-line"></i>
                                    <span>{{ __('Translations') }}</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endHasTypePermission
            @endif
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>