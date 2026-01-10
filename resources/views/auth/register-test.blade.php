@extends('layouts.app', [
    'class' => 'register-page',
    'backgroundImagePath' => 'img/bg/jan-sendereks.jpg'
])

@section('content')
<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-lg-5 col-md-5 ml-auto">
                <div class="apprentice-spaces-content" style="max-width: 980px; width: 100%; max-height: 70vh; overflow-y: auto; padding: 20px 10px; background: transparent; color: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                    <h2>Test Registration</h2>
                    <p>This is a test registration form that bypasses email verification to isolate issues.</p>
                    <hr />
                    <h5>Debug Information:</h5>
                    <ul>
                        <li>Route: {{ route('register.test') }}</li>
                        <li>Current Time: {{ now()->format('Y-m-d H:i:s') }}</li>
                        <li>CSRF Token: Present</li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mr-auto">
                <div class="card card-signup text-center">
                    <div class="card-header ">
                        <h4 class="card-title">Test Registration</h4>
                        <div class="social">
                            <img src="{{ asset('paper') }}/img/logo.png" style="width: 50%; height: auto;">
                            <p class="card-description">Test registration form</p>
                        </div>
                    </div>
                    <div class="card-body ">
                        <form class="form" method="POST" action="{{ route('register.test') }}" id="testRegistrationForm">
                            @csrf

                            <!-- Registration Type Selection -->
                            <div class="input-group{{ $errors->has('registration_type') ? ' has-danger' : '' }}">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="nc-icon nc-tag-content"></i>
                                    </span>
                                </div>
                                <select name="registration_type" id="registration_type" class="form-control" required>
                                    <option value="">Select Registration Type</option>
                                    <option value="fighter" {{ old('registration_type') == 'fighter' ? 'selected' : '' }}>Fighter</option>
                                    <option value="professional" {{ old('registration_type') == 'professional' ? 'selected' : '' }}>Coach/Professional</option>
                                    <option value="gym" {{ old('registration_type') == 'gym' ? 'selected' : '' }}>Gym</option>
                                </select>
                                @if ($errors->has('registration_type'))
                                <span class="invalid-feedback" style="display: block;" role="alert">
                                    <strong>{{ $errors->first('registration_type') }}</strong>
                                </span>
                                @endif
                            </div>

                            <div class="input-group{{ $errors->has('name') ? ' has-danger' : '' }}">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="nc-icon nc-single-02"></i>
                                    </span>
                                </div>
                                <input name="name" type="text" class="form-control" placeholder="Name" value="{{ old('name') }}" required>
                                @if ($errors->has('name'))
                                <span class="invalid-feedback" style="display: block;" role="alert">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                                @endif
                            </div>

                            <div class="input-group{{ $errors->has('email') ? ' has-danger' : '' }}">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="nc-icon nc-email-85"></i>
                                    </span>
                                </div>
                                <input name="email" type="email" class="form-control" placeholder="Email" required value="{{ old('email') }}">
                                @if ($errors->has('email'))
                                <span class="invalid-feedback" style="display: block;" role="alert">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                                @endif
                            </div>

                            <div class="input-group{{ $errors->has('password') ? ' has-danger' : '' }}">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="nc-icon nc-key-25"></i>
                                    </span>
                                </div>
                                <input name="password" type="password" class="form-control" placeholder="Password" required>
                                @if ($errors->has('password'))
                                <span class="invalid-feedback" style="display: block;" role="alert">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                                @endif
                            </div>

                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="nc-icon nc-key-25"></i>
                                    </span>
                                </div>
                                <input name="password_confirmation" type="password" class="form-control" placeholder="Password confirmation" required>
                                @if ($errors->has('password_confirmation'))
                                <span class="invalid-feedback" style="display: block;" role="alert">
                                    <strong>{{ $errors->first('password_confirmation') }}</strong>
                                </span>
                                @endif
                            </div>

                            <!-- Fighter Fields (simplified for testing) -->
                            <div id="fighter_fields" style="display: none;">
                                <h5 class="text-left mt-3 mb-3">Fighter Information</h5>

                                <div class="input-group{{ $errors->has('gender') ? ' has-danger' : '' }}">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="nc-icon nc-single-02"></i>
                                        </span>
                                    </div>
                                    <select name="gender" class="form-control">
                                        <option value="">Select Gender</option>
                                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                    </select>
                                    @if ($errors->has('gender'))
                                    <span class="invalid-feedback" style="display: block;" role="alert">
                                        <strong>{{ $errors->first('gender') }}</strong>
                                    </span>
                                    @endif
                                </div>

                                <div class="input-group{{ $errors->has('discipline') ? ' has-danger' : '' }}">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="nc-icon nc-tag-content"></i>
                                        </span>
                                    </div>
                                    <select name="discipline" class="form-control" required>
                                        <option value="">Select Primary Discipline</option>
                                        <option value="boxing" {{ old('discipline') == 'boxing' ? 'selected' : '' }}>Boxing</option>
                                        <option value="mma" {{ old('discipline') == 'mma' ? 'selected' : '' }}>MMA</option>
                                    </select>
                                    @if ($errors->has('discipline'))
                                    <span class="invalid-feedback" style="display: block;" role="alert">
                                        <strong>{{ $errors->first('discipline') }}</strong>
                                    </span>
                                    @endif
                                </div>

                                <div class="input-group{{ $errors->has('experience') ? ' has-danger' : '' }}">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="nc-icon nc-chart-bar-32"></i>
                                        </span>
                                    </div>
                                    <select name="experience" class="form-control" required>
                                        <option value="">Select Experience Level</option>
                                        <option value="beginner" {{ old('experience') == 'beginner' ? 'selected' : '' }}>Beginner</option>
                                        <option value="intermediate" {{ old('experience') == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                                        <option value="advanced" {{ old('experience') == 'advanced' ? 'selected' : '' }}>Advanced</option>
                                    </select>
                                    @if ($errors->has('experience'))
                                    <span class="invalid-feedback" style="display: block;" role="alert">
                                        <strong>{{ $errors->first('experience') }}</strong>
                                    </span>
                                    @endif
                                </div>

                                <div class="input-group{{ $errors->has('level') ? ' has-danger' : '' }}">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="nc-icon nc-trophy"></i>
                                        </span>
                                    </div>
                                    <select name="level" class="form-control" required>
                                        <option value="">Select Competition Level</option>
                                        <option value="amateur" {{ old('level') == 'amateur' ? 'selected' : '' }}>Amateur</option>
                                        <option value="semi_pro" {{ old('level') == 'semi_pro' ? 'selected' : '' }}>Semi Pro</option>
                                        <option value="professional" {{ old('level') == 'professional' ? 'selected' : '' }}>Professional</option>
                                    </select>
                                    @if ($errors->has('level'))
                                    <span class="invalid-feedback" style="display: block;" role="alert">
                                        <strong>{{ $errors->first('level') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Location Fields -->
                            <div id="common_fields" style="display: none;">
                                <h5 class="text-left mt-3 mb-3">Location Information</h5>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="input-group{{ $errors->has('country_id') ? ' has-danger' : '' }}">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="nc-icon nc-globe"></i>
                                                </span>
                                            </div>
                                            <select name="country_id" id="registration_country_select" class="form-control">
                                                <option value="">Select Country</option>
                                                <!-- Countries will be loaded dynamically -->
                                            </select>
                                            @if ($errors->has('country_id'))
                                            <span class="invalid-feedback" style="display: block;" role="alert">
                                                <strong>{{ $errors->first('country_id') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group{{ $errors->has('city_id') ? ' has-danger' : '' }}">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="nc-icon nc-pin-3"></i>
                                                </span>
                                            </div>
                                            <select name="city_id" id="registration_city_select" class="form-control" disabled>
                                                <option value="">Select Country First</option>
                                            </select>
                                            @if ($errors->has('city_id'))
                                            <span class="invalid-feedback" style="display: block;" role="alert">
                                                <strong>{{ $errors->first('city_id') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-check text-left">
                                <label class="form-check-label">
                                    <input class="form-check-input" name="agree_terms_and_conditions" type="checkbox" required>
                                    <span class="form-check-sign"></span>
                                    {{ __('I agree to the') }}
                                    <a href="#something">{{ __('terms and conditions') }}</a>.
                                </label>
                                @if ($errors->has('agree_terms_and_conditions'))
                                <span class="invalid-feedback" style="display: block;" role="alert">
                                    <strong>{{ $errors->first('agree_terms_and_conditions') }}</strong>
                                </span>
                                @endif
                            </div>

                            <div class="card-footer ">
                                <button type="submit" class="btn btn-info btn-round">{{ __('Register Test') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Handle registration type change
        $('#registration_type').change(function() {
            var selectedType = $(this).val();

            // Hide all conditional fields
            $('#fighter_fields').hide();
            $('#common_fields').hide();

            // Show relevant fields based on selection
            if (selectedType === 'fighter') {
                $('#fighter_fields').show();
                $('#common_fields').show();
                loadRegistrationCountries();
            }
        });

        // Add form submission logging
        $('#testRegistrationForm').on('submit', function(e) {
            console.log('Test registration form submitted');

            // Log form data
            var formData = $(this).serializeArray();
            var logData = {};
            formData.forEach(function(item) {
                logData[item.name] = item.value;
            });

            console.log('Test form data:', logData);
        });
    });

    function loadRegistrationCountries() {
        $.ajax({
            url: '{{ route("api.countries") }}',
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    var options = '<option value="">Select Country</option>';
                    response.data.forEach(function(country) {
                        options += '<option value="' + country.id + '">' + country.name + '</option>';
                    });
                    $('#registration_country_select').html(options);
                }
            },
            error: function() {
                console.error('Error loading countries');
            }
        });
    }

    $(document).on('change', '#registration_country_select', function() {
        var countryId = $(this).val();
        if (countryId) {
            loadRegistrationCities(countryId);
        } else {
            $('#registration_city_select').html('<option value="">Select Country First</option>').prop('disabled', true);
        }
    });

    function loadRegistrationCities(countryId) {
        $.ajax({
            url: '{{ route("api.cities") }}',
            type: 'GET',
            data: { country_id: countryId },
            success: function(response) {
                if (response.success) {
                    var options = '<option value="">Select City</option>';
                    response.data.forEach(function(city) {
                        options += '<option value="' + city.id + '">' + city.name + '</option>';
                    });
                    $('#registration_city_select').html(options).prop('disabled', false);
                }
            },
            error: function() {
                console.error('Error loading cities');
                $('#registration_city_select').html('<option value="">Error loading cities</option>');
            }
        });
    }
</script>
@endpush
