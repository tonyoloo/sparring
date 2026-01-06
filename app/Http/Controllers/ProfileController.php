<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use App\Http\Requests\PasswordRequest;
use App\Models\Fighter;
use App\Models\FighterPhoto;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * Show the form for editing the profile.
     *
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        $user = auth()->user();
        $studentdata = \App\Models\StudentData::where('user_id', $user->id)->first();
        return view('profile.edit', compact('studentdata'));
    }

    /**
     * Show the form for editing the fighter profile.
     *
     * @return \Illuminate\View\View
     */
    public function editFighter()
    {
        $user = auth()->user();
        $fighter = $user->fighter;

        if (!$fighter) {
            return redirect()->route('profile.edit')->withErrors(['error' => 'Fighter profile not found.']);
        }

        // Determine current country and city for pre-selection
        $currentCountryId = $fighter->country_id;
        $currentCityId = $fighter->city_id;

        return view('pages.fighter-profile', compact('fighter', 'currentCountryId', 'currentCityId'));
    }

    /**
     * Update the profile
     *
     * @param  \App\Http\Requests\ProfileRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ProfileRequest $request)
    {
        auth()->user()->update($request->all());

        return back()->withStatus(__('Profile successfully updated.'));
    }

    /**
     * Change the password
     *
     * @param  \App\Http\Requests\PasswordRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function password(PasswordRequest $request)
    {
        auth()->user()->update(['password' => Hash::make($request->get('password'))]);

        return back()->withPasswordStatus(__('Password successfully updated.'));
    }

    /**
     * Update the fighter profile
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateFighter(Request $request)
    {
        $user = auth()->user();
        $fighter = $user->fighter;

        if (!$fighter) {
            return back()->withErrors(['error' => 'Fighter profile not found.']);
        }

        // Base validation rules
        $rules = [
            'name' => 'required|string|max:255',
            'country_id' => 'nullable|exists:countries,id',
            'city_id' => 'nullable|exists:cities,id',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB max
            'fighter_photos' => 'nullable|array|max:3',
            'fighter_photos.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        // Add type-specific validation
        switch ($fighter->category) {
            case 'fighters':
                $rules = array_merge($rules, [
                    'discipline' => 'required|string',
                    'experience' => 'required|in:beginner,intermediate,advanced',
                    'level' => 'required|in:amateur,semi_pro,professional',
                    'height' => 'nullable|integer|min:100|max:250',
                    'weight' => 'nullable|integer|min:30|max:200',
                    'age' => 'nullable|integer|min:16|max:100',
                    'spar_amount' => 'nullable|numeric|min:0',
                    'bio' => 'nullable|string|max:1000',
                ]);
                break;

            case 'professionals':
                $rules = array_merge($rules, [
                    'primary_profession' => 'required|string',
                    'badge_level' => 'nullable|in:bronze,silver,gold',
                    'profession_count' => 'nullable|integer|min:1|max:10',
                    'discipline' => 'nullable|string',
                    'bio' => 'nullable|string|max:1000',
                ]);
                break;

            case 'gyms':
                $rules = array_merge($rules, [
                    'gym_type' => 'required|string',
                    'bio' => 'required|string|max:1000',
                    'contact_info' => 'nullable|string|max:500',
                ]);
                break;
        }

        $validatedData = $request->validate($rules);

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            $image = $request->file('profile_image');
            $imageName = time() . '_' . $fighter->id . '.' . $image->getClientOriginalExtension();

            // Store in public disk under fighters directory
            $path = $image->storeAs('fighters', $imageName, 'public');
            $validatedData['profile_image'] = '/storage/' . $path;
        }

        // Handle multiple fighter photos upload
        if ($request->hasFile('fighter_photos')) {
            $currentPhotoCount = $fighter->photos()->count();
            $uploadedPhotos = $request->file('fighter_photos');
            $maxPhotos = 3 - $currentPhotoCount;

            // Only process if we haven't exceeded the limit
            if (count($uploadedPhotos) <= $maxPhotos) {
                foreach ($uploadedPhotos as $index => $photo) {
                    $photoName = time() . '_' . $fighter->id . '_photo_' . ($currentPhotoCount + $index + 1) . '.' . $photo->getClientOriginalExtension();

                    // Store in public disk under fighters/photos directory
                    $path = $photo->storeAs('fighters/photos', $photoName, 'public');

                    // Create the photo record
                    FighterPhoto::create([
                        'fighter_id' => $fighter->id,
                        'photo_path' => $path,
                        'photo_name' => $photo->getClientOriginalName(),
                        'is_primary' => ($currentPhotoCount + $index) === 0 && !$fighter->photos()->exists(), // First photo is primary if no photos exist
                        'sort_order' => $currentPhotoCount + $index,
                    ]);

                    $currentPhotoCount++;
                }
            }
        }

        $fighter->update($validatedData);

        return back()->withStatus(__('Profile successfully updated.'));
    }

    /**
     * Make a photo the primary photo for the fighter.
     */
    public function makePhotoPrimary($photoId)
    {
        $user = auth()->user();
        $fighter = $user->fighter;

        if (!$fighter) {
            return redirect()->route('fighter.edit')->with('error', 'You need a fighter profile to manage photos.');
        }

        $photo = FighterPhoto::where('id', $photoId)->where('fighter_id', $fighter->id)->first();

        if (!$photo) {
            return redirect()->back()->with('error', 'Photo not found.');
        }

        $photo->makePrimary();

        return redirect()->back()->with('success', 'Primary photo updated successfully.');
    }

    /**
     * Delete a fighter photo.
     */
    public function deletePhoto($photoId)
    {
        $user = auth()->user();
        $fighter = $user->fighter;

        if (!$fighter) {
            return redirect()->route('fighter.edit')->with('error', 'You need a fighter profile to manage photos.');
        }

        $photo = FighterPhoto::where('id', $photoId)->where('fighter_id', $fighter->id)->first();

        if (!$photo) {
            return redirect()->back()->with('error', 'Photo not found.');
        }

        // If this was the primary photo, make another photo primary if available
        if ($photo->is_primary) {
            $nextPhoto = FighterPhoto::where('fighter_id', $fighter->id)
                                    ->where('id', '!=', $photoId)
                                    ->orderBy('sort_order')
                                    ->first();

            if ($nextPhoto) {
                $nextPhoto->makePrimary();
            }
        }

        $photo->delete();

        return redirect()->back()->with('success', 'Photo deleted successfully.');
    }
}
