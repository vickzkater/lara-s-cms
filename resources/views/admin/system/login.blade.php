<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset($global_config->app_favicon) }}" type="image/{{ $global_config->app_favicon_type }}" />

    <title>{{ $global_config->app_name }} | Admin Panel</title>

    <!-- Bootstrap -->
    <link href="{{ asset('admin/vendors/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="{{ asset('admin/vendors/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
    <!-- NProgress -->
    <link href="{{ asset('admin/vendors/nprogress/nprogress.css') }}" rel="stylesheet">
    <!-- Animate.css -->
    <link href="{{ asset('admin/vendors/animate.css/animate.min.css') }}" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="{{ asset('admin/build/css/custom.min.css') }}" rel="stylesheet">

    <style>
      .vlogin {
        background: #F7F7F7 url({{ asset('images/background.jpg') }}) no-repeat fixed center;
        background-size: cover;
      }
      .instagram { 
        background: #f09433; 
        background: -moz-linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%); 
        background: -webkit-linear-gradient(45deg, #f09433 0%,#e6683c 25%,#dc2743 50%,#cc2366 75%,#bc1888 100%); 
        background: linear-gradient(45deg, #f09433 0%,#e6683c 25%,#dc2743 50%,#cc2366 75%,#bc1888 100%); 
        filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f09433', endColorstr='#bc1888',GradientType=1 );
        color: white !important;
      }
      .instagram:hover {
        background: #e6683c;
      }
      .btn-social {
        text-decoration: none !important;
      }
    </style>
  
    @if (env('RECAPTCHA_SECRET_KEY_ADMIN'))
      <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    @endif
  </head>

  <body class="login vlogin">
    <div>
      <div class="login_wrapper">
        <div class="animate form login_form">

          @include('_template_adm.message')
          
          <section class="login_content">
            <center>
              <img src="{{ asset($global_config->app_logo_image) }}" class="img-responsive" alt="{{ $global_config->app_name }}" style="max-width: 150px; max-height: 150px;">
            </center>
            
            <form action="{{ route('admin.do_login') }}" method="POST" id="submitform">
              {{ csrf_field() }}
              <h1>{{ ucwords(lang('admin login form', $translation)) }}</h1>
              <div>
              <input type="text" name="login_id" value="{{ old('login_id') }}" class="form-control" placeholder="{{ ucwords(lang('username', $translation)) }}" required autocomplete="off" />
              </div>
              <div>
                <input type="password" name="login_pass" class="form-control" placeholder="{{ ucwords(lang('password', $translation)) }}" required autocomplete="off" />
              </div>

              @if (env('RECAPTCHA_SECRET_KEY_ADMIN'))
                <div style="margin-bottom: 10px;">
                  <center>
                    <div class="g-recaptcha" data-sitekey="{{ env('RECAPTCHA_SITE_KEY_ADMIN') }}"></div>
                  </center>
                </div>
              @endif

              <div>
                <button type="submit" class="btn btn-primary btn-block submit" id="btn-login">{{ ucfirst(lang('log in', $translation)) }}</button>
              </div>

              @if (env('AUTH_WITH_PROVIDER'))
                <div> - OR - </div>

                <div>
                  @if(env('GOOGLE_CLIENT_MODULE', false))
                    <a href="{{ route('admin.auth.provider', 'google') }}" class="btn btn-danger btn-block btn-social"><i class="fa fa-google"></i> &nbsp;&nbsp;&nbsp;Login with Google</a>
                  @endif
                  @if(env('FACEBOOK_CLIENT_MODULE', false))
                    <a href="{{ route('admin.auth.provider', 'facebook') }}" class="btn btn-primary btn-block btn-social"><i class="fa fa-facebook"></i> &nbsp;&nbsp;&nbsp;Login with Facebook</a>
                  @endif
                  @if(env('TWITTER_CLIENT_MODULE', false))
                    <a href="{{ route('admin.auth.provider', 'twitter') }}" class="btn btn-info btn-block btn-social"><i class="fa fa-twitter"></i> &nbsp;&nbsp;&nbsp;Login with Twitter</a>
                  @endif
                  @if(env('INSTAGRAM_CLIENT_MODULE', false))
                    <a href="{{ route('admin.auth.provider', 'instagram') }}" class="btn instagram btn-block btn-social"><i class="fa fa-instagram"></i> &nbsp;&nbsp;&nbsp;Login with Instagram</a>
                  @endif
                  @if(env('LINKEDIN_CLIENT_MODULE', false))
                    <a href="#" class="btn btn-dark btn-block btn-social"><i class="fa fa-linkedin"></i> &nbsp;&nbsp;&nbsp;Login with LinkedIn</a>
                  @endif
                </div>
              @endif

              <div class="clearfix"></div>

              <div class="separator">
                <div>
                  <h1>{{ $global_config->app_name }}</h1>
                  <p>
                    &copy; {{ date('Y') }} {{ $global_config->app_name }} {{ 'v'.$global_config->app_version }}
                    @if (!empty($global_config->powered))
                      - {{ lang('Powered by', $translation) }} <a href="{{ $global_config->powered_url }}">{{ $global_config->powered }}</a>
                    @endif
                  </p>
                </div>
              </div>
            </form>
          </section>
        </div>
      </div>
    </div>

    <script type="text/javascript" src="{{ asset('admin/vendors/jquery/dist/jquery.min.js') }}"></script>
    <script>
      $(document).ready(function () {
        $("#submitform").on('submit',function(e) {
          validate_form();

          // check reCAPTCHA
          var data_form = $(this).serialize();
          var split_data = data_form.split('&');
          var continue_step = true;
          // check empty reCAPTCHA
          $.each(split_data , function (index, value) {
            var split_tmp = value.split('=');
            if (split_tmp[0] == 'g-recaptcha-response' && split_tmp[1] == '') {
              continue_step = false;
              alert('Silahkan beri centang pada kotak "I\'m not a robot" (reCAPTCHA) untuk melanjutkan');
              return false;
            }
          });
          if (!continue_step) {
            return false;
          }

          return true;
        });
      });

      function validate_form() {
        $('#btn-login').addClass('disabled');
        $('#btn-login').removeClass('btn-primary');
        $('#btn-login').addClass('btn-warning');
        $('#btn-login').html('<i class="fa fa-spin fa-spinner"></i>&nbsp; {{ ucwords(lang('loading', $translation)) }}...');

        setTimeout(function(){ window.location.reload(); }, 3000);
      }
    </script>
  </body>
</html>
