@extends('_template_adm.master')

@php
$pagetitle = 'User Manager'; 
if(isset($data)){
    $pagetitle .= ' (Edit)';
    $link = route('admin_user_do_edit', $data->id);
}else {
    $pagetitle .= ' (Add New)';
    $link = route('admin_user_do_create');
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
                            $input_name = 'name';
                            $bad_item = '';
                            if($errors->has($input_name)){
                                $bad_item = 'bad item';
                            }
                        @endphp
                        <div class="form-group {{ $bad_item }}">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Name <span class="required">*</span></label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <input type="text" id="name" required="required" class="form-control col-md-7 col-xs-12" name="name" value="<?php $iname = 'name'; if(isset($data->$iname)){ echo $data->$iname; }else{ echo old($iname); } ?>">
                                @if($errors->has($input_name))
                                    <div class="text-danger">
                                        {{ $errors->first($input_name) }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        @php
                            $input_name = 'username';
                            $bad_item = '';
                            if($errors->has($input_name)){
                                $bad_item = 'bad item';
                            }
                        @endphp
                        <div class="form-group {{ $bad_item }}">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="username">Username <span class="required">*</span></label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <input type="text" id="username" required="required" class="form-control col-md-7 col-xs-12" value="<?php $iname = 'username'; if(isset($data->$iname)){ echo $data->$iname; }else{ echo old($iname); } ?>" <?php if(isset($data->$iname)){ echo 'readonly'; }else{ echo 'name="username"'; } ?>>
                                @if($errors->has($input_name))
                                    <div class="text-danger">
                                        {{ $errors->first($input_name) }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        @php
                            $input_name = 'email';
                            $bad_item = '';
                            if($errors->has($input_name)){
                                $bad_item = 'bad item';
                            }
                        @endphp
                        <div class="form-group {{ $bad_item }}">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="email">Email <span class="required">*</span></label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <input type="email" id="email" required="required" class="form-control col-md-7 col-xs-12" name="email" value="<?php $iname = 'email'; if(isset($data->$iname)){ echo $data->$iname; }else{ echo old($iname); } ?>">
                                @if($errors->has($input_name))
                                    <div class="text-danger">
                                        {{ $errors->first($input_name) }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        @php
                        // usergroup
                        $input_name = 'usergroup'; 
                        $bad_item = '';
                        if($errors->has($input_name)){
                            $bad_item = 'bad item';
                        }
                        @endphp
                        <div class="form-group {{ $bad_item }}">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="{{ $input_name }}">Usergroup <span class="required">*</span></label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <select id="{{ $input_name }}" name="{{ $input_name }}" class="form-control select2" required>
                                    <option value="" disabled selected>- Please Choose One -</option>
                                    @if (isset($usergroups))
                                        @foreach ($usergroups as $item)
                                        @php
                                            $stats = '';
                                            if($item->id == old($input_name)){
                                                $stats = 'selected';
                                            }elseif(isset($data) && $item->id == $data->$input_name){
                                                $stats = 'selected';
                                            }
                                        @endphp
                                        <option value="{{ $item->id }}" {{ $stats }}>{{ $item->name }}</option>
                                        @endforeach
                                    @else
                                    <option value="" disabled>NO DATA AVAILABLE</option>
                                    @endif
                                </select>
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
                                        $checked = '';
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
                        @if (isset($data))
                            <div class="form-group">
                                <label class="control-label col-md-6 col-sm-6 col-xs-12">
                                    <i class="fa fa-info-circle"></i> Input fields below to change password or leave it blank
                                </label>
                            </div>
                        @endif
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="new-password"><?php if(isset($data)){ echo 'New '; } ?>Password</label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <input type="password" id="new-password" class="form-control col-md-7 col-xs-12" name="password" autocomplete="off" placeholder="min 8 chars - combination of alphabet, number, special chars (!?_-.)" <?php if(!isset($data)){ echo 'required="required"'; } ?>>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="confirm-password">Confirm Password</label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <input type="password" id="confirm-password" class="form-control col-md-7 col-xs-12" name="password_confirmation" autocomplete="off" placeholder="must input same as password above" <?php if(!isset($data)){ echo 'required="required"'; } ?>>
                            </div>
                        </div>
                        
                        <div class="ln_solid"></div>
                        <div class="form-group">
                            <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i>&nbsp; Submit</button>
                                <a href="{{ route('admin_user_manager') }}" class="btn btn-danger"><i class="fa fa-times"></i>&nbsp; Cancel</a>
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
    <!-- Select2 -->
    <link href="{{ asset('/admin/vendors/select2/dist/css/select2.min.css') }}" rel="stylesheet">
@endsection

@section('script')
    <!-- Select2 -->
    <script src="{{ asset('/admin/vendors/select2/dist/js/select2.min.js') }}"></script>

    <script>
    // Initialize Select2
    $('.select2').select2();
    </script>
@endsection