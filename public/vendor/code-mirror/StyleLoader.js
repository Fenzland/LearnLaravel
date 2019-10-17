import Promisive from './Promisive.js';

export default (document.adoptedStyleSheets
	?class StyleLoader extends Promisive {
		// #styleSheets= [];
		
		constructor( ...urls )
		{
			const styleSheets= [];
			
			super( Promise.all(
				urls.map( url=> {
					const styleSheet= new CSSStyleSheet();
					
					styleSheets.push( styleSheet, );
					
					fetch( url, )
						.then( response=> response.text(), )
						.then( CSS=> styleSheet.replace( CSS, ), )
					;
				}, ),
			) );
			
			this._styleSheets= styleSheets;
		}
		
		apply( documentOrShadowRoot, )
		{
			documentOrShadowRoot.adoptedStyleSheets= [ ...this._styleSheets, ];
		}
	}
	:class StyleLoader extends Promisive {
		// #urls= [];
		
		constructor( ...urls )
		{
			super();
			
			this._urls= urls;
		}
		
		apply( documentOrShadowRoot, )
		{
			this._urls.forEach( url=> {
				const linkElement= document.createElement( 'link', );
				
				linkElement.href= url;
				linkElement.rel= 'stylesheet';
				
				documentOrShadowRoot.appendChild( linkElement, );
			}, );
		}
	}
);
