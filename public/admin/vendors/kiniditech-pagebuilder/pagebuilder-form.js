/**
 * PageBuilder - Form (Build form pages using content elements)
 * Version: 1.0.0 (2020-06-10)
 *
 * Copyright (c) KINIDI Tech and other contributors
 * Released under the MIT license.
 * For more information, see https://kiniditech.com/ or https://github.com/vickzkater
 *
 * PageBuilder depends on several libraries, such as:
 * - jQuery (https://jquery.com/download/)
 * - jQuery UI (https://jqueryui.com/download/)
 * - TinyMCE (https://www.tiny.cloud/get-tiny/downloads/)
 * - Bootstrap (https://getbootstrap.com/docs/4.3/getting-started/download/)
 *
 * Support Form Elements:
 * - Multiple Choice (Text)
 * - Multiple Choice (Image)
 * - Checkboxes (Text)
 * - Dropdown
 * - Linear Scale
 */

function add_question(type, collapsed = false, identifier = 0, data = '', config = '', options = '', required = false) {
  var html_content_element = '';

  switch (type) {
      case 'multiple_choice_text':
          html_content_element = set_html_multiple_choice_text(collapsed, identifier, data, config, options, required);
          break;
      case 'multiple_choice_image':
          html_content_element = set_html_multiple_choice_image(collapsed, identifier, data, config, options, required);
          break;
      case 'checkboxes_text':
          html_content_element = set_html_checkboxes_text(collapsed, identifier, data, config, options, required);
          break;
      case 'drop-down':
          html_content_element = set_html_dropdown(collapsed, identifier, data, config, options, required);
          break;
      case 'linear_scale':
          html_content_element = set_html_linear_scale(collapsed, identifier, data, config, options, required);
          break;
  }

  $(content_container).append(html_content_element);

  // initialize sortable
  $(".sortable-option").sortable();
}

function show_question_element_media(id) {

  var media_elm = $('#question_media_' + id).val();

  $('.media-question-' + id).hide();

  if (media_elm == 'youtube') {
      $('#media-question-youtube-' + id).show();
  } else if (media_elm == 'image') {
      $('#media-question-image-' + id).show();
  }
}

function delete_option(id) {
  if (confirm("Are you sure to delete this option (this action can't be undone) ?")) {
      $('#v_option_' + id).remove();
      return true;
  }
  return false;
}

function add_multiple_option_text(id) {
  var uniqid = Date.now();
  // GET TOTAL OPTIONS
  var total_opt = document.getElementsByClassName('option-' + id).length;
  var next_opt = parseInt(total_opt) + 1;

  var html = '';
  html += '<div class="form-group option-' + id + '" id="v_option_' + uniqid + '" style="border: 2px dashed black; margin:5px; padding:10px;">';
  html += '<label class="control-label col-md-3 col-sm-3 col-xs-12"><i class="fa fa-sort"></i> Option <span class="required">*</span></label>';
  html += '<div class="col-md-6 col-sm-6 col-xs-12">';
  html += '<input type="text" autocomplete="off" value="" name="v_opt[' + id + '][]" required="required" class="form-control col-md-11 col-xs-12" placeholder="Option ' + next_opt + '">';
  html += '<i class="fa fa-trash" style="color:red; margin-left:20px; margin-top:10px;" onclick="delete_option(' + uniqid + ')"></i>';
  html += '</div>';
  html += '</div>';

  $("#list-option-" + id).append(html);
}

function add_multiple_option_image(id) {
  var uniqid = Date.now();
  var default_value = '{{ asset("/images/no-image.png") }}';
  var html = '';
  html += '<div class="form-group option-' + id + '" id="v_option_' + uniqid + '" style="border: 2px dashed black; margin:5px; padding:10px;">';
  html += '<label class="control-label col-md-3 col-sm-3 col-xs-12"><i class="fa fa-sort"></i> Option <span class="required">*</span></label>';
  html += '<div class="col-md-6 col-sm-6 col-xs-12">';
  html += '<img src="' + default_value + '" style="max-width: 200px;max-height: 200px;display: block;">';
  html += '<input type="file" name="v_opt[' + id + '][]" required="required" class="form-control col-md-7 col-xs-12" accept=".jpg, .jpeg, .png" onchange="readURL(this, \'before\');" style="margin-top:5px">';
  html += '<i class="fa fa-trash" style="color:red; margin-left:20px; margin-top:10px;" onclick="delete_option(' + uniqid + ')"></i>';
  html += '</div>';
  html += '</div>';

  $("#list-option-" + id).append(html);
}

// SET CONTENT ELEMENT BELOW *********

