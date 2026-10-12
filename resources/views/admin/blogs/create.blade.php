@extends('layouts.admin')

@section('title', 'Create Article')
@section('page_title', 'Create Article')

@section('content')
@include('admin.blogs._form', ['blog' => $blog, 'editing' => false])
@endsection
