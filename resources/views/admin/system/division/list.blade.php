@extends('_template_adm.master')

@php
  // USE LIBRARIES
  use App\Libraries\Helper;

  $this_object = ucwords(lang('office', $translation));

  if(isset($data)){
    $pagetitle = $this_object;
  }else{
    $pagetitle = ucwords(lang('deleted #item', $translation, ['#item' => $this_object]));
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
            @if (Helper::authorizing('Division', 'Restore')['status'] == 'true')
              <a href="{{ route('admin.division.deleted') }}" class="btn btn-round btn-danger" style="float: right; margin-bottom: 5px;" data-toggle="tooltip" title="{{ ucwords(lang('view deleted items', $translation)) }}">
                <i class="fa fa-trash"></i>
              </a>
            @endif
            <a href="{{ route('admin.division.create') }}" class="btn btn-round btn-success" style="float: right;">
              <i class="fa fa-plus-circle"></i>&nbsp; {{ ucwords(lang('add new', $translation)) }}
            </a>
          </div>
        </div>  
      @else
        <div class="title_right">
          <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right">
            <a href="{{ route('admin.division.list') }}" class="btn btn-round btn-primary" style="float: right;">
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
              <table class="table table-striped table-bordered">
                <thead>
                  <tr>
                    <th>{{ ucwords(lang('name', $translation)) }}</th>
                    <th>{{ ucwords(lang('status', $translation)) }}</th>
                    <th>{{ ucwords(lang('created', $translation)) }}</th>
                    @if (isset($deleted))
                      <th>{{ ucwords(lang('deleted at', $translation)) }}</th>
                    @else
                      <th>{{ ucwords(lang('last updated', $translation)) }}</th>
                    @endif
                    <th>{{ ucwords(lang('action', $translation)) }}</th>
                  </tr>
                </thead>
                @if (isset($data) && count($data) > 0)
                  <tbody class="sorted_table">
                    @foreach ($data as $item)
                      <tr role="row" id="row-{{ $item->id }}" title="{{ ucfirst(lang("Drag & drop to sorting", $translation)) }}" data-toggle="tooltip">
                        <td class="dragndrop">{{ $item->name }}</td>
                        <td>
                          @if ($item->status != 1)
                            <span class="label label-danger"><i>{{ ucwords(lang('disabled', $translation)) }}</i></span>
                          @else
                            <span class="label label-success">{{ ucwords(lang('enabled', $translation)) }}</span>
                          @endif
                        </td>
                        <td>{{ $item->created_at }}</td>
                        <td>{{ Helper::time_ago(strtotime($item->updated_at), lang('ago', $translation), Helper::get_periods($translation)) }}</td>
                        <td>
                          <a href="{{ route('admin.division.edit', $item->id) }}" class="btn btn-xs btn-primary" title="{{ ucwords(lang('edit', $translation)) }}">
                            <i class="fa fa-pencil"></i>&nbsp; {{ ucwords(lang('edit', $translation)) }}
                          </a>
                          <form action="{{ route('admin.division.delete') }}" method="POST" onsubmit="return confirm('{{ lang('Are you sure to delete this #item?', $translation, ['#item'=>$this_object]) }}');" style="display: inline">
                            {{ csrf_field() }}
                            <input type="hidden" name="id" value="{{ $item->id }}">
                            <button type="submit" class="btn btn-xs btn-danger" title="{{ ucwords(lang('delete', $translation)) }}">
                              <i class="fa fa-trash"></i>&nbsp; {{ ucwords(lang('delete', $translation)) }}
                            </button>
                          </form>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                @elseif(isset($deleted) && count($deleted) > 0)
                  <tbody>
                    @foreach ($deleted as $item)
                      <tr>
                        <td>{{ $item->name }}</td>
                        <td>
                          @if ($item->status != 1)
                            <span class="label label-danger"><i>{{ ucwords(lang('disabled', $translation)) }}</i></span>
                          @else
                            <span class="label label-success">{{ ucwords(lang('enabled', $translation)) }}</span>
                          @endif
                        </td>
                        <td>{{ $item->created_at }}</td>
                        <td>{{ Helper::time_ago(strtotime($item->deleted_at), lang('ago', $translation), Helper::get_periods($translation)) }}</td>
                        <td>
                          <form action="{{ route('admin.division.restore') }}" method="POST" onsubmit="return confirm('{{ lang('Are you sure to restore this #item?', $translation, ['#item'=>$this_object]) }}');">
                            {{ csrf_field() }}
                            <input type="hidden" name="id" value="{{ $item->id }}">
                            <button type="submit" class="btn btn-xs btn-primary" title="{{ ucwords(lang('restore', $translation)) }}">
                              <i class="fa fa-check"></i>&nbsp; {{ ucwords(lang('restore', $translation)) }}
                            </button>
                          </form>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                @else
                  <tbody>
                    <tr>
                      <td colspan="5"><h2 class="text-center">{{ strtoupper(lang('no data available', $translation)) }}</h2></td>
                    </tr>
                  </tbody>
                @endif
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('css')
  <!-- Sortable-Table -->
  @include('_form_element.sortable_table.css')
@endsection

@section('script')
  <script>
    var AjaxSortingURL = '{{ route("admin.division.sorting") }}';
  </script>
  <!-- Sortable-Table -->
  @include('_form_element.sortable_table.script')
@endsection