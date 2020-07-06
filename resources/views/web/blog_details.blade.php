@extends('_template_web.master')

@php
    $pagetitle = $data->title;
    $contents = json_decode($data->content);

    $keyword = '';
    if(isset($_GET['q'])){
        $keyword = $_GET['q'];
    }
@endphp

@section('title', $pagetitle)

@section('css')
    <style>
        .video-container {
            overflow: hidden;
            position: relative;
            width:100%;
            margin-top: 10px;
        }

        .video-container::after {
            padding-top: 56.25%;
            display: block;
            content: '';
        }

        .video-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
    </style>
@endsection

@section('script-head')
    <script type="text/javascript" src="https://platform-api.sharethis.com/js/sharethis.js#property=5f006f1710009800120b8d5b&product=inline-share-buttons" async="async"></script>
@endsection

@section('content')
    <div class="container">

        <!-- Page Heading/Breadcrumbs -->
        <h1 class="mt-4 mb-3">
            {{ $pagetitle }}
            @if (!empty($data->author))    
                <small>by <a href="{{ route('web.blog') }}?author={{ $data->author }}">{{ $data->author }}</a></small>
            @endif
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('web.home') }}">Home</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('web.blog') }}">Blog</a>
            </li>
            <li class="breadcrumb-item active">{{ $pagetitle }}</li>
        </ol>

        <div class="row">

            <!-- Post Content Column -->
            <div class="col-lg-8">

                <!-- Preview Image -->
                {{-- http://placehold.it/900x300 --}}
                <img class="img-fluid rounded" src="{{ asset($data->thumbnail) }}" alt="{{ $pagetitle }}">

                <hr>

                <!-- Date/Time -->
                <p>
                    Posted on {{ date('F j, Y', strtotime($data->posted_at)) }}<br>
                    <div class="sharethis-inline-share-buttons"></div>
                </p>

                <hr>

                <!-- Post Content -->
                <p class="lead">{{ $data->summary }}</p>

                @foreach ($contents as $item)
                    @if ($item->type == 'text')
                        @php
                            echo $item->text;
                        @endphp
                    @elseif ($item->type == 'image')
                        <center>
                            <p><img class="img-fluid" src="{{ asset($item->image) }}" alt="{{ $item->image }}"></p>
                        </center>
                    @elseif ($item->type == 'image & text')
                        <div class="row">
                            @if ($item->text_position == 'left')
                                <div class="col-lg-6">
                                    @php
                                        echo $item->text;
                                    @endphp
                                </div>
                                <div class="col-lg-6" style="margin-top: 30px;">
                                    <div class="row">
                                        <div class="col">
                                            <center>
                                                <img src="{{ asset($item->image) }}" class="img-fluid" alt="{{ $item->image }}">
                                            </center>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="col-lg-6">
                                    <div class="row">
                                        <div class="col">
                                            <center>
                                                <img src="{{ asset($item->image) }}" class="img-fluid" alt="{{ $item->image }}">
                                            </center>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    @php
                                        echo $item->text;
                                    @endphp
                                </div>
                            @endif
                        </div>
                    @elseif ($item->type == 'video')
                        @php
                            $video_value = $item->video;
                            $param = explode('?v=', $video_value);
                        @endphp
                        <p>
                            <div class="video-container">
                                <iframe width="560" height="315" src="https://www.youtube-nocookie.com/embed/{{ $param[1] }}" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                            </div>
                        </p>
                    @elseif ($item->type == 'video & text')
                        <div class="row">
                            @php
                                $video_value = $item->video;
                                $param = explode('?v=', $video_value);
                            @endphp
                            @if ($item->text_position == 'left')
                                <div class="col-lg-6">
                                    <p>@php
                                        echo $item->text;
                                    @endphp</p>
                                </div>
                                <div class="col-lg-6">
                                    <div class="row">
                                        <div class="col">
                                            <div class="video-container">
                                                <iframe width="560" height="315" src="https://www.youtube-nocookie.com/embed/{{ $param[1] }}" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="col-lg-6">
                                    <div class="row">
                                        <div class="col">
                                            <div class="video-container">
                                                <iframe width="560" height="315" src="https://www.youtube-nocookie.com/embed/{{ $param[1] }}" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <p>@php
                                        echo $item->text;
                                    @endphp</p>
                                </div>
                            @endif
                        </div>
                    @elseif ($item->type == 'plain text')
                        @php
                            echo $item->text;
                        @endphp
                    @endif
                @endforeach

                <hr>

                <div class="sharethis-inline-share-buttons"></div>

                <hr>
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
                        {!! $qrcode_main !!}
                    </div>
                </div>

                <div class="card my-4">
                    <h5 class="card-header">QR Code to this page</h5>
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
        function search_page() {
            var keyword = $('#keyword').val();
            if(keyword == ''){
                $('#keyword').focus();
                alert('Please input your keyword first');
                return false;
            }
            var uri = "{{ route('web.blog') }}?q="+keyword;
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