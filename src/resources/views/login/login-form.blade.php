@extends('scaffolding/main/home/logged-out')

@section('title')
Administrative Login
@endsection

@section('html-head-append')
@endsection

@section('main')
<div>
    <h1>Sales Lead Manager Administrative Interface</h1>
	
	<form action="{{$loginActionUrl}}" method="post">

		@csrf

		<div class="form-field">
        	<label for="admin-login-email">Email</label>
        	<input type="text" name="username" id="admin-login-email" value="{{ old('username') }}">
        	<div class="field-error">{{ $errors->first('username') }}</div>
        </div>

		<div class="form-field">
        	<label for="admin-login-password">Password</label>
        	<input type="password" name="password" id="admin-login-password" value="{{ old('password') }}">
        	<div class="field-error">{{ $errors->first('password') }}</div>
        </div>
		
		<input type="hidden" name="redirectTo" value="{{ $redirectTo }}" />
		
    	<input type="submit" class="ui-button ui-widget ui-corner-all" id="admin-login-button" value="Log In">

	</form>
</div>

@endsection
