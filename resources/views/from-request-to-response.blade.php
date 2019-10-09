@extends('layout')

@section('title', 'from Request to Response')

@section('main')
	><article
		><p>As a web framework, the main work of Laravel is to accept a Request then to send a Response.</p
		><p>Take look at the <code>index.php</code> of a Laravel project:</p
		><code-mirror
			language="PHP"
			readonly="readonly"
		>
			$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
			
			$response = $kernel->handle(
			    $request = Illuminate\Http\Request::capture()
			);
			
			$response->send();
		</code-mirror
		><p>The process is simple, they make a <strong>kernal</strong>, capture the <strong>request</strong>, then handle the request with the kernal so get a <strong>response</strong>, and send the response. </p
		><figure
			><svg
				viewBox="0 0 768 128"
				><svg
					x="128"
					y="96"
					><text
						x="0"
						y="4"
						text-anchor="middle"
					>capture()</text
					><path
						d="
							M -64,0
								l 24, 0
								l -12, -12
								l 88, 0
								l 12, 12
								l 16, 0
								m -16, 0
								l -12, 12
								l -88, 0
								l 12, -12
						"
						fill="transparent"
						stroke="hsla(0,0%,0%,0.5)"
						stroke-width="1"
				/></svg
				><svg
					x="256"
					y="96"
					><text
						x="0"
						y="4"
						text-anchor="middle"
					>Request</text
					><path
						d="
							M -64,0
								c 24,0 32,-24 64,-24
								c 32,0 40,24 64,24
								c -24,0 -32,24 -64,24
								c -32,0 -40,-24 -64,-24
							z
						"
						fill="transparent"
						stroke="hsla(0,0%,0%,0.5)"
						stroke-width="1"
				/></svg
				><svg
					x="384"
					y="32"
					><text
						x="0"
						y="4"
						text-anchor="middle"
					>Kernel</text
					><path
						d="
							M 0,16
								l -32, 0
								l 0, -32
								l 64, 0
								l 0, 32
								l -32, 0
								l 0, 36
						"
						fill="transparent"
						stroke="hsla(0,0%,0%,0.5)"
						stroke-width="1"
				/></svg
				><svg
					x="384"
					y="96"
					><text
						x="0"
						y="4"
						text-anchor="middle"
					>handle()</text
					><path
						d="
							M -64,0
								l 24, 0
								l -12, -12
								l 88, 0
								l 12, 12
								l 16, 0
								m -16, 0
								l -12, 12
								l -88, 0
								l 12, -12
						"
						fill="transparent"
						stroke="hsla(0,0%,0%,0.5)"
						stroke-width="1"
				/></svg
				><svg
					x="512"
					y="96"
					><text
						x="0"
						y="4"
						text-anchor="middle"
					>Response</text
					><path
						d="
							M -64,0
								c 24,0 32,-24 64,-24
								c 32,0 40,24 64,24
								c -24,0 -32,24 -64,24
								c -32,0 -40,-24 -64,-24
							z
						"
						fill="transparent"
						stroke="hsla(0,0%,0%,0.5)"
						stroke-width="1"
				/></svg
				><svg
					x="640"
					y="96"
					><text
						x="0"
						y="4"
						text-anchor="middle"
					>send()</text
					><path
						d="
							M -64,0
								l 24, 0
								l -12, -12
								l 88, 0
								l 12, 12
								l 16, 0
								m -16, 0
								l -12, 12
								l -88, 0
								l 12, -12
						"
						fill="transparent"
						stroke="hsla(0,0%,0%,0.5)"
						stroke-width="1"
				/></svg
			></svg
		></figure
	></article
@endsection
