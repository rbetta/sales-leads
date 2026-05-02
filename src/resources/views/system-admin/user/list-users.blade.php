@extends('scaffolding/main/admin/logged-in')

@section('title')
Carcosa System
@endsection

@section('html-head-append')
@endsection

@section('main')
<div>

	<a href="{{ route('admin:user:display-create-user-form', ['clientId' => $client->id]) }}">New User</a>
    <table class="data">
    	<caption>Users belonging to client: {{ $client->label }}</caption>
    	<tr>
    		<th>ID</th>
    		<th>Label</th>
    		<th>Username</th>
    		<th>Email</th>
    		<th>Locale</th>
    		<th>Active</th>
    		<th>Deactivated</th>
    		<th>Created</th>
    		<th>Updated</th>
    		<th>Deleted</th>
    		<th>Actions</th>
    	</tr>
    	@foreach ($users as $user)
    	<tr>
    		<td>{{ $user->id }}</td>
    		<td>{{ $user->label }}</td>
    		<td>{{ $user->username }}</td>
    		<td>{{ $user->email }}</td>
    		<td>{{ $user->locale }}</td>
    		<td>{{ $user->is_active ? 'Yes' : 'No' }}</td>
    		<td>{{ $carcosa['locale']->formatShortDateTime($user->deactivated_at, false) }}</td>
    		<td>{{ $carcosa['locale']->formatShortDateTime($user->created_at, false) }}</td>
    		<td>{{ $carcosa['locale']->formatShortDateTime($user->updated_at, false) }}</td>
    		<td>{{ $carcosa['locale']->formatShortDateTime($user->deleted_at, false) }}</td>
    		<td>
    			<a href="{{ route('admin:user:display-edit-user-form', ['userId' => $user->id]) }}">Edit</a>
    		</td>
    	</tr>
    	@endforeach
    </table>
	
</div>
@endsection
