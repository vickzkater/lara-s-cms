@extends('_template_web.master')

@section('title', 'A PHP Laravel Skeleton for CMS')

@section('content')
    @include('_template_web.banner')

    <div class="container">

        <h1 class="my-4">
            Welcome to {{ $global_config->app_name }}
            <br>
            <a href="https://travis-ci.org/vickzkater/lara-s-cms" target="_blank"><img src="https://travis-ci.org/vickzkater/lara-s-cms.svg" alt="Build Status"></a>
            <a href="https://packagist.org/packages/vickzkater/lara-s-cms" target="_blank"><img class="latest_stable_version_img" src="https://img.shields.io/packagist/v/vickzkater/lara-s-cms" alt="Latest Stable Version"></a>
            <a href="https://packagist.org/packages/vickzkater/lara-s-cms" target="_blank"><img class="total_img" src="https://img.shields.io/packagist/dt/vickzkater/lara-s-cms" alt="Total Downloads"></a>
            <a href="https://packagist.org/packages/vickzkater/lara-s-cms" target="_blank"><img class="license_img" src="https://img.shields.io/packagist/l/vickzkater/lara-s-cms" alt="License"></a>
        </h1>

        <!-- Marketing Icons Section -->
        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="card h-100">
                    <h4 class="card-header">What is "Lara-S-CMS" ?</h4>
                    <div class="card-body">
                        <p class="card-text">{{ $global_config->app_name }} by <a href="https://kiniditech.com">KINIDI Tech</a> is a PHP Laravel Skeleton for Content Management System (CMS) or Admin Dashboard (within/without website)</p>
                    </div>
                    <div class="card-footer">
                        <a href="https://github.com/vickzkater/lara-s-cms#readme" class="btn btn-primary">Learn More</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-4">
                <div class="card h-100">
                <h4 class="card-header">Requirements</h4>
                <div class="card-body">
                    <p class="card-text">
                        <ul>
                            <li>PHP >= 7.2.5</li>
                            <li><a href="https://laravel.com/docs/7.x/installation#server-requirements">Laravel 7.x Requirements</a></li>
                        </ul>
                    </p>
                </div>
                <div class="card-footer">
                    <a href="https://github.com/vickzkater/lara-s-cms#readme" class="btn btn-primary">Learn More</a>
                </div>
                </div>
            </div>
            <div class="col-lg-4 mb-4">
                <div class="card h-100">
                <h4 class="card-header">Version</h4>
                <div class="card-body">
                    <p class="card-text">
                        <table>
                            <tr>
                                <td>Laravel</td>
                                <td>&nbsp;&nbsp; | &nbsp;&nbsp;</td>
                                <td>Lara-S-CMS</td>
                            </tr>
                            <tr>
                                <td>5.8.x</td>
                                <td>&nbsp;&nbsp; | &nbsp;&nbsp;</td>
                                <td>1.0 ; 1.1.0</td>
                            </tr>
                            <tr>
                                <td>6.x</td>
                                <td>&nbsp;&nbsp; | &nbsp;&nbsp;</td>
                                <td>1.0.1 ; 1.1.1</td>
                            </tr>
                            <tr>
                                <td>7.x</td>
                                <td>&nbsp;&nbsp; | &nbsp;&nbsp;</td>
                                <td>1.2.x</td>
                            </tr>
                        </table>
                    </p>
                </div>
                <div class="card-footer">
                    <a href="https://github.com/vickzkater/lara-s-cms#readme" class="btn btn-primary">Learn More</a>
                </div>
                </div>
            </div>
        </div>
        <!-- /.row -->

        @if (isset($products[0]))
            <!-- Portfolio Section -->
            <h2>Supported by</h2>

            <div class="row">
                @foreach ($products as $item)
                    <div class="col-lg-4 col-sm-6 portfolio-item">
                        <div class="card h-100">
                            <a href="#">
                                {{-- http://placehold.it/700x400 --}}
                                <img class="card-img-top" src="{{ asset($item->image) }}" alt="{{ $item->title }}">
                            </a>
                            <div class="card-body">
                                <h4 class="card-title">
                                    <a href="#">{{ $item->title }}</a>
                                </h4>
                                <p class="card-text"><?php echo $item->description; ?></p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <!-- /.row -->
        @endif

        <!-- Features Section -->
        <div class="row">
            <div class="col-lg-6">
                <h2>{{ $global_config->app_name }} Features</h2>
                <p style="text-align: justify;">{{ $global_config->app_name }} by <a href="https://kiniditech.com">KINIDI Tech</a> is a PHP Laravel Skeleton for Content Management System (CMS) or Admin Dashboard (within/without website) with features:</p>
                <ul>
                    <li>Support Multi Languages</li>
                    <li>Division/Subsidiary Management</li>
                    <li>Branch per Division Management</li>
                    <li>Rule Management</li>
                    <li>Usergroup Management</li>
                    <li>User (Admin) Management</li>
                    <li>Access/Privilege/User Roles Management</li>
                    <li>Custom Maintenance Mode</li>
                    <li>Support DataTables AJAX</li>
                    <li>Sortable Data List</li>
                    <li>Import & Export Excel File</li>
                    <li><a href="https://github.com/vickzkater/kiniditech-pagebuilder">PageBuilder by KINIDI Tech</a></li>
                    <li><strong><i>and many others, please check <a href="https://github.com/vickzkater/lara-s-cms#readme">here</i></a></strong></li>
                </ul>
                <p>Lara-S-CMS using Bootstrap 4 Admin Dashboard Template <a href="https://github.com/ColorlibHQ/gentelella">Gentelella</a> as Admin Template and <a href="https://startbootstrap.com/templates/modern-business/">Modern Business</a> as Website Template.</p>
            </div>
            <div class="col-lg-6">
                {{-- http://placehold.it/700x450 --}}
                <img class="img-fluid rounded" src="https://hosting.kiniditech.com/lara-s-cms_modules_v1.2.2.jpg" alt="Lara-S-CMS Modules">
            </div>
        </div>
        <!-- /.row -->

        <hr>

        <!-- Call to Action Section -->
        <div class="row mb-4">
        <div class="col-md-8">
            <p>Check out more about Lara-S-CMS and download the project on GitHub!</p>
        </div>
        <div class="col-md-4">
            <a class="btn btn-lg btn-secondary btn-block" href="https://github.com/vickzkater/lara-s-cms">Go to GitHub</a>
        </div>
        </div>

    </div>
    <!-- /.container -->
@endsection