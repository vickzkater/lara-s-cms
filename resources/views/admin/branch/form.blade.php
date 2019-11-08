@extends('_template_adm.master')

@php
$pagetitle = 'Branch'; 
if(isset($data)){
    $pagetitle .= ' (Edit)';
    $link = route('admin_branch_do_edit', $data->id);
}else {
    $pagetitle .= ' (Add New)';
    $link = route('admin_branch_do_create');
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
                    <form data-parsley-validate class="form-horizontal form-label-left" action="{{ $link }}" method="POST" enctype="multipart/form-data">
                        {{ csrf_field() }}

                        @php
                            // set_input_form($type, $input_name, $label_name, $data, $errors, $required = false, $placeholder = null, $id_name = null, $value = null, $attributes = null, $defined_data = null, $options = [])
                            echo set_input_form('select2', 'division_id', 'Division', $data, $errors, true, ucwords(lang('please choose one', $translation)), null, null, null, $divisions, ['id', 'name']);
                            echo set_input_form('text', 'name', 'Name', $data, $errors, true, 'Branch Name', null, null, 'autocomplete="off"');
                            echo set_input_form('text', 'phone', 'Phone', $data, $errors, false, '6281234567890', null, null, 'autocomplete="off"');
                            echo set_input_form('textarea', 'location', 'Location', $data, $errors, false, 'Location ...', null, null, null);
                            echo set_input_form('switch', 'status', 'Status', $data, $errors);
                        @endphp
                        
                        <div class="ln_solid"></div>
                        <div class="form-group">
                            <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i>&nbsp; 
                                    @if (isset($data))
                                        Save Changes
                                    @else
                                        Submit
                                    @endif
                                </button>
                                <a href="{{ route('admin_branch_list') }}" class="btn btn-danger"><i class="fa fa-times"></i>&nbsp; Cancel</a>
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
    <link href="{{ asset('/admin/vendors/switchery/dist/switchery.min.css') }}" rel="stylesheet">
    <!-- Select2 -->
    <link href="{{ asset('/admin/vendors/select2/dist/css/select2.min.css') }}" rel="stylesheet">
@endsection

@section('script')
    <!-- Switchery -->
    <script src="{{ asset('/admin/vendors/switchery/dist/switchery.min.js') }}"></script>
    <!-- Select2 -->
    <script src="{{ asset('/admin/vendors/select2/dist/js/select2.min.js') }}"></script>

    <script>
    // Initialize Select2
    $('.select2').select2();
    </script>
@endsection