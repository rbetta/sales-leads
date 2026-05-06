<script setup lang="ts">
	import		{ reactive, ref, provide, onMounted } from 'vue';
	import		{ fetchWithCsrf }	from '@/main';
	import		LeadCategory		from '@classes/LeadCategory';
	import		LeadCategoryService	from '@classes/LeadCategoryService';
	import		ApiResponse			from '@classes/ApiResponse';
	import type	{ IApiResponse }	from '@typedefs/IApiResponse';
	import		ApiModel			from '@classes/ApiModel';
	import type	{ IApiModel }		from '@typedefs/IApiModel';
	import type	{ JsonData }		from '@typedefs/JsonData';
	
	// The top-level lead categories.
	const categories: Array<LeadCategory> = reactive([]);
	
	// Data describing the lead category currently being displayed
	// in the editor.
	const categoryToEdit = ref<LeadCategory|null>(null);
	
	// Form submission errors for the "Create/Edit Category" form.
	const editCategoryErrors = ref<Record<string, string[]>>({});
	
	const props = defineProps({
					
		// The Cross-Site Request Forgery token.
		csrfToken: {
			type: String,
			validator(value) {
				return ('' !== value);
			}
		},
		
	});
	
	provide('csrfToken', props.csrfToken);
	
	// Initially populate this component.
	onMounted(async () => {
		
		// Retrieve root categories from the API.
		const service			= new LeadCategoryService();
		const rootCategories	= await service.getLeadCategories(null);
		
		// Add root categories to the DOM.
		for (const rootCategory of rootCategories) {
			categories.push(rootCategory);
		}
		
	});
	
	// Handle child component events for loading a category into the editor.
	function editCategory(e: CustomEvent)
	{
		// Extract the category to edit from the event.
		const [categoryData]: [LeadCategory] = e.detail;

		// Update the ref containing the property to edit.
		categoryToEdit.value = categoryData;

	}
	
	// Handle the addition of a new root category.
	function addRootCategory()
	{
		const newCategory = new LeadCategory();
		categoryToEdit.value = newCategory;
	}
	
	// Handle an attempt to save a lead category.
	async function saveCategory()
	{
		
		// Obtain the lead category from the editor.
		const categoryToSave: LeadCategory|null = categoryToEdit.value;
		
		// Sanity check.
		if (null === categoryToSave) {
			return;
		}
		
		// Is the category new?
		const isNew: boolean = ('' === (categoryToSave.id ?? ''));
		
		// Attempt to save the category.
		const service		= new LeadCategoryService();
		const apiResponse	= await service.saveLeadCategory(categoryToSave);
		
		// Retrieve errors from the response.
		editCategoryErrors.value = apiResponse.getAllErrors();
		
		// Handle a successful save operation.
		if (! apiResponse.hasErrors()) {
			
			// Retrieve the lead category returned by the server.
			const apiModelFromServer: ApiModel		= Object.assign(new ApiModel(), apiResponse.getData('leadCategory'));
			const categoryFromServer: LeadCategory	= Object.assign(new LeadCategory(), apiModelFromServer.getAllProperties());
			
			if (isNew) {
				// This is a new category; append it.
				categoryToSave.id		= categoryFromServer.id;
				categoryToSave.parentId	= categoryFromServer.parentId;
				categoryToSave.label	= categoryFromServer.label;
				categories.push(categoryToSave);
			} else {
				// This is an existing category; update it.
				const indexToUpdate = findChildCategoryIndex(categoryToSave);
				if (null !== indexToUpdate) {
					// Only the label can be updated.
					(categories[indexToUpdate] as LeadCategory).label = categoryFromServer.label;
				} 
			}
			
			// Close the editor.
			categoryToEdit.value = null;
		}
		
	}
	
	// Handle an attempt to delete a lead category.
	async function deleteCategory(e: CustomEvent)
	{
		
		// Extract the category to delete from the event.
		const [categoryToDelete]: [LeadCategory] = e.detail;

		// Sanity check.
		if (null === categoryToDelete) {
			return;
		}
		
		// Attempt to delete the category.
		const deleteChildren	= false;	// Don't delete children; promote them instead.
		const service			= new LeadCategoryService();
		const apiResponse		= await service.deleteLeadCategory(categoryToDelete, deleteChildren);
		
		// Handle a successful deletion.
		if (! apiResponse.hasErrors()) {
			
			// Close the editor if the category being edited was deleted.
			if (categoryToEdit.value?.id === categoryToDelete.id) {
				categoryToEdit.value = null;
			}
			
			// Remove the deleted category from this component's children.
			const indexToDelete = findChildCategoryIndex(categoryToDelete);
			if (null !== indexToDelete) {
				categories.splice(indexToDelete, 1);
			}
			
		}
		
	}
	
	// Retrieve the index of the children property for a matching
	// LeadCategory by ID. If it is not present, or if the supplied
	// LeadCategory instance has no ID, then throw an exception.
	function findChildCategoryIndex(category: LeadCategory) : number|null
	{
		
		// Sanity check.
		if ('' === (category.id ?? '')) {
			throw new Error(
				'A LeadCategory supplied to the child searcher had no ID.'
			);
		}
		
		// Find the index of the matching category.
		for (const [index, existingCategory] of categories.entries()) {
			if (category.id === existingCategory.id) {
				return index;
			}
		}
		
		// The lead category was not found among the children.
		return null;
		
	}

</script>

<template>
	<template v-if="categoryToEdit">
		<div class="editor" id="lead-category-editor">
			<label for="lead-category-editor-label">Label</label>
			<input type="text" id="lead-category-editor-label" v-model="categoryToEdit.label" />
			<div class="error" v-for="error in editCategoryErrors['label'] ?? []">{{ error }}</div>
			<input type="hidden" id="lead-category-editor-id" v-model="categoryToEdit.id" />
			<input type="hidden" id="lead-category-editor-parent-id" v-model="categoryToEdit.parentId" />
			<button @click="saveCategory">Save</button>
		</div>
	</template>
	<div class="tree">
		<lead-category-tree-item
			v-for="category in categories"
			@edit-category="editCategory"
			@delete-category="deleteCategory"
			:key="category.id"
			:category="category"
			:csrfToken="csrfToken"
		/>
		<div class=""><a @click.prevent="addRootCategory">New</a></div>
	</div>
</template>

<style scoped>

</style>
