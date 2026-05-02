import type { JsonData } 	from '@typedefs/JsonData';
import type { IApiModel }	from '@typedefs/IApiModel';

// eslint-disable-next-line @typescript-eslint/no-unused-vars
export default class ApiModel implements IApiModel
{
	properties:		Record<string, JsonData>	= {};
	relationships:	Record<string, JsonData>	= {};
	type:			string						= '';
	
	// Get all properties from the model.
	getAllProperties() : Record<string, JsonData>
	{
		return this.properties;
	}
	
	// Get whether a specified property exists in the model.
	hasProperty(field: string) : boolean
	{
		const value = this.properties[field] ?? undefined;
		return (undefined === value);
	} 
	
	// Get a specified value from the model.
	getProperty(field: string) : JsonData
	{
		const value = this.getAllProperties()[field] ?? undefined;
		if (undefined === value) {
			throw new Error(`The supplied property "${field}" does not exist in the model returned by the API response.`);
		} else {
			return value;
		}
	}
		
}
