<div class="lead-category" data-id="{{ $leadCategory->getValue()->id }}" data-parent-id="{{ $leadCategory->getValue()->parent_id }}">
	<div class="lead-category-label">{{ $leadCategory->getValue()->label }}</div>
	<div class="lead-category-add-child"><a href="{{ route('system-admin:lead-category:display-create-lead-category-form', ['parentId' => $leadCategory->getValue()->id]) }}">Add Child</a></div>
	@if ($leadCategory->getHasChildren())
	<div class="lead-category-children">
		@foreach ($leadCategory->getChildren() as $child)
		<x-lead-category :leadCategory="$child" />
		@endforeach
	</div>
	@endif
</div>
