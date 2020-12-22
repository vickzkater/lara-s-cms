@extends('_template_adm.master')

@php
    $pagetitle = ucwords(lang('language', $translation)); 
    if(isset($data)){
        $pagetitle .= ' ('.ucwords(lang('edit', $translation)).')';
        $link = route('admin.language.do_edit', $data->id);
    }else {
        $pagetitle .= ' ('.ucwords(lang('new', $translation)).')';
        $link = route('admin.language.do_create');
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
                                $config->placeholder = 'Sample: English / Indonesia';
                                echo set_input_form2('text', 'name', ucwords(lang('name', $translation)), $data, $errors, true, $config);

                                $config = new \stdClass();
                                $config->placeholder = 'Sample: EN / ID';
                                echo set_input_form2('text', 'alias', ucwords(lang('alias', $translation)), $data, $errors, true, $config);

                                $config = new \stdClass();
                                $config->default = 'checked';
                                echo set_input_form2('switch', 'status', ucwords(lang('status', $translation)), $data, $errors, false, $config);
                                
                                if($data){
                                    echo '<hr><center><h2>>> '.strtoupper(lang('master translation', $translation)).' <<</h2></center><hr>';
                                    if(isset($master_data)){
                                        $values = [];
                                        foreach ($master_data as $item) {
                                            $values[$item->id] = $item->translate;
                                        }
                                    }
                                    if(isset($master)){
                                        foreach ($master as $item) {
                                            $value = null;
                                            $empty = '<span class="label label-warning"><i class="fa fa-warning"></i></span>&nbsp; ';
                                            if(isset($values[$item->id])){
                                                $value = $values[$item->id];
                                                $empty = '';
                                            }
                                            // set_input_form ($type, $input_name, $label_name, $data, $errors, $required = false, $placeholder = null, $id_name = null, $value = null, $attributes = null, $defined_data = null, $options = [])
                                            echo set_input_form('text', 'translate['.$item->id.']', $empty.$item->phrase, $data, $errors, false, 'input translation here', 'translate_'.$item->id, $value);
                                        }
                                    }
                                }
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
                                    <a href="{{ route('admin.language.list') }}" class="btn btn-danger"><i class="fa fa-times"></i>&nbsp; 
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
@endsection