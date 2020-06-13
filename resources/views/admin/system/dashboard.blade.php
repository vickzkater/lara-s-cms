@extends('_template_adm.master')

@section('title', ucwords(lang('dashboard', $translation)))

@section('content')
  <div class="row">
    <!-- message info -->
    @include('_template_adm.message')
    
    <h2 class="text-center">{{ strtoupper(lang('welcome, #name', $translation, ['#name' => Session::get('admin')->name])) }}</h2>
  </div>
@endsection