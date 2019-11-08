@extends('_template_adm.master')

@php
// library
use App\Libraries\Helper;

$incoming_items = Helper::get_total_incoming();

$pagetitle = ucwords(lang('product', $translation)); 

// set form action link
if(isset($data)){
    $pagetitle .= ' ('.ucwords(lang('edit', $translation)).')';
    $link = route('admin_product_do_edit', $data->id);
    $link2 = route('admin_product_submit_qc', $data->id);
    $link3 = route('admin_product_upload_photos', $data->id);
    $link4 = route('admin_product_publish', $data->id);
    $link5 = route('admin_product_booking', $data->id);
}else {
    $data = null;
    $pagetitle .= ' ('. ucwords(lang('new', $translation)) .')';
    $link = route('admin_product_do_create');
    $link2 = '#';
    $link3 = '#';
    $link4 = '#';
    $link5 = '#';
}

// authorizing...
$rule_set_price = 41;
$rule_do_qc = 42;
$rule_upload_photos = 43;
$rule_publish = 44;
$rule_purchase_details = 45;
$rule_booked = 47;
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
                <!-- Smart Wizard -->
                <div id="smartwizard_product">
                    <ul>
                        @if (Helper::authorizing('Product', 'Purchase Details')['status'] == 'true')
                            <li><a href="#step-1">{{ ucwords(lang('purchase', $translation)) }}<br /><small>{{ ucwords(lang('purchase details', $translation)) }}</small></a></li>
                        @endif

                        @if (isset($data))
                            @if (Helper::authorizing('Product', 'Do QC')['status'] == 'true')
                                <li><a href="#step-2">QC<br /><small>Quality Control Task List</small></a></li>
                            @endif
                            @if (Helper::authorizing('Product', 'Upload Photos')['status'] == 'true')
                                <li><a href="#step-3">Photos<br /><small>{{ ucwords(lang('upload product photos', $translation)) }}</small></a></li>
                            @endif
                            @if (Helper::authorizing('Product', 'Publish')['status'] == 'true')
                                <li><a href="#step-4">Publish<br /><small>{{ ucwords(lang('publish product details', $translation)) }}</small></a></li>
                            @endif
                            @if (Helper::authorizing('Product', 'Set Booked')['status'] == 'true')
                                <li><a href="#step-5">Booking<br /><small>{{ ucwords(lang('set booking product', $translation)) }}</small></a></li>
                            @endif
                        @endif
                    </ul>
                    
                    <div>
                        <!-- Purchase Details -->
                        <div id="step-1" class="">
                            <h2 class="StepTitle">{{ ucwords(lang('purchase details', $translation)) }}</h2>
                            <form class="form-horizontal form-label-left" action="{{ $link }}" method="POST" enctype="multipart/form-data" onsubmit="return validateFormPurchase()">
                                {{ csrf_field() }}

                                <hr><center><label>>> {{ strtoupper(lang('seller details', $translation)) }} <<</label><hr></center>

                                @php
                                    // set_input_form ($type, $input_name, $label_name, $data, $errors, $required = false, $placeholder = null, $id_name = null, $value = null, $attributes = null, $defined_data = null, $options = [])
                                    echo set_input_form ('text', 'seller_name', ucwords(lang('name', $translation)), $data, $errors, true, null, null, null, 'autocomplete="off"', null, []);
                                    echo set_input_form ('number_only', 'seller_phone', ucwords(lang('phone', $translation)), $data, $errors, true, '08123456789', null, null, 'autocomplete="off"', null, []);
                                    echo set_input_form ('capital', 'seller_bank_name', ucwords(lang('bank name', $translation)), $data, $errors, false, 'BCA/BNI/BRI/MANDIRI/DLL', null, null, null, null, []);
                                    echo set_input_form ('text', 'seller_bank_account', ucwords(lang('bank account', $translation)), $data, $errors, true, '1234567890', null, null, null, null, []);
                                @endphp

                                @php
                                $input_name = 'seller_idcard';
                                $bad_item = '';
                                if($errors->has($input_name)){
                                    $bad_item = 'bad item';
                                }
                                @endphp
                                <div class="form-group {{ $bad_item }}">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="{{ $input_name }}">KTP</label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        @if(isset($data->$input_name) && !empty($data->$input_name))
                                        <img src="{{ asset('/uploads/product/seller/'.$data->$input_name) }}" class="vimg" />
                                        @else
                                            <img src="{{ asset('/images/identity-card.png') }}" class="vimg" />
                                        @endif

                                        <input type="file" id="{{ $input_name }}" class="form-control col-md-7 col-xs-12" name="{{ $input_name }}" accept=".jpg, .jpeg, .png" onchange="readURL(this, 'before');" style="margin-top:5px">
                                        @if($errors->has($input_name))
                                            <div class="text-danger">
                                                {{ $errors->first($input_name) }}
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                @php
                                $input_name = 'unit_in_tkp';
                                $bad_item = '';
                                if($errors->has($input_name)){
                                    $bad_item = 'bad item';
                                }
                                @endphp
                                <div class="form-group {{ $bad_item }}">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="{{ $input_name }}">{{ (lang('Unit in TKP', $translation)) }} <span class="required">*</span></label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        @if(isset($data->$input_name) && !empty($data->$input_name))
                                        <?php $unit_in_tkp = asset('/uploads/product/seller/'.$data->$input_name); ?>
                                        <img src="{{ asset('/uploads/product/seller/'.$data->$input_name) }}" class="vimg" />
                                        @else
                                            <img src="{{ asset('/images/no-image.png') }}" class="vimg" />
                                        @endif

                                        <input type="file" id="{{ $input_name }}" class="form-control col-md-7 col-xs-12" name="{{ $input_name }}" accept=".jpg, .jpeg, .png" onchange="readURL(this, 'before');" style="margin-top:5px" <?php if(!isset($data->$input_name)) echo 'required'; ?> />
                                        @if($errors->has($input_name))
                                            <div class="text-danger">
                                                {{ $errors->first($input_name) }}
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                @php
                                    echo set_input_form('capital', 'plat_no', 'No. Plat', $data, $errors, true, 'B 1234 XYZ', null, null, 'autocomplete="off"');
                                @endphp

                                <hr><center><label>>> {{ strtoupper(lang('product details', $translation)) }} <<</label><hr></center>

                                @php
                                    // set_input_form ($type, $input_name, $label_name, $data, $errors, $required = false, $placeholder = null, $id_name = null, $value = null, $attributes = null, $defined_data = null, $options = [])
                                    echo set_input_form ('text', 'name', ucwords(lang('name', $translation)), $data, $errors, true, null, null, null, 'autocomplete="off"', null, []);
                                    echo set_input_form ('select2', 'brand_id', ucwords(lang('brand', $translation)), $data, $errors, true, ucwords(lang('please choose one', $translation)), null, null, 'autocomplete="off"', $brands, ['id', 'name']);
                                    
                                    $kms = [
                                        '&lt;1000', '2000', '3000', '4000', '5000', '6000', '7000', '8000', '9000', '10000', '11000', '12000', '13000', '14000', '15000', '&gt;15000', '20000++'
                                    ];
                                    $kms_formatted = [];
                                    foreach ($kms as $item) {
                                        $obj = new \stdClass();
                                        $obj->id = $item;
                                        $obj->name = $item;
                                        $kms_formatted[] = $obj;
                                    }
                                    echo set_input_form ('select2', 'km', ucwords(lang('kilometer', $translation)), $data, $errors, true, ucwords(lang('please choose one', $translation)), null, null, 'autocomplete="off"', $kms_formatted, ['id', 'name']);

                                    echo set_input_form ('datepicker', 'tax', ucwords(lang('tax', $translation)), $data, $errors, true, (lang('click date icon for choose the date', $translation)), null, null, 'autocomplete="off"', null, []);
                                    echo set_input_form ('datepicker', 'purchase_date', ucwords(lang('purchase date', $translation)), $data, $errors, true, (lang('click date icon for choose the date', $translation)), null, null, 'autocomplete="off"', null, []);
                                    echo set_input_form ('number_format', 'purchase_price', ucwords(lang('purchase price', $translation)), $data, $errors, true, ucwords(lang('numeric only', $translation)), null, null, 'autocomplete="off"', null, ['Rp']);
                                    echo set_input_form ('select2', 'branch_id', ucwords(lang('branch', $translation)), $data, $errors, true, ucwords(lang('please choose one', $translation)), null, null, 'autocomplete="off"', $branches, ['id', 'name']);
                                @endphp

                                @php
                                // QC List - BEGIN
                                $input_name = 'qc_status';
                                $style = "display:none";
                                if(isset($data->$input_name)){
                                    if($data->$input_name==1 || old($input_name)=='no_qc'){
                                        $style = "display:none";
                                    }else{
                                        $style = "";
                                    }
                                }
                                @endphp
                                <div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">QC List (*{{ (lang('if exists', $translation)) }})</label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <div class="radio" style="margin-bottom:5px">
                                            <label onclick="displayQCList('hide')">
                                                <input type="radio" name="qc_status" value="no_qc" id="no_qc" class="flat" onclick="displayQCList('hide')" <?php if(isset($data->$input_name) && $data->$input_name==1){ echo 'checked'; }elseif(old($input_name)=='no_qc'){ echo 'checked'; } ?> /> {{ (lang('QC DONE - READY TO SELL', $translation)) }}
                                            </label>
                                        </div>
                                        <div class="radio" style="margin-bottom:5px">
                                            <label onclick="displayQCList()">
                                                <input type="radio" name="qc_status" value="yes_qc" id="yes_qc" class="flat" onclick="displayQCList()" <?php if(isset($data->$input_name) && $data->$input_name==0){ echo 'checked'; }elseif(old($input_name)=='yes_qc'){ echo 'checked'; } ?> /> {{ (lang('Need to QC', $translation)) }}
                                            </label>
                                        </div>
                                    </div>
                                    <input type="hidden" name="qc_deleted" id="qc-item-deleted">
                                </div>
                                
                                <div class="form-group" id="qc-list" style="{{$style}}">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">
                                        <b class="btn btn-primary btn-sm" onclick="addQcItem()"><i class="fa fa-plus-circle"></i> {{ (lang('Add QC Item', $translation)) }}</b>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12" id="qc-list-item">
                                        @if (isset($data->qc_list))
                                            @php
                                            $json = json_decode($data->qc_list);
                                            $i = 0;
                                            @endphp
                                            @if($json)
                                                @foreach ($json as $item)
                                                    @php
                                                    $i++;
                                                    $delbtn = '';
                                                    if($i > 1){
                                                        $delbtn = '<b onclick="deleteQcItem('.$i.')" style="cursor:pointer;float:right;" class="btn btn-sm btn-danger"><i class="fa fa-close"></i></b>';
                                                    }
                                                    @endphp
                                                    <div class="col-md-6 col-sm-6 col-xs-12" id="qc-item-{{$i}}">
                                                        <h4>ITEM {{$i}} <?php echo $delbtn; ?></h4>
                                                        <img src="{{ asset('/uploads/product/qc/'.$item->image) }}"  />
                                                        <input type="file" class="form-control col-md-7 col-xs-12 qc-image" name="qc_img[]" id="qc_img_{{$i}}" accept=".jpg, .jpeg, .png" onchange="readURL(this, 'before');" style="margin-top:5px; margin-bottom:5px;">
                                                        <input type="text" name="qc_desc[]" id="qc_desc_{{$i}}" class="form-control" value="{{$item->description}}" placeholder="{{ ucwords(lang('description', $translation)) }}" />
                                                    </div>
                                                @endforeach
                                            @endif
                                        @else
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <h4>ITEM 1</h4>
                                                <img src="{{ asset('/images/no-image.png') }}"  />
                                                <input type="file" class="form-control col-md-7 col-xs-12 qc-image" name="qc_img[]" id="qc_img_1" accept=".jpg, .jpeg, .png" onchange="readURL(this, 'before');" style="margin-top:5px; margin-bottom:5px;">
                                                <input type="text" name="qc_desc[]" id="qc_desc_1" class="form-control" value="" placeholder="{{ ucwords(lang('description', $translation)) }}" />
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <?php // QC List - END ?>

                                @php
                                // Modif - BEGIN
                                $input_name = 'modif_status';
                                $style = "display:none";
                                if(isset($data->$input_name)){
                                    if($data->$input_name==1 || old($input_name)=='no_modif'){
                                        $style = "display:none";
                                    }else{
                                        $style = "";
                                    }
                                }
                                @endphp
                                <div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Modif Parts (*{{ (lang('if exists', $translation)) }})</label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <div class="radio" style="margin-bottom:5px">
                                            <label onclick="displayModifList('hide')">
                                                <input type="radio" name="modif_status" value="no_modif" class="flat" onclick="displayModifList('hide')" <?php if(isset($data->$input_name) && $data->$input_name==1){ echo 'checked'; }elseif(old($input_name)=='no_modif'){ echo 'checked'; } ?> /> {{ (lang('NO Modifications', $translation)) }}
                                            </label>
                                        </div>
                                        <div class="radio" style="margin-bottom:5px">
                                            <label onclick="displayModifList()">
                                                <input type="radio" name="modif_status" value="yes_modif" class="flat" onclick="displayModifList()" <?php if(isset($data->$input_name) && $data->$input_name==0){ echo 'checked'; }elseif(old($input_name)=='yes_modif'){ echo 'checked'; } ?> /> {{ (lang('Modif exists', $translation)) }}
                                            </label>
                                        </div>
                                    </div>
                                    <input type="hidden" name="modif_deleted" id="modif-item-deleted">
                                </div>

                                <div class="form-group" id="modif-list" style="{{$style}}">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">
                                        <b class="btn btn-primary btn-sm" onclick="addModifItem()"><i class="fa fa-plus-circle"></i> {{ (lang('Add Modif Item', $translation)) }}</b>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12" id="modif-list-item">
                                            @if (isset($data->modif_list))
                                            @php
                                            $json = json_decode($data->modif_list);
                                            $i = 0;
                                            @endphp
                                            @if($json)
                                                @foreach ($json as $item)
                                                    @php
                                                    $i++;
                                                    $delbtn = '';
                                                    if($i > 1){
                                                        $delbtn = '<b onclick="deleteModifItem('.$i.')" style="cursor:pointer;float:right;" class="btn btn-sm btn-danger"><i class="fa fa-close"></i></b>';
                                                    }
                                                    @endphp
                                                    <div class="col-md-6 col-sm-6 col-xs-12" id="modif-item-{{$i}}">
                                                        <h4>ITEM {{$i}} <?php echo $delbtn; ?></h4>
                                                        <img src="{{ asset('/uploads/product/modif/'.$item->image) }}"  />
                                                        <input type="file" class="form-control col-md-7 col-xs-12 modif-image" name="modif_img[]" id="modif_img_{{$i}}" accept=".jpg, .jpeg, .png" onchange="readURL(this, 'before');" style="margin-top:5px; margin-bottom:5px;">
                                                        <input type="text" name="modif_desc[]" id="modif_desc_{{$i}}" class="form-control" value="{{$item->description}}" placeholder="{{ ucwords(lang('description', $translation)) }}" />
                                                    </div>
                                                @endforeach
                                            @endif
                                        @else
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <h4>ITEM 1</h4>
                                                <img src="{{ asset('/images/no-image.png') }}"  />
                                                <input type="file" class="form-control col-md-7 col-xs-12 modif-image" name="modif_img[]" id="modif_img_1" accept=".jpg, .jpeg, .png" onchange="readURL(this, 'before');" style="margin-top:5px; margin-bottom:5px;">
                                                <input type="text" name="modif_desc[]" id="modif_desc_1" class="form-control" value="" placeholder="{{ ucwords(lang('description', $translation)) }}" />
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <?php // Modif - END ?>

                                @if (Helper::authorizing('Product', 'Set Sell Price')['status'] == 'true')
                                    <hr><center><label>>> {{ strtoupper(lang('set price', $translation)) }} <<</label><hr></center>

                                    @php
                                        echo set_input_form ('number_format', 'price_now', ucwords(lang('sell price', $translation)), $data, $errors, true, ucwords(lang('numeric only', $translation)), null, null, 'autocomplete="off"', null, ['Rp']);
                                    @endphp
                                @endif
                                
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
                                        @if ($incoming_items > 0)
                                            <a href="{{ route('admin_incoming_list') }}" class="btn btn-danger"><i class="fa fa-times"></i>&nbsp; {{ ucwords(lang('cancel', $translation)) }}</a>
                                        @else
                                            <a href="{{ route('admin_product_list') }}" class="btn btn-danger"><i class="fa fa-times"></i>&nbsp; {{ ucwords(lang('cancel', $translation)) }}</a>
                                        @endif
                                    </div>
                                </div>
                            </form>
                        </div>

                        @if (isset($data))
                            @if (Helper::authorizing('Product', 'Do QC')['status'] == 'true')
                                <!-- Quality Control Task List -->
                                <div id="step-2" class="">
                                    <h2 class="StepTitle">Quality Control Task List</h2>
                                    <form class="form-horizontal form-label-left" action="{{ $link2 }}" method="POST" enctype="multipart/form-data">
                                        {{ csrf_field() }}

                                        @if (isset($unit_in_tkp))
                                            <div class="form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12">{{ (lang('Unit in TKP', $translation)) }}</label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <img src="{{ $unit_in_tkp }}" class="vimg" />
                                                </div>
                                            </div>
                                        @endif

                                        @php
                                            echo set_input_form('text', 'plat_no', 'No. Plat', $data, $errors, false, 'B 1234 XYZ', null, null, 'readonly');
                                            echo set_input_form ('text', 'name', ucwords(lang('name', $translation)), $data, $errors, false, null, null, null, 'readonly', null, []);
                                            echo set_input_form ('text', 'km', (lang('KM', $translation)), $data, $errors, false, null, null, null, 'readonly', null, []);
                                            echo set_input_form ('datepicker', 'tax', ucwords(lang('tax', $translation)), $data, $errors, false, (lang('click date icon for choose the date', $translation)), null, null, 'readonly', null, []);
                                        @endphp

                                        <hr>
                
                                        @php
                                        $input_name = 'qc_list';
                                        $json = '';
                                        if(isset($data->$input_name)){
                                            // convert json
                                            $json = json_decode($data->$input_name);
                                        }
                                        $i = 0;
                                        $j = 1;
                                        @endphp
                                        @if ($json)
                                            <div class="table-responsive">
                                                <table class="table table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>{{ ucwords(lang('description', $translation)) }}</th>
                                                            <th>{{ ucwords(lang('before', $translation)) }}</th>
                                                            <th>{{ ucwords(lang('after', $translation)) }}</th>
                                                            <th>Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($json as $item)
                                                            <tr>
                                                                <td>{{$j++}}</td>
                                                                <td>{{$item->description}}</td>
                                                                <td><img src="{{ asset('uploads/product/qc/'.$item->image) }}" class="vimg" /></td>
                                                                <td>
                                                                    @php
                                                                        $img_after = asset('images/no-image.png');
                                                                        if(isset($item->image_after)){
                                                                            if(file_exists(public_path('/uploads/product/qc/'.$item->image_after))){
                                                                                $img_after = asset('uploads/product/qc/'.$item->image_after);
                                                                            }
                                                                        }
                                                                    @endphp
                                                                    <img src="{{ $img_after }}" class="vimg" />
                                                                    <input type="file" name="qc_img_after[]" accept=".jpg, .jpeg, .png" onchange="readURL(this, 'before');" style="margin-top:5px">
                                                                </td>
                                                                <td>
                                                                    <select name="qc_list_status[]">
                                                                        <option value="0" <?php if($item->status==0) echo 'selected'; ?>>Pending</option>
                                                                        <option value="1" <?php if($item->status==1) echo 'selected'; ?>>DONE</option>
                                                                        <option value="2" <?php if($item->status==2) echo 'selected'; ?>>As it is</option>
                                                                    </select>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <h2 class="text-center">{{ strtoupper(lang('no task in qc list', $translation)) }}</h2>
                                        @endif
                                        
                                        @if ($json)
                                            <div class="ln_solid"></div>
                                            <div class="form-group">
                                                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                                    <button type="submit" class="btn btn-success"><i class="fa fa-save"></i>&nbsp; {{ ucwords(lang('submit', $translation)) }}</button>
                                                    <a href="{{ route('admin_product_list') }}" class="btn btn-danger"><i class="fa fa-times"></i>&nbsp; {{ ucwords(lang('cancel', $translation)) }}</a>
                                                </div>
                                            </div>
                                        @endif
                                    </form>
                                </div>
                            @endif
                            @if (Helper::authorizing('Product', 'Upload Photos')['status'] == 'true')
                                <!-- Upload Product Photos -->
                                <div id="step-3" class="">
                                    <h2 class="StepTitle">{{ ucwords(lang('upload product photos', $translation)) }}</h2>
                                    <form class="form-horizontal form-label-left" action="{{ $link3 }}" method="POST" enctype="multipart/form-data">
                                        {{ csrf_field() }}

                                        @if (isset($unit_in_tkp))
                                            <div class="form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12">{{ (lang('Unit in TKP', $translation)) }}</label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <img src="{{ $unit_in_tkp }}" class="vimg" />
                                                </div>
                                            </div>
                                        @endif

                                        @php
                                            echo set_input_form('text', 'plat_no', 'No. Plat', $data, $errors, false, 'B 1234 XYZ', null, null, 'readonly');
                                            echo set_input_form ('text', 'name', ucwords(lang('name', $translation)), $data, $errors, false, null, null, null, 'readonly', null, []);
                                            echo set_input_form ('text', 'km', (lang('KM', $translation)), $data, $errors, false, null, null, null, 'readonly', null, []);
                                            echo set_input_form ('datepicker', 'tax', ucwords(lang('tax', $translation)), $data, $errors, false, (lang('click date icon for choose the date', $translation)), null, null, 'readonly', null, []);
                                        @endphp

                                        <hr>

                                        @php
                                        $input_name = 'images';
                                        $json = '';
                                        $list = [];
                                        if(isset($data->$input_name) && $data->$input_name != ''){
                                            // convert json
                                            $json = json_decode($data->$input_name);

                                            foreach ($json as $key => $value) {
                                                $list[$key] = $value;
                                            }
                                        }
                                        @endphp

                                        @for ($i = 1; $i <= 8; $i++)
                                            <div class="form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="image">{{ ucwords(lang('image', $translation)) }} {{ $i }}</label>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    @if(isset($list['image_'.$i]))
                                                        <div class="radio" style="margin-bottom:5px">
                                                            @php
                                                            $stats = '';
                                                            if($data->image_primary == $i){
                                                                $stats = 'checked';
                                                                $primary_img = asset('/uploads/product/'.$list['image_'.$i]);
                                                            }
                                                            @endphp
                                                            <label>
                                                                <input type="radio" name="image_primary" value="{{ $i }}" class="flat" {{ $stats }}> {{ ucwords(lang('set as primary image', $translation)) }}
                                                            </label>
                                                        </div>
                                                        <img src="{{ asset('/uploads/product/'.$list['image_'.$i]) }}" class="vimg" />
                                                    @else
                                                        <img src="{{ asset('/images/coming-soon.jpg') }}" class="vimg" />
                                                    @endif

                                                    <input type="file" id="image_{{ $i }}" class="form-control col-md-7 col-xs-12" name="image_{{ $i }}" accept=".jpg, .jpeg, .png" onchange="readURL(this, 'before');" style="margin-top:5px">
                                                </div>
                                            </div>
                                        @endfor
                                        
                                        <div class="ln_solid"></div>
                                        <div class="form-group">
                                            <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                                <button type="submit" class="btn btn-success"><i class="fa fa-upload"></i>&nbsp; {{ ucwords(lang('upload', $translation)) }}</button>
                                                <a href="{{ route('admin_product_list') }}" class="btn btn-danger"><i class="fa fa-times"></i>&nbsp; {{ ucwords(lang('cancel', $translation)) }}</a>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            @endif
                            @if (Helper::authorizing('Product', 'Publish')['status'] == 'true')
                                <!-- Publish Product Details -->
                                <div id="step-4" class="">
                                    <h2 class="StepTitle">{{ ucwords(lang('publish product details', $translation)) }}</h2>
                                    <form class="form-horizontal form-label-left" action="{{ $link4 }}" method="POST" enctype="multipart/form-data">
                                        {{ csrf_field() }}
                                        
                                        <div class="form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="image">{{ ucwords(lang('primary image', $translation)) }}</label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                @if (isset($primary_img))
                                                    <img src="{{ $primary_img }}" class="vimg" />
                                                @else 
                                                    <img src="{{ asset('/images/coming-soon.jpg') }}" class="vimg" />
                                                @endif
                                            </div>
                                        </div>

                                        @php
                                            echo set_input_form('text', 'plat_no', 'No. Plat', $data, $errors, false, 'B 1234 XYZ', null, null, 'readonly');
                                            echo set_input_form ('text', 'name', ucwords(lang('name', $translation)), $data, $errors, false, null, null, null, 'readonly', null, []);
                                            echo set_input_form ('number_format', 'price_now', ucwords(lang('sell price', $translation)), $data, $errors, false, null, null, null, 'readonly', null, []);
                                            echo set_input_form ('text', 'km', (lang('KM', $translation)), $data, $errors, false, null, null, null, 'readonly', null, []);
                                            echo set_input_form ('datepicker', 'tax', ucwords(lang('tax', $translation)), $data, $errors, false, (lang('click date icon for choose the date', $translation)), null, null, 'readonly', null, []);
                                        @endphp

                                        <hr>

                                        @php
                                        // set_input_form ($type, $input_name, $label_name, $data, $errors, $required = false, $placeholder = null, $id_name = null, $value = null, $attributes = null, $defined_data = null, $options = [])
                                            echo set_input_form('text', 'display_name', ucwords(lang('display name', $translation)), $data, $errors, true, null, null, null, null);
                                            echo set_input_form('textarea', 'summary', ucwords(lang('summary', $translation)), $data, $errors, true, null, null, null, null, null, [5]);
                                            echo set_input_form('textarea', 'description', ucwords(lang('description', $translation)), $data, $errors, true, null, null, null, null, null, [20]);
                                            echo set_input_form('switch', 'post_toped', 'Tokopedia', $data, $errors, false, null, null, null, null, null, ['unchecked']);
                                            echo set_input_form('switch', 'post_olx', 'OLX', $data, $errors, false, null, null, null, null, null, ['unchecked']);
                                        @endphp
                                        
                                        <div class="ln_solid"></div>
                                        <div class="form-group">
                                            <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i>&nbsp; 
                                                    @if (isset($data))
                                                    {{ ucwords(lang('publish', $translation)) }}
                                                    @else
                                                    {{ ucwords(lang('submit', $translation)) }}
                                                    @endif
                                                </button>
                                                <a href="{{ route('admin_product_list') }}" class="btn btn-danger"><i class="fa fa-times"></i>&nbsp; {{ ucwords(lang('cancel', $translation)) }}</a>
                                            </div>
                                        </div>
                
                                    </form>
                                </div>
                            @endif
                            @if (Helper::authorizing('Product', 'Set Booked')['status'] == 'true')
                                <!-- Set Booking Product -->
                                <div id="step-5" class="">
                                    <h2 class="StepTitle">Set Booking Product</h2>
                                    <form class="form-horizontal form-label-left" action="{{ $link5 }}" method="POST" enctype="multipart/form-data" id="form_booking">
                                        {{ csrf_field() }}

                                        <div class="form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Status</label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                @if (!empty($data->sold_date))
                                                    <span class="label label-success" style="font-size:x-large">S O L D</span>
                                                @elseif (!empty($data->booked_date))
                                                    <span class="label label-primary" style="font-size:x-large">B O O K E D</span>
                                                @else 
                                                    <span class="label label-info" style="font-size:x-large">AVAILABLE</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="image">Image</label>
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                @if (isset($primary_img))
                                                    <img src="{{ $primary_img }}" class="vimg" />
                                                @else 
                                                    <img src="{{ asset('/images/coming-soon.jpg') }}" class="vimg" />
                                                @endif
                                            </div>
                                        </div>

                                        @php
                                            echo set_input_form('text', 'plat_no', 'No. Plat', $data, $errors, false, 'B 1234 XYZ', null, null, 'readonly');
                                            echo set_input_form ('text', 'name', ucwords(lang('name', $translation)), $data, $errors, false, null, null, null, 'readonly', null, []);
                                            echo set_input_form ('number_format', 'price_now', ucwords(lang('sell price', $translation)), $data, $errors, false, null, null, null, 'readonly', null, []);
                                            echo set_input_form ('text', 'km', (lang('KM', $translation)), $data, $errors, false, null, null, null, 'readonly', null, []);
                                            echo set_input_form ('datepicker', 'tax', ucwords(lang('tax', $translation)), $data, $errors, false, (lang('click date icon for choose the date', $translation)), null, null, 'readonly', null, []);
                                        @endphp

                                        <hr>

                                        @php
                                        // set_input_form ($type, $input_name, $label_name, $data, $errors, $required = false, $placeholder = null, $id_name = null, $value = null, $attributes = null, $defined_data = null, $options = [])
                                            echo set_input_form('datepicker', 'booked_date', ucwords(lang('booked date')), $data, $errors, true, (lang('click date icon for choose the date')), null, null);
                                            echo set_input_form('hidden', 'cancel_booked', null, $data, $errors, false, null, null, null);
                                            echo set_input_form('select2', 'customer', ucwords(lang('customer')), $data, $errors, true, ucwords(lang('please choose one')), null, null, null, $customers, ['id', 'name']);
                                            echo set_input_form ('number_format', 'nominal', 'Nominal DP', $data, $errors, true, ucwords(lang('only numeric')), null, null, 'autocomplete="off"', null, ['Rp']);
                                            echo set_input_form('textarea', 'note', ucwords(lang('note')), $data, $errors, false, '*optional', null, null, null);
                                        
                                            if(!empty($data->booked_by)){
                                                echo set_input_form('text', 'booked_name', 'Set BOOKED By', $data, $errors, false, null, null, $data->booked_name, 'readonly');
                                            }
                                        @endphp
                                        
                                        <div class="ln_solid"></div>

                                        @if (!empty($data->booked_date))
                                            <div class="form-group">
                                                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                                    <span class="btn btn-warning" onclick="cancel_booking()"><i class="fa fa-refresh"></i>&nbsp; Cancel BOOKED</span>
                                                </div>
                                            </div>
                                            <div class="ln_solid"></div>
                                        @endif

                                        <div class="form-group">
                                            <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i>&nbsp; 
                                                    Set BOOKED
                                                </button>
                                                <a href="{{ route('admin_product_list') }}" class="btn btn-danger"><i class="fa fa-times"></i>&nbsp; {{ ucwords(lang('cancel')) }}</a>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
                <!-- End SmartWizard Content -->
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
    <!-- bootstrap-datetimepicker -->
    <link href="{{ asset('/admin/vendors/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css') }}" rel="stylesheet">
    <!-- jQuery Smart Wizard 4 -->
    <link href="{{ asset('/admin/vendors/jQuery-Smart-Wizard-4/dist/css/smart_wizard.css') }}" rel="stylesheet">
    <link href="{{ asset('/admin/vendors/jQuery-Smart-Wizard-4/dist/css/smart_wizard_theme_dots.css') }}" rel="stylesheet">
    <!-- Select2 -->
    <link href="{{ asset('/admin/vendors/select2/dist/css/select2.min.css') }}" rel="stylesheet">

    <style>
    .mr-2 { float: right !important; }
    #qc-list-item img, #modif-list-item img { 
        display: block;
        margin-left: auto;
        margin-right: auto;
        max-width: 200px;
        max-height: 200px;
     }
     .vimg {
        max-width: 200px;
        max-height: 200px;
     }
    </style>