function set_html_multiple_choice_text(collapsed, identifier, data, config, options, required) {
  if (identifier == 0) {
      var uniqid = Date.now();
  } else {
      var uniqid = identifier;
  }
  var default_value = '{{ asset("/images/no-image.png") }}';

  var question_text = '';
  var question_media = '';
  var select_image = '';
  var select_youtube = '';
  var question_src = '';
  var question_src_image = default_value;
  var question_src_youtube = '';
  var points_per_item = '';
  var is_required_yes = '';
  var is_required_no = 'selected';
  if (data != '') {
      data = JSON.parse(data);
      question_text = data.question_text;
      question_media = data.question_media;
      question_src = data.question_src;
      if (question_media == 'image') {
          select_image = 'selected';
          question_src_image = '{{ asset("uploads/mission") }}/' + question_src;
      } else if (question_media == 'youtube') {
          select_youtube = 'selected';
          question_src_youtube = question_src;
      }
      points_per_item = data.points_per_item;
      is_required = data.is_required;
      if (is_required == 1) {
          is_required_yes = 'selected';
          is_required_no = '';
      }
  }

  var opt_other_yes = '';
  var opt_other_no = 'selected';
  if (config != '') {
      config = JSON.parse(config);
      opt_other = config.opt_other;
      if (opt_other == 1) {
          opt_other_yes = 'selected';
          opt_other_no = '';
      }
  }

  var html = '<div class="panel" id="pagebuilder_elm_' + uniqid + '">';
  html += '<input type="hidden" name="v_element_type[' + uniqid + ']" value="multiple_choice">';
  html += '<input type="hidden" name="opt_type[' + uniqid + ']" value="text">';

  if (collapsed) {
      html += '<a class="panel-heading" role="tab" data-toggle="collapse" data-parent="'+ content_container +'" href="#collapse' + uniqid + '" aria-expanded="false">';
  } else {
      html += '<a class="panel-heading" role="tab" data-toggle="collapse" data-parent="'+ content_container +'" href="#collapse' + uniqid + '" aria-expanded="true">';
  }

  html += '<h4 class="panel-title">Multiple Choice (Text) - <i id=section' + uniqid + '>' + question_text + '</i><span class="pull-right"><i class="fa fa-sort"></i><i class="fa fa-trash" style="color:red; margin-left: 20px;" onclick="delete_content_element(' + uniqid + ')"></i></span></h4>';
  html += '</a>';

  if (collapsed) {
      html += '<div id="collapse' + uniqid + '" class="panel-collapse collapse" role="tabpanel">';
  } else {
      html += '<div id="collapse' + uniqid + '" class="panel-collapse collapse in" role="tabpanel">';
  }

  html += '<div class="panel-body">';
  html += '<div class="form-group">';
  html += '<label class="control-label col-md-3 col-sm-3 col-xs-12">Question <span class="required">*</span></label>';
  html += '<div class="col-md-9 col-sm-9 col-xs-12"><textarea name="question_text[' + uniqid + ']" class="form-control col-md-7 col-xs-12" onblur="set_section_name(' + uniqid + ', this.value)">' + question_text + '</textarea></div>';
  html += '</div>';

  html += '<div class="form-group">';
  html += '<label class="control-label col-md-3 col-sm-3 col-xs-12">Media <span class="required">*</span></label>';
  html += '<div class="col-md-6 col-sm-6 col-xs-12">';
  html += '<select name="question_media[' + uniqid + ']" id="question_media_' + uniqid + '" class="form-control col-md-7 col-xs-12" onchange="show_question_element_media(' + uniqid + ')">';
  html += '<option value="">NO MEDIA</option>';
  html += '<option value="image" ' + select_image + '>Image</option>';
  html += '<option value="youtube" ' + select_youtube + '>YouTube</option>';
  html += '</select>';
  html += '</div>';
  html += '</div>';

  html += '<div class="form-group media-question-' + uniqid + '" id="media-question-youtube-' + uniqid + '" style="display:none;">';
  html += '<label class="control-label col-md-3 col-sm-3 col-xs-12">Video (YouTube URL) <span class="required">*</span></label>';
  html += '<div class="col-md-6 col-sm-6 col-xs-12"><input type="text" autocomplete="off" name="v_element_content_video[' + uniqid + ']" placeholder="https://www.youtube.com/watch?v=XXXX" class="form-control col-md-7 col-xs-12" value="' + question_src_youtube + '"></div>';
  html += '</div>';

  html += '<div class="form-group media-question-' + uniqid + '" id="media-question-image-' + uniqid + '" style="display:none;">';
  html += '<label class="control-label col-md-3 col-sm-3 col-xs-12">Image <span class="required">*</span></label>';
  html += '<div class="col-md-6 col-sm-6 col-xs-12">';
  html += '<img src="' + question_src_image + '" style="max-width: 200px;max-height: 200px;display: block;">';
  html += '<input type="file" name="v_element_content_image[' + uniqid + ']" class="form-control col-md-7 col-xs-12" accept=".jpg, .jpeg, .png" onchange="readURL(this, \'before\');" style="margin-top:5px">';
  html += '<input type="hidden" name="v_element_content_image_exist[' + uniqid + ']" value="' + question_src + '">';
  html += '</div>';
  html += '</div>';

  html += '<hr>';

  html += '<div class="form-group">';
  html += '<label class="control-label col-md-3 col-sm-3 col-xs-12">&nbsp;</label>';
  html += '<div class="col-md-6 col-sm-6 col-xs-12">';
  html += '<span class="btn btn-info" onclick="add_multiple_option_text(' + uniqid + ')"><i class="fa fa-plus-circle"></i> Add Option</span>';
  html += '</div>';
  html += '</div>';

  html += '<div class="sortable-option" id="list-option-' + uniqid + '">';
  if (options != '') {
      options = JSON.parse(options);
      $.each(options, function(key, value) {
          var i = key + 1;
          html += '<div class="form-group option-' + uniqid + '" id="v_option_' + uniqid + i + '" style="border: 2px dashed black; margin:5px; padding:10px;">';
          html += '<label class="control-label col-md-3 col-sm-3 col-xs-12"><i class="fa fa-sort"></i> Option <span class="required">*</span></label>';
          html += '<div class="col-md-6 col-sm-6 col-xs-12">';
          html += '<input type="text" autocomplete="off" value="' + value + '" name="v_opt[' + uniqid + '][]" required="required" class="form-control col-md-11 col-xs-12" placeholder="Option ' + i + '" />';
          html += '<i class="fa fa-trash" style="color:red; margin-left:20px; margin-top:10px;" onclick="delete_option(' + uniqid + i + ')"></i>';
          html += '</div>';
          html += '</div>';
      });
  } else {
      for (i = 1; i <= 4; i++) {
          html += '<div class="form-group option-' + uniqid + '" id="v_option_' + uniqid + i + '" style="border: 2px dashed black; margin:5px; padding:10px;">';
          html += '<label class="control-label col-md-3 col-sm-3 col-xs-12"><i class="fa fa-sort"></i> Option <span class="required">*</span></label>';
          html += '<div class="col-md-6 col-sm-6 col-xs-12">';
          html += '<input type="text" autocomplete="off" value="" name="v_opt[' + uniqid + '][]" required="required" class="form-control col-md-11 col-xs-12" placeholder="Option ' + i + '" />';
          html += '<i class="fa fa-trash" style="color:red; margin-left:20px; margin-top:10px;" onclick="delete_option(' + uniqid + i + ')"></i>';
          html += '</div>';
          html += '</div>';
      }
  }
  html += '</div>';

  html += '<hr>';

  html += '<div class="form-group">';
  html += '<label class="control-label col-md-3 col-sm-3 col-xs-12">Add "Other" Option</label>';
  html += '<div class="col-md-6 col-sm-6 col-xs-12">';
  html += '<select name="question_opt_other[' + uniqid + ']" class="form-control col-md-7 col-xs-12">';
  html += '<option value="0" ' + opt_other_no + '>NO</option>';
  html += '<option value="1" ' + opt_other_yes + '>YES</option>';
  html += '</select>';
  html += '</div>';
  html += '</div>';

  html += '<div class="form-group">';
  html += '<label class="control-label col-md-3 col-sm-3 col-xs-12">Nominal Points <span class="required">*</span></label>';
  html += '<div class="col-md-9 col-sm-9 col-xs-12"><input type="number" name="opt_nominal_points[' + uniqid + ']" min="0" class="form-control col-md-7 col-xs-12" placeholder="enter here if want to set points per question" value="' + points_per_item + '"></div>';
  html += '</div>';

  if (required) {
      html += '<div class="form-group">';
      html += '<label class="control-label col-md-3 col-sm-3 col-xs-12">Required to Answer? <span class="required">*</span></label>';
      html += '<div class="col-md-6 col-sm-6 col-xs-12">';
      html += '<select name="question_is_required[' + uniqid + ']" class="form-control col-md-7 col-xs-12">';
      html += '<option value="0" ' + is_required_no + '>NO</option>';
      html += '<option value="1" ' + is_required_yes + '>YES</option>';
      html += '</select>';
      html += '</div>';
      html += '</div>';
  }

  html += '</div>';
  html += '</div>';
  html += '</div>';

  return html;
}

