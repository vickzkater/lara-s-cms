{{-- ADD HTML SMALL MODAL [BEGIN] --}}
@extends('_template_adm.modal_small')
{{-- SMALL MODAL CONFIG --}}
@section('small_modal_title', ucwords(lang('add #item', $translation, ['#item'=>ucwords(lang('content element', $translation))])))
@section('small_modal_content')
    <span class="btn btn-primary btn-block" onclick="add_content_element('text', true)"><i class="fa fa-font"></i>&nbsp; {{ ucwords(lang('add #item', $translation, ['#item'=>ucwords(lang('text', $translation))])) }}</span><br>
    <span class="btn btn-primary btn-block" onclick="add_content_element('image', true)"><i class="fa fa-image"></i>&nbsp; {{ ucwords(lang('add #item', $translation, ['#item'=>ucwords(lang('image', $translation))])) }}</span><br>
    <span class="btn btn-primary btn-block" onclick="add_content_element('image & text', true)"><i class="fa fa-image"></i>&nbsp; {{ ucwords(lang('add #item', $translation, ['#item'=>ucwords(lang('image & text', $translation))])) }}</span><br>
    <span class="btn btn-primary btn-block" onclick="add_content_element('video', true)"><i class="fa fa-youtube-play"></i>&nbsp; {{ ucwords(lang('add #item', $translation, ['#item'=>ucwords(lang('video', $translation))])) }}</span><br>
    <span class="btn btn-primary btn-block" onclick="add_content_element('video & text', true)"><i class="fa fa-youtube-play"></i>&nbsp; {{ ucwords(lang('add #item', $translation, ['#item'=>ucwords(lang('video & text', $translation))])) }}</span><br>
    <span class="btn btn-primary btn-block" onclick="add_content_element('plain text', true)"><i class="fa fa-font"></i>&nbsp; {{ ucwords(lang('add #item', $translation, ['#item'=>ucwords(lang('plain text', $translation))])) }}</span>
@endsection
{{-- ADD HTML SMALL MODAL [END] --}}

@extends('_template_adm.master')

