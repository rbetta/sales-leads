@extends('scaffolding/main/admin/logged-in')

@section('title')
Carcosa System
@endsection

@section('html-head-append')
@endsection

@section('main')
<div>

	@if ($client)
	<a href="{{ route('admin:permission:display-create-client-permission-form', ['clientId' => $client->id]) }}">New Permission</a>
	@elseif ($application)
	<a href="{{ route('admin:permission:display-create-application-permission-form', ['applicationId' => $application->id]) }}">New Permission</a>
	@else
	<a href="{{ route('admin:permission:display-create-system-permission-form', []) }}">New Permission</a>
	@endif
    <table class="data">
    	<caption>
    		@if ($client)
        	Permissions belonging to client {{ $client->label }}
        	@elseif ($application)
        	Permissions belonging to application {{ $application->label }}
        	@else
        	System Permissions
        	@endif
    	</caption>
    	<tr>
    		<th>ID</th>
    		<th>Label</th>
    		<th>Type</th>
    		<th>Created</th>
    		<th>Updated</th>
    		<th>Deleted</th>
    		<th>Actions</th>
    	</tr>
    	@foreach ($permissions as $permission)
    	<tr>
    		<td>{{ $permission->id }}</td>
    		<td>{{ $permission->label }}</td>
    		<td>{{ $permission->is_custom ? 'Custom' : 'System' }}</td>
    		<td>{{ $carcosa['locale']->formatShortDateTime($permission->created_at, false) }}</td>
    		<td>{{ $carcosa['locale']->formatShortDateTime($permission->updated_at, false) }}</td>
    		<td>{{ $carcosa['locale']->formatShortDateTime($permission->deleted_at, false) }}</td>
    		<td>
    			<a href="{{ route('admin:permission:display-edit-permission-form', ['permissionId' => $permission->id]) }}">Edit</a>
    		</td>
    	</tr>
    	@endforeach
    </table>
	
</div>
@endsection
