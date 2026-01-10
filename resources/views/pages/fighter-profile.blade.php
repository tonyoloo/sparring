@extends('layouts.app', [
    'class' => '',
    'elementActive' => 'fighter_profile'
])

@section('content')
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                @if(auth()->user()->fighter)
                    @php $fighter = auth()->user()->fighter; @endphp

                    <div class="card">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h4 class="card-title">{{ ucfirst($fighter->category) }} Profile</h4>
                                    <p class="card-category">Manage your {{ $fighter->category }} profile information</p>
                                </div>
                                <div class="col-md-6 text-right">
                                    @if($fighter->category === 'fighters')
                                        <a href="{{ route('directory') }}" class="btn btn-info btn-sm">
                                            <i class="fa fa-eye"></i> View in Fighters Directory
                                        </a>
                                    @elseif($fighter->category === 'professionals')
                                        <a href="{{ route('professionals') }}" class="btn btn-info btn-sm">
                                            <i class="fa fa-eye"></i> View in Professionals Directory
                                        </a>
                                    @elseif($fighter->category === 'gyms')
                                        <a href="{{ route('gyms') }}" class="btn btn-info btn-sm">
                                            <i class="fa fa-eye"></i> View in Gyms Directory
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <form action="{{ route('fighter.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="card-body">

                                        <!-- Photo Upload Section -->
                                        <div class="photo-upload-section">
                                            <div class="form-group">
                                                <label for="fighter_photos">Add Photos (Maximum 3 total)</label>
                                                <input type="file" id="fighter_photos" name="fighter_photos[]"
                                                       class="form-control" accept="image/*" multiple
                                                       {{ ($fighter->photos->count() ?? 0) >= 3 ? 'disabled' : '' }}>
                                                <small class="form-text text-muted">
                                                    You can upload up to {{ 3 - ($fighter->photos->count() ?? 0) }} more photos.
                                                    JPG, PNG or GIF. Max 2MB each.
                                                </small>
                                                <div id="photoUploadStatus" class="mt-2 text-info"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Basic Info -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Name *</label>
                                            <input type="text" name="name" class="form-control"
                                                   value="{{ $fighter->name }}" required>
                                        </div>
                                    </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Country</label>
                                                <select name="country_id" id="country_select" class="form-control">
                                                    <option value="">Select Country</option>
                                                    <!-- Countries will be loaded dynamically -->
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>City</label>
                                                <select name="city_id" id="city_select" class="form-control" disabled>
                                                    <option value="">Select Country First</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                </div>

                                @if($fighter->category === 'fighters')
                                    <!-- Fighter Specific Fields -->
                                    <!-- <h5 class="mt-4 mb-3">Fighting Information</h5> -->

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Primary Discipline *</label>
                                                <select name="discipline" id="discipline_select" class="form-control" required>
                                                    <option value="">Select Discipline</option>
                                                    <!-- Disciplines will be loaded dynamically -->
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Stance</label>
                                                <select name="stance" class="form-control">
                                                    <option value="">Select Stance</option>
                                                    <option value="orthodox" {{ $fighter->stance == 'orthodox' ? 'selected' : '' }}>Orthodox</option>
                                                    <option value="southpaw" {{ $fighter->stance == 'southpaw' ? 'selected' : '' }}>Southpaw</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Experience Level *</label>
                                                <select name="experience" class="form-control" required>
                                                    <option value="">Select Experience</option>
                                                    <option value="beginner" {{ $fighter->experience == 'beginner' ? 'selected' : '' }}>Beginner</option>
                                                    <option value="intermediate" {{ $fighter->experience == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                                                    <option value="advanced" {{ $fighter->experience == 'advanced' ? 'selected' : '' }}>Advanced</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Competition Level *</label>
                                                <select name="level" class="form-control" required>
                                                    <option value="">Select Level</option>
                                                    <option value="amateur" {{ $fighter->level == 'amateur' ? 'selected' : '' }}>Amateur</option>
                                                    <option value="semi_pro" {{ $fighter->level == 'semi_pro' ? 'selected' : '' }}>Semi Pro</option>
                                                    <option value="professional" {{ $fighter->level == 'professional' ? 'selected' : '' }}>Professional</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Height (cm)</label>
                                                <input type="number" name="height" class="form-control"
                                                       value="{{ $fighter->height }}" min="100" max="250" placeholder="Height in cm">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Weight (kg)</label>
                                                <input type="number" name="weight" class="form-control"
                                                       value="{{ $fighter->weight }}" min="30" max="200" placeholder="Weight in kg">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Age</label>
                                                <input type="number" name="age" class="form-control"
                                                       value="{{ $fighter->age }}" min="16" max="100" placeholder="Your age">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Sparring Amount (KSH)</label>
                                                <input type="number" name="spar_amount" class="form-control"
                                                       value="{{ $fighter->spar_amount }}" min="0" step="0.01" placeholder="Amount per sparring session">
                                                <small class="form-text text-muted">Optional: Amount you charge for sparring sessions</small>
                                            </div>
                                        </div>
                                    </div>

                                @elseif($fighter->category === 'professionals')
                                    <!-- Professional Specific Fields -->
                                    <h5 class="mt-4 mb-3">Professional Information</h5>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Primary Profession *</label>
                                                <select name="primary_profession" class="form-control" required>
                                                    <option value="">Select Profession</option>
                                                    <option value="strength_conditioning" {{ $fighter->primary_profession == 'strength_conditioning' ? 'selected' : '' }}>Strength & Conditioning</option>
                                                    <option value="nutritionist" {{ $fighter->primary_profession == 'nutritionist' ? 'selected' : '' }}>Nutritionist</option>
                                                    <option value="sports_psychologist" {{ $fighter->primary_profession == 'sports_psychologist' ? 'selected' : '' }}>Sports Psychologist</option>
                                                    <option value="physiotherapist" {{ $fighter->primary_profession == 'physiotherapist' ? 'selected' : '' }}>Physiotherapist</option>
                                                    <option value="sports_medical_doctor" {{ $fighter->primary_profession == 'sports_medical_doctor' ? 'selected' : '' }}>Sports Medical Doctor</option>
                                                    <option value="boxing_coach" {{ $fighter->primary_profession == 'boxing_coach' ? 'selected' : '' }}>Boxing Coach</option>
                                                    <option value="wrestling_coach" {{ $fighter->primary_profession == 'wrestling_coach' ? 'selected' : '' }}>Wrestling Coach</option>
                                                    <option value="striking_coach" {{ $fighter->primary_profession == 'striking_coach' ? 'selected' : '' }}>Striking Coach</option>
                                                    <option value="bjj_coach" {{ $fighter->primary_profession == 'bjj_coach' ? 'selected' : '' }}>BJJ Coach</option>
                                                    <option value="muay_thai_coach" {{ $fighter->primary_profession == 'muay_thai_coach' ? 'selected' : '' }}>Muay Thai Coach</option>
                                                    <option value="coaching" {{ $fighter->primary_profession == 'coaching' ? 'selected' : '' }}>Coaching</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Specialization Discipline</label>
                                                <select name="discipline" class="form-control">
                                                    <option value="">Select Discipline</option>
                                                    <option value="boxing" {{ $fighter->discipline == 'boxing' ? 'selected' : '' }}>Boxing</option>
                                                    <option value="mma" {{ $fighter->discipline == 'mma' ? 'selected' : '' }}>MMA</option>
                                                    <option value="taekwondo" {{ $fighter->discipline == 'taekwondo' ? 'selected' : '' }}>Taekwondo</option>
                                                    <option value="karate" {{ $fighter->discipline == 'karate' ? 'selected' : '' }}>Karate</option>
                                                    <option value="wrestling" {{ $fighter->discipline == 'wrestling' ? 'selected' : '' }}>Wrestling</option>
                                                    <option value="jiu_jitsu" {{ $fighter->discipline == 'jiu_jitsu' ? 'selected' : '' }}>Jiu jitsu</option>
                                                    <option value="kick_boxing" {{ $fighter->discipline == 'kick_boxing' ? 'selected' : '' }}>Kick Boxing</option>
                                                    <option value="thai_boxing" {{ $fighter->discipline == 'thai_boxing' ? 'selected' : '' }}>Thai Boxing</option>
                                                    <option value="judo" {{ $fighter->discipline == 'judo' ? 'selected' : '' }}>Judo</option>
                                                    <option value="kung_fu" {{ $fighter->discipline == 'kung_fu' ? 'selected' : '' }}>Kung Fu</option>
                                                    <option value="tai_chi" {{ $fighter->discipline == 'tai_chi' ? 'selected' : '' }}>Tai Chi</option>
                                                    <option value="wing_chun" {{ $fighter->discipline == 'wing_chun' ? 'selected' : '' }}>Wing Chun</option>
                                                    <option value="krav_maga" {{ $fighter->discipline == 'krav_maga' ? 'selected' : '' }}>Krav Maga</option>
                                                    <option value="aikido" {{ $fighter->discipline == 'aikido' ? 'selected' : '' }}>Aikido</option>
                                                    <option value="choi_kwang_do" {{ $fighter->discipline == 'choi_kwang_do' ? 'selected' : '' }}>Choi kwang do</option>
                                                    <option value="capoeira" {{ $fighter->discipline == 'capoeira' ? 'selected' : '' }}>Capoeira</option>
                                                    <option value="ninjutsu" {{ $fighter->discipline == 'ninjutsu' ? 'selected' : '' }}>Ninjutsu</option>
                                                    <option value="kendo" {{ $fighter->discipline == 'kendo' ? 'selected' : '' }}>Kendo</option>
                                                    <option value="kobudo" {{ $fighter->discipline == 'kobudo' ? 'selected' : '' }}>Kobudo</option>
                                                    <option value="hapkido" {{ $fighter->discipline == 'hapkido' ? 'selected' : '' }}>Hapkido</option>
                                                    <option value="tang_soo_do" {{ $fighter->discipline == 'tang_soo_do' ? 'selected' : '' }}>Tang soo do</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Badge Level</label>
                                                <select name="badge_level" class="form-control">
                                                    <option value="">No Badge</option>
                                                    <option value="bronze" {{ $fighter->badge_level == 'bronze' ? 'selected' : '' }}>Bronze</option>
                                                    <option value="silver" {{ $fighter->badge_level == 'silver' ? 'selected' : '' }}>Silver</option>
                                                    <option value="gold" {{ $fighter->badge_level == 'gold' ? 'selected' : '' }}>Gold</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Number of Specialties</label>
                                                <input type="number" name="profession_count" class="form-control"
                                                       value="{{ $fighter->profession_count ?? 1 }}" min="1" max="10">
                                            </div>
                                        </div>
                                    </div>

                                @elseif($fighter->category === 'gyms')
                                    <!-- Gym Specific Fields -->
                                    <h5 class="mt-4 mb-3">Gym Information</h5>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Gym Type *</label>
                                                <select name="gym_type" class="form-control" required>
                                                    <option value="">Select Gym Type</option>
                                                    <option value="boxing" {{ $fighter->gym_type == 'boxing' ? 'selected' : '' }}>Boxing</option>
                                                    <option value="mma" {{ $fighter->gym_type == 'mma' ? 'selected' : '' }}>MMA</option>
                                                    <option value="taekwondo" {{ $fighter->gym_type == 'taekwondo' ? 'selected' : '' }}>Taekwondo</option>
                                                    <option value="karate" {{ $fighter->gym_type == 'karate' ? 'selected' : '' }}>Karate</option>
                                                    <option value="wrestling" {{ $fighter->gym_type == 'wrestling' ? 'selected' : '' }}>Wrestling</option>
                                                    <option value="jiu_jitsu" {{ $fighter->gym_type == 'jiu_jitsu' ? 'selected' : '' }}>Jiu jitsu</option>
                                                    <option value="kick_boxing" {{ $fighter->gym_type == 'kick_boxing' ? 'selected' : '' }}>Kick Boxing</option>
                                                    <option value="thai_boxing" {{ $fighter->gym_type == 'thai_boxing' ? 'selected' : '' }}>Thai Boxing</option>
                                                    <option value="judo" {{ $fighter->gym_type == 'judo' ? 'selected' : '' }}>Judo</option>
                                                    <option value="kung_fu" {{ $fighter->gym_type == 'kung_fu' ? 'selected' : '' }}>Kung Fu</option>
                                                    <option value="tai_chi" {{ $fighter->gym_type == 'tai_chi' ? 'selected' : '' }}>Tai Chi</option>
                                                    <option value="wing_chun" {{ $fighter->gym_type == 'wing_chun' ? 'selected' : '' }}>Wing Chun</option>
                                                    <option value="krav_maga" {{ $fighter->gym_type == 'krav_maga' ? 'selected' : '' }}>Krav Maga</option>
                                                    <option value="aikido" {{ $fighter->gym_type == 'aikido' ? 'selected' : '' }}>Aikido</option>
                                                    <option value="choi_kwang_do" {{ $fighter->gym_type == 'choi_kwang_do' ? 'selected' : '' }}>Choi kwang do</option>
                                                    <option value="capoeira" {{ $fighter->gym_type == 'capoeira' ? 'selected' : '' }}>Capoeira</option>
                                                    <option value="ninjutsu" {{ $fighter->gym_type == 'ninjutsu' ? 'selected' : '' }}>Ninjutsu</option>
                                                    <option value="kendo" {{ $fighter->gym_type == 'kendo' ? 'selected' : '' }}>Kendo</option>
                                                    <option value="kobudo" {{ $fighter->gym_type == 'kobudo' ? 'selected' : '' }}>Kobudo</option>
                                                    <option value="hapkido" {{ $fighter->gym_type == 'hapkido' ? 'selected' : '' }}>Hapkido</option>
                                                    <option value="tang_soo_do" {{ $fighter->gym_type == 'tang_soo_do' ? 'selected' : '' }}>Tang soo do</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Gym Description</label>
                                                <textarea name="bio" class="form-control" rows="4" placeholder="Describe your gym, facilities, training programs, etc.">{{ $fighter->bio }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Contact Information</label>
                                                <input type="text" name="contact_info" class="form-control"
                                                       value="{{ $fighter->contact_info }}" placeholder="Phone, website, address, etc.">
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Bio/About Section for all types -->
                                @if($fighter->category !== 'gyms')
                                    <!-- <h5 class="mt-4 mb-3">About You</h5> -->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Bio/About</label>
                                                <textarea name="bio" class="form-control" rows="4" placeholder="Tell others about yourself, your experience, achievements, etc.">{{ $fighter->bio }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-md-12 text-center">
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            <i class="fa fa-save"></i> Update Profile
                                        </button>
                                        <a href="{{ route('home') }}" class="btn btn-secondary btn-lg ml-2">
                                            <i class="fa fa-times"></i> Cancel
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <!-- Profile Images Section (Outside main form to avoid nesting) -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">Profile Images</h5>
                                    </div>
                                    <div class="card-body">
                                        <!-- Current Photos Display -->
                                        <div id="currentPhotos" class="row mb-4">
                                            @forelse($fighter->photos ?? [] as $photo)
                                                <div class="col-md-4 mb-3">
                                                    <div class="photo-item card">
                                                        <img src="{{ $photo->photo_url }}"
                                                             alt="{{ $photo->photo_name ?? 'Photo' }}"
                                                             class="card-img-top"
                                                             style="height: 150px; object-fit: cover;">
                                                        <div class="card-body p-2">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <small class="text-muted">
                                                                    @if($photo->is_primary)
                                                                        <i class="fa fa-star text-warning"></i> Primary
                                                                    @else
                                                                        Photo {{ $loop->iteration }}
                                                                    @endif
                                                                </small>
                                                                <div>
                                                                    @if(!$photo->is_primary)
                                                                        <form action="{{ route('fighter.photo.make-primary', $photo->id) }}"
                                                                              method="POST" class="d-inline">
                                                                            @csrf
                                                                            @method('PATCH')
                                                                            <button type="submit" class="btn btn-outline-warning btn-sm"
                                                                                    title="Make Primary">
                                                                                <i class="fa fa-star"></i>
                                                                            </button>
                                                                        </form>
                                                                    @endif
                                                                    <form action="{{ route('fighter.photo.delete', $photo->id) }}"
                                                                          method="POST" class="d-inline">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="btn btn-outline-danger btn-sm"
                                                                                title="Delete Photo"
                                                                                onclick="return confirm('Are you sure you want to delete this photo?')">
                                                                            <i class="fa fa-trash"></i>
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="col-md-12">
                                                    <div class="text-center p-4 bg-light rounded">
                                                        <i class="fa fa-camera fa-2x text-muted mb-2"></i>
                                                        <p class="text-muted">No photos uploaded yet</p>
                                                    </div>
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                @else
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="fa fa-user-times fa-4x text-muted mb-4"></i>
                            <h4>No Profile Found</h4>
                            <p class="text-muted">You don't have a fighter/coach/gym profile yet. Register as one to create your profile.</p>
                            <a href="{{ route('register') }}" class="btn btn-primary">Create Profile</a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            demo.checkFullPageBackgroundImage();
        });
    </script>
@endpush
@push('scripts')
<script>
$(document).ready(function() {
    // Load countries and disciplines on page load
    loadCountries();
    loadDisciplines();

    // Profile image preview
    $('#profile_image').change(function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#profileImagePreview').attr('src', e.target.result);
            };
            reader.readAsDataURL(file);
        }
    });

    // Fighter photos preview and validation
    $('#fighter_photos').change(function() {
        const files = this.files;
        const maxFiles = 3 - {{ $fighter->photos->count() ?? 0 }};

        if (files.length > maxFiles) {
            alert('You can only upload ' + maxFiles + ' more photo(s).');
            $(this).val('');
            return;
        }

        // Validate file sizes
        for (let i = 0; i < files.length; i++) {
            if (files[i].size > 2 * 1024 * 1024) { // 2MB
                alert('File "' + files[i].name + '" is too large. Maximum size is 2MB.');
                $(this).val('');
                return;
            }
        }

        // Show selected files count
        if (files.length > 0) {
            $('#photoUploadStatus').text(files.length + ' photo(s) selected for upload');
        } else {
            $('#photoUploadStatus').text('');
        }
    });

    // Country change handler
    $(document).on('change', '#country_select', function() {
        var countryId = $(this).val();
        if (countryId) {
            loadCities(countryId);
        } else {
            $('#city_select').html('<option value="">Select Country First</option>').prop('disabled', true);
        }
    });

    // City change handler - no need for hidden field anymore
    $(document).on('change', '#city_select', function() {
        // City ID is sent directly via the form field
    });

    // Form validation
    $('form').on('submit', function(e) {
        // Add any custom validation here if needed
        return true;
    });

    function loadCountries() {
        $.ajax({
            url: '{{ route("api.countries") }}',
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    var options = '<option value="">Select Country</option>';
                    response.data.forEach(function(country) {
                        var selected = '{{ $currentCountryId }}' == country.id ? 'selected' : '';
                        options += '<option value="' + country.id + '" ' + selected + '>' + country.name + '</option>';
                    });
                    $('#country_select').html(options);

                    // If a country is pre-selected, load its cities
                    if ('{{ $currentCountryId }}') {
                        loadCities('{{ $currentCountryId }}');
                    }

                    // Set initial values for country and city selects
                    // Values are set directly via the select options above
                }
            },
            error: function() {
                console.error('Error loading countries');
            }
        });
    }

    function loadCities(countryId) {
        $.ajax({
            url: '{{ route("api.cities") }}',
            type: 'GET',
            data: { country_id: countryId },
            success: function(response) {
                if (response.success) {
                    var options = '<option value="">Select City</option>';
                    response.data.forEach(function(city) {
                        var selected = '{{ $currentCityId }}' == city.id ? 'selected' : '';
                        options += '<option value="' + city.id + '" ' + selected + '>' + city.name + '</option>';
                    });
                    $('#city_select').html(options).prop('disabled', false);
                }
            },
            error: function() {
                console.error('Error loading cities');
                $('#city_select').html('<option value="">Error loading cities</option>');
            }
        });
    }

    function loadDisciplines() {
        $.ajax({
            url: '{{ route("api.disciplines") }}',
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    var options = '<option value="">Select Discipline</option>';
                    response.data.forEach(function(discipline) {
                        var selected = '{{ $fighter->discipline_id }}' == discipline.id ? 'selected' : '';
                        options += '<option value="' + discipline.id + '" ' + selected + '>' + discipline.name + '</option>';
                    });
                    $('#discipline_select').html(options);
                }
            },
            error: function() {
                console.error('Error loading disciplines');
                $('#discipline_select').html('<option value="">Error loading disciplines</option>');
            }
        });
    }
});
</script>
@endpush
