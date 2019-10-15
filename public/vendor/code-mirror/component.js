import 'https://better-js.fenz.land/index.js';
import CodeMirror from './code-mirror.js';

const styleSheet= new CSSStyleSheet();

export default class CodeMirrorElement extends HTMLElement
{
	#document;
	
	#codeMirror;
	
	constructor()
	{
		super();
		
		this.#document= this.attachShadow( { mode: 'open', }, );
		this.#document.adoptedStyleSheets= [ styleSheet, ];
		
		const tags= document.createElement( 'div', );
		tags.classList.add( 'tags', );
		
		this.#document.appendChild( tags, );
		
		const filename= this.getAttribute( 'filename', );
		const filenameTag= document.createElement( 'span', );
		filenameTag.classList.add( 'filename-tag', );
		filenameTag.appendChild( document.createTextNode( filename|| '', ), )
		
		tags.appendChild( filenameTag, );
		
		const langage= this.getAttribute( 'language', )|| 'text';
		const languageTag= document.createElement( 'span', );
		languageTag.classList.add( 'language-tag', );
		languageTag.appendChild( document.createTextNode( langage, ), )
		
		tags.appendChild( languageTag, );
		
		const content= unifyCode( this.childNodes.mapAndFilter( node=> node instanceof Text&& node.data, ).implode(), );
		
		this.#codeMirror= new CodeMirror( this.#document, {
			value: content,
			mode: langage,
			indentUnit: 4,
			indentWithTabs: true,
			readOnly: this.hasAttribute( 'readonly', ),
		}, );
		
	}
	
	connectedCallback()
	{
		const content= unifyCode( this.childNodes.mapAndFilter( node=> node instanceof Text&& node.data, ).implode(), );
		
		this.#codeMirror.setValue( content, );
	}
	
	disconnectedCallback()
	{
		
	}
	
	adoptedCallback()
	{
		
	}
	
	static get observedAttributes()
	{
		return [ 'language', 'filename', 'readonly', ];
	}
	
	attributeChangedCallback( attribute, oldValue, newValue, )
	{
		const method= `attribute${attribute.toCamelCase( true, '-', )}ChangedCallback`;
		
		if( this[method] )
			this[method]( newValue, oldValue );
	}
	
	attributeLanguageChangedCallback( language, )
	{
		this.#codeMirror.setOption( 'mode', language, );
		this.#document.querySelector( '.language-tag', ).firstChild.data= language;
	}
	
	attributeFilenameChangedCallback( filename, )
	{
		this.#document.querySelector( '.filename-tag', ).firstChild.data= filename|| '';
	}
	
	attributeReadonlyChangedCallback( readonly, )
	{
		this.#codeMirror.setOption( 'readOnly', readonly !== null, );
	}
}

(async()=> {
	const CSSUrl= import.meta.url.replace( /\/[^/]+(?:\?.*)?(?:#.*)?$/, '/code-mirror.css' );
	const $CSSCode= fetch( CSSUrl, ).then( response=> response.text(), )
	
	await styleSheet.replace( await $CSSCode, );
	
	customElements.define( 'code-mirror', CodeMirrorElement, );
})();

function unifyCode( text, )
{
	const lines= text.split( '\n', );
	
	if( lines.length < 1 )
		return '';
	
	if( lines[0] === '' )
		lines.shift();
	
	if( lines.length < 1 )
		return '';
	
	const indentation= lines[0].matchGroup( /^\t*/, 0, );
	const regex= new RegExp( `^${indentation}`, );
	
	if( lines.get( -1, ) === indentation.replace( /\t$/, '', ) )
		lines.pop();
	
	return lines.map( line=> line.replace( regex, '', ), ).implode( '\n', );
}
