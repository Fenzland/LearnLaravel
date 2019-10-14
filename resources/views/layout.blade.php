<!DOCTYPE html
><html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
	><head
		><meta charset="utf-8"
		><meta name="viewport" content="width=device-width, initial-scale=1"
		><title>@yield('title', 'Learn Laravel')</title
		><!-- Fonts 
		--><link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet"
		><link href="https://fonts.googleapis.com/css?family=Fira%20Code:400" rel="stylesheet"
		><!-- Styles 
		--><style
		>
			:root
			{
				--theme-color-hue: 3;
				--theme-color: hsla(var(--theme-color-hue),100%,56%,1); 
				--active-color-hue: 208;
				--active-color: hsla(var(--active-color-hue),100%,50%,1);
				--base-color: hsla(200,6%,41%,1);
				
				font-family: 'Nunito', sans-serif;
				font-weight: 200;
				height: 100%;
				line-height: 2;
				
				color: var(--base-color);
			}
			
			code
			{
				font-family: 'Fira Code', monospace;
			}
			
			a:link
			{
				--color-transition: 250ms;
				
				transition:
					color var(--color-transition)
					,
					text-decoration-color var(--color-transition)
				;
			}
			
			a:link
			{
				color: var(--active-color);
				text-decoration-color: hsla(var(--active-color-hue),100%,50%,0.5);
			}
			
			a:visited:not(:active)
			{
				color: var(--active-color);
				text-decoration-color: hsla(var(--active-color-hue),100%,50%,0.25);
			}
			
			a:link:active
			{
				text-decoration-color: hsla(var(--theme-color-hue),100%,50%,0.5);
				color: var(--theme-color);
			}
			
			a:link:not(:focus):not(:hover):not(:active)
			{
				text-decoration-color: hsla(var(--active-color-hue),100%,50%,0);
				--color-transition: 1000ms;
			}
			
			svg svg
			{
				overflow: visible;
			}
			
			::selection
			{
				background-color: transparent;
				text-shadow: 0 0 1px, 0 0 2px;
			}
			
			:root>body
			{
				display:flex;
				width: 100%;
				height: 100%;
				margin: 0;
				flex-direction: column;
				align-items: stretch;
			}
			
			:root>body>header
			{
				text-align: center;
			}
			
			:root>body>header>h1
			{
				font-size: 2rem;
				font-weight: 200;
			}
			
			:root>body>header>hr
			{
				border-color: var(--theme-color);
				border-style: solid;
				margin-left: 25vw;
				margin-right: 25vw;
				margin-bottom: 2rem;
				border-radius: 50%;
			}
			
			:root>body>header>p.slogan
			{
				margin-top: 3rem;
				margin-bottom: 2rem;
			}
			
			:root>body>header>p.slogan:empty
			{
				display: none;
			}
			
			:root>body>main
			{
				flex-grow: 1;
			}
			
			:root>body>main
			{
				width: 100vmin;
				min-width: 60vw;
				max-width: calc( 100vw - 2rem );
				margin-left: auto;
				margin-right: auto;
				padding-left: 1rem;
				padding-right: 1rem;
			}
			
			:root>body>footer
			{
				text-align: center;
			}
			
			article>p code
			{
				font-size: 0.875rem;
				padding: 0.125em;
				border-radius: 0.125em;
				
				background-color: hsla(var(--theme-color-hue),100%,87.5%,0.25);
			}
			
			article>figure
			{
				text-align: center;
			}
			
			code-mirror:not(:defined)
			{
				white-space: pre;
				font-family: 'Fira Code', monospace;
				font-size: 75%;
			}
			
			@yield('styles')
		</style
		><script type="module" src="/vendor/code-mirror/component.js"></script
	></head
	><body
		><header
			><h1>@yield('title')</h1
			><hr></hr
			><p class="slogan">@yield('slogan')</p
		></header
		><main
			@yield('main')
		></main
		><footer>&copy;Fenzland</footer
	></body
></html
>