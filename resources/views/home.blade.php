@extends('layouts.menu')

@section('title', 'Home')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')

@if (count($arrLeads+$arrActiveJobs+$arrInprocessCandidates+$arrActiveCandidates))
	@include('staticwork.swlist')

@endif	

@include('home.mycandidates',['data'=>$mycandidates])
@include('home.myjobs',['data'=>$myjobs])
	
@stop
