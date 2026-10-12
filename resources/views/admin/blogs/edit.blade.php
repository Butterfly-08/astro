@extends('layouts.admin')

@section('title', 'Edit Article')
@section('page_title', 'Edit Article')

@section('content')
@include('admin.blogs._form', ['blog' => $blog, 'editing' => true])
@endsection
