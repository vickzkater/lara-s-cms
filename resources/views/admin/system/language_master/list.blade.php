@extends('_template_adm.master')

@php
  $pagetitle = ucwords(lang('dictionary', $translation));
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
          
        <div class="title_right">
          <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right">
            <a href="{{ route('admin.langmaster.create') }}" class="btn btn-round btn-success" style="float: right;">
              <i class="fa fa-plus-circle"></i>&nbsp; {{ ucwords(lang('add new', $translation)) }}
            </a>
          </div>
        </div>
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
                <table id="datatables" class="table table-striped table-bordered">
                  <thead>
                    <tr>
                      <th>{{ ucwords(lang('phrase', $translation)) }}</th>
                      <th>{{ ucwords(lang('status', $translation)) }}</th>
                      <th>{{ ucwords(lang('created', $translation)) }}</th>
                      <th>{{ ucwords(lang('last updated', $translation)) }}</th>
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
      refresh_data();
    });

    function refresh_data() {
      $('#datatables').dataTable().fnDestroy();
      var table = $('#datatables').DataTable({
          orderCellsTop: true,
          fixedHeader: false,
          serverSide: true,
          processing: true,
          ajax: "{{ route('admin.langmaster.get_data') }}",
          columns: [
              {data: 'phrase', name: 'phrase'},
              {data: 'status', name: 'status'},
              {data: 'created_at', name: 'created_at'},
              {data: 'updated_at', name: 'updated_at'},
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