@extends('layouts.app', [
    'class' => '',
    'elementActive' => 'profile'
])

@section('content')
    <div class="content">
        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif
        @if (session('password_status'))
            <div class="alert alert-success" role="alert">
                {{ session('password_status') }}
            </div>
        @endif
        <div class="row">
            <div class="col-md-4">
                <div class="card card-user">
                    <div class="image">
                        <img src="{{ asset('paper/img/logo.png') }}" alt="...">
                    </div>
                    <div class="card-body">
                        <div class="author">
                            <a href="#">
                                <img class="avatar border-gray" src="{{ asset('paper/img/logo.png') }}" alt="...">

                                <h5 class="title">{{ __(auth()->user()->name)}}</h5>
                            </a>
                            <p class="description">
                            @ {{ __(auth()->user()->name)}}
                            </p>
                        </div>
                        <div class="mt-3">
                            <h6>User Information</h6>
                            <ul class="list-group text-left">
                                <li class="list-group-item"><strong>Name:</strong> {{ $user->name }}</li>
                                <li class="list-group-item"><strong>Email:</strong> {{ $user->email }}</li>
                            </ul>
                        </div>
                    </div>
                  
                </div>
              
            </div>
            <div class="col-md-8 text-center">
                <form class="col-md-12" action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="card">
                        <div class="card-header">
                            <h5 class="title">{{ __('Edit Profile') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <label class="col-md-3 col-form-label">{{ __('Name') }}</label>
                                <div class="col-md-9">
                                    <div class="form-group">
                                        <input type="text" name="name" class="form-control" placeholder="Name" value="{{ auth()->user()->name }}" required>
                                    </div>
                                    @if ($errors->has('name'))
                                        <span class="invalid-feedback" style="display: block;" role="alert">
                                            <strong>{{ $errors->first('name') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-md-3 col-form-label">{{ __('Email') }}</label>
                                <div class="col-md-9">
                                    <div class="form-group">
                                        <input type="email" name="email" class="form-control" placeholder="Email" value="{{ auth()->user()->email }}" required>
                                    </div>
                                    @if ($errors->has('email'))
                                        <span class="invalid-feedback" style="display: block;" role="alert">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="card-footer ">
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <button type="submit" class="btn btn-info btn-round">{{ __('Save Changes') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <form class="col-md-12" action="{{ route('profile.password') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card">
                        <div class="card-header">
                            <h5 class="title">{{ __('Change Password') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <label class="col-md-3 col-form-label">{{ __('Old Password') }}</label>
                                <div class="col-md-9">
                                    <div class="form-group">
                                        <input type="password" name="old_password" class="form-control" placeholder="Old password" required>
                                    </div>
                                    @if ($errors->has('old_password'))
                                        <span class="invalid-feedback" style="display: block;" role="alert">
                                            <strong>{{ $errors->first('old_password') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-md-3 col-form-label">{{ __('New Password') }}</label>
                                <div class="col-md-9">
                                    <div class="form-group">
                                        <input type="password" name="password" class="form-control" placeholder="Password" required>
                                    </div>
                                    @if ($errors->has('password'))
                                        <span class="invalid-feedback" style="display: block;" role="alert">
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-md-3 col-form-label">{{ __('Password Confirmation') }}</label>
                                <div class="col-md-9">
                                    <div class="form-group">
                                        <input type="password" name="password_confirmation" class="form-control" placeholder="Password Confirmation" required>
                                    </div>
                                    @if ($errors->has('password_confirmation'))
                                        <span class="invalid-feedback" style="display: block;" role="alert">
                                            <strong>{{ $errors->first('password_confirmation') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="card-footer ">
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <button type="submit" class="btn btn-info btn-round">{{ __('Save Changes') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                @if(auth()->user()->fighter)
                    @php $fighter = auth()->user()->fighter; @endphp
                    <form class="col-md-12" action="{{ route('fighter.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="card">
                            <div class="card-header">
                                <h5 class="title">{{ ucfirst($fighter->category) }} Profile</h5>
                            </div>
                            <div class="card-body">

                                @if($fighter->category === 'fighters')
                                    <!-- Fighter Profile Fields -->
                                    <div class="row">
                                        <label class="col-md-3 col-form-label">{{ __('Discipline') }}</label>
                                        <div class="col-md-9">
                                            <div class="form-group">
                                                <select name="discipline" class="form-control">
                                                    <option value="">Select Discipline</option>
                                                    <option value="boxing" {{ $fighter->discipline == 'boxing' ? 'selected' : '' }}>Boxing</option>
                                                    <option value="mma" {{ $fighter->discipline == 'mma' ? 'selected' : '' }}>MMA</option>
                                                    <option value="taekwondo" {{ $fighter->discipline == 'taekwondo' ? 'selected' : '' }}>Taekwondo</option>
                                                    <option value="karate" {{ $fighter->discipline == 'karate' ? 'selected' : '' }}>Karate</option>
                                                    <option value="wrestling" {{ $fighter->discipline == 'wrestling' ? 'selected' : '' }}>Wrestling</option>
                                                    <option value="jiu_jitsu" {{ $fighter->discipline == 'jiu_jitsu' ? 'selected' : '' }}>Jiu jitsu</option>
                                                    <option value="kick_boxing" {{ $fighter->discipline == 'kick_boxing' ? 'selected' : '' }}>Kick Boxing</option>
                                                    <option value="thai_boxing" {{ $fighter->discipline == 'thai_boxing' ? 'selected' : '' }}>Thai Boxing</option>
                                                    <option value="judo" {{ $fighter->discipline == 'judo' ? 'selected' : '' }}>Judo</option>
                                                    <option value="kung_fu" {{ $fighter->discipline == 'kung_fu' ? 'selected' : '' }}>Kung Fu</option>
                                                    <option value="tai_chi" {{ $fighter->discipline == 'tai_chi' ? 'selected' : '' }}>Tai Chi</option>
                                                    <option value="wing_chun" {{ $fighter->discipline == 'wing_chun' ? 'selected' : '' }}>Wing Chun</option>
                                                    <option value="krav_maga" {{ $fighter->discipline == 'krav_maga' ? 'selected' : '' }}>Krav Maga</option>
                                                    <option value="aikido" {{ $fighter->discipline == 'aikido' ? 'selected' : '' }}>Aikido</option>
                                                    <option value="choi_kwang_do" {{ $fighter->discipline == 'choi_kwang_do' ? 'selected' : '' }}>Choi kwang do</option>
                                                    <option value="capoeira" {{ $fighter->discipline == 'capoeira' ? 'selected' : '' }}>Capoeira</option>
                                                    <option value="ninjutsu" {{ $fighter->discipline == 'ninjutsu' ? 'selected' : '' }}>Ninjutsu</option>
                                                    <option value="kendo" {{ $fighter->discipline == 'kendo' ? 'selected' : '' }}>Kendo</option>
                                                    <option value="kobudo" {{ $fighter->discipline == 'kobudo' ? 'selected' : '' }}>Kobudo</option>
                                                    <option value="hapkido" {{ $fighter->discipline == 'hapkido' ? 'selected' : '' }}>Hapkido</option>
                                                    <option value="tang_soo_do" {{ $fighter->discipline == 'tang_soo_do' ? 'selected' : '' }}>Tang soo do</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-3 col-form-label">{{ __('Experience') }}</label>
                                        <div class="col-md-9">
                                            <div class="form-group">
                                                <select name="experience" class="form-control">
                                                    <option value="beginner" {{ $fighter->experience == 'beginner' ? 'selected' : '' }}>Beginner</option>
                                                    <option value="intermediate" {{ $fighter->experience == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                                                    <option value="advanced" {{ $fighter->experience == 'advanced' ? 'selected' : '' }}>Advanced</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-3 col-form-label">{{ __('Level') }}</label>
                                        <div class="col-md-9">
                                            <div class="form-group">
                                                <select name="level" class="form-control">
                                                    <option value="amateur" {{ $fighter->level == 'amateur' ? 'selected' : '' }}>Amateur</option>
                                                    <option value="semi_pro" {{ $fighter->level == 'semi_pro' ? 'selected' : '' }}>Semi Pro</option>
                                                    <option value="professional" {{ $fighter->level == 'professional' ? 'selected' : '' }}>Professional</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-3 col-form-label">{{ __('Height (cm)') }}</label>
                                        <div class="col-md-9">
                                            <div class="form-group">
                                                <input type="number" name="height" class="form-control" value="{{ $fighter->height }}" min="100" max="250">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-3 col-form-label">{{ __('Weight (kg)') }}</label>
                                        <div class="col-md-9">
                                            <div class="form-group">
                                                <input type="number" name="weight" class="form-control" value="{{ $fighter->weight }}" min="30" max="200">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-3 col-form-label">{{ __('Age') }}</label>
                                        <div class="col-md-9">
                                            <div class="form-group">
                                                <input type="number" name="age" class="form-control" value="{{ $fighter->age }}" min="16" max="100">
                                            </div>
                                        </div>
                                    </div>

                                @elseif($fighter->category === 'professionals')
                                    <!-- Professional Profile Fields -->
                                    <div class="row">
                                        <label class="col-md-3 col-form-label">{{ __('Primary Profession') }}</label>
                                        <div class="col-md-9">
                                            <div class="form-group">
                                                <select name="primary_profession" class="form-control">
                                                    <option value="strength_conditioning" {{ $fighter->primary_profession == 'strength_conditioning' ? 'selected' : '' }}>Strength & Conditioning</option>
                                                    <option value="nutritionist" {{ $fighter->primary_profession == 'nutritionist' ? 'selected' : '' }}>Nutritionist</option>
                                                    <option value="sports_psychologist" {{ $fighter->primary_profession == 'sports_psychologist' ? 'selected' : '' }}>Sports Psychologist</option>
                                                    <option value="physiotherapist" {{ $fighter->primary_profession == 'physiotherapist' ? 'selected' : '' }}>Physiotherapist</option>
                                                    <option value="sports_medical_doctor" {{ $fighter->primary_profession == 'sports_medical_doctor' ? 'selected' : '' }}>Sports Medical Doctor</option>
                                                    <option value="boxing_coach" {{ $fighter->primary_profession == 'boxing_coach' ? 'selected' : '' }}>Boxing Coach</option>
                                                    <option value="wrestling_coach" {{ $fighter->wrestling_coach == 'wrestling_coach' ? 'selected' : '' }}>Wrestling Coach</option>
                                                    <option value="striking_coach" {{ $fighter->primary_profession == 'striking_coach' ? 'selected' : '' }}>Striking Coach</option>
                                                    <option value="bjj_coach" {{ $fighter->primary_profession == 'bjj_coach' ? 'selected' : '' }}>BJJ Coach</option>
                                                    <option value="muay_thai_coach" {{ $fighter->primary_profession == 'muay_thai_coach' ? 'selected' : '' }}>Muay Thai Coach</option>
                                                    <option value="coaching" {{ $fighter->primary_profession == 'coaching' ? 'selected' : '' }}>Coaching</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-3 col-form-label">{{ __('Badge Level') }}</label>
                                        <div class="col-md-9">
                                            <div class="form-group">
                                                <select name="badge_level" class="form-control">
                                                    <option value="">No Badge</option>
                                                    <option value="bronze" {{ $fighter->badge_level == 'bronze' ? 'selected' : '' }}>Bronze</option>
                                                    <option value="silver" {{ $fighter->badge_level == 'silver' ? 'selected' : '' }}>Silver</option>
                                                    <option value="gold" {{ $fighter->badge_level == 'gold' ? 'selected' : '' }}>Gold</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-3 col-form-label">{{ __('Specialties Count') }}</label>
                                        <div class="col-md-9">
                                            <div class="form-group">
                                                <input type="number" name="profession_count" class="form-control" value="{{ $fighter->profession_count }}" min="1" max="10">
                                            </div>
                                        </div>
                                    </div>

                                @elseif($fighter->category === 'gyms')
                                    <!-- Gym Profile Fields -->
                                    <div class="row">
                                        <label class="col-md-3 col-form-label">{{ __('Gym Type') }}</label>
                                        <div class="col-md-9">
                                            <div class="form-group">
                                                <select name="gym_type" class="form-control">
                                                    <option value="boxing" {{ $fighter->gym_type == 'boxing' ? 'selected' : '' }}>Boxing</option>
                                                    <option value="mma" {{ $fighter->gym_type == 'mma' ? 'selected' : '' }}>MMA</option>
                                                    <option value="taekwondo" {{ $fighter->gym_type == 'taekwondo' ? 'selected' : '' }}>Taekwondo</option>
                                                    <option value="karate" {{ $fighter->gym_type == 'karate' ? 'selected' : '' }}>Karate</option>
                                                    <option value="wrestling" {{ $fighter->gym_type == 'wrestling' ? 'selected' : '' }}>Wrestling</option>
                                                    <option value="jiu_jitsu" {{ $fighter->gym_type == 'jiu_jitsu' ? 'selected' : '' }}>Jiu jitsu</option>
                                                    <option value="kick_boxing" {{ $fighter->gym_type == 'kick_boxing' ? 'selected' : '' }}>Kick Boxing</option>
                                                    <option value="thai_boxing" {{ $fighter->gym_type == 'thai_boxing' ? 'selected' : '' }}>Thai Boxing</option>
                                                    <option value="judo" {{ $fighter->gym_type == 'judo' ? 'selected' : '' }}>Judo</option>
                                                    <option value="kung_fu" {{ $fighter->gym_type == 'kung_fu' ? 'selected' : '' }}>Kung Fu</option>
                                                    <option value="tai_chi" {{ $fighter->gym_type == 'tai_chi' ? 'selected' : '' }}>Tai Chi</option>
                                                    <option value="wing_chun" {{ $fighter->gym_type == 'wing_chun' ? 'selected' : '' }}>Wing Chun</option>
                                                    <option value="krav_maga" {{ $fighter->gym_type == 'krav_maga' ? 'selected' : '' }}>Krav Maga</option>
                                                    <option value="aikido" {{ $fighter->gym_type == 'aikido' ? 'selected' : '' }}>Aikido</option>
                                                    <option value="choi_kwang_do" {{ $fighter->gym_type == 'choi_kwang_do' ? 'selected' : '' }}>Choi kwang do</option>
                                                    <option value="capoeira" {{ $fighter->gym_type == 'capoeira' ? 'selected' : '' }}>Capoeira</option>
                                                    <option value="ninjutsu" {{ $fighter->gym_type == 'ninjutsu' ? 'selected' : '' }}>Ninjutsu</option>
                                                    <option value="kendo" {{ $fighter->gym_type == 'kendo' ? 'selected' : '' }}>Kendo</option>
                                                    <option value="kobudo" {{ $fighter->gym_type == 'kobudo' ? 'selected' : '' }}>Kobudo</option>
                                                    <option value="hapkido" {{ $fighter->gym_type == 'hapkido' ? 'selected' : '' }}>Hapkido</option>
                                                    <option value="tang_soo_do" {{ $fighter->gym_type == 'tang_soo_do' ? 'selected' : '' }}>Tang soo do</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-3 col-form-label">{{ __('Bio/Description') }}</label>
                                        <div class="col-md-9">
                                            <div class="form-group">
                                                <textarea name="bio" class="form-control" rows="3">{{ $fighter->bio }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-3 col-form-label">{{ __('Contact Info') }}</label>
                                        <div class="col-md-9">
                                            <div class="form-group">
                                                <input type="text" name="contact_info" class="form-control" value="{{ $fighter->contact_info }}" placeholder="Phone, website, etc.">
                                            </div>
                                        </div>
                                    </div>
                                @endif

                            </div>
                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-md-12 text-center">
                                        <button type="submit" class="btn btn-info btn-round">{{ __('Update Profile') }}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
@endsection
