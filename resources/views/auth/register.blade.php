@extends('layouts.app', [
'class' => 'register-page',
'backgroundImagePath' => 'img/bg/jan-sendereks.jpg'
])

@section('content')
<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-lg-5 col-md-5 ml-auto">
                <div class="apprentice-spaces-content" style="max-width: 980px; width: 100%; max-height: 70vh; overflow-y: auto; padding: 20px 10px; background: transparent; color: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                    <h2>Welcome to Ngumi Network</h2>
                    <h4>Bridging the Gap Between Fighters</h4>
                    <p>Serious fighters know that progress comes from regular sparring with a variety of partners. Yet, finding new and suitable sparring partners outside your own gym has always been a challenge. Ngumi Network bridges this gap by creating a central space where fighters can connect, communicate, and arrange sparring sessions efficiently. No more relying on chance encounters or outdated contacts—your next sparring partner is just a search away.</p>
                    <hr />
                    <h5>Who We Are</h5>
                    <p>Ngumi Network is a community-driven platform created by people who understand combat sports. We recognize the challenges faced by amateur and professional fighters, trainers, women fighters, and young athletes. Our goal is to support growth, safety, and opportunity across all levels of combat sports.</p> 
                    <hr />
                   <h5>What We Do</h5>
                    <ul>
    <li data-start="1211" data-end="1314">
        <p data-start="1213" data-end="1314">Find sparring partners based on age, gender, weight, height, stance, experience level, and location</p>
    </li>
    <li data-start="1315" data-end="1379">
        <p data-start="1317" data-end="1379">Connect directly with fighters or trainers outside their gym</p>
    </li>
    <li data-start="1380" data-end="1428">
        <p data-start="1382" data-end="1428">Send and receive sparring requests with ease</p>
    </li>
    <li data-start="1429" data-end="1518">
        <p data-start="1431" data-end="1518">Discover gyms, coaches, physiotherapists, and other performance-support professionals</p>
    </li>
    <li data-start="1519" data-end="1582">
        <p data-start="1521" data-end="1582">Manage training connections anytime, from mobile or desktop</p>
    </li>
</ul>
                        
                   
                    
                   
                    <hr />
                    <h5>Our Vision</h5>
                    <p>To become the leading combat sports network that connects fighters, trainers, and support professionals worldwide, creating equal access to opportunities and elevating performance across all disciplines.</p> <hr />
                    <h5>Our Mission</h5>
                    <p>To empower fighters and trainers by removing barriers to connection, improving access to quality sparring and support services, and helping combat athletes reach their full potential—both inside and outside the gym.</p><hr />
                    <h5>Why Choose Ngumi Network?</h5>
                    <ul>
    <li data-start="2187" data-end="2227">
        <p data-start="2189" data-end="2227">Built specifically for combat sports</p>
    </li>
    <li data-start="2228" data-end="2289">
        <p data-start="2230" data-end="2289">Supports fighters, trainers, and entire performance teams</p>
    </li>
    <li data-start="2290" data-end="2342">
        <p data-start="2292" data-end="2342">Inclusive of all levels, genders, and age groups</p>
    </li>
    <li data-start="2343" data-end="2396">
        <p data-start="2345" data-end="2396">Easy-to-use platform accessible anytime, anywhere</p>
    </li>
    <li data-start="2397" data-end="2447">
        <p data-start="2399" data-end="2447">Focused on real connections, not just profiles</p>
    </li>
