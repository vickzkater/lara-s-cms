@extends('_template_adm.master')

@php
  // USE LIBRARIES
  use App\Libraries\Helper;

  if(isset($data)){
    $pagetitle = ucwords(lang('language', $translation));
  }else{
    $pagetitle = ucwords(lang('deleted #item', $translation, ['#item' => ucwords(lang('language', $translation))]));
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
            <a href="{{ route('admin.language.create') }}" class="btn btn-round btn-success" style="float: right;">
              <i class="fa fa-plus-circle"></i>&nbsp; {{ ucwords(lang('add new', $translation)) }}
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
                    <th>#</th>
                    <th>{{ ucwords(lang('language', $translation)) }}</th>
                    <th>{{ ucwords(lang('alias', $translation)) }}</th>
                    <th>{{ ucwords(lang('status', $translation)) }}</th>
                    <th>{{ ucwords(lang('created', $translation)) }}</th>
                    <th>{{ ucwords(lang('last updated', $translation)) }}</th>
                    <th>{{ ucwords(lang('action', $translation)) }}</th>
                  </tr>
                </thead>
                <tbody>
                  @if (isset($data) && count($data) > 0)
                    @php
                      $i = 1;
                      $perpage = 10;
                      if(isset($_GET['page'])){
                        $i = ($_GET['page'] - 1) * $perpage + $i;
                      }
                    @endphp
                    @foreach ($data as $item)
                      <tr>
                        <th scope="row">{{ $i }}</th>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->alias }}</td>
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
                          <a href="{{ route('admin.language.edit', $item->id) }}" class="btn btn-xs btn-primary" title="{{ ucwords(lang('edit', $translation)) }}"><i class="fa fa-pencil"></i>&nbsp; {{ ucwords(lang('edit', $translation)) }}</a>
                        </td>
                      </tr>
                      @php
                        $i++;
                      @endphp
                    @endforeach
                  @else
                    <tr>
                      <td colspan="7"><h2 class="text-center">{{ strtoupper(lang('no data available', $translation)) }}</h2></td>
                    </tr>
                  @endif
                </tbody>
              </table>
            </div>
            
            <div class="pull-right">
              @if(isset($data))
                {{ $data->appends(request()->input())->links() }}
              @endif
              @if(isset($deleted))
                {{ $deleted->appends(request()->input())->links() }}
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection