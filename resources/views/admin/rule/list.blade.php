@extends('_template_adm.master')

@php
if(isset($data)){
  $pagetitle = 'Rule List'; 
}else{
  $pagetitle = 'Deleted rules'; 
}

$search = '';
if(isset($_GET['search'])){
  $search = $_GET['search'];
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
                <a href="{{ route('admin_rule_create') }}" class="btn btn-round btn-success" style="float: right;">Add New</a>
            </div>
            <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right top_search">
              <div class="input-group">
                <input type="text" id="search_keyword" value="{{ $search }}" class="form-control" placeholder="Search for...">
                <span class="input-group-btn">
                  <button class="btn btn-default" type="button" onclick="search_data()">Go!</button>
                </span>
              </div>
            </div>
          </div>  
        @endif
    </div>

    <form id="search_data" method="GET" action="{{ route('admin_rule_list') }}" style="display:none">
      <input type="text" name="search" id="search_value" value="{{ $search }}">
    </form>
    
    <div class="clearfix"></div>
    
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2><?php if(isset($deleted)) echo 'Deleted '; ?>Rule List</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                  <div class="table-responsive">
                    <table class="table table-striped">
                      <thead>
                        <tr>
                          <th>#</th>
                          <th>Module</th>
                          <th>Rule</th>
                          <th>Status</th>
                          <th>Created</th>
                          <th>Last Updated</th>
                          <th>Action</th>
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
                                <td>{{ $item->module }}</td>
                                <td>{{ $item->name }}</td>
                                <td>
                                  @if ($item->status != 1)
                                  <span class="label label-danger"><i>Disabled</i></span>
                                  @else
                                  <span class="label label-success">Enabled</span>
                                  @endif
                                </td>
                                <td>{{ $item->created_at }}</td>
                                <td>{{ $item->updated_at }}</td>
                                <td>
                                  <a href="{{ route('admin_rule_edit', $item->id) }}" class="btn btn-xs btn-primary" title="Edit"><i class="fa fa-pencil"></i></a>
                                  <button class="btn btn-xs btn-danger" title="Delete" onclick="if(confirm('Are you sure to delete this rule?')) window.location.replace('{{ route('admin_rule_delete', $item->id) }}');"><i class="fa fa-trash"></i></button>
                                </td>
                              </tr>
                              @php
                                  $i++;
                              @endphp
                            @endforeach
                        @elseif(isset($deleted) && count($deleted) > 0)
                          @php
                            $i = 1;
                            $perpage = 10;
                            if(isset($_GET['page'])){
                              $i = ($_GET['page'] - 1) * $perpage + $i;
                            }
                          @endphp
                            @foreach ($deleted as $item)
                            <tr>
                              <th scope="row">{{ $i }}</th>
                              <td>{{ $item->module }}</td>
                              <td>{{ $item->name }}</td>
                              <td>
                                @if ($item->status != 1)
                                <span class="label label-danger"><i>Disabled</i></span>
                                @else
                                <span class="label label-success">Enabled</span>
                                @endif
                              </td>
                              <td>{{ $item->created_at }}</td>
                              <td>{{ $item->updated_at }}</td>
                              <td>
                                <button class="btn btn-xs btn-success" title="Restore" onclick="if(confirm('Are you sure to restore this rule?')) window.location.replace('{{ route('admin_rule_restore', $item->id) }}');"><i class="fa fa-check"></i></button>
                              </td>
                            </tr>
                            @php
                                $i++;
                            @endphp
                          @endforeach
                        @else
                          <tr>
                            <td colspan="7"><h2 class="text-center">NO DATA AVAILABLE</h2></td>
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

@section('script')
    <script>
    function search_data()
    {
      $('#search_value').val($('#search_keyword').val());
      $('#search_data').submit();
    }
    </script>
@endsection