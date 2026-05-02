import type { JsonData } 				from '@typedefs/JsonData';
import type { IMessage, IApiResponse }	from '@typedefs/IApiResponse';
import		ApiModel					from '@classes/ApiModel';

// eslint-disable-next-line @typescript-eslint/no-unused-vars
export default class ApiResponse implements IApiResponse
{
	
	data:			Record<string, JsonData>	= {};
	messages:		Record<string, IMessage[]>	= {};
	
	// Get all data from the API response.
	getAllData() : Record<string, JsonData>
	{
		return this.data;
	}
	
	// Get a specified value from the API response data.
	getData(field: string) : JsonData
	{
		const value = this.getAllData()[field] ?? undefined;
		if (undefined === value) {
			throw new Error(`The supplied key "${field}" does not exist in the API response data.`);
		} else {
			return value;
		}
	}
	
	// Get whether a specified value exists in the API response.
	hasData(field: string) : boolean
	{
		const value = this.data[field] ?? undefined;
		return (undefined === value);
	}
	
	// Get an array of ApiModel instances from a top-level data value.
	getDataAsApiModels(field: string) : ApiModel[]
	{
		const results = [];
		const apiModels = this.getData(field) as unknown as ApiModel[];
		for (const apiModel of apiModels) {
			results.push(Object.assign(new ApiModel(), apiModel));
		}
		return results;
	}
	
	// If a field name is specified, then return whether that field has
	// any errors. Otherwise, return whether any errors exist on any field.
	hasErrors(field?: string): boolean { 
		if (undefined === field) {
			return Object.keys(this.getAllErrors()).length > 0;
		} else {
			return this.getErrors(field).length > 0;
		}
	}
	
	// Get all error messages for a given field, as strings.
	getErrors(field: string) : string[]
	{
		const errors = this.getAllErrors();
		return errors[field] ?? [];
	}
	
	// Get all error messages, as an object whose keys are field names,
	// and whose values are error messages (as strings).
	getAllErrors() : Record<string, string[]>
	{
		
		// Discard any non-error messages., and convert all Message
		// instances into their string text values.
		const allErrors: Record<string, string[]> = {};
		for (const [field, messages] of Object.entries(this.messages)) {
			allErrors[field] = messages
				.filter((msg: IMessage) => 'error' === msg.type)
				.map((msg: IMessage) => msg.text);
		}
		return allErrors;
		
	}
	
}
