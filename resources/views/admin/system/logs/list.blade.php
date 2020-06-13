@extends('_template_adm.master')

@php
  // LIBRARIES
  use App\Libraries\Helper;

  $pagetitle = ucwords(lang('system logs', $translation));
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
            <h2>{{ ucwords(lang('data list', $translation)) }}</h2>
            <div class="clearfix"></div>
          </div>

          <div class="x_content">
            <div class="table-responsive">
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>{{ ucwords(lang('user', $translation)) }}</th>
                    <th>{{ ucwords(lang('action', $translation)) }}</th>
                    <th>{{ ucwords(lang('timestamp', $translation)) }}</th>
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
                        <td>{{ $item->username }}</td>
                        <td>
                          @php
                          if (empty($item->object)) {
                            echo $item->act_name;
                          } else {
                            if(in_array($item->action, [4,5,6,7])) {
                              // user
                              echo '<a href="'.route('admin.user.edit', $item->object).'">'.$item->act_name.'</a>';
                            } else if(in_array($item->action, [9, 10, 11, 12])) {
                              // division
                              echo '<a href="'.route('admin.division.edit', $item->object).'">'.$item->act_name.'</a>';
                            } else if(in_array($item->action, [13, 14, 15, 16])) {
                              // branch
                              echo '<a href="'.route('admin.branch.edit', $item->object).'">'.$item->act_name.'</a>';
                            } else if(in_array($item->action, [17, 18, 19, 20])) {
                              // rule
                              echo '<a href="'.route('admin.rule.edit', $item->object).'">'.$item->act_name.'</a>';
                            } else if(in_array($item->action, [21, 22, 23, 24])) {
                              // usergroup
                              echo '<a href="'.route('admin.usergroup.edit', $item->object).'">'.$item->act_name.'</a>';
                            } else if(in_array($item->action, [25, 26, 27, 28])) {
                              // brand
                              echo '<a href="'.route('admin_brand_edit', $item->object).'">'.$item->act_name.'</a>';
                            } else if(in_array($item->action, [29, 30])) {
                              // language
                              echo '<a href="'.route('admin.language.edit', $item->object).'">'.$item->act_name.'</a>';
                            }
                          }
                          @endphp
                        </td>
                        <td>
                          {{ $item->updated_at }}
                          ({{ Helper::time_ago(strtotime($item->updated_at), lang('ago', $translation), Helper::get_periods($translation)) }})
                        </td>
                      </tr>
                      @php
                          $i++;
                      @endphp
                    @endforeach
                  @else
                    <tr>
                      <td colspan="4"><h2 class="text-center">{{ strtoupper(lang('no data available', $translation)) }}</h2></td>
                    </tr>
                  @endif
                </tbody>
              </table>
            </div>
            
            <div class="pull-right">
              @if(isset($data))
                {{ $data->appends(request()->input())->links() }}
              @endif
              </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection