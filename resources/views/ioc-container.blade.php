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
		There is benefit to use the class or interface name as the <i>Spell</i>, I'll show you later.</p
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
		Why don't Laravel just take this simple way? Because we may access the kernel in other places, a singleton is necessary.</p
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
		If the type hint is not suitable as a <i>Spell</i>, such as the dependency is a string or a resource. 
		We can use a feature named contextual concrete: </p
		><code-mirror
			language="PHP"
			readonly="readonly"
		>
			class Foo
			{
			    public function __construct($phpInput) {}
			}
			
			$container->addContextualBinding(Foo::class, '$phpInput', fopen( 'php://input', 'r' ) );
			
			$container->make(Foo::class);
		</code-mirror
	></article
@endsection
