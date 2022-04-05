@extends('layouts.admin')

@section('title', __('Edit Public Holiday: '. $publicholiday->description))

@include('admin.publicholidays.form')