function set_html_multiple_choice_image(collapsed, identifier, data, config, options, required) {
  if (identifier == 0) {
      var uniqid = Date.now();
  } else {
      var uniqid = identifier;
  }
  var default_value = '{{ asset("/images/no-image.png") }}';

  var question_text = '';
  var question_media = '';
  var select_image = '';
  var select_youtube = '';
  var question_src = '';
  var question_src_image = default_value;
  var question_src_youtube = '';
  var points_per_item = '';
  var is_required_yes = '';
  var is_required_no = 'selected';
  if (data != '') {
      data = JSON.parse(data);
      question_text = data.question_text;
      question_media = data.question_media;
      question_src = data.question_src;
      if (question_media == 'image') {
          select_image = 'selected';
          question_src_image = '{{ asset("uploads/mission") }}/' + question_src;
      } else if (question_media == 'youtube') {
          select_youtube = 'selected';
          question_src_youtube = question_src;
      }
      points_per_item = data.points_per_item;
      is_required = data.is_required;
      if (is_required == 1) {
          is_required_yes = 'selected';
          is_required_no = '';
      }
  }

  var opt_other_yes = '';
  var opt_other_no = 'selected';
  if (config != '') {
      config = JSON.parse(config);
      opt_other = config.opt_other;
      if (opt_other == 1) {
          opt_other_yes = 'selected';
          opt_other_no = '';
      }
  }

  var html = '<div class="panel" id="pagebuilder_elm_' + uniqid + '">';
  html += '<input type="hidden" name="v_element_type[' + uniqid + ']" value="multiple_choice">';
  html += '<input type="hidden" name="opt_type[' + uniqid + ']" value="image">';

  if (collapsed) {
      html += '<a class="panel-heading" role="tab" data-toggle="collapse" data-parent="'+ content_container +'" href="#collapse' + uniqid + '" aria-expanded="false">';
  } else {
      html += '<a class="panel-heading" role="tab" data-toggle="collapse" data-parent="'+ content_container +'" href="#collapse' + uniqid + '" aria-expanded="true">';
  }

  html += '<h4 class="panel-title">Multiple Choice (Image) - <i id=section' + uniqid + '>' + question_text + '</i><span class="pull-right"><i class="fa fa-sort"></i><i class="fa fa-trash" style="color:red; margin-left: 20px;" onclick="delete_content_element(' + uniqid + ')"></i></span></h4>';
  html += '</a>';

  if (collapsed) {
      html += '<div id="collapse' + uniqid + '" class="panel-collapse collapse" role="tabpanel">';
  } else {
      html += '<div id="collapse' + uniqid + '" class="panel-collapse collapse in" role="tabpanel">';
  }

  html += '<div class="panel-body">';
  html += '<div class="form-group">';
  html += '<label class="control-label col-md-3 col-sm-3 col-xs-12">Question <span class="required">*</span></label>';
  html += '<div class="col-md-9 col-sm-9 col-xs-12"><textarea name="question_text[' + uniqid + ']" class="form-control col-md-7 col-xs-12" onblur="set_section_name(' + uniqid + ', this.value)">' + question_text + '</textarea></div>';
  html += '</div>';

  html += '<div class="form-group">';
  html += '<label class="control-label col-md-3 col-sm-3 col-xs-12">Media <span class="required">*</span></label>';
  html += '<div class="col-md-6 col-sm-6 col-xs-12">';
  html += '<select name="question_media[' + uniqid + ']" id="question_media_' + uniqid + '" class="form-control col-md-7 col-xs-12" onchange="show_question_element_media(' + uniqid + ')">';
  html += '<option value="">NO MEDIA</option>';
  html += '<option value="image" ' + select_image + '>Image</option>';
  html += '<option value="youtube" ' + select_youtube + '>YouTube</option>';
  html += '</select>';
  html += '</div>';
  html += '</div>';

  html += '<div class="form-group media-question-' + uniqid + '" id="media-question-youtube-' + uniqid + '" style="display:none;">';
  html += '<label class="control-label col-md-3 col-sm-3 col-xs-12">Video (YouTube URL) <span class="required">*</span></label>';
  html += '<div class="col-md-6 col-sm-6 col-xs-12"><input type="text" autocomplete="off" name="v_element_content_video[' + uniqid + ']" placeholder="https://www.youtube.com/watch?v=XXXX" class="form-control col-md-7 col-xs-12" value="' + question_src_youtube + '"></div>';
  html += '</div>';

  html += '<div class="form-group media-question-' + uniqid + '" id="media-question-image-' + uniqid + '" style="display:none;">';
  html += '<label class="control-label col-md-3 col-sm-3 col-xs-12">Image <span class="required">*</span></label>';
  html += '<div class="col-md-6 col-sm-6 col-xs-12">';
  html += '<img src="' + question_src_image + '" style="max-width: 200px;max-height: 200px;display: block;">';
  html += '<input type="file" name="v_element_content_image[' + uniqid + ']" class="form-control col-md-7 col-xs-12" accept=".jpg, .jpeg, .png" onchange="readURL(this, \'before\');" style="margin-top:5px">';
  html += '<input type="hidden" name="v_element_content_image_exist[' + uniqid + ']" value="' + question_src + '">';
  html += '</div>';
  html += '</div>';

  html += '<hr>';

  html += '<div class="form-group">';
  html += '<label class="control-label col-md-3 col-sm-3 col-xs-12">&nbsp;</label>';
  html += '<div class="col-md-6 col-sm-6 col-xs-12">';
  html += '<span class="btn btn-info" onclick="add_multiple_option_image(' + uniqid + ')"><i class="fa fa-plus-circle"></i> Add Option</span>';
  html += '</div>';
  html += '</div>';

  html += '<div class="sortable-option" id="list-option-' + uniqid + '">';
  if (options != '') {
      options = JSON.parse(options);
      $.each(options, function(key, value) {
          var i = key + 1;
          html += '<div class="form-group option-' + uniqid + '" id="v_option_' + uniqid + i + '" style="border: 2px dashed black; margin:5px; padding:10px;">';
          html += '<label class="control-label col-md-3 col-sm-3 col-xs-12"><i class="fa fa-sort"></i> Option <span class="required">*</span></label>';
          html += '<div class="col-md-6 col-sm-6 col-xs-12">';
          html += '<img src="{{ asset("uploads/mission") }}/' + value + '" style="max-width: 200px;max-height: 200px;display: block;">';
          html += '<input type="file" name="v_opt[' + uniqid + '][]" class="form-control col-md-7 col-xs-12" accept=".jpg, .jpeg, .png" onchange="readURL(this, \'before\');" style="margin-top:5px">';
          html += '<i class="fa fa-trash" style="color:red; margin-left:20px; margin-top:10px;" onclick="delete_option(' + uniqid + i + ')"></i>';
          html += '</div>';
          html += '</div>';
          html += '<input type="hidden" name="v_opt_exist[' + uniqid + '][]" value="' + value + '" />';
      });
  } else {
      for (i = 1; i <= 4; i++) {
          html += '<div class="form-group option-' + uniqid + '" id="v_option_' + uniqid + i + '" style="border: 2px dashed black; margin:5px; padding:10px;">';
          html += '<label class="control-label col-md-3 col-sm-3 col-xs-12"><i class="fa fa-sort"></i> Option <span class="required">*</span></label>';
          html += '<div class="col-md-6 col-sm-6 col-xs-12">';
          html += '<img src="' + default_value + '" style="max-width: 200px;max-height: 200px;display: block;">';
          html += '<input type="file" name="v_opt[' + uniqid + '][]" required="required" class="form-control col-md-7 col-xs-12" accept=".jpg, .jpeg, .png" onchange="readURL(this, \'before\');" style="margin-top:5px">';
          html += '<i class="fa fa-trash" style="color:red; margin-left:20px; margin-top:10px;" onclick="delete_option(' + uniqid + i + ')"></i>';
          html += '</div>';
          html += '</div>';
      }
  }
  html += '</div>';

  html += '<hr>';

  html += '<div class="form-group">';
  html += '<label class="control-label col-md-3 col-sm-3 col-xs-12">Nominal Points <span class="required">*</span></label>';
  html += '<div class="col-md-9 col-sm-9 col-xs-12"><input type="number" name="opt_nominal_points[' + uniqid + ']" min="0" class="form-control col-md-7 col-xs-12" placeholder="enter here if want to set points per question" value="' + points_per_item + '"></div>';
  html += '</div>';

  if (required) {
      html += '<div class="form-group">';
      html += '<label class="control-label col-md-3 col-sm-3 col-xs-12">Required to Answer? <span class="required">*</span></label>';
      html += '<div class="col-md-6 col-sm-6 col-xs-12">';
      html += '<select name="question_is_required[' + uniqid + ']" class="form-control col-md-7 col-xs-12">';
      html += '<option value="0" ' + is_required_no + '>NO</option>';
      html += '<option value="1" ' + is_required_yes + '>YES</option>';
      html += '</select>';
      html += '</div>';
      html += '</div>';
  }

  html += '</div>';
  html += '</div>';
  html += '</div>';

  return html;
}

