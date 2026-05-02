@extends('scaffolding/main/admin/logged-in')

@section('title')
Carcosa System
@endsection

@section('html-head-append')
@endsection

@section('main')
<div>

	<h1>{{ $client ? 'Edit' : 'Create' }} Client</h1>

	<form action="{{ route('admin:client:handle-create-or-edit-client-form', ['clientId' => $client?->id]) }}" method="post">
		
		@csrf

		<input type="hidden" name="client[id]" value="{{ old('client.id') ?? $client?->id }}">

		<x-admin-field-text id="admin-client-label" label="Label" name="client[label]" :value="$client?->label" />

		<x-admin-field-radio-boolean id-prefix="client[isTest]" label="Test Client" name="client[isTest]" trueLabel="Yes" falseLabel="No" :value="$client?->is_test" />
		
		<x-admin-field-radio-boolean id-prefix="client[isInternal]" label="Internal" name="client[isInternal]" trueLabel="Yes" falseLabel="No" :value="$client?->is_internal" />
		
    	<input type="submit" id="admin-client-submit" value="{{ $client ? 'Edit' : 'Create' }}">
		
	</form>
	
</div>
@endsection
