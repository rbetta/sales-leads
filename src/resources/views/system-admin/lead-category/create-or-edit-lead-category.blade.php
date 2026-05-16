@extends('scaffolding/main/system-admin/logged-in')

@section('title')
Carcosa System
@endsection

@section('html-head-append')
@endsection

@section('main')
<div>

	<h1>{{ $leadCategory?->id ? 'Edit' : 'Create' }} Lead Category</h1>

	<form action="{{ route('system-admin:lead-category:handle-create-or-edit-lead-category-form', []) }}" method="post">
		
		@csrf

		<input type="hidden" name="leadCategory[id]" value="{{ old('leadCategory.id') ?? $leadCategory?->id }}">
		
		<input type="hidden" name="leadCategory[parentId]" value="{{ old('leadCategory.parentId') ?? $leadCategory?->parent_id }}">

		<x-admin-field-text id="admin-lead-category-label" label="Label" name="leadCategory[label]" :value="$leadCategory?->label" />
		
    	<input type="submit" id="admin-lead-category-submit" value="{{ $leadCategory?->id ? 'Edit' : 'Create' }}">
		
	</form>
	
</div>
@endsection
