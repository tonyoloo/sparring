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
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h4 class="card-title">{{ $fighter->name }}'s Profile</h4>
                                <p class="card-category">{{ ucfirst($fighter->category) }} • {{ $fighter->location }}</p>
                            </div>
                            <div class="col-md-4 text-right">
                                <a href="{{ route('directory') }}" class="btn btn-secondary btn-sm">
                                    <i class="fa fa-arrow-left"></i> Back to Directory
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <!-- Profile Images and Basic Info -->
                            <div class="col-md-4 text-center">
                                <!-- Main Profile Image -->
                                <div class="profile-image-container mb-4">
                                    @php
                                        $primaryPhoto = $fighter->photos->where('is_primary', true)->first();
                                        $mainImage = $primaryPhoto ? $primaryPhoto->photo_url : ($fighter->profile_image ?? asset('paper/img/default-avatar.png'));
                                    @endphp
                                    <img src="{{ $mainImage }}"
                                         alt="{{ $fighter->name }}"
                                         class="rounded-circle profile-image"
                                         style="width: 150px; height: 150px; object-fit: cover; border: 4px solid #ddd;">
                                </div>

                                <!-- Additional Photos -->
                                @if($fighter->photos->count() > 1)
                                    <div class="additional-photos mb-4">
                                        <div class="row">
                                            @foreach($fighter->photos->where('is_primary', false)->take(3) as $photo)
                                                <div class="col-4">
                                                    <img src="{{ $photo->photo_url }}"
                                                         alt="Photo {{ $loop->iteration + 1 }}"
                                                         class="rounded img-thumbnail"
                                                         style="width: 60px; height: 60px; object-fit: cover; cursor: pointer;"
                                                         onclick="showPhotoModal('{{ $photo->photo_url }}', '{{ $photo->photo_name ?? 'Photo ' . ($loop->iteration + 1) }}')">
                                                </div>
                                            @endforeach
                                        </div>
                                        @if($fighter->photos->count() > 4)
                                            <small class="text-muted mt-1 d-block">+{{ $fighter->photos->count() - 4 }} more photos</small>
                                        @endif
                                    </div>
                                @endif

                                <h5 class="mb-1">{{ $fighter->name }}</h5>

                                @if($fighter->category === 'fighters')
                                    @php
                                        $disciplineName = 'Unknown';
                                        if ($fighter->relationLoaded('discipline')) {
                                            $discipline = $fighter->getRelation('discipline');
                                            if ($discipline instanceof \App\Models\Discipline) {
                                                $disciplineName = $discipline->name;
                                            }
                                        } elseif ($fighter->discipline_id) {
                                            $discipline = $fighter->discipline()->first();
                                            if ($discipline) {
                                                $disciplineName = $discipline->name;
                                            }
                                        }
                                    @endphp
                                    <p class="text-muted mb-2">{{ $disciplineName }} Fighter</p>
                                    <span class="badge badge-{{ $fighter->level === 'professional' ? 'warning' : ($fighter->level === 'semi_pro' ? 'info' : 'secondary') }}">
                                        {{ ucfirst(str_replace('_', ' ', $fighter->level ?? 'Unknown')) }}
                                    </span>
                                @elseif($fighter->category === 'professionals')
                                    <p class="text-muted mb-2">{{ ucwords(str_replace('_', ' ', $fighter->primary_profession ?? 'Professional')) }}</p>
                                    @if($fighter->badge_level)
                                        <span class="badge badge-warning">
                                            <i class="fa fa-medal"></i> {{ ucfirst($fighter->badge_level) }}
                                        </span>
                                    @endif
                                @else
                                    <p class="text-muted mb-2">{{ ucfirst(str_replace('_', ' ', $fighter->gym_type ?? 'Unknown')) }} Gym</p>
                                @endif

                                @if($fighter->spar_amount && $fighter->spar_amount > 0)
                                    <div class="mt-3 p-3 bg-success text-white rounded">
                                        <small class="d-block opacity-75">Sparring Rate</small>
                                        <strong class="h4">KSH {{ number_format($fighter->spar_amount, 0) }}</strong>
                                        <small class="d-block opacity-75">per session</small>
                                    </div>
                                @endif
                            </div>

                            <!-- Detailed Information -->
                            <div class="col-md-8">
                                <!-- Fighting Stats (for fighters) -->
                                @if($fighter->category === 'fighters')
                                    <h5 class="mb-3">Fighting Profile</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="stats-card p-3 mb-3 bg-light rounded">
                                                <div class="row">
                                                    <div class="col-6 text-center">
                                                        <div class="stat-value">{{ $fighter->height ?? '-' }}cm</div>
                                                        <div class="stat-label">Height</div>
                                                    </div>
                                                    <div class="col-6 text-center">
                                                        <div class="stat-value">{{ $fighter->weight ?? '-' }}kg</div>
                                                        <div class="stat-label">Weight</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="stats-card p-3 mb-3 bg-light rounded">
                                                <div class="row">
                                                    <div class="col-6 text-center">
                                                        <div class="stat-value">{{ $fighter->age ?? '-' }}</div>
                                                        <div class="stat-label">Age</div>
                                                    </div>
                                                    <div class="col-6 text-center">
                                                        <div class="stat-value">{{ ucfirst($fighter->stance ?? '-') }}</div>
                                                        <div class="stat-label">Stance</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <strong>Experience:</strong> {{ ucfirst($fighter->experience ?? 'Not specified') }}
                                        </div>
                                        <div class="col-md-4">
                                            <strong>Level:</strong> {{ ucfirst(str_replace('_', ' ', $fighter->level ?? 'Not specified')) }}
                                        </div>
                                        <div class="col-md-4">
                                            <strong>Gender:</strong> {{ ucfirst($fighter->gender ?? 'Not specified') }}
                                        </div>
                                    </div>
                                @endif

                                <!-- Professional Info (for professionals) -->
                                @if($fighter->category === 'professionals')
                                    <h5 class="mb-3">Professional Profile</h5>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <strong>Specialty:</strong> {{ ucwords(str_replace('_', ' ', $fighter->primary_profession ?? 'Not specified')) }}
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Experience:</strong> {{ $fighter->profession_count ?? 1 }} specialties
                                        </div>
                                    </div>

                                    @php
                                        $discipline = null;
                                        if ($fighter->relationLoaded('discipline')) {
                                            $discipline = $fighter->getRelation('discipline');
                                            if (!($discipline instanceof \App\Models\Discipline)) {
                                                $discipline = null;
                                            }
                                        } elseif ($fighter->discipline_id) {
                                            $discipline = $fighter->discipline()->first();
                                        }
                                    @endphp
                                    @if($discipline)
                                        <div class="mb-3">
                                            <strong>Sport Focus:</strong> {{ $discipline->name }}
                                        </div>
                                    @endif
                                @endif

                                <!-- Gym Info (for gyms) -->
                                @if($fighter->category === 'gyms')
                                    <h5 class="mb-3">Gym Information</h5>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <strong>Gym Type:</strong> {{ ucfirst(str_replace('_', ' ', $fighter->gym_type ?? 'Not specified')) }}
                                        </div>
                                    </div>

                                    @if($fighter->contact_info)
                                        <div class="mb-3">
                                            <strong>Contact:</strong> {{ $fighter->contact_info }}
                                        </div>
                                    @endif
                                @endif

                                <!-- Bio/About Section -->
                                @if($fighter->bio)
                                    <h5 class="mb-3">About</h5>
                                    <div class="bio-section p-3 bg-light rounded">
                                        <p class="mb-0">{{ $fighter->bio }}</p>
                                    </div>
                                @endif

                                <!-- Action Buttons -->
                                <div class="mt-4">
                                    @if(auth()->check() && auth()->user()->fighter && auth()->user()->fighter->id !== $fighter->id)
                                        <a href="{{ route('spar-request.create', $fighter->id) }}"
                                           class="btn btn-success btn-lg mr-2">
                                            <i class="fa fa-hand-rock"></i> Request to Spar
                                        </a>
                                    @endif

                                    {{-- Contact button - hidden for now, will be restored later --}}
                                    @if(false && $fighter->email)
                                        <a href="mailto:{{ $fighter->email }}" 
                                           class="btn btn-outline-primary btn-lg" 
                                           title="Contact Fighter"
                                           onclick="window.location.href='mailto:{{ $fighter->email }}'; return false;"
                                           style="display: none !important; visibility: hidden !important; opacity: 0 !important; position: absolute !important; left: -9999px !important;">
                                            <i class="fa fa-envelope"></i> Contact
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Photo Modal -->
    <div class="modal fade" id="photoModal" tabindex="-1" role="dialog" aria-labelledby="photoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="photoModalLabel">Photo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalPhoto" src="" alt="" class="img-fluid rounded">
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
function showPhotoModal(photoUrl, photoName) {
    $('#modalPhoto').attr('src', photoUrl);
    $('#photoModalLabel').text(photoName);
    $('#photoModal').modal('show');
}

$(document).ready(function() {
    // Any additional JavaScript can go here
});
</script>
@endpush

<style>
.profile-image {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.stats-card {
    border: 1px solid #e3e3e3;
}

.stat-value {
    font-size: 1.5rem;
    font-weight: bold;
    color: #007bff;
}

.stat-label {
    font-size: 0.8rem;
    color: #666;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.bio-section {
    line-height: 1.6;
}
</style>
