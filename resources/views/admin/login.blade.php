<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ env('APP_NAME') }} | Admin Panel</title>
	  <link rel="icon" href="{{ asset(env('APP_FAVICON')) }}" type="image/{{ env('APP_FAVICON_TYPE', 'png') }}" />

    <!-- Bootstrap -->
    <link href="{{ asset('/admin/vendors/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="{{ asset('/admin/vendors/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
    <!-- NProgress -->
    <link href="{{ asset('/admin/vendors/nprogress/nprogress.css') }}" rel="stylesheet">
    <!-- Animate.css -->
    <link href="{{ asset('/admin/vendors/animate.css/animate.min.css') }}" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="{{ asset('/admin/build/css/custom.min.css') }}" rel="stylesheet">
  </head>

  <body class="login">
    <div>
      <a class="hiddenanchor" id="signup"></a>
      <a class="hiddenanchor" id="signin"></a>

      <div class="login_wrapper">
        <div class="animate form login_form">

          @include('_template_adm.message')
          
          <section class="login_content">
            <form action="{{ route('admin_do_login') }}" method="POST">
              {{ csrf_field() }}
              <h1>{{ ucwords(lang('admin login form', $translation)) }}</h1>
              <div>
              <input type="text" name="login_id" value="{{ old('login_id') }}" class="form-control" placeholder="{{ ucwords(lang('username', $translation)) }}" required="" />
              </div>
              <div>
                <input type="password" name="login_pass" class="form-control" placeholder="{{ ucwords(lang('password', $translation)) }}" required="" autocomplete="off" />
              </div>
              <div>
                <button type="submit" class="btn btn-default submit">{{ ucfirst(lang('log in', $translation)) }}</button>
                <!--<a class="reset_pass" href="#">Lost your password?</a>-->
              </div>

              <div class="clearfix"></div>

              <div class="separator">
                <!--<p class="change_link">New to site?
                  <a href="#signup" class="to_register"> Create Account </a>
                </p>-->

                <div class="clearfix"></div>
                <br />

                <div>
                  <h1><?php echo $app_logo; ?> &nbsp;{{ env('APP_NAME') }}</h1>
                  <p>
                    &copy; {{ date('Y') }} {{ env('APP_NAME') }} 
                    @if (env('POWERED'))
                      - <a href="{{ env('POWERED_URL') }}">{{ env('POWERED') }}</a>
                    @endif
                  </p>
                </div>
              </div>
            </form>
          </section>
        </div>

        <div id="register" class="animate form registration_form">
          <section class="login_content">
            <form>
              <h1>Create Account</h1>
              <div>
                <input type="text" class="form-control" placeholder="Username" required="" />
              </div>
              <div>
                <input type="email" class="form-control" placeholder="Email" required="" />
              </div>
              <div>
                <input type="password" class="form-control" placeholder="Password" required="" />
              </div>
              <div>
                <a class="btn btn-default submit" href="index.html">Submit</a>
              </div>

              <div class="clearfix"></div>

              <div class="separator">
                <p class="change_link">Already a member ?
                  <a href="#signin" class="to_register"> Log in </a>
                </p>

                <div class="clearfix"></div>
                <br />

                <div>
                  <h1><?php echo $app_logo; ?> &nbsp;{{ env('APP_NAME') }}</h1>
                  <p>
                    &copy; {{ date('Y') }} {{ env('APP_NAME') }} 
                    @if (env('POWERED'))
                      - <a href="{{ env('POWERED_URL') }}">{{ env('POWERED') }}</a>
                    @endif
                  </p>
                </div>
              </div>
            </form>
          </section>
        </div>
      </div>
    </div>
  </body>
</html>
