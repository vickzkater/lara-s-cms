@extends('_template_adm.master')

@php
$pagetitle = 'Brand'; 
if(isset($data)){
    $pagetitle .= ' (Edit)';
    $link = route('admin_brand_do_edit', $data->id);
}else {
    $pagetitle .= ' (Add New)';
    $link = route('admin_brand_do_create');
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
                            $input_name = 'name';
                            $bad_item = '';
                            if($errors->has($input_name)){
                                $bad_item = 'bad item';
                            }
                        @endphp
                        <div class="form-group {{ $bad_item }}">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Name <span class="required">*</span></label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <input type="text" id="name" required="required" class="form-control col-md-7 col-xs-12" name="name" value="<?php $iname = 'name'; if(isset($data->$iname)){ echo $data->$iname; }else{ echo old($iname); } ?>" placeholder="Brand Name" autocomplete="off">
                                @if($errors->has($input_name))
                                    <div class="text-danger">
                                        {{ $errors->first($input_name) }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        @php
                            $input_name = 'status';
                            $bad_item = '';
                            if($errors->has($input_name)){
                                $bad_item = 'bad item';
                            }
                        @endphp
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Status <span class="required">*</span></label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                @php
                                $checked = 'checked';
                                if(isset($data->status) && $data->status === 0){ 
                                    $checked = '';
                                }elseif(old('status') === 0){ 
                                    $checked = ''; 
                                }
                                @endphp
                                <div class="">
                                    <label>
                                        <input type="checkbox" class="js-switch" name="status" value="1" {{ $checked }} />
                                    </label>
                                </div>
                                @if($errors->has($input_name))
                                    <div class="text-danger">
                                        {{ $errors->first($input_name) }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        
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
                                <a href="{{ route('admin_brand_list') }}" class="btn btn-danger"><i class="fa fa-times"></i>&nbsp; Cancel</a>
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
@endsection

@section('script')
    <!-- iCheck -->
    <script src="{{ asset('/admin/vendors/iCheck/icheck.min.js') }}"></script>
    <!-- Switchery -->
    <script src="{{ asset('/admin/vendors/switchery/dist/switchery.min.js') }}"></script>
@endsection