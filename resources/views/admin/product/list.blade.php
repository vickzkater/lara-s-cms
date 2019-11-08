@extends('_template_adm.master')

@php
if(isset($data)){
  $pagetitle = 'Product'; 
}else{
  $pagetitle = 'Deleted Product'; 
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
                <a href="{{ route('admin_product_create') }}" class="btn btn-round btn-success" style="float: right;">Add New</a>
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
    
    <form id="search_data" method="GET" action="{{ route('admin_product_list') }}" style="display:none">
      <input type="text" name="search" id="search_value" value="{{ $search }}">
    </form>

    <div class="clearfix"></div>
    
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2><?php if(isset($deleted)) echo 'Deleted '; ?>Product Motor List</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                  <div class="table-responsive">
                    <table class="table table-striped">
                      <thead>
                        <tr>
                          <th>#</th>
                          <th colspan="2">Action</th>
                          <th>Image</th>
                          <th>Name</th>
                          <th>Branch</th>
                          <th>Sell Price</th>
                          <th>QC</th>
                          <th>Photos</th>
                          <th>Published</th>
                          <th>Status</th>
                          <th>Last Updated</th>
                          <th>Delete</th>
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
                              @php
                              $image = asset('/images/coming-soon.jpg');
                              if(isset($item->images) && $item->images != ''){
                                // convert json
                                $json = json_decode($item->images);

                                foreach ($json as $key => $value) {
                                  $list[$key] = $value;
                                }

                                if(isset($list['image_'.$item->image_primary])){
                                  $image = asset('/uploads/product/'.$list['image_'.$item->image_primary]);
                                }
                              }
                              @endphp
                              <tr>
                                <th scope="row">{{ $i }}</th>
                                <td>
                                  <a href="{{ route('admin_product_edit', $item->id.'#step-1') }}">
                                    <button type="button" class="btn btn-sm btn-info">Purchase</button>
                                  </a>
                                  <br>
                                  <a href="{{ route('admin_product_edit', $item->id.'#step-2') }}">
                                    <button type="button" class="btn btn-sm btn-info">&nbsp;QC Task&nbsp;</button>
                                  </a>
                                  <hr>
                                  <a href="{{ route('admin_product_edit', $item->id.'#step-5') }}">
                                    <button type="button" class="btn btn-sm btn-primary">BOOKED&nbsp;</button>
                                  </a>
                                  {{-- <div class="btn-group">
                                    <button data-toggle="dropdown" class="btn btn-primary dropdown-toggle btn-sm" type="button">Edit Product <span class="caret"></span></button>
                                    <ul role="menu" class="dropdown-menu">
                                      <li><a href="{{ route('admin_product_edit', $item->id.'#step-1') }}">Purchase Details</a>
                                      </li>
                                      <li><a href="{{ route('admin_product_edit', $item->id.'#step-2') }}">QC List</a>
                                      </li>
                                      <li><a href="{{ route('admin_product_edit', $item->id.'#step-3') }}">Upload Photos</a>
                                      </li>
                                      <li><a href="{{ route('admin_product_edit', $item->id.'#step-4') }}">Publish</a>
                                      </li>
                                      <li><a href="{{ route('admin_product_edit', $item->id.'#step-5') }}">*BOOKING</a>
                                      </li>
                                      <li><a href="{{ route('admin_product_edit', $item->id.'#step-6') }}">**SOLD</a>
                                      </li>
                                      <li class="divider"></li>
                                      <li><a href="#" onclick="if(confirm('Are you sure to delete this product?')) window.location.replace('{{ route('admin_product_delete', $item->id) }}');">Delete</a>
                                      </li>
                                    </ul>
                                  </div> --}}
                                </td>
                                <td>
                                  <a href="{{ route('admin_product_edit', $item->id.'#step-3') }}">
                                    <button type="button" class="btn btn-sm btn-info">Upload</button>
                                  </a>
                                  <br>
                                  <a href="{{ route('admin_product_edit', $item->id.'#step-4') }}">
                                    <button type="button" class="btn btn-sm btn-info">Publish</button>
                                  </a>
                                  <hr>
                                  <a href="{{ route('admin_product_edit', $item->id.'#step-6') }}">
                                    <button type="button" class="btn btn-sm btn-success">&nbsp;&nbsp;SOLD&nbsp;&nbsp;</button>
                                  </a>
                                </td>
                                <td><img src="{{ $image }}" style="max-width: 100px;" /></td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->division . ' - ' . $item->branch_name }}</td>
                                <td>{{ $item->currency.' '.number_format($item->price_now, 0, ',', '.') }},-</td>
                                <td>
                                  <a href="{{ route('admin_product_edit', $item->id.'#step-2') }}" title="Go to QC Task List">
                                  @if ($item->qc_status != 1)
                                  <span class="label label-danger"><i>Not Yet</i></span>
                                  @else
                                  <span class="label label-success">DONE</span>
                                  @endif
                                  </a>
                                </td>
                                <td>
                                  <a href="{{ route('admin_product_edit', $item->id.'#step-3') }}" title="Go to Upload Photos">
                                  @if ($item->photo_status != 1)
                                  <span class="label label-danger"><i>Not Yet</i></span>
                                  @else
                                  <span class="label label-success">Uploaded</span>
                                  @endif
                                  </a>
                                </td>
                                <td>
                                  <a href="{{ route('admin_product_edit', $item->id.'#step-4') }}" title="Go to Publish Details">
                                  @if ($item->publish_status != 1)
                                  <span class="label label-danger"><i>Not Yet</i></span>
                                  @else
                                  <span class="label label-success">Published</span>
                                  @endif
                                  </a>
                                </td>
                                <td>
                                  @if (!empty($item->sold_date))
                                    <span class="label label-success">SOLD</span>
                                  @elseif (!empty($item->booked_date))
                                    <span class="label label-primary"><i>BOOKED</i></span>
                                  @else
                                    <span class="label label-info">Available</span>
                                  @endif
                                </td>
                                <td>{{ $item->updated_at }}</td>
                                <td>
                                  <button type="button" class="btn btn-md btn-danger" onclick="if(confirm('Are you sure to delete this product?')) window.location.replace('{{ route('admin_product_delete', $item->id) }}');">
                                    <i class="fa fa-trash"></i>
                                  </button>
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
                              @php
                              $image = asset('/images/coming-soon.jpg');
                              if(isset($item->images) && $item->images != ''){
                                // convert json
                                $json = json_decode($item->images);

                                foreach ($json as $key => $value) {
                                  $list[$key] = $value;
                                }

                                if(isset($list['image_'.$item->image_primary])){
                                  $image = asset('/uploads/product/'.$list['image_'.$item->image_primary]);
                                }
                              }
                              @endphp
                              <tr>
                                <th scope="row">{{ $i }}</th>
                                <td colspan="2">
                                  <button class="btn btn-sm btn-success" title="Restore" onclick="if(confirm('Are you sure to restore this product?')) window.location.replace('{{ route('admin_product_restore', $item->id) }}');"><i class="fa fa-check"></i></button>
                                </td>
                                <td><img src="{{ $image }}" style="max-width: 100px;" /></td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->branch_name }}</td>
                                <td>{{ $item->currency.' '.number_format($item->purchase_price, 0, ',', '.') }},-</td>
                                <td>
                                  @if ($item->qc_status != 1)
                                  <span class="label label-danger"><i>Not Yet</i></span>
                                  @else
                                  <span class="label label-success">DONE</span>
                                  @endif
                                </td>
                                <td>
                                    @if ($item->photo_status != 1)
                                    <span class="label label-danger"><i>Not Yet</i></span>
                                    @else
                                    <span class="label label-success">Uploaded</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($item->publish_status != 1)
                                    <span class="label label-danger"><i>Not Yet</i></span>
                                    @else
                                    <span class="label label-success">Published</span>
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
                                <td>-</td>
                              </tr>
                              @php
                                  $i++;
                              @endphp
                            @endforeach
                        @else
                          <tr>
                            <td colspan="13"><h2 class="text-center">NO DATA AVAILABLE</h2></td>
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