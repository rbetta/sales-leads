@extends('scaffolding/main/admin/logged-in')

@section('title')
Carcosa System
@endsection

@section('html-head-append')
@endsection

@section('main')
<div>

	<h1>{{ $permission ? 'Edit' : 'Create' }} Permission</h1>

	<form action="{{ route('admin:permission:handle-create-or-edit-permission-form', ['permissionId' => $permission?->id]) }}" method="post">
		
		@csrf

		<input type="hidden" name="permission[id]" value="{{ old('permission.id') ?? $permission?->id }}">

		<input type="hidden" name="permission[clientId]" value="{{ $client?->id }}">
		
		<input type="hidden" name="permission[applicationId]" value="{{ $application?->id }}">
		
		@if ($client)
		<x-admin-field-read-only label="Client" :value="$client->label" />
		@elseif ($application)
		<x-admin-field-read-only label="Application" :value="$application->label" />
		@else
		{{-- This is a system-level permission. --}}
		@endif
		
		<x-admin-field-text id="admin-permission-label" label="Label" name="permission[label]" :value="$permission?->label" />
		
		<x-admin-field-textarea id="admin-permission-description" label="Description" name="permission[description]" :value="$permission?->description" />
		
    	<input type="submit" id="admin-permission-submit" value="{{ $permission ? 'Edit' : 'Create' }}">
		
	</form>
	
</div>
@endsection
