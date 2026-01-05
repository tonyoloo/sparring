<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Fighter;
use App\Http\Controllers\Controller;
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
    protected $redirectTo = '/login';

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
        $rules = [
            'registration_type' => ['required', 'in:fighter,professional,gym'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'agree_terms_and_conditions' => ['required'],
        ];

        // Add conditional validation based on registration type
        if (isset($data['registration_type'])) {
            switch ($data['registration_type']) {
                case 'fighter':
                    $rules = array_merge($rules, [
                        'discipline' => ['required', 'string'],
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
                        'discipline' => ['nullable', 'string'],
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
                'region' => ['nullable', 'string'],
            ]);
        }

        return Validator::make($data, $rules);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // Create fighter profile based on registration type
        $this->createFighterProfile($user, $data);

        return $user;
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
        $fighterData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'category' => $data['registration_type'],
            'region' => $data['region'] ?? null,
            'is_active' => true,
        ];

        // Add type-specific data
        switch ($data['registration_type']) {
            case 'fighter':
                $fighterData = array_merge($fighterData, [
                    'gender' => $data['gender'] ?? null,
                    'discipline' => $data['discipline'] ?? null,
                    'stance' => $data['stance'] ?? null,
                    'experience' => $data['experience'] ?? null,
                    'level' => $data['level'] ?? null,
                    'height' => $data['height'] ?? null,
                    'weight' => $data['weight'] ?? null,
                    'age' => $data['age'] ?? null,
                ]);
                break;

            case 'professional':
                $fighterData = array_merge($fighterData, [
                    'primary_profession' => $data['primary_profession'] ?? null,
                    'discipline' => $data['discipline'] ?? null,
                    'badge_level' => $data['badge_level'] ?? null,
                    'profession_count' => $data['profession_count'] ?? 1,
                ]);
                break;

            case 'gym':
                $fighterData = array_merge($fighterData, [
                    'gym_type' => $data['gym_type'] ?? null,
                    'bio' => $data['bio'] ?? null,
                    'contact_info' => $data['contact_info'] ?? null,
                ]);
                break;
        }

        Fighter::create($fighterData);
    }
}
