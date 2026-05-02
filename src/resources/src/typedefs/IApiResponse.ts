import type { JsonData } from '@typedefs/JsonData';

export interface IMessage
{
	text:			string;
	displayToUser:	boolean;
	type:			string;
};

// eslint-disable-next-line @typescript-eslint/no-unused-vars
export interface IApiResponse
{
	data:			Record<string, JsonData>;
	messages:		Record<string, IMessage[]>;
	
	// Get values from the top-level "data" attribute.
	getAllData() : Record<string, JsonData>;
	getData(field: string) : JsonData;
	hasData(field: string) : boolean;
	
	// Get errors from the top-level "messages" attribute
	// (filtering out any non-error messages).
	hasErrors(field?: string): boolean;
	getErrors(field: string) : string[];
	getAllErrors() : Record<string, string[]>;
	
}
