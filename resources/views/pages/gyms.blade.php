@extends('layouts.app', [
    'class' => '',
    'elementActive' => 'gyms'
])

@section('content')
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Search</h4>
                        <p class="card-category">Search gyms, fighters & sports professionals.</p>

                        <!-- Category Tabs -->
                        <div class="nav-tabs-navigation">
                            <div class="nav-tabs-wrapper">
                                <ul class="nav nav-tabs" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link {{ $category === 'fighters' ? 'active' : '' }}" href="{{ route('directory') }}">
                                            <i class="fa fa-users"></i> Fighters
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ $category === 'professionals' ? 'active' : '' }}" href="{{ route('professionals') }}">
                                            <i class="fa fa-user-md"></i> Professionals
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ $category === 'gyms' ? 'active' : '' }}" href="{{ route('gyms') }}">
                                            <i class="fa fa-building"></i> Gyms
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Search and Filters -->
                        <form method="GET" action="{{ route('gyms') }}" id="gymsForm">
                            <div class="row">
                                <!-- Search Input -->
                                <div class="col-md-12 mb-3">
                                    <div class="input-group">
                                        <input type="text" name="search" value="{{ request('search') }}"
                                               class="form-control" placeholder="Search for gyms...">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="submit">
                                                <i class="fa fa-search"></i> Search
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Filter Toggle -->
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-outline-primary btn-sm" id="toggleFilters">
                                        <i class="fa fa-filter"></i> Show Search Filters
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm ml-2" id="clearFilters">
                                        Clear All Filters
                                    </button>
                                </div>
                            </div>

                            <!-- Filters Section -->
                            <div id="filtersSection" style="display: none;">
                                <div class="row">
                                    <!-- Country Filter -->
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Country</label>
                                            <select name="country" id="country_filter" class="form-control">
                                                <option value="">All Countries</option>
                                                <!-- Countries will be loaded dynamically -->
                                            </select>
                                        </div>
                                    </div>

                                    <!-- City Filter -->
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>City</label>
                                            <select name="city" id="city_filter" class="form-control" disabled>
                                                <option value="">Select Country First</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Discipline/Gym Type Filter -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Gym Type</label>
                                            <select name="gym_type" class="form-control">
                                                <option value="">All Gym Types</option>
                                                <option value="boxing" {{ request('gym_type') == 'boxing' ? 'selected' : '' }}>Boxing</option>
                                                <option value="mma" {{ request('gym_type') == 'mma' ? 'selected' : '' }}>MMA</option>
                                                <option value="taekwondo" {{ request('gym_type') == 'taekwondo' ? 'selected' : '' }}>Taekwondo</option>
                                                <option value="karate" {{ request('gym_type') == 'karate' ? 'selected' : '' }}>Karate</option>
                                                <option value="wrestling" {{ request('gym_type') == 'wrestling' ? 'selected' : '' }}>Wrestling</option>
                                                <option value="jiu_jitsu" {{ request('gym_type') == 'jiu_jitsu' ? 'selected' : '' }}>Jiu jitsu</option>
                                                <option value="kick_boxing" {{ request('gym_type') == 'kick_boxing' ? 'selected' : '' }}>Kick Boxing</option>
                                                <option value="thai_boxing" {{ request('gym_type') == 'thai_boxing' ? 'selected' : '' }}>Thai Boxing</option>
                                                <option value="judo" {{ request('gym_type') == 'judo' ? 'selected' : '' }}>Judo</option>
                                                <option value="kung_fu" {{ request('gym_type') == 'kung_fu' ? 'selected' : '' }}>Kung Fu</option>
                                                <option value="tai_chi" {{ request('gym_type') == 'tai_chi' ? 'selected' : '' }}>Tai Chi</option>
                                                <option value="wing_chun" {{ request('gym_type') == 'wing_chun' ? 'selected' : '' }}>Wing Chun</option>
                                                <option value="krav_maga" {{ request('gym_type') == 'krav_maga' ? 'selected' : '' }}>Krav Maga</option>
                                                <option value="aikido" {{ request('gym_type') == 'aikido' ? 'selected' : '' }}>Aikido</option>
                                                <option value="choi_kwang_do" {{ request('gym_type') == 'choi_kwang_do' ? 'selected' : '' }}>Choi kwang do</option>
                                                <option value="capoeira" {{ request('gym_type') == 'capoeira' ? 'selected' : '' }}>Capoeira</option>
                                                <option value="ninjutsu" {{ request('gym_type') == 'ninjutsu' ? 'selected' : '' }}>Ninjutsu</option>
                                                <option value="kendo" {{ request('gym_type') == 'kendo' ? 'selected' : '' }}>Kendo</option>
                                                <option value="kobudo" {{ request('gym_type') == 'kobudo' ? 'selected' : '' }}>Kobudo</option>
                                                <option value="hapkido" {{ request('gym_type') == 'hapkido' ? 'selected' : '' }}>Hapkido</option>
                                                <option value="tang_soo_do" {{ request('gym_type') == 'tang_soo_do' ? 'selected' : '' }}>Tang soo do</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Primary Profession Filter -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Specialties</label>
                                            <select name="profession" class="form-control">
                                                <option value="">All Specialties</option>
                                                <option value="strength_conditioning" {{ request('profession') == 'strength_conditioning' ? 'selected' : '' }}>Strength & Conditioning</option>
                                                <option value="nutritionist" {{ request('profession') == 'nutritionist' ? 'selected' : '' }}>Nutritionist</option>
                                                <option value="sports_psychologist" {{ request('profession') == 'sports_psychologist' ? 'selected' : '' }}>Sports Psychologist</option>
                                                <option value="physiotherapist" {{ request('profession') == 'physiotherapist' ? 'selected' : '' }}>Physiotherapist</option>
                                                <option value="sports_medical_doctor" {{ request('profession') == 'sports_medical_doctor' ? 'selected' : '' }}>Sports Medical Doctor</option>
                                                <option value="boxing_coach" {{ request('profession') == 'boxing_coach' ? 'selected' : '' }}>Boxing Coach</option>
                                                <option value="wrestling_coach" {{ request('profession') == 'wrestling_coach' ? 'selected' : '' }}>Wrestling Coach</option>
                                                <option value="striking_coach" {{ request('profession') == 'striking_coach' ? 'selected' : '' }}>Striking Coach</option>
                                                <option value="bjj_coach" {{ request('profession') == 'bjj_coach' ? 'selected' : '' }}>BJJ Coach</option>
                                                <option value="muay_thai_coach" {{ request('profession') == 'muay_thai_coach' ? 'selected' : '' }}>Muay Thai Coach</option>
                                                <option value="coaching" {{ request('profession') == 'coaching' ? 'selected' : '' }}>Coaching</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Apply Filters Button -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <!-- Results -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5>Viewing {{ $fighters->firstItem() ?? 0 }} - {{ $fighters->lastItem() ?? 0 }} of {{ $fighters->total() }} active gyms</h5>
                                </div>

                                <div class="row">
                                    @forelse($fighters as $gym)
                                        <div class="col-md-4 mb-4">
                                            <div class="card gym-card">
                                                <div class="card-body text-center">
                                                    <div class="gym-image mb-3">
                                                        @php
                                                            $primaryPhoto = $gym->photos->where('is_primary', true)->first();
                                                            $profileImage = $primaryPhoto ? $primaryPhoto->photo_url : ($gym->profile_image ?? asset('paper/img/gym-default.png'));
                                                        @endphp
                                                        <img src="{{ $profileImage }}"
                                                             alt="{{ $gym->name }}"
                                                             class="img-fluid rounded"
                                                             style="width: 100%; height: 120px; object-fit: cover;">
                                                    </div>

                                                    <h5 class="card-title mb-2">{{ $gym->name }}</h5>

                                                    @if($gym->gym_type)
                                                        <div class="mb-2">
                                                            <small class="text-primary font-weight-bold">
                                                                {{ ucwords(str_replace('_', ' ', $gym->gym_type)) }} Gym
                                                            </small>
                                                        </div>
                                                    @endif

                                                    @if($gym->region)
                                                        <div class="mb-2">
                                                            <small class="text-muted">
                                                                <i class="fa fa-map-marker"></i> {{ ucwords(str_replace('_', ' ', $gym->region)) }}
                                                            </small>
                                                        </div>
                                                    @endif

                                                    @if($gym->spar_amount && $gym->spar_amount > 0)
                                                        <div class="mb-2">
                                                            <span class="badge badge-success">
                                                                <i class="fa fa-building"></i> KSH {{ number_format($gym->spar_amount, 0) }}
                                                            </span>
                                                        </div>
                                                    @endif

                                                    @if($gym->bio)
                                                        <div class="mb-3">
                                                            <p class="card-text small text-muted">{{ Str::limit($gym->bio, 80) }}</p>
                                                        </div>
                                                    @endif

                                                    <div class="mt-3">
                                                        <a href="{{ route('fighter.show', $gym->id) }}"
                                                           class="btn btn-outline-primary btn-sm">
                                                            <i class="fa fa-eye"></i> View Details
                                                        </a>
                                                        <a href="{{ route('spar-request.create', $gym->id) }}"
                                                           class="btn btn-success btn-sm ml-1"
                                                           title="Request to arrange sparring at this gym">
                                                            <i class="fa fa-building"></i> Arrange Sparring
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-md-12">
                                            <div class="text-center py-5">
                                                <i class="fa fa-building fa-3x text-muted mb-3"></i>
                                                <h5 class="text-muted">No gyms found matching your criteria</h5>
                                                <p class="text-muted">Try adjusting your search filters</p>
                                            </div>
                                        </div>
                                    @endforelse
                                </div>

                                <!-- Pagination -->
                                @if($fighters->hasPages())
                                    <div class="d-flex justify-content-center mt-4">
                                        {{ $fighters->appends(request()->query())->links() }}
                                    </div>
                                @endif
                            </div>
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
    // Load countries on page load
    loadCountries();

    // Toggle filters
    $('#toggleFilters').click(function() {
        $('#filtersSection').slideToggle();
        var icon = $(this).find('i');
        if ($('#filtersSection').is(':visible')) {
            $(this).html('<i class="fa fa-times"></i> Hide Filters');
        } else {
            $(this).html('<i class="fa fa-filter"></i> Show Search Filters');
        }
    });

    // Clear all filters
    $('#clearFilters').click(function() {
        $('input[type="text"], select').val('');
        $('#gymsForm').submit();
    });

    // Country change handler
    $(document).on('change', '#country_filter', function() {
        var countryId = $(this).val();
        if (countryId) {
            loadCities(countryId);
        } else {
            $('#city_filter').html('<option value="">Select Country First</option>').prop('disabled', true);
        }
    });

    function loadCountries() {
        $.ajax({
            url: '{{ route("api.countries") }}',
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    var options = '<option value="">All Countries</option>';
                    response.data.forEach(function(country) {
                        var selected = '{{ request("country") }}' === country.id.toString() ? 'selected' : '';
                        options += '<option value="' + country.id + '" ' + selected + '>' + country.name + '</option>';
                    });
                    $('#country_filter').html(options);
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
                    var options = '<option value="">All Cities</option>';
                    response.data.forEach(function(city) {
                        var selected = '{{ request("city") }}' === city.id.toString() ? 'selected' : '';
                        options += '<option value="' + city.id + '" ' + selected + '>' + city.name + '</option>';
                    });
                    $('#city_filter').html(options).prop('disabled', false);
                }
            },
            error: function() {
                console.error('Error loading cities');
                $('#city_filter').html('<option value="">Error loading cities</option>');
            }
        });
    }
});
</script>
@endpush

<style>
.gym-card {
    transition: transform 0.2s;
    border: 1px solid #e3e3e3;
}

.gym-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.gym-image {
    border-radius: 0.375rem;
    overflow: hidden;
}

.nav-tabs .nav-link {
    border: none;
    border-bottom: 2px solid transparent;
    background: none;
    color: #666;
}

.nav-tabs .nav-link.active {
    border-bottom-color: #007bff;
    color: #007bff;
    background: none;
}

.nav-tabs .nav-link:hover {
    border-bottom-color: #007bff;
    color: #007bff;
}
</style>
