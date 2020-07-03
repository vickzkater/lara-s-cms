@php
  use Illuminate\Support\Facades\DB;
  $global_config = DB::table('sys_config')->first();
  // get default language
  $default_lang = env('DEFAULT_LANGUAGE', 'EN');
  // get language data
  $getLanguageMasterMenu = DB::table('sys_language_master_details')
      ->select('sys_language_master.phrase', 'sys_language_master_details.translate')
      ->leftJoin('sys_languages', 'sys_language_master_details.language_id', '=', 'sys_languages.id')
      ->leftJoin('sys_language_master', 'sys_language_master_details.language_master_id', '=', 'sys_language_master.id')
      ->where('sys_languages.alias', $default_lang)
      ->get();

  // convert to single array
  $translation = [];
  foreach ($getLanguageMasterMenu as $list) {
      $translation[$list->phrase] = $list->translate;
  }
@endphp

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->

    <meta name="keywords" content="{{ $global_config->meta_keywords }}">
    <meta name="description" content="{{ $global_config->meta_description }}">

    <title>We will be back with new and exciting features!</title>

    <link rel="icon" href="{{ asset($global_config->app_favicon) }}" type="image/{{ $global_config->app_favicon_type }}" />
    <meta name="theme-color" content="#ffffff">

    <!-- Bootstrap -->
    <link href="{{ asset('/maintenance/css/bootstrap.min.css') }}" rel="stylesheet">
    
    <!-- google font -->
    <link href='https://fonts.googleapis.com/css?family=Cabin:400,700' rel='stylesheet' type='text/css'>

    <!-- Font Awesome -->
    <link href="{{ asset('admin/vendors/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">

    <!-- custom css -->
    <link href="{{ asset('/maintenance/css/custom.css') }}" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    <div class="se-pre-con"></div>
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <div class="header-logo-wrapper">
            <img src="{{ asset($global_config->app_logo_image) }}" alt="{{ $global_config->app_name }}" title="{{ $global_config->app_name }}" class="img-responsive center-block" style="max-width:300px !important;" />
            <h1 class="text-center">{{ $global_config->app_name }}</h1>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-12">
        <h1 class="text-center">We will be back with new and exciting features!</h1>
        <h3 class="text-center">Kami akan segera kembali dengan fitur-fitur baru dan menarik!</h3>
        </div>
      </div>
  
      <div class="row">
        <div class="col-md-12">
          <div id="counter_wrapper">
            <div class="text-center" id="counter"></div>
          </div>
        </div>
      </div>

      {{-- <div class="text-center subscribe-form-wrapper">
        <form action="#" class="form-inline">
          <div class="form-group">
           <label for="subscriberName">name</label>
           <input type="text" name="subscriberName" class="center-block form-control" placeholder="name" />
          </div>

          <div class="form-group">
            <label for="subscriberEmail">email</label>
            <input type="email" name="subscriberEmail" class="center-block form-control form-subs-email" placeholder="email" />
          </div>

          <button type="submit" class="btn btn-default">Subscribe</button>
        </form>
      </div> --}}

      <div class="row">
        <div class="col-md-12">
          <div class="social-media-wrapper text-center">
            @if (env('SOCMED_FACEBOOK_MODULE', 'OFF') == 'ON')
              <a href="{{ env('SOCMED_FACEBOOK_LINK', '#') }}" target="_blank"><i class="fa fa-facebook-official"></i></a>
            @endif
            @if (env('SOCMED_TWITTER_MODULE', 'OFF') == 'ON')
              <a href="{{ env('SOCMED_TWITTER_LINK', '#') }}" target="_blank"><i class="fa fa-twitter"></i></a>
            @endif
            @if (env('SOCMED_INSTAGRAM_MODULE', 'OFF') == 'ON')
              <a href="{{ env('SOCMED_INSTAGRAM_LINK', '#') }}" target="_blank"><i class="fa fa-instagram"></i></a>
            @endif
            @if (env('SOCMED_INSTAGRAM2_MODULE', 'OFF') == 'ON')
              <a href="{{ env('SOCMED_INSTAGRAM2_LINK', '#') }}" target="_blank"><i class="fa fa-instagram"></i></a>
            @endif
            @if (env('SOCMED_YOUTUBE_MODULE', 'OFF') == 'ON')
              <a href="{{ env('SOCMED_YOUTUBE_LINK', '#') }}" target="_blank"><i class="fa fa-youtube"></i></a>
            @endif
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-12">
          <div class="text-center copyright">
            Copyright &copy; {{ date('Y') }} {{ $global_config->app_name }}
            @if (!empty($global_config->powered))
              - {{ lang('Powered by', $translation) }} <a href="{{ $global_config->powered_url }}">{{ $global_config->powered }}</a>
            @endif
          </div> 
        </div>
      </div>
    
    </div>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="{{ asset('/maintenance/js/jquery.min.js') }}"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="{{ asset('/maintenance/js/bootstrap.min.js') }}"></script>

    <!-- fit text -->
    <script src="{{ asset('/maintenance/js/jquery.fittext.js') }}"></script>

    <!-- jquery countdown -->
    <script src="{{ asset('/maintenance/js/jquery.plugin.js') }}"></script>
    <script src="{{ asset('/maintenance/js/jquery.countdown.js') }}"></script>

    <!--placeholder -->
    <script src="{{ asset('/maintenance/js/jquery.placeholder.js') }}"></script>

    <script>
    $(window).load(function() {
        // Animate loader off screen
        $(".se-pre-con").fadeOut("slow");
    });

    $(document).ready(function(){
        $("#counter").countdown({
        until: new Date({{ env('APP_MAINTENANCE_UNTIL') }}),
        format: 'dHMS'
    });

    $("#counter_wrapper").fitText(1.2, {
        minFontSize: '20px',
        maxFontSize: '50px'
        });
    });
    </script>
  </body>
</html>