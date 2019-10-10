@extends('layout')

@section('title', 'Learn Laravel')

@section('slogan', 'Let\'s Learn Laravel. Let\'s be Web Artisans.')

@section('styles')
	
	:root>body>header>h1
	{
		font-size: 4rem;
	}
	
	:root>body>main>ul.post-list>li
	{
		color: var(--theme-color);
	}
	
	:root>body>main>ul.post-list>li>*
	{
		color: var(--base-color);
	}
	
@endsection

@section('main')
	><ul class="post-list"
		><li
			><h3>First Guide</h3
			><p>This is not another manual or documentation of Laravel. We not talk about how to use, but about how to understanding.</p
			><p
				><a href="/first-guide">Read more &gt;&gt;</a
			></p
		></li
		><li
			><h3>from Request to Response</h3
			><p>As a web framework, the main work of Laravel is to accept a Request then to send a Response.</p
			><p
				><a href="/from-request-to-response">Read more &gt;&gt;</a
			></p
		></li
	></ul
@endsection
