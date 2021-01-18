<!-- top navigation -->
<div class="top_nav">
    <div class="nav_menu">
        <nav>
            <div class="nav toggle">
                <a id="menu_toggle"><i class="fa fa-bars"></i></a>
            </div>

            <ul class="nav navbar-nav navbar-right">
                <li class="">
                    <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    <img src="{{ asset('/images/avatar.png') }}" alt="">{{ Session::get('admin')->name }}
                    <span class=" fa fa-angle-down"></span>
                    </a>
                    <ul class="dropdown-menu dropdown-usermenu animated fadeInDown pull-right">
                        <li><a href="{{ route('admin.profile') }}"> {{ ucwords(lang('my profile', $translation)) }}</a></li>
                        <li>
                            <a href="{{ route('admin.logout') }}" style="color:rgba(231,76,60,.88); !important;" onclick="return confirm('{{ lang('Are you sure to logout?', $translation) }}')">
                                <b><i class="fa fa-sign-out pull-right"></i> {{ ucwords(lang('log out', $translation)) }}</b>
                            </a>
                        </li>
                    </ul>
                </li>
                <li role="presentation" class="dropdown">
                    <a href="javascript:;" class="dropdown-toggle info-number" data-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-language"></i>
                    </a>
                    <ul id="menu1" class="dropdown-menu list-unstyled msg_list animated fadeInDown" role="menu">
                        <li>
                            <div class="text-center">
                                <strong>{{ ucwords(lang('set language', $translation)) }}</strong>
                            </div>
                        </li>
                        @if (isset($languages) && count($languages) > 0)
                            @foreach ($languages as $key => $value)
                                @php
                                    $lang_stat = '';
                                    if (Session::get('language') == $value->alias)
                                    {
                                        $lang_stat = '<b class="pull-right">'. strtoupper(lang('active', $translation)) .'</b>';
                                    }
                                @endphp
                                <li>
                                    <a href="{{ route('admin.change_language', $value->alias) }}">
                                        <span>{{ $value->alias . ' - ' . $value->name }} <?php echo $lang_stat; ?></span>
                                    </a>
                                </li>
                            @endforeach
                        @else
                            <li>
                                <a>
                                    <span>EN - English <i class="pull-right">default</i></span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
            </ul>
        </nav>
    </div>
</div>
<!-- /top navigation -->