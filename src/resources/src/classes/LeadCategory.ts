// eslint-disable-next-line @typescript-eslint/no-unused-vars
export default class LeadCategory
{
	
	id:				string | null;
	parentId:		string | null;
	label:			string;
	children:		Array<LeadCategory> | null;
	
	constructor()
	{
		this.id			= null;
		this.parentId	= null;
		this.label		= '';
		this.children	= null;
	}
	
}
