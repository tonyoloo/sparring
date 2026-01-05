@extends('layouts.app', [
    'class' => '',
    'elementActive' => 'spar_requests'
])

@section('content')
    <div class="content">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                @if (session('error'))
                    <div class="alert alert-danger" role="alert">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h4 class="card-title">Request to Spar</h4>
                                <p class="card-category">Send a sparring request to {{ $targetFighter->name }}</p>
                            </div>
                            <div class="col-md-4 text-right">
                                <a href="{{ route('directory') }}" class="btn btn-secondary btn-sm">
                                    <i class="fa fa-arrow-left"></i> Back to Directory
                                </a>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('spar-request.store', $targetFighter->id) }}" method="POST">
                        @csrf

                        <!-- Fighter Info -->
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <div class="d-flex align-items-center p-3 bg-light rounded">
                                        @php
                                            $primaryPhoto = $targetFighter->photos->where('is_primary', true)->first();
                                            $profileImage = $primaryPhoto ? $primaryPhoto->photo_url : ($targetFighter->profile_image ?? asset('paper/img/default-avatar.png'));
                                        @endphp
                                        <img src="{{ $profileImage }}"
                                             alt="{{ $targetFighter->name }}"
                                             class="rounded-circle mr-3"
                                             style="width: 60px; height: 60px; object-fit: cover;">
                                        <div class="flex-grow-1">
                                            <h5 class="mb-1">{{ $targetFighter->name }}</h5>
                                            <p class="text-muted mb-1">
                                                {{ ucfirst(str_replace('_', ' ', $targetFighter->discipline ?? 'Unknown')) }} •
                                                {{ ucfirst($targetFighter->experience ?? 'Unknown') }} •
                                                {{ ucfirst($targetFighter->level ?? 'Unknown') }}
                                            </p>
                                            @if($targetFighter->region)
                                                <small class="text-muted d-block">
                                                    <i class="fa fa-map-marker"></i> {{ ucwords(str_replace('_', ' ', $targetFighter->region)) }}
                                                </small>
                                            @endif
                                            @if($targetFighter->spar_amount && $targetFighter->spar_amount > 0)
                                                <div class="mt-2">
                                                    <span class="badge badge-success">
                                                        <i class="fa fa-money"></i> Sparring Rate: KSH {{ number_format($targetFighter->spar_amount, 0) }} per session
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Message -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="message">Message <span class="text-muted">(Optional)</span></label>
                                        <textarea name="message" id="message" class="form-control" rows="3"
                                                  placeholder="Introduce yourself and explain why you'd like to spar with {{ $targetFighter->name }}..."
                                                  maxlength="500">{{ old('message') }}</textarea>
                                        <small class="form-text text-muted">Max 500 characters</small>
                                        @error('message')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Requested Date -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="requested_date">Preferred Date & Time <span class="text-muted">(Optional)</span></label>
                                        <input type="datetime-local" name="requested_date" id="requested_date"
                                               class="form-control" value="{{ old('requested_date') }}"
                                               min="{{ now()->addDay()->format('Y-m-d\TH:i') }}">
                                        <small class="form-text text-muted">When would you like to spar?</small>
                                        @error('requested_date')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="location">Location <span class="text-muted">(Optional)</span></label>
                                        <input type="text" name="location" id="location" class="form-control"
                                               placeholder="Gym name, address, or area" value="{{ old('location') }}" maxlength="255">
                                        <small class="form-text text-muted">Where would you like to spar?</small>
                                        @error('location')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Additional Notes -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="notes">Additional Notes <span class="text-muted">(Optional)</span></label>
                                        <textarea name="notes" id="notes" class="form-control" rows="2"
                                                  placeholder="Any special requirements, training goals, or other details..."
                                                  maxlength="1000">{{ old('notes') }}</textarea>
                                        <small class="form-text text-muted">Max 1000 characters</small>
                                        @error('notes')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer">
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fa fa-paper-plane"></i> Send Spar Request
                                    </button>
                                    <a href="{{ route('directory') }}" class="btn btn-secondary btn-lg ml-2">
                                        <i class="fa fa-times"></i> Cancel
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Set minimum date to tomorrow
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    const minDateTime = tomorrow.toISOString().slice(0, 16);
    $('#requested_date').attr('min', minDateTime);
});
</script>
@endpush
