{{-- ADD HTML SMALL MODAL - BEGIN --}}
@extends('_template_adm.modal_small')
{{-- SMALL MODAL CONFIG --}}
@section('small_modal_title', ucwords(lang('import', $translation)).' Excel')
@section('small_modal_content')
  <label>{{ lang('Browse the file', $translation) }}</label>
  <div class="form-group">
    <input type="file" name="file" required="required" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
  </div>
@endsection
@section('small_modal_btn_label', ucwords(lang('import', $translation)))
@section('small_modal_btn_onclick', "$('.btn-submit').addClass('disabled');$('.btn-submit').html('<i class=\"fa fa-spin fa-spinner\"></i>&nbsp; ".lang('Loading, please wait..', $translation)."');")
@section('small_modal_form', true)
@section('small_modal_method', 'POST')
@section('small_modal_url', route('admin.product.import_excel'))
{{-- ADD HTML SMALL MODAL - END --}}

@extends('_template_adm.master')

@php
  // USE LIBRARIES
  use App\Libraries\Helper;

  $this_object = ucwords(lang('product', $translation));
  $this_module = 'Product';

  if(isset($data)){
    $pagetitle = $this_object;
    $link_get_data = route('admin.product.get_data');
    $function_get_data = 'refresh_data();';
  }else{
    $pagetitle = ucwords(lang('deleted #item', $translation, ['#item' => $this_object]));
    $link_get_data = route('admin.product.get_data_deleted');
    $function_get_data = 'refresh_data_deleted();';
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

      @if (isset($data))
        <div class="title_right">
          <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right">
            @if (Helper::authorizing('Product', 'Restore')['status'] == 'true')
              <a href="{{ route('admin.product.deleted') }}" class="btn btn-round btn-danger" style="float: right; margin-bottom: 5px;" data-toggle="tooltip" title="{{ ucwords(lang('view deleted items', $translation)) }}">
                <i class="fa fa-trash"></i>
              </a>
            @endif
            <a href="{{ route('admin.product.create') }}" class="btn btn-round btn-success" style="float: right;">
              <i class="fa fa-plus-circle"></i>&nbsp; {{ ucwords(lang('add new', $translation)) }}
            </a>
          </div>
        </div>
      @else
        <div class="title_right">
          <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right">
            <a href="{{ route('admin.product.list') }}" class="btn btn-round btn-primary" style="float: right;">
              <i class="fa fa-check-circle"></i>&nbsp; {{ ucwords(lang('active items', $translation)) }}
            </a>
          </div>
        </div>  
      @endif

      @if (isset($data))
        <div class="title_left">
          @if (Helper::authorizing($this_module, 'Export Excel')['status'] == 'true')
            <a href="{{ route('admin.product.export_excel') }}" class="btn btn-round btn-warning" style="margin: 10px 0;" target="_blank">
              <i class="fa fa-cloud-download"></i>&nbsp; {{ ucwords(lang('export', $translation)) }} Excel
            </a>
          @else
            &nbsp;
          @endif

          @if (Helper::authorizing($this_module, 'Import Excel')['status'] == 'true')
            <a href="{{ route('admin.product.import_excel_template') }}" class="btn btn-round btn-info" style="margin: 10px 0;" target="_blank">
              <i class="fa fa-download"></i>&nbsp; {{ lang('Download template for Import', $translation) }} Excel
            </a>
          @endif
        </div>
        <div class="title_right">
          <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right">
            @if (Helper::authorizing($this_module, 'Import Excel')['status'] == 'true')
              <button type="button" class="btn btn-primary btn-round" data-toggle="modal" data-target=".bs-modal-sm" style="float: right;">
                <i class="fa fa-cloud-upload"></i>&nbsp; {{ ucwords(lang('import', $translation)) }} Excel
              </button>
            @else
              &nbsp;
            @endif
          </div>
        </div>
      @endif
    </div>

    <div class="clearfix"></div>

    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>{{ ucwords(lang('data list', $translation)) }}</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <div class="table-responsive">
              <table id="datatables" class="table table-striped table-bordered" style="display:none">
                <thead>
                  <tr>
                    <th>{{ ucwords(lang('title', $translation)) }}</th>
                    <th>{{ ucwords(lang('subtitle', $translation)) }}</th>
                    <th>{{ ucwords(lang('image', $translation)) }}</th>
                    <th>{{ ucwords(lang('status', $translation)) }}</th>
                    <th>{{ ucwords(lang('created', $translation)) }}</th>
                    <th>{{ ucwords(lang('last updated', $translation)) }}</th>
                    <th>{{ ucwords(lang('action', $translation)) }}</th>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>

              <table id="datatables-deleted" class="table table-striped table-bordered" style="display:none">
                <thead>
                  <tr>
                    <th>{{ ucwords(lang('title', $translation)) }}</th>
                    <th>{{ ucwords(lang('subtitle', $translation)) }}</th>
                    <th>{{ ucwords(lang('image', $translation)) }}</th>
                    <th>{{ ucwords(lang('status', $translation)) }}</th>
                    <th>{{ ucwords(lang('created', $translation)) }}</th>
                    <th>{{ ucwords(lang('deleted at', $translation)) }}</th>
                    <th>{{ ucwords(lang('action', $translation)) }}</th>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('script')
  <!-- Datatables -->
  @include('_form_element.datatables.script')

  <script>
    $(document).ready(function() {
      {{ $function_get_data }}
    });

    function refresh_data() {
      $('#datatables').show();
      $('#datatables').dataTable().fnDestroy();
      var table = $('#datatables').DataTable({
        order: [[ 4, "desc" ]],
        orderCellsTop: true,
        fixedHeader: false,
        serverSide: true,
        processing: true,
        ajax: "{{ $link_get_data }}",
        columns: [
            {data: 'title', name: 'products.title'},
            {data: 'subtitle', name: 'products.subtitle'},
            {data: 'image_item', name: 'image_item'},
            {data: 'item_status', name: 'item_status'},
            {data: 'created_at', name: 'products.created_at'},
            {data: 'updated_at', name: 'products.updated_at'},
            {data: 'action', name: 'action'},
        ]
      });
    }

    function refresh_data_deleted() {
      $('#datatables-deleted').show();
      $('#datatables-deleted').dataTable().fnDestroy();
      var table = $('#datatables-deleted').DataTable({
        order: [[ 0, "asc" ]],
        orderCellsTop: true,
        fixedHeader: false,
        serverSide: true,
        processing: true,
        ajax: "{{ $link_get_data }}",
        columns: [
            {data: 'title', name: 'products.title'},
            {data: 'subtitle', name: 'products.subtitle'},
            {data: 'image_item', name: 'image_item'},
            {data: 'item_status', name: 'item_status'},
            {data: 'created_at', name: 'products.created_at'},
            {data: 'deleted_at', name: 'products.deleted_at'},
            {data: 'action', name: 'action'},
        ]
      });
    }
  </script>
@endsection

@section('css')
  <!-- Datatables -->
  @include('_form_element.datatables.css')
@endsection