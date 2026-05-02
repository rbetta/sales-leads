import type { JsonData }  from '@typedefs/JsonData';

// eslint-disable-next-line @typescript-eslint/no-unused-vars
export interface IApiModel
{
	
	properties:		Record<string, JsonData>;
	relationships:	Record<string, JsonData>;
	type:			string;
	
	getAllProperties() : Record<string, JsonData>;
	hasProperty(field: string) : boolean;
	getProperty(field: string) : JsonData;
	
}
