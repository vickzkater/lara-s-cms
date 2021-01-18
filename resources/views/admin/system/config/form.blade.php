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
                            
                            $config = new \stdClass();
                            $config->info_text = '<i class="fa fa-info-circle"></i> &nbsp;Input base URL for this application/website that managed by this CMS.';
                            echo set_input_form2('text', 'app_url_site', ucwords(lang('application URL', $translation)), $data, $errors, true, $config);

                            $config = new \stdClass();
                            $config->info_text = '<i class="fa fa-info-circle"></i> &nbsp;Input Main URL, if this application used for manage microsite. <br>(If this is used by "blog.your-domain.com" as microsite, then input "your-domain.com" as Main URL)';
                            echo set_input_form2('text', 'app_url_main', ucwords(lang('main application URL', $translation)), $data, $errors, false, $config);
                            
                            $config = new \stdClass();
                            $config->defined_data = ['ico', 'png'];
                            $config->info_text = '<i class="fa fa-info-circle"></i> &nbsp;Recommended to use "ico" & please make sure to upload image based on this type.';
                            echo set_input_form2('select', 'app_favicon_type', ucwords(lang('favicon type', $translation)), $data, $errors, true, $config);

                            $config = new \stdClass();
                            $config->info = '<i class="fa fa-info-circle"></i> &nbsp;Recommended to use the smallest image size.';
                            if (empty($data->app_favicon)) {
                                echo set_input_form2('image', 'app_favicon', ucwords(lang('favicon', $translation)), $data, $errors, true, $config);
                            } else {
                                echo set_input_form2('image', 'app_favicon', ucwords(lang('favicon', $translation)), $data, $errors, false, $config);
                            }

                            $config = new \stdClass();
                            $config->info_text = '<i class="fa fa-info-circle"></i> &nbsp;Set application logo based on Font Awesome (input without "fa-", only the icon name, e.g, star/laptop/bank) <i style="font-weight:bold">Not recommended to use this, please use "Application Logo Image".</i>';
                            echo set_input_form2('text', 'app_logo', ucwords(lang('application logo icon', $translation)), $data, $errors, true, $config);

                            $config = new \stdClass();
                            $config->info = '<i class="fa fa-info-circle"></i> &nbsp;Recommended to use the square image, PNG transparent, min. 72 x 72px.';
                            if (empty($data->app_logo_image)) {
                                echo set_input_form2('image', 'app_logo_image', ucwords(lang('application logo image', $translation)), $data, $errors, true, $config);
                            } else {
                                echo set_input_form2('image', 'app_logo_image', ucwords(lang('application logo image', $translation)), $data, $errors, false, $config);
                            }

                            $config = new \stdClass();
                            $config->info_text = '<i class="fa fa-info-circle"></i> &nbsp;Enter a short description as Help popup content.';
                            $config->autosize = true;
                            echo set_input_form2('textarea', 'help', ucwords(lang('help', $translation)), $data, $errors, true, $config);

                            echo set_input_form2('text', 'powered', ucwords(lang('powered by', $translation)), $data, $errors, false);
                            echo set_input_form2('text', 'powered_url', ucwords(lang('powered URL', $translation)), $data, $errors, false);
                        @endphp
                        
                        <div class="ln_solid"></div>

                        @php
                            echo set_input_form2('text', 'meta_title', ucwords(lang('meta title', $translation)), $data, $errors, true);

                            $config = new \stdClass();
                            $config->autosize = true;
                            echo set_input_form2('textarea', 'meta_description', ucwords(lang('meta description', $translation)), $data, $errors, true, $config);
                            echo set_input_form2('text', 'meta_author', ucwords(lang('meta author', $translation)), $data, $errors, true);
                            echo set_input_form2('tags', 'meta_keywords', ucwords(lang('meta keywords', $translation)), $data, $errors, true);
                        @endphp

                        <div class="ln_solid"></div>

                        @php
                            $config = new \stdClass();
                            $config->info_text = '<i class="fa fa-info-circle"></i> &nbsp;Example: website/article/etc. Read more on <a href="https://ogp.me/#types" target="_blank" style="font-style:italic; text-decoration:underline;">ogp.me <i class="fa fa-external-link"></i></a>.';
                            echo set_input_form2('text', 'og_type', ucwords(lang('open graph type', $translation)), $data, $errors, true, $config);

                            $config = new \stdClass();
                            $config->info_text = '<i class="fa fa-info-circle"></i> &nbsp;If your object is part of a larger web site, the name which should be displayed for the overall site. e.g., "IMDb".';
                            echo set_input_form2('text', 'og_site_name', ucwords(lang('open graph site name', $translation)), $data, $errors, true, $config);

                            $config = new \stdClass();
                            $config->info_text = '<i class="fa fa-info-circle"></i> &nbsp;The title of your object as it should appear within the graph.';
                            echo set_input_form2('text', 'og_title', ucwords(lang('open graph title', $translation)), $data, $errors, true, $config);

                            $config = new \stdClass();
                            $config->info = '<i class="fa fa-info-circle"></i> &nbsp;An image which should represent your object within the graph.';
                            if (empty($data->og_image)) {
                                echo set_input_form2('image', 'og_image', ucwords(lang('open graph image', $translation)), $data, $errors, true, $config);
                            } else {
                                echo set_input_form2('image', 'og_image', ucwords(lang('open graph image', $translation)), $data, $errors, false, $config);
                            }

                            $config = new \stdClass();
                            $config->info_text = '<i class="fa fa-info-circle"></i> &nbsp;A one to two sentence description of your object.';
                            $config->autosize = true;
                            echo set_input_form2('textarea', 'og_description', ucwords(lang('open graph description', $translation)), $data, $errors, true, $config);
                        @endphp

                        <div class="ln_solid"></div>

                        @php
                            $config = new \stdClass();
                            $config->defined_data = ["summary", "summary_large_image", "app", "player"];
                            $config->info_text = '<i class="fa fa-info-circle"></i> &nbsp;You can check <a href="https://developer.twitter.com/en/docs/twitter-for-websites/cards/overview/markup" target="_blank" style="font-style:italic; text-decoration:underline;">Twitter Dev Docs <i class="fa fa-external-link"></i></a> for more details. And test your Twitter Card on <a href="https://cards-dev.twitter.com/validator" target="_blank" style="font-style:italic; text-decoration:underline;">Card Validator <i class="fa fa-external-link"></i></a>.';
                            echo set_input_form2('select', 'twitter_card', ucwords(lang('twitter card', $translation)), $data, $errors, true, $config);

                            $config = new \stdClass();
                            $config->info_text = '<i class="fa fa-info-circle"></i> &nbsp;@username for the website used in the card footer.';
                            echo set_input_form2('text', 'twitter_site', ucwords(lang('twitter site', $translation)), $data, $errors, false, $config);

                            $config = new \stdClass();
                            $config->info_text = '<i class="fa fa-info-circle"></i> &nbsp;Same as Twitter Site, but the user’s Twitter ID. You can use <a href="https://tweeterid.com/" target="_blank" style="font-style:italic; text-decoration:underline;">tweeterid.com <i class="fa fa-external-link"></i></a> to get Twitter ID.';
                            echo set_input_form2('text', 'twitter_site_id', (lang('Twitter Site ID', $translation)), $data, $errors, false, $config);

                            $config = new \stdClass();
                            $config->info_text = '<i class="fa fa-info-circle"></i> &nbsp;@username for the content creator/author.';
                            echo set_input_form2('text', 'twitter_creator', ucwords(lang('twitter creator', $translation)), $data, $errors, false, $config);

                            $config = new \stdClass();
                            $config->info_text = '<i class="fa fa-info-circle"></i> &nbsp;Twitter user ID of content creator.';
                            echo set_input_form2('text', 'twitter_creator_id', (lang('Twitter Creator ID', $translation)), $data, $errors, false, $config);
                        @endphp
                        
                        <div class="ln_solid"></div>

                        @php
                            $config = new \stdClass();
                            $config->info_text = '<i class="fa fa-info-circle"></i> &nbsp;In order to use Facebook Insights you must add the app ID to your page. Insights lets you view analytics for traffic to your site from Facebook. Read more on <a href="https://developers.facebook.com/docs/sharing/webmasters/" target="_blank" style="font-style:italic; text-decoration:underline;">FB Dev Docs <i class="fa fa-external-link"></i></a>. And test your markup on <a href="https://developers.facebook.com/tools/debug/" target="_blank" style="font-style:italic; text-decoration:underline;">Sharing Debugger <i class="fa fa-external-link"></i></a>.';
                            echo set_input_form2('text', 'fb_app_id', (lang('FB App ID', $translation)), $data, $errors, false, $config);
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
    <!-- autosize -->
    @include('_form_element.autosize.script')
    <!-- jQuery Tags Input -->
    @include('_form_element.tagsinput.script')
@endsection