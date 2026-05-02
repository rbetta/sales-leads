@extends('scaffolding/main/admin/logged-in')

@section('title')
Carcosa System
@endsection

@section('html-head-append')
@endsection

@section('main')
<div>

	<a href="{{ route('admin:client:handle-create-or-edit-client-form', []) }}">New Client</a>
    <table class="data">
    	<caption>Clients</caption>
    	<tr>
    		<th>ID</th>
    		<th>Label</th>
    		<th>Test?</th>
    		<th>Internal?</th>
    		<th>Created</th>
    		<th>Updated</th>
    		<th>Deleted</th>
    		<th>Actions</th>
    	</tr>
    	@foreach ($clients as $client)
    	<tr>
    		<td>{{ $client->id }}</td>
    		<td>{{ $client->label }}</td>
    		<td>{{ $client->is_test ? 'Yes' : 'No' }}</td>
    		<td>{{ $client->is_internal ? 'Yes' : 'No' }}</td>
    		<td>{{ $carcosa['locale']->formatShortDateTime($client->created_at, false) }}</td>
    		<td>{{ $carcosa['locale']->formatShortDateTime($client->updated_at, false) }}</td>
    		<td>{{ $carcosa['locale']->formatShortDateTime($client->deleted_at, false) }}</td>
    		<td>
    			<a href="{{ route('admin:client:display-create-or-edit-client-form', ['clientId' => $client->id]) }}">Edit</a>
    			<a href="{{ route('admin:application:display-application-list', ['clientId' => $client->id]) }}">Applications</a>
    			<a href="{{ route('admin:user:display-user-list', ['clientId' => $client->id]) }}">Users</a>
    			<a href="{{ route('admin:organization:display-organization-list', ['clientId' => $client->id]) }}">Organizations</a>
    		</td>
    	</tr>
    	@endforeach
    </table>
	
</div>
@endsection
