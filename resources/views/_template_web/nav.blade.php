<nav class="navbar fixed-top navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container">
        <a class="navbar-brand" href="{{ route('web.home') }}"><img src="{{ asset($global_config->app_logo_image) }}" class="img-responsive" alt="{{ $global_config->app_name }}" style="max-width: 35px; max-height: 35px;"> {{ $global_config->app_name }}</a>
        <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav ml-auto">
                @php
                    if (!isset($page_menu)) {
                        $page_menu = 'home';
                    }
                @endphp
                <li class="nav-item">
                    <a class="nav-link @if($page_menu == 'home') active @endif" href="{{ route('web.home') }}">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @if($page_menu == 'blog') active @endif" href="{{ route('web.blog') }}">Blog</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @if($page_menu == 'admin') active @endif" href="{{ route('admin.home') }}">ADMIN</a>
                </li>
            </ul>
        </div>
    </div>
</nav>