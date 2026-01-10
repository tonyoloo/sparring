<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Fighter;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/email/verify';

    /**
     * Handle test registration without email verification
     */
    public function registerTest(Request $request)
    {
        \Log::info('Test registration started');

        $request->validate([
            'registration_type' => 'required|in:fighter,professional,gym',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'agree_terms_and_conditions' => 'required',
        ]);

        \Log::info('Test registration validation passed');

        try {
            $user = $this->create($request->all());

            \Log::info('Test user created, logging in', ['user_id' => $user->id]);

            // Log the user in directly (bypass email verification)
            auth()->login($user);

            return redirect('/')->with('success', 'Test registration successful! You are now logged in.');
        } catch (\Exception $e) {
            \Log::error('Test registration failed', ['error' => $e->getMessage()]);
            return back()->with('error', 'Registration failed: ' . $e->getMessage());
        }
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        \Log::info('Registration validation started', [
            'registration_type' => $data['registration_type'] ?? 'not_set',
            'name' => $data['name'] ?? 'not_set',
            'email' => $data['email'] ?? 'not_set',
            'has_password' => isset($data['password']),
            'has_country_id' => isset($data['country_id']),
            'has_city_id' => isset($data['city_id']),
            'agree_terms' => $data['agree_terms_and_conditions'] ?? false,
            'has_discipline' => isset($data['discipline']),
            'discipline_value' => $data['discipline'] ?? 'NULL',
            'has_experience' => isset($data['experience']),
            'experience_value' => $data['experience'] ?? 'NULL',
            'has_level' => isset($data['level']),
            'level_value' => $data['level'] ?? 'NULL',
            'all_request_keys' => array_keys($data),
            'full_request_data' => $data
        ]);

        // Pre-validation check for fighter registration
        if (($data['registration_type'] ?? '') === 'fighter') {
            if (!isset($data['discipline']) || empty($data['discipline'])) {
                \Log::warning('Fighter registration submitted without discipline field', [
                    'all_fields' => array_keys($data)
                ]);
            }
        }

        $rules = [
            'registration_type' => ['required', 'in:fighter,professional,gym'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'agree_terms_and_conditions' => ['required'],
        ];

        // Add conditional validation based on registration type
        if (isset($data['registration_type'])) {
            \Log::info('Adding conditional validation for type: ' . $data['registration_type']);
            switch ($data['registration_type']) {
                case 'fighter':
                    \Log::info('Adding fighter validation rules');
                    $rules = array_merge($rules, [
                        'fighter_discipline' => ['required', 'integer', 'exists:disciplines,id'],
                        'discipline' => ['nullable'], // Allow for backward compatibility
                        'experience' => ['required', 'in:beginner,intermediate,advanced'],
                        'level' => ['required', 'in:amateur,semi_pro,professional'],
                        'gender' => ['nullable', 'in:male,female'],
                        'stance' => ['nullable', 'in:orthodox,southpaw'],
                        'height' => ['nullable', 'integer', 'min:100', 'max:250'],
                        'weight' => ['nullable', 'integer', 'min:30', 'max:200'],
                        'age' => ['nullable', 'integer', 'min:16', 'max:100'],
                    ]);
                    break;

                case 'professional':
                    $rules = array_merge($rules, [
                        'primary_profession' => ['required', 'string'],
                        'profession_count' => ['nullable', 'integer', 'min:1', 'max:10'],
                        'badge_level' => ['nullable', 'in:bronze,silver,gold'],
                        'professional_discipline' => ['nullable', 'integer', 'exists:disciplines,id'],
                        'discipline' => ['nullable'], // Allow for backward compatibility
                    ]);
                    break;

                case 'gym':
                    $rules = array_merge($rules, [
                        'gym_type' => ['required', 'string'],
                        'bio' => ['nullable', 'string', 'max:1000'],
                        'contact_info' => ['nullable', 'string', 'max:500'],
                    ]);
                    break;
            }

            // Common fields for all types
            $rules = array_merge($rules, [
                'country_id' => ['nullable', 'exists:countries,id'],
                'city_id' => ['nullable', 'exists:cities,id'],
            ]);
        }

        $validator = Validator::make($data, $rules);

        // Add custom error messages
        $validator->setAttributeNames([
            'fighter_discipline' => 'discipline',
            'professional_discipline' => 'discipline',
        ]);

        if ($validator->fails()) {
            \Log::warning('Registration validation failed', [
                'errors' => $validator->errors()->toArray(),
                'input_data' => array_keys($data),
                'registration_type' => $data['registration_type'] ?? 'not_set',
                'fighter_discipline' => $data['fighter_discipline'] ?? 'not_set',
                'professional_discipline' => $data['professional_discipline'] ?? 'not_set',
                'discipline_value' => $data['discipline'] ?? 'not_set'
            ]);

            // If discipline is required but missing, and type is fighter, log this specifically
            if (($data['registration_type'] ?? '') === 'fighter' && empty($data['fighter_discipline']) && empty($data['discipline'])) {
                \Log::error('Fighter registration missing discipline field', [
                    'all_data' => $data
                ]);
            }
        } else {
            \Log::info('Registration validation passed');
        }

        return $validator;
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        \Log::info('Creating user account', [
            'name' => $data['name'],
            'email' => $data['email'],
            'registration_type' => $data['registration_type']
        ]);

        try {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            \Log::info('User created successfully', [
                'user_id' => $user->id,
                'user_email' => $user->email
            ]);

            // Create fighter profile based on registration type
            $this->createFighterProfile($user, $data);

            return $user;
        } catch (\Exception $e) {
            \Log::error('Failed to create user', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * Create fighter profile based on registration type
     *
     * @param User $user
     * @param array $data
     * @return void
     */
    protected function createFighterProfile(User $user, array $data)
    {
        // Map registration_type to category enum values (fighters, professionals, gyms)
        $categoryMap = [
            'fighter' => 'fighters',
            'professional' => 'professionals',
            'gym' => 'gyms'
        ];
        $category = $categoryMap[$data['registration_type']] ?? 'fighters';

        \Log::info('Creating fighter profile', [
            'user_id' => $user->id,
            'registration_type' => $data['registration_type'],
            'category' => $category,
            'country_id' => $data['country_id'] ?? null,
            'city_id' => $data['city_id'] ?? null
        ]);

        $fighterData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'category' => $category,
            'country_id' => !empty($data['country_id']) ? (int)$data['country_id'] : null,
            'city_id' => !empty($data['city_id']) ? (int)$data['city_id'] : null,
            'is_active' => true,
        ];

        // Add type-specific data
        switch ($data['registration_type']) {
            case 'fighter':
                \Log::info('Creating fighter profile with fighter-specific data');
                // Map fighter_discipline to discipline_id (support both field names for backward compatibility)
                $disciplineId = $data['fighter_discipline'] ?? $data['discipline'] ?? null;
                $fighterData = array_merge($fighterData, [
                    'gender' => !empty($data['gender']) && in_array($data['gender'], ['male', 'female']) ? $data['gender'] : null,
                    'discipline_id' => $disciplineId ? (int)$disciplineId : null,
                    'stance' => !empty($data['stance']) && in_array($data['stance'], ['orthodox', 'southpaw']) ? $data['stance'] : null,
                    'experience' => !empty($data['experience']) && in_array($data['experience'], ['beginner', 'intermediate', 'advanced']) ? $data['experience'] : null,
                    'level' => !empty($data['level']) && in_array($data['level'], ['amateur', 'semi_pro', 'professional']) ? $data['level'] : null,
                    'height' => !empty($data['height']) ? (int)$data['height'] : null,
                    'weight' => !empty($data['weight']) ? (int)$data['weight'] : null,
                    'age' => !empty($data['age']) ? (int)$data['age'] : null,
                ]);
                \Log::info('Fighter discipline_id set', ['discipline_id' => $disciplineId]);
                break;

            case 'professional':
                // Map professional_discipline to discipline_id (support both field names for backward compatibility)
                $disciplineId = $data['professional_discipline'] ?? $data['discipline'] ?? null;
                $professionCount = !empty($data['profession_count']) ? (int)$data['profession_count'] : 1;
                $fighterData = array_merge($fighterData, [
                    'primary_profession' => $data['primary_profession'] ?? null,
                    'discipline_id' => $disciplineId ? (int)$disciplineId : null,
                    'badge_level' => !empty($data['badge_level']) && in_array($data['badge_level'], ['bronze', 'silver', 'gold']) ? $data['badge_level'] : null,
                    'profession_count' => $professionCount,
                ]);
                \Log::info('Professional discipline_id set', ['discipline_id' => $disciplineId]);
                break;

            case 'gym':
                $fighterData = array_merge($fighterData, [
                    'gym_type' => $data['gym_type'] ?? null,
                    'bio' => $data['bio'] ?? null,
                    'contact_info' => $data['contact_info'] ?? null,
                ]);
                break;
        }

        try {
            $fighter = Fighter::create($fighterData);
            \Log::info('Fighter profile created successfully', [
                'fighter_id' => $fighter->id,
                'user_id' => $user->id,
                'category' => $fighter->category
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to create fighter profile', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'fighter_data' => $fighterData
            ]);
            throw $e;
        }
    }
}
