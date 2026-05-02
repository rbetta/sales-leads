@extends('scaffolding/main/admin/logged-in')

@section('title')
Carcosa System
@endsection

@section('html-head-append')
@endsection

@section('main')
<div>

	<h1>{{ $user ? 'Edit' : 'Create' }} User</h1>

	<form action="{{ route('admin:user:handle-create-or-edit-user-form', ['clientId' => $client?->id]) }}" method="post">
		
		@csrf

		<input type="hidden" name="user[id]" value="{{ old('user.id') ?? $user?->id }}">

		<input type="hidden" name="user[clientId]" value="{{ $client->id }}">

		<x-admin-field-read-only label="Client" :value="$client->label" />

		<x-admin-field-text id="admin-user-username" label="Username" name="user[username]" :value="$user?->username" />
		
		@if ( ! $user )
		<div class="form-field">
            <label for="admin-user-password">Password</label>
            <input type="password" name="user[password]" id="admin-user-password" value="{{ old('user.password') }}">
            <div class="field-error">{{ $errors->first('user.password') }}</div>
		</div>
		@endif
		
		<x-admin-field-text id="admin-user-email" label="Email" name="user[email]" :value="$user?->email" />
		
		<x-admin-field-text id="admin-user-label" label="Label" name="user[label]" :value="$user?->label" />
		
		<x-admin-field-select id="admin-user-locale" label="Locale" name="user[locale]" :options="$locales" :value="$user?->getLocale()" />
		
		<x-admin-field-radio-boolean id-prefix="admin-user-isActive" label="Active" name="user[isActive]" trueLabel="Yes" falseLabel="No" :value="$user?->is_active" />
		
		<x-admin-field-checkboxes idPrefix="admin-user-groups" label="Groups" name="user[groupIds][]" :options="$allGroups" :values="$groups" />
		
    	<input type="submit" id="admin-user-submit" value="{{ $user ? 'Edit' : 'Create' }}">
		
	</form>
	
</div>
@endsection
