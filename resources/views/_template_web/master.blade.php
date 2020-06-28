<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="{{ $global_config->meta_description }}">
    <meta name="author" content="{{ $global_config->meta_author }}">
    <link rel="icon" href="{{ asset($global_config->favicon) }}" type="image/{{ $global_config->favicon_type }}" />

    <title>
        @if(View::hasSection('title')) 
            @yield('title') -
        @endif
        {{ $global_config->app_name }}
    </title>

    <!-- Bootstrap core CSS -->
    <link href="{{ asset('web/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- Custom fonts for this template -->
    <link href="https://fonts.googleapis.com/css?family=Raleway:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Lora:400,400i,700,700i" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="{{ asset('web/css/business-casual.min.css') }}" rel="stylesheet">

    @yield('css')

    @yield('script-head')
</head>

<body>

    <h1 class="site-heading text-center text-white d-none d-lg-block">
        <span class="site-heading-upper text-primary mb-3">A PHP Laravel Skeleton with Bootstrap 4 Theme</span>
        <span class="site-heading-lower">{{ $global_config->app_name }}</span>
    </h1>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark py-lg-4" id="mainNav">
        <div class="container">
            <a class="navbar-brand text-uppercase text-expanded font-weight-bold d-lg-none" href="#">{{ $global_config->app_name }}</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarResponsive">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item px-lg-4 @if ($page == 'home') active @endif">
                        <a class="nav-link text-uppercase text-expanded" href="{{ route('web.home') }}">Home
                            <span class="sr-only">(current)</span>
                        </a>
                    </li>
                    <li class="nav-item px-lg-4 @if ($page == 'about') active @endif">
                        <a class="nav-link text-uppercase text-expanded" href="{{ route('web.about') }}">About</a>
                    </li>
                    <li class="nav-item px-lg-4 @if ($page == 'products') active @endif">
                        <a class="nav-link text-uppercase text-expanded" href="{{ route('web.products') }}">Products</a>
                    </li>
                    <li class="nav-item px-lg-4 @if ($page == 'store') active @endif">
                        <a class="nav-link text-uppercase text-expanded" href="{{ route('web.store') }}">Store</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    @yield('content')

    <footer class="footer text-faded text-center py-5">
        <div class="container">
            <p class="m-0 small">Copyright &copy; 2020 {{ $global_config->app_name }}</p>
        </div>
    </footer>

    <!-- Bootstrap core JavaScript -->
    <script src="{{ asset('web/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('web/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    @yield('script')
</body>

</html>
