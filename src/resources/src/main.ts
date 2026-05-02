import { defineCustomElement } from 'vue'

// Import components to load as custom elements.
import LeadCategoryTree from '@components/LeadCategoryTree.ce.vue';
import LeadCategoryTreeItem from '@components/LeadCategoryTreeItem.ce.vue';

// Define custom elements.
customElements.define('lead-category-tree', defineCustomElement(LeadCategoryTree))
customElements.define('lead-category-tree-item', defineCustomElement(LeadCategoryTreeItem))

// Wrap the fetch API to properly handle our CSRF tokens.
export async function fetchWithCsrf(target: string | Request, options: RequestInit = {}) : Promise<Response>
{
	
	// Obtain the CSRF token.
	// Note: uses the first matching header (assumes only one exists).
	const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
	
	// Merge the supplied options with the CSRF token.
	const headers: object = options.headers ?? {};
	const mergedOptions = {
		...options,
		...{
			headers : {
				...headers,
				...{'X-CSRF-TOKEN': csrfToken}
			}
		}
	};
	
	// Invoke the fetch API.
	return fetch(target, mergedOptions);
	
}
