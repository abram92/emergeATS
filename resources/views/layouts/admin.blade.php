@extends('layouts.menu')


@section('css')
	<link href="{{ asset('css/custom.css') }}" rel="stylesheet" />

@endsection

{{-- @include('partials.classcss',['var1'=>Route::current()->uri()]) --}}