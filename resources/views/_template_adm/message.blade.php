@if (count($errors) > 0)
    <div class="alert alert-danger alert-dismissible fade in" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
        <h4><i class="icon fa fa-ban"></i> {{ ucwords(lang('error', $translation)) }}</h4>
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (session('error'))
    <div class="clearfix"></div>
    <div class="alert alert-danger alert-dismissible fade in" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
        <h4><i class="icon fa fa-ban"></i> {{ ucwords(lang('error', $translation)) }}</h4>
        {{ session('error') }}
    </div>
@endif

@if (session('success'))
    <div class="clearfix"></div>
    <div class="alert alert-success alert-dismissible fade in" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
        <h4><i class="icon fa fa-check"></i> {{ ucwords(lang('success', $translation)) }}</h4>
        {{ session('success') }}
    </div>
@endif

@if (session('warning'))
    <div class="clearfix"></div>
    <div class="alert alert-warning alert-dismissible fade in" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
        <h4><i class="icon fa fa-warning"></i> {{ ucwords(lang('warning', $translation)) }}</h4>
        {{ session('warning') }}
    </div>
@endif

@if (session('info'))
    <div class="clearfix"></div>
    <div class="alert alert-info alert-dismissible fade in" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
        <h4><i class="icon fa fa-info-circle"></i> {{ ucwords(lang('info', $translation)) }}</h4>
        {{ session('info') }}
    </div>
@endif