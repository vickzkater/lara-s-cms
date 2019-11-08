@extends('_template_adm.master')

@php
$pagetitle = 'Rule'; 
if(isset($data)){
    $pagetitle .= ' (Edit)';
    $link = route('admin_rule_do_edit', $data->id);
}else {
    $pagetitle .= ' (New)';
    $link = route('admin_rule_do_create');
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
                    <h2>Form Details</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <br />
                    <form id="form_data" data-parsley-validate class="form-horizontal form-label-left" action="{{ $link }}" method="POST" enctype="multipart/form-data">
                        {{ csrf_field() }}

                        @php
                            // set_input_form ($type, $input_name, $label_name, $data, $errors, $required = false, $placeholder = null, $id_name = null, $value = null, $attributes = null, $defined_data = null, $options = [])
                            echo set_input_form('select2', 'module_id', 'Module', $data, $errors, true, ucwords(lang('please choose one', $translation)), null, null, null, $modules, ['id', 'name']);
                            echo set_input_form('text', 'name', 'Name', $data, $errors, true, 'Rule Name, sample: Add New/Edit/View', null, null, 'autocomplete="off"');
                            echo set_input_form('textarea', 'description', 'Description', $data, $errors, false, 'Description ...', null, null);
                            if(empty($data))
                            {
                                echo set_input_form('switch', 'packet', 'Packet <i class="fa fa-info-circle" title="Add New | Edit | Delete | Restore | View List | View Details" data-toggle="tooltip" data-original-title="Add New | Edit | Delete | Restore | View List | View Details"></i>', $data, $errors, false, null, null, 'Add New|Edit|Delete|Restore|View List|View Details', 'onclick="using_packet()"', 'Add New|Edit|Delete|Restore|View List|View Details', ['unchecked', 'always']);
                            }
                            echo set_input_form('switch', 'status', 'Status', $data, $errors);
                        @endphp
                        
                        <div class="ln_solid"></div>
                        <div class="form-group">
                            <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                <input type="hidden" name="input_again" id="input_again" value="no" required />
                                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i>&nbsp; 
                                    @if (isset($data))
                                        Save
                                    @else
                                        Submit
                                    @endif
                                </button>
                                @if (!isset($data))
                                    <span class="btn btn-primary" onclick="saveThenAdd()"><i class="fa fa-save"></i>&nbsp; Add Again</span>
                                @endif
                                <a href="{{ route('admin_rule_list') }}" class="btn btn-danger"><i class="fa fa-times"></i>&nbsp; Cancel</a>
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
    <!-- iCheck -->
    <link href="{{ asset('/admin/vendors/iCheck/skins/flat/green.css') }}" rel="stylesheet">
    <!-- Switchery -->
    <link href="{{ asset('/admin/vendors/switchery/dist/switchery.min.css') }}" rel="stylesheet">
    <!-- Select2 -->
    <link href="{{ asset('/admin/vendors/select2/dist/css/select2.min.css') }}" rel="stylesheet">
@endsection

@section('script')
    <!-- iCheck -->
    <script src="{{ asset('/admin/vendors/iCheck/icheck.min.js') }}"></script>
    <!-- Switchery -->
    <script src="{{ asset('/admin/vendors/switchery/dist/switchery.min.js') }}"></script>
    <!-- Select2 -->
    <script src="{{ asset('/admin/vendors/select2/dist/js/select2.min.js') }}"></script>

    <script>
    // Initialize Select2
    $('.select2').select2();

    function saveThenAdd()
    {
        $('#input_again').val('yes');
        $('#form_data').submit();
    }

    function using_packet()
    {
        if($('#packet:checked').length > 0)
        {
            $('.vinput_name').hide();
            $('.vinput_description').hide();
            $('#name').attr('required', false);
        }
        else
        {
            $('.vinput_name').show();
            $('.vinput_description').show();
            $('#name').attr('required', true);
        }
    }
    </script>
@endsection