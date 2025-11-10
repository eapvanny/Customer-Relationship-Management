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
            @hasTypePermission('view report')
                <li>
                    <a class="fw-bold p-3 text-dark m-0 text-uppercase">
                        <span>{{ __('Sale Module') }}</span>
                    </a>
                </li>
            @endHasTypePermission

            @hasTypePermission('view customer')
                <li class="treeview">
                    <a href="#" class="text-decoration-none">
                        <i class="fa fa-solid fa-users"></i>
                        <span>{{ __('Customer Management') }}</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        @hasTypePermission('view depo')
                            <li>
                                <a href="{{ route('depo.index') }}" class="text-decoration-none">
                                    <i class="fa-solid fa-store"></i> <span>{{ __('Depo') }}</span>
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
                    </ul>
                </li>
            @endHasTypePermission
            @hasTypePermission('view report')
                <li>
                    <a href="{{ route('report.index') }}" class="text-decoration-none">
                        <i class="fa fa-regular fa-folder-open"></i> <span>{{ __('Daily Sale') }}</span>
                    </a>
                </li>
            @endHasTypePermission
            @hasTypePermission('view marketing')
                <li>
                    <a class="fw-bold p-3 text-dark m-0 text-uppercase">
                        <span>{{ __('Marketing Module') }}</span>
                    </a>
                </li>
            @endHasTypePermission
            {{-- SE section start --}}

            {{-- master data start here  --}}
            @if (in_array(auth()->user()->type, [AppHelper::SE, AppHelper::ALL]))

                <li class="treeview">
                    <a href="#" class="text-decoration-none">
                        <i class="fa fa-solid fa-database"></i>
                        <span>{{ __('Master data') }}</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        @hasTypePermission('view region')
                            <li>
                                <a href="{{ route('region.index') }}" class="text-decoration-none">
                                    <i class="fa fa-solid fa-map-location-dot"></i>
                                    <span>{{ __('Region Management') }}</span>
                                </a>
                            </li>
                        @endHasTypePermission
                        @hasTypePermission('view depot management')
                            <li>
                                <a href="{{ route('outlet.index') }}" class="text-decoration-none">
                                    <i class="fa fa-solid fa-store"></i> <span>{{ __('Depot Management') }}</span>
                                </a>
                            </li>
                        @endHasTypePermission
                        @hasTypePermission('view posm')
                            <li>
                                <a href="{{ route('posm.index') }}" class="text-decoration-none">
                                    <i class="fa fa-solid fa-umbrella"></i> <span>{{ __('POSM Management') }}</span>
                                </a>
                            </li>
                        @endHasTypePermission
                        @hasTypePermission('view customer province')
                            <li>
                                <a href="{{ route('cp.index') }}" class="text-decoration-none">
                                    <i class="fa fa-solid fa-user"></i> <span>{{ __('Customer (Province)') }}</span>
                                </a>
                            </li>
                        @endHasTypePermission
                    </ul>
                </li>
            @endif
            {{-- master data end here  --}}

            {{-- Daily Sale Report Province --}}
            @hasTypePermission('view daily sale province')
                <li>
                    <a href="{{ route('reports.index') }}" class="text-decoration-none">
                        <i class="fa fa-regular fa-folder-open"></i> <span>{{ __('Daily Sale') }}</span>
                    </a>
                </li>
            @endHasTypePermission
            {{-- master data end here  --}}
            
            @if (in_array(auth()->user()->type, [AppHelper::SE, AppHelper::ALL]))

                <li class="treeview">
                    <a href="#" class="text-decoration-none">
                        <i class="fa fa-bullhorn"></i>
                        <span>{{ __('Sale Promotion') }}</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        @hasTypePermission('view asm')
                            <li>
                                <a href="{{ route('asm.index') }}" class="text-decoration-none">
                                    <i class="fa fa-circle"></i><span> {{ __('ASM Program') }}</span>
                                </a>
                            </li>
                        @endHasTypePermission
                        @hasTypePermission('view se')
                            <li>
                                <a href="{{ route('se.index') }}" class="text-decoration-none">
                                    <i class="fa fa-circle"></i><span>{{ __('SE Program') }}</span>
                                </a>
                            </li>
                        @endHasTypePermission
                    </ul>
                </li>

                <li class="treeview">
                    <a href="#" class="text-decoration-none">
                        <i class="fa fa-object-group"></i>
                        <span>{{ __('Display Program') }}</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        @hasTypePermission('view wholesale')
                            <li>
                                <a href="{{ route('wholesale.index') }}" class="text-decoration-none">
                                    <i class="fa fa-circle"></i> <span> {{ __('Wholesale') }}</span>
                                </a>
                            </li>
                        @endHasTypePermission
                        @hasTypePermission('view sub-wholesale')
                            <li>
                                <a href="{{ route('displaysub.index') }}" class="text-decoration-none">
                                    <i class="fa fa-circle"></i> <span> {{ __('Sub-Wholesale') }}</span>
                                </a>
                            </li>
                        @endHasTypePermission
                        @hasTypePermission('view retail')
                            <li>
                                <a href="{{ route('retail.index') }}" class="text-decoration-none">
                                    <i class="fa fa-circle"></i> <span> {{ __('Retail') }}</span>
                                </a>
                            </li>
                        @endHasTypePermission
                    </ul>
                </li>



                <li>
                    <a href="{{ route('exclusive.index') }}" class="text-decoration-none">
                        <i class="fa fa-thumbs-up"></i>
                        <span>{{ __('Exclusive Customer') }}</span>
                    </a>
                    {{-- <ul class="treeview-menu">
                        @hasTypePermission('view school')
                            <li>
                                <a href="{{ route('school.index') }}" class="text-decoration-none">
                                    <i class="fa fa-circle"></i> <span> {{ __('School') }}</span>
                                </a>
                            </li>
                        @endHasTypePermission
                        @hasTypePermission('view restaurant')
                            <li>
                                <a href="{{ route('restaurant.index') }}" class="text-decoration-none">
                                    <i class="fa fa-circle"></i> <span> {{ __('Restaurant') }}</span>
                                </a>
                            </li>
                        @endHasTypePermission
                        @hasTypePermission('view sport club')
                            <li>
                                <a href="{{ route('sportClub.index') }}" class="text-decoration-none">
                                    <i class="fa fa-circle"></i> <span> {{ __('Sport club') }}</span>
                                </a>
                            </li>
                        @endHasTypePermission
                    </ul> --}}
                </li>
            @endif
            {{-- SE section end --}}

            {{-- user part start here --}}

            @if (auth()->user()->role_id === AppHelper::USER_SUPER_ADMIN ||
                    auth()->user()->role_id === AppHelper::USER_ADMINISTRATOR ||
                    auth()->user()->role_id === AppHelper::USER_SUP ||
                    auth()->user()->role_id === AppHelper::USER_ADMIN ||
                    auth()->user()->role_id === AppHelper::USER_DIRECTOR ||
                    auth()->user()->role_id === AppHelper::USER_MANAGER ||
                    auth()->user()->role_id === AppHelper::USER_RSM)
                <li>
                    <a class="fw-bold p-3 text-dark m-0 text-uppercase">
                        <span>{{ __('System Operating') }}</span>
                    </a>
                </li>
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
                                    {{-- <i class="fa fa-users"></i> <span>{{ __('Users (Admin)') }}</span> --}}
                                    <i class="fa fa-users"></i> <span>{{ __('Users') }}</span>
                                </a>
                            </li>
                        @endHasTypePermission
                        @hasTypePermission('view role')
                            <li>
                                <a href="{{ route('role.index') }}" class="text-decoration-none">
                                    <i class="fa fa-users-gear"></i> <span>{{ __('Role') }}</span>
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

                {{-- <li class="treeview">
                    <a href="#" class="text-decoration-none">
                        <i class="fa fa-solid fa-user-gear"></i>
                        <span>{{ __('Role&Permission') }}</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        @hasTypePermission('view role')
                            <li>
                                <a href="{{ route('role.index') }}" class="text-decoration-none">
                                    <i class="fa fa-users-gear"></i> <span>{{ __('Role') }}</span>
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
                </li> --}}
            @endif

            {{-- user part end here  --}}

            {{-- role and permission part start here --}}
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
