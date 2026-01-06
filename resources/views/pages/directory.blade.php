@extends('layouts.app', [
    'class' => '',
    'elementActive' => 'directory'
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
                                        <a class="nav-link {{ ($category ?? 'fighters') === 'fighters' ? 'active' : '' }}" href="{{ route('directory') }}">
                                            <i class="fa fa-users"></i> Fighters
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ ($category ?? 'fighters') === 'professionals' ? 'active' : '' }}" href="{{ route('professionals') }}">
                                            <i class="fa fa-user-md"></i> Professionals
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ ($category ?? 'fighters') === 'gyms' ? 'active' : '' }}" href="{{ route('gyms') }}">
                                            <i class="fa fa-building"></i> Gyms
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Search and Filters -->
                        <form method="GET" action="{{ route('directory') }}" id="directoryForm">
                            <div class="row">
                                <!-- Search Input -->
                                <div class="col-md-12 mb-3">
                                    <div class="input-group">
                                        <input type="text" name="search" value="{{ request('search') }}"
                                               class="form-control" placeholder="Search for fighters, coaches & gyms...">
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
                                    <!-- Gender Filter -->
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Gender</label>
                                            <select name="gender" class="form-control">
                                                <option value="">All</option>
                                                <option value="male" {{ request('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                                <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                            </select>
                                        </div>
                                    </div>

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

                                    <!-- Discipline Filter -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Disciplines</label>
                                            <select name="discipline" class="form-control">
                                                <option value="">All Disciplines</option>
                                                <option value="boxing" {{ request('discipline') == 'boxing' ? 'selected' : '' }}>Boxing</option>
                                                <option value="mma" {{ request('discipline') == 'mma' ? 'selected' : '' }}>MMA</option>
                                                <option value="taekwondo" {{ request('discipline') == 'taekwondo' ? 'selected' : '' }}>Taekwondo</option>
                                                <option value="karate" {{ request('discipline') == 'karate' ? 'selected' : '' }}>Karate</option>
                                                <option value="wrestling" {{ request('discipline') == 'wrestling' ? 'selected' : '' }}>Wrestling</option>
                                                <option value="jiu_jitsu" {{ request('discipline') == 'jiu_jitsu' ? 'selected' : '' }}>Jiu jitsu</option>
                                                <option value="kick_boxing" {{ request('discipline') == 'kick_boxing' ? 'selected' : '' }}>Kick Boxing</option>
                                                <option value="thai_boxing" {{ request('discipline') == 'thai_boxing' ? 'selected' : '' }}>Thai Boxing</option>
                                                <option value="judo" {{ request('discipline') == 'judo' ? 'selected' : '' }}>Judo</option>
                                                <option value="kung_fu" {{ request('discipline') == 'kung_fu' ? 'selected' : '' }}>Kung Fu</option>
                                                <option value="tai_chi" {{ request('discipline') == 'tai_chi' ? 'selected' : '' }}>Tai Chi</option>
                                                <option value="wing_chun" {{ request('discipline') == 'wing_chun' ? 'selected' : '' }}>Wing Chun</option>
                                                <option value="krav_maga" {{ request('discipline') == 'krav_maga' ? 'selected' : '' }}>Krav Maga</option>
                                                <option value="aikido" {{ request('discipline') == 'aikido' ? 'selected' : '' }}>Aikido</option>
                                                <option value="choi_kwang_do" {{ request('discipline') == 'choi_kwang_do' ? 'selected' : '' }}>Choi kwang do</option>
                                                <option value="capoeira" {{ request('discipline') == 'capoeira' ? 'selected' : '' }}>Capoeira</option>
                                                <option value="ninjutsu" {{ request('discipline') == 'ninjutsu' ? 'selected' : '' }}>Ninjutsu</option>
                                                <option value="kendo" {{ request('discipline') == 'kendo' ? 'selected' : '' }}>Kendo</option>
                                                <option value="kobudo" {{ request('discipline') == 'kobudo' ? 'selected' : '' }}>Kobudo</option>
                                                <option value="hapkido" {{ request('discipline') == 'hapkido' ? 'selected' : '' }}>Hapkido</option>
                                                <option value="tang_soo_do" {{ request('discipline') == 'tang_soo_do' ? 'selected' : '' }}>Tang soo do</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Stance Filter -->
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Stance</label>
                                            <select name="stance" class="form-control">
                                                <option value="">All</option>
                                                <option value="orthodox" {{ request('stance') == 'orthodox' ? 'selected' : '' }}>Orthodox</option>
                                                <option value="southpaw" {{ request('stance') == 'southpaw' ? 'selected' : '' }}>Southpaw</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Experience Filter -->
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Experience</label>
                                            <select name="experience" class="form-control">
                                                <option value="">All</option>
                                                <option value="beginner" {{ request('experience') == 'beginner' ? 'selected' : '' }}>Beginner</option>
                                                <option value="intermediate" {{ request('experience') == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                                                <option value="advanced" {{ request('experience') == 'advanced' ? 'selected' : '' }}>Advanced</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Level Filter -->
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Level</label>
                                            <select name="level" class="form-control">
                                                <option value="">All</option>
                                                <option value="amateur" {{ request('level') == 'amateur' ? 'selected' : '' }}>Amateur</option>
                                                <option value="semi_pro" {{ request('level') == 'semi_pro' ? 'selected' : '' }}>Semi Pro</option>
                                                <option value="professional" {{ request('level') == 'professional' ? 'selected' : '' }}>Professional</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Height Filter -->
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Height (cm)</label>
                                            <input type="number" name="height_min" value="{{ request('height_min') }}"
                                                   class="form-control" placeholder="Min">
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <input type="number" name="height_max" value="{{ request('height_max') }}"
                                                   class="form-control" placeholder="Max">
                                        </div>
                                    </div>

                                    <!-- Weight Filter -->
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Weight (kg)</label>
                                            <input type="number" name="weight_min" value="{{ request('weight_min') }}"
                                                   class="form-control" placeholder="Min">
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <input type="number" name="weight_max" value="{{ request('weight_max') }}"
                                                   class="form-control" placeholder="Max">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Primary Profession Filter -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Primary Profession</label>
                                            <select name="profession" class="form-control">
                                                <option value="">All</option>
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

                                    <!-- Category Filter -->
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Category</label>
                                            <select name="category" class="form-control">
                                                <option value="">All</option>
                                                <option value="fighters" {{ request('category') == 'fighters' ? 'selected' : '' }}>Fighters</option>
                                                <option value="professionals" {{ request('category') == 'professionals' ? 'selected' : '' }}>Professionals</option>
                                                <option value="gyms" {{ request('category') == 'gyms' ? 'selected' : '' }}>Gyms</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Apply Filters Button -->
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <button type="submit" class="btn btn-primary btn-block">Apply Filters</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <!-- Results -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5>Viewing {{ $fighters->firstItem() ?? 0 }} - {{ $fighters->lastItem() ?? 0 }} of {{ $fighters->total() }} active members</h5>
                                </div>

                                <div class="row">
                                    @forelse($fighters as $fighter)
                                        <div class="col-md-4 mb-4">
                                            <div class="card profile-card">
                                                <div class="card-body text-center">
                                                    @if($fighter->level === 'professional')
                                                        <span class="badge badge-warning position-absolute" style="top: 10px; right: 10px;">Professional</span>
                                                    @elseif($fighter->level === 'semi_pro')
                                                        <span class="badge badge-info position-absolute" style="top: 10px; right: 10px;">Semi Pro</span>
                                                    @else
                                                        <span class="badge badge-secondary position-absolute" style="top: 10px; right: 10px;">Amateur</span>
                                                    @endif

                                                    @php
                                                        $primaryPhoto = $fighter->photos->where('is_primary', true)->first();
                                                        $profileImage = $primaryPhoto ? $primaryPhoto->photo_url : ($fighter->profile_image ?? asset('paper/img/default-avatar.png'));
                                                    @endphp
                                                    <img src="{{ $profileImage }}"
                                                         alt="{{ $fighter->name }}"
                                                         class="rounded-circle mb-3"
                                                         style="width: 80px; height: 80px; object-fit: cover;">

                                                    <h5 class="card-title mb-1">{{ $fighter->name }}</h5>

                                                    <div class="mb-2">
                                                        <small class="text-muted">
                                                            Discipline: {{ ucfirst(str_replace('_', ' ', $fighter->discipline ?? 'N/A')) }}
                                                            @if($fighter->discipline_count > 1)
                                                                +{{ $fighter->discipline_count - 1 }}
                                                            @endif
                                                        </small>
                                                    </div>

                                                    @if($fighter->spar_amount && $fighter->spar_amount > 0)
                                                        <div class="mb-2">
                                                            <span class="badge badge-success">
                                                                <i class="fa fa-money"></i> KSH {{ number_format($fighter->spar_amount, 0) }}
                                                            </span>
                                                        </div>
                                                    @endif

                                                    <div class="row text-center">
                                                        <div class="col-4">
                                                            <small class="text-muted d-block">Age</small>
                                                            <strong>{{ $fighter->age ?? '-' }}</strong>
                                                        </div>
                                                        <div class="col-4">
                                                            <small class="text-muted d-block">Weight</small>
                                                            <strong>{{ $fighter->weight ? $fighter->weight . 'kg' : '-' }}</strong>
                                                        </div>
                                                        <div class="col-4">
                                                            <small class="text-muted d-block">Experience</small>
                                                            <strong>{{ ucfirst($fighter->experience ?? '-') }}</strong>
                                                        </div>
                                                    </div>

                                                    <div class="mt-3">
                                                        <a href="{{ route('fighter.show', $fighter->id) }}"
                                                           class="btn btn-outline-primary btn-sm">
                                                            <i class="fa fa-eye"></i> View Profile
                                                        </a>
                                                        <a href="{{ route('spar-request.create', $fighter->id) }}"
                                                           class="btn btn-success btn-sm ml-1">
                                                            <i class="fa fa-hand-rock"></i> Request to Spar
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-md-12">
                                            <div class="text-center py-5">
                                                <i class="fa fa-search fa-3x text-muted mb-3"></i>
                                                <h5 class="text-muted">No fighters found matching your criteria</h5>
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
        $('input[type="text"], input[type="number"], select').val('');
        $('#directoryForm').submit();
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

    // Auto-submit on filter change (optional)
    // $('select').change(function() {
    //     $('#directoryForm').submit();
    // });

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
.profile-card {
    transition: transform 0.2s;
    border: 1px solid #e3e3e3;
}

.profile-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.badge {
    font-size: 0.7em;
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
