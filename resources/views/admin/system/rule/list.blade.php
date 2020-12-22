@extends('_template_adm.master')

@php
  // USE LIBRARIES
  use App\Libraries\Helper;
  
  $this_object = ucwords(lang('rules', $translation));

  if(isset($data)){
    $pagetitle = $this_object;
    $link_get_data = route('admin.rule.get_data');
    $function_get_data = 'refresh_data();';
  }else{
    $pagetitle = ucwords(lang('deleted #item', $translation, ['#item' => $this_object]));
    $link_get_data = route('admin.rule.get_data_deleted');
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
            @if (Helper::authorizing('Rule', 'Restore')['status'] == 'true')
              <a href="{{ route('admin.rule.deleted') }}" class="btn btn-round btn-danger" style="float: right; margin-bottom: 5px;" data-toggle="tooltip" title="{{ ucwords(lang('view deleted items', $translation)) }}">
                <i class="fa fa-trash"></i>
              </a>
            @endif
            <a href="{{ route('admin.rule.create') }}" class="btn btn-round btn-success" style="float: right;">
              <i class="fa fa-plus-circle"></i>&nbsp; {{ ucwords(lang('add new', $translation)) }}
            </a>
          </div>
        </div>
      @else
        <div class="title_right">
          <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right">
            <a href="{{ route('admin.rule.list') }}" class="btn btn-round btn-primary" style="float: right;">
              <i class="fa fa-check-circle"></i>&nbsp; {{ ucwords(lang('active items', $translation)) }}
            </a>
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
                    <th>{{ ucwords(lang('module', $translation)) }}</th>
                    <th>{{ ucwords(lang('rule', $translation)) }}</th>
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
                    <th>{{ ucwords(lang('module', $translation)) }}</th>
                    <th>{{ ucwords(lang('rule', $translation)) }}</th>
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
        order: [[ 0, "asc" ]],
        orderCellsTop: true,
        fixedHeader: false,
        serverSide: true,
        processing: true,
        ajax: "{{ $link_get_data }}",
        columns: [
          {data: 'module', name: 'sys_modules.name'},
          {data: 'name', name: 'sys_rules.name'},
          {data: 'item_status', name: 'item_status'},
          {data: 'created_at', name: 'sys_rules.created_at'},
          {data: 'updated_at', name: 'sys_rules.updated_at'},
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
          {data: 'module', name: 'sys_modules.name'},
          {data: 'name', name: 'sys_rules.name'},
          {data: 'item_status', name: 'item_status'},
          {data: 'created_at', name: 'sys_rules.created_at'},
          {data: 'deleted_at', name: 'sys_rules.deleted_at'},
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