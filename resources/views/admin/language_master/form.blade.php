@extends('_template_adm.master')

@php
$pagetitle = 'Language Master'; 
if(isset($data)){
    $pagetitle .= ' (Edit)';
    $link = route('admin_langmaster_do_edit', $data->id);
}else {
    $pagetitle .= ' (New)';
    $link = route('admin_langmaster_do_create');
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
                            echo set_input_form('text', 'phrase', ucwords(lang('phrase', $translation)), $data, $errors, true, 'Sample: log in / edit / delete');
                            echo set_input_form('switch', 'status', 'Status', $data, $errors);
                        @endphp
                        
                        <div class="ln_solid"></div>
                        <div class="form-group">
                            <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                <input type="hidden" name="input_again" id="input_again" value="no" required />
                                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i>&nbsp; 
                                    @if (isset($data))
                                        {{ ucwords(lang('save', $translation)) }}
                                    @else
                                        {{ ucwords(lang('submit', $translation)) }}
                                    @endif
                                </button>
                                @if (!isset($data))
                                    <span class="btn btn-primary" onclick="saveThenAdd()"><i class="fa fa-save"></i>&nbsp; {{ ucwords(lang('add new again', $translation)) }}</span>
                                @endif
                                <a href="{{ route('admin_langmaster_list') }}" class="btn btn-danger"><i class="fa fa-times"></i>&nbsp; {{ ucwords(lang('cancel', $translation)) }}</a>
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
@endsection

@section('script')
   <!-- Switchery -->
    <script src="{{ asset('/admin/vendors/switchery/dist/switchery.min.js') }}"></script>

    <script>
    function saveThenAdd(){
        $('#input_again').val('yes');
        $('#form_data').submit();
    }
    </script>
@endsection