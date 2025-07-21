@extends('errors.layout')

@section('title', 'Page Not Found')

@section('message')
<div class="text-center py-10">
    <h1 class="text-4xl font-bold text-red-500">404 - Page Not Found</h1>
    <p class="mt-4 text-gray-600">Oops! The page you're looking for doesn't exist.</p>
</div>
@endsection