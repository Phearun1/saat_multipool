<!-- ========== Left Sidebar Start ========== -->
<div class="vertical-menu">

    <!-- LOGO -->
    <div class="navbar-brand-box">
        <a href="/" class="logo logo-dark">
            <span class="logo-sm">
                <img src="{{ URL::asset('/assets/images/logo-sm.png') }}" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="{{ URL::asset('/assets/images/logo-dark.png') }}" alt="" height="20">
            </span>
        </a>

        <a href="/" class="logo logo-light">
            <span class="logo-sm">
                <img src="{{ URL::asset('/assets/images/logo-sm.png') }}" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="{{ URL::asset('/assets/images/logo-light.png') }}" alt="" height="20">
            </span>
        </a>
    </div>

    <button type="button" class="btn btn-sm px-3 font-size-16 header-item waves-effect vertical-menu-btn">
        <i class="fa fa-fw fa-bars"></i>
    </button>

    <div data-simplebar class="sidebar-menu-scroll">

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
                <li class="menu-title">{{ __('messages.menu') }}</li>
                <li>
                    <a href="/">
                        <i class="uil-home-alt text-primary"></i>
                        <span>{{ __('messages.dashboard') }}</span>
                    </a>
                </li>

                <li class="menu-title">{{ __('messages.machine') }}</li>
                <li>
                    <a href="/view_all_machine">
                        <i class="fas fa-cogs text-warning"></i>
                        <span>{{ __('messages.view_machine') }}</span>
                    </a>
                </li>

                @if(Auth::user()->user_type == 1) <!-- Investor -->

                <li class="menu-title">{{ __('messages.wallet') }}</li>
                <li>
                    <a href="/wallet">
                        <i class="uil-wallet text-success"></i>
                        <span>{{ __('messages.wallet') }}</span>
                    </a>
                </li>

                <li class="menu-title">{{ __('messages.sponsor_account') }}</li>
                <li>
                    <a href="/sponsor_account">
                        <i class="uil-wallet text-primary"></i>
                        <span>{{ __('messages.sponsor_account') }}</span>
                    </a>
                </li>

                <li class="menu-title">{{ __('messages.new_location') }}</li>
                <li>
                    <a href="/view_new_location_request">
                        <i class="uil-location-point text-success"></i>
                        <span>{{ __('messages.new_location') }}</span>
                    </a>
                </li>

                <li class="menu-title">{{ __('messages.referral') }}</li>
                <li>
                    <a href="/view_referral">
                        <i class="fas fa-user-friends text-primary"></i>
                        <span>{{ __('messages.referral') }}</span>
                    </a>
                </li>
                @endif

                @if(Auth::user()->user_type == 5) <!-- Admin -->

                <li class="menu-title">{{ __('messages.users') }}</li>
                <li>
                    <a href="/admin/view_all_users">
                        <i class="fas fa-users text-success"></i>
                        <span>{{ __('messages.view_all_users') }}</span>
                    </a>

                    <a href="/admin/view_all_account_referral_requests">
                        <i class="fas fa-user text-warning"></i>
                        <span>{{ __('messages.view_all_account_referral_requests') }}</span>
                    </a>
                </li>

                <li class="menu-title">{{ __('messages.new_location') }}</li>
                <li>
                    <a href="/admin/view_new_location_requests">
                        <i class="fas fa-map-marker-alt text-success"></i>
                        <span>{{ __('messages.new_location') }}</span>
                    </a>
                </li>

                <li class="menu-title">{{ __('messages.maintenance_record') }}</li>
                <li>
                    <a href="/admin/view_maintenance_record">
                        <i class="fas fa-tools text-warning"></i>
                        <span>{{ __('messages.view_maintenance_record') }}</span>
                    </a>
                </li>

                <li class="menu-title">{{ __('messages.operational_partner_account') }}</li>
                <li>
                    <a href="/admin/view_operational_partner_account">
                        <i class="fas fa-user-tie text-primary"></i>
                        <span>{{ __('messages.view_operational_partner_account') }}</span>
                    </a>
                </li>

                <li class="menu-title">{{ __('messages.profit_distribution') }}</li>
                <li>
                    <a href="/admin/view_all_profit_distribution">
                        <i class="fas fa-chart-line text-primary"></i>
                        <span>{{ __('messages.view_profit_distribution') }}</span>
                    </a>
                </li>

                @endif
            </ul>

            <!-- Profile Section -->
            <ul class="metismenu list-unstyled mt-auto" id="side-menu">
                <li class="menu-title">{{ __('messages.profile') }}</li>
                <li>
                    <a href="/view_profile">
                        <i class="uil-user text-success"></i>
                        <span>{{ __('messages.profile') }}</span>
                    </a>
                </li>
            </ul>

        </div>
        <!-- Sidebar -->
    </div>
</div>
<!-- Left Sidebar End -->