</ul>
                    <hr />
                    <h5>Join the Movement</h5>
                    <p>Whether you’re a student seeking experience, a company ready to host interns, or an institution looking for a reliable placement partner — Ngumi Network is here for you.</p>
                    <ul>
                        <li>🌐 Visit us: <a href="https://www.apprenticespaces.co.ke" target="_blank">www.apprenticespaces.co.ke</a></li>
                        <li>📧 Email: info@apprenticespaces.co.ke</li>
                        <li>📞 Call: +254 XXX XXX XXX</li>
                        <li>🔗 Follow us: [LinkedIn] [Instagram] [Facebook] [Twitter]</li>
                    </ul>
                    <p>Let’s prepare the next generation of professionals — together.</p>
                    <hr />
                    
                    <ul>
    <li>
        <p data-start="2543" data-end="2827">Combat sports are built on discipline, community, and growth. Ngumi Network brings these values together in one place. Whether you&rsquo;re a fighter looking for your next sparring partner, a trainer managing athletes, or a professional supporting performance, there&rsquo;s a place for you here.</p>
        <p data-start="2829" data-end="2945" data-is-last-node="" data-is-only-node="">Join Ngumi Network today and be part of a growing movement that&rsquo;s redefining how fighters connect and progress. 🥊💪</p>
    </li>
</ul>
                     </div>
            </div>
            <div class="col-lg-4 col-md-6 mr-auto">
                <div class="card card-signup text-center">
                    <div class="card-header ">
                        <h4 class="card-title">{{ __('Register') }}</h4>
                        <div class="social">
                           
<img src="{{ asset('paper') }}/img/logo.png" style="width: 50%; height: auto;">

                            <p class="card-description">{{ __('or be classical') }}</p>
                        </div>
                    </div>
                    <div class="card-body ">
                        <form class="form" method="POST" action="{{ route('register') }}">
                            @csrf

                            <!-- Registration Type Selection -->
                            <div class="input-group{{ $errors->has('registration_type') ? ' has-danger' : '' }}">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="nc-icon nc-tag-content"></i>
                                    </span>
                                </div>
                                <select name="registration_type" id="registration_type" class="form-control" required>
                                    <option value="">Select Registration Type</option>
                                    <option value="fighter" {{ old('registration_type') == 'fighter' ? 'selected' : '' }}>Fighter</option>
                                    <option value="professional" {{ old('registration_type') == 'professional' ? 'selected' : '' }}>Coach/Professional</option>
                                    <option value="gym" {{ old('registration_type') == 'gym' ? 'selected' : '' }}>Gym</option>
                                </select>
                                @if ($errors->has('registration_type'))
                                <span class="invalid-feedback" style="display: block;" role="alert">
                                    <strong>{{ $errors->first('registration_type') }}</strong>
                                </span>
                                @endif
                            </div>

                            <div class="input-group{{ $errors->has('name') ? ' has-danger' : '' }}">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="nc-icon nc-single-02"></i>
                                    </span>
                                </div>
                                <input name="name" type="text" class="form-control" placeholder="Name" value="{{ old('name') }}" required autofocus>
                                @if ($errors->has('name'))
                                <span class="invalid-feedback" style="display: block;" role="alert">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="input-group{{ $errors->has('email') ? ' has-danger' : '' }}">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="nc-icon nc-email-85"></i>
                                    </span>
                                </div>
                                <input name="email" type="email" class="form-control" placeholder="Email" required value="{{ old('email') }}">
                                @if ($errors->has('email'))
                                <span class="invalid-feedback" style="display: block;" role="alert">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="input-group{{ $errors->has('password') ? ' has-danger' : '' }}">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="nc-icon nc-key-25"></i>
                                    </span>
                                </div>
                                <input name="password" type="password" class="form-control" placeholder="Password" required>
                                @if ($errors->has('password'))
                                <span class="invalid-feedback" style="display: block;" role="alert">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="nc-icon nc-key-25"></i>
                                    </span>
                                </div>
                                <input name="password_confirmation" type="password" class="form-control" placeholder="Password confirmation" required>
                                @if ($errors->has('password_confirmation'))
                                <span class="invalid-feedback" style="display: block;" role="alert">
                                    <strong>{{ $errors->first('password_confirmation') }}</strong>
                                </span>
                                @endif
                            </div>

                            <!-- Fighter Fields -->
                            <div id="fighter_fields" style="display: none;">
                                <h5 class="text-left mt-3 mb-3">Fighter Information</h5>

                                <div class="input-group{{ $errors->has('gender') ? ' has-danger' : '' }}">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="nc-icon nc-single-02"></i>
                                        </span>
                                    </div>
                                    <select name="gender" class="form-control">
                                        <option value="">Select Gender</option>
                                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                    </select>
                                    @if ($errors->has('gender'))
                                    <span class="invalid-feedback" style="display: block;" role="alert">
                                        <strong>{{ $errors->first('gender') }}</strong>
                                    </span>
                                    @endif
                                </div>

                                <div class="input-group{{ $errors->has('discipline') ? ' has-danger' : '' }}">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="nc-icon nc-tag-content"></i>
                                        </span>
                                    </div>
                                    <select name="discipline" class="form-control">
                                        <option value="">Select Primary Discipline</option>
                                        <option value="boxing" {{ old('discipline') == 'boxing' ? 'selected' : '' }}>Boxing</option>
                                        <option value="mma" {{ old('discipline') == 'mma' ? 'selected' : '' }}>MMA</option>
                                        <option value="taekwondo" {{ old('discipline') == 'taekwondo' ? 'selected' : '' }}>Taekwondo</option>
                                        <option value="karate" {{ old('discipline') == 'karate' ? 'selected' : '' }}>Karate</option>
                                        <option value="wrestling" {{ old('discipline') == 'wrestling' ? 'selected' : '' }}>Wrestling</option>
                                        <option value="jiu_jitsu" {{ old('discipline') == 'jiu_jitsu' ? 'selected' : '' }}>Jiu jitsu</option>
                                        <option value="kick_boxing" {{ old('discipline') == 'kick_boxing' ? 'selected' : '' }}>Kick Boxing</option>
                                        <option value="thai_boxing" {{ old('discipline') == 'thai_boxing' ? 'selected' : '' }}>Thai Boxing</option>
                                        <option value="judo" {{ old('discipline') == 'judo' ? 'selected' : '' }}>Judo</option>
                                        <option value="kung_fu" {{ old('discipline') == 'kung_fu' ? 'selected' : '' }}>Kung Fu</option>
                                        <option value="tai_chi" {{ old('discipline') == 'tai_chi' ? 'selected' : '' }}>Tai Chi</option>
                                        <option value="wing_chun" {{ old('discipline') == 'wing_chun' ? 'selected' : '' }}>Wing Chun</option>
                                        <option value="krav_maga" {{ old('discipline') == 'krav_maga' ? 'selected' : '' }}>Krav Maga</option>
                                        <option value="aikido" {{ old('discipline') == 'aikido' ? 'selected' : '' }}>Aikido</option>
                                        <option value="choi_kwang_do" {{ old('discipline') == 'choi_kwang_do' ? 'selected' : '' }}>Choi kwang do</option>
                                        <option value="capoeira" {{ old('discipline') == 'capoeira' ? 'selected' : '' }}>Capoeira</option>
                                        <option value="ninjutsu" {{ old('discipline') == 'ninjutsu' ? 'selected' : '' }}>Ninjutsu</option>
                                        <option value="kendo" {{ old('discipline') == 'kendo' ? 'selected' : '' }}>Kendo</option>
                                        <option value="kobudo" {{ old('discipline') == 'kobudo' ? 'selected' : '' }}>Kobudo</option>
                                        <option value="hapkido" {{ old('discipline') == 'hapkido' ? 'selected' : '' }}>Hapkido</option>
                                        <option value="tang_soo_do" {{ old('discipline') == 'tang_soo_do' ? 'selected' : '' }}>Tang soo do</option>
                                    </select>
                                    @if ($errors->has('discipline'))
                                    <span class="invalid-feedback" style="display: block;" role="alert">
                                        <strong>{{ $errors->first('discipline') }}</strong>
                                    </span>
                                    @endif
                                </div>

                                <div class="input-group{{ $errors->has('stance') ? ' has-danger' : '' }}">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="nc-icon nc-compass-05"></i>
                                        </span>
                                    </div>
                                    <select name="stance" class="form-control">
                                        <option value="">Select Stance (Optional)</option>
                                        <option value="orthodox" {{ old('stance') == 'orthodox' ? 'selected' : '' }}>Orthodox</option>
                                        <option value="southpaw" {{ old('stance') == 'southpaw' ? 'selected' : '' }}>Southpaw</option>
                                    </select>
                                    @if ($errors->has('stance'))
                                    <span class="invalid-feedback" style="display: block;" role="alert">
                                        <strong>{{ $errors->first('stance') }}</strong>
                                    </span>
                                    @endif
                                </div>

                                <div class="input-group{{ $errors->has('experience') ? ' has-danger' : '' }}">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="nc-icon nc-chart-bar-32"></i>
                                        </span>
                                    </div>
                                    <select name="experience" class="form-control">
                                        <option value="">Select Experience Level</option>
                                        <option value="beginner" {{ old('experience') == 'beginner' ? 'selected' : '' }}>Beginner</option>
                                        <option value="intermediate" {{ old('experience') == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                                        <option value="advanced" {{ old('experience') == 'advanced' ? 'selected' : '' }}>Advanced</option>
                                    </select>
                                    @if ($errors->has('experience'))
                                    <span class="invalid-feedback" style="display: block;" role="alert">
                                        <strong>{{ $errors->first('experience') }}</strong>
                                    </span>
                                    @endif
                                </div>

                                <div class="input-group{{ $errors->has('level') ? ' has-danger' : '' }}">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="nc-icon nc-trophy"></i>
                                        </span>
                                    </div>
                                    <select name="level" class="form-control">
                                        <option value="">Select Competition Level</option>
                                        <option value="amateur" {{ old('level') == 'amateur' ? 'selected' : '' }}>Amateur</option>
                                        <option value="semi_pro" {{ old('level') == 'semi_pro' ? 'selected' : '' }}>Semi Pro</option>
                                        <option value="professional" {{ old('level') == 'professional' ? 'selected' : '' }}>Professional</option>
                                    </select>
                                    @if ($errors->has('level'))
                                    <span class="invalid-feedback" style="display: block;" role="alert">
                                        <strong>{{ $errors->first('level') }}</strong>
                                    </span>
                                    @endif
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="input-group{{ $errors->has('height') ? ' has-danger' : '' }}">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="nc-icon nc-user-run"></i>
                                                </span>
                                            </div>
                                            <input name="height" type="number" class="form-control" placeholder="Height (cm)" value="{{ old('height') }}" min="100" max="250">
                                            @if ($errors->has('height'))
                                            <span class="invalid-feedback" style="display: block;" role="alert">
                                                <strong>{{ $errors->first('height') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group{{ $errors->has('weight') ? ' has-danger' : '' }}">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="nc-icon nc-chart-pie-36"></i>
                                                </span>
                                            </div>
                                            <input name="weight" type="number" class="form-control" placeholder="Weight (kg)" value="{{ old('weight') }}" min="30" max="200">
                                            @if ($errors->has('weight'))
                                            <span class="invalid-feedback" style="display: block;" role="alert">
                                                <strong>{{ $errors->first('weight') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="input-group{{ $errors->has('age') ? ' has-danger' : '' }}">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="nc-icon nc-calendar-60"></i>
                                        </span>
                                    </div>
                                    <input name="age" type="number" class="form-control" placeholder="Age" value="{{ old('age') }}" min="16" max="100">
                                    @if ($errors->has('age'))
                                    <span class="invalid-feedback" style="display: block;" role="alert">
                                        <strong>{{ $errors->first('age') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Professional Fields -->
                            <div id="professional_fields" style="display: none;">
                                <h5 class="text-left mt-3 mb-3">Professional Information</h5>

                                <div class="input-group{{ $errors->has('primary_profession') ? ' has-danger' : '' }}">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="nc-icon nc-briefcase-24"></i>
                                        </span>
                                    </div>
                                    <select name="primary_profession" class="form-control">
                                        <option value="">Select Primary Profession</option>
                                        <option value="strength_conditioning" {{ old('primary_profession') == 'strength_conditioning' ? 'selected' : '' }}>Strength & Conditioning</option>
                                        <option value="nutritionist" {{ old('primary_profession') == 'nutritionist' ? 'selected' : '' }}>Nutritionist</option>
                                        <option value="sports_psychologist" {{ old('primary_profession') == 'sports_psychologist' ? 'selected' : '' }}>Sports Psychologist</option>
                                        <option value="physiotherapist" {{ old('primary_profession') == 'physiotherapist' ? 'selected' : '' }}>Physiotherapist</option>
                                        <option value="sports_medical_doctor" {{ old('primary_profession') == 'sports_medical_doctor' ? 'selected' : '' }}>Sports Medical Doctor</option>
                                        <option value="boxing_coach" {{ old('primary_profession') == 'boxing_coach' ? 'selected' : '' }}>Boxing Coach</option>
                                        <option value="wrestling_coach" {{ old('primary_profession') == 'wrestling_coach' ? 'selected' : '' }}>Wrestling Coach</option>
                                        <option value="striking_coach" {{ old('primary_profession') == 'striking_coach' ? 'selected' : '' }}>Striking Coach</option>
                                        <option value="bjj_coach" {{ old('primary_profession') == 'bjj_coach' ? 'selected' : '' }}>BJJ Coach</option>
                                        <option value="muay_thai_coach" {{ old('primary_profession') == 'muay_thai_coach' ? 'selected' : '' }}>Muay Thai Coach</option>
                                        <option value="coaching" {{ old('primary_profession') == 'coaching' ? 'selected' : '' }}>Coaching</option>
                                    </select>
                                    @if ($errors->has('primary_profession'))
                                    <span class="invalid-feedback" style="display: block;" role="alert">
                                        <strong>{{ $errors->first('primary_profession') }}</strong>
                                    </span>
                                    @endif
                                </div>

                                <div class="input-group{{ $errors->has('discipline') ? ' has-danger' : '' }}">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="nc-icon nc-tag-content"></i>
                                        </span>
                                    </div>
                                    <select name="discipline" class="form-control">
                                        <option value="">Select Specialization Discipline</option>
                                        <option value="boxing" {{ old('discipline') == 'boxing' ? 'selected' : '' }}>Boxing</option>
                                        <option value="mma" {{ old('discipline') == 'mma' ? 'selected' : '' }}>MMA</option>
                                        <option value="taekwondo" {{ old('discipline') == 'taekwondo' ? 'selected' : '' }}>Taekwondo</option>
                                        <option value="karate" {{ old('discipline') == 'karate' ? 'selected' : '' }}>Karate</option>
                                        <option value="wrestling" {{ old('discipline') == 'wrestling' ? 'selected' : '' }}>Wrestling</option>
                                        <option value="jiu_jitsu" {{ old('discipline') == 'jiu_jitsu' ? 'selected' : '' }}>Jiu jitsu</option>
                                        <option value="kick_boxing" {{ old('discipline') == 'kick_boxing' ? 'selected' : '' }}>Kick Boxing</option>
                                        <option value="thai_boxing" {{ old('discipline') == 'thai_boxing' ? 'selected' : '' }}>Thai Boxing</option>
                                        <option value="judo" {{ old('discipline') == 'judo' ? 'selected' : '' }}>Judo</option>
                                        <option value="kung_fu" {{ old('discipline') == 'kung_fu' ? 'selected' : '' }}>Kung Fu</option>
                                        <option value="tai_chi" {{ old('discipline') == 'tai_chi' ? 'selected' : '' }}>Tai Chi</option>
                                        <option value="wing_chun" {{ old('discipline') == 'wing_chun' ? 'selected' : '' }}>Wing Chun</option>
                                        <option value="krav_maga" {{ old('discipline') == 'krav_maga' ? 'selected' : '' }}>Krav Maga</option>
                                        <option value="aikido" {{ old('discipline') == 'aikido' ? 'selected' : '' }}>Aikido</option>
                                        <option value="choi_kwang_do" {{ old('discipline') == 'choi_kwang_do' ? 'selected' : '' }}>Choi kwang do</option>
                                        <option value="capoeira" {{ old('discipline') == 'capoeira' ? 'selected' : '' }}>Capoeira</option>
                                        <option value="ninjutsu" {{ old('discipline') == 'ninjutsu' ? 'selected' : '' }}>Ninjutsu</option>
                                        <option value="kendo" {{ old('discipline') == 'kendo' ? 'selected' : '' }}>Kendo</option>
                                        <option value="kobudo" {{ old('discipline') == 'kobudo' ? 'selected' : '' }}>Kobudo</option>
                                        <option value="hapkido" {{ old('discipline') == 'hapkido' ? 'selected' : '' }}>Hapkido</option>
                                        <option value="tang_soo_do" {{ old('discipline') == 'tang_soo_do' ? 'selected' : '' }}>Tang soo do</option>
                                    </select>
                                    @if ($errors->has('discipline'))
                                    <span class="invalid-feedback" style="display: block;" role="alert">
                                        <strong>{{ $errors->first('discipline') }}</strong>
                                    </span>
                                    @endif
                                </div>

                                <div class="input-group{{ $errors->has('badge_level') ? ' has-danger' : '' }}">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="nc-icon nc-trophy"></i>
                                        </span>
                                    </div>
                                    <select name="badge_level" class="form-control">
                                        <option value="">Select Badge Level (Optional)</option>
                                        <option value="bronze" {{ old('badge_level') == 'bronze' ? 'selected' : '' }}>Bronze</option>
                                        <option value="silver" {{ old('badge_level') == 'silver' ? 'selected' : '' }}>Silver</option>
                                        <option value="gold" {{ old('badge_level') == 'gold' ? 'selected' : '' }}>Gold</option>
                                    </select>
                                    @if ($errors->has('badge_level'))
                                    <span class="invalid-feedback" style="display: block;" role="alert">
                                        <strong>{{ $errors->first('badge_level') }}</strong>
                                    </span>
                                    @endif
                                </div>

                                <div class="input-group{{ $errors->has('profession_count') ? ' has-danger' : '' }}">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="nc-icon nc-bullet-list-67"></i>
                                        </span>
                                    </div>
                                    <input name="profession_count" type="number" class="form-control" placeholder="Number of professions/specialties" value="{{ old('profession_count', 1) }}" min="1" max="10">
                                    @if ($errors->has('profession_count'))
                                    <span class="invalid-feedback" style="display: block;" role="alert">
                                        <strong>{{ $errors->first('profession_count') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Gym Fields -->
                            <div id="gym_fields" style="display: none;">
                                <h5 class="text-left mt-3 mb-3">Gym Information</h5>

                                <div class="input-group{{ $errors->has('gym_type') ? ' has-danger' : '' }}">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="nc-icon nc-tag-content"></i>
                                        </span>
                                    </div>
                                    <select name="gym_type" class="form-control">
                                        <option value="">Select Gym Type</option>
                                        <option value="boxing" {{ old('gym_type') == 'boxing' ? 'selected' : '' }}>Boxing</option>
                                        <option value="mma" {{ old('gym_type') == 'mma' ? 'selected' : '' }}>MMA</option>
                                        <option value="taekwondo" {{ old('gym_type') == 'taekwondo' ? 'selected' : '' }}>Taekwondo</option>
                                        <option value="karate" {{ old('gym_type') == 'karate' ? 'selected' : '' }}>Karate</option>
                                        <option value="wrestling" {{ old('gym_type') == 'wrestling' ? 'selected' : '' }}>Wrestling</option>
                                        <option value="jiu_jitsu" {{ old('gym_type') == 'jiu_jitsu' ? 'selected' : '' }}>Jiu jitsu</option>
                                        <option value="kick_boxing" {{ old('gym_type') == 'kick_boxing' ? 'selected' : '' }}>Kick Boxing</option>
                                        <option value="thai_boxing" {{ old('gym_type') == 'thai_boxing' ? 'selected' : '' }}>Thai Boxing</option>
                                        <option value="judo" {{ old('gym_type') == 'judo' ? 'selected' : '' }}>Judo</option>
                                        <option value="kung_fu" {{ old('gym_type') == 'kung_fu' ? 'selected' : '' }}>Kung Fu</option>
                                        <option value="tai_chi" {{ old('gym_type') == 'tai_chi' ? 'selected' : '' }}>Tai Chi</option>
                                        <option value="wing_chun" {{ old('gym_type') == 'wing_chun' ? 'selected' : '' }}>Wing Chun</option>
                                        <option value="krav_maga" {{ old('gym_type') == 'krav_maga' ? 'selected' : '' }}>Krav Maga</option>
                                        <option value="aikido" {{ old('gym_type') == 'aikido' ? 'selected' : '' }}>Aikido</option>
                                        <option value="choi_kwang_do" {{ old('gym_type') == 'choi_kwang_do' ? 'selected' : '' }}>Choi kwang do</option>
                                        <option value="capoeira" {{ old('gym_type') == 'capoeira' ? 'selected' : '' }}>Capoeira</option>
                                        <option value="ninjutsu" {{ old('gym_type') == 'ninjutsu' ? 'selected' : '' }}>Ninjutsu</option>
                                        <option value="kendo" {{ old('gym_type') == 'kendo' ? 'selected' : '' }}>Kendo</option>
                                        <option value="kobudo" {{ old('gym_type') == 'kobudo' ? 'selected' : '' }}>Kobudo</option>
                                        <option value="hapkido" {{ old('gym_type') == 'hapkido' ? 'selected' : '' }}>Hapkido</option>
                                        <option value="tang_soo_do" {{ old('gym_type') == 'tang_soo_do' ? 'selected' : '' }}>Tang soo do</option>
                                    </select>
                                    @if ($errors->has('gym_type'))
                                    <span class="invalid-feedback" style="display: block;" role="alert">
                                        <strong>{{ $errors->first('gym_type') }}</strong>
                                    </span>
                                    @endif
                                </div>

                                <div class="input-group{{ $errors->has('bio') ? ' has-danger' : '' }}">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="nc-icon nc-align-left-2"></i>
                                        </span>
                                    </div>
                                    <textarea name="bio" class="form-control" rows="3" placeholder="Gym description/bio">{{ old('bio') }}</textarea>
                                    @if ($errors->has('bio'))
                                    <span class="invalid-feedback" style="display: block;" role="alert">
                                        <strong>{{ $errors->first('bio') }}</strong>
                                    </span>
                                    @endif
                                </div>

                                <div class="input-group{{ $errors->has('contact_info') ? ' has-danger' : '' }}">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="nc-icon nc-mobile"></i>
                                        </span>
                                    </div>
                                    <input name="contact_info" type="text" class="form-control" placeholder="Contact information (phone, website, etc.)" value="{{ old('contact_info') }}">
                                    @if ($errors->has('contact_info'))
                                    <span class="invalid-feedback" style="display: block;" role="alert">
                                        <strong>{{ $errors->first('contact_info') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Common Fields for All Types -->
                            <div id="common_fields" style="display: none;">
                                <h5 class="text-left mt-3 mb-3">Location Information</h5>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="input-group{{ $errors->has('country_id') ? ' has-danger' : '' }}">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="nc-icon nc-globe"></i>
                                                </span>
                                            </div>
                                            <select name="country_id" id="registration_country_select" class="form-control">
                                                <option value="">Select Country</option>
                                                <!-- Countries will be loaded dynamically -->
                                            </select>
                                            @if ($errors->has('country_id'))
                                            <span class="invalid-feedback" style="display: block;" role="alert">
                                                <strong>{{ $errors->first('country_id') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group{{ $errors->has('city_id') ? ' has-danger' : '' }}">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="nc-icon nc-pin-3"></i>
                                                </span>
                                            </div>
                                            <select name="city_id" id="registration_city_select" class="form-control" disabled>
                                                <option value="">Select Country First</option>
                                            </select>
                                            @if ($errors->has('city_id'))
                                            <span class="invalid-feedback" style="display: block;" role="alert">
                                                <strong>{{ $errors->first('city_id') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-check text-left">
                                <label class="form-check-label">
                                    <input class="form-check-input" name="agree_terms_and_conditions" type="checkbox">
                                    <span class="form-check-sign"></span>
                                    {{ __('I agree to the') }}
                                    <a href="#something">{{ __('terms and conditions') }}</a>.
                                </label>
                                @if ($errors->has('agree_terms_and_conditions'))
                                <span class="invalid-feedback" style="display: block;" role="alert">
                                    <strong>{{ $errors->first('agree_terms_and_conditions') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="card-footer ">
                                <button type="submit" class="btn btn-info btn-round">{{ __('Get Started') }}</button>
                            </div>
                        </form>
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
        demo.checkFullPageBackgroundImage();

        // Handle registration type change
        $('#registration_type').change(function() {
            var selectedType = $(this).val();

            // Hide all conditional fields
            $('#fighter_fields').hide();
            $('#professional_fields').hide();
            $('#gym_fields').hide();
            $('#common_fields').hide();

            // Show relevant fields based on selection
            if (selectedType === 'fighter') {
                $('#fighter_fields').show();
                $('#common_fields').show();
                loadRegistrationCountries();
            } else if (selectedType === 'professional') {
                $('#professional_fields').show();
                $('#common_fields').show();
                loadRegistrationCountries();
            } else if (selectedType === 'gym') {
                $('#gym_fields').show();
                $('#common_fields').show();
                loadRegistrationCountries();
            }
        });

        // Country change handler for registration
        $(document).on('change', '#registration_country_select', function() {
            var countryId = $(this).val();
            if (countryId) {
                loadRegistrationCities(countryId);
            } else {
                $('#registration_city_select').html('<option value="">Select Country First</option>').prop('disabled', true);
            }
        });

        // Trigger change on page load if a value is already selected (for validation errors)
        if ($('#registration_type').val()) {
            $('#registration_type').trigger('change');
        }
    });

    function loadRegistrationCountries() {
        $.ajax({
            url: '{{ route("api.countries") }}',
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    var options = '<option value="">Select Country</option>';
                    response.data.forEach(function(country) {
                        options += '<option value="' + country.id + '">' + country.name + '</option>';
                    });
                    $('#registration_country_select').html(options);
                }
            },
            error: function() {
                console.error('Error loading countries');
            }
        });
    }

    function loadRegistrationCities(countryId) {
        $.ajax({
            url: '{{ route("api.cities") }}',
            type: 'GET',
            data: { country_id: countryId },
            success: function(response) {
                if (response.success) {
                    var options = '<option value="">Select City</option>';
                    response.data.forEach(function(city) {
                        options += '<option value="' + city.id + '">' + city.name + '</option>';
                    });
                    $('#registration_city_select').html(options).prop('disabled', false);
                }
            },
            error: function() {
                console.error('Error loading cities');
                $('#registration_city_select').html('<option value="">Error loading cities</option>');
            }
        });
    }
</script>
@endpush