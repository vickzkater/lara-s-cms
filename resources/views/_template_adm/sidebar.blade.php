@php
    // library
    use App\Libraries\Helper;

    $badge_new = '<span class="label label-success pull-right">NEW</span>';
@endphp

<!-- sidebar menu -->
<div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
    <div class="menu_section">
        <h3>{{ ucwords(lang('main menu', $translation)) }}</h3>
        <ul class="nav side-menu">
            <li>
                <a href="{{ route('admin.home') }}">
                    <i class="fa fa-dashboard"></i> {{ ucwords(lang('dashboard', $translation)) }}
                </a>
            </li>
            <li>
                <a href="{{ route('admin.profile') }}">
                    <i class="fa fa-user"></i> {{ ucwords(lang('my profile', $translation)) }}
                </a>
            </li>

            @if (Helper::authorizing('Banner', 'View List')['status'] == 'true')
                @php
                    $menu_active = '';
                    if(Helper::is_menu_active('/banner/')){
                        $menu_active = 'current-page';
                    }
                @endphp
                <li class="{{ $menu_active }}">
                    <a href="{{ route('admin.banner.list') }}">
                        <i class="fa fa-image"></i> {{ ucwords(lang('banner', $translation)) }}
                    </a>
                </li>
            @endif
            
            @if (Helper::authorizing('Product', 'View List')['status'] == 'true')
                @php
                    $menu_active = '';
                    if(Helper::is_menu_active('/product/')){
                        $menu_active = 'current-page';
                    }
                @endphp
                <li class="{{ $menu_active }}">
                    <a href="{{ route('admin.product.list') }}">
                        <i class="fa fa-cubes"></i> {{ ucwords(lang('product', $translation)) }}
                    </a>
                </li>
            @endif

            @if (Helper::authorizing('Article', 'View List')['status'] == 'true')
                <li>
                    <a><i class="fa fa-newspaper-o"></i> {{ ucwords(lang('article', $translation)) }} <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                        @if (Helper::authorizing('Topic', 'View List')['status'] == 'true')
                            <li><a href="{{ route('admin.topic.list') }}">{{ ucwords(lang('topic', $translation)) }}</a></li>
                        @endif
                        @if (Helper::authorizing('Article', 'View List')['status'] == 'true')
                            <li><a href="{{ route('admin.article.list') }}">{{ ucwords(lang('article', $translation)) }}</a></li>
                        @endif
                    </ul>
                </li>
            @endif
        </ul>
    </div>
    
    @php
        $priv_admin = 0;
    @endphp
    <div class="menu_section" id="navmenu_admin" style="display:none">
        <hr>
        <h3>{{ ucwords(lang('administration', $translation)) }}</h3>
        <ul class="nav side-menu">
            @if (Helper::authorizing('System Logs', 'View List')['status'] == 'true')
                <?php $priv_admin++; ?>
                <li><a href="{{ route('admin.logs') }}"><i class="fa fa-exchange"></i> {{ ucwords(lang('logs', $translation)) }}</a></li>
            @endif

            @if (Helper::authorizing('Language', 'View List')['status'] == 'true')
                <li><a id="menu-language"><i class="fa fa-language"></i> {{ ucwords(lang('language', $translation)) }} <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                        @if (Helper::authorizing('Dictionary', 'View List')['status'] == 'true')
                            @php
                                $priv_admin++;
                                $menu_active = '';
                                if(Helper::is_menu_active('/system/dictionary/')){
                                    $menu_active = 'current-page';
                                }
                            @endphp
                            <li class="{{ $menu_active }}">
                                <a href="{{ route('admin.langmaster.list') }}">
                                    {{ ucwords(lang('dictionary', $translation)) }}
                                </a>
                            </li>
                        @endif

                        @if (Helper::authorizing('Language', 'View List')['status'] == 'true')
                            @php
                                $priv_admin++;
                                $menu_active = '';
                                if(Helper::is_menu_active('/system/language/')){
                                    $menu_active = 'current-page';
                                }
                            @endphp
                            <li class="{{ $menu_active }}">
                                <a href="{{ route('admin.language.list') }}">
                                    {{ ucwords(lang('language', $translation)) }}
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
                @if(Helper::is_menu_active('/system/dictionary/') || Helper::is_menu_active('/system/language/'))
                    <script>
                        setTimeout(function(){ 
                            document.getElementById('menu-language').click();
                        }, 1000);
                    </script>
                @endif
            @endif
            
            @if (Helper::authorizing('User', 'View List')['status'] == 'true')
                @php
                    $priv_admin++;
                    $menu_active = '';
                    if(Helper::is_menu_active('/system/user/')){
                        $menu_active = 'current-page';
                    }
                @endphp
                <li class="{{ $menu_active }}">
                    <a href="{{ route('admin.user.list') }}">
                        <i class="fa fa-user"></i> {{ ucwords(lang('admin', $translation)) }}
                    </a>
                </li>
            @endif

            @if (Helper::authorizing('Usergroup', 'View List')['status'] == 'true')
                @php
                    $priv_admin++;
                    $menu_active = '';
                    if(Helper::is_menu_active('/system/usergroup/')){
                        $menu_active = 'current-page';
                    }
                @endphp
                <li class="{{ $menu_active }}">
                    <a href="{{ route('admin.usergroup.list') }}">
                        <i class="fa fa-users"></i> {{ ucwords(lang('admin group', $translation)) }}
                    </a>
                </li>
            @endif

            @if (Helper::authorizing('Rule', 'View List')['status'] == 'true')
                @php
                    $priv_admin++;
                    $menu_active = '';
                    if(Helper::is_menu_active('/system/rule/')){
                        $menu_active = 'current-page';
                    }
                @endphp
                <li class="{{ $menu_active }}">
                    <a href="{{ route('admin.rule.list') }}">
                        <i class="fa fa-gavel"></i> {{ ucwords(lang('rules', $translation)) }}
                    </a>
                </li>
            @endif

            @if (Helper::authorizing('Branch', 'View List')['status'] == 'true')
                @php
                    $priv_admin++;
                    $menu_active = '';
                    if(Helper::is_menu_active('/system/branch/')){
                        $menu_active = 'current-page';
                    }
                @endphp
                <li class="{{ $menu_active }}">
                    <a href="{{ route('admin.branch.list') }}">
                        <i class="fa fa-sitemap"></i> {{ ucwords(lang('branch', $translation)) }}
                    </a>
                </li>
            @endif

            @if (Helper::authorizing('Division', 'View List')['status'] == 'true')
                @php
                    $priv_admin++;
                    $menu_active = '';
                    if(Helper::is_menu_active('/system/division/')){
                        $menu_active = 'current-page';
                    }
                @endphp
                <li class="{{ $menu_active }}">
                    <a href="{{ route('admin.division.list') }}">
                        <i class="fa fa-bank"></i> {{ ucwords(lang('office', $translation)) }}
                    </a>
                </li>
            @endif

            @if (Helper::authorizing('Config', 'Update')['status'] == 'true')
                <?php $priv_admin++; ?>
                <li><a href="{{ route('admin.config') }}"><i class="fa fa-gears"></i> {{ ucwords(lang('config', $translation)) }}</a></li>
            @endif
        </ul>
    </div>
</div>
<!-- /sidebar menu -->

@section('script-sidebar')
    <script>
        @if ($priv_admin > 0)
            $('#navmenu_admin').show();
        @endif
    </script>
@endsection