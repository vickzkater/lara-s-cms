@extends('_template_adm.master')
@section('title', 'Dashboard')

@section('content')
  <div class="row">
    <!-- message info -->
    @include('_template_adm.message')
    <h2 class="text-center">{{ strtoupper(lang('welcome back, #name', $translation, ['#name' => Session::get('admin')->name])) }}</h2>
  </div>
@endsection