function set_html_checkboxes_text(collapsed, identifier, data, config, options, required) {
  if (identifier == 0) {
      var uniqid = Date.now();
  } else {
      var uniqid = identifier;
  }
  var default_value = '{{ asset("/images/no-image.png") }}';

  var question_text = '';
  var question_media = '';
  var select_image = '';
  var select_youtube = '';
  var question_src = '';
  var question_src_image = default_value;
  var question_src_youtube = '';
  var points_per_item = '';
  var is_required_yes = '';
  var is_required_no = 'selected';
  if (data != '') {
      data = JSON.parse(data);
      question_text = data.question_text;
      question_media = data.question_media;
      question_src = data.question_src;
      if (question_media == 'image') {
          select_image = 'selected';
          question_src_image = '{{ asset("uploads/mission") }}/' + question_src;
      } else if (question_media == 'youtube') {
          select_youtube = 'selected';
          question_src_youtube = question_src;
      }
      points_per_item = data.points_per_item;
      is_required = data.is_required;
      if (is_required == 1) {
          is_required_yes = 'selected';
          is_required_no = '';
      }
  }

  var opt_other_yes = '';
  var opt_other_no = 'selected';
  if (config != '') {
      config = JSON.parse(config);
      opt_other = config.opt_other;
      if (opt_other == 1) {
          opt_other_yes = 'selected';
          opt_other_no = '';
      }
  }

  var html = '<div class="panel" id="pagebuilder_elm_' + uniqid + '">';
  html += '<input type="hidden" name="v_element_type[' + uniqid + ']" value="checkboxes">';
  html += '<input type="hidden" name="opt_type[' + uniqid + ']" value="text">';

  if (collapsed) {
      html += '<a class="panel-heading" role="tab" data-toggle="collapse" data-parent="'+ content_container +'" href="#collapse' + uniqid + '" aria-expanded="false">';
  } else {
      html += '<a class="panel-heading" role="tab" data-toggle="collapse" data-parent="'+ content_container +'" href="#collapse' + uniqid + '" aria-expanded="true">';
  }

  html += '<h4 class="panel-title">Checkboxes - <i id=section' + uniqid + '>' + question_text + '</i><span class="pull-right"><i class="fa fa-sort"></i><i class="fa fa-trash" style="color:red; margin-left: 20px;" onclick="delete_content_element(' + uniqid + ')"></i></span></h4>';
  html += '</a>';

  if (collapsed) {
      html += '<div id="collapse' + uniqid + '" class="panel-collapse collapse" role="tabpanel">';
  } else {
      html += '<div id="collapse' + uniqid + '" class="panel-collapse collapse in" role="tabpanel">';
  }

  html += '<div class="panel-body">';
  html += '<div class="form-group">';
  html += '<label class="control-label col-md-3 col-sm-3 col-xs-12">Question <span class="required">*</span></label>';
  html += '<div class="col-md-9 col-sm-9 col-xs-12"><textarea name="question_text[' + uniqid + ']" class="form-control col-md-7 col-xs-12" onblur="set_section_name(' + uniqid + ', this.value)">' + question_text + '</textarea></div>';
  html += '</div>';

  html += '<div class="form-group">';
  html += '<label class="control-label col-md-3 col-sm-3 col-xs-12">Media <span class="required">*</span></label>';
  html += '<div class="col-md-6 col-sm-6 col-xs-12">';
  html += '<select name="question_media[' + uniqid + ']" id="question_media_' + uniqid + '" class="form-control col-md-7 col-xs-12" onchange="show_question_element_media(' + uniqid + ')">';
  html += '<option value="">NO MEDIA</option>';
  html += '<option value="image" ' + select_image + '>Image</option>';
  html += '<option value="youtube" ' + select_youtube + '>YouTube</option>';
  html += '</select>';
  html += '</div>';
  html += '</div>';

  html += '<div class="form-group media-question-' + uniqid + '" id="media-question-youtube-' + uniqid + '" style="display:none;">';
  html += '<label class="control-label col-md-3 col-sm-3 col-xs-12">Video (YouTube URL) <span class="required">*</span></label>';
  html += '<div class="col-md-6 col-sm-6 col-xs-12"><input type="text" autocomplete="off" name="v_element_content_video[' + uniqid + ']" placeholder="https://www.youtube.com/watch?v=XXXX" class="form-control col-md-7 col-xs-12" value="' + question_src_youtube + '"></div>';
  html += '</div>';

  html += '<div class="form-group media-question-' + uniqid + '" id="media-question-image-' + uniqid + '" style="display:none;">';
  html += '<label class="control-label col-md-3 col-sm-3 col-xs-12">Image <span class="required">*</span></label>';
  html += '<div class="col-md-6 col-sm-6 col-xs-12">';
  html += '<img src="' + question_src_image + '" style="max-width: 200px;max-height: 200px;display: block;">';
  html += '<input type="file" name="v_element_content_image[' + uniqid + ']" class="form-control col-md-7 col-xs-12" accept=".jpg, .jpeg, .png" onchange="readURL(this, \'before\');" style="margin-top:5px">';
  html += '<input type="hidden" name="v_element_content_image_exist[' + uniqid + ']" value="' + question_src + '">';
  html += '</div>';
  html += '</div>';

  html += '<hr>';

  html += '<div class="form-group">';
  html += '<label class="control-label col-md-3 col-sm-3 col-xs-12">&nbsp;</label>';
  html += '<div class="col-md-6 col-sm-6 col-xs-12">';
  html += '<span class="btn btn-info" onclick="add_multiple_option_text(' + uniqid + ')"><i class="fa fa-plus-circle"></i> Add Option</span>';
  html += '</div>';
  html += '</div>';

  html += '<div class="sortable-option" id="list-option-' + uniqid + '">';
  if (options != '') {
      options = JSON.parse(options);
      $.each(options, function(key, value) {
          var i = key + 1;
          html += '<div class="form-group option-' + uniqid + '" id="v_option_' + uniqid + i + '" style="border: 2px dashed black; margin:5px; padding:10px;">';
          html += '<label class="control-label col-md-3 col-sm-3 col-xs-12"><i class="fa fa-sort"></i> Option <span class="required">*</span></label>';
          html += '<div class="col-md-6 col-sm-6 col-xs-12">';
          html += '<input type="text" autocomplete="off" value="' + value + '" name="v_opt[' + uniqid + '][]" required="required" class="form-control col-md-11 col-xs-12" placeholder="Option ' + i + '" />';
          html += '<i class="fa fa-trash" style="color:red; margin-left:20px; margin-top:10px;" onclick="delete_option(' + uniqid + i + ')"></i>';
          html += '</div>';
          html += '</div>';
      });
  } else {
      for (i = 1; i <= 4; i++) {
          html += '<div class="form-group option-' + uniqid + '" id="v_option_' + uniqid + i + '" style="border: 2px dashed black; margin:5px; padding:10px;">';
          html += '<label class="control-label col-md-3 col-sm-3 col-xs-12"><i class="fa fa-sort"></i> Option <span class="required">*</span></label>';
          html += '<div class="col-md-6 col-sm-6 col-xs-12">';
          html += '<input type="text" autocomplete="off" value="" name="v_opt[' + uniqid + '][]" required="required" class="form-control col-md-11 col-xs-12" placeholder="Option ' + i + '" />';
          html += '<i class="fa fa-trash" style="color:red; margin-left:20px; margin-top:10px;" onclick="delete_option(' + uniqid + i + ')"></i>';
          html += '</div>';
          html += '</div>';
      }
  }
  html += '</div>';

  html += '<hr>';

  html += '<div class="form-group">';
  html += '<label class="control-label col-md-3 col-sm-3 col-xs-12">Nominal Points <span class="required">*</span></label>';
  html += '<div class="col-md-9 col-sm-9 col-xs-12"><input type="number" name="opt_nominal_points[' + uniqid + ']" min="0" class="form-control col-md-7 col-xs-12" placeholder="enter here if want to set points per question" value="' + points_per_item + '"></div>';
  html += '</div>';

  html += '<div class="form-group">';
  html += '<label class="control-label col-md-3 col-sm-3 col-xs-12">Add "Other" Option</label>';
  html += '<div class="col-md-6 col-sm-6 col-xs-12">';
  html += '<select name="question_opt_other[' + uniqid + ']" class="form-control col-md-7 col-xs-12">';
  html += '<option value="0" ' + opt_other_no + '>NO</option>';
  html += '<option value="1" ' + opt_other_yes + '>YES</option>';
  html += '</select>';
  html += '</div>';
  html += '</div>';

  if (required) {
      html += '<div class="form-group">';
      html += '<label class="control-label col-md-3 col-sm-3 col-xs-12">Required to Answer? <span class="required">*</span></label>';
      html += '<div class="col-md-6 col-sm-6 col-xs-12">';
      html += '<select name="question_is_required[' + uniqid + ']" class="form-control col-md-7 col-xs-12">';
      html += '<option value="0" ' + is_required_no + '>NO</option>';
      html += '<option value="1" ' + is_required_yes + '>YES</option>';
      html += '</select>';
      html += '</div>';
      html += '</div>';
  }

  html += '</div>';
  html += '</div>';
  html += '</div>';

  return html;
}

