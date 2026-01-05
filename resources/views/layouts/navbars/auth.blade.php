<div class="sidebar" data-color="dark" data-active-color="danger">
    <div class="logo">
        <a href="https://mobileapp.helb.co.ke" class="simple-text logo-mini">
            <div class="logo-image-small">
                <img src="{{ asset('paper') }}/img/logo.png">
            </div>
        </a>
        <a href="https://mobileapp.helb.co.ke" class="simple-text logo-normal">
            {{ __('NGUMI NETWORK') }}
        </a>
    </div>
    <div class="sidebar-wrapper">


        <ul class="nav">

            <!-- <li class="{{ $elementActive == 'studentprofile' ? 'active' : '' }}">
                <a href="{{ route('page.index', 'studentprofile') }}">
                    <i class="nc-icon nc-tablet-2"></i>
                    <p>{{ __('student profile') }}</p>
                </a>
            </li>
            <li class="{{ $elementActive == 'viewprofile' ? 'active' : '' }}">
                <a href="{{ route('page.index', 'viewprofile') }}">
                    <i class="nc-icon nc-tablet-2"></i>
                    <p>{{ __('view profile') }}</p>
                </a>
            </li>
            <li class="{{ $elementActive == 'profile' ? 'active' : '' }}">
                <a href="{{ route('profile.edit') }}">
                <i class="nc-icon nc-tablet-2"></i>
                <p>{{ __(' User Profile ') }}</p>
                </a>
            </li> -->
            <li class="{{ $elementActive == 'fighter_profile' ? 'active' : '' }}">
                <a href="{{ route('fighter.edit') }}">
                <i class="nc-icon nc-single-02"></i>
                <p>{{ __('My Profile') }}</p>
                </a>
            </li>
            <li class="{{ $elementActive == 'spar_requests' ? 'active' : '' }}">
                <a href="{{ route('spar-requests.index') }}">
                <i class="nc-icon nc-favourite-28"></i>
                <p>{{ __('Spar Requests') }}</p>
                </a>
            </li>
            <!-- Directory Links -->
            <li class="{{ $elementActive == 'directory' ? 'active' : '' }}">
                <a href="{{ route('directory') }}">
                    <i class="nc-icon nc-user-run"></i>
                    <p>{{ __('Directory') }}</p>
                </a>
            </li>











        </ul>
    </div>
</div>