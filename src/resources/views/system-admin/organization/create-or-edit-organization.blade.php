@extends('scaffolding/main/admin/logged-in')

@section('title')
Carcosa System
@endsection

@section('html-head-append')
@endsection

@section('main')
<div>

	<h1>{{ $organization ? 'Edit' : 'Create' }} Organization</h1>

	<form action="{{ route('admin:organization:handle-create-or-edit-organization-form', ['organizationId' => $organization?->id]) }}" method="post">
		
		@csrf

		<input type="hidden" name="organization[id]" value="{{ old('organization.id') ?? $organization?->id }}">

		<input type="hidden" name="organization[clientId]" value="{{ $client->id }}">

		<x-admin-field-read-only label="Client" :value="$client->label" />

		<x-admin-field-text id="admin-organization-label" label="Label" name="organization[label]" :value="$organization?->label" />
		
    	<input type="submit" id="admin-organization-submit" value="{{ $organization ? 'Edit' : 'Create' }}">
		
	</form>
	
</div>
@endsection
