@extends('scaffolding/main/home/logged-out')

@section('title')
Sales Lead System
@endsection

@section('html-head-append')
@endsection

@section('main')
<div>

    <h1>Welcome to the Sales Lead System</h1>

	<p>
		Find fresh sales leads for your business now!
	</p>
	
	<p>
		<a href="{{ route('login:password:display-login-form') }}">Log In</a>
	</p>
	
</div>
@endsection