function set_html_dropdown(collapsed, identifier, data, config, options, required) {
  if (identifier == 0) {
      var uniqid = Date.now();
  } else {
      var uniqid = identifier;
  }
  var default_value = '{{ asset("/images/no-image.png") }}';

  var question_text = '';
  var question_media = '';
  var select_image = '';
  var select_youtube = '';
  var question_src = '';
  var question_src_image = default_value;
  var question_src_youtube = '';
  var points_per_item = '';
  var is_required_yes = '';
  var is_required_no = 'selected';
  if (data != '') {
      data = JSON.parse(data);
      question_text = data.question_text;
      question_media = data.question_media;
      question_src = data.question_src;
      if (question_media == 'image') {
          select_image = 'selected';
          question_src_image = '{{ asset("uploads/mission") }}/' + question_src;
      } else if (question_media == 'youtube') {
          select_youtube = 'selected';
          question_src_youtube = question_src;
      }
      points_per_item = data.points_per_item;
      is_required = data.is_required;
      if (is_required == 1) {
          is_required_yes = 'selected';
          is_required_no = '';
      }
  }

  var opt_other_yes = '';
  var opt_other_no = 'selected';
  if (config != '') {
      config = JSON.parse(config);
      opt_other = config.opt_other;
      if (opt_other == 1) {
          opt_other_yes = 'selected';
          opt_other_no = '';
      }
  }

  var html = '<div class="panel" id="pagebuilder_elm_' + uniqid + '">';
  html += '<input type="hidden" name="v_element_type[' + uniqid + ']" value="drop-down">';
  html += '<input type="hidden" name="opt_type[' + uniqid + ']" value="text">';

  if (collapsed) {
      html += '<a class="panel-heading" role="tab" data-toggle="collapse" data-parent="'+ content_container +'" href="#collapse' + uniqid + '" aria-expanded="false">';
  } else {
      html += '<a class="panel-heading" role="tab" data-toggle="collapse" data-parent="'+ content_container +'" href="#collapse' + uniqid + '" aria-expanded="true">';
  }

  html += '<h4 class="panel-title">Drop-down - <i id=section' + uniqid + '>' + question_text + '</i><span class="pull-right"><i class="fa fa-sort"></i><i class="fa fa-trash" style="color:red; margin-left: 20px;" onclick="delete_content_element(' + uniqid + ')"></i></span></h4>';
  html += '</a>';

  if (collapsed) {
      html += '<div id="collapse' + uniqid + '" class="panel-collapse collapse" role="tabpanel">';
  } else {
      html += '<div id="collapse' + uniqid + '" class="panel-collapse collapse in" role="tabpanel">';
  }

  html += '<div class="panel-body">';
  html += '<div class="form-group">';
  html += '<label class="control-label col-md-3 col-sm-3 col-xs-12">Question <span class="required">*</span></label>';
  html += '<div class="col-md-9 col-sm-9 col-xs-12"><textarea name="question_text[' + uniqid + ']" class="form-control col-md-7 col-xs-12" onblur="set_section_name(' + uniqid + ', this.value)">' + question_text + '</textarea></div>';
  html += '</div>';

  html += '<div class="form-group">';
  html += '<label class="control-label col-md-3 col-sm-3 col-xs-12">Media <span class="required">*</span></label>';
  html += '<div class="col-md-6 col-sm-6 col-xs-12">';
  html += '<select name="question_media[' + uniqid + ']" id="question_media_' + uniqid + '" class="form-control col-md-7 col-xs-12" onchange="show_question_element_media(' + uniqid + ')">';
  html += '<option value="">NO MEDIA</option>';
  html += '<option value="image" ' + select_image + '>Image</option>';
  html += '<option value="youtube" ' + select_youtube + '>YouTube</option>';
  html += '</select>';
  html += '</div>';
  html += '</div>';

  html += '<div class="form-group media-question-' + uniqid + '" id="media-question-youtube-' + uniqid + '" style="display:none;">';
  html += '<label class="control-label col-md-3 col-sm-3 col-xs-12">Video (YouTube URL) <span class="required">*</span></label>';
  html += '<div class="col-md-6 col-sm-6 col-xs-12"><input type="text" autocomplete="off" name="v_element_content_video[' + uniqid + ']" placeholder="https://www.youtube.com/watch?v=XXXX" class="form-control col-md-7 col-xs-12" value="' + question_src_youtube + '"></div>';
  html += '</div>';

  html += '<div class="form-group media-question-' + uniqid + '" id="media-question-image-' + uniqid + '" style="display:none;">';
  html += '<label class="control-label col-md-3 col-sm-3 col-xs-12">Image <span class="required">*</span></label>';
  html += '<div class="col-md-6 col-sm-6 col-xs-12">';
  html += '<img src="' + question_src_image + '" style="max-width: 200px;max-height: 200px;display: block;">';
  html += '<input type="file" name="v_element_content_image[' + uniqid + ']" class="form-control col-md-7 col-xs-12" accept=".jpg, .jpeg, .png" onchange="readURL(this, \'before\');" style="margin-top:5px">';
  html += '<input type="hidden" name="v_element_content_image_exist[' + uniqid + ']" value="' + question_src + '">';
  html += '</div>';
  html += '</div>';

  html += '<hr>';

  html += '<div class="form-group">';
  html += '<label class="control-label col-md-3 col-sm-3 col-xs-12">&nbsp;</label>';
  html += '<div class="col-md-6 col-sm-6 col-xs-12">';
  html += '<span class="btn btn-info" onclick="add_multiple_option_text(' + uniqid + ')"><i class="fa fa-plus-circle"></i> Add Option</span>';
  html += '</div>';
  html += '</div>';

  html += '<div class="sortable-option" id="list-option-' + uniqid + '">';
  if (options != '') {
      options = JSON.parse(options);
      $.each(options, function(key, value) {
          var i = key + 1;
          html += '<div class="form-group option-' + uniqid + '" id="v_option_' + uniqid + i + '" style="border: 2px dashed black; margin:5px; padding:10px;">';
          html += '<label class="control-label col-md-3 col-sm-3 col-xs-12"><i class="fa fa-sort"></i> Option <span class="required">*</span></label>';
          html += '<div class="col-md-6 col-sm-6 col-xs-12">';
          html += '<input type="text" autocomplete="off" value="' + value + '" name="v_opt[' + uniqid + '][]" required="required" class="form-control col-md-11 col-xs-12" placeholder="Option ' + i + '" />';
          html += '<i class="fa fa-trash" style="color:red; margin-left:20px; margin-top:10px;" onclick="delete_option(' + uniqid + i + ')"></i>';
          html += '</div>';
          html += '</div>';
      });
  } else {
      for (i = 1; i <= 4; i++) {
          html += '<div class="form-group option-' + uniqid + '" id="v_option_' + uniqid + i + '" style="border: 2px dashed black; margin:5px; padding:10px;">';
          html += '<label class="control-label col-md-3 col-sm-3 col-xs-12"><i class="fa fa-sort"></i> Option <span class="required">*</span></label>';
          html += '<div class="col-md-6 col-sm-6 col-xs-12">';
          html += '<input type="text" autocomplete="off" value="" name="v_opt[' + uniqid + '][]" required="required" class="form-control col-md-11 col-xs-12" placeholder="Option ' + i + '" />';
          html += '<i class="fa fa-trash" style="color:red; margin-left:20px; margin-top:10px;" onclick="delete_option(' + uniqid + i + ')"></i>';
          html += '</div>';
          html += '</div>';
      }
  }
  html += '</div>';

  html += '<hr>';

  html += '<div class="form-group">';
  html += '<label class="control-label col-md-3 col-sm-3 col-xs-12">Nominal Points <span class="required">*</span></label>';
  html += '<div class="col-md-9 col-sm-9 col-xs-12"><input type="number" name="opt_nominal_points[' + uniqid + ']" min="0" class="form-control col-md-7 col-xs-12" placeholder="enter here if want to set points per question" value="' + points_per_item + '"></div>';
  html += '</div>';

  if (required) {
      html += '<div class="form-group">';
      html += '<label class="control-label col-md-3 col-sm-3 col-xs-12">Required to Answer? <span class="required">*</span></label>';
      html += '<div class="col-md-6 col-sm-6 col-xs-12">';
      html += '<select name="question_is_required[' + uniqid + ']" class="form-control col-md-7 col-xs-12">';
      html += '<option value="0" ' + is_required_no + '>NO</option>';
      html += '<option value="1" ' + is_required_yes + '>YES</option>';
      html += '</select>';
      html += '</div>';
      html += '</div>';
  }

  html += '</div>';
  html += '</div>';
  html += '</div>';

  return html;
}

