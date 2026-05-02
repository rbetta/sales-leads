@extends('scaffolding/main/admin/logged-in')

@section('title')
Carcosa System
@endsection

@section('html-head-append')
@endsection

@section('main')
<div>

	@foreach ($defaultPermissionsByLevel as $permissionLevelId => $defaultPermissions)
	<a href="{{ route('admin:permission:display-create-default-permission-form', ['permissionLevelId' => $permissionLevelId]) }}">New {{ ($permissionLevelsById[$permissionLevelId])->label }} Permission</a>
    <table class="data">
    	<caption>
    		Default {{ ($permissionLevelsById[$permissionLevelId])->label }} Permissions
    	</caption>
    	<tr>
    		<th>ID</th>
    		<th>Code</th>
    		<th>Label</th>
    		<th>Description</th>
    		<th>Created</th>
    		<th>Updated</th>
    		<th>Deleted</th>
    		<th>Actions</th>
    	</tr>
    	@foreach ($defaultPermissions as $defaultPermission)
    	<tr>
    		<td>{{ $defaultPermission->id }}</td>
    		<td>{{ $defaultPermission->code }}</td>
    		<td>{{ $defaultPermission->label }}</td>
    		<td>{{ $defaultPermission->description }}</td>
    		<td>{{ $carcosa['locale']->formatShortDateTime($defaultPermission->created_at, false) }}</td>
    		<td>{{ $carcosa['locale']->formatShortDateTime($defaultPermission->updated_at, false) }}</td>
    		<td>{{ $carcosa['locale']->formatShortDateTime($defaultPermission->deleted_at, false) }}</td>
    		<td>
    			<a href="{{ route('admin:permission:display-edit-default-permission-form', ['defaultPermissionId' => $defaultPermission->id]) }}">Edit</a>
    		</td>
    	</tr>
    	@endforeach
    </table>
	@endforeach
	
</div>
@endsection