@endsection

@section('script')
    <!-- iCheck -->
    <script src="{{ asset('/admin/vendors/iCheck/icheck.min.js') }}"></script>
    <!-- Switchery -->
    <script src="{{ asset('/admin/vendors/switchery/dist/switchery.min.js') }}"></script>
    <!-- jQuery Smart Wizard 4 -->
    <script src="{{ asset('/admin/vendors/jQuery-Smart-Wizard-4/dist/js/jquery.smartWizard.min.js') }}"></script>
    <!-- bootstrap-datetimepicker -->
    <script src="{{ asset('/admin/vendors/moment/min/moment.min.js') }}"></script>
    <script src="{{ asset('/admin/vendors/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js') }}"></script>
    <!-- jQuery Tags Input -->
    <script src="{{ asset('/admin/vendors/jquery.tagsinput/src/jquery.tagsinput.js') }}"></script>
    <!-- Select2 -->
    <script src="{{ asset('/admin/vendors/select2/dist/js/select2.min.js') }}"></script>

    @php
        // for set smart wizard form
        $allowed = 0;
        if(Helper::authorizing('Product', 'Purchase Details')['status'] == 'true'){
            $allowed++;
        }
        if(Helper::authorizing('Product', 'Do QC')['status'] == 'true'){
            $allowed++;
        }
        if(Helper::authorizing('Product', 'Upload Photos')['status'] == 'true'){
            $allowed++;
        }
        if(Helper::authorizing('Product', 'Publish')['status'] == 'true'){
            $allowed++;
        }
        if(Helper::authorizing('Product', 'Set Booked')['status'] == 'true'){
            $allowed++;
        }

        // set show prev-next button
        $toolbarPosition = 'none';
        if($allowed > 1 && isset($data)){
            $toolbarPosition = 'both';
        }
    @endphp
    
    <script>
        $(document).ready(function() {
            // Initialize Smart Wizard
            $('#smartwizard_product').smartWizard({
                selected: 0,  // Initial selected step, 0 = first step 
                keyNavigation:false, // Enable/Disable keyboard navigation(left and right keys are used if enabled)
                autoAdjustHeight:true, // Automatically adjust content height
                cycleSteps: false, // Allows to cycle the navigation of steps
                backButtonSupport: true, // Enable the back button support
                useURLhash: true, // Enable selection of the step based on url hash
                lang: {  // Language variables
                    next: 'Next >', 
                    previous: '< Prev'
                },
                toolbarSettings: {
                    toolbarPosition: '{{ $toolbarPosition }}', // none, top, bottom, both
                    toolbarButtonPosition: 'right', // left, right
                    showNextButton: true, // show/hide a Next button
                    showPreviousButton: true, // show/hide a Previous button
                    toolbarExtraButtons: []
                }, 
                anchorSettings: {
                    anchorClickable: true, // Enable/Disable anchor navigation
                    enableAllAnchors: false, // Activates all anchors clickable all times
                    markDoneStep: true, // add done css
                    enableAnchorOnDoneStep: true // Enable/Disable the done steps navigation
                },            
                contentURL: null, // content url, Enables Ajax content loading. can set as data data-content-url on anchor
                disabledSteps: [],    // Array Steps disabled
                errorSteps: [],    // Highlight step with errors
                hiddenSteps: [],    // an array of step numbers to be hidden
                theme: 'dots',
                transitionEffect: 'slide', // Effect on navigation, none/slide/fade
                transitionSpeed: '400'
            });
        });
        
        // Initialize datetimepicker
        $('.input-datepicker').datetimepicker({
            format: 'DD/MM/YYYY'
        });

        // Initialize Select2
        $('.select2').select2();

        function addQcItem(){
            var totalItem = $('.qc-image').length;
            totalItem++;
            var content = `<div class="col-md-6 col-sm-6 col-xs-12" id="qc-item-`+totalItem+`">
                                <h4>ITEM `+totalItem+` <b onclick="deleteQcItem(`+totalItem+`)" style="cursor:pointer;float:right;" class="btn btn-sm btn-danger"><i class="fa fa-close"></i></b></h4>
                                <img src="{{ asset('/images/no-image.png') }}"  />
                                <input type="file" class="form-control col-md-7 col-xs-12 qc-image" name="qc_img[]" accept=".jpg, .jpeg, .png" onchange="readURL(this, 'before');" style="margin-top:5px; margin-bottom:5px;" required />
                                <input type="text" name="qc_desc[]" class="form-control" value="" placeholder="{{ ucwords(lang('description', $translation)) }}" required />
                            </div>`;
            $('#qc-list-item').append(content);
        }

        function deleteQcItem(no){
            var totalItem = $('.qc-image').length;
            if(no < totalItem){
                alert('Failed to delete QC item because you can delete the last item only');
            }else if(confirm('Are you sure to delete this QC item '+no+'?')){
                $('#qc-item-'+no).remove();
                
                no--;
                if($('#qc-item-deleted').val()){
                    var deleted = $('#qc-item-deleted').val().split('|');
                    deleted.push(no);
                }else{
                    var deleted = [no];
                }
                $('#qc-item-deleted').val(deleted.join('|'));
            }
        }

        function displayQCList(state){
            if(state == 'hide'){
                $('#qc-list').hide();
                $("#qc_img_1").attr('required', false);
                $("#qc_desc_1").attr('required', false);

                // remove item 2 and more
                var totalItem = $('.qc-image').length;
                for(var i = 2; i <= totalItem; i++){
                    $('#qc-item-'+i).remove();
                }
            }else{
                $('#qc-list').show();
                $("#qc_img_1").attr('required', true);
                $("#qc_desc_1").attr('required', true);
            }
        }

        function addModifItem(){
            var totalItem = $('.modif-image').length;
            totalItem++;
            var content = `<div class="col-md-6 col-sm-6 col-xs-12" id="modif-item-`+totalItem+`">
                                <h4>ITEM `+totalItem+` <b onclick="deleteModifItem(`+totalItem+`)" style="cursor:pointer;float:right;" class="btn btn-sm btn-danger"><i class="fa fa-close"></i></b></h4>
                                <img src="{{ asset('/images/no-image.png') }}"  />
                                <input type="file" class="form-control col-md-7 col-xs-12 modif-image" name="modif_img[]" accept=".jpg, .jpeg, .png" onchange="readURL(this, 'before');" style="margin-top:5px; margin-bottom:5px;" required />
                                <input type="text" name="modif_desc[]" class="form-control" value="" placeholder="{{ ucwords(lang('description', $translation)) }}" required />
                            </div>`;
            $('#modif-list-item').append(content);
        }

        function deleteModifItem(no){
            var totalItem = $('.modif-image').length;
            if(no < totalItem){
                alert('Failed to delete modif item because you can delete the last item only');
            }else if(confirm('Are you sure to delete this modif item '+no+'?')){
                $('#modif-item-'+no).remove();
                
                no--;
                if($('#modif-item-deleted').val()){
                    var deleted = $('#modif-item-deleted').val().split('|');
                    deleted.push(no);
                }else{
                    var deleted = [no];
                }
                $('#modif-item-deleted').val(deleted.join('|'));
            }
        }

        function displayModifList(state){
            if(state == 'hide'){
                $('#modif-list').hide();
                $("#modif_img_1").attr('required', false);
                $("#modif_desc_1").attr('required', false);

                // remove item 2 and more
                var totalItem = $('.modif-image').length;
                for(var i = 2; i <= totalItem; i++){
                    $('#modif-item-'+i).remove();
                }
            }else{
                $('#modif-list').show();
                $("#modif_img_1").attr('required', true);
                $("#modif_desc_1").attr('required', true);
            }
        }

        function validateFormPurchase(){
            if(typeof $("input[name='qc_status']:checked").val() == 'undefined'){
                alert('You must choose QC List at least one');
                return false;
            }else if(typeof $("input[name='modif_status']:checked").val() == 'undefined'){
                alert('You must choose Modif at least one');
                return false;
            }
        }

        function cancel_booking()
        {
            if(confirm('Are you sure to CANCEL THIS BOOKING PRODUCT?')){
                $('#cancel_booked').val('yes');
                $('#form_booking').submit();
            }
        }
    </script>
@endsection