function set_html_linear_scale(collapsed, identifier, data, config, options, required) {
  if (identifier == 0) {
      var uniqid = Date.now();
  } else {
      var uniqid = identifier;
  }
  var default_value = '{{ asset("/images/no-image.png") }}';

  var question_text = '';
  var question_media = '';
  var select_image = '';
  var select_youtube = '';
  var question_src = '';
  var question_src_image = default_value;
  var question_src_youtube = '';
  var points_per_item = '';
  var is_required_yes = '';
  var is_required_no = 'selected';
  if (data != '') {
      data = JSON.parse(data);
      question_text = data.question_text;
      question_media = data.question_media;
      question_src = data.question_src;
      if (question_media == 'image') {
          select_image = 'selected';
          question_src_image = '{{ asset("uploads/mission") }}/' + question_src;
      } else if (question_media == 'youtube') {
          select_youtube = 'selected';
          question_src_youtube = question_src;
      }
      points_per_item = data.points_per_item;
      is_required = data.is_required;
      if (is_required == 1) {
          is_required_yes = 'selected';
          is_required_no = '';
      }
  }

  var opt_other_yes = '';
  var opt_other_no = 'selected';
  var start_from_0 = '';
  var start_from_1 = 'selected';
  var total_opt = 0;
  if (config != '') {
      config = JSON.parse(config);
      opt_other = config.opt_other;
      if (opt_other == 1) {
          opt_other_yes = 'selected';
          opt_other_no = '';
      }
      start_from = config.start_from;
      if (start_from != 1) {
          start_from_0 = 'selected';
          start_from_1 = '';
      }
      total_opt = config.total_opt;
  }

  var html = '<div class="panel" id="pagebuilder_elm_' + uniqid + '">';
  html += '<input type="hidden" name="v_element_type[' + uniqid + ']" value="linear_scale">';
  html += '<input type="hidden" name="opt_type[' + uniqid + ']" value="text">';

  if (collapsed) {
      html += '<a class="panel-heading" role="tab" data-toggle="collapse" data-parent="'+ content_container +'" href="#collapse' + uniqid + '" aria-expanded="false">';
  } else {
      html += '<a class="panel-heading" role="tab" data-toggle="collapse" data-parent="'+ content_container +'" href="#collapse' + uniqid + '" aria-expanded="true">';
  }

  html += '<h4 class="panel-title">Linear Scale - <i id=section' + uniqid + '>' + question_text + '</i><span class="pull-right"><i class="fa fa-sort"></i><i class="fa fa-trash" style="color:red; margin-left: 20px;" onclick="delete_content_element(' + uniqid + ')"></i></span></h4>';
  html += '</a>';

  if (collapsed) {
      html += '<div id="collapse' + uniqid + '" class="panel-collapse collapse" role="tabpanel">';
  } else {
      html += '<div id="collapse' + uniqid + '" class="panel-collapse collapse in" role="tabpanel">';
  }

  html += '<div class="panel-body">';
  html += '<div class="form-group">';
  html += '<label class="control-label col-md-3 col-sm-3 col-xs-12">Question <span class="required">*</span></label>';
  html += '<div class="col-md-9 col-sm-9 col-xs-12"><textarea name="question_text[' + uniqid + ']" class="form-control col-md-7 col-xs-12" onblur="set_section_name(' + uniqid + ', this.value)">' + question_text + '</textarea></div>';
  html += '</div>';

  html += '<div class="form-group">';
  html += '<label class="control-label col-md-3 col-sm-3 col-xs-12">Media <span class="required">*</span></label>';
  html += '<div class="col-md-6 col-sm-6 col-xs-12">';
  html += '<select name="question_media[' + uniqid + ']" id="question_media_' + uniqid + '" class="form-control col-md-7 col-xs-12" onchange="show_question_element_media(' + uniqid + ')">';
  html += '<option value="">NO MEDIA</option>';
  html += '<option value="image" ' + select_image + '>Image</option>';
  html += '<option value="youtube" ' + select_youtube + '>YouTube</option>';
  html += '</select>';
  html += '</div>';
  html += '</div>';

  html += '<div class="form-group media-question-' + uniqid + '" id="media-question-youtube-' + uniqid + '" style="display:none;">';
  html += '<label class="control-label col-md-3 col-sm-3 col-xs-12">Video (YouTube URL) <span class="required">*</span></label>';
  html += '<div class="col-md-6 col-sm-6 col-xs-12"><input type="text" autocomplete="off" name="v_element_content_video[' + uniqid + ']" placeholder="https://www.youtube.com/watch?v=XXXX" class="form-control col-md-7 col-xs-12" value="' + question_src_youtube + '"></div>';
  html += '</div>';

  html += '<div class="form-group media-question-' + uniqid + '" id="media-question-image-' + uniqid + '" style="display:none;">';
  html += '<label class="control-label col-md-3 col-sm-3 col-xs-12">Image <span class="required">*</span></label>';
  html += '<div class="col-md-6 col-sm-6 col-xs-12">';
  html += '<img src="' + question_src_image + '" style="max-width: 200px;max-height: 200px;display: block;">';
  html += '<input type="file" name="v_element_content_image[' + uniqid + ']" class="form-control col-md-7 col-xs-12" accept=".jpg, .jpeg, .png" onchange="readURL(this, \'before\');" style="margin-top:5px">';
  html += '<input type="hidden" name="v_element_content_image_exist[' + uniqid + ']" value="' + question_src + '">';
  html += '</div>';
  html += '</div>';

  html += '<hr>';

  html += '<div class="form-group">';
  html += '<label class="control-label col-md-3 col-sm-3 col-xs-12">Start From <span class="required">*</span></label>';
  html += '<div class="col-md-6 col-sm-6 col-xs-12">';
  html += '<select name="start_from[' + uniqid + ']" class="form-control col-md-7 col-xs-12">';
  html += '<option value="0" ' + start_from_0 + '>0</option>';
  html += '<option value="1" ' + start_from_1 + '>1</option>';
  html += '</select>';
  html += '</div>';
  html += '</div>';

  html += '<div class="form-group">';
  html += '<label class="control-label col-md-3 col-sm-3 col-xs-12">Until To <span class="required">*</span></label>';
  html += '<div class="col-md-6 col-sm-6 col-xs-12">';
  html += '<select name="until_to[' + uniqid + ']" class="form-control col-md-7 col-xs-12">';
  var until_to_stat = '';
  for (i = 2; i <= 10; i++) {
      until_to_stat = '';
      if (total_opt == i) {
          until_to_stat = 'selected';
      }
      html += '<option value="' + i + '" ' + until_to_stat + '>' + i + '</option>';
  }
  html += '</select>';
  html += '</div>';
  html += '</div>';

  var start_from_label = '';
  var until_to_label = '';
  if (options != '') {
      options = JSON.parse(options);
      if (typeof options[0] !== 'undefined') {
          start_from_label = options[0];
      }
      if (typeof options[1] !== 'undefined') {
          until_to_label = options[1];
      }
  }

  html += '<div class="form-group">';
  html += '<label class="control-label col-md-3 col-sm-3 col-xs-12">Start From Label (optional)</label>';
  html += '<div class="col-md-6 col-sm-6 col-xs-12"><input type="text" autocomplete="off" name="v_opt[' + uniqid + '][]" placeholder="Strongly Agree" class="form-control col-md-7 col-xs-12" value="' + start_from_label + '" /></div>';
  html += '</div>';

  html += '<div class="form-group">';
  html += '<label class="control-label col-md-3 col-sm-3 col-xs-12">Until To Label (optional)</label>';
  html += '<div class="col-md-6 col-sm-6 col-xs-12"><input type="text" autocomplete="off" name="v_opt[' + uniqid + '][]" placeholder="Strongly Disagree" class="form-control col-md-7 col-xs-12" value="' + until_to_label + '" /></div>';
  html += '</div>';

  html += '<hr>';

  html += '<div class="form-group">';
  html += '<label class="control-label col-md-3 col-sm-3 col-xs-12">Nominal Points <span class="required">*</span></label>';
  html += '<div class="col-md-9 col-sm-9 col-xs-12"><input type="number" name="opt_nominal_points[' + uniqid + ']" min="0" class="form-control col-md-7 col-xs-12" placeholder="enter here if want to set points per question" value="' + points_per_item + '"></div>';
  html += '</div>';

  if (required) {
      html += '<div class="form-group">';
      html += '<label class="control-label col-md-3 col-sm-3 col-xs-12">Required to Answer? <span class="required">*</span></label>';
      html += '<div class="col-md-6 col-sm-6 col-xs-12">';
      html += '<select name="question_is_required[' + uniqid + ']" class="form-control col-md-7 col-xs-12">';
      html += '<option value="0" ' + is_required_no + '>NO</option>';
      html += '<option value="1" ' + is_required_yes + '>YES</option>';
      html += '</select>';
      html += '</div>';
      html += '</div>';
  }

  html += '</div>';
  html += '</div>';
  html += '</div>';

  return html;
}
