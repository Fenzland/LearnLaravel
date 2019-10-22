@extends('layout')

@section('title', 'from Request to Response')

@section('main')
	><article
		><p>As a web framework, the main work of Laravel is to accept a Request then to send a Response.</p
		><p>Take look at the <code>public/index.php</code> of a Laravel project:</p
		><code-mirror
			language="PHP"
			readonly="readonly"
			filename="public/index.php"
		>@subindent
			$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
			
			$response = $kernel->handle(
			    $request = Illuminate\Http\Request::capture()
			);
			
			$response->send();
		@endsubindent</code-mirror
		><p>The process is simple, they make a <strong>kernel</strong>, capture the <strong>request</strong>, then handle the request with the kernel so get a <strong>response</strong>, and send the response. </p
		><figure
			><svg
				viewBox="0 0 768 128"
				style="max-width:768px; background-color:hsla(0,0%,0%,0.015625)"
				><svg
					x="128"
					y="96"
					><path
						d="
							M -64,0
								l 24,0
								l -12,-12
								l 88,0
								l 12,12
								l 16,0
								m -16,0
								l -12,12
								l -88,0
								l 12,-12
						"
						fill="white"
						stroke="hsla(0,0%,0%,0.5)"
						stroke-width="1"
					/><text
						x="0"
						y="4"
						text-anchor="middle"
					>capture()</text
				></svg
				><svg
					x="256"
					y="96"
					><path
						d="
							M -64,0
								c 24,0 32,-24 64,-24
								c 32,0 40,24 64,24
								c -24,0 -32,24 -64,24
								c -32,0 -40,-24 -64,-24
							z
						"
						fill="white"
						stroke="hsla(0,0%,0%,0.5)"
						stroke-width="1"
					/><text
						x="0"
						y="4"
						text-anchor="middle"
					>Request</text
				></svg
				><svg
					x="384"
					y="32"
					><path
						d="
							M 0,16
								l -32,0
								l 0,-32
								l 64,0
								l 0,32
								l -32,0
								l 0,36
						"
						fill="white"
						stroke="hsla(0,0%,0%,0.5)"
						stroke-width="1"
					/><text
						x="0"
						y="4"
						text-anchor="middle"
					>Kernel</text
				></svg
				><svg
					x="384"
					y="96"
					><path
						d="
							M -64,0
								l 24,0
								l -12,-12
								l 88,0
								l 12,12
								l 16,0
								m -16,0
								l -12,12
								l -88,0
								l 12,-12
						"
						fill="white"
						stroke="hsla(0,0%,0%,0.5)"
						stroke-width="1"
					/><text
						x="0"
						y="4"
						text-anchor="middle"
					>handle()</text
				></svg
				><svg
					x="512"
					y="96"
					><path
						d="
							M -64,0
								c 24,0 32,-24 64,-24
								c 32,0 40,24 64,24
								c -24,0 -32,24 -64,24
								c -32,0 -40,-24 -64,-24
							z
						"
						fill="white"
						stroke="hsla(0,0%,0%,0.5)"
						stroke-width="1"
					/><text
						x="0"
						y="4"
						text-anchor="middle"
					>Response</text
				></svg
				><svg
					x="640"
					y="96"
					><path
						d="
							M -64,0
								l 24,0
								l -12,-12
								l 88,0
								l 12,12
								l 16,0
								m -16,0
								l -12,12
								l -88,0
								l 12,-12
						"
						fill="white"
						stroke="hsla(0,0%,0%,0.5)"
						stroke-width="1"
					/><text
						x="0"
						y="4"
						text-anchor="middle"
					>send()</text
				></svg
			></svg
		></figure
		><p>As you see, all miraculous magics are in the box of <code>$kernel->handle()</code>. But what is the <code>$kernel</code>? We can find where it from. </p
		><code-mirror
			language="PHP"
			readonly="readonly"
			filename="public/index.php"
		>@subindent
			$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
		@endsubindent</code-mirror
		><code-mirror
			language="PHP"
			readonly="readonly"
			filename="bootstrap/app.php"
		>@subindent
			$app->singleton(
			    Illuminate\Contracts\Http\Kernel::class,
			    App\Http\Kernel::class
			);
		@endsubindent</code-mirror
		><p>So, the <code>$kernel</code> is the singleton of class <code>App\Http\Kernel</code>. 
		Let's check the class. Aha, you will find that here is where we manage our middlewares. 
		But there is no <code>handle()</code>, we need search at the parent class <code>Illuminate\Foundation\Http\Kernel</code>. 
		Here we are:</p
		><code-mirror
			language="PHP"
			readonly="readonly"
			filename="vendor/laravel/framework/src/Illuminate/Foundation/Http/Kernel.php"
		>@subindent
			/**
			 * Handle an incoming HTTP request.
			 * 
			 * @param  \Illuminate\Http\Request  $request
			 * @return \Illuminate\Http\Response
			 */
			public function handle($request)
		@endsubindent</code-mirror
		><p>Let's just skip noises and focus on the main line:</p
		><code-mirror
			language="PHP"
			readonly="readonly"
			filename="vendor/laravel/framework/src/Illuminate/Foundation/Http/Kernel.php"
		>@subindent
			$response = $this->sendRequestThroughRouter($request);
		@endsubindent</code-mirror
		><p>Let's jump to <code>sendRequestThroughRouter()</code> and focus on:</p
		><code-mirror
			language="PHP"
			readonly="readonly"
			filename="vendor/laravel/framework/src/Illuminate/Foundation/Http/Kernel.php"
		>@subindent
			// the copy of code is modified to focus on the topical
			return (new \Illuminate\Routing\Pipeline())
			            ->send($request)
			            ->through($this->middleware)
			            ->then($this->dispatchToRouter());
		@endsubindent</code-mirror
		><p>There is a magician named <code>Pipeline</code>. 
		Do not get acquaintance with her too deep at this time, just look on what she does: 
		sending the <code>$request</code> through the global middlewares then dispatching with router. </p
		><p>In router, we expectedly find that:</p
		><code-mirror
			language="PHP"
			readonly="readonly"
			filename="vendor/laravel/framework/src/Illuminate/Routing/Router.php"
		>@subindent
			// the copy of code is modified to focus on the topical
			$route = $this->findRoute($request);
		@endsubindent</code-mirror
		><p>With the route we find, another pipeline is here.</p
		><code-mirror
			language="PHP"
			readonly="readonly"
			filename="vendor/laravel/framework/src/Illuminate/Routing/Router.php"
		>@subindent
			// the copy of code is modified to focus on the topical
			return (new \Illuminate\Routing\Pipeline())
			            ->send($request)
			            ->through($middleware)
			            ->then(function ($request) use ($route) {
			                return $this->prepareResponse(
			                    $request, $route->runController()
			                );
			            });
		@endsubindent</code-mirror
		><p>In this pipeline, the request is sent through middlewares with the route to the controller. A response will be created with the result of controller.</p
		><figure
			><svg
				viewBox="0 0 768 256"
				style="max-width:768px; background-color:hsla(0,0%,0%,0.015625)"
				><svg
					x="64"
					y="192"
					><path
						d="
							M 64,0
								c -24,0 -24,-24 -64,-24
								c -40,0 -48,16 -48,24
								c 0,8 8,24 48,24
								c 40,0 40,-24 64,-24
							z
						"
						fill="white"
						stroke="hsla(0,0%,0%,0.5)"
						stroke-width="1"
					/><text
						x="0"
						y="4"
						text-anchor="middle"
					>Request</text
				></svg
				><svg
					x="192"
					y="64"
					><path
						d="
							M -64,-16
								l 192,0
								l 0,168
								l -192,0
							z
						"
						fill="transparent"
						stroke="hsla(0,0%,0%,0.5)"
						stroke-width="1"
						stroke-dasharray="2 4"
					/><text
						x="0"
						y="4"
						text-anchor="middle"
					>Kernel</text
				></svg
				><svg
					x="224"
					y="192"
					><path
						d="
							M -96,0
								l 24,0
								l -12,-12
								l 44,0
								l 4,4
								l 0,-4
								l 20,0
								l 4,4
								l 0,-4
								l 20,0
								l 4,4
								l 0,-4
								l 20,0
								l 4,4
								l 0,-4
								l 32,0
								l 12,12
								l 16,0
							M -72,0
								l -12,12
								l 44,0
								l 4,-4
								l 0,4
								l 20,0
								l 4,-4
								l 0,4
								l 20,0
								l 4,-4
								l 0,4
								l 20,0
								l 4,-4
								l 0,4
								l 32,0
								l 12,-12
						"
						fill="white"
						stroke="hsla(0,0%,0%,0.5)"
						stroke-width="1"
					/><text
						x="0"
						y="4"
						text-anchor="middle"
					>global middlewares</text
				></svg
				><svg
					x="384"
					y="64"
					><path
						d="
							M -64,-16
								l 320,0
								l 0,168
								l -320,0
						"
						fill="transparent"
						stroke="hsla(0,0%,0%,0.5)"
						stroke-width="1"
						stroke-dasharray="2 4"
					/><text
						x="0"
						y="4"
						text-anchor="middle"
					>Router</text
				></svg
				><svg
					x="384"
					y="96"
					><path
						d="
							M -56,96
								l 0,-96
							M -56,0
								l 16,0
								l -12,-12
								l 88,0
								l 12,12
								l 16,0
								m -16,0
								l -12,12
								l -88,0
								l 12,-12
						"
						fill="white"
						stroke="hsla(0,0%,0%,0.5)"
						stroke-width="1"
					/><text
						x="0"
						y="4"
						text-anchor="middle"
					>findRoute()</text
				></svg
				><svg
					x="512"
					y="96"
					><path
						d="
							M 0,24
								c 0,32 -96,28 -96,60
							M 0,24
								c 0,32 64,28 64,60
						"
						fill="transparent"
						stroke="hsla(0,0%,0%,0.5)"
						stroke-width="1"
					/><path
						d="
							M -64,0
								c 24,0 24,-24 64,-24
								c 40,0 48,16 48,24
								c -0,8 -8,24 -48,24
								c -40,0 -40,-24 -64,-24
							z
						"
						fill="white"
						stroke="hsla(0,0%,0%,0.5)"
						stroke-width="1"
					/><text
						x="0"
						y="4"
						text-anchor="middle"
					>Route</text
				></svg
				><svg
					x="416"
					y="192"
					><path
						d="
							M -96,0
								l 24,0
								l -12,-12
								l 44,0
								l 4,4
								l 0,-4
								l 20,0
								l 4,4
								l 0,-4
								l 20,0
								l 4,4
								l 0,-4
								l 20,0
								l 4,4
								l 0,-4
								l 32,0
								l 12,12
								l 16,0
							M -72,0
								l -12,12
								l 44,0
								l 4,-4
								l 0,4
								l 20,0
								l 4,-4
								l 0,4
								l 20,0
								l 4,-4
								l 0,4
								l 20,0
								l 4,-4
								l 0,4
								l 32,0
								l 12,-12
						"
						fill="white"
						stroke="hsla(0,0%,0%,0.5)"
						stroke-width="1"
					/><text
						x="0"
						y="4"
						text-anchor="middle"
					>route middlewares</text
				></svg
				><svg
					x="576"
					y="192"
					><path
						d="
							M -64,0
								l 24,0
								l -12,-12
								l 88,0
								l 12,12
								l 16,0
								m -16,0
								l -12,12
								l -88,0
								l 12,-12
						"
						fill="white"
						stroke="hsla(0,0%,0%,0.5)"
						stroke-width="1"
					/><text
						x="0"
						y="4"
						text-anchor="middle"
					>controller</text
				></svg
				><svg
					x="704"
					y="192"
					><path
						d="
							M -64,0
								c 24,0 24,-24 64,-24
								c 40,0 48,16 48,24
								c -0,8 -8,24 -48,24
								c -40,0 -40,-24 -64,-24
							z
						"
						fill="white"
						stroke="hsla(0,0%,0%,0.5)"
						stroke-width="1"
					/><text
						x="0"
						y="4"
						text-anchor="middle"
					>Response</text
				></svg
				><svg
					x="384"
					y="240"
					><path
						d="
							M -64,0
								l 32,0
							M 64,0
								l -32,0
						"
						fill="transparent"
						stroke="hsla(0,0%,0%,0.5)"
						stroke-width="1"
						stroke-dasharray="3 4 5 4 16"
					/><text
						x="0"
						y="4"
						text-anchor="middle"
					>Pipelines</text
				></svg
			></svg
		></figure
		><p>But this is only half of effects of middlewares. A typical middleware is like this:</p
		><code-mirror
			language="PHP"
			readonly="readonly"
			filename="app/Http/Middleware/*.php"
		>@subindent
			public function handle($request, Closure $next)
			{
			    // do something with $request
			    
			    $response = $next($request);
			    
			    // do something with $response
			    
			    return $response;
			}
		@endsubindent</code-mirror
		><p>In a middleware, we can process not only request, but also response. The full process is like this:</p
		><figure
			><svg
				viewBox="0 0 512 576"
				style="max-width:512px; background-color:hsla(0,0%,0%,0.015625)"
				><svg
					x="128"
					y="448"
					><path
						d="
							M -64,-416
								c 0,0  128,0 128,192
								c 0,192 -64,168 -64,224
								c 0,48 32,64 64,64
							M -64,-416
								l -8,-8
							M -64,-416
								l -8,8
						"
						fill="transparent"
						stroke="hsla(0,0%,0%,0.5)"
						stroke-width="1"
					/><circle
						cx="0"
						cy="0"
						r="48"
						fill="white"
						stroke="hsla(0,0%,0%,0.5)"
						stroke-width="1"
					/><text
						x="0"
						y="4"
						text-anchor="middle"
					>Request</text
				></svg
				><svg
					x="384"
					y="448"
					><path
						d="
							M 64,-416
								c 0,0  -128,0 -128,192
								c 0,192 64,168 64,224
								c 0,48 -32,64 -64,64
							M 64,-416
								l -8,-8
							M 64,-416
								l -8,8
						"
						fill="transparent"
						stroke="hsla(0,0%,0%,0.5)"
						stroke-width="1"
					/><circle
						cx="0"
						cy="0"
						r="48"
						fill="white"
						stroke="hsla(0,0%,0%,0.5)"
						stroke-width="1"
					/><text
						x="0"
						y="4"
						text-anchor="middle"
					>Response</text
				></svg
				><svg
					x="256"
					y="512"
					><path
						d="
							M -64,0
								l 24,0
								l -12,-12
								l 88,0
								l 12,12
								l 16,0
								m -16,0
								l -12,12
								l -88,0
								l 12,-12
						"
						fill="white"
						stroke="hsla(0,0%,0%,0.5)"
						stroke-width="1"
					/><text
						x="0"
						y="4"
						text-anchor="middle"
					>Controller</text
				></svg
				><svg
					x="256"
					y="128"
					><path
						d="
							M 0,-16
								c 96,0 128,-32 128,16
								c 0,48 -32,16 -128,16
								c -96,-0 -128,32 -128,-16
								c -0,-48 32,-16 128,-16
						"
						fill="white"
						stroke="hsla(0,0%,0%,0.5)"
						stroke-width="1"
					/><text
						x="0"
						y="4"
						text-anchor="middle"
					>middleware</text
				></svg
				><svg
					x="256"
					y="192"
					><path
						d="
							M 0,-16
								c 96,0 128,-32 128,16
								c 0,48 -32,16 -128,16
								c -96,-0 -128,32 -128,-16
								c -0,-48 32,-16 128,-16
						"
						fill="white"
						stroke="hsla(0,0%,0%,0.5)"
						stroke-width="1"
					/><text
						x="0"
						y="4"
						text-anchor="middle"
					>middleware</text
				></svg
				><svg
					x="256"
					y="256"
					><circle
						cx="0"
						cy="-16"
						r="4"
						fill="hsla(0,0%,0%,0.5)"
					/><circle
						cx="0"
						cy="0"
						r="4"
						fill="hsla(0,0%,0%,0.5)"
					/><circle
						cx="0"
						cy="16"
						r="4"
						fill="hsla(0,0%,0%,0.5)"
				/></svg
				><svg
					x="256"
					y="320"
					><path
						d="
							M 0,-16
								c 96,0 128,-32 128,16
								c 0,48 -32,16 -128,16
								c -96,-0 -128,32 -128,-16
								c -0,-48 32,-16 128,-16
						"
						fill="white"
						stroke="hsla(0,0%,0%,0.5)"
						stroke-width="1"
					/><text
						x="0"
						y="4"
						text-anchor="middle"
					>middleware</text
				></svg
			></svg
		></figure
		><p>This structure gives middleware ability to block the request into next level and respond directly. 
		And the middlewares of upper levels will still work well.</p
		><h3>Exception Handling</h3
		><p>When a exception or error is thrown, the response cannot create successfully. 
		Let's go back where we've caught a sight of try-catch: <code>Illuminate\Foundation\Http\Kernel::handle()</code>.</p
		><code-mirror
			language="PHP"
			readonly="readonly"
			filename="vendor/laravel/framework/src/Illuminate/Foundation/Http/Kernel.php"
		>@subindent
			// the copy of code is modified to focus on the topical
			try {
			    $response = $this->sendRequestThroughRouter($request);
			} catch (Exception $e) {
			    $response = $this->app[Illuminate\Contracts\Debug\ExceptionHandler::class]->render($request, $e);
			} catch (Throwable $e) {
			    $response = $this->app[Illuminate\Contracts\Debug\ExceptionHandler::class]->render($request, $e);
			}
		@endsubindent</code-mirror
		><p>As we see, the exception or error will be caught at the top level, out of all middlewares. 
		And an <a>Exception Handler</a> will render it into a response. 
		That means, on the positive hand, exception or error from middlewares can be caught, 
		but on the negative hand, when an exception or error thrown, what we act on response in middlewares will not work anymore. 
		For example, your middleware cannot add <code>Access-Control-Allow-Origin</code> to your exception responses.</p
		><p>A good practice is add a global handler middleware between common middlewares and business middlewares to handle exceptions or errors.</p
		><figure
			><svg
				viewBox="0 0 512 512"
				style="max-width:512px; background-color:hsla(0,0%,0%,0.015625)"
				><svg
					x="128"
					y="448"
					><path
						d="
							M -64,-416
								c 0,0  128,0 128,192
								c 0,192 -64,224 -64,224
							M -64,-416
								l -8,-8
							M -64,-416
								l -8,8
						"
						fill="transparent"
						stroke="hsla(0,0%,0%,0.5)"
						stroke-width="1"
				/></svg
				><svg
					x="384"
					y="448"
					><path
						d="
							M 64,-416
								c 0,0  -128,0 -128,192
								c 0,192 64,224 64,224
							M 64,-416
								l -8,-8
							M 64,-416
								l -8,8
						"
						fill="transparent"
						stroke="hsla(0,0%,0%,0.5)"
						stroke-width="1"
				/></svg
				><svg
					x="384"
					y="64"
					><path
						d="
							M 0,-16
								c 48,0 64,8 64,16
								c 0,8 -24,16 -64,16
								c -48,-0 -64,-8 -64,-16
								c -0,-8 24,-16 64,-16
						"
						fill="hsla(3,100%,81.25%,1)"
						stroke="hsla(0,0%,0%,0.5)"
						stroke-width="1"
					/><text
						x="0"
						y="4"
						text-anchor="middle"
					>global handler</text
				></svg
				><svg
					x="256"
					y="128"
					><path
						d="
							M 0,-16
								c 96,0 128,-32 128,16
								c 0,48 -32,16 -128,16
								c -96,-0 -128,32 -128,-16
								c -0,-48 32,-16 128,-16
						"
						fill="white"
						stroke="hsla(0,0%,0%,0.5)"
						stroke-width="1"
					/><text
						x="0"
						y="4"
						text-anchor="middle"
					>common middleware</text
				></svg
				><svg
					x="256"
					y="192"
					><circle
						cx="0"
						cy="-16"
						r="4"
						fill="hsla(0,0%,0%,0.5)"
					/><circle
						cx="0"
						cy="0"
						r="4"
						fill="hsla(0,0%,0%,0.5)"
					/><circle
						cx="0"
						cy="16"
						r="4"
						fill="hsla(0,0%,0%,0.5)"
				/></svg
				><svg
					x="256"
					y="256"
					><path
						d="
							M 0,-16
								c 96,0 128,-32 128,16
								c 0,48 -32,16 -128,16
								c -96,-0 -128,32 -128,-16
								c -0,-48 32,-16 128,-16
						"
						fill="white"
						stroke="hsla(0,0%,0%,0.5)"
						stroke-width="1"
					/><text
						x="0"
						y="4"
						text-anchor="middle"
					>common middleware</text
				></svg
				><svg
					x="256"
					y="320"
					><path
						d="
							M 0,-16
								c 96,0 128,-32 128,16
								c 0,48 -32,16 -128,16
								c -96,-0 -128,32 -128,-16
								c -0,-48 32,-16 128,-16
						"
						fill="hsla(3,100%,81.25%,1)"
						stroke="hsla(0,0%,0%,0.5)"
						stroke-width="1"
					/><text
						x="0"
						y="4"
						text-anchor="middle"
					>handler middleware</text
				></svg
				><svg
					x="256"
					y="384"
					><path
						d="
							M 0,-16
								c 96,0 128,-32 128,16
								c 0,48 -32,16 -128,16
								c -96,-0 -128,32 -128,-16
								c -0,-48 32,-16 128,-16
						"
						fill="white"
						stroke="hsla(0,0%,0%,0.5)"
						stroke-width="1"
					/><text
						x="0"
						y="4"
						text-anchor="middle"
					>business middleware</text
				></svg
				><svg
					x="256"
					y="448"
					><circle
						cx="0"
						cy="-16"
						r="4"
						fill="hsla(0,0%,0%,0.5)"
					/><circle
						cx="0"
						cy="0"
						r="4"
						fill="hsla(0,0%,0%,0.5)"
					/><circle
						cx="0"
						cy="16"
						r="4"
						fill="hsla(0,0%,0%,0.5)"
				/></svg
			></svg
		></figure
		><p>So your exception response will not miss your global middlewares. 
		As the example, you can find file <code>app/Http/Middleware/Handler.php</code> in this repository. The main code is just like what in the kernel: </p
		><code-mirror
			language="PHP"
			readonly="readonly"
			filename="app/Http/Middleware/Handler.php"
		>@subindent
			// the copy of code is modified to focus on the topical
			try {
			    $response = $next($request);
			} catch (Exception $e) {
			    $response = $this->handler->render($request, $e);
			} catch (Throwable $e) {
			    $response = $this->handler->render($request, $e);
			}
		@endsubindent</code-mirror
		><p>Don't forget to register it in the <code>App\Http\Kernel</code>:</p
		><code-mirror
			language="PHP"
			readonly="readonly"
			filename="app/Http/Kernel.php"
		>@subindent
			protected $middleware = [
			    // ...
			    \App\Http\Middleware\Handler::class,
			];
			
			protected $middlewarePriority = [
			    // common middlewares
			    \App\Http\Middleware\Handler::class,
			    // business middlewares
			];
		@endsubindent</code-mirror
		><h3>Summing up</h3
		><p>So far, we've learned how Laravel handle a HTTP request and respond a response. 
		We've met several friends: HTTP Kernel, Pipeline, Router and Middleware. 
		What I can do is just introducing.For digging deeper, you can read the source codes of Laravel. 
		Good Luck!</p
	></article
@endsection
