@php
  use App\Libraries\Helper;
@endphp

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	  <link rel="icon" href="{{ asset($global_config->app_favicon) }}" type="image/{{ $global_config->app_favicon_type }}" />

    <title>@if(View::hasSection('title'))@yield('title') | @endif{!! $global_config->app_name !!}@if(env('ADMIN_DIR') != '') Admin @endif</title>

    <meta name="description" content="{!! $global_config->meta_description !!}">
    <meta name="keywords" content="{!! str_replace(',', ', ', $global_config->meta_keywords) !!}">

    @if(View::hasSection('open_graph'))
      @yield('open_graph')
    @else
      {{-- DEFAULT OPEN GRAPH --}}
      <meta property="og:type" content="{!! $global_config->og_type !!}" />
      <meta property="og:site_name" content="{!! $global_config->og_site_name !!}" />
      <meta property="og:title" content="@if(View::hasSection('title'))@yield('title')@else{!! $global_config->og_title !!}@endif" />
      <meta property="og:image" content="{{ asset($global_config->og_image) }}" />
      <meta property="og:description" content="{!! $global_config->og_description !!}" />
      <meta property="og:url" content="{{ Helper::get_url() }}" />

      @if ($global_config->fb_app_id)
        <meta property="fb:app_id" content="{!! $global_config->fb_app_id !!}" />
      @endif

      <meta property="twitter:card" content="{!! $global_config->twitter_card !!}" />
      @if ($global_config->twitter_site)
        <meta property="twitter:site" content="{!! $global_config->twitter_site !!}" />
      @endif
      @if ($global_config->twitter_site_id)
        <meta property="twitter:site:id" content="{!! $global_config->twitter_site_id !!}" />
      @endif
      @if ($global_config->twitter_creator)
        <meta property="twitter:creator" content="{!! $global_config->twitter_creator !!}" />
      @endif
      @if ($global_config->twitter_creator_id)
        <meta property="twitter:creator:id" content="{!! $global_config->twitter_creator_id !!}" />
      @endif
    @endif

    <!-- Bootstrap -->
    <link href="{{ asset('admin/vendors/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="{{ asset('admin/vendors/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
    <!-- NProgress -->
    <link href="{{ asset('admin/vendors/nprogress/nprogress.css') }}" rel="stylesheet">
    <!-- jQuery custom content scroller -->
    <link href="{{ asset('admin/vendors/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.min.css') }}" rel="stylesheet">
    <!-- bootstrap-progressbar -->
    <link href="{{ asset('admin/vendors/bootstrap-progressbar/css/bootstrap-progressbar-3.3.4.min.css') }}" rel="stylesheet">
    <!-- animate.css -->
    <link href="{{ asset('admin/vendors/animate.css/animate.min.css') }}" rel="stylesheet">
    
    <!-- Custom Theme Style -->
    <link href="{{ asset('admin/build/css/custom.css') }}" rel="stylesheet">

    <style>
      .scroll-top {
        width: 40px;
        height: 30px;
        position: fixed;
        bottom: 50px;
        right: 17px;
        display: none;
        -ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=50)";       /* IE 8 */
        filter: alpha(opacity=50);  /* IE 5-7 */
        -moz-opacity: 0.5;          /* Netscape */
        -khtml-opacity: 0.5;        /* Safari 1.x */
        opacity: 0.5;               /* Good browsers */
      }
      .scroll-top i {
        display: inline-block;
        color: #FFFFFF;
      }

      /* template coloring setup */
      .left_col, .nav_title, body, .sidebar-footer {
        background: #143c6d !important;
      }
      .nav.side-menu>li.active>a{
        background: linear-gradient(#334556,#2C4257),#143c6d !important;
      }
      .sidebar-footer a {
        background: #fae54d !important;
        color: #143c6d !important;
      }
      .sidebar-footer a:hover {
        background: #46a2db !important;
        color: white !important;
      }
      .nav_menu {
        background: #fae54d !important;
      }
      .nav.navbar-nav>li>a{
        color :#143c6d !important;
      }
      .top_nav .nav .open>a,.top_nav .nav .open>a:focus,.top_nav .nav .open>a:hover,.top_nav .nav>li>a:focus,.top_nav .nav>li>a:hover{
        background:#46a2db !important;
        color: white !important;
      }
      #menu_toggle {
        color: #143c6d !important;
      }
    </style>

    @yield('css')

    @yield('script-head')
  </head>

  <body class="nav-md">
    <div class="container body">
      <div class="main_container">
        <div class="col-md-3 left_col menu_fixed">
          <div class="left_col scroll-view">
            <div class="navbar nav_title" style="border: 0;">
              <a href="{{ route('admin.home') }}" class="site_title"><?php echo $app_logo; ?> <span>{{ $global_config->app_name }}</span></a>
            </div>

            <div class="clearfix"></div>

            <!-- menu profile quick info -->
            <div class="profile clearfix">
              <div class="profile_pic">
                <img src="{{ asset('/images/avatar.png') }}" alt="avatar" class="img-circle profile_img">
              </div>
              <div class="profile_info">
                <span>{{ ucwords(lang('welcome', $translation)) }},</span>
                <h2>{{ Session::get('admin')->name }}</h2>
              </div>
            </div>
            <!-- /menu profile quick info -->

            <br />

            @include('_template_adm.sidebar')

            <!-- /menu footer buttons -->
            <div class="sidebar-footer hidden-small">
              <a data-toggle="tooltip" data-placement="top" title="{{ ucwords(lang('my profile', $translation)) }}" href="{{ route('admin.profile') }}">
                <span class="glyphicon glyphicon-user" aria-hidden="true"></span>
              </a>
              <a data-toggle="tooltip" data-placement="top" title="{{ ucwords(lang('view website', $translation)) }}" href="{{ $global_config->app_url_site }}" target="_blank">
                  <span class="glyphicon glyphicon-globe" aria-hidden="true"></span>
              </a>
              <a data-toggle="tooltip" data-placement="top" title="{{ ucwords(lang('help', $translation)) }}" onclick="alert('{{ $global_config->help }}')">
                <span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span>
              </a>
              <a data-toggle="tooltip" data-placement="top" title="{{ ucwords(lang('log out', $translation)) }}" href="{{ route('admin.logout') }}" onclick="return confirm('{{ lang('Are you sure to logout?', $translation) }}')">
                <span class="glyphicon glyphicon-off" aria-hidden="true"></span>
              </a>
            </div>
            <!-- /menu footer buttons -->
            
          </div>
        </div>

        @include('_template_adm.nav')

        <!-- page content -->
        <div class="right_col" role="main">
          @yield('content')
        </div>
        <!-- /page content -->

        <!-- footer content -->
        <footer>
          <div class="pull-right">
            &copy; {{ date('Y') }} {{ $global_config->app_name }} {{ 'v'.$global_config->app_version }}
            @if (!empty($global_config->powered))
              - {{ lang('Powered by', $translation) }} <a href="{{ $global_config->powered_url }}">{{ $global_config->powered }}</a>
            @endif
          </div>
          <div class="clearfix"></div>
          
          @if (env('DISPLAY_SESSION', false))
            @include('_template_adm.debug')
          @endif
        </footer>
        <!-- /footer content -->
      </div>
    </div>

    <button class="btn btn-primary scroll-top" data-scroll="up" type="button">
      <i class="fa fa-chevron-up"></i>
    </button>

    <!-- jQuery -->
    <script src="{{ asset('admin/vendors/jquery/dist/jquery.min.js') }}"></script>
    <!-- Bootstrap -->
    <script src="{{ asset('admin/vendors/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <!-- FastClick -->
    <script src="{{ asset('admin/vendors/fastclick/lib/fastclick.js') }}"></script>
    <!-- NProgress -->
    <script src="{{ asset('admin/vendors/nprogress/nprogress.js') }}"></script>
    <!-- jQuery custom content scroller -->
    <script src="{{ asset('admin/vendors/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.concat.min.js') }}"></script>
    <!-- bootstrap-progressbar -->
    <script src="{{ asset('admin/vendors/bootstrap-progressbar/bootstrap-progressbar.min.js') }}"></script>

    @yield('script-sidebar')

    <!-- Custom Theme Scripts -->
    <script src="{{ asset('admin/build/js/custom.js?v=4') }}"></script>
    <!-- Custom Script -->
    <script src="{{ asset('admin/js/thehelper.js?v=2') }}"></script>

    <script>
      $(document).ready(function () {
        $(window).scroll(function () {
            if ($(this).scrollTop() > 100) {
                $('.scroll-top').fadeIn();
            } else {
                $('.scroll-top').fadeOut();
            }
        });

        $('.scroll-top').click(function () {
            $("html, body").animate({
                scrollTop: 0
            }, 500);
            return false;
        });

      });
    </script>

    @yield('script')
  </body>
</html>
