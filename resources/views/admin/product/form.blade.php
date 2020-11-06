@extends('_template_adm.master')

@php
    $pagetitle = ucwords(lang('product', $translation)); 
    if(isset($data)){
        $pagetitle .= ' ('.ucwords(lang('edit', $translation)).')';
        $link = route('admin.product.do_edit', $data->id);
    }else {
        $pagetitle .= ' ('.ucwords(lang('new', $translation)).')';
        $link = route('admin.product.do_create');
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
                        <form class="form-horizontal form-label-left" action="{{ $link }}" method="POST" enctype="multipart/form-data">
                            {{ csrf_field() }}

                            @php
                                // set_input_form2($type, $input_name, $label_name, $data, $errors, $required = false, $config = null)
                                $config = new \stdClass();
                                $config->attributes = 'autocomplete="off"';
                                $config->placeholder = lang("Sample: Men's Shoes (Product Category) + KINIDI (Brand) + Black Canvas (Info)", $translation);
                                echo set_input_form2('text', 'title', ucwords(lang('title', $translation)), $data, $errors, true, $config);

                                $config = new \stdClass();
                                $config->attributes = 'autocomplete="off"';
                                echo set_input_form2('text', 'subtitle', ucwords(lang('subtitle', $translation)), $data, $errors, true, $config);

                                $config = new \stdClass();
                                $config->info = 'Max 2MB per file';
                                if(isset($data)){
                                    // IF EDIT, THEN NOT REQUIRED
                                    $config->delete = true;
                                    echo set_input_form2('image', 'image', ucwords(lang('image', $translation)), $data, $errors, false, $config);
                                }else{
                                    echo set_input_form2('image', 'image', ucwords(lang('image', $translation)), $data, $errors, true, $config);
                                }

                                echo set_input_form2('textarea', 'description', ucwords(lang('description', $translation)), $data, $errors, true);

                                $config = new \stdClass();
                                $config->placeholder = 'dd/mm/yyyy';
                                echo set_input_form2('datepicker', 'purchase_date', ucwords(lang('purchase date', $translation)), $data, $errors, true, $config);

                                $config = new \stdClass();
                                $config->attributes = 'readonly';
                                $config->placeholder = 'dd/mm/yyyy';
                                echo set_input_form2('datepicker', 'expired_date', ucwords(lang('expired date', $translation)), $data, $errors, false, $config);

                                echo set_input_form2('number_format', 'qty', lang('QTY', $translation), $data, $errors, true);

                                $config = new \stdClass();
                                $config->info_text = '<i class="fa fa-info-circle"></i>&nbsp; Max 2MB per file';
                                if(isset($data)){
                                    $config->delete = true;
                                    echo set_input_form2('file', 'attachments', ucwords(lang('attachments', $translation)), $data, $errors, false, $config);
                                }else{
                                    echo set_input_form2('file', 'attachments', ucwords(lang('attachments', $translation)), $data, $errors, false, $config);
                                }

                                $config = new \stdClass();
                                $config->default = 'checked';
                                echo set_input_form2('switch', 'status', ucwords(lang('status', $translation)), $data, $errors, false, $config);
                            @endphp
                            
                            <div class="ln_solid"></div>

                            <div class="form-group">
                                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                    <button type="submit" class="btn btn-success" onclick="validate_form()"><i class="fa fa-save"></i>&nbsp; 
                                        @if (isset($data))
                                            {{ ucwords(lang('save', $translation)) }}
                                        @else
                                            {{ ucwords(lang('submit', $translation)) }}
                                        @endif
                                    </button>
                                    <a href="{{ route('admin.product.list') }}" class="btn btn-danger"><i class="fa fa-times"></i>&nbsp; {{ ucwords(lang('cancel', $translation)) }}</a>
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
    <!-- bootstrap-datetimepicker -->
    @include('_form_element.datetimepicker.css')
@endsection

@section('script')
    <!-- Switchery -->
    @include('_form_element.switchery.script')
    <!-- bootstrap-datetimepicker -->
    @include('_form_element.datetimepicker.script')
    <!-- TinyMCE -->
    @include('_form_element.tinymce.script')

    <script>
        // Initialize TinyMCE
        init_tinymce('#description');

        function validate_form() {
            if ($('#description').val() == '') {
                alert("{{ lang('#item field is required', $translation, ['#item'=>ucwords(lang('description', $translation))]) }}");
                scroll_to("#description", 5);
                return false;
            }
            return true;
        }

        function scroll_to(to, offset) {
            jQuery('html,body').animate({scrollTop: jQuery(to).offset().top - offset}, 400, 'swing');
        }
    </script>
@endsection