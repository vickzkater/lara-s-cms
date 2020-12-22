@extends('_template_adm.master')

@php
    $pagetitle = ucwords(lang('rule', $translation)); 
    if(isset($data)){
        $pagetitle .= ' ('.ucwords(lang('edit', $translation)).')';
        $link = route('admin.rule.do_edit', $data->id);
    }else {
        $pagetitle .= ' ('.ucwords(lang('new', $translation)).')';
        $link = route('admin.rule.do_create');
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
                        <form id="form_data" class="form-horizontal form-label-left" action="{{ $link }}" method="POST">
                            {{ csrf_field() }}

                            @php
                                // set_input_form2($type, $input_name, $label_name, $data, $errors, $required = false, $config = null)
                                $config = new \stdClass();
                                $config->placeholder = ucwords(lang('please choose one', $translation));
                                $config->defined_data = $modules;
                                $config->field_value = 'id';
                                $config->field_text = 'name';
                                echo set_input_form2('select2', 'module_id', ucwords(lang('module', $translation)), $data, $errors, true, $config);

                                $config = new \stdClass();
                                $config->attributes = 'autocomplete="off"';
                                $config->placeholder = 'Sample: Add New/Edit/Delete';
                                echo set_input_form2('text', 'name', ucwords(lang('name', $translation)), $data, $errors, true, $config);

                                echo set_input_form2('textarea', 'description', ucwords(lang('description', $translation)), $data, $errors, false);

                                if (empty($data)) {
                                    echo set_input_form('switch', 'packet', ucwords(lang('package', $translation)).' <i class="fa fa-info-circle" title="Add New | Edit | Delete | Restore | View List | View Details" data-toggle="tooltip" data-original-title="Add New | Edit | Delete | Restore | View List | View Details"></i>', $data, $errors, false, null, null, 'Add New|Edit|Delete|Restore|View List|View Details', 'onclick="using_packet()"', 'Add New|Edit|Delete|Restore|View List|View Details', ['unchecked', 'always']);
                                }

                                $config = new \stdClass();
                                $config->default = 'checked';
                                echo set_input_form2('switch', 'status', ucwords(lang('status', $translation)), $data, $errors, false, $config);
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
                                    <input type="hidden" name="input_again" id="input_again" value="no" required />
                                    @if (!isset($data))
                                        <span class="btn btn-primary" onclick="save_then_add()"><i class="fa fa-save"></i>&nbsp; {{ ucwords(lang('add new again', $translation)) }}</span>
                                    @endif
                                    <a href="{{ route('admin.rule.list') }}" class="btn btn-danger"><i class="fa fa-times"></i>&nbsp; 
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

        function save_then_add() {
            $('#input_again').val('yes');
            $('#form_data').submit();
        }

        function using_packet() {
            if($('#packet:checked').length > 0) {
                $('.vinput_name').hide();
                $('.vinput_description').hide();
                $('#name').attr('required', false);
            } else {
                $('.vinput_name').show();
                $('.vinput_description').show();
                $('#name').attr('required', true);
            }
        }
    </script>
@endsection