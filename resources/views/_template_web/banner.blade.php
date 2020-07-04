@if (isset($banners[0]))
  <header>
      <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
        <ol class="carousel-indicators">
          @for ($i = 0; $i < count($banners); $i++)
            @php
              $active = '';
              if ($i == 0) {
                $active = 'active';
              }
            @endphp
            <li data-target="#carouselExampleIndicators" data-slide-to="{{ $i }}" class="{{ $active }}"></li>
          @endfor
        </ol>
        <div class="carousel-inner" role="listbox">
          @for ($i = 0; $i < count($banners); $i++)
            @php
              $active = '';
              if ($i == 0) {
                $active = 'active';
              }
              $item = $banners[$i];
            @endphp
            {{-- http://placehold.it/1900x1080 --}}
            <div class="carousel-item {{ $active }}" style="background-image: url('{{ asset($item->image) }}')">
              @if (!empty($item->title))
                <div class="carousel-caption d-none d-md-block">
                  <h3>{{ $item->title }}</h3>
                  <p>{{ $item->description }}</p>
                </div>
              @endif
            </div>
          @endfor
        </div>
        <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
          <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
          <span class="carousel-control-next-icon" aria-hidden="true"></span>
          <span class="sr-only">Next</span>
        </a>
      </div>
  </header>
@endif