@extends('layouts.admin')

@include('admin.baseform.baseindex', ['baseclass' => 'Job Titles', 'basepath' => 'jobtitles', 'dbsearch' => true])
