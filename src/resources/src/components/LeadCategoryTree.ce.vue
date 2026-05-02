<script setup lang="ts">
	import		{ reactive, ref, provide, onMounted } from 'vue';
	import		{ fetchWithCsrf }	from '@/main';
	import		LeadCategory		from '@classes/LeadCategory';
	import		ApiResponse			from '@classes/ApiResponse';
	import type	{ IApiResponse }	from '@typedefs/IApiResponse';
	import		ApiModel			from '@classes/ApiModel';
	import type	{ IApiModel }		from '@typedefs/IApiModel';
	import type	{ JsonData }		from '@typedefs/JsonData';
	
	// The top-level lead categories.
	const categories: Array<LeadCategory> = reactive([]);
	
	// The lead category currently being edited in the editor.
	// This will be a reactive proxy, so any changes made to
	// it will be reflected in the tree of categories.
	let category: LeadCategory|null = null;
	
	const categoryId		= ref<string|null>(null);
	const categoryParentId	= ref<string|null>(null);
	const categoryLabel		= ref<string|null>('');
	
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
		const result		= await fetchWithCsrf('/api/v1/system-admin/lead-category/by-parent-id/');
		const rawData		= await result.json();
		const apiResponse	= Object.assign(new ApiResponse(), rawData);

		// Add each root category to this component's children.
		const rootCategoryModels = apiResponse.getDataAsApiModels('leadCategories');
		for (let rootCategoryModel of rootCategoryModels) {
			let rootCategory: LeadCategory = Object.assign(
				new LeadCategory(),
				rootCategoryModel.getAllProperties()
			)
			categories.push(rootCategory);
		}
		
	});
	
	// Handle child component events for loading a category into the editor.
	function editCategory(categoryToEdit: LeadCategory|null)
	{
		category = categoryToEdit;
	}
	
	// Handle the addition of a new root category.
	function addRootCategory()
	{
		category = new LeadCategory();
		categories.push(category);
	}
	
	// Handle an attempt to save a lead category.
	async function saveCategory()
	{	
		fetchWithCsrf(
			'/api/v1/system-admin/lead-category',
			{
				method: 'POST',
				headers: {
					'Content-Type':	'application/json',
				},
				body: JSON.stringify({
					leadCategory: {
						id:			categoryId.value,
						parentId:	categoryParentId.value,
						label:		categoryLabel.value,
					}
				})
			}
		).then((data: object) => {
			
		})
		.catch((error: Error) => {
			
		});
		
	}
	
</script>

<template>
	<div class="editor" v-if="category" id="lead-category-editor">
		<label for="lead-category-editor-label">Label</label>
		<input id="lead-category-editor-label" type="text" v-model="categoryLabel" />
		<input type="hidden" id="lead-category-editor-id" v-model="categoryId" />
		<input type="hidden" id="lead-category-editor-parent-id" v-model="categoryParentId" />
		<button @click="saveCategory">Save</button>
	</div>
	<div class="tree">
		<lead-category-tree-item
			v-for="category in categories"
			@edit-category="editCategory"
			:key="category.id"
			:category="category"
			:csrfToken="csrfToken"
		/>
		<div class=""><a @click.prevent="addRootCategory">New</a></div>
	</div>
</template>

<style scoped>

</style>
