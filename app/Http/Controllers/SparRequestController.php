<?php

namespace App\Http\Controllers;

use App\Models\Fighter;
use App\Models\SparRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;


class SparRequestController extends Controller
{
    /**
     * Display the spar requests management page.
     */
    public function index()
    {
        $user = Auth::user();
        $fighter = $user->fighter;

        if (!$fighter) {
            return redirect()->route('fighter.edit')->with('error', 'You need a fighter profile to manage spar requests.');
        }

        // Get sent requests
        $sentRequests = SparRequest::with(['receiver'])
            ->sentBy($fighter->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'sent_page');

        // Get received requests
        $receivedRequests = SparRequest::with(['sender'])
            ->receivedBy($fighter->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'received_page');

        return view('pages.spar-requests', compact('sentRequests', 'receivedRequests'));
    }

    /**
     * Show the form for creating a new spar request.
     */
    public function create($fighterId)
    {
        // Debug logging
        Log::info('SparRequestController@create called', [
            'fighterId' => $fighterId,
            'user_id' => auth()->id(),
            'has_fighter' => auth()->user()->fighter ? 'yes' : 'no'
        ]);

        // Temporary debug - remove this after testing
        // dd('SparRequestController@create reached', $fighterId, auth()->user());

        $user = Auth::user();
        $currentFighter = $user->fighter;

        if (!$currentFighter) {
            Log::warning('User tried to create spar request without fighter profile', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);
            return redirect()->route('fighter.edit')->with('error', 'You need a fighter profile to send spar requests.');
        }

        try {
            $targetFighter = Fighter::with(['photos', 'country', 'city'])->findOrFail($fighterId);
            Log::info('Target fighter found', ['target_fighter_id' => $targetFighter->id, 'target_fighter_name' => $targetFighter->name]);
        } catch (\Exception $e) {
            Log::error('Target fighter not found', ['fighterId' => $fighterId, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'The fighter you are trying to contact does not exist.');
        }

        // Check if user is trying to request themselves
        if ($currentFighter->id === $targetFighter->id) {
            Log::info('User tried to send spar request to themselves', ['user_id' => $user->id, 'fighter_id' => $fighterId]);
            return redirect()->back()->with('error', 'You cannot send a spar request to yourself.');
        }

        // Check if there's already a pending request between these users
        $existingRequest = SparRequest::where(function ($query) use ($currentFighter, $targetFighter) {
            $query->where('sender_id', $currentFighter->id)
                  ->where('receiver_id', $targetFighter->id);
        })->orWhere(function ($query) use ($currentFighter, $targetFighter) {
            $query->where('sender_id', $targetFighter->id)
                  ->where('receiver_id', $currentFighter->id);
        })->where('status', 'pending')->first();

        Log::info('Checking for existing requests', [
            'current_fighter_id' => $currentFighter->id,
            'target_fighter_id' => $targetFighter->id,
            'existing_request_found' => $existingRequest ? 'YES' : 'NO',
            'existing_request_id' => $existingRequest ? $existingRequest->id : null
        ]);

        if ($existingRequest) {
            Log::info('Redirecting with existing request error');
            return redirect()->back()->with('error', 'There is already a pending spar request between you and this fighter.');
        }

        Log::info('Spar request create view being returned', [
            'target_fighter' => $targetFighter->name,
            'target_fighter_id' => $targetFighter->id,
            'view_path' => 'pages.create-spar-request',
            'timestamp' => now()->toISOString()
        ]);

        $view = view('pages.create-spar-request', compact('targetFighter'));
        Log::info('View rendered successfully', ['view_exists' => $view ? 'yes' : 'no']);

        return $view;
    }

    /**
     * Store a newly created spar request.
     */
    public function store(Request $request, $fighterId)
    {
        $user = Auth::user();
        $currentFighter = $user->fighter;

        if (!$currentFighter) {
            return redirect()->route('fighter.edit')->with('error', 'You need a fighter profile to send spar requests.');
        }

        $targetFighter = Fighter::with(['photos', 'country', 'city'])->findOrFail($fighterId);

        // Validate request
        $validator = Validator::make($request->all(), [
            'message' => 'nullable|string|max:500',
            'requested_date' => 'nullable|date|after:today',
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check for existing pending request
        $existingRequest = SparRequest::where(function ($query) use ($currentFighter, $targetFighter) {
            $query->where('sender_id', $currentFighter->id)
                  ->where('receiver_id', $targetFighter->id);
        })->orWhere(function ($query) use ($currentFighter, $targetFighter) {
            $query->where('sender_id', $targetFighter->id)
                  ->where('receiver_id', $currentFighter->id);
        })->where('status', 'pending')->first();

        if ($existingRequest) {
            return redirect()->back()->with('error', 'There is already a pending spar request between you and this fighter.');
        }

        // Create the spar request
        $sparRequest = SparRequest::create([
            'sender_id' => $currentFighter->id,
            'receiver_id' => $targetFighter->id,
            'message' => $request->message,
            'requested_date' => $request->requested_date,
            'location' => $request->location,
            'notes' => $request->notes,
            'status' => 'pending',
        ]);

        // Send notification to the receiver if user exists
        if ($targetFighter->user) {
            $targetFighter->user->notify(new \App\Notifications\SparRequestSent($sparRequest, $currentFighter));
        }

        return redirect()->route('spar-requests.index')->with('success', 'Spar request sent successfully!');
    }

    /**
     * Accept a spar request.
     */
    public function accept($id)
    {
        $user = Auth::user();
        $fighter = $user->fighter;

        if (!$fighter) {
            return redirect()->route('fighter.edit')->with('error', 'You need a fighter profile to manage spar requests.');
        }

        $sparRequest = SparRequest::findOrFail($id);

        // Check if user can respond to this request
        if (!$sparRequest->canBeRespondedToBy($fighter->id)) {
            return redirect()->back()->with('error', 'You cannot respond to this spar request.');
        }

        $sparRequest->update([
            'status' => 'accepted',
            'responded_at' => now(),
        ]);

        // Send notification to the sender if user exists
        if ($sparRequest->sender->user) {
            $sparRequest->sender->user->notify(new \App\Notifications\SparRequestAccepted($sparRequest, $fighter));
        }

        return redirect()->back()->with('success', 'Spar request accepted!');
    }

    /**
     * Reject a spar request.
     */
    public function reject($id)
    {
        $user = Auth::user();
        $fighter = $user->fighter;

        if (!$fighter) {
            return redirect()->route('fighter.edit')->with('error', 'You need a fighter profile to manage spar requests.');
        }

        $sparRequest = SparRequest::findOrFail($id);

        // Check if user can respond to this request
        if (!$sparRequest->canBeRespondedToBy($fighter->id)) {
            return redirect()->back()->with('error', 'You cannot respond to this spar request.');
        }

        $sparRequest->update([
            'status' => 'rejected',
            'responded_at' => now(),
        ]);

        // Send notification to the sender if user exists
        if ($sparRequest->sender->user) {
            $sparRequest->sender->user->notify(new \App\Notifications\SparRequestRejected($sparRequest, $fighter));
        }

        return redirect()->back()->with('success', 'Spar request rejected.');
    }

    /**
     * Cancel a spar request.
     */
    public function cancel($id)
    {
        $user = Auth::user();
        $fighter = $user->fighter;

        if (!$fighter) {
            return redirect()->route('fighter.edit')->with('error', 'You need a fighter profile to manage spar requests.');
        }

        $sparRequest = SparRequest::findOrFail($id);

        // Check if user can cancel this request
        if (!$sparRequest->canBeCancelledBy($fighter->id)) {
            return redirect()->back()->with('error', 'You cannot cancel this spar request.');
        }

        $sparRequest->update([
            'status' => 'cancelled',
            'responded_at' => now(),
        ]);

        // Send notification to the receiver if user exists
        if ($sparRequest->receiver->user) {
            $sparRequest->receiver->user->notify(new \App\Notifications\SparRequestCancelled($sparRequest, $fighter));
        }

        return redirect()->back()->with('success', 'Spar request cancelled.');
    }

    /**
     * Mark a spar request as completed.
     */
    public function complete($id)
    {
        $user = Auth::user();
        $fighter = $user->fighter;

        if (!$fighter) {
            return redirect()->route('fighter.edit')->with('error', 'You need a fighter profile to manage spar requests.');
        }

        $sparRequest = SparRequest::findOrFail($id);

        // Check if user can complete this request
        if (!$sparRequest->canBeCompletedBy($fighter->id)) {
            return redirect()->back()->with('error', 'You cannot complete this spar request.');
        }

        $sparRequest->update([
            'status' => 'completed',
        ]);

        return redirect()->back()->with('success', 'Spar session marked as completed!');
    }
}
