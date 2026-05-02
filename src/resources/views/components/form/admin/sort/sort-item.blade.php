<div class="admin-sorter" id="{{ $id }}">
	<div class="admin-sorter-items">
    	@foreach ($existingItems as $existingItem)
    	<div class="admin-sorter-item existing {{ ((string) $existingItem->getAdminSorterInstanceId() === (string) $itemInstanceId) ? 'allow-reordering' : 'disallow-reordering' }}">
    		<div class="admin-sorter-item-handle"></div>
        	<div class="admin-sorter-item-label" data-sortable-item-label-source-id="{{ ((string )$existingItem->getAdminSorterInstanceId() === (string) $itemInstanceId) ? $sourceIdForItemLabel : '' }}">{{ $existingItem->getAdminSorterLabel() }}</div>
    	</div>
    	@endforeach
    	@if ("" === (string) $itemInstanceId)
    	<div class="admin-sorter-item new allow-reordering">
    		<div class="admin-sorter-item-handle"></div>
        	<div class="admin-sorter-item-label" data-sortable-item-label-source-id="{{ $sourceIdForItemLabel }}"></div>
    	</div>
    	@endif
    </div>
	<input type="hidden" name="{{ $name }}" data-field-type="sortable-item-index" value="{{ old($toArrayDotSyntax($name)) ?? '' }}">
	<div class="field-error">{{ $errors->first($toArrayDotSyntax($name)) }}</div>
</div>
