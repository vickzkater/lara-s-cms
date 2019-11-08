@extends('_template_adm.master')

@php
if(isset($data)){
  $pagetitle = 'Customer'; 
}else{
  $pagetitle = 'Deleted Customer'; 
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
                <a href="{{ route('admin_customer_create') }}" class="btn btn-round btn-success" style="float: right;">Add New</a>
            </div>
          </div>  
        @endif
    </div>
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2><?php if(isset($deleted)) echo 'Deleted '; ?>Customer List</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                  <div class="table-responsive">
                    <table class="table table-striped">
                      <thead>
                        <tr>
                          <th>#</th>
                          <th>Name</th>
                          <th>Phone</th>
                          <th>Email</th>
                          <th>Status</th>
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
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->phone }}</td>
                                <td>
                                  @if (!empty($item->email))
                                  {{ $item->email }}
                                  @else
                                  -
                                  @endif
                                </td>
                                <td>
                                  @if ($item->status != 1)
                                  <span class="label label-danger"><i>Disabled</i></span>
                                  @else
                                  <span class="label label-success">Enabled</span>
                                  @endif
                                </td>
                                <td>{{ $item->updated_at }}</td>
                                <td>
                                  <a href="{{ route('admin_customer_edit', $item->id) }}" class="btn btn-xs btn-primary" title="Edit"><i class="fa fa-pencil"></i></a>
                                  <button class="btn btn-xs btn-danger" title="Delete" onclick="if(confirm('Are you sure to delete this customer?')) window.location.replace('{{ route('admin_customer_delete', $item->id) }}');"><i class="fa fa-trash"></i></button>
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
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->phone }}</td>
                                <td>
                                  @if (!empty($item->email))
                                  {{ $item->email }}
                                  @else
                                  -
                                  @endif
                                </td>
                                <td>
                                  @if ($item->status != 1)
                                  <span class="label label-danger"><i>Disabled</i></span>
                                  @else
                                  <span class="label label-success">Enabled</span>
                                  @endif
                                </td>
                                <td>{{ $item->updated_at }}</td>
                                <td>
                                  <button class="btn btn-xs btn-success" title="Restore" onclick="if(confirm('Are you sure to restore this customer?')) window.location.replace('{{ route('admin_customer_restore', $item->id) }}');"><i class="fa fa-check"></i></button>
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