@php
    $pagetitle = ucwords(lang('article', $translation)); 
    if(isset($data)){
        $pagetitle .= ' ('.ucwords(lang('edit', $translation)).')';
        $link = route('admin.article.do_edit', $data->id);
    }else {
        $data = null;
        $pagetitle .= ' ('.ucwords(lang('add new', $translation)).')';
        $link = route('admin.article.do_create');
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
                        <form data-parsley-validate class="form-horizontal form-label-left" action="{{ $link }}" method="POST" enctype="multipart/form-data">
                            {{ csrf_field() }}

                            @php
                                // set_input_form2($type, $input_name, $label_name, $data, $errors, $required = false, $config = null)
                                $config = new \stdClass();
                                $config->attributes = 'autocomplete="off"';
                                echo set_input_form2('text', 'title', ucwords(lang('title', $translation)), $data, $errors, true, $config);

                                $config = new \stdClass();
                                $config->attributes = 'autocomplete="off"';
                                $config->placeholder = lang('Must be unique. If left empty, system will auto-generate this.', $translation);
                                echo set_input_form2('text', 'slug', ucwords(lang('slug', $translation)), $data, $errors, false, $config);

                                $config = new \stdClass();
                                $config->info = '<i class="fa fa-info-circle"></i>&nbsp; '.lang('The image will be cropped & resized to #widthx#height px as a thumbnail image', $translation, ['#width'=>750, '#height'=>300]);
                                if(isset($data)){
                                    // EDIT - not required
                                    echo set_input_form2('image', 'thumbnail', ucwords(lang('thumbnail', $translation)), $data, $errors, false, $config);
                                }else{
                                    echo set_input_form2('image', 'thumbnail', ucwords(lang('thumbnail', $translation)), $data, $errors, true, $config);
                                }
                                
                                $config = new \stdClass();
                                $config->info_text = '<i class="fa fa-info-circle"></i>&nbsp; '.lang('separate with commas', $translation);
                                echo set_input_form2('tags', 'keywords', ucwords(lang('keywords', $translation)), $data, $errors, false, $config);

                                echo set_input_form2('textarea', 'summary', ucwords(lang('summary', $translation)), $data, $errors, true);
                            @endphp

                            @if ($topics)
                                <div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">{{  ucwords(lang('topic', $translation)) }} <span class="required">*</span></label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" name="access_all" value="ALL" id="master_check_all">
                                                <b>*{{ strtoupper(lang('check all', $translation)) }}*</b>
                                            </label>
                                        </div>
                                        @php
                                            $no = 1;
                                            $add_script = []; // sbg wadah simpan script utk centang semua

                                            $selected_topics = [];
                                            if(isset($data) && !empty($data->topic)){
                                                $selected_topics = explode(',', $data->topic);
                                            }
                                        @endphp
                                        @foreach ($topics as $item)
                                            @if ($no == 1 || $no % 3 == 1)
                                                <div class="col-md-4 col-sm-4 col-xs-12" style="margin-top:10px">
                                            @endif

                                            @php
                                                $checked_stat = '';
                                                if(in_array($item->id, $selected_topics)) {
                                                    $checked_stat = 'checked';
                                                }
                                            @endphp

                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" name="topic[]" value="{{ $item->id }}" class="check_topic" onclick="is_all_checked()" {{ $checked_stat }}>
                                                    {{ $item->name }}
                                                    @if ($item->description)
                                                        &nbsp;<i class="fa fa-info-circle" title="{{ $item->description }}" data-toggle="tooltip" data-original-title="{{ $item->description }}"></i>
                                                    @endif
                                                </label>
                                            </div>

                                            @if ($no % 3 == 0 || $no == count($topics))
                                                </div>
                                            @endif

                                            @php
                                                $no++;
                                            @endphp
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @php
                                $config = new \stdClass();
                                $config->attributes = 'autocomplete="off"';
                                echo set_input_form2('text', 'author', ucwords(lang('author', $translation)), $data, $errors, false, $config);

                                $config = new \stdClass();
                                $config->attributes = 'readonly';
                                $config->placeholder = 'dd/mm/yyyy';
                                echo set_input_form2('datepicker', 'posted_at', ucwords(lang('posted date', $translation)), $data, $errors, false, $config);

                                echo set_input_form2('switch', 'status', ucwords(lang('published', $translation)), $data, $errors, false);
                            @endphp

                            <div class="ln_solid"></div>

                            <h2>{{ ucwords(lang('article content', $translation)) }}</h2>

                            <div class="row">
                                <div class="col-lg-12">
                                    <div id="content-pagebuilder" role="tablist" aria-multiselectable="true" class="accordion"></div>
                                    <hr>
                                    <span class="btn btn-primary" data-toggle="modal" data-target=".bs-modal-sm">
                                        <i class="fa fa-plus-circle"></i> {{ ucwords(lang('add #item', $translation, ['#item'=>ucwords(lang('content element', $translation))])) }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="ln_solid"></div>

                            <div class="form-group">
                                <div class="col-lg-12 text-center">
                                    <button type="submit" class="btn btn-success btn-lg"><i class="fa fa-save"></i>&nbsp; 
                                        @if (isset($data))
                                            {{ ucwords(lang('save changes', $translation)) }}
                                        @else
                                            {{ ucwords(lang('submit', $translation)) }}
                                        @endif
                                    </button>
                                    &nbsp;&nbsp;
                                    <a href="{{ route('admin.article.list') }}" class="btn btn-danger btn-lg"><i class="fa fa-times"></i>&nbsp; 
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
    <!-- bootstrap-datetimepicker -->
    @include('_form_element.datetimepicker.css')
    <!-- Page Builder by KINIDI Tech -->
    @include('_form_element.pagebuilder.css')
@endsection

@section('script')
    <!-- Switchery -->
    @include('_form_element.switchery.script')
    <!-- bootstrap-datetimepicker -->
    @include('_form_element.datetimepicker.script')
    <!-- jQuery Tags Input -->
    @include('_form_element.tagsinput.script')
    <!-- TinyMCE -->
    @include('_form_element.tinymce.script')
    <!-- Page Builder by KINIDI Tech -->
    @include('_form_element.pagebuilder.script')

    <script>
        // SET DEFAULT IMAGE FOR "NO IMAGE"
        var pagebuilder_no_img = "{{ asset('admin/vendors/kiniditech-pagebuilder/no-image.png') }}";

        $('#master_check_all').on("click", function() {
            var all = $('.check_topic').length;
            var total = $('.check_topic:checked').length;

            if(total == all && $('#master_check_all:checked').length == 0){
                $(".check_topic").removeAttr("checked");
            }else{
                $(".check_topic").prop("checked", "checked");
            }
        });

        function is_all_checked() {
            var all = $('.check_topic').length;
            var total = $('.check_topic:checked').length;

            if(total == all){
                $("#master_check_all").prop("checked", "checked");
            }else{
                $("#master_check_all").removeAttr("checked");
            }
        }

        @if (isset($data))
            is_all_checked();

            @if (!empty($data->content))
                @php
                    $content = json_decode($data->content);
                @endphp
                @foreach ($content as $key => $value)
                    @if ($value->type == 'text')
                        @php
                            $pb_text = json_encode($value->text);
                            $pb_text = substr($pb_text, 1, strlen($pb_text)-2);
                        @endphp
                        var pb_text = encodeURI("{{ $pb_text }}");
                        // SET JSON OBJECT DATA
                        var the_data = { 
                            section: "{{ $value->section }}", 
                            text: pb_text 
                        };
                        add_content_element('text', true, {{ $key }}, the_data);
                    @elseif ($value->type == 'image')
                        @php
                            $pb_image = asset($value->image);
                        @endphp
                        var pb_image = encodeURI("{{ $pb_image }}");
                        // SET JSON OBJECT DATA
                        var the_data = { 
                            section: "{{ $value->section }}", 
                            image: pb_image 
                        };
                        add_content_element('image', true, {{ $key }}, the_data);
                    @elseif ($value->type == 'image & text')
                        @php
                            $pb_image = asset($value->image);
                            $pb_text = json_encode($value->text);
                            $pb_text = substr($pb_text, 1, strlen($pb_text)-2);
                        @endphp
                        var pb_image = encodeURI("{{ $pb_image }}");
                        var pb_text = encodeURI("{{ $pb_text }}");
                        // SET JSON OBJECT DATA
                        var the_data = { 
                            section: "{{ $value->section }}", 
                            image: pb_image,
                            text: pb_text,
                            text_position: "{{ $value->text_position }}"
                        };
                        add_content_element('image & text', true, {{ $key }}, the_data);
                    @elseif ($value->type == 'video')
                        @php
                            $pb_video = $value->video;
                        @endphp
                        var pb_video = encodeURI("{{ $pb_video }}");
                        // SET JSON OBJECT DATA
                        var the_data = { 
                            section: "{{ $value->section }}", 
                            video: pb_video 
                        };
                        add_content_element('video', true, {{ $key }}, the_data);
                    @elseif ($value->type == 'video & text')
                        @php
                            $pb_video = $value->video;
                            $pb_text = json_encode($value->text);
                            $pb_text = substr($pb_text, 1, strlen($pb_text)-2);
                        @endphp
                        var pb_video = encodeURI("{{ $pb_video }}");
                        var pb_text = encodeURI("{{ $pb_text }}");
                        // SET JSON OBJECT DATA
                        var the_data = { 
                            section: "{{ $value->section }}", 
                            video: pb_video,
                            text: pb_text,
                            text_position: "{{ $value->text_position }}"
                        };
                        add_content_element('video & text', true, {{ $key }}, the_data);
                    @elseif ($value->type == 'plain text')
                        @php
                            $pb_text = json_encode($value->text);
                            $pb_text = substr($pb_text, 1, strlen($pb_text)-2);
                        @endphp
                        var pb_text = encodeURI("{{ $pb_text }}");
                        // SET JSON OBJECT DATA
                        var the_data = { 
                            section: "{{ $value->section }}", 
                            text: pb_text 
                        };
                        add_content_element('plain text', true, {{ $key }}, the_data);
                    @endif
                @endforeach
            @endif
        @endif
    </script>
@endsection