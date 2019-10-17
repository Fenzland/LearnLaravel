
export default class Promisive
{
	// #promise;
	
	constructor( promise=Promise.resolve(), )
	{
		this._promise= promise;
	}
	
	then( ...args )
	{
		this._promise.then( ...args, );
	}
	
	catch( ...args )
	{
		this._promise.catch( ...args, );
	}
	
	finally( ...args )
	{
		this._promise.finally( ...args, );
	}
}
