@extends('scaffolding/main/seller/logged-in')

@section('title')
Sales Lead System
@endsection

@section('html-head-append')
<link rel="stylesheet" type="text/css" href="/css/admin/main.css" />
@endsection

@section('main')
<div>

    <h1>Welcome, {{ $carcosa['user']->label }}</h1>
	
</div>
@endsection
