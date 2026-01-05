@extends('layouts.app', [
    'class' => '',
    'elementActive' => 'spar_requests'
])

@section('content')
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                @if (session('success'))
                    <div class="alert alert-success" role="alert">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger" role="alert">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Spar Requests</h4>
                        <p class="card-category">Manage your sparring requests and sessions</p>
                    </div>

                    <div class="card-body">
                        <!-- Navigation Tabs -->
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#received" role="tab">
                                    <i class="fa fa-inbox"></i> Received ({{ $receivedRequests->total() }})
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#sent" role="tab">
                                    <i class="fa fa-paper-plane"></i> Sent ({{ $sentRequests->total() }})
                                </a>
                            </li>
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content">
                            <!-- Received Requests Tab -->
                            <div class="tab-pane active" id="received" role="tabpanel">
                                <div class="mt-4">
                                    <h5>Requests You've Received</h5>

                                    @forelse($receivedRequests as $request)
                                        <div class="card mb-3 border-left-primary">
                                            <div class="card-body">
                                                <div class="row align-items-center">
                                                    <div class="col-md-2">
                                                        <img src="{{ $request->sender->profile_image ?? asset('paper/img/default-avatar.png') }}"
                                                             alt="{{ $request->sender->name }}"
                                                             class="rounded-circle"
                                                             style="width: 60px; height: 60px; object-fit: cover;">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <h6 class="mb-1">{{ $request->sender->name }}</h6>
                                                        <p class="text-muted small mb-1">
                                                            {{ ucfirst(str_replace('_', ' ', $request->sender->discipline ?? 'Unknown')) }} •
                                                            {{ ucfirst($request->sender->experience ?? 'Unknown') }}
                                                        </p>
                                                        <p class="mb-1">{{ $request->message ?? 'No message provided' }}</p>

                                                        @if($request->requested_date)
                                                            <small class="text-info">
                                                                <i class="fa fa-calendar"></i>
                                                                Requested: {{ $request->requested_date->format('M j, Y g:i A') }}
                                                            </small>
                                                        @endif

                                                        @if($request->location)
                                                            <br><small class="text-muted">
                                                                <i class="fa fa-map-marker"></i> {{ $request->location }}
                                                            </small>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-4 text-right">
                                                        <div class="mb-2">
                                                            <span class="badge badge-{{ $request->status === 'pending' ? 'warning' : ($request->status === 'accepted' ? 'success' : 'danger') }}">
                                                                {{ ucfirst($request->status) }}
                                                            </span>
                                                        </div>

                                                        @if($request->status === 'pending')
                                                            <form action="{{ route('spar-request.accept', $request->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('POST')
                                                                <button type="submit" class="btn btn-success btn-sm">
                                                                    <i class="fa fa-check"></i> Accept
                                                                </button>
                                                            </form>
                                                            <form action="{{ route('spar-request.reject', $request->id) }}" method="POST" class="d-inline ml-1">
                                                                @csrf
                                                                @method('POST')
                                                                <button type="submit" class="btn btn-danger btn-sm">
                                                                    <i class="fa fa-times"></i> Reject
                                                                </button>
                                                            </form>
                                                        @elseif($request->status === 'accepted')
                                                            <form action="{{ route('spar-request.complete', $request->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('POST')
                                                                <button type="submit" class="btn btn-primary btn-sm">
                                                                    <i class="fa fa-check-circle"></i> Mark Complete
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center py-5">
                                            <i class="fa fa-inbox fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">No spar requests received</h5>
                                            <p class="text-muted">When someone sends you a spar request, it will appear here.</p>
                                        </div>
                                    @endforelse

                                    {{ $receivedRequests->appends(['tab' => 'received'])->links() }}
                                </div>
                            </div>

                            <!-- Sent Requests Tab -->
                            <div class="tab-pane" id="sent" role="tabpanel">
                                <div class="mt-4">
                                    <h5>Requests You've Sent</h5>

                                    @forelse($sentRequests as $request)
                                        <div class="card mb-3 border-left-info">
                                            <div class="card-body">
                                                <div class="row align-items-center">
                                                    <div class="col-md-2">
                                                        <img src="{{ $request->receiver->profile_image ?? asset('paper/img/default-avatar.png') }}"
                                                             alt="{{ $request->receiver->name }}"
                                                             class="rounded-circle"
                                                             style="width: 60px; height: 60px; object-fit: cover;">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <h6 class="mb-1">{{ $request->receiver->name }}</h6>
                                                        <p class="text-muted small mb-1">
                                                            {{ ucfirst(str_replace('_', ' ', $request->receiver->discipline ?? 'Unknown')) }} •
                                                            {{ ucfirst($request->receiver->experience ?? 'Unknown') }}
                                                        </p>
                                                        <p class="mb-1">{{ $request->message ?? 'No message provided' }}</p>

                                                        @if($request->requested_date)
                                                            <small class="text-info">
                                                                <i class="fa fa-calendar"></i>
                                                                Requested: {{ $request->requested_date->format('M j, Y g:i A') }}
                                                            </small>
                                                        @endif

                                                        @if($request->location)
                                                            <br><small class="text-muted">
                                                                <i class="fa fa-map-marker"></i> {{ $request->location }}
                                                            </small>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-4 text-right">
                                                        <div class="mb-2">
                                                            <span class="badge badge-{{ $request->status === 'pending' ? 'warning' : ($request->status === 'accepted' ? 'success' : ($request->status === 'rejected' ? 'danger' : 'secondary')) }}">
                                                                {{ ucfirst($request->status) }}
                                                            </span>
                                                        </div>

                                                        @if(in_array($request->status, ['pending', 'accepted']))
                                                            <form action="{{ route('spar-request.cancel', $request->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('POST')
                                                                <button type="submit" class="btn btn-outline-secondary btn-sm"
                                                                        onclick="return confirm('Are you sure you want to cancel this spar request?')">
                                                                    <i class="fa fa-ban"></i> Cancel
                                                                </button>
                                                            </form>
                                                        @elseif($request->status === 'accepted')
                                                            <form action="{{ route('spar-request.complete', $request->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('POST')
                                                                <button type="submit" class="btn btn-primary btn-sm">
                                                                    <i class="fa fa-check-circle"></i> Mark Complete
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center py-5">
                                            <i class="fa fa-paper-plane fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">No spar requests sent</h5>
                                            <p class="text-muted">When you send spar requests to other fighters, they will appear here.</p>
                                        </div>
                                    @endforelse

                                    {{ $sentRequests->appends(['tab' => 'sent'])->links() }}
                                </div>
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
    // Handle tab switching based on URL parameter
    const urlParams = new URLSearchParams(window.location.search);
    const activeTab = urlParams.get('tab');

    if (activeTab === 'sent') {
        $('.nav-tabs a[href="#sent"]').tab('show');
    }
});
</script>
@endpush
