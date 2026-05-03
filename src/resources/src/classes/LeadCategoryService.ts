import	LeadCategory		from '@classes/LeadCategory';
import	ApiResponse			from '@classes/ApiResponse';
import	{ fetchWithCsrf }	from '@/main';

// eslint-disable-next-line @typescript-eslint/no-unused-vars
export default class LeadCategoryService
{
	
	// Get an array of LeadCategory instances from a given parent ID.
	async getLeadCategories(parentId: string|null) : Promise<LeadCategory[]>
	{
		
		// Determine the API URL.
		const escParentId	= encodeURIComponent(parentId ?? '');
		const url			= `/api/v1/system-admin/lead-category/by-parent-id/${escParentId}`;
		
		// Retrieve categories from the API by parent ID.
		const result		= await fetchWithCsrf(url);
		const rawData		= await result.json();
		const apiResponse	= Object.assign(new ApiResponse(), rawData);

		// Add each root category to this component's children.
		const results: LeadCategory[] = [];
		const categoryModels = apiResponse.getDataAsApiModels('leadCategories');
		for (const categoryModel of categoryModels) {
			const rootCategory: LeadCategory = Object.assign(
				new LeadCategory(),
				categoryModel.getAllProperties()
			)
			results.push(rootCategory);
		}
		return results;
		
	} 
	
	// Attempt to save a LeadCategory.
	async saveLeadCategory(category: LeadCategory) : Promise<ApiResponse>
	{
		
		// Determine the API URL.
		const url = `/api/v1/system-admin/lead-category`;
				
		// Attempt to save the lead category.
		const result = await fetchWithCsrf(
			url,
			{
				method: 'POST',
				headers: {
					'Content-Type':	'application/json',
				},
				body: JSON.stringify({
					leadCategory: {
						id:			category.id,
						parentId:	category.parentId,
						label:		category.label,
					}
				})
			}
		);
		const rawData		= await result.json();
		const apiResponse	= Object.assign(new ApiResponse(), rawData);
		
		return apiResponse;
		
	}
	
}
