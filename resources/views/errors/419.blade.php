@extends('errors.layout')

@section('title', 'Page Expired')

@section('message')
<div class="text-center py-10">
    <h1 class="text-4xl font-bold text-yellow-500">419 - Page Expired</h1>
    <p class="mt-4 text-gray-600">Sorry, your session has expired. Please refresh and try again.</p>
    <a href="{{ url()->previous() }}" class="mt-6 inline-block bg-blue-500 text-white px-4 py-2 rounded">Go Back</a>
</div>
@endsection