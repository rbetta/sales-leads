@extends('scaffolding/main/system-admin/logged-in')

@section('title')
Sales Lead System
@endsection

@section('html-head-append')
@endsection

@section('main')
<div>

	<h1>Lead Categories</h1>
	<lead-category-tree />
	
</div>
@endsection
