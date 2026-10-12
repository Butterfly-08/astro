@extends('layouts.admin')

@section('title', 'Edit Horoscope')
@section('page_title', 'Edit Horoscope')

@section('content')
@include('admin.horoscopes._form', ['horoscope' => $horoscope, 'editing' => true])
@endsection
