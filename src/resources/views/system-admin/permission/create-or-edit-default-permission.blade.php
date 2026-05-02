@extends('scaffolding/main/admin/logged-in')

@section('title')
Carcosa System
@endsection

@section('html-head-append')
@endsection

@section('main')
<div>

	<h1>{{ $defaultPermission ? 'Edit' : 'Create' }} Default Permission</h1>

	<form action="{{ route('admin:permission:handle-create-or-edit-default-permission-form', ['defaultPermissionId' => $defaultPermission?->id]) }}" method="post">
		
		@csrf

		<input type="hidden" name="defaultPermission[id]" value="{{ old('defaultPermission.id') ?? $defaultPermission?->id }}">

		<input type="hidden" name="defaultPermission[permissionLevelId]" value="{{ $permissionLevel?->id }}">
		
		<x-admin-field-read-only label="Level" :value="$permissionLevel->label" />

		<x-admin-field-text id="admin-default-permission-code" label="Code" name="defaultPermission[code]" :value="$defaultPermission?->code" />

		<x-admin-field-text id="admin-default-permission-label" label="Label" name="defaultPermission[label]" :value="$defaultPermission?->label" />
		
		<x-admin-field-textarea id="admin-default-permission-description" label="Description" name="defaultPermission[description]" :value="$defaultPermission?->description" />
		
    	<input type="submit" id="admin-default-permission-submit" value="{{ $defaultPermission ? 'Edit' : 'Create' }}">
		
	</form>
	
</div>
@endsection
