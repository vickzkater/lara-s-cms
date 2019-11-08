@extends('_template_adm.master')

@php
if(isset($data)){
  $pagetitle = 'Incoming Unit'; 
}else{
  $pagetitle = 'Deleted Incoming Unit'; 
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
    </div>
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2><?php if(isset($deleted)) echo 'Deleted '; ?>Incoming Unit List</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                  <div class="table-responsive">
                    <table class="table table-striped">
                      <thead>
                        <tr>
                          <th>#</th>
                          <th>Unit in TKP</th>
                          <th>Name</th>
                          <th>Branch</th>
                          <th>Purchase Price</th>
                          <th>Seller</th>
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
                              @php
                              $image = asset('/images/coming-soon.jpg');
                              if(isset($item->unit_in_tkp) && $item->unit_in_tkp != ''){
                                $image = asset('/uploads/product/seller/'.$item->unit_in_tkp);
                              }
                              @endphp
                              <tr>
                                <th scope="row">{{ $i }}</th>
                                <td><img src="{{ $image }}" style="max-width: 100px;" /></td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->branch_name }}</td>
                                <td>{{ $item->currency.' '.number_format($item->purchase_price, 0, ',', '.') }},-</td>
                                <td>{{ $item->seller_name }}</td>
                                <td>{{ $item->created_at }}</td>
                                <td>{{ $item->updated_at }}</td>
                                <td>
                                  <a href="{{ route('admin_product_edit', $item->id.'#step-1') }}" class="btn btn-success">Set Price</a>
                                </td>
                              </tr>
                              @php
                                  $i++;
                              @endphp
                            @endforeach
                        @else
                          <tr>
                            <td colspan="9"><h2 class="text-center">NO DATA AVAILABLE</h2></td>
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