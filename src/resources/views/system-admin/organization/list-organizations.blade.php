@extends('scaffolding/main/admin/logged-in')

@section('title')
Carcosa System
@endsection

@section('html-head-append')
@endsection

@section('main')
<div>

	<a href="{{ route('admin:organization:display-create-organization-form', ['clientId' => $client->id]) }}">New Organization</a>
    <table class="data">
    	<caption>Organizations belonging to {{ $client->label }}</caption>
    	<tr>
    		<th>ID</th>
    		<th>Label</th>
    		<th>Created</th>
    		<th>Updated</th>
    		<th>Deleted</th>
    		<th>Actions</th>
    	</tr>
    	@foreach ($organizations as $organization)
    	<tr>
    		<td>{{ $organization->id }}</td>
    		<td>{{ $organization->label }}</td>
    		<td>{{ $carcosa['locale']->formatShortDateTime($organization->created_at, false) }}</td>
    		<td>{{ $carcosa['locale']->formatShortDateTime($organization->updated_at, false) }}</td>
    		<td>{{ $carcosa['locale']->formatShortDateTime($organization->deleted_at, false) }}</td>
    		<td>
    			<a href="{{ route('admin:organization:display-edit-organization-form', ['organizationId' => $organization->id]) }}">Edit</a>
    			<a href="{{ route('admin:workflow:display-workflow-list', ['organizationId' => $organization->id]) }}">Workflows</a>
    		</td>
    	</tr>
    	@endforeach
    </table>
	
</div>
@endsection
