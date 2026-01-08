@extends('layouts.app', [
    'class' => '',
    'elementActive' => 'profile'
])

@section('content')
<div class="content">
    @if (session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
    @endif
    @if (session('password_status'))
        <div class="alert alert-success" role="alert">
            {{ session('password_status') }}
        </div>
    @endif


    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="title">{{ __('General Profile Settings') }}</h5>
                    <p class="card-category">{{ __('Manage your account settings and security') }}</p>
                </div>
                <div class="card-body">
                    <!-- User Information Display -->
                    <div class="mt-3">
                        <h6>User Information</h6>
                        <ul class="list-group text-left">
                            <li class="list-group-item"><strong>Name:</strong> {{ auth()->user()->name }}</li>
                            <li class="list-group-item"><strong>Email:</strong> {{ auth()->user()->email }}</li>
                        </ul>
                    </div>

                    <!-- Password Change Section -->
                    <div class="mt-4">
                        <h6>{{ __('Change Password') }}</h6>
                        <form class="col-md-12" action="{{ route('profile.password') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group{{ $errors->has('old_password') ? ' has-danger' : '' }}">
                                        <label>{{ __('Current Password') }}</label>
                                        <input type="password" name="old_password" class="form-control" placeholder="Current password" required>
                                        @if ($errors->has('old_password'))
                                        <span class="invalid-feedback" style="display: block;" role="alert">
                                            <strong>{{ $errors->first('old_password') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group{{ $errors->has('password') ? ' has-danger' : '' }}">
                                        <label>{{ __('New Password') }}</label>
                                        <input type="password" name="password" class="form-control" placeholder="New password" required>
                                        @if ($errors->has('password'))
                                        <span class="invalid-feedback" style="display: block;" role="alert">
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group{{ $errors->has('password_confirmation') ? ' has-danger' : '' }}">
                                        <label>{{ __('Confirm New Password') }}</label>
                                        <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm new password" required>
                                        @if ($errors->has('password_confirmation'))
                                        <span class="invalid-feedback" style="display: block;" role="alert">
                                            <strong>{{ $errors->first('password_confirmation') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-info btn-round">{{ __('Update Password') }}</button>
                                        <a href="{{ route('fighter.edit') }}" class="btn btn-secondary btn-round">{{ __('Edit Fighter Profile') }}</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Account Information Section -->
                    <div class="mt-4">
                        <h6>{{ __('Account Information') }}</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Account Created') }}</label>
                                    <p class="form-control-plaintext">{{ auth()->user()->created_at ? auth()->user()->created_at->format('M d, Y') : 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Last Updated') }}</label>
                                    <p class="form-control-plaintext">{{ auth()->user()->updated_at ? auth()->user()->updated_at->format('M d, Y') : 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Security Tips -->
                    <div class="mt-4">
                        <div class="alert alert-info">
                            <h6><i class="nc-icon nc-alert-circle-i"></i> {{ __('Security Tips') }}</h6>
                            <ul class="mb-0">
                                <li>{{ __('Use a strong password with at least 8 characters') }}</li>
                                <li>{{ __('Include a mix of uppercase, lowercase, numbers, and symbols') }}</li>
                                <li>{{ __('Change your password regularly') }}</li>
                                <li>{{ __('Never share your password with others') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-user">
                <div class="card-body">
                    <div class="author">
                        <div class="block block-one"></div>
                        <div class="block block-two"></div>
                        <div class="block block-three"></div>
                        <div class="block block-four"></div>
                        <a href="#pablo">
                            <img class="avatar" src="{{ asset('paper') }}/img/default-avatar.png" alt="Default Avatar">
                            <h5 class="title">{{ auth()->user()->name }}</h5>
                        </a>
                        <p class="description">{{ __('General Profile') }}</p>
                    </div>
                    <div class="card-description text-center">
                        {{ __('Manage your account settings, password, and security preferences.') }}
                    </div>
                </div>
                <div class="card-footer">
                    <div class="button-container">
                        <button class="btn btn-icon btn-round btn-facebook">
                            <i class="fab fa-facebook"></i>
                        </button>
                        <button class="btn btn-icon btn-round btn-twitter">
                            <i class="fab fa-twitter"></i>
                        </button>
                        <button class="btn btn-icon btn-round btn-google">
                            <i class="fab fa-google-plus"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
