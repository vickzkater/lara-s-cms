@extends('_template_adm.master')

@php
   $pagetitle = 'Profile'; 
@endphp

@section('title', $pagetitle)

@section('content')
<div class="">
    <!-- message info -->
    @include('_template_adm.message')

    <div class="page-title">
        <div class="title_left">
            <h3>My {{ $pagetitle }}</h3>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Profile Details</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <br />
                    <form data-parsley-validate class="form-horizontal form-label-left" action="{{ route('admin_profile_edit') }}" method="POST">
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
                                <input type="text" id="username" required="required" class="form-control col-md-7 col-xs-12" value="<?php $iname = 'username'; if(isset($data->$iname)){ echo $data->$iname; }else{ echo old($iname); } ?>" readonly>
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

                        <div class="ln_solid"></div>
                        @php
                            $input_name = 'current_pass';
                            $bad_item = '';
                            if($errors->has($input_name)){
                                $bad_item = 'bad item';
                            }
                        @endphp
                        <div class="form-group {{ $bad_item }}">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="current-password">Current Password</label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <input type="password" id="current-password" class="form-control col-md-7 col-xs-12" name="current_pass" autocomplete="off" placeholder="input here if want to change password">
                                @if($errors->has($input_name))
                                    <div class="text-danger">
                                        {{ $errors->first($input_name) }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        @php
                            $input_name = 'password';
                            $bad_item = '';
                            if($errors->has($input_name)){
                                $bad_item = 'bad item';
                            }
                        @endphp
                        <div class="form-group {{ $bad_item }}">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="new-password">New Password</label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <input type="password" id="new-password" class="form-control col-md-7 col-xs-12" name="password" autocomplete="off" placeholder="input here if want to change password">
                                @if($errors->has($input_name))
                                    <div class="text-danger">
                                        {{ $errors->first($input_name) }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="confirm-password">Confirm Password</label>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <input type="password" id="confirm-password" class="form-control col-md-7 col-xs-12" name="password_confirmation" autocomplete="off" placeholder="input here if want to change password">
                            </div>
                        </div>
                        
                        <div class="ln_solid"></div>
                        <div class="form-group">
                            <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                <button type="submit" class="btn btn-success">Save Changes</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection