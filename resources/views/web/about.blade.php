@extends('_template_web.master')

@section('title', ucwords(lang('about', $translation)))

@section('content')
    <section class="page-section about-heading">
        <div class="container">
        <img class="img-fluid rounded about-heading-img mb-3 mb-lg-0" src="{{ asset('web/img/about.jpg') }}" alt="">
            <div class="about-heading-content">
                <div class="row">
                    <div class="col-xl-9 col-lg-10 mx-auto">
                        <div class="bg-faded rounded p-5">
                            <h2 class="section-heading mb-4">
                                <span class="section-heading-upper">Strong Coffee, Strong Roots</span>
                                <span class="section-heading-lower">About Our Cafe</span>
                            </h2>
                            <p>Founded in 1987 by the Hernandez brothers, our establishment has been serving up rich coffee sourced from artisan farmers in various regions of South and Central America. We are dedicated to travelling the world, finding the best coffee, and bringing back to you here in our cafe.</p>
                            <p class="mb-0">We guarantee that you will fall in <em>lust</em> with our decadent blends the moment you walk inside until you finish your last sip. Join us for your daily routine, an outing with friends, or simply just to enjoy some alone time.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection