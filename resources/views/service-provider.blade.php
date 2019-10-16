@extends('layout')

@section('title', 'Service Provider')

@section('main')
	><article
		><p>During reading documentations of Laravel, you will find <code>AppServiceProvider</code> everywhere. 
		Once you need set something globally, they teach you to add code into this class:</p
		><code-mirror
			language="PHP"
			readonly="readonly"
			filename="app/Providers/AppServiceProvider.php"
			height="16em"
		>
			public function boot()
			{
			    View::share('key', 'value');
			    
			    Route::resourceVerbs([
			        'create' => 'crear',
			        'edit' => 'editar',
			    ]);
			    
			    Validator::extend('foo', function ($attribute, $value, $parameters, $validator) {
			        return $value == 'foo';
			    });
			    
			    Blade::component('components.alert', 'alert');
			    Blade::withoutDoubleEncoding();
			    Blade::directive('datetime', function ($expression) {
			        return "&lt;?php echo ($expression)->format('m/d/Y H:i'); ?&gt;";
			    });
			    Blade::if('env', function ($environment) {
			        return app()->environment($environment);
			    });
			    
			    Queue::failing(function (JobFailed $event) {
			        // $event->connectionName
			        // $event->job
			        // $event->exception
			    });
			    
			    DB::listen(function ($query) {
			        // $query->sql
			        // $query->bindings
			        // $query->time
			    });
			    
			    Paginator::defaultView('view-name');
			    
			    Paginator::defaultSimpleView('view-name');
			    
			    Schema::defaultStringLength(191);
			    
			    User::observe(UserObserver::class);
			    
			    Relation::morphMap([
			        'posts' => 'App\Post',
			        'videos' => 'App\Video',
			    ]);
			    
			    Resource::withoutWrapping();
			    
			    Carbon::serializeUsing(function ($carbon) {
			        return $carbon->format('U');
			    });
			    
			    // ...
			}
		</code-mirror
		><p>And if you use a package for Laravel, you may need to register some service provider class into file <code>config/app.php</code>. 
		And over there, we can find over two dozens of service providers, include <code>App\Providers\AppServiceProvider::class</code> too. 
		So we see, here is where we configure service providers. If we remove one from here, it will go out of business. 
		Now, let's check where the configuration works, by searching <code>app.providers</code> globally, we find it in <code>Illuminate\Foundation\Application</code>.</p
		><code-mirror
			language="PHP"
			readonly="readonly"
			filename="vendor/laravel/framework/src/Illuminate/Foundation/Application.php"
		>
			public function registerConfiguredProviders()
			{
			    $providers = Collection::make($this->config['app.providers'])
			                    ->partition(function ($provider) {
			                        return Str::startsWith($provider, 'Illuminate\\');
			                    });
			
			    $providers->splice(1, 0, [$this->make(PackageManifest::class)->providers()]);
			
			    (new ProviderRepository($this, new Filesystem, $this->getCachedServicesPath()))
			                ->load($providers->collapse()->toArray());
			}
		</code-mirror
		><p>Um, what's an unwonted unreadable function in Laravel source code! Don't worry, let's just ignore the details and see what's the result:</p
		><code-mirror
			language="PHP"
			readonly="readonly"
			filename="vendor/laravel/framework/src/Illuminate/Foundation/Application.php"
		>
			(new ProviderRepository($this, new Filesystem, $this->getCachedServicesPath()))
			            ->load(dd($providers->collapse()->toArray()));
		</code-mirror
		><p>Do some testing, we can find what's the noodles do: move Illuminate service providers to the front of list, 
		and insert package service providers between them and other configured service providers. Here is a readable version:</p
		><code-mirror
			language="PHP"
			readonly="readonly"
			filename="vendor/laravel/framework/src/Illuminate/Foundation/Application.php"
		>
			public function registerConfiguredProviders()
			{
			    $providers = $this->config['app.providers'];
			
			    $illuminateProviders = array_filter($providers, function ($provider) {
			        return Str::startsWith($provider, 'Illuminate\\');
			    });
			
			    $customProviders = array_filter($providers, function ($provider) {
			        return ! Str::startsWith($provider, 'Illuminate\\');
			    });
			
			    $packageProviders = $this->make(PackageManifest::class)->providers();
			
			    $providers = array_merge($illuminateProviders, $packageProviders, $customProviders);
			
			    $providerRepository = new ProviderRepository($this, new Filesystem, $this->getCachedServicesPath());
			
			    $providerRepository->load($providers);
			}
		</code-mirror
		><p>Next, we go to the <code>ProviderRepository::load</code>:</p
		><code-mirror
			language="PHP"
			readonly="readonly"
			filename="vendor/laravel/framework/src/Illuminate/Foundation/ProviderRepository.php"
		>
			// the copy of code is modified to focus on the topical
			public function load(array $providers)
			{
			    $eagerProviders = array_filter($providers, function ($provider) {
			        return ! ((new $provider($this->app))->isDeferred());
			    });
			
			    $deferredProviders = array_filter($providers, function ($provider) {
			        return (new $provider($this->app))->isDeferred();
			    });
			
			    foreach ($eagerProviders as $provider) {
			        $this->app->register($provider);
			    }
			
			    $deferred = [];
			
			    foreach ($deferredProviders as $provider) {
			        foreach ((new $provider($this->app))->provides() as $service) {
			            $deferred[$service] = $provider;
			        }
			    }
			
			    $this->app->addDeferredServices($deferred);
			}
		</code-mirror
		><p>For normal (or eager) service providers, Laravel register it one by one. 
		For deferred providers, Laravel collect the <code>service => provider</code> map and add to <code>$app</code>. 
		Only when the service need be resolve, the service provider will be registered.</p
		><p>Let's go to <code>Illuminate\Foundation\Application::register()</code></p
		><code-mirror
			language="PHP"
			readonly="readonly"
			filename="vendor/laravel/framework/src/Illuminate/Foundation/Application.php"
		>
			// the copy of code is modified to focus on the topical
			public function register($provider, $force = false)
			{
			    if (is_string($provider)) {
			        $provider = new $provider($this);
			    }
			
			    $provider->register();
			
			    if (property_exists($provider, 'bindings')) {
			        foreach ($provider->bindings as $key => $value) {
			            $this->bind($key, $value);
			        }
			    }
			
			    if (property_exists($provider, 'singletons')) {
			        foreach ($provider->singletons as $key => $value) {
			            $this->singleton($key, $value);
			        }
			    }
			
			    if ($this->isBooted()) {
			        if (method_exists($provider, 'boot')) {
			            return $this->call([$provider, 'boot']);
			        }
			    }
			
			    return $provider;
			}
		</code-mirror
		><p>They instantiate the service provider, then run method <code>register()</code>, then bind services to container, 
		and run method <code>boot()</code> after the <code>$app</code> bootstrapped with dependency injection. So a typical service provider is like this:</p
		><code-mirror
			language="PHP"
			readonly="readonly"
		>
			class FooServiceProvider extends ServiceProvider
			{
			    public $singletons = [
			        ConnectionInterface::class => Connection::class,
			    ];
			
			    public $bindings = [
			        HelloInterface::class => Hello::class,
			    ];
			
			    public function register()
			    {
			        Connection::bootstrap();
			    }
			
			    public function boot(ConnectionInterface $connection, Hello $hello)
			    {
			        $connection->connect(config('foo.host'));
			        
			        $connection->send($hello);
			    }
			}
		</code-mirror
		><p>Declare for container in <code>$bindings</code> and <code>$singletons</code>, 
		run static initialization on <code>register()</code>, and run non-static initialization in <code>boot()</code>.</p
		><p>In this example, we will connect the connection for each process. That's expensive. 
		So we can use deferred service provider: </p
		><code-mirror
			language="PHP"
			readonly="readonly"
		>
			class FooServiceProvider extends ServiceProvider implements DeferrableProvider
			{
			    // ...
			    
			    public function provides()
			    {
			        return [
			            ConnectionInterface::class,
			            HelloInterface::class,
			        ];
			    }
			}
		</code-mirror
		><p>Now let's back to the first code block of this article, what's a dish of noodles. 
		We can slice them into several service providers. And make most of them deferred. Here is a example: </p
		><code-mirror
			language="PHP"
			readonly="readonly"
			filename="app/Providers/ViewSharingServiceProvider.php"
		>
			class ViewSharingServiceProvider extends ServiceProvider implements DeferrableProvider
			{
			    public function boot(\Illuminate\Contracts\View\Factory $view)
			    {
			        $view->share('key', 'value');
			    }
			
			    public function provides()
			    {
			        return [
			            'view',
			        ];
			    }
			}
		</code-mirror
		><p>We can use dependency injection in <code>boot()</code>, and deferred this with <code>'view'</code> service. 
		So that this service provider will only work for web requests but not API requests. 
		In fact, some negligent service providers will access view service even in API requests, 
		they make your <code>ViewSharingServiceProvider</code> run unnecessarily. You can make them or partition of them deferred too. 
		They are:</p
		><code-mirror
			language="PHP"
			readonly="readonly"
			filename="config/app.php"
		>
			Illuminate\Notifications\NotificationServiceProvider::class,
			Illuminate\Pagination\PaginationServiceProvider::class,
			// may be some third-part service providers
		</code-mirror
		><p>You can find a example of extending this two building in this project.</p
		><p>If you are trying to modify a service provider into deferred or edit the method <code>providers(), </code>
		don't forget to delete the cache file <code>bootstrap/cache/services.php</code>.</p
		><p>The official documentations says the deferred service provider is for <q>only registering bindings in the service container</q>. 
		This isn't a good guide. Our <code>ViewSharingServiceProvider</code> uses view service rather registers anything, 
		it's just okay and worthy to be a best practice.</p
		><h3>Summing up</h3
		><p>We've explored how and when the service providers and their members work, understood how to write a good service provider. 
		We also learned when and how to use deferred providers to optimize our application. And don't write a <code>AppServiceProvider</code> filled with noodles.</p
	></article
@endsection
