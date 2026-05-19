<div class="lead-category{{ $leadCategory->getValue()->deleted_at ? ' deleted' : '' }}" data-id="{{ $leadCategory->getValue()->id }}" data-parent-id="{{ $leadCategory->getValue()->parent_id }}">
	<div class="lead-category-label">{{ $leadCategory->getValue()->label }}</div>
	<div class="lead-category-edit">
		<a href="{{ route('system-admin:lead-category:display-edit-lead-category-form', ['leadCategoryId' => $leadCategory->getValue()->id]) }}">Edit</a>
	</div>
	<div class="lead-category-add-child">
		<a href="{{ route('system-admin:lead-category:display-create-lead-category-form', ['parentId' => $leadCategory->getValue()->id]) }}">Add Child</a>
	</div>
	<div class="lead-category-delete">
		<form method="post" data-action="delete-lead-category" action="{{ route('system-admin:lead-category:delete-lead-category', []) }}">
			@csrf
			<input type="hidden" name="leadCategoryIds[]" value="{{ $leadCategory->getValue()->id }}" />
			<input type="hidden" name="childStrategy" value="delete-children" />
			<input type="submit" value="Delete" data-action="delete-lead-category"></input>
		</form>
	</div>
	@if ($leadCategory->getHasChildren())
	<div class="lead-category-children">
		@foreach ($leadCategory->getChildren() as $child)
		<x-lead-category :leadCategory="$child" />
		@endforeach
	</div>
	@endif
</div>
