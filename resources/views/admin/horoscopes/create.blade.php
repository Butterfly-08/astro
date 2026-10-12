@extends('layouts.admin')

@section('title', 'Add Horoscope')
@section('page_title', 'Add Horoscope')

@section('content')
@include('admin.horoscopes._form', ['horoscope' => $horoscope, 'editing' => false])
@endsection
