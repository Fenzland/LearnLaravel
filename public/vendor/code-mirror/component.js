import 'https://better-js.fenz.land/index.js';
import CodeMirror from './code-mirror.js';
import StyleLoader from './StyleLoader.js';

const styleLoader= new StyleLoader(
	import.meta.url.replace( /\/[^/]+(?:\?.*)?(?:#.*)?$/, '/code-mirror.css' ),
);

export default class CodeMirrorElement extends HTMLElement
{
	// POLYFILL for not supporting properties
	// #document;
	
	// #codeMirror;
	
	constructor()
	{
		super();
		
		this._document= this.attachShadow( { mode: 'open', }, );
		styleLoader.apply( this._document, );
		
		const tags= document.createElement( 'div', );
		tags.classList.add( 'tags', );
		
		this._document.appendChild( tags, );
		
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
		
		this._codeMirror= new CodeMirror( this._document, {
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
		
		this._codeMirror.setValue( content, );
	}
	
	disconnectedCallback()
	{
		
	}
	
	adoptedCallback()
	{
		
	}
	
	static get observedAttributes()
	{
		return [ 'language', 'filename', 'readonly', 'width', 'height', ];
	}
	
	attributeChangedCallback( attribute, oldValue, newValue, )
	{
		const method= `attribute${attribute.toCamelCase( true, '-', )}ChangedCallback`;
		
		if( this[method] )
			this[method]( newValue, oldValue );
	}
	
	attributeLanguageChangedCallback( language, )
	{
		this._codeMirror.setOption( 'mode', language, );
		this._document.querySelector( '.language-tag', ).firstChild.data= language;
	}
	
	attributeFilenameChangedCallback( filename, )
	{
		this._document.querySelector( '.filename-tag', ).firstChild.data= filename|| '';
	}
	
	attributeReadonlyChangedCallback( readonly, )
	{
		this._codeMirror.setOption( 'readOnly', readonly !== null, );
	}
	
	attributeWidthChangedCallback( width, )
	{
		if( width === null )
			this._codeMirror.setSize( 'auto', null, );
		
		this._codeMirror.setSize( width, null, );
	}
	
	attributeHeightChangedCallback( height, )
	{
		if( height === null )
			this._codeMirror.setSize( null, 'auto', );
		
		this._codeMirror.setSize( null, height, );
	}
}

styleLoader.then( ()=> {
	customElements.define( 'code-mirror', CodeMirrorElement, );
}, );

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
