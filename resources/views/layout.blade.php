<!DOCTYPE html
><html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
	><head
		><meta charset="utf-8"
		><meta name="viewport" content="width=device-width, initial-scale=1"
		><title>@yield('title', 'Learn Laravel')</title
		><!-- Fonts 
		--><link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet"
		><link href="https://fonts.googleapis.com/css?family=Fira%20Code:200" rel="stylesheet"
		><!-- Styles 
		--><style
		>
			:root
			{
				--theme-color-hue: 3;
				--theme-color: hsla(var(--theme-color-hue),100%,56%,1); 
				--base-color: hsla(200,6%,41%,1);
				
				font-family: 'Nunito', sans-serif;
				font-weight: 200;
				color: var(--base-color);
				height: 100%;
			}
			
			code
			{
				font-family: 'Fira Code', monospace;
			}
			
			svg svg
			{
				overflow: visible;
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
				margin-left: auto;
				margin-right: auto;
				padding-left: 1em;
				padding-right: 1em;
			}
			
			:root>body>footer
			{
				text-align: center;
			}
			
			p code
			{
				font-size: 0.875rem;
				padding: 0.125em;
				border-radius: 0.125em;
				
				background-color: hsla(var(--theme-color-hue),100%,87.5%,0.25);
			}
			
			code-mirror:not(:defined)
			{
				white-space: pre;
			}
			
			@yield('styles')
		</style
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