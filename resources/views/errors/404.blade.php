@php
    use Illuminate\Support\Facades\DB;
    $global_config = DB::table('sys_config')->first();

    if (env('ADMIN_DIR') == '' || Session::get('admin')) {
        $homepage = route('admin.home');
    } else {
        $homepage = route('web.home');
    }
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>404 ({{ lang("PAGE NOT FOUND") }}) | {{ $global_config->app_name }}</title>
    <meta name="keywords" content="{{ $global_config->meta_keywords }}" />
    <meta name="description" content="{{ $global_config->meta_description }}" />
    <meta name="Author" content="{{ $global_config->meta_author }}" />

    <link rel="icon" href="{{ asset($global_config->app_favicon) }}" type="image/{{ $global_config->app_favicon_type }}" />

    <!-- Latest compiled and minified CSS -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">

    <!-- Latest compiled and minified JavaScript -->
    {{-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script> --}}

    <style>
        html, body {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
            background-color: #000;
        }

        .bg-img {
            position: absolute;
            width: 100%;
            height: 100%;
            background: url({{ asset('images/background_404.jpg') }}) no-repeat center center fixed;
            background-size: cover;
            background-color: #000;
            opacity: .5;
            filter: alpha(opacity=50);
        }

        .content {
            font-family: 'Avenir-Next',Avenir,Helvetica,sans-serif;
            color: #fff;
            background-color: none;
            z-index: 2;
            position: absolute;
            top: 50%;
            -webkit-transform: translateY(-50%);
            -ms-transform: translateY(-50%);
            transform: translateY(-50%);
        }

        h1 {
            font-size: 160px;
            margin-bottom: 0;
            margin-top: 0;
        }

        h2 {
            margin-top: 0;
            max-width: 700px;
            font-size: 30px;
            width: 90%;
        }

        p {
            text-align: left;
            padding-bottom: 32px;
        }

        .btn {
            display: inline-block;
            border: 1px solid #aaa;
            border-radius: 40px;
            padding: 15px 30px;
            margin-right: 15px;
            margin-bottom: 10px;
            color: white;
        }
        .btn:hover {
            color: #e2e2e2;
            background: rgba(255, 255, 255, 0.1);
        }

        @media only screen and (max-width: 480px) {
            .btn {
                background-color: white;
                color: #444444;
                width: 100%;
            }

            h1 {
                font-size: 120px;
            }
        }
    </style>
</head>
<body>
    <div class='container'>
      <div class='row content'>
        <div class='col-lg-12'></div>
        <div class='col-lg-12'>
          <h1>404</h1>
          <h2>{{ lang("Oops, the page you're looking for does not exist.") }}</h2>
          <p>
            {{ lang("You may want to head back to the homepage.") }}
            {{-- <br>
              If you think something is broken, report a problem.
            <br> --}}
          </p>
          <a href="{{ $homepage }}" class='btn'>{{ lang("RETURN HOME") }}</a>
          {{-- <span class='btn'>REPORT PROBLEM</span> --}}
        </div>
      </div>
    </div>
    <div class='bg-img'></div>
  </body>  
</html>