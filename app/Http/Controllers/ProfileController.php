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
     * Show the general profile page with password change functionality
     *
     * @return \Illuminate\View\View
     */
    public function general()
    {
        return view('profile.general');
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
            return redirect()->route('profile.general')->withErrors(['error' => 'Fighter profile not found.']);
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

        $hasFileinfo = extension_loaded('fileinfo');
        $allowedImageExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        // Base validation rules (avoid 'image'/'mimes' when fileinfo extension is missing)
        $rules = [
            'name' => 'required|string|max:255',
            'country_id' => 'nullable|exists:countries,id',
            'city_id' => 'nullable|exists:cities,id',
            'profile_image' => $hasFileinfo
                ? 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
                : 'nullable|file|max:2048',
            'fighter_photos' => 'nullable|array|max:3',
            'fighter_photos.*' => $hasFileinfo
                ? 'image|mimes:jpeg,png,jpg,gif|max:2048'
                : 'file|max:2048',
        ];

        // Add type-specific validation
        switch ($fighter->category) {
            case 'fighters':
                $rules = array_merge($rules, [
                    'discipline' => 'required|integer|exists:disciplines,id',
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
                    'discipline' => 'nullable|integer|exists:disciplines,id',
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

        // When fileinfo is missing, validate image extensions manually
        if (!$hasFileinfo && $request->hasFile('profile_image')) {
            $ext = strtolower($request->file('profile_image')->getClientOriginalExtension());
            if (!in_array($ext, $allowedImageExtensions)) {
                return back()->withErrors(['profile_image' => 'Invalid file type. Allowed: JPG, PNG, GIF.']);
            }
        }
        if (!$hasFileinfo) {
            $photos = $request->file('fighter_photos');
            if (is_array($photos)) {
                foreach ($photos as $i => $file) {
                    if ($file && $file->isValid()) {
                        $ext = strtolower($file->getClientOriginalExtension());
                        if (!in_array($ext, $allowedImageExtensions)) {
                            return back()->withErrors(['fighter_photos' => 'Invalid file type in photo(s). Allowed: JPG, PNG, GIF.']);
                        }
                    }
                }
            }
        }

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            $image = $request->file('profile_image');
            $imageName = time() . '_' . $fighter->id . '.' . $image->getClientOriginalExtension();
            if ($hasFileinfo) {
                $path = $image->storeAs('fighters', $imageName, 'public');
                $validatedData['profile_image'] = '/storage/' . $path;
            } else {
                $dir = storage_path('app/public/fighters');
                if (!is_dir($dir)) {
                    @mkdir($dir, 0755, true);
                }
                $dest = $dir . '/' . $imageName;
                if (move_uploaded_file($image->getRealPath(), $dest)) {
                    $validatedData['profile_image'] = '/storage/fighters/' . $imageName;
                }
            }
        }

        // Handle multiple fighter photos upload (array input: name="fighter_photos[]")
        $uploadedPhotos = $request->file('fighter_photos');
        $uploadedPhotos = is_array($uploadedPhotos) ? array_values(array_filter($uploadedPhotos, function ($file) {
            return $file && $file->isValid();
        })) : [];
        if (!empty($uploadedPhotos)) {
            $currentPhotoCount = $fighter->photos()->count();
            $maxPhotos = 3 - $currentPhotoCount;
            $toProcess = array_slice($uploadedPhotos, 0, $maxPhotos);

            $photosDir = null;
            if (!$hasFileinfo) {
                $photosDir = storage_path('app/public/fighters/photos');
                if (!is_dir($photosDir)) {
                    @mkdir($photosDir, 0755, true);
                }
            }

            foreach ($toProcess as $index => $photo) {
                $photoName = time() . '_' . $fighter->id . '_photo_' . ($currentPhotoCount + $index + 1) . '.' . $photo->getClientOriginalExtension();
                if ($hasFileinfo) {
                    $path = $photo->storeAs('fighters/photos', $photoName, 'public');
                } else {
                    $dest = $photosDir . '/' . $photoName;
                    if (!move_uploaded_file($photo->getRealPath(), $dest)) {
                        continue;
                    }
                    $path = 'fighters/photos/' . $photoName;
                }
                FighterPhoto::create([
                    'fighter_id' => $fighter->id,
                    'photo_path' => $path,
                    'photo_name' => $photo->getClientOriginalName(),
                    'is_primary' => ($currentPhotoCount + $index) === 0 && !$fighter->photos()->exists(),
                    'sort_order' => $currentPhotoCount + $index,
                ]);
            }
        }

        unset($validatedData['fighter_photos']);
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
