@extends('_template_adm.master')

@php
    $pagetitle = ucwords(lang('admin', $translation)); 
    if(isset($data)){
        $pagetitle .= ' ('.ucwords(lang('edit', $translation)).')';
        $link = route('admin.user.do_edit', $data->id);
    }else {
        $pagetitle .= ' ('.ucwords(lang('new', $translation)).')';
        $link = route('admin.user.do_create');
        $data = null;
    }
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
                        <form class="form-horizontal form-label-left" action="{{ $link }}" method="POST">
                            {{ csrf_field() }}

                            @php
                                // set_input_form2($type, $input_name, $label_name, $data, $errors, $required = false, $config = null)
                                $config = new \stdClass();
                                $config->attributes = 'autocomplete="off"';
                                echo set_input_form2('text', 'name', ucwords(lang('name', $translation)), $data, $errors, true, $config);

                                $config = new \stdClass();
                                $config->attributes = 'autocomplete="off"';
                                $config->placeholder = lang('must be unique', $translation);
                                echo set_input_form2('word', 'username', ucwords(lang('username', $translation)), $data, $errors, true, $config);

                                $config = new \stdClass();
                                $config->attributes = 'autocomplete="off"';
                                $config->placeholder = 'username@domain.com';
                                echo set_input_form2('email', 'email', ucwords(lang('email', $translation)), $data, $errors, true, $config);

                                $config = new \stdClass();
                                $config->placeholder = '- '.ucwords(lang('please choose one', $translation)).' -';
                                $config->defined_data = $usergroups;
                                $config->field_value = 'id';
                                $config->field_text = 'name';
                                echo set_input_form2('select2', 'usergroup', ucwords(lang('usergroup', $translation)), $data, $errors, true, $config);

                                $config = new \stdClass();
                                $config->default = 'checked';
                                echo set_input_form2('switch', 'status', ucwords(lang('status', $translation)), $data, $errors, false, $config);
                            @endphp

                            <div class="ln_solid"></div>

                            @if (isset($data))
                                <div class="form-group">
                                    <label class="control-label col-md-6 col-sm-6 col-xs-12">
                                        <i class="fa fa-info-circle"></i> {{ lang('Input fields below to change password or leave it blank', $translation) }}
                                    </label>
                                </div>
                            @endif
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="new-password">
                                    {{ ucwords(lang('new #item', $translation, ['#item' => ucwords(lang('password', $translation))])) }}
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="password" id="new-password" class="form-control col-md-7 col-xs-12" name="password" autocomplete="off" placeholder="{{ lang('Recommendations: at least 8 characters & use a combination of alphabet, numbers and special characters (!?_-.)', $translation) }}" <?php if(!isset($data)){ echo 'required="required"'; } ?>>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="confirm-password">
                                    {{ ucwords(lang('confirm #item', $translation, ['#item' => ucwords(lang('password', $translation))])) }}
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="password" id="confirm-password" class="form-control col-md-7 col-xs-12" name="password_confirmation" autocomplete="off" placeholder="{{ lang('must be the same as the password above', $translation) }}" <?php if(!isset($data)){ echo 'required="required"'; } ?>>
                                </div>
                            </div>
                            
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
                                    <a href="{{ route('admin.user.list') }}" class="btn btn-danger"><i class="fa fa-times"></i>&nbsp; 
                                        @if (isset($data))
                                            {{ ucwords(lang('close', $translation)) }}
                                        @else
                                            {{ ucwords(lang('cancel', $translation)) }}
                                        @endif
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

@section('css')
    <!-- Switchery -->
    @include('_form_element.switchery.css')
    <!-- Select2 -->
    @include('_form_element.select2.css')
@endsection

@section('script')
    <!-- Switchery -->
    @include('_form_element.switchery.script')
    <!-- Select2 -->
    @include('_form_element.select2.script')

    <script>
        // Initialize Select2
        $('.select2').select2();
    </script>
@endsection