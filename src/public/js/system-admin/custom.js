jQuery(document).ready(function () {
	
	// Handle the user clicking on the "Delete" button for a lead category.
	jQuery('.lead-category form[data-action="delete-lead-category"]').on('submit', function (e) {
		
		// Ask the user to confirm deletion.
		let label	= jQuery(this).closest('.lead-category').children('.lead-category-label').text();
		if (! confirm('Are you sure you want to delete the following lead category?\n\n' + label)) {
			
			// The user cancelled the deletion request.	
			e.preventDefault();
			
		}
		
	});
	
});