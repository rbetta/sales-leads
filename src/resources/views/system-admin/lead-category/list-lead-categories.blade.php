@extends('scaffolding/main/system-admin/logged-in')

@section('title')
Sales Lead System
@endsection

@section('html-head-append')
@endsection

@section('main')
<div>

	<h1>Lead Categories</h1>
	@foreach ($leadCategories as $leadCategory)
	<x-lead-category :leadCategory="$leadCategory" />
	@endforeach
	
</div>
@endsection
