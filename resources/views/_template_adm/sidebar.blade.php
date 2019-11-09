@php
// library
use App\Libraries\Helper;

$incoming_items = Helper::get_total_incoming();

$badge_incoming = '';
if($incoming_items > 0){
    $badge_incoming = '<span class="label label-warning pull-right">'.$incoming_items.' unit motor</span>';
}
@endphp

<!-- sidebar menu -->
<div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
    <div class="menu_section">
        <h3>{{ ucwords(lang('general', $translation)) }}</h3>
        <ul class="nav side-menu">
            <li><a href="{{ route('admin_home') }}"><i class="fa fa-dashboard"></i> {{ ucwords(lang('dashboard', $translation)) }}</a></li>
            <li><a href="{{ route('admin_profile') }}"><i class="fa fa-user"></i> {{ ucwords(lang('profile', $translation)) }}</a></li>
            
            @if (Helper::authorizing('Customer', 'View List')['status'] == 'true')
                <li><a href="{{ route('admin_customer_list') }}"><i class="fa fa-child"></i> {{ ucwords(lang('customer', $translation)) }}</a></li>
            @endif
            @if (Helper::authorizing('Incoming', 'View List')['status'] == 'true')
                <li><a href="{{ route('admin_incoming_list') }}"><i class="fa fa-download"></i> {{ ucwords(lang('incoming', $translation)) }} <?php echo $badge_incoming; ?></a></li>
            @endif
            @if (Helper::authorizing('Product', 'View List')['status'] == 'true')
                <li><a href="{{ route('admin_product_list') }}"><i class="fa fa-cube"></i> {{ ucwords(lang('product', $translation)) }}</a></li>
            @endif
            @if (Helper::authorizing('Brand', 'View List')['status'] == 'true')
                <li><a href="{{ route('admin_brand_list') }}"><i class="fa fa-trademark"></i> {{ ucwords(lang('brand', $translation)) }}</a></li>
            @endif
            @if (Helper::authorizing('Banner', 'View List')['status'] == 'true')
                <li><a href="{{ route('admin_banner_list') }}"><i class="fa fa-image"></i> {{ ucwords(lang('banner', $translation)) }}</a></li>
            @endif
        </ul>
    </div>

    <?php $priv_admin = 0; ?>
    <div class="menu_section" id="navmenu_admin" style="display:none">
        <h3>{{ ucwords(lang('administration', $translation)) }}</h3>
        <ul class="nav side-menu">
            @if (Helper::authorizing('System Logs', 'View List')['status'] == 'true')
                <?php $priv_admin++; ?>
                <li><a href="javascript:void(0)"><i class="fa fa-exchange"></i> {{ ucwords(lang('system logs', $translation)) }}</a></li>
            @endif
            @if (Helper::authorizing('User Manager', 'View List')['status'] == 'true')
                <?php $priv_admin++; ?>
                <li><a href="{{ route('admin_user_manager') }}"><i class="fa fa-user"></i> {{ ucwords(lang('user manager', $translation)) }}</a></li>
            @endif
            @if (Helper::authorizing('Usergroup Manager', 'View List')['status'] == 'true')
                <?php $priv_admin++; ?>
                <li><a href="{{ route('admin_group_manager') }}"><i class="fa fa-users"></i> {{ ucwords(lang('usergroup manager', $translation)) }}</a></li>
            @endif
            @if (Helper::authorizing('Rule', 'View List')['status'] == 'true')
                <?php $priv_admin++; ?>
                <li><a href="{{ route('admin_rule_list') }}"><i class="fa fa-gavel"></i> {{ ucwords(lang('rule manager', $translation)) }}</a></li>
            @endif
            @if (Helper::authorizing('Division', 'View List')['status'] == 'true')
                <?php $priv_admin++; ?>
                <li><a href="{{ route('admin_division_list') }}"><i class="fa fa-bank"></i> {{ ucwords(lang('division', $translation)) }}</a></li>
            @endif
            @if (Helper::authorizing('Branch', 'View List')['status'] == 'true')
                <?php $priv_admin++; ?>
                <li><a href="{{ route('admin_branch_list') }}"><i class="fa fa-sitemap"></i> {{ ucwords(lang('branch', $translation)) }}</a></li>
            @endif
            @if (Helper::authorizing('Language', 'View List')['status'] == 'true')
                <?php $priv_admin++; ?>
                <li><a href="{{ route('admin_language_list') }}"><i class="fa fa-language"></i> {{ ucwords(lang('language', $translation)) }} </a></li>
            @endif
            @if (Helper::authorizing('Language Master', 'View List')['status'] == 'true')
                <?php $priv_admin++; ?>
                <li><a href="{{ route('admin_langmaster_list') }}"><i class="fa fa-book"></i> {{ ucwords(lang('language master', $translation)) }}</a></li>
            @endif
        </ul>
    </div>

    <?php $priv_restore = 0; ?>
    <div class="menu_section" id="navmenu_restore" style="display:none">
        <h3>{{ ucwords(lang('restore data', $translation)) }}</h3>
        <ul class="nav side-menu">
            <li><a><i class="fa fa-trash"></i> {{ ucwords(lang('deleted data', $translation)) }} <span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                    @if (Helper::authorizing('User Manager', 'Restore')['status'] == 'true')
                        <?php $priv_restore++; ?>
                        <li><a href="{{ route('admin_user_manager_deleted') }}">{{ ucwords(lang('user', $translation)) }}</a></li>
                    @endif
                    @if (Helper::authorizing('Usergroup Manager', 'Restore')['status'] == 'true')
                        <?php $priv_restore++; ?>
                        <li><a href="{{ route('admin_group_manager_deleted') }}">{{ ucwords(lang('usergroup', $translation)) }}</a></li>
                    @endif
                    @if (Helper::authorizing('Rule', 'Restore')['status'] == 'true')
                        <?php $priv_restore++; ?>
                        <li><a href="{{ route('admin_rule_deleted') }}">{{ ucwords(lang('rule', $translation)) }}</a></li>
                    @endif
                    @if (Helper::authorizing('Division', 'Restore')['status'] == 'true')
                        <?php $priv_restore++; ?>
                        <li><a href="{{ route('admin_division_deleted') }}">{{ ucwords(lang('division', $translation)) }}</a></li>
                    @endif
                    @if (Helper::authorizing('Branch', 'Restore')['status'] == 'true')
                        <?php $priv_restore++; ?>
                        <li><a href="{{ route('admin_branch_deleted') }}">{{ ucwords(lang('branch', $translation)) }}</a></li>
                    @endif
                    @if (Helper::authorizing('Customer', 'Restore')['status'] == 'true')
                        <?php $priv_restore++; ?>
                        <li><a href="{{ route('admin_customer_deleted') }}">{{ ucwords(lang('customer', $translation)) }}</a></li>
                    @endif
                    @if (Helper::authorizing('Brand', 'Restore')['status'] == 'true')
                        <?php $priv_restore++; ?>
                        <li><a href="{{ route('admin_brand_deleted') }}">{{ ucwords(lang('brand', $translation)) }}</a></li>
                    @endif
                    @if (Helper::authorizing('Product', 'Restore')['status'] == 'true')
                        <?php $priv_restore++; ?>
                        <li><a href="{{ route('admin_product_deleted') }}">{{ ucwords(lang('product', $translation)) }}</a></li>
                    @endif
                    @if (Helper::authorizing('Banner', 'Restore')['status'] == 'true')
                        <?php $priv_restore++; ?>
                        <li><a href="{{ route('admin_banner_deleted') }}">{{ ucwords(lang('banner', $translation)) }}</a></li>
                    @endif
                </ul>
            </li>
        </ul>
    </div>
</div>
<!-- /sidebar menu -->

@section('script-sidebar')
<script>
@if ($priv_admin > 0)
    $('#navmenu_admin').show();
@endif
@if ($priv_restore > 0)
    $('#navmenu_restore').show();
@endif
</script>
@endsection