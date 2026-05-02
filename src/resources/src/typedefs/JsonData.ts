// eslint-disable-next-line @typescript-eslint/no-unused-vars
type JsonData	= string
				| number
				| boolean
				| null
				| JsonData[]
				| { [key: string]: JsonData };
export type { JsonData };