@extends('layout')

@section('title', 'IoC Container')

@section('main')
	><article
		><p>In previous article, we've see that the magician <code>$app</code> produced the <code>$kernel</code> out of gloved hands 
		with a spell <code>Illuminate\Contracts\Http\Kernel::class</code>. Let's reveal the secrets of the magic.</p
		><p>The <code>$app</code> is a instance of <code>Illuminate\Foundation\Application</code>, which extends <code>Illuminate\Container\Container</code>. 
		The magic is inherited from here, the container, or recognized as IoC container.</p
		><p>How it works? It's absolutely not to simply call <code>new</code> on the given class. In fact, you'll find it's a interface rather then a class: </p
		><code-mirror
			language="PHP"
			readonly="readonly"
			filename="vendor/laravel/framework/src/Illuminate/Contracts/Http/Kernel.php"
		>
			namespace Illuminate\Contracts\Http;
			
			interface Kernel
		</code-mirror
		><p>We've also find some code seems like binding a instantiable class with the interface: </p
		><code-mirror
			language="PHP"
			readonly="readonly"
			filename="bootstrap/app.php"
		>
			$app->singleton(
			    Illuminate\Contracts\Http\Kernel::class,
			    App\Http\Kernel::class
			);
		</code-mirror
		><p>So is the <code>$app</code> just call <code>new</code> on this class? You can try it, an <code>ArgumentCountError</code> will be thrown. 
		The constructor of class <code>App\Http\Kernel</code> requires 2 parameters.</p
		><code-mirror
			language="PHP"
			readonly="readonly"
			filename="vendor/laravel/framework/src/Illuminate/Foundation/Http/Kernel.php"
		>
			/**
			 * Create a new HTTP kernel instance.
			 *
			 * @param  \Illuminate\Contracts\Foundation\Application  $app
			 * @param  \Illuminate\Routing\Router  $router
			 * @return void
			 */
			public function __construct(Application $app, Router $router)
		</code-mirror
		><p>For instantiating, the container need to know this. Therefore, there must be metaprogramming inside, must be reflecting inside. 
		Let's trace the code: </p
		><code-mirror
			language="PHP"
			readonly="readonly"
			filename="vendor/laravel/framework/src/Illuminate/Container/Container.php"
		>
			// the copy of code is modified to focus on the topical
			public function singleton($abstract, $concrete)
			{
			    $this->bind($abstract, $concrete);
			}
			
			public function bind($abstract, $concrete)
			{
			    if (! $concrete instanceof Closure) {
			        $concrete = $this->getClosure($abstract, $concrete);
			    }
			
			    $this->bindings[$abstract] = ['concrete' => $concrete];
			}
			
			protected function getClosure($abstract, $concrete)
			{
			    return function ($container) use ($abstract, $concrete) {
			        if ($abstract == $concrete) {
			            return $container->build($concrete);
			        }
			
			        return $container->resolve($concrete);
			    };
			}
		</code-mirror
		><p>We call <code>singleton()</code> on <code>bind()</code> with an <code>$abstract</code> and a concrete class. 
		The <code>$abstract</code> is just act as a string, you can replace <code>Illuminate\Contracts\Http\Kernel::class</code> 
		both in <code>public/index.php</code> and <code>bootstrap/app.php</code> to <code>'This is the HTTP Kernel!'</code>. 
		It'll work fine as before. For magical, let's just call it the <i>'Spell'</i>. 
		There is benefit to use the class or interface name as the <i>Spell</i>, We'll see later.</p
		><p>The container keeps a map from each <i>Spell</i> to a anonymous function for instantiation: <code>$this->bindings</code>. 
		When you call <code>make()</code> or <code>get()</code> on the container, these instantiation functions will be call. </p
		><code-mirror
			language="PHP"
			readonly="readonly"
			filename="vendor/laravel/framework/src/Illuminate/Container/Container.php"
		>
			// the copy of code is modified to focus on the topical
			public function make($abstract, array $parameters = [])
			{
			    return $this->resolve($abstract, $parameters);
			}
			
			protected function resolve($abstract)
			{
			    $concrete = $this->getConcrete($abstract);
			
			    $object = $this->build($concrete);
			
			    return $object;
			}
			
			public function build($concrete)
			{
			    if ($concrete instanceof Closure) {
			        return $concrete($this);
			    }
		</code-mirror
		><p>There are some recurrent calls, but in the very end, it will call the <code>build()</code> with the <code>$concrete</code> you first given. 
		It end up as the code like this:</p
		><code-mirror
			language="PHP"
			readonly="readonly"
		>
			$app->build(App\Http\Kernel::class)
		</code-mirror
		><p>You can modify the <code>public/index.php</code> in this way. It'll work well as well. 
		Why don't Laravel just take this simple way? Because we may access the kernel in other places, a singleton is necessary. 
		What's more, functions added by <code>extend()</code> will not work if we use <code>build()</code> directly instead of <code>make()</code>. 
		We will talk about <code>extend()</code> later.</p
		><p>Let's back to the <code>build()</code> called with a class name.</p
		><code-mirror
			language="PHP"
			readonly="readonly"
			filename="vendor/laravel/framework/src/Illuminate/Container/Container.php"
		>
			// the copy of code is modified to focus on the topical
			public function build($concrete)
			{
			    $reflector = new ReflectionClass($concrete);
			
			    $constructor = $reflector->getConstructor();
			
			    $dependencies = $constructor->getParameters();
			
			    $instances = $this->resolveDependencies($dependencies);
			
			    return $reflector->newInstanceArgs($instances);
			}
			
			protected function resolveDependency(ReflectionParameter $dependency)
			{
			    if (is_null($dependency->getClass())) {
			        $concrete = $this->getContextualConcrete('$'.$parameter->name);
			        
			        return $concrete instanceof Closure ? $concrete($this) : $concrete;
			    }
			    
			    $this->make($dependency->getClass()->name);
			}
		</code-mirror
		><p>The metaprogramming magic is here. They reflect the class and get the dependencies from constructor. 
		Then make the dependencies, and call the constructor with these dependencies to instantiate.  
		If we add a class or interface as type hint of a parameter. The container will use it as the <i>Spell</i>. 
		Now you see, that's the benefit of using class or interface name as <i>Spell</i> we've talk about.
		We only need to write the <i>Spell</i> as type hint in <code>__construct()</code>, and the container will call the <i>Spell</i> automatically. 
		That's why Laravel using <code>Illuminate\Contracts\Http\Kernel::class</code> rather then something like <code>'This is the HTTP Kernel!'</code>. 
		If the type hint is not suitable as a <i>Spell</i>, such as the dependency is a string or a resource, or the same <i>Spell</i> for different concretes in different place. 
		We can use a feature named contextual concrete: </p
		><code-mirror
			language="PHP"
			readonly="readonly"
		>
			class Foo
			{
			    public function __construct($phpInput) {}
			}
			
			$container->addContextualBinding(Foo::class, '$phpInput', fopen( 'php://input', 'r' ));
			
			$container->make(Foo::class);
		</code-mirror
		><p>Or you can use more readable syntax like the case in documentation:</p
		><code-mirror
			language="PHP"
			readonly="readonly"
		>
			$container->when(Foo::class)->need('$phpInput')->give(fopen( 'php://input', 'r' ));
		</code-mirror
		><p>For readable and efficiency, we can set aliases to <i>Spells</i>. 
		If you can use 'Ex' instead of 'Expelliarmus', Noseless Voldemort will not have enough time to call the 'Avada Kedavra'. 
		It's a joke, but <code>app</code> for <code>Illuminate\Contracts\Foundation\Application</code>, 
		<code>http-kernel</code> for <code>Illuminate\Contracts\Http\Kernel</code> etc. will be helpful.</p
		><code-mirror
			language="PHP"
			readonly="readonly"
		>
			$app->alias(Illuminate\Contracts\Http\Kernel::class, 'http-kernel');
			
			$kernel = $app->make('http-kernel');
		</code-mirror
		><p>We also need take care of these code:</p
		><code-mirror
			language="PHP"
			readonly="readonly"
			filename="vendor/laravel/framework/src/Illuminate/Container/Container.php"
		>
			// the copy of code is modified to focus on the topical
			protected function resolve($abstract)
			{
			    $object = $this->build($concrete);
			
			    foreach ($this->getExtenders($abstract) as $extender) {
			        $object = $extender($object, $this);
			    }
			
			    return $object;
			}
		</code-mirror
		><p>The object returned by <code>resolve()</code> may not be the one from <code>build()</code>. 
		Follow the <code>getExtenders()</code>, we find: </p
		><code-mirror
			language="PHP"
			readonly="readonly"
			filename="vendor/laravel/framework/src/Illuminate/Container/Container.php"
		>
			// the copy of code is modified to focus on the topical
			protected function getExtenders($abstract)
			{
			    return $this->extenders[$abstract] ?? [];
			}
			
			public function extend($abstract, Closure $closure)
			{
			    $this->extenders[$abstract][] = $closure;
			}
		</code-mirror
		><p>That's means we can use <code>extend()</code> to add modifiers to end up modify or change the object. 
		If you bind with abstract and concrete class, like our kernel example, 
		the resolve will recurrently called with both the abstract and concrete. 
		Therefore, you can both call extend with abstract and concrete.</p
		><code-mirror
			language="PHP"
			readonly="readonly"
		>
			$app->singleton(
			    Illuminate\Contracts\Http\Kernel::class,
			    App\Http\Kernel::class
			);
			
			$app->extend(App\Http\Kernel::class, function ($kernel) {
			    $kernel->foo = 'foo';
			
			    return $kernel;
			});
			
			$app->extend(Illuminate\Contracts\Http\Kernel::class, function ($kernel) {
			    $kernel->bar = 'bar';
			
			    return $kernel;
			});
			
			$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
			
			$kernel->foo; // 'foo'
			$kernel->bar; // 'bar'
		</code-mirror
		><p>For extendable, understandable and maintainable reason, 
		it's better only to modify the object but not to change it in the extender.</p
		><code-mirror
			language="PHP"
			readonly="readonly"
		>
			// Good
			$app->extend(Foo::class, function ($foo) {
			    $foo->doSomeExtends();
			
			    return $foo;
			});
			
			// Bad
			$app->extend(Foo::class, function ($foo) {
			    $bar = new SomethingElse();
			
			    return $bar;
			});
		</code-mirror
		><p>Now, let's talk about the IoC. IoC is short for <i>Inversion of Control</i>. 
		It's made up with Dependence Inversion Principle (DIP) and Dependency Injection (DI). 
		The DIP says: a module should not depend on another module, but both them should depend on abstraction. 
		For example:</p
		><code-mirror
			language="PHP"
			readonly="readonly"
		>
			interface Connection
			{}
			
			class FooConnection implement Connection
			{
			    public function __construct()
			    {}
			}
			
			class App
			{
			    public function __construct(Connection $connection)
			    {}
			}
			
			$connection = new FooConnection();
			$app = new App($connection);
		</code-mirror
		><p>As we see, class <code>App</code> is not depend on class <code>FooConnection</code>, 
		but both two classes depend on the interface <code>Connection</code>. 
		So when we change <code>FooConnection</code> to <code>BarConnection</code>, class <code>App</code> need not be modified. 
		And when we test the class <code>App</code>, we can pass a <code>MockConnection</code> to prevent a real connection.</p
		><p>So far, we also need face to a problem: every time we instantiate <code>App</code>, we need instantiate a implement of <code>Connection</code> first. 
		Different implement of <code>Connection</code> may requires different dependencies, dependency may has it's own dependencies... 
		Now, DI is what we need, it is a feature that detecting the dependencies then instantiating them and injecting into the constructor. 
		This just happen to be what container acts. </p
		><p>So we have a overview of IoC, decoupling dependent between modules, and instantiating automatically with container. 
		Your module code and module using code become more declarative, and standalone, not caring about details about other modules but only interfaces.</p
		><p>I just have a question about the naming 'Inversion'. That's not invert anything, that's just the original way to design things. Do you agree?</p
		><h3></h3
		><p>After touching the textbook knowledge, let's back to see our example:</p
		><code-mirror
			language="PHP"
			readonly="readonly"
			filename="vendor/laravel/framework/src/Illuminate/Foundation/Http/Kernel.php"
		>
			// the copy of code is modified to focus on the topical
			namespace Illuminate\Foundation\Http;
			
			class Kernel implements KernelContract
			{
			    public function __construct(\Illuminate\Contracts\Foundation\Application $app, \Illuminate\Routing\Router $router)
		</code-mirror
		><p>The <code>\Illuminate\Contracts\Foundation\Application</code> is a interface but <code>\Illuminate\Routing\Router</code> is not.
		So this is not fully follow the DIP. So the defect is we cannot change the router freely. But we still can extends it. 
		We can write a <code>MyRouter extends \Illuminate\Routing\Router</code>, then register it in the <code>App\Providers\RouteServiceProvider</code>:</p
		><code-mirror
			language="PHP"
			readonly="readonly"
			filename="app/Providers/RouteServiceProvider.php"
		>
			public function boot()
			{
			    $this->app->singleton(\Illuminate\Routing\Router::class, MyRouter::class);
		</code-mirror
		><p>Now, you can make creative works in <code>MyRouter</code>. If you register <code>MyRouter</code> in <code>bootstrap/app.php</code>, 
		you will find your routes will not work, and all request will responded with 404. That's some reason about the service providers. 
		We will talk about this on next article. </p
		><p>DI is so powerful, we can expect it's not only for constructing. By searching <code>public function</code> in the container class, 
		we can find to methods that official documentation not talk about: <code>call()</code> and <code>wrap()</code>. </p
		><code-mirror
			language="PHP"
			readonly="readonly"
			filename="vendor/laravel/framework/src/Illuminate/Container/Container.php"
		>
			/**
			 * Call the given Closure / class@method and inject its dependencies.
			 *
			 * @param  callable|string  $callback
			 * @param  array  $parameters
			 * @return mixed
			 */
			public function call($callback, array $parameters = [] )
			
			/**
			 * Wrap the given closure such that its dependencies will be injected when executed.
			 *
			 * @param  \Closure  $callback
			 * @param  array  $parameters
			 * @return \Closure
			 */
			public function wrap(Closure $callback, array $parameters = [])
			{
			    return function () use ($callback, $parameters) {
			        return $this->call($callback, $parameters);
			    };
			}
		</code-mirror
		><p>Here is some examples for <code>call()</code>: </p
		><code-mirror
			language="PHP"
			readonly="readonly"
		>
			$container->call(function (Illuminate\Contracts\Http\Kernel $kernel) {
				dump( $kernel );
			});
		</code-mirror
		><code-mirror
			language="PHP"
			readonly="readonly"
		>
			function function_name(Illuminate\Contracts\Http\Kernel $kernel)
			{
				dump( $kernel );
			}
			
			$app->call( 'function_name' );
		</code-mirror
		><code-mirror
			language="PHP"
			readonly="readonly"
		>
			class ClassName
			{
				public static function methodName(Illuminate\Contracts\Http\Kernel $kernel)
				{
					dump( $kernel );
				}
			}
			
			$app->call( [ClassName::class, 'methodName'] );
			$app->call( 'ClassName::methodName' );
			$app->call( 'ClassName@methodName' ); // not standard, not recommended
		</code-mirror
		><code-mirror
			language="PHP"
			readonly="readonly"
		>
			class ClassName
			{
				public static function methodName(Illuminate\Contracts\Http\Kernel $kernel)
				{
					dump( $kernel );
				}
			}
			
			$app->call( [ClassName::class, 'methodName'] );
			$app->call( 'ClassName::methodName' );
		</code-mirror
		><code-mirror
			language="PHP"
			readonly="readonly"
		>
			class ClassName
			{
				public function methodName(Illuminate\Contracts\Http\Kernel $kernel)
				{
					dump( $kernel );
				}
			}
			
			$app->call( [new ClassName(), 'methodName'] );
			$app->call( 'ClassName@methodName' ); // for non-static method, container will instantiate the class automatically.
		</code-mirror
		><p>The method <code>wrap()</code> however, returns a anonymous function for calling later or higher order functions. 
		unfortunately, the <code>wrap()</code> is not as powerful as <code>call()</code> because of a improper type hint. </p
		><h3>Summing up</h3
		><p>In this article, we dig into Container, find out how it works. Then learn what is IoC, DIP and DI, know why the container designed like this. 
		And show a example about how to extend a framework module with the container. </p
	></article
@endsection
