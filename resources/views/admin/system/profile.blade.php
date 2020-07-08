@extends('_template_adm.master')

@php
   $pagetitle = ucwords(lang('my profile', $translation)); 
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
                    <h2>{{ ucwords(lang('profile details', $translation)) }}</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <br />
                    <form class="form-horizontal form-label-left" action="{{ route('admin.profile.edit') }}" method="POST">
                        {{ csrf_field() }}

                        @php
                            // set_input_form2($type, $input_name, $label_name, $data, $errors, $required = false, $config = null)
                            $config = new \stdClass();
                            $config->attributes = 'autocomplete="off"';
                            echo set_input_form2('text', 'name', ucwords(lang('name', $translation)), $data, $errors, true, $config);

                            $config = new \stdClass();
                            $config->attributes = 'readonly';
                            $config->placeholder = lang('must be unique', $translation);
                            echo set_input_form2('word', 'username', ucwords(lang('username', $translation)), $data, $errors, true, $config);

                            $config = new \stdClass();
                            $config->attributes = 'autocomplete="off"';
                            $config->placeholder = 'username@domain.com';
                            echo set_input_form2('email', 'email', ucwords(lang('email', $translation)), $data, $errors, true, $config);
                        @endphp

                        <div class="ln_solid"></div>

                        @php
                            $config = new \stdClass();
                            $config->placeholder = lang('input here if want to change password', $translation);
                            echo set_input_form2('password', 'current_pass', ucwords(lang('current #item', $translation, ['#item' => lang('password', $translation)])), $data, $errors, false, $config);

                            $config = new \stdClass();
                            $config->placeholder = lang('input here if want to change password', $translation);
                            echo set_input_form2('password', 'password', ucwords(lang('new #item', $translation, ['#item' => lang('password', $translation)])), $data, $errors, false, $config);

                            $config = new \stdClass();
                            $config->placeholder = lang('input here if want to change password', $translation);
                            echo set_input_form2('password', 'password_confirmation', ucwords(lang('confirm #item', $translation, ['#item' => lang('password', $translation)])), $data, $errors, false, $config);
                        @endphp
                        
                        <div class="ln_solid"></div>

                        <div class="form-group">
                            <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i>&nbsp; {{ ucwords(lang('save changes', $translation)) }}</button>
                                <a href="{{ route('admin.logout.all') }}" class="btn btn-danger" onclick="return confirm('{{ lang('Are you sure to logout your account from all sessions?', $translation) }}')">
                                    {{ ucwords(lang('logout all sessions', $translation)) }}&nbsp; <i class="fa fa-sign-out"></i>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection