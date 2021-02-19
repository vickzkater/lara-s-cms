<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="{{ $global_config->meta_description }}">
    <meta name="author" content="{{ $global_config->meta_author }}">
    <link rel="icon" href="{{ asset($global_config->app_favicon) }}" type="image/{{ $global_config->app_favicon_type }}" />

    <title>@if(View::hasSection('title'))@yield('title') | @endif{!! $global_config->app_name !!}</title>

    <!-- Bootstrap core CSS -->
    <link href="{{ asset('web/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="{{ asset('web/css/modern-business.css') }}" rel="stylesheet">

    @yield('css')

    @yield('script-head')
</head>

<body>

    <!-- Navigation -->
    @include('_template_web.nav')

    <!-- Page Content -->
    @yield('content')

    <!-- Footer -->
    <footer class="py-5 bg-dark">
        <div class="container">
            <p class="m-0 text-center text-white">
                Copyright &copy; {{ date('Y') }} {{ $global_config->app_name }}
                @if (!empty($global_config->powered))
                    - {{ lang('Powered by', $translation) }} <a href="{{ $global_config->powered_url }}">{{ $global_config->powered }}</a>
                @endif
            </p>
        </div>
        <!-- /.container -->
    </footer>

    <!-- Bootstrap core JavaScript -->
    <script src="{{ asset('web/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('web/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    @yield('script')
</body>

</html>
