@extends('layouts.app', [
    'class' => '',
    'elementActive' => 'professionals'
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
                        <form method="GET" action="{{ route('professionals') }}" id="professionalsForm">
                            <div class="row">
                                <!-- Search Input -->
                                <div class="col-md-12 mb-3">
                                    <div class="input-group">
                                        <input type="text" name="search" value="{{ request('search') }}"
                                               class="form-control" placeholder="Search for professionals...">
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
                                    <!-- Region Filter -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Region</label>
                                            <select name="region" class="form-control">
                                                <option value="">All Regions</option>
                                                <option value="east" {{ request('region') == 'east' ? 'selected' : '' }}>East</option>
                                                <option value="south_east" {{ request('region') == 'south_east' ? 'selected' : '' }}>South East</option>
                                                <option value="south_west" {{ request('region') == 'south_west' ? 'selected' : '' }}>South West</option>
                                                <option value="west_midlands" {{ request('region') == 'west_midlands' ? 'selected' : '' }}>West Midlands</option>
                                                <option value="london" {{ request('region') == 'london' ? 'selected' : '' }}>London</option>
                                                <option value="north_east" {{ request('region') == 'north_east' ? 'selected' : '' }}>North East</option>
                                                <option value="north_west" {{ request('region') == 'north_west' ? 'selected' : '' }}>North West</option>
                                                <option value="yorkshire_humber" {{ request('region') == 'yorkshire_humber' ? 'selected' : '' }}>Yorkshire & Humber</option>
                                                <option value="east_midlands" {{ request('region') == 'east_midlands' ? 'selected' : '' }}>East Midlands</option>
                                                <option value="northern_ireland" {{ request('region') == 'northern_ireland' ? 'selected' : '' }}>Northern Ireland</option>
                                                <option value="scotland" {{ request('region') == 'scotland' ? 'selected' : '' }}>Scotland</option>
                                                <option value="wales" {{ request('region') == 'wales' ? 'selected' : '' }}>Wales</option>
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

                                    <!-- Primary Profession Filter -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Primary Profession</label>
                                            <select name="profession" class="form-control">
                                                <option value="">All Professions</option>
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
                                    <h5>Viewing {{ $fighters->firstItem() ?? 0 }} - {{ $fighters->lastItem() ?? 0 }} of {{ $fighters->total() }} active members</h5>
                                </div>

                                <div class="row">
                                    @forelse($fighters as $professional)
                                        <div class="col-md-4 mb-4">
                                            <div class="card professional-card">
                                                <div class="card-body text-center">
                                                    @if(isset($professional->badge_level) && $professional->badge_level === 'bronze')
                                                        <span class="badge badge-warning position-absolute" style="top: 10px; right: 10px; z-index: 10;">
                                                            <i class="fa fa-medal"></i> Bronze
                                                        </span>
                                                    @endif

                                                    @php
                                                        $primaryPhoto = $professional->photos->where('is_primary', true)->first();
                                                        $profileImage = $primaryPhoto ? $primaryPhoto->photo_url : ($professional->profile_image ?? asset('paper/img/default-avatar.png'));
                                                    @endphp
                                                    <img src="{{ $profileImage }}"
                                                         alt="{{ $professional->name }}"
                                                         class="rounded-circle mb-3"
                                                         style="width: 80px; height: 80px; object-fit: cover;">

                                                    <h5 class="card-title mb-1">{{ $professional->name }}</h5>

                                                    <div class="mb-2">
                                                        <small class="text-primary font-weight-bold">
                                                            {{ ucwords(str_replace(['_', 'coach'], [' ', ' Coach'], $professional->primary_profession ?? 'Professional')) }}
                                                            @if(isset($professional->profession_count) && $professional->profession_count > 1)
                                                                +{{ $professional->profession_count - 1 }}
                                                            @endif
                                                        </small>
                                                    </div>

                                                    @if($professional->region)
                                                        <div class="mb-2">
                                                            <small class="text-muted">
                                                                <i class="fa fa-map-marker"></i> {{ ucwords(str_replace('_', ' ', $professional->region)) }}
                                                            </small>
                                                        </div>
                                                    @endif

                                                    @if($professional->spar_amount && $professional->spar_amount > 0)
                                                        <div class="mb-2">
                                                            <span class="badge badge-success">
                                                                <i class="fa fa-money"></i> KSH {{ number_format($professional->spar_amount, 0) }}
                                                            </span>
                                                        </div>
                                                    @endif

                                                    <div class="mt-3">
                                                        <a href="{{ route('fighter.show', $professional->id) }}"
                                                           class="btn btn-outline-primary btn-sm">
                                                            <i class="fa fa-eye"></i> View Profile
                                                        </a>
                                                        <a href="{{ route('spar-request.create', $professional->id) }}"
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
                                                <h5 class="text-muted">No professionals found matching your criteria</h5>
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
        $('#professionalsForm').submit();
    });

    // Auto-submit on filter change (optional)
    // $('select').change(function() {
    //     $('#professionalsForm').submit();
    // });
});
</script>
@endpush

<style>
.professional-card {
    transition: transform 0.2s;
    border: 1px solid #e3e3e3;
}

.professional-card:hover {
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
