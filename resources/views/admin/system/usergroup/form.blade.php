@extends('_template_adm.master')

@php
    $pagetitle = ucwords(lang('admin group', $translation)); 
    if(isset($data)){
        $pagetitle .= ' ('.ucwords(lang('edit', $translation)).')';
        $link = route('admin.usergroup.do_edit', $data->id);
    }else {
        $pagetitle .= ' ('.ucwords(lang('new', $translation)).')';
        $link = route('admin.usergroup.do_create');
        $data = null;
    }

    // if add new, declare empty variables
    if(!isset($access)){
        $access = []; 
    }
    if(!isset($division_allowed)){
        $division_allowed = []; 
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
                                $config->attributes = 'autocomplete="off"';
                                echo set_input_form2('text', 'name', ucwords(lang('name', $translation)), $data, $errors, true, $config);

                                $config = new \stdClass();
                                $config->default = 'checked';
                                echo set_input_form2('switch', 'status', ucwords(lang('status', $translation)), $data, $errors, false, $config);
                            @endphp

                            {{-- Division --}}
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="division">{{ ucwords(lang('division', $translation)) }} <span class="required">*</span></label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    @if ($divisions)
                                        @php 
                                            $no = 1;
                                            $add_script_division = []; // sbg wadah simpan script utk centang check all per module
                                        @endphp
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" name="access_all_divisions" value="ALL" id="master_check_all_division">
                                                <b>*{{ strtoupper(lang('check all', $translation)) }}*</b>
                                            </label>
                                        </div>
                                        @foreach ($divisions as $key => $value)
                                            @if ($no == 1 || $no % 3 == 1)
                                                <div class="row">
                                            @endif
                                            <div class="col-md-4 col-sm-4 col-xs-12" style="margin-top:10px">
                                                <span class="label label-success">{{ $key }}</span>
                                                @if (count($value) > 0)
                                                    @php
                                                        $module_name = strtolower(str_replace(' ', '_', $key));
                                                        $access_ids = []; // save access per module
                                                        $access_checked = []; // save checked access per module
                                                    @endphp
                                                    <div class="checkbox">
                                                        <label>
                                                            <input type="checkbox" value="" class="access_division" id="check_all_division_{{ $module_name }}" onclick="check_all_division('division_{{ $module_name }}')">
                                                            *{{ ucwords(lang('check all', $translation)) }}
                                                        </label>
                                                    </div>
                                                    @foreach ($value as $item)
                                                        @php 
                                                            $access_ids[] = $item->id;
                                                            $stat = '';
                                                            if(count($division_allowed) > 0){
                                                                if(in_array($item->id, $division_allowed)){
                                                                    $stat = 'checked';
                                                                    $access_checked[] = $item->id;
                                                                }
                                                            }
                                                        @endphp
                                                        <div class="checkbox">
                                                            <label>
                                                                <input type="checkbox" name="branch[]" value="{{ $item->id }}" class="access_division division_{{ $module_name }}" onclick="is_all_division_checked('division_{{ $module_name }}')" {{ $stat }}>
                                                                {{ $item->name }}
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                    @php
                                                        if (count($access_checked) == count($access_ids)){
                                                            $add_script_division[] = '<script>$("#check_all_division_'.$module_name.'").attr("checked", true);</script>';
                                                        }
                                                    @endphp
                                                @endif
                                            </div>
                                            @php
                                                if($no % 3 == 0 || $no == count($divisions)){
                                                    echo '</div><br>';
                                                }
                                                $no++;
                                            @endphp
                                        @endforeach
                                    @else
                                        {{ lang('NO DIVISION and/or BRANCHES ARE AVAILABLE, please create a new division', $translation) }} <a href="{{ route('admin.division.create') }}"><u>{{ lang('here', $translation) }}</u></a> {{ lang('and a new branch', $translation) }} <a href="{{ route('admin.branch.create') }}"><u>{{ lang('here', $translation) }}</u></a>
                                    @endif
                                </div>
                            </div>
                        
                            {{-- Access --}}
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="access">{{ ucwords(lang('access', $translation)) }} <span class="required">*</span></label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    @if ($rules)
                                        @php 
                                            $no = 1;
                                            $add_script = []; // sbg wadah simpan script utk centang check all per module
                                        @endphp
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" name="access_all" value="ALL" id="master_check_all">
                                                <b>*{{ strtoupper(lang('check all', $translation)) }}*</b>
                                            </label>
                                        </div>
                                        @foreach ($rules as $key => $value)
                                            @if ($no == 1 || $no % 3 == 1)
                                                <div class="row">
                                            @endif
                                            <div class="col-md-4 col-sm-4 col-xs-12" style="margin-top:10px">
                                                <span class="label label-primary">{{ $key }}</span>
                                                @if (count($value) > 0)
                                                    @php
                                                        $module_name = strtolower(str_replace(' ', '_', $key));
                                                        $access_ids = []; // save access per module
                                                        $access_checked = []; // save checked access per module
                                                    @endphp
                                                    <div class="checkbox">
                                                        <label>
                                                            <input type="checkbox" value="" class="access_module" id="check_all_module_{{ $module_name }}" onclick="check_all_module('module_{{ $module_name }}')">
                                                            *{{ ucwords(lang('check all', $translation)) }}
                                                        </label>
                                                    </div>
                                                    @foreach ($value as $item)
                                                        @php 
                                                            $access_ids[] = $item->id;
                                                            $stat = '';
                                                            if(count($access) > 0){
                                                                if(in_array($item->id, $access)){
                                                                    $stat = 'checked';
                                                                    $access_checked[] = $item->id;
                                                                }
                                                            }
                                                        @endphp
                                                        <div class="checkbox">
                                                            <label>
                                                                <input type="checkbox" name="access[]" value="{{ $item->id }}" class="access_module module_{{ $module_name }}" onclick="is_all_checked('module_{{ $module_name }}')" {{ $stat }}>
                                                                {{ $item->name }}
                                                                @if ($item->description)
                                                                    &nbsp;<i class="fa fa-info-circle" title="{{ $item->description }}" data-toggle="tooltip" data-original-title="{{ $item->description }}"></i>
                                                                @endif
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                    @php
                                                    if (count($access_checked) == count($access_ids)){
                                                        $add_script[] = '<script>$("#check_all_module_'.$module_name.'").attr("checked", true);</script>';
                                                    }
                                                    @endphp
                                                @endif
                                            </div>
                                            @php
                                                if($no % 3 == 0 || $no == count($rules)){
                                                    echo '</div><br>';
                                                }
                                                $no++;
                                            @endphp
                                        @endforeach
                                    @else
                                        {{ lang('NO RULES AVAILABLE, please make a new rule', $translation) }} <a href="{{ route('admin.rule.create') }}"><u>{{ lang('here', $translation) }}</u></a>
                                    @endif
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
                                    <a href="{{ route('admin.usergroup.list') }}" class="btn btn-danger"><i class="fa fa-times"></i>&nbsp; 
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
@endsection

@section('script')
    <!-- Switchery -->
    @include('_form_element.switchery.script')
    
    @php
        // utk centang "check_all" per division
        if (isset($add_script_division) && count($add_script_division) > 0){
            echo implode(' ', $add_script_division);
        }
        // utk centang "check all" branch
        if(isset($add_script_division) && count($divisions) == count($add_script_division)){
            echo '<script>$("#master_check_all_division").attr("checked", true);</script>';
        }

        // utk centang "check_all" per module
        if (isset($add_script) && count($add_script) > 0){
            echo implode(' ', $add_script);
        }
        // utk centang "check all" access
        if(isset($add_script) && count($rules) == count($add_script)){
            echo '<script>$("#master_check_all").attr("checked", true);</script>';
        }
    @endphp

    <script>
        // Division
        $('#master_check_all_division').on("click", function() {
            var all = $('.access_division').length;
            var total = $('.access_division:checked').length;

            if(total == all && $('#master_check_all_division:checked').length == 0){
                $(".access_division").removeAttr("checked");
            }else{
                $(".access_division").prop("checked", "checked");
            }
        });

        function check_all_division(module_name) {
            var all = $('.'+module_name).length;
            var total = $('.'+module_name+':checked').length;

            if(total == all){
                $("."+module_name).removeAttr("checked");
                $("#master_check_all_division").removeAttr("checked");
            }else{
                $("."+module_name).prop("checked", "checked");
            }
        }

        function is_all_division_checked(module_name) {
            var all = $('.'+module_name).length;
            var total = $('.'+module_name+':checked').length;

            if(total == all){
                $("#check_all_"+module_name).prop("checked", "checked");
            }else{
                $("#check_all_"+module_name).removeAttr("checked");
                $("#master_check_all_division").removeAttr("checked");
            }
        }

        // Access
        $('#master_check_all').on("click", function() {
            var all = $('.access_module').length;
            var total = $('.access_module:checked').length;

            if(total == all && $('#master_check_all:checked').length == 0){
                $(".access_module").removeAttr("checked");
            }else{
                $(".access_module").prop("checked", "checked");
            }
        });

        function check_all_module(module_name) {
            var all = $('.'+module_name).length;
            var total = $('.'+module_name+':checked').length;

            if(total == all){
                $("."+module_name).removeAttr("checked");
                $("#master_check_all").removeAttr("checked");
            }else{
                $("."+module_name).prop("checked", "checked");
            }
        }

        function is_all_checked(module_name) {
            var all = $('.'+module_name).length;
            var total = $('.'+module_name+':checked').length;

            if(total == all){
                $("#check_all_"+module_name).prop("checked", "checked");
            }else{
                $("#check_all_"+module_name).removeAttr("checked");
                $("#master_check_all").removeAttr("checked");
            }
        }
    </script>
@endsection