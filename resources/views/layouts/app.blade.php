<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('paper') }}/img/apple-icon.png">
    <link rel="icon" type="image/png" href="{{ asset('paper') }}/img/favicon.png">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <!-- SEO Meta Tags -->
    <title>{{ $pageTitle ?? 'Ngumi Network - Bridging the Gap Between Fighters' }}</title>
    <meta name="description" content="{{ $pageDescription ?? 'Ngumi Network is a community-driven platform connecting fighters, trainers, and combat sports enthusiasts. Find sparring partners, share knowledge, and grow in combat sports.' }}">
    <meta name="keywords" content="{{ $pageKeywords ?? 'combat sports, martial arts, fighters, sparring, boxing, MMA, karate, taekwondo, wrestling, training, fitness' }}">
    <meta name="author" content="Ngumi Network">
    <meta name="robots" content="index, follow">
    <meta name="language" content="English">
    <meta name="revisit-after" content="7 days">

    <!-- Canonical URL -->
    <link rel="canonical" href="{{ url()->current() }}" />

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $ogTitle ?? $pageTitle ?? 'Ngumi Network - Bridging the Gap Between Fighters' }}">
    <meta property="og:description" content="{{ $ogDescription ?? $pageDescription ?? 'Ngumi Network is a community-driven platform connecting fighters, trainers, and combat sports enthusiasts.' }}">
    <meta property="og:image" content="{{ $ogImage ?? asset('paper/img/logo.png') }}">
    <meta property="og:site_name" content="Ngumi Network">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="{{ $twitterTitle ?? $pageTitle ?? 'Ngumi Network - Bridging the Gap Between Fighters' }}">
    <meta property="twitter:description" content="{{ $twitterDescription ?? $pageDescription ?? 'Ngumi Network is a community-driven platform connecting fighters, trainers, and combat sports enthusiasts.' }}">
    <meta property="twitter:image" content="{{ $twitterImage ?? $ogImage ?? asset('paper/img/logo.png') }}">

    <!-- Structured Data (JSON-LD) -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "Ngumi Network",
        "url": "{{ url('/') }}",
        "logo": "{{ asset('paper/img/logo.png') }}",
        "description": "Ngumi Network is a community-driven platform connecting fighters, trainers, and combat sports enthusiasts. Find sparring partners, share knowledge, and grow in combat sports.",
        "sameAs": [
            "{{ url('/') }}"
        ],
        "contactPoint": {
            "@type": "ContactPoint",
            "telephone": "",
            "contactType": "customer service",
            "availableLanguage": "English"
        },
        "areaServed": "Worldwide",
        "serviceType": "Combat Sports Platform"
    }
    </script>

    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebSite",
        "name": "Ngumi Network",
        "url": "{{ url('/') }}",
        "description": "Bridging the Gap Between Fighters - Find sparring partners, connect with trainers, and grow in combat sports.",
        "inLanguage": "en-US",
        "potentialAction": {
            "@type": "SearchAction",
            "target": {
                "@type": "EntryPoint",
                "urlTemplate": "{{ url('/directory?search={search_term_string}') }}"
            },
            "query-input": "required name=search_term_string"
        }
    }
    </script>

    <!-- Favicon and Icons -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('paper/img/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('paper/img/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('paper/img/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('paper/site.webmanifest') }}">

    <!-- Theme Color -->
    <meta name="theme-color" content="#ffffff">
    <meta name="msapplication-TileColor" content="#ffffff">

    <!-- Additional SEO Tags -->
    <meta name="geo.region" content="Worldwide">
    <meta name="geo.placename" content="Global">
    <meta name="distribution" content="global">
    <meta name="rating" content="general">
    <meta name="classification" content="sports, martial arts, combat sports">

    <!-- Dublin Core Metadata -->
    <meta name="DC.title" content="{{ $pageTitle ?? 'Ngumi Network - Bridging the Gap Between Fighters' }}">
    <meta name="DC.description" content="{{ $pageDescription ?? 'Ngumi Network is a community-driven platform connecting fighters, trainers, and combat sports enthusiasts.' }}">
    <meta name="DC.subject" content="combat sports, martial arts, fighters, sparring">
    <meta name="DC.creator" content="Ngumi Network">

    <!-- Mobile Specific -->
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Ngumi Network">

    <!-- Security Headers -->
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <meta http-equiv="X-Frame-Options" content="SAMEORIGIN">
    <meta http-equiv="X-XSS-Protection" content="1; mode=block">
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
    <!--     Fonts and icons     -->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,200" rel="stylesheet" />
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" rel="stylesheet">
    <!-- CSS Files -->
    <link href="{{ asset('paper') }}/css/bootstrap.min.css" rel="stylesheet" />
    <link href="{{ asset('paper') }}/css/index.min.css" rel="stylesheet" />
    <link href="{{ asset('paper') }}/css/tony.css" rel="stylesheet" />


    <link href="{{ asset('paper') }}/css/paper-dashboard.css?v=2.0.0" rel="stylesheet" />
    <!-- CSS Just for demo purpose, don't include it in your project -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.10.23/af-2.3.5/b-1.6.5/b-colvis-1.6.5/b-flash-1.6.5/b-html5-1.6.5/b-print-1.6.5/cr-1.5.3/fc-3.3.2/fh-3.1.7/kt-2.5.3/r-2.2.6/rg-1.1.2/rr-1.2.7/sc-2.0.3/sb-1.0.1/sp-1.2.2/sl-1.3.1/datatables.min.css" />
    <script type="text/javascript" src="https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.10.23/af-2.3.5/b-1.6.5/b-colvis-1.6.5/b-flash-1.6.5/b-html5-1.6.5/b-print-1.6.5/cr-1.5.3/fc-3.3.2/fh-3.1.7/kt-2.5.3/r-2.2.6/rg-1.1.2/rr-1.2.7/sc-2.0.3/sb-1.0.1/sp-1.2.2/sl-1.3.1/datatables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>

    <script src="{{ asset('paper') }}/js/users.js"></script>

    {{-- <script src="/paper/js/users.js"></script> --}}
</head>

<body class="{{ $class ?? '' }}">

    @auth()
    @include('layouts.page_templates.auth')
    @include('layouts.navbars.fixed-plugin')
    @endauth

    @guest
    @include('layouts.page_templates.guest')
    @endguest

    <!--   Core JS Files   -->
    <script src="{{ asset('paper') }}/js/core/jquery.min.js"></script>
    <script src="{{ asset('paper') }}/js/core/popper.min.js"></script>
    <script src="{{ asset('paper') }}/js/core/bootstrap.min.js"></script>
    <script src="{{ asset('paper') }}/js/plugins/perfect-scrollbar.jquery.min.js"></script>
    <!--  Google Maps Plugin    -->
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_KEY_HERE"></script>
    <!-- Chart JS -->
    <script src="{{ asset('paper') }}/js/plugins/chartjs.min.js"></script>
    <!--  Notifications Plugin    -->
    <script src="{{ asset('paper') }}/js/plugins/bootstrap-notify.js"></script>
    <!-- Control Center for Now Ui Dashboard: parallax effects, scripts for the example pages etc -->
    <script src="{{ asset('paper') }}/js/paper-dashboard.min.js?v=2.0.0" type="text/javascript"></script>
    <!-- Paper Dashboard DEMO methods, don't include it in your project! -->
    <script src="{{ asset('paper') }}/demo/demo.js"></script>
    <!-- Sharrre libray -->





    @stack('scripts')

    @include('layouts.navbars.fixed-plugin-js')
</body>

</html>