@extends('_template_adm.master')

@php
$pagetitle = 'Customer'; 
if(isset($data)){
    $pagetitle .= ' (Edit)';
    $link = route('admin_customer_do_edit', $data->id);
}else {
    $pagetitle .= ' (Add New)';
    $link = route('admin_customer_do_create');
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
                    <form data-parsley-validate class="form-horizontal form-label-left" action="{{ $link }}" method="POST">
                        {{ csrf_field() }}

                        @php
                        // name
                        $field_name = 'Name';
                        $input_name = 'name';
                        $required = true;
                        $placeholder = 'Name';
                        $bad_item = '';
                        if($errors->has($input_name)){
                            $bad_item = 'bad item';
                        }
                        @endphp
                        <div class="form-group {{ $bad_item }}">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="{{ $input_name }}">{{ $field_name }} <?php if($required){ echo '<span class="required">*</span>'; } ?></label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <input type="text" id="{{ $input_name }}" <?php if($required){ echo 'required="required"'; } ?> class="form-control col-md-7 col-xs-12" name="{{ $input_name }}" value="<?php if(old($input_name)){ echo old($input_name); }elseif(isset($data->$input_name)){ echo $data->$input_name; } ?>" placeholder="{{ $placeholder }}">
                                @if($errors->has($input_name))
                                    <div class="text-danger">
                                        {{ $errors->first($input_name) }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        @php
                        // email
                        $field_name = 'Email';
                        $input_name = 'email';
                        $required = false;
                        $placeholder = 'username@domain.com';
                        $bad_item = '';
                        if($errors->has($input_name)){
                            $bad_item = 'bad item';
                        }
                        @endphp
                        <div class="form-group {{ $bad_item }}">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="{{ $input_name }}">{{ $field_name }} <?php if($required){ echo '<span class="required">*</span>'; } ?></label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <input type="email" id="{{ $input_name }}" <?php if($required){ echo 'required="required"'; } ?> class="form-control col-md-7 col-xs-12" name="{{ $input_name }}" value="<?php if(old($input_name)){ echo old($input_name); }elseif(isset($data->$input_name)){ echo $data->$input_name; } ?>" placeholder="{{ $placeholder }}">
                                @if($errors->has($input_name))
                                    <div class="text-danger">
                                        {{ $errors->first($input_name) }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        @php
                        // phone
                        $field_name = 'Phone';
                        $input_name = 'phone';
                        $required = true;
                        $placeholder = 'Only numeric';
                        $bad_item = '';
                        if($errors->has($input_name)){
                            $bad_item = 'bad item';
                        }
                        @endphp
                        <div class="form-group {{ $bad_item }}">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="{{ $input_name }}">{{ $field_name }} <?php if($required){ echo '<span class="required">*</span>'; } ?></label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <input type="number" id="{{ $input_name }}" <?php if($required){ echo 'required="required"'; } ?> class="form-control col-md-7 col-xs-12" name="{{ $input_name }}" value="<?php if(old($input_name)){ echo old($input_name); }elseif(isset($data->$input_name)){ echo $data->$input_name; } ?>" placeholder="{{ $placeholder }}">
                                @if($errors->has($input_name))
                                    <div class="text-danger">
                                        {{ $errors->first($input_name) }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        @php
                        // address
                        $field_name = 'Address';
                        $input_name = 'address';
                        $required = true;
                        $placeholder = 'Address';
                        $bad_item = '';
                        if($errors->has($input_name)){
                            $bad_item = 'bad item';
                        }
                        @endphp
                        <div class="form-group {{ $bad_item }}">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="{{ $input_name }}">{{ $field_name }} <?php if($required){ echo '<span class="required">*</span>'; } ?></label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <textarea id="{{ $input_name }}" <?php if($required){ echo 'required="required"'; } ?> class="form-control col-md-7 col-xs-12" name="{{ $input_name }}" placeholder="{{ $placeholder }}"><?php if(old($input_name)){ echo old($input_name); }elseif(isset($data->$input_name)){ echo $data->$input_name; } ?></textarea>
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
                                <div id="status" class="btn-group" data-toggle="buttons">
                                    @php
                                        $checked = 'checked';
                                        $active = '';
                                        if(isset($data->status) && $data->status==1){ 
                                            $checked = 'checked';
                                            $active = 'active focus';
                                        }elseif(old('status')===1){ 
                                            $checked = 'checked'; 
                                            $active = 'active focus';
                                        }
                                    @endphp
                                    <label class="btn btn-success {{ $active }}" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                                        <input type="radio" name="status" value="1" {{ $checked }}> &nbsp; <i class="fa fa-check"></i> Enable &nbsp;
                                    </label>
                                    @php
                                        $checked = '';
                                        $active = '';
                                        if(isset($data->status) && $data->status==0){ 
                                            $checked = 'checked';
                                            $active = 'active focus';
                                        }elseif(old('status')===0){ 
                                            $checked = 'checked'; 
                                            $active = 'active focus';
                                        }
                                    @endphp
                                    <label class="btn btn-default {{ $active }}" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                                        <input type="radio" name="status" value="0" {{ $checked }}> <i class="fa fa-times"></i> Disable
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
                                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i>&nbsp; Submit</button>
                                <a href="{{ route('admin_customer_list') }}" class="btn btn-danger"><i class="fa fa-times"></i>&nbsp; Cancel</a>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection