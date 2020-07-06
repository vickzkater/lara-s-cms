@extends('_template_web.master')

@php
    use App\Libraries\Helper;

    $keyword = '';
    if(isset($_GET['q'])){
        $keyword = $_GET['q'];
    }

    $pagetitle = 'Blog';
@endphp

@section('title', $pagetitle)

@section('content')
    <div class="container">

        <!-- Page Heading/Breadcrumbs -->
        <h1 class="mt-4 mb-3">
            {{ $pagetitle }}
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('web.home') }}">Home</a>
            </li>
            <li class="breadcrumb-item active">{{ $pagetitle }}</li>
        </ol>

        <div class="row">

            <!-- Blog Entries Column -->
            <div class="col-md-8">
                @if (isset($data[0]))
                    @foreach ($data as $item)
                        <!-- Blog Post -->
                        <div class="card mb-4">
                            {{-- http://placehold.it/750x300 --}}
                            <img class="card-img-top" src="{{ asset($item->thumbnail) }}" alt="{{ $item->title }}">
                            <div class="card-body">
                                <h2 class="card-title">{{ $item->title }}</h2>
                                <p class="card-text">{{ Helper::read_more($item->summary) }}</p>
                                <a href="{{ route('web.blog.details', $item->slug) }}" class="btn btn-primary">Read More &rarr;</a>
                            </div>
                            <div class="card-footer text-muted">
                                Posted on {{ date('F j, Y', strtotime($item->posted_at)) }}
                                @if (!empty($item->author))    
                                    by <a href="{{ route('web.blog') }}?author={{ $item->author }}">{{ $item->author }}</a>
                                @endif
                            </div>
                        </div>
                    @endforeach

                    <!-- Pagination -->
                    <ul class="pagination justify-content-center mb-4">
                        <li class="page-item">
                            <a class="page-link" href="#">&larr; Older</a>
                        </li>
                        <li class="page-item disabled">
                            <a class="page-link" href="#">Newer &rarr;</a>
                        </li>
                    </ul>
                @else
                    <center><h2>NO DATA</h2></center>
                @endif

            </div>

            <!-- Sidebar Widgets Column -->
            <div class="col-md-4">

                <!-- Search Widget -->
                <div class="card mb-4">
                    <h5 class="card-header">Search</h5>
                    <div class="card-body">
                        <div class="input-group">
                            <input type="text" id="keyword" class="form-control" placeholder="Search for..." value="{{ $keyword }}">
                            <span class="input-group-append">
                                <button class="btn btn-secondary" type="button" onclick="search_page()">Go!</button>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Categories Widget -->
                <div class="card my-4">
                    <h5 class="card-header">Topics</h5>
                    <div class="card-body">
                        @if (isset($topics[0]))
                            @php
                                $fifty = ceil(count($topics) / 2);
                            @endphp
                            <div class="row">
                                <div class="col-lg-6">
                                    <ul class="list-unstyled mb-0">
                                        @for ($i = 0; $i < $fifty; $i++)
                                            @php
                                                $topic_name = $topics[$i]->name;
                                                $url = route('web.blog') . '?topic=' . $topic_name;
                                            @endphp
                                            <li>
                                                <a href="{{ $url }}">{{ $topic_name }}</a>
                                            </li>
                                        @endfor
                                    </ul>
                                </div>
                                <div class="col-lg-6">
                                    <ul class="list-unstyled mb-0">
                                        @for ($i = $fifty; $i < count($topics); $i++)
                                            @php
                                                $topic_name = $topics[$i]->name;
                                                $url = route('web.blog') . '?topic=' . $topic_name;
                                            @endphp
                                            <li>
                                                <a href="{{ $url }}">{{ $topic_name }}</a>
                                            </li>
                                        @endfor
                                    </ul>
                                </div>
                            </div>
                        @else
                            NO DATA
                        @endif
                    </div>
                </div>

                <!-- Side Widget -->
                <div class="card my-4">
                    <h5 class="card-header">SCAN ME</h5>
                    <div class="card-body text-center">
                        {!! $qrcode !!}
                    </div>
                </div>

            </div>

        </div>
        <!-- /.row -->

    </div>
    <!-- /.container -->
@endsection

@section('script')
    <script src="{{ asset('admin/js/thehelper.js') }}"></script>

    <script>
        function open_page(page) {
            set_param_url('page', page);
            var uri = window.location.href;
            window.location.href = uri;
        }

        function search_page() {
            var keyword = $('#keyword').val();
            if(keyword == ''){
                $('#keyword').focus();
                alert('Please input your keyword first');
                return false;
            }
            set_param_url('q', keyword);
            var uri = window.location.href;
            window.location.href = uri;
        }

        var input = document.getElementById("keyword");
        input.addEventListener("keyup", function(event) {
            if (event.keyCode === 13) {
                event.preventDefault();
                search_page();
            }
        });
    </script>
@endsection