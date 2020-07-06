@extends('_template_adm.master')

@php
    $pagetitle = ucwords(lang('config', $translation));
    $link = route('admin.config.update', $data->id);
@endphp

@section('title', $pagetitle)

@section('content')
<div class="">
    <!-- message info -->
    @include('_template_adm.message')

    <div class="page-title">
        <div class="title_left">
            <h3>{{ $pagetitle }}</h3>
        </div>
    </div>

    <div class="clearfix"></div>

    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>{{ ucwords(lang('form details', $translation)) }}</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <br />
                    <form class="form-horizontal form-label-left" action="{{ $link }}" method="POST" enctype="multipart/form-data">
                        {{ csrf_field() }}

                        @php
                            // set_input_form2($type, $input_name, $label_name, $data, $errors, $required = false, $config = null)
                            echo set_input_form2('text', 'app_name', ucwords(lang('application name', $translation)), $data, $errors, true);
                            echo set_input_form2('text', 'app_version', ucwords(lang('application version', $translation)), $data, $errors, true);
                            
                            echo set_input_form2('text', 'app_url_site', ucwords(lang('application URL', $translation)), $data, $errors, true);
                            echo set_input_form2('text', 'app_url_main', ucwords(lang('application URL main', $translation)), $data, $errors, false);
                            
                            $config = new \stdClass();
                            $config->defined_data = ['ico' => 'ico', 'png' => 'png'];
                            echo set_input_form2('select', 'app_favicon_type', ucwords(lang('favicon type', $translation)), $data, $errors, true, $config);
                            if (empty($data->app_favicon)) {
                                echo set_input_form2('image', 'app_favicon', ucwords(lang('favicon', $translation)), $data, $errors, true);
                            } else {
                                echo set_input_form2('image', 'app_favicon', ucwords(lang('favicon', $translation)), $data, $errors, false);
                            }
                            echo set_input_form2('text', 'app_logo', ucwords(lang('application logo icon', $translation)), $data, $errors, true);
                            if (empty($data->app_logo_image)) {
                                echo set_input_form2('image', 'app_logo_image', ucwords(lang('application logo image', $translation)), $data, $errors, true);
                            } else {
                                echo set_input_form2('image', 'app_logo_image', ucwords(lang('application logo image', $translation)), $data, $errors, false);
                            }

                            echo set_input_form2('textarea', 'help', ucwords(lang('help', $translation)), $data, $errors, true);

                            echo set_input_form2('text', 'powered', ucwords(lang('powered by', $translation)), $data, $errors, false);
                            echo set_input_form2('text', 'powered_url', ucwords(lang('powered URL', $translation)), $data, $errors, false);

                            echo set_input_form2('tags', 'meta_keywords', ucwords(lang('meta keywords', $translation)), $data, $errors, false);
                            echo set_input_form2('text', 'meta_title', ucwords(lang('meta title', $translation)), $data, $errors, true);
                            echo set_input_form2('textarea', 'meta_description', ucwords(lang('meta description', $translation)), $data, $errors, false);
                            echo set_input_form2('text', 'meta_author', ucwords(lang('meta author', $translation)), $data, $errors, true);
                        @endphp
                        
                        <div class="ln_solid"></div>
                        <div class="form-group">
                            <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i>&nbsp; 
                                    @if (isset($data))
                                        {{ ucwords(lang('save', $translation)) }}
                                    @else
                                        {{ ucwords(lang('submit', $translation)) }}
                                    @endif
                                </button>
                                <a href="{{ route('admin.config') }}" class="btn btn-danger"><i class="fa fa-times"></i>&nbsp; {{ ucwords(lang('cancel', $translation)) }}</a>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
    <!-- Switchery -->
    @include('_form_element.switchery.css')
@endsection

@section('script')
    <!-- Switchery -->
    @include('_form_element.switchery.script')
    <!-- jQuery Tags Input -->
    @include('_form_element.tagsinput.script')
@endsection