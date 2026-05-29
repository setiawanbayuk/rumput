<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Title -->
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="icon" href="{{ asset('assets/img/logo/logo-pkk-index.png') }}" type="image/icon type">

    <!-- Google Fonts: Poppins & Nunito -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800%7CShadows+Into+Light%7CPlayfair+Display:400&display=swap" rel="stylesheet">
    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <!-- AOS (Animate On Scroll) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css">

    <!-- Custom CSS Files -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components.css') }}">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('css/skins/reverse.css') }}">
    <link rel="stylesheet" href="{{ asset('css/simpkk-home-mobile-pro.css') }}?v=20260529">

    <!-- Additional CSS (if any) -->
    @stack('css')

    <!-- Custom CSS -->
    <style>
        :root {
        --primary-color: #046BA8;
        --secondary-color: #03456b;
        --accent-color: #012c44;
        --light-bg: #f9f9f9;
        --text-color: #f9f9f9;
        }
        /* HERO SECTION */
        .hero {
        min-height: 100vh;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
        padding: 0 20px;
        color: #fff;
        }
        .hero h1 {
        font-size: 3rem;
        font-weight: 700;
        margin-bottom: 20px;
        }
        .hero p {
        font-size: 1.25rem;
        margin-bottom: 30px;
        max-width: 600px;
        }
        .btn-cta {
        background-color: #fff;
        color: var(--secondary-color);
        padding: 12px 40px;
        border: none;
        border-radius: 50px;
        font-weight: 600;
        transition: all 0.3s ease;
        }
        .btn-cta:hover {
        background-color: var(--accent-color);
        color: #fff;
        transform: scale(1.05);
        }
        /* SECTION STYLE */
        .section {
        padding: 80px 20px;
        }
        .section-title {
        font-size: 2.5rem;
        font-weight: 600;
        margin-bottom: 40px;
        }
        /* FEATURE CARDS */
        .feature-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        background-color: #fff;
        }
        .feature-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
        }
        .feature-card i {
        font-size: 3rem;
        color: var(--primary-color);
        margin-bottom: 15px;
        }
        /* CONTRIBUTORS */
        .contributors img {
        border-radius: 50%;
        border: 4px solid #fff;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .contributors img:hover {
        transform: scale(1.1);
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        /* FOOTER */
        footer {
        background: var(--secondary-color);
        color: #fff;
        text-align: center;
        }
        /* SCROLLBAR */
        ::-webkit-scrollbar{
            width: 7px;
            height: 7px;
        }
        ::-webkit-scrollbar-thumb{
            background: #dedede;
            border-radius: 15px;
        }
    </style>
</head>
<body class="pkk-guest-page">
    <!-- Header -->
    @include('homepage.components.guest-header')

    <!-- HERO SECTION -->
    @include('homepage.hero-section')

    <!-- FEATURES SECTION -->
    @include('homepage.feature-section')

    <!-- Footer -->
    @include('components.footer', ['isGuest' => true])

    <!-- JS Libraries -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <script>
        AOS.init({
        once: true,
        duration: 800,
        });
    </script>
    
    <!-- jQuery and Popper.js (required for Bootstrap 4) -->
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>

    <!-- Bootstrap JS (Bootstrap 4) -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- Custom JS Libraries -->
    <script src="{{ asset('library/jquery.nicescroll/dist/jquery.nicescroll.min.js') }}"></script>
    <script src="{{ asset('library/moment/min/moment.min.js') }}"></script>

    <!-- Template JS Files -->
    <script src="{{ asset('js/stisla.js') }}"></script>
    <script src="{{ asset('js/scripts.js') }}"></script>
    <script src="{{ asset('js/custom.js') }}"></script>

    <script src="{{ asset('js/simpkk-home-mobile-pro.js') }}?v=20260529"></script>

    <!-- Additional JS (if any) -->
    @stack('scripts')
</body>
</html>
