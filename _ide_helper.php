<?php
/* @noinspection ALL */
// @formatter:off
// phpcs:ignoreFile

/**
 * A helper file for Laravel, to provide autocomplete information to your IDE
 * Generated for Laravel 12.18.0.
 *
 * This file should not be included in your code, only analyzed by your IDE!
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 * @see https://github.com/barryvdh/laravel-ide-helper
 */
namespace Illuminate\Support\Facades {
    /**
     * 
     *
     * @see \Illuminate\Foundation\Application
     */
    class App {
        /**
         * Begin configuring a new Laravel application instance.
         *
         * @param string|null $basePath
         * @return \Illuminate\Foundation\Configuration\ApplicationBuilder 
         * @static 
         */
        public static function configure($basePath = null)
        {
            return \Illuminate\Foundation\Application::configure($basePath);
        }

        /**
         * Infer the application's base directory from the environment.
         *
         * @return string 
         * @static 
         */
        public static function inferBasePath()
        {
            return \Illuminate\Foundation\Application::inferBasePath();
        }

        /**
         * Get the version number of the application.
         *
         * @return string 
         * @static 
         */
        public static function version()
        {
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->version();
        }

        /**
         * Run the given array of bootstrap classes.
         *
         * @param string[] $bootstrappers
         * @return void 
         * @static 
         */
        public static function bootstrapWith($bootstrappers)
        {
            /** @var \Illuminate\Foundation\Application $instance */
            $instance->bootstrapWith($bootstrappers);
        }

        /**
         * Register a callback to run after loading the environment.
         *
         * @param \Closure $callback
         * @return void 
         * @static 
         */
        public static function afterLoadingEnvironment($callback)
        {
            /** @var \Illuminate\Foundation\Application $instance */
            $instance->afterLoadingEnvironment($callback);
        }

        /**
         * Register a callback to run before a bootstrapper.
         *
         * @param string $bootstrapper
         * @param \Closure $callback
         * @return void 
         * @static 
         */
        public static function beforeBootstrapping($bootstrapper, $callback)
        {
            /** @var \Illuminate\Foundation\Application $instance */
            $instance->beforeBootstrapping($bootstrapper, $callback);
        }

        /**
         * Register a callback to run after a bootstrapper.
         *
         * @param string $bootstrapper
         * @param \Closure $callback
         * @return void 
         * @static 
         */
        public static function afterBootstrapping($bootstrapper, $callback)
        {
            /** @var \Illuminate\Foundation\Application $instance */
            $instance->afterBootstrapping($bootstrapper, $callback);
        }

        /**
         * Determine if the application has been bootstrapped before.
         *
         * @return bool 
         * @static 
         */
        public static function hasBeenBootstrapped()
        {
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->hasBeenBootstrapped();
        }

        /**
         * Set the base path for the application.
         *
         * @param string $basePath
         * @return \Illuminate\Foundation\Application 
         * @static 
         */
        public static function setBasePath($basePath)
        {
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->setBasePath($basePath);
        }

        /**
         * Get the path to the application "app" directory.
         *
         * @param string $path
         * @return string 
         * @static 
         */
        public static function path($path = '')
        {
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->path($path);
        }

        /**
         * Set the application directory.
         *
         * @param string $path
         * @return \Illuminate\Foundation\Application 
         * @static 
         */
        public static function useAppPath($path)
        {
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->useAppPath($path);
        }

        /**
         * Get the base path of the Laravel installation.
         *
         * @param string $path
         * @return string 
         * @static 
         */
        public static function basePath($path = '')
        {
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->basePath($path);
        }

        /**
         * Get the path to the bootstrap directory.
         *
         * @param string $path
         * @return string 
         * @static 
         */
        public static function bootstrapPath($path = '')
        {
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->bootstrapPath($path);
        }

        /**
         * Get the path to the service provider list in the bootstrap directory.
         *
         * @return string 
         * @static 
         */
        public static function getBootstrapProvidersPath()
        {
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->getBootstrapProvidersPath();
        }

        /**
         * Set the bootstrap file directory.
         *
         * @param string $path
         * @return \Illuminate\Foundation\Application 
         * @static 
         */
        public static function useBootstrapPath($path)
        {
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->useBootstrapPath($path);
        }

        /**
         * Get the path to the application configuration files.
         *
         * @param string $path
         * @return string 
         * @static 
         */
        public static function configPath($path = '')
        {
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->configPath($path);
        }

        /**
         * Set the configuration directory.
         *
         * @param string $path
         * @return \Illuminate\Foundation\Application 
         * @static 
         */
        public static function useConfigPath($path)
        {
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->useConfigPath($path);
        }

        /**
         * Get the path to the database directory.
         *
         * @param string $path
         * @return string 
         * @static 
         */
        public static function databasePath($path = '')
        {
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->databasePath($path);
        }

        /**
         * Set the database directory.
         *
         * @param string $path
         * @return \Illuminate\Foundation\Application 
         * @static 
         */
        public static function useDatabasePath($path)
        {
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->useDatabasePath($path);
        }

        /**
         * Get the path to the language files.
         *
         * @param string $path
         * @return string 
         * @static 
         */
        public static function langPath($path = '')
        {
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->langPath($path);
        }

        /**
         * Set the language file directory.
         *
         * @param string $path
         * @return \Illuminate\Foundation\Application 
         * @static 
         */
        public static function useLangPath($path)
        {
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->useLangPath($path);
        }

        /**
         * Get the path to the public / web directory.
         *
         * @param string $path
         * @return string 
         * @static 
         */
        public static function publicPath($path = '')
        {
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->publicPath($path);
        }

        /**
         * Set the public / web directory.
         *
         * @param string $path
         * @return \Illuminate\Foundation\Application 
         * @static 
         */
        public static function usePublicPath($path)
        {
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->usePublicPath($path);
        }

        /**
         * Get the path to the storage directory.
         *
         * @param string $path
         * @return string 
         * @static 
         */
        public static function storagePath($path = '')
        {
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->storagePath($path);
        }

        /**
         * Set the storage directory.
         *
         * @param string $path
         * @return \Illuminate\Foundation\Application 
         * @static 
         */
        public static function useStoragePath($path)
        {
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->useStoragePath($path);
        }

        /**
         * Get the path to the resources directory.
         *
         * @param string $path
         * @return string 
         * @static 
         */
        public static function resourcePath($path = '')
        {
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->resourcePath($path);
        }

        /**
         * Get the path to the views directory.
         * 
         * This method returns the first configured path in the array of view paths.
         *
         * @param string $path
         * @return string 
         * @static 
         */
        public static function viewPath($path = '')
        {
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->viewPath($path);
        }

        /**
         * Join the given paths together.
         *
         * @param string $basePath
         * @param string $path
         * @return string 
         * @static 
         */
        public static function joinPaths($basePath, $path = '')
        {
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->joinPaths($basePath, $path);
        }

        /**
         * Get the path to the environment file directory.
         *
         * @return string 
         * @static 
         */
        public static function environmentPath()
        {
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->environmentPath();
        }

        /**
         * Set the directory for the environment file.
         *
         * @param string $path
         * @return \Illuminate\Foundation\Application 
         * @static 
         */
        public static function useEnvironmentPath($path)
        {
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->useEnvironmentPath($path);
        }

        /**
         * Set the environment file to be loaded during bootstrapping.
         *
         * @param string $file
         * @return \Illuminate\Foundation\Application 
         * @static 
         */
        public static function loadEnvironmentFrom($file)
        {
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->loadEnvironmentFrom($file);
        }

        /**
         * Get the environment file the application is using.
         *
         * @return string 
         * @static 
         */
        public static function environmentFile()
        {
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->environmentFile();
        }

        /**
         * Get the fully qualified path to the environment file.
         *
         * @return string 
         * @static 
         */
        public static function environmentFilePath()
        {
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->environmentFilePath();
        }

        /**
         * Get or check the current application environment.
         *
         * @param string|array $environments
         * @return string|bool 
         * @static 
         */
        public static function environment(...$environments)
        {
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->environment(...$environments);
        }

        /**
         * Determine if the application is in the local environment.
         *
         * @return bool 
         * @static 
         */
        public static function isLocal()
        {
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->isLocal();
        }

        /**
         * Determine if the application is in the production environment.
         *
         * @return bool 
         * @static 
         */
        public static function isProduction()
        {
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->isProduction();
        }

        /**
         * Detect the application's current environment.
         *
         * @param \Closure $callback
         * @return string 
         * @static 
         */
        public static function detectEnvironment($callback)
        {
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->detectEnvironment($callback);
        }

        /**
         * Determine if the application is running in the console.
         *
         * @return bool 
         * @static 
         */
        public static function runningInConsole()
        {
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->runningInConsole();
        }

        /**
         * Determine if the application is running any of the given console commands.
         *
         * @param string|array $commands
         * @return bool 
         * @static 
         */
        public static function runningConsoleCommand(...$commands)
        {
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->runningConsoleCommand(...$commands);
        }

        /**
         * Determine if the application is running unit tests.
         *
         * @return bool 
         * @static 
         */
        public static function runningUnitTests()
        {
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->runningUnitTests();
        }

        /**
         * Determine if the application is running with debug mode enabled.
         *
         * @return bool 
         * @static 
         */
        public static function hasDebugModeEnabled()
        {
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->hasDebugModeEnabled();
        }

        /**
         * Register a new registered listener.
         *
         * @param callable $callback
         * @return void 
         * @static 
         */
        public static function registered($callback)
        {
            /** @var \Illuminate\Foundation\Application $instance */
            $instance->registered($callback);
        }

        /**
         * Register all of the configured providers.
         *
         * @return void 
         * @static 
         */
        public static function registerConfiguredProviders()
        {
            /** @var \Illuminate\Foundation\Application $instance */
            $instance->registerConfiguredProviders();
        }

        /**
         * Register a service provider with the application.
         *
         * @param \Illuminate\Support\ServiceProvider|string $provider
         * @param bool $force
         * @return \Illuminate\Support\ServiceProvider 
         * @static 
         */
        public static function register($provider, $force = false)
        {
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->register($provider, $force);
        }

        /**
         * Get the registered service provider instance if it exists.
         *
         * @param \Illuminate\Support\ServiceProvider|string $provider
         * @return \Illuminate\Support\ServiceProvider|null 
         * @static 
         */
        public static function getProvider($provider)
        {
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->getProvider($provider);
        }

        /**
         * Get the registered service provider instances if any exist.
         *
         * @param \Illuminate\Support\ServiceProvider|string $provider
         * @return array 
         * @static 
         */
        public static function getProviders($provider)
        {
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->getProviders($provider);
        }

        /**
         * Resolve a service provider instance from the class name.
         *
         * @param string $provider
         * @return \Illuminate\Support\ServiceProvider 
         * @static 
         */
        public static function resolveProvider($provider)
        {
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->resolveProvider($provider);
        }

        /**
         * Load and boot all of the remaining deferred providers.
         *
         * @return void 
         * @static 
         */
        public static function loadDeferredProviders()
        {
            /** @var \Illuminate\Foundation\Application $instance */
            $instance->loadDeferredProviders();
        }

        /**
         * Load the provider for a deferred service.
         *
         * @param string $service
         * @return void 
         * @static 
         */
        public static function loadDeferredProvider($service)
        {
            /** @var \Illuminate\Foundation\Application $instance */
            $instance->loadDeferredProvider($service);
        }

        /**
         * Register a deferred provider and service.
         *
         * @param string $provider
         * @param string|null $service
         * @return void 
         * @static 
         */
        public static function registerDeferredProvider($provider, $service = null)
        {
            /** @var \Illuminate\Foundation\Application $instance */
            $instance->registerDeferredProvider($provider, $service);
        }

        /**
         * Resolve the given type from the container.
         *
         * @template TClass of object
         * @param string|class-string<TClass> $abstract
         * @param array $parameters
         * @return ($abstract is class-string<TClass> ? TClass : mixed)
         * @throws \Illuminate\Contracts\Container\BindingResolutionException
         * @static 
         */
        public static function make($abstract, $parameters = [])
        {
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->make($abstract, $parameters);
        }

        /**
         * Determine if the given abstract type has been bound.
         *
         * @param string $abstract
         * @return bool 
         * @static 
         */
        public static function bound($abstract)
        {
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->bound($abstract);
        }

        /**
         * Determine if the application has booted.
         *
         * @return bool 
         * @static 
         */
        public static function isBooted()
        {
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->isBooted();
        }

        /**
         * Boot the application's service providers.
         *
         * @return void 
         * @static 
         */
        public static function boot()
        {
            /** @var \Illuminate\Foundation\Application $instance */
            $instance->boot();
        }

        /**
         * Register a new boot listener.
         *
         * @param callable $callback
         * @return void 
         * @static 
         */
        public static function booting($callback)
        {
            /** @var \Illuminate\Foundation\Application $instance */
            $instance->booting($callback);
        }

        /**
         * Register a new "booted" listener.
         *
         * @param callable $callback
         * @return void 
         * @static 
         */
        public static function booted($callback)
        {
            /** @var \Illuminate\Foundation\Application $instance */
            $instance->booted($callback);
        }

        /**
         * {@inheritdoc}
         *
         * @return \Symfony\Component\HttpFoundation\Response 
         * @static 
         */
        public static function handle($request, $type = 1, $catch = true)
        {
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->handle($request, $type, $catch);
        }

        /**
         * Handle the incoming HTTP request and send the response to the browser.
         *
         * @param \Illuminate\Http\Request $request
         * @return void 
         * @static 
         */
        public static function handleRequest($request)
        {
            /** @var \Illuminate\Foundation\Application $instance */
            $instance->handleRequest($request);
        }

        /**
         * Handle the incoming Artisan command.
         *
         * @param \Symfony\Component\Console\Input\InputInterface $input
         * @return int 
         * @static 
         */
        public static function handleCommand($input)
        {
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->handleCommand($input);
        }

        /**
         * Determine if the framework's base configuration should be merged.
         *
         * @return bool 
         * @static 
         */
        public static function shouldMergeFrameworkConfiguration()
        {
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->shouldMergeFrameworkConfiguration();
        }

        /**
         * Indicate that the framework's base configuration should not be merged.
         *
         * @return \Illuminate\Foundation\Application 
         * @static 
         */
        public static function dontMergeFrameworkConfiguration()
        {
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->dontMergeFrameworkConfiguration();
        }

        /**
         * Determine if middleware has been disabled for the application.
         *
         * @return bool 
         * @static 
         */
        public static function shouldSkipMiddleware()
        {
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->shouldSkipMiddleware();
        }

        /**
         * Get the path to the cached services.php file.
         *
         * @return string 
         * @static 
         */
        public static function getCachedServicesPath()
        {
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->getCachedServicesPath();
        }

        /**
         * Get the path to the cached packages.php file.
         *
         * @return string 
         * @static 
         */
        public static function getCachedPackagesPath()
        {
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->getCachedPackagesPath();
        }

        /**
         * Determine if the application configuration is cached.
         *
         * @return bool 
         * @static 
         */
        public static function configurationIsCached()
        {
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->configurationIsCached();
        }

        /**
         * Get the path to the configuration cache file.
         *
         * @return string 
         * @static 
         */
        public static function getCachedConfigPath()
        {
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->getCachedConfigPath();
        }

        /**
         * Determine if the application routes are cached.
         *
         * @return bool 
         * @static 
         */
        public static function routesAreCached()
        {
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->routesAreCached();
        }

        /**
         * Get the path to the routes cache file.
         *
         * @return string 
         * @static 
         */
        public static function getCachedRoutesPath()
        {
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->getCachedRoutesPath();
        }

        /**
         * Determine if the application events are cached.
         *
         * @return bool 
         * @static 
         */
        public static function eventsAreCached()
        {
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->eventsAreCached();
        }

        /**
         * Get the path to the events cache file.
         *
         * @return string 
         * @static 
         */
        public static function getCachedEventsPath()
        {
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->getCachedEventsPath();
        }

        /**
         * Add new prefix to list of absolute path prefixes.
         *
         * @param string $prefix
         * @return \Illuminate\Foundation\Application 
         * @static 
         */
        public static function addAbsoluteCachePathPrefix($prefix)
        {
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->addAbsoluteCachePathPrefix($prefix);
        }

        /**
         * Get an instance of the maintenance mode manager implementation.
         *
         * @return \Illuminate\Contracts\Foundation\MaintenanceMode 
         * @static 
         */
        public static function maintenanceMode()
        {
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->maintenanceMode();
        }

        /**
         * Determine if the application is currently down for maintenance.
         *
         * @return bool 
         * @static 
         */
        public static function isDownForMaintenance()
        {
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->isDownForMaintenance();
        }

        /**
         * Throw an HttpException with the given data.
         *
         * @param int $code
         * @param string $message
         * @param array $headers
         * @return never 
         * @throws \Symfony\Component\HttpKernel\Exception\HttpException
         * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
         * @static 
         */
        public static function abort($code, $message = '', $headers = [])
        {
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->abort($code, $message, $headers);
        }

        /**
         * Register a terminating callback with the application.
         *
         * @param callable|string $callback
         * @return \Illuminate\Foundation\Application 
         * @static 
         */
        public static function terminating($callback)
        {
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->terminating($callback);
        }

        /**
         * Terminate the application.
         *
         * @return void 
         * @static 
         */
        public static function terminate()
        {
            /** @var \Illuminate\Foundation\Application $instance */
            $instance->terminate();
        }

        /**
         * Get the service providers that have been loaded.
         *
         * @return array<string, bool> 
         * @static 
         */
        public static function getLoadedProviders()
        {
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->getLoadedProviders();
        }

        /**
         * Determine if the given service provider is loaded.
         *
         * @param string $provider
         * @return bool 
         * @static 
         */
        public static function providerIsLoaded($provider)
        {
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->providerIsLoaded($provider);
        }

        /**
         * Get the application's deferred services.
         *
         * @return array 
         * @static 
         */
        public static function getDeferredServices()
        {
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->getDeferredServices();
        }

        /**
         * Set the application's deferred services.
         *
         * @param array $services
         * @return void 
         * @static 
         */
        public static function setDeferredServices($services)
        {
            /** @var \Illuminate\Foundation\Application $instance */
            $instance->setDeferredServices($services);
        }

        /**
         * Determine if the given service is a deferred service.
         *
         * @param string $service
         * @return bool 
         * @static 
         */
        public static function isDeferredService($service)
        {
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->isDeferredService($service);
        }

        /**
         * Add an array of services to the application's deferred services.
         *
         * @param array $services
         * @return void 
         * @static 
         */
        public static function addDeferredServices($services)
        {
            /** @var \Illuminate\Foundation\Application $instance */
            $instance->addDeferredServices($services);
        }

        /**
         * Remove an array of services from the application's deferred services.
         *
         * @param array $services
         * @return void 
         * @static 
         */
        public static function removeDeferredServices($services)
        {
            /** @var \Illuminate\Foundation\Application $instance */
            $instance->removeDeferredServices($services);
        }

        /**
         * Configure the real-time facade namespace.
         *
         * @param string $namespace
         * @return void 
         * @static 
         */
        public static function provideFacades($namespace)
        {
            /** @var \Illuminate\Foundation\Application $instance */
            $instance->provideFacades($namespace);
        }

        /**
         * Get the current application locale.
         *
         * @return string 
         * @static 
         */
        public static function getLocale()
        {
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->getLocale();
        }

        /**
         * Get the current application locale.
         *
         * @return string 
         * @static 
         */
        public static function currentLocale()
        {
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->currentLocale();
        }

        /**
         * Get the current application fallback locale.
         *
         * @return string 
         * @static 
         */
        public static function getFallbackLocale()
        {
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->getFallbackLocale();
        }

        /**
         * Set the current application locale.
         *
         * @param string $locale
         * @return void 
         * @static 
         */
        public static function setLocale($locale)
        {
            /** @var \Illuminate\Foundation\Application $instance */
            $instance->setLocale($locale);
        }

        /**
         * Set the current application fallback locale.
         *
         * @param string $fallbackLocale
         * @return void 
         * @static 
         */
        public static function setFallbackLocale($fallbackLocale)
        {
            /** @var \Illuminate\Foundation\Application $instance */
            $instance->setFallbackLocale($fallbackLocale);
        }

        /**
         * Determine if the application locale is the given locale.
         *
         * @param string $locale
         * @return bool 
         * @static 
         */
        public static function isLocale($locale)
        {
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->isLocale($locale);
        }

        /**
         * Register the core class aliases in the container.
         *
         * @return void 
         * @static 
         */
        public static function registerCoreContainerAliases()
        {
            /** @var \Illuminate\Foundation\Application $instance */
            $instance->registerCoreContainerAliases();
        }

        /**
         * Flush the container of all bindings and resolved instances.
         *
         * @return void 
         * @static 
         */
        public static function flush()
        {
            /** @var \Illuminate\Foundation\Application $instance */
            $instance->flush();
        }

        /**
         * Get the application namespace.
         *
         * @return string 
         * @throws \RuntimeException
         * @static 
         */
        public static function getNamespace()
        {
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->getNamespace();
        }

        /**
         * Define a contextual binding.
         *
         * @param array|string $concrete
         * @return \Illuminate\Contracts\Container\ContextualBindingBuilder 
         * @static 
         */
        public static function when($concrete)
        {
            //Method inherited from \Illuminate\Container\Container 
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->when($concrete);
        }

        /**
         * Define a contextual binding based on an attribute.
         *
         * @param string $attribute
         * @param \Closure $handler
         * @return void 
         * @static 
         */
        public static function whenHasAttribute($attribute, $handler)
        {
            //Method inherited from \Illuminate\Container\Container 
            /** @var \Illuminate\Foundation\Application $instance */
            $instance->whenHasAttribute($attribute, $handler);
        }

        /**
         * Returns true if the container can return an entry for the given identifier.
         * 
         * Returns false otherwise.
         * 
         * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
         * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
         *
         * @return bool 
         * @param string $id Identifier of the entry to look for.
         * @return bool 
         * @static 
         */
        public static function has($id)
        {
            //Method inherited from \Illuminate\Container\Container 
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->has($id);
        }

        /**
         * Determine if the given abstract type has been resolved.
         *
         * @param string $abstract
         * @return bool 
         * @static 
         */
        public static function resolved($abstract)
        {
            //Method inherited from \Illuminate\Container\Container 
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->resolved($abstract);
        }

        /**
         * Determine if a given type is shared.
         *
         * @param string $abstract
         * @return bool 
         * @static 
         */
        public static function isShared($abstract)
        {
            //Method inherited from \Illuminate\Container\Container 
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->isShared($abstract);
        }

        /**
         * Determine if a given string is an alias.
         *
         * @param string $name
         * @return bool 
         * @static 
         */
        public static function isAlias($name)
        {
            //Method inherited from \Illuminate\Container\Container 
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->isAlias($name);
        }

        /**
         * Register a binding with the container.
         *
         * @param \Closure|string $abstract
         * @param \Closure|string|null $concrete
         * @param bool $shared
         * @return void 
         * @throws \TypeError
         * @throws ReflectionException
         * @static 
         */
        public static function bind($abstract, $concrete = null, $shared = false)
        {
            //Method inherited from \Illuminate\Container\Container 
            /** @var \Illuminate\Foundation\Application $instance */
            $instance->bind($abstract, $concrete, $shared);
        }

        /**
         * Determine if the container has a method binding.
         *
         * @param string $method
         * @return bool 
         * @static 
         */
        public static function hasMethodBinding($method)
        {
            //Method inherited from \Illuminate\Container\Container 
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->hasMethodBinding($method);
        }

        /**
         * Bind a callback to resolve with Container::call.
         *
         * @param array|string $method
         * @param \Closure $callback
         * @return void 
         * @static 
         */
        public static function bindMethod($method, $callback)
        {
            //Method inherited from \Illuminate\Container\Container 
            /** @var \Illuminate\Foundation\Application $instance */
            $instance->bindMethod($method, $callback);
        }

        /**
         * Get the method binding for the given method.
         *
         * @param string $method
         * @param mixed $instance
         * @return mixed 
         * @static 
         */
        public static function callMethodBinding($method, $instance)
        {
            //Method inherited from \Illuminate\Container\Container 
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->callMethodBinding($method, $instance);
        }

        /**
         * Add a contextual binding to the container.
         *
         * @param string $concrete
         * @param \Closure|string $abstract
         * @param \Closure|string $implementation
         * @return void 
         * @static 
         */
        public static function addContextualBinding($concrete, $abstract, $implementation)
        {
            //Method inherited from \Illuminate\Container\Container 
            /** @var \Illuminate\Foundation\Application $instance */
            $instance->addContextualBinding($concrete, $abstract, $implementation);
        }

        /**
         * Register a binding if it hasn't already been registered.
         *
         * @param \Closure|string $abstract
         * @param \Closure|string|null $concrete
         * @param bool $shared
         * @return void 
         * @static 
         */
        public static function bindIf($abstract, $concrete = null, $shared = false)
        {
            //Method inherited from \Illuminate\Container\Container 
            /** @var \Illuminate\Foundation\Application $instance */
            $instance->bindIf($abstract, $concrete, $shared);
        }

        /**
         * Register a shared binding in the container.
         *
         * @param \Closure|string $abstract
         * @param \Closure|string|null $concrete
         * @return void 
         * @static 
         */
        public static function singleton($abstract, $concrete = null)
        {
            //Method inherited from \Illuminate\Container\Container 
            /** @var \Illuminate\Foundation\Application $instance */
            $instance->singleton($abstract, $concrete);
        }

        /**
         * Register a shared binding if it hasn't already been registered.
         *
         * @param \Closure|string $abstract
         * @param \Closure|string|null $concrete
         * @return void 
         * @static 
         */
        public static function singletonIf($abstract, $concrete = null)
        {
            //Method inherited from \Illuminate\Container\Container 
            /** @var \Illuminate\Foundation\Application $instance */
            $instance->singletonIf($abstract, $concrete);
        }

        /**
         * Register a scoped binding in the container.
         *
         * @param \Closure|string $abstract
         * @param \Closure|string|null $concrete
         * @return void 
         * @static 
         */
        public static function scoped($abstract, $concrete = null)
        {
            //Method inherited from \Illuminate\Container\Container 
            /** @var \Illuminate\Foundation\Application $instance */
            $instance->scoped($abstract, $concrete);
        }

        /**
         * Register a scoped binding if it hasn't already been registered.
         *
         * @param \Closure|string $abstract
         * @param \Closure|string|null $concrete
         * @return void 
         * @static 
         */
        public static function scopedIf($abstract, $concrete = null)
        {
            //Method inherited from \Illuminate\Container\Container 
            /** @var \Illuminate\Foundation\Application $instance */
            $instance->scopedIf($abstract, $concrete);
        }

        /**
         * "Extend" an abstract type in the container.
         *
         * @param string $abstract
         * @param \Closure $closure
         * @return void 
         * @throws \InvalidArgumentException
         * @static 
         */
        public static function extend($abstract, $closure)
        {
            //Method inherited from \Illuminate\Container\Container 
            /** @var \Illuminate\Foundation\Application $instance */
            $instance->extend($abstract, $closure);
        }

        /**
         * Register an existing instance as shared in the container.
         *
         * @template TInstance of mixed
         * @param string $abstract
         * @param TInstance $instance
         * @return TInstance 
         * @static 
         */
        public static function instance($abstract, $instance)
        {
            //Method inherited from \Illuminate\Container\Container 
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->instance($abstract, $instance);
        }

        /**
         * Assign a set of tags to a given binding.
         *
         * @param array|string $abstracts
         * @param array|mixed $tags
         * @return void 
         * @static 
         */
        public static function tag($abstracts, $tags)
        {
            //Method inherited from \Illuminate\Container\Container 
            /** @var \Illuminate\Foundation\Application $instance */
            $instance->tag($abstracts, $tags);
        }

        /**
         * Resolve all of the bindings for a given tag.
         *
         * @param string $tag
         * @return iterable 
         * @static 
         */
        public static function tagged($tag)
        {
            //Method inherited from \Illuminate\Container\Container 
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->tagged($tag);
        }

        /**
         * Alias a type to a different name.
         *
         * @param string $abstract
         * @param string $alias
         * @return void 
         * @throws \LogicException
         * @static 
         */
        public static function alias($abstract, $alias)
        {
            //Method inherited from \Illuminate\Container\Container 
            /** @var \Illuminate\Foundation\Application $instance */
            $instance->alias($abstract, $alias);
        }

        /**
         * Bind a new callback to an abstract's rebind event.
         *
         * @param string $abstract
         * @param \Closure $callback
         * @return mixed 
         * @static 
         */
        public static function rebinding($abstract, $callback)
        {
            //Method inherited from \Illuminate\Container\Container 
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->rebinding($abstract, $callback);
        }

        /**
         * Refresh an instance on the given target and method.
         *
         * @param string $abstract
         * @param mixed $target
         * @param string $method
         * @return mixed 
         * @static 
         */
        public static function refresh($abstract, $target, $method)
        {
            //Method inherited from \Illuminate\Container\Container 
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->refresh($abstract, $target, $method);
        }

        /**
         * Wrap the given closure such that its dependencies will be injected when executed.
         *
         * @param \Closure $callback
         * @param array $parameters
         * @return \Closure 
         * @static 
         */
        public static function wrap($callback, $parameters = [])
        {
            //Method inherited from \Illuminate\Container\Container 
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->wrap($callback, $parameters);
        }

        /**
         * Call the given Closure / class@method and inject its dependencies.
         *
         * @param callable|string $callback
         * @param array<string, mixed> $parameters
         * @param string|null $defaultMethod
         * @return mixed 
         * @throws \InvalidArgumentException
         * @static 
         */
        public static function call($callback, $parameters = [], $defaultMethod = null)
        {
            //Method inherited from \Illuminate\Container\Container 
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->call($callback, $parameters, $defaultMethod);
        }

        /**
         * Get a closure to resolve the given type from the container.
         *
         * @template TClass of object
         * @param string|class-string<TClass> $abstract
         * @return ($abstract is class-string<TClass> ? \Closure(): TClass : \Closure(): mixed)
         * @static 
         */
        public static function factory($abstract)
        {
            //Method inherited from \Illuminate\Container\Container 
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->factory($abstract);
        }

        /**
         * An alias function name for make().
         *
         * @template TClass of object
         * @param string|class-string<TClass>|callable $abstract
         * @param array $parameters
         * @return ($abstract is class-string<TClass> ? TClass : mixed)
         * @throws \Illuminate\Contracts\Container\BindingResolutionException
         * @static 
         */
        public static function makeWith($abstract, $parameters = [])
        {
            //Method inherited from \Illuminate\Container\Container 
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->makeWith($abstract, $parameters);
        }

        /**
         * {@inheritdoc}
         *
         * @template TClass of object
         * @param string|class-string<TClass> $id
         * @return ($id is class-string<TClass> ? TClass : mixed)
         * @static 
         */
        public static function get($id)
        {
            //Method inherited from \Illuminate\Container\Container 
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->get($id);
        }

        /**
         * Instantiate a concrete instance of the given type.
         *
         * @template TClass of object
         * @param \Closure(static, array):  TClass|class-string<TClass>  $concrete
         * @return TClass 
         * @throws \Illuminate\Contracts\Container\BindingResolutionException
         * @throws \Illuminate\Contracts\Container\CircularDependencyException
         * @static 
         */
        public static function build($concrete)
        {
            //Method inherited from \Illuminate\Container\Container 
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->build($concrete);
        }

        /**
         * Resolve a dependency based on an attribute.
         *
         * @param \ReflectionAttribute $attribute
         * @return mixed 
         * @static 
         */
        public static function resolveFromAttribute($attribute)
        {
            //Method inherited from \Illuminate\Container\Container 
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->resolveFromAttribute($attribute);
        }

        /**
         * Register a new before resolving callback for all types.
         *
         * @param \Closure|string $abstract
         * @param \Closure|null $callback
         * @return void 
         * @static 
         */
        public static function beforeResolving($abstract, $callback = null)
        {
            //Method inherited from \Illuminate\Container\Container 
            /** @var \Illuminate\Foundation\Application $instance */
            $instance->beforeResolving($abstract, $callback);
        }

        /**
         * Register a new resolving callback.
         *
         * @param \Closure|string $abstract
         * @param \Closure|null $callback
         * @return void 
         * @static 
         */
        public static function resolving($abstract, $callback = null)
        {
            //Method inherited from \Illuminate\Container\Container 
            /** @var \Illuminate\Foundation\Application $instance */
            $instance->resolving($abstract, $callback);
        }

        /**
         * Register a new after resolving callback for all types.
         *
         * @param \Closure|string $abstract
         * @param \Closure|null $callback
         * @return void 
         * @static 
         */
        public static function afterResolving($abstract, $callback = null)
        {
            //Method inherited from \Illuminate\Container\Container 
            /** @var \Illuminate\Foundation\Application $instance */
            $instance->afterResolving($abstract, $callback);
        }

        /**
         * Register a new after resolving attribute callback for all types.
         *
         * @param string $attribute
         * @param \Closure $callback
         * @return void 
         * @static 
         */
        public static function afterResolvingAttribute($attribute, $callback)
        {
            //Method inherited from \Illuminate\Container\Container 
            /** @var \Illuminate\Foundation\Application $instance */
            $instance->afterResolvingAttribute($attribute, $callback);
        }

        /**
         * Fire all of the after resolving attribute callbacks.
         *
         * @param \ReflectionAttribute[] $attributes
         * @param mixed $object
         * @return void 
         * @static 
         */
        public static function fireAfterResolvingAttributeCallbacks($attributes, $object)
        {
            //Method inherited from \Illuminate\Container\Container 
            /** @var \Illuminate\Foundation\Application $instance */
            $instance->fireAfterResolvingAttributeCallbacks($attributes, $object);
        }

        /**
         * Get the name of the binding the container is currently resolving.
         *
         * @return class-string|string|null 
         * @static 
         */
        public static function currentlyResolving()
        {
            //Method inherited from \Illuminate\Container\Container 
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->currentlyResolving();
        }

        /**
         * Get the container's bindings.
         *
         * @return array 
         * @static 
         */
        public static function getBindings()
        {
            //Method inherited from \Illuminate\Container\Container 
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->getBindings();
        }

        /**
         * Get the alias for an abstract if available.
         *
         * @param string $abstract
         * @return string 
         * @static 
         */
        public static function getAlias($abstract)
        {
            //Method inherited from \Illuminate\Container\Container 
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->getAlias($abstract);
        }

        /**
         * Remove all of the extender callbacks for a given type.
         *
         * @param string $abstract
         * @return void 
         * @static 
         */
        public static function forgetExtenders($abstract)
        {
            //Method inherited from \Illuminate\Container\Container 
            /** @var \Illuminate\Foundation\Application $instance */
            $instance->forgetExtenders($abstract);
        }

        /**
         * Remove a resolved instance from the instance cache.
         *
         * @param string $abstract
         * @return void 
         * @static 
         */
        public static function forgetInstance($abstract)
        {
            //Method inherited from \Illuminate\Container\Container 
            /** @var \Illuminate\Foundation\Application $instance */
            $instance->forgetInstance($abstract);
        }

        /**
         * Clear all of the instances from the container.
         *
         * @return void 
         * @static 
         */
        public static function forgetInstances()
        {
            //Method inherited from \Illuminate\Container\Container 
            /** @var \Illuminate\Foundation\Application $instance */
            $instance->forgetInstances();
        }

        /**
         * Clear all of the scoped instances from the container.
         *
         * @return void 
         * @static 
         */
        public static function forgetScopedInstances()
        {
            //Method inherited from \Illuminate\Container\Container 
            /** @var \Illuminate\Foundation\Application $instance */
            $instance->forgetScopedInstances();
        }

        /**
         * Get the globally available instance of the container.
         *
         * @return static 
         * @static 
         */
        public static function getInstance()
        {
            //Method inherited from \Illuminate\Container\Container 
            return \Illuminate\Foundation\Application::getInstance();
        }

        /**
         * Set the shared instance of the container.
         *
         * @param \Illuminate\Contracts\Container\Container|null $container
         * @return \Illuminate\Contracts\Container\Container|static 
         * @static 
         */
        public static function setInstance($container = null)
        {
            //Method inherited from \Illuminate\Container\Container 
            return \Illuminate\Foundation\Application::setInstance($container);
        }

        /**
         * Determine if a given offset exists.
         *
         * @param string $key
         * @return bool 
         * @static 
         */
        public static function offsetExists($key)
        {
            //Method inherited from \Illuminate\Container\Container 
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->offsetExists($key);
        }

        /**
         * Get the value at a given offset.
         *
         * @param string $key
         * @return mixed 
         * @static 
         */
        public static function offsetGet($key)
        {
            //Method inherited from \Illuminate\Container\Container 
            /** @var \Illuminate\Foundation\Application $instance */
            return $instance->offsetGet($key);
        }

        /**
         * Set the value at a given offset.
         *
         * @param string $key
         * @param mixed $value
         * @return void 
         * @static 
         */
        public static function offsetSet($key, $value)
        {
            //Method inherited from \Illuminate\Container\Container 
            /** @var \Illuminate\Foundation\Application $instance */
            $instance->offsetSet($key, $value);
        }

        /**
         * Unset the value at a given offset.
         *
         * @param string $key
         * @return void 
         * @static 
         */
        public static function offsetUnset($key)
        {
            //Method inherited from \Illuminate\Container\Container 
            /** @var \Illuminate\Foundation\Application $instance */
            $instance->offsetUnset($key);
        }

        /**
         * Register a custom macro.
         *
         * @param string $name
         * @param object|callable $macro
         * @param-closure-this static  $macro
         * @return void 
         * @static 
         */
        public static function macro($name, $macro)
        {
            \Illuminate\Foundation\Application::macro($name, $macro);
        }

        /**
         * Mix another object into the class.
         *
         * @param object $mixin
         * @param bool $replace
         * @return void 
         * @throws \ReflectionException
         * @static 
         */
        public static function mixin($mixin, $replace = true)
        {
            \Illuminate\Foundation\Application::mixin($mixin, $replace);
        }

        /**
         * Checks if macro is registered.
         *
         * @param string $name
         * @return bool 
         * @static 
         */
        public static function hasMacro($name)
        {
            return \Illuminate\Foundation\Application::hasMacro($name);
        }

        /**
         * Flush the existing macros.
         *
         * @return void 
         * @static 
         */
        public static function flushMacros()
        {
            \Illuminate\Foundation\Application::flushMacros();
        }

            }
    /**
     * 
     *
     * @see \Illuminate\Foundation\Console\Kernel
     */
    class Artisan {
        /**
         * Re-route the Symfony command events to their Laravel counterparts.
         *
         * @internal 
         * @return \Illuminate\Foundation\Console\Kernel 
         * @static 
         */
        public static function rerouteSymfonyCommandEvents()
        {
            /** @var \Illuminate\Foundation\Console\Kernel $instance */
            return $instance->rerouteSymfonyCommandEvents();
        }

        /**
         * Run the console application.
         *
         * @param \Symfony\Component\Console\Input\InputInterface $input
         * @param \Symfony\Component\Console\Output\OutputInterface|null $output
         * @return int 
         * @static 
         */
        public static function handle($input, $output = null)
        {
            /** @var \Illuminate\Foundation\Console\Kernel $instance */
            return $instance->handle($input, $output);
        }

        /**
         * Terminate the application.
         *
         * @param \Symfony\Component\Console\Input\InputInterface $input
         * @param int $status
         * @return void 
         * @static 
         */
        public static function terminate($input, $status)
        {
            /** @var \Illuminate\Foundation\Console\Kernel $instance */
            $instance->terminate($input, $status);
        }

        /**
         * Register a callback to be invoked when the command lifecycle duration exceeds a given amount of time.
         *
         * @param \DateTimeInterface|\Carbon\CarbonInterval|float|int $threshold
         * @param callable $handler
         * @return void 
         * @static 
         */
        public static function whenCommandLifecycleIsLongerThan($threshold, $handler)
        {
            /** @var \Illuminate\Foundation\Console\Kernel $instance */
            $instance->whenCommandLifecycleIsLongerThan($threshold, $handler);
        }

        /**
         * When the command being handled started.
         *
         * @return \Illuminate\Support\Carbon|null 
         * @static 
         */
        public static function commandStartedAt()
        {
            /** @var \Illuminate\Foundation\Console\Kernel $instance */
            return $instance->commandStartedAt();
        }

        /**
         * Resolve a console schedule instance.
         *
         * @return \Illuminate\Console\Scheduling\Schedule 
         * @static 
         */
        public static function resolveConsoleSchedule()
        {
            /** @var \Illuminate\Foundation\Console\Kernel $instance */
            return $instance->resolveConsoleSchedule();
        }

        /**
         * Register a Closure based command with the application.
         *
         * @param string $signature
         * @param \Closure $callback
         * @return \Illuminate\Foundation\Console\ClosureCommand 
         * @static 
         */
        public static function command($signature, $callback)
        {
            /** @var \Illuminate\Foundation\Console\Kernel $instance */
            return $instance->command($signature, $callback);
        }

        /**
         * Register the given command with the console application.
         *
         * @param \Symfony\Component\Console\Command\Command $command
         * @return void 
         * @static 
         */
        public static function registerCommand($command)
        {
            /** @var \Illuminate\Foundation\Console\Kernel $instance */
            $instance->registerCommand($command);
        }

        /**
         * Run an Artisan console command by name.
         *
         * @param string $command
         * @param array $parameters
         * @param \Symfony\Component\Console\Output\OutputInterface|null $outputBuffer
         * @return int 
         * @throws \Symfony\Component\Console\Exception\CommandNotFoundException
         * @static 
         */
        public static function call($command, $parameters = [], $outputBuffer = null)
        {
            /** @var \Illuminate\Foundation\Console\Kernel $instance */
            return $instance->call($command, $parameters, $outputBuffer);
        }

        /**
         * Queue the given console command.
         *
         * @param string $command
         * @param array $parameters
         * @return \Illuminate\Foundation\Bus\PendingDispatch 
         * @static 
         */
        public static function queue($command, $parameters = [])
        {
            /** @var \Illuminate\Foundation\Console\Kernel $instance */
            return $instance->queue($command, $parameters);
        }

        /**
         * Get all of the commands registered with the console.
         *
         * @return array 
         * @static 
         */
        public static function all()
        {
            /** @var \Illuminate\Foundation\Console\Kernel $instance */
            return $instance->all();
        }

        /**
         * Get the output for the last run command.
         *
         * @return string 
         * @static 
         */
        public static function output()
        {
            /** @var \Illuminate\Foundation\Console\Kernel $instance */
            return $instance->output();
        }

        /**
         * Bootstrap the application for artisan commands.
         *
         * @return void 
         * @static 
         */
        public static function bootstrap()
        {
            /** @var \Illuminate\Foundation\Console\Kernel $instance */
            $instance->bootstrap();
        }

        /**
         * Bootstrap the application without booting service providers.
         *
         * @return void 
         * @static 
         */
        public static function bootstrapWithoutBootingProviders()
        {
            /** @var \Illuminate\Foundation\Console\Kernel $instance */
            $instance->bootstrapWithoutBootingProviders();
        }

        /**
         * Set the Artisan application instance.
         *
         * @param \Illuminate\Console\Application|null $artisan
         * @return void 
         * @static 
         */
        public static function setArtisan($artisan)
        {
            /** @var \Illuminate\Foundation\Console\Kernel $instance */
            $instance->setArtisan($artisan);
        }

        /**
         * Set the Artisan commands provided by the application.
         *
         * @param array $commands
         * @return \Illuminate\Foundation\Console\Kernel 
         * @static 
         */
        public static function addCommands($commands)
        {
            /** @var \Illuminate\Foundation\Console\Kernel $instance */
            return $instance->addCommands($commands);
        }

        /**
         * Set the paths that should have their Artisan commands automatically discovered.
         *
         * @param array $paths
         * @return \Illuminate\Foundation\Console\Kernel 
         * @static 
         */
        public static function addCommandPaths($paths)
        {
            /** @var \Illuminate\Foundation\Console\Kernel $instance */
            return $instance->addCommandPaths($paths);
        }

        /**
         * Set the paths that should have their Artisan "routes" automatically discovered.
         *
         * @param array $paths
         * @return \Illuminate\Foundation\Console\Kernel 
         * @static 
         */
        public static function addCommandRoutePaths($paths)
        {
            /** @var \Illuminate\Foundation\Console\Kernel $instance */
            return $instance->addCommandRoutePaths($paths);
        }

            }
    /**
     * 
     *
     * @see \Illuminate\Auth\AuthManager
     * @see \Illuminate\Auth\SessionGuard
     */
    class Auth {
        /**
         * Attempt to get the guard from the local cache.
         *
         * @param string|null $name
         * @return \Illuminate\Contracts\Auth\Guard|\Illuminate\Contracts\Auth\StatefulGuard 
         * @static 
         */
        public static function guard($name = null)
        {
            /** @var \Illuminate\Auth\AuthManager $instance */
            return $instance->guard($name);
        }

        /**
         * Create a session based authentication guard.
         *
         * @param string $name
         * @param array $config
         * @return \Illuminate\Auth\SessionGuard 
         * @static 
         */
        public static function createSessionDriver($name, $config)
        {
            /** @var \Illuminate\Auth\AuthManager $instance */
            return $instance->createSessionDriver($name, $config);
        }

        /**
         * Create a token based authentication guard.
         *
         * @param string $name
         * @param array $config
         * @return \Illuminate\Auth\TokenGuard 
         * @static 
         */
        public static function createTokenDriver($name, $config)
        {
            /** @var \Illuminate\Auth\AuthManager $instance */
            return $instance->createTokenDriver($name, $config);
        }

        /**
         * Get the default authentication driver name.
         *
         * @return string 
         * @static 
         */
        public static function getDefaultDriver()
        {
            /** @var \Illuminate\Auth\AuthManager $instance */
            return $instance->getDefaultDriver();
        }

        /**
         * Set the default guard driver the factory should serve.
         *
         * @param string $name
         * @return void 
         * @static 
         */
        public static function shouldUse($name)
        {
            /** @var \Illuminate\Auth\AuthManager $instance */
            $instance->shouldUse($name);
        }

        /**
         * Set the default authentication driver name.
         *
         * @param string $name
         * @return void 
         * @static 
         */
        public static function setDefaultDriver($name)
        {
            /** @var \Illuminate\Auth\AuthManager $instance */
            $instance->setDefaultDriver($name);
        }

        /**
         * Register a new callback based request guard.
         *
         * @param string $driver
         * @param callable $callback
         * @return \Illuminate\Auth\AuthManager 
         * @static 
         */
        public static function viaRequest($driver, $callback)
        {
            /** @var \Illuminate\Auth\AuthManager $instance */
            return $instance->viaRequest($driver, $callback);
        }

        /**
         * Get the user resolver callback.
         *
         * @return \Closure 
         * @static 
         */
        public static function userResolver()
        {
            /** @var \Illuminate\Auth\AuthManager $instance */
            return $instance->userResolver();
        }

        /**
         * Set the callback to be used to resolve users.
         *
         * @param \Closure $userResolver
         * @return \Illuminate\Auth\AuthManager 
         * @static 
         */
        public static function resolveUsersUsing($userResolver)
        {
            /** @var \Illuminate\Auth\AuthManager $instance */
            return $instance->resolveUsersUsing($userResolver);
        }

        /**
         * Register a custom driver creator Closure.
         *
         * @param string $driver
         * @param \Closure $callback
         * @return \Illuminate\Auth\AuthManager 
         * @static 
         */
        public static function extend($driver, $callback)
        {
            /** @var \Illuminate\Auth\AuthManager $instance */
            return $instance->extend($driver, $callback);
        }

        /**
         * Register a custom provider creator Closure.
         *
         * @param string $name
         * @param \Closure $callback
         * @return \Illuminate\Auth\AuthManager 
         * @static 
         */
        public static function provider($name, $callback)
        {
            /** @var \Illuminate\Auth\AuthManager $instance */
            return $instance->provider($name, $callback);
        }

        /**
         * Determines if any guards have already been resolved.
         *
         * @return bool 
         * @static 
         */
        public static function hasResolvedGuards()
        {
            /** @var \Illuminate\Auth\AuthManager $instance */
            return $instance->hasResolvedGuards();
        }

        /**
         * Forget all of the resolved guard instances.
         *
         * @return \Illuminate\Auth\AuthManager 
         * @static 
         */
        public static function forgetGuards()
        {
            /** @var \Illuminate\Auth\AuthManager $instance */
            return $instance->forgetGuards();
        }

        /**
         * Set the application instance used by the manager.
         *
         * @param \Illuminate\Contracts\Foundation\Application $app
         * @return \Illuminate\Auth\AuthManager 
         * @static 
         */
        public static function setApplication($app)
        {
            /** @var \Illuminate\Auth\AuthManager $instance */
            return $instance->setApplication($app);
        }

        /**
         * Create the user provider implementation for the driver.
         *
         * @param string|null $provider
         * @return \Illuminate\Contracts\Auth\UserProvider|null 
         * @throws \InvalidArgumentException
         * @static 
         */
        public static function createUserProvider($provider = null)
        {
            /** @var \Illuminate\Auth\AuthManager $instance */
            return $instance->createUserProvider($provider);
        }

        /**
         * Get the default user provider name.
         *
         * @return string 
         * @static 
         */
        public static function getDefaultUserProvider()
        {
            /** @var \Illuminate\Auth\AuthManager $instance */
            return $instance->getDefaultUserProvider();
        }

        /**
         * Get the currently authenticated user.
         *
         * @return \App\Models\User|null 
         * @static 
         */
        public static function user()
        {
            /** @var \Illuminate\Auth\SessionGuard $instance */
            return $instance->user();
        }

        /**
         * Get the ID for the currently authenticated user.
         *
         * @return int|string|null 
         * @static 
         */
        public static function id()
        {
            /** @var \Illuminate\Auth\SessionGuard $instance */
            return $instance->id();
        }

        /**
         * Log a user into the application without sessions or cookies.
         *
         * @param array $credentials
         * @return bool 
         * @static 
         */
        public static function once($credentials = [])
        {
            /** @var \Illuminate\Auth\SessionGuard $instance */
            return $instance->once($credentials);
        }

        /**
         * Log the given user ID into the application without sessions or cookies.
         *
         * @param mixed $id
         * @return \App\Models\User|false 
         * @static 
         */
        public static function onceUsingId($id)
        {
            /** @var \Illuminate\Auth\SessionGuard $instance */
            return $instance->onceUsingId($id);
        }

        /**
         * Validate a user's credentials.
         *
         * @param array $credentials
         * @return bool 
         * @static 
         */
        public static function validate($credentials = [])
        {
            /** @var \Illuminate\Auth\SessionGuard $instance */
            return $instance->validate($credentials);
        }

        /**
         * Attempt to authenticate using HTTP Basic Auth.
         *
         * @param string $field
         * @param array $extraConditions
         * @return \Symfony\Component\HttpFoundation\Response|null 
         * @throws \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException
         * @static 
         */
        public static function basic($field = 'email', $extraConditions = [])
        {
            /** @var \Illuminate\Auth\SessionGuard $instance */
            return $instance->basic($field, $extraConditions);
        }

        /**
         * Perform a stateless HTTP Basic login attempt.
         *
         * @param string $field
         * @param array $extraConditions
         * @return \Symfony\Component\HttpFoundation\Response|null 
         * @throws \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException
         * @static 
         */
        public static function onceBasic($field = 'email', $extraConditions = [])
        {
            /** @var \Illuminate\Auth\SessionGuard $instance */
            return $instance->onceBasic($field, $extraConditions);
        }

        /**
         * Attempt to authenticate a user using the given credentials.
         *
         * @param array $credentials
         * @param bool $remember
         * @return bool 
         * @static 
         */
        public static function attempt($credentials = [], $remember = false)
        {
            /** @var \Illuminate\Auth\SessionGuard $instance */
            return $instance->attempt($credentials, $remember);
        }

        /**
         * Attempt to authenticate a user with credentials and additional callbacks.
         *
         * @param array $credentials
         * @param array|callable|null $callbacks
         * @param bool $remember
         * @return bool 
         * @static 
         */
        public static function attemptWhen($credentials = [], $callbacks = null, $remember = false)
        {
            /** @var \Illuminate\Auth\SessionGuard $instance */
            return $instance->attemptWhen($credentials, $callbacks, $remember);
        }

        /**
         * Log the given user ID into the application.
         *
         * @param mixed $id
         * @param bool $remember
         * @return \App\Models\User|false 
         * @static 
         */
        public static function loginUsingId($id, $remember = false)
        {
            /** @var \Illuminate\Auth\SessionGuard $instance */
            return $instance->loginUsingId($id, $remember);
        }

        /**
         * Log a user into the application.
         *
         * @param \Illuminate\Contracts\Auth\Authenticatable $user
         * @param bool $remember
         * @return void 
         * @static 
         */
        public static function login($user, $remember = false)
        {
            /** @var \Illuminate\Auth\SessionGuard $instance */
            $instance->login($user, $remember);
        }

        /**
         * Log the user out of the application.
         *
         * @return void 
         * @static 
         */
        public static function logout()
        {
            /** @var \Illuminate\Auth\SessionGuard $instance */
            $instance->logout();
        }

        /**
         * Log the user out of the application on their current device only.
         * 
         * This method does not cycle the "remember" token.
         *
         * @return void 
         * @static 
         */
        public static function logoutCurrentDevice()
        {
            /** @var \Illuminate\Auth\SessionGuard $instance */
            $instance->logoutCurrentDevice();
        }

        /**
         * Invalidate other sessions for the current user.
         * 
         * The application must be using the AuthenticateSession middleware.
         *
         * @param string $password
         * @return \App\Models\User|null 
         * @throws \Illuminate\Auth\AuthenticationException
         * @static 
         */
        public static function logoutOtherDevices($password)
        {
            /** @var \Illuminate\Auth\SessionGuard $instance */
            return $instance->logoutOtherDevices($password);
        }

        /**
         * Register an authentication attempt event listener.
         *
         * @param mixed $callback
         * @return void 
         * @static 
         */
        public static function attempting($callback)
        {
            /** @var \Illuminate\Auth\SessionGuard $instance */
            $instance->attempting($callback);
        }

        /**
         * Get the last user we attempted to authenticate.
         *
         * @return \App\Models\User 
         * @static 
         */
        public static function getLastAttempted()
        {
            /** @var \Illuminate\Auth\SessionGuard $instance */
            return $instance->getLastAttempted();
        }

        /**
         * Get a unique identifier for the auth session value.
         *
         * @return string 
         * @static 
         */
        public static function getName()
        {
            /** @var \Illuminate\Auth\SessionGuard $instance */
            return $instance->getName();
        }

        /**
         * Get the name of the cookie used to store the "recaller".
         *
         * @return string 
         * @static 
         */
        public static function getRecallerName()
        {
            /** @var \Illuminate\Auth\SessionGuard $instance */
            return $instance->getRecallerName();
        }

        /**
         * Determine if the user was authenticated via "remember me" cookie.
         *
         * @return bool 
         * @static 
         */
        public static function viaRemember()
        {
            /** @var \Illuminate\Auth\SessionGuard $instance */
            return $instance->viaRemember();
        }

        /**
         * Set the number of minutes the remember me cookie should be valid for.
         *
         * @param int $minutes
         * @return \Illuminate\Auth\SessionGuard 
         * @static 
         */
        public static function setRememberDuration($minutes)
        {
            /** @var \Illuminate\Auth\SessionGuard $instance */
            return $instance->setRememberDuration($minutes);
        }

        /**
         * Get the cookie creator instance used by the guard.
         *
         * @return \Illuminate\Contracts\Cookie\QueueingFactory 
         * @throws \RuntimeException
         * @static 
         */
        public static function getCookieJar()
        {
            /** @var \Illuminate\Auth\SessionGuard $instance */
            return $instance->getCookieJar();
        }

        /**
         * Set the cookie creator instance used by the guard.
         *
         * @param \Illuminate\Contracts\Cookie\QueueingFactory $cookie
         * @return void 
         * @static 
         */
        public static function setCookieJar($cookie)
        {
            /** @var \Illuminate\Auth\SessionGuard $instance */
            $instance->setCookieJar($cookie);
        }

        /**
         * Get the event dispatcher instance.
         *
         * @return \Illuminate\Contracts\Events\Dispatcher 
         * @static 
         */
        public static function getDispatcher()
        {
            /** @var \Illuminate\Auth\SessionGuard $instance */
            return $instance->getDispatcher();
        }

        /**
         * Set the event dispatcher instance.
         *
         * @param \Illuminate\Contracts\Events\Dispatcher $events
         * @return void 
         * @static 
         */
        public static function setDispatcher($events)
        {
            /** @var \Illuminate\Auth\SessionGuard $instance */
            $instance->setDispatcher($events);
        }

        /**
         * Get the session store used by the guard.
         *
         * @return \Illuminate\Contracts\Session\Session 
         * @static 
         */
        public static function getSession()
        {
            /** @var \Illuminate\Auth\SessionGuard $instance */
            return $instance->getSession();
        }

        /**
         * Return the currently cached user.
         *
         * @return \App\Models\User|null 
         * @static 
         */
        public static function getUser()
        {
            /** @var \Illuminate\Auth\SessionGuard $instance */
            return $instance->getUser();
        }

        /**
         * Set the current user.
         *
         * @param \Illuminate\Contracts\Auth\Authenticatable $user
         * @return \Illuminate\Auth\SessionGuard 
         * @static 
         */
        public static function setUser($user)
        {
            /** @var \Illuminate\Auth\SessionGuard $instance */
            return $instance->setUser($user);
        }

        /**
         * Get the current request instance.
         *
         * @return \Symfony\Component\HttpFoundation\Request 
         * @static 
         */
        public static function getRequest()
        {
            /** @var \Illuminate\Auth\SessionGuard $instance */
            return $instance->getRequest();
        }

        /**
         * Set the current request instance.
         *
         * @param \Symfony\Component\HttpFoundation\Request $request
         * @return \Illuminate\Auth\SessionGuard 
         * @static 
         */
        public static function setRequest($request)
        {
            /** @var \Illuminate\Auth\SessionGuard $instance */
            return $instance->setRequest($request);
        }

        /**
         * Get the timebox instance used by the guard.
         *
         * @return \Illuminate\Support\Timebox 
         * @static 
         */
        public static function getTimebox()
        {
            /** @var \Illuminate\Auth\SessionGuard $instance */
            return $instance->getTimebox();
        }

        /**
         * Determine if the current user is authenticated. If not, throw an exception.
         *
         * @return \App\Models\User 
         * @throws \Illuminate\Auth\AuthenticationException
         * @static 
         */
        public static function authenticate()
        {
            /** @var \Illuminate\Auth\SessionGuard $instance */
            return $instance->authenticate();
        }

        /**
         * Determine if the guard has a user instance.
         *
         * @return bool 
         * @static 
         */
        public static function hasUser()
        {
            /** @var \Illuminate\Auth\SessionGuard $instance */
            return $instance->hasUser();
        }

        /**
         * Determine if the current user is authenticated.
         *
         * @return bool 
         * @static 
         */
        public static function check()
        {
            /** @var \Illuminate\Auth\SessionGuard $instance */
            return $instance->check();
        }

        /**
         * Determine if the current user is a guest.
         *
         * @return bool 
         * @static 
         */
        public static function guest()
        {
            /** @var \Illuminate\Auth\SessionGuard $instance */
            return $instance->guest();
        }

        /**
         * Forget the current user.
         *
         * @return \Illuminate\Auth\SessionGuard 
         * @static 
         */
        public static function forgetUser()
        {
            /** @var \Illuminate\Auth\SessionGuard $instance */
            return $instance->forgetUser();
        }

        /**
         * Get the user provider used by the guard.
         *
         * @return \Illuminate\Contracts\Auth\UserProvider 
         * @static 
         */
        public static function getProvider()
        {
            /** @var \Illuminate\Auth\SessionGuard $instance */
            return $instance->getProvider();
        }

        /**
         * Set the user provider used by the guard.
         *
         * @param \Illuminate\Contracts\Auth\UserProvider $provider
         * @return void 
         * @static 
         */
        public static function setProvider($provider)
        {
            /** @var \Illuminate\Auth\SessionGuard $instance */
            $instance->setProvider($provider);
        }

        /**
         * Register a custom macro.
         *
         * @param string $name
         * @param object|callable $macro
         * @param-closure-this static  $macro
         * @return void 
         * @static 
         */
        public static function macro($name, $macro)
        {
            \Illuminate\Auth\SessionGuard::macro($name, $macro);
        }

        /**
         * Mix another object into the class.
         *
         * @param object $mixin
         * @param bool $replace
         * @return void 
         * @throws \ReflectionException
         * @static 
         */
        public static function mixin($mixin, $replace = true)
        {
            \Illuminate\Auth\SessionGuard::mixin($mixin, $replace);
        }

        /**
         * Checks if macro is registered.
         *
         * @param string $name
         * @return bool 
         * @static 
         */
        public static function hasMacro($name)
        {
            return \Illuminate\Auth\SessionGuard::hasMacro($name);
        }

        /**
         * Flush the existing macros.
         *
         * @return void 
         * @static 
         */
        public static function flushMacros()
        {
            \Illuminate\Auth\SessionGuard::flushMacros();
        }

            }
    /**
     * 
     *
     * @see \Illuminate\View\Compilers\BladeCompiler
     */
    class Blade {
        /**
         * Compile the view at the given path.
         *
         * @param string|null $path
         * @return void 
         * @static 
         */
        public static function compile($path = null)
        {
            /** @var \Illuminate\View\Compilers\BladeCompiler $instance */
            $instance->compile($path);
        }

        /**
         * Get the path currently being compiled.
         *
         * @return string 
         * @static 
         */
        public static function getPath()
        {
            /** @var \Illuminate\View\Compilers\BladeCompiler $instance */
            return $instance->getPath();
        }

        /**
         * Set the path currently being compiled.
         *
         * @param string $path
         * @return void 
         * @static 
         */
        public static function setPath($path)
        {
            /** @var \Illuminate\View\Compilers\BladeCompiler $instance */
            $instance->setPath($path);
        }

        /**
         * Compile the given Blade template contents.
         *
         * @param string $value
         * @return string 
         * @static 
         */
        public static function compileString($value)
        {
            /** @var \Illuminate\View\Compilers\BladeCompiler $instance */
            return $instance->compileString($value);
        }

        /**
         * Evaluate and render a Blade string to HTML.
         *
         * @param string $string
         * @param array $data
         * @param bool $deleteCachedView
         * @return string 
         * @static 
         */
        public static function render($string, $data = [], $deleteCachedView = false)
        {
            return \Illuminate\View\Compilers\BladeCompiler::render($string, $data, $deleteCachedView);
        }

        /**
         * Render a component instance to HTML.
         *
         * @param \Illuminate\View\Component $component
         * @return string 
         * @static 
         */
        public static function renderComponent($component)
        {
            return \Illuminate\View\Compilers\BladeCompiler::renderComponent($component);
        }

        /**
         * Strip the parentheses from the given expression.
         *
         * @param string $expression
         * @return string 
         * @static 
         */
        public static function stripParentheses($expression)
        {
            /** @var \Illuminate\View\Compilers\BladeCompiler $instance */
            return $instance->stripParentheses($expression);
        }

        /**
         * Register a custom Blade compiler.
         *
         * @param callable $compiler
         * @return void 
         * @static 
         */
        public static function extend($compiler)
        {
            /** @var \Illuminate\View\Compilers\BladeCompiler $instance */
            $instance->extend($compiler);
        }

        /**
         * Get the extensions used by the compiler.
         *
         * @return array 
         * @static 
         */
        public static function getExtensions()
        {
            /** @var \Illuminate\View\Compilers\BladeCompiler $instance */
            return $instance->getExtensions();
        }

        /**
         * Register an "if" statement directive.
         *
         * @param string $name
         * @param callable $callback
         * @return void 
         * @static 
         */
        public static function if($name, $callback)
        {
            /** @var \Illuminate\View\Compilers\BladeCompiler $instance */
            $instance->if($name, $callback);
        }

        /**
         * Check the result of a condition.
         *
         * @param string $name
         * @param mixed $parameters
         * @return bool 
         * @static 
         */
        public static function check($name, ...$parameters)
        {
            /** @var \Illuminate\View\Compilers\BladeCompiler $instance */
            return $instance->check($name, ...$parameters);
        }

        /**
         * Register a class-based component alias directive.
         *
         * @param string $class
         * @param string|null $alias
         * @param string $prefix
         * @return void 
         * @static 
         */
        public static function component($class, $alias = null, $prefix = '')
        {
            /** @var \Illuminate\View\Compilers\BladeCompiler $instance */
            $instance->component($class, $alias, $prefix);
        }

        /**
         * Register an array of class-based components.
         *
         * @param array $components
         * @param string $prefix
         * @return void 
         * @static 
         */
        public static function components($components, $prefix = '')
        {
            /** @var \Illuminate\View\Compilers\BladeCompiler $instance */
            $instance->components($components, $prefix);
        }

        /**
         * Get the registered class component aliases.
         *
         * @return array 
         * @static 
         */
        public static function getClassComponentAliases()
        {
            /** @var \Illuminate\View\Compilers\BladeCompiler $instance */
            return $instance->getClassComponentAliases();
        }

        /**
         * Register a new anonymous component path.
         *
         * @param string $path
         * @param string|null $prefix
         * @return void 
         * @static 
         */
        public static function anonymousComponentPath($path, $prefix = null)
        {
            /** @var \Illuminate\View\Compilers\BladeCompiler $instance */
            $instance->anonymousComponentPath($path, $prefix);
        }

        /**
         * Register an anonymous component namespace.
         *
         * @param string $directory
         * @param string|null $prefix
         * @return void 
         * @static 
         */
        public static function anonymousComponentNamespace($directory, $prefix = null)
        {
            /** @var \Illuminate\View\Compilers\BladeCompiler $instance */
            $instance->anonymousComponentNamespace($directory, $prefix);
        }

        /**
         * Register a class-based component namespace.
         *
         * @param string $namespace
         * @param string $prefix
         * @return void 
         * @static 
         */
        public static function componentNamespace($namespace, $prefix)
        {
            /** @var \Illuminate\View\Compilers\BladeCompiler $instance */
            $instance->componentNamespace($namespace, $prefix);
        }

        /**
         * Get the registered anonymous component paths.
         *
         * @return array 
         * @static 
         */
        public static function getAnonymousComponentPaths()
        {
            /** @var \Illuminate\View\Compilers\BladeCompiler $instance */
            return $instance->getAnonymousComponentPaths();
        }

        /**
         * Get the registered anonymous component namespaces.
         *
         * @return array 
         * @static 
         */
        public static function getAnonymousComponentNamespaces()
        {
            /** @var \Illuminate\View\Compilers\BladeCompiler $instance */
            return $instance->getAnonymousComponentNamespaces();
        }

        /**
         * Get the registered class component namespaces.
         *
         * @return array 
         * @static 
         */
        public static function getClassComponentNamespaces()
        {
            /** @var \Illuminate\View\Compilers\BladeCompiler $instance */
            return $instance->getClassComponentNamespaces();
        }

        /**
         * Register a component alias directive.
         *
         * @param string $path
         * @param string|null $alias
         * @return void 
         * @static 
         */
        public static function aliasComponent($path, $alias = null)
        {
            /** @var \Illuminate\View\Compilers\BladeCompiler $instance */
            $instance->aliasComponent($path, $alias);
        }

        /**
         * Register an include alias directive.
         *
         * @param string $path
         * @param string|null $alias
         * @return void 
         * @static 
         */
        public static function include($path, $alias = null)
        {
            /** @var \Illuminate\View\Compilers\BladeCompiler $instance */
            $instance->include($path, $alias);
        }

        /**
         * Register an include alias directive.
         *
         * @param string $path
         * @param string|null $alias
         * @return void 
         * @static 
         */
        public static function aliasInclude($path, $alias = null)
        {
            /** @var \Illuminate\View\Compilers\BladeCompiler $instance */
            $instance->aliasInclude($path, $alias);
        }

        /**
         * Register a handler for custom directives, binding the handler to the compiler.
         *
         * @param string $name
         * @param callable $handler
         * @return void 
         * @throws \InvalidArgumentException
         * @static 
         */
        public static function bindDirective($name, $handler)
        {
            /** @var \Illuminate\View\Compilers\BladeCompiler $instance */
            $instance->bindDirective($name, $handler);
        }

        /**
         * Register a handler for custom directives.
         *
         * @param string $name
         * @param callable $handler
         * @param bool $bind
         * @return void 
         * @throws \InvalidArgumentException
         * @static 
         */
        public static function directive($name, $handler, $bind = false)
        {
            /** @var \Illuminate\View\Compilers\BladeCompiler $instance */
            $instance->directive($name, $handler, $bind);
        }

        /**
         * Get the list of custom directives.
         *
         * @return array 
         * @static 
         */
        public static function getCustomDirectives()
        {
            /** @var \Illuminate\View\Compilers\BladeCompiler $instance */
            return $instance->getCustomDirectives();
        }

        /**
         * Indicate that the following callable should be used to prepare strings for compilation.
         *
         * @param callable $callback
         * @return \Illuminate\View\Compilers\BladeCompiler 
         * @static 
         */
        public static function prepareStringsForCompilationUsing($callback)
        {
            /** @var \Illuminate\View\Compilers\BladeCompiler $instance */
            return $instance->prepareStringsForCompilationUsing($callback);
        }

        /**
         * Register a new precompiler.
         *
         * @param callable $precompiler
         * @return void 
         * @static 
         */
        public static function precompiler($precompiler)
        {
            /** @var \Illuminate\View\Compilers\BladeCompiler $instance */
            $instance->precompiler($precompiler);
        }

        /**
         * Execute the given callback using a custom echo format.
         *
         * @param string $format
         * @param callable $callback
         * @return string 
         * @static 
         */
        public static function usingEchoFormat($format, $callback)
        {
            /** @var \Illuminate\View\Compilers\BladeCompiler $instance */
            return $instance->usingEchoFormat($format, $callback);
        }

        /**
         * Set the echo format to be used by the compiler.
         *
         * @param string $format
         * @return void 
         * @static 
         */
        public static function setEchoFormat($format)
        {
            /** @var \Illuminate\View\Compilers\BladeCompiler $instance */
            $instance->setEchoFormat($format);
        }

        /**
         * Set the "echo" format to double encode entities.
         *
         * @return void 
         * @static 
         */
        public static function withDoubleEncoding()
        {
            /** @var \Illuminate\View\Compilers\BladeCompiler $instance */
            $instance->withDoubleEncoding();
        }

        /**
         * Set the "echo" format to not double encode entities.
         *
         * @return void 
         * @static 
         */
        public static function withoutDoubleEncoding()
        {
            /** @var \Illuminate\View\Compilers\BladeCompiler $instance */
            $instance->withoutDoubleEncoding();
        }

        /**
         * Indicate that component tags should not be compiled.
         *
         * @return void 
         * @static 
         */
        public static function withoutComponentTags()
        {
            /** @var \Illuminate\View\Compilers\BladeCompiler $instance */
            $instance->withoutComponentTags();
        }

        /**
         * Get the path to the compiled version of a view.
         *
         * @param string $path
         * @return string 
         * @static 
         */
        public static function getCompiledPath($path)
        {
            //Method inherited from \Illuminate\View\Compilers\Compiler 
            /** @var \Illuminate\View\Compilers\BladeCompiler $instance */
            return $instance->getCompiledPath($path);
        }

        /**
         * Determine if the view at the given path is expired.
         *
         * @param string $path
         * @return bool 
         * @throws \ErrorException
         * @static 
         */
        public static function isExpired($path)
        {
            //Method inherited from \Illuminate\View\Compilers\Compiler 
            /** @var \Illuminate\View\Compilers\BladeCompiler $instance */
            return $instance->isExpired($path);
        }

        /**
         * Get a new component hash for a component name.
         *
         * @param string $component
         * @return string 
         * @static 
         */
        public static function newComponentHash($component)
        {
            return \Illuminate\View\Compilers\BladeCompiler::newComponentHash($component);
        }

        /**
         * Compile a class component opening.
         *
         * @param string $component
         * @param string $alias
         * @param string $data
         * @param string $hash
         * @return string 
         * @static 
         */
        public static function compileClassComponentOpening($component, $alias, $data, $hash)
        {
            return \Illuminate\View\Compilers\BladeCompiler::compileClassComponentOpening($component, $alias, $data, $hash);
        }

        /**
         * Compile the end-component statements into valid PHP.
         *
         * @return string 
         * @static 
         */
        public static function compileEndComponentClass()
        {
            /** @var \Illuminate\View\Compilers\BladeCompiler $instance */
            return $instance->compileEndComponentClass();
        }

        /**
         * Sanitize the given component attribute value.
         *
         * @param mixed $value
         * @return mixed 
         * @static 
         */
        public static function sanitizeComponentAttribute($value)
        {
            return \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($value);
        }

        /**
         * Compile an end-once block into valid PHP.
         *
         * @return string 
         * @static 
         */
        public static function compileEndOnce()
        {
            /** @var \Illuminate\View\Compilers\BladeCompiler $instance */
            return $instance->compileEndOnce();
        }

        /**
         * Add a handler to be executed before echoing a given class.
         *
         * @param string|callable $class
         * @param callable|null $handler
         * @return void 
         * @static 
         */
        public static function stringable($class, $handler = null)
        {
            /** @var \Illuminate\View\Compilers\BladeCompiler $instance */
            $instance->stringable($class, $handler);
        }

        /**
         * Compile Blade echos into valid PHP.
         *
         * @param string $value
         * @return string 
         * @static 
         */
        public static function compileEchos($value)
        {
            /** @var \Illuminate\View\Compilers\BladeCompiler $instance */
            return $instance->compileEchos($value);
        }

        /**
         * Apply the echo handler for the value if it exists.
         *
         * @param string $value
         * @return string 
         * @static 
         */
        public static function applyEchoHandler($value)
        {
            /** @var \Illuminate\View\Compilers\BladeCompiler $instance */
            return $instance->applyEchoHandler($value);
        }

            }
    /**
     * 
     *
     * @method static mixed auth(\Illuminate\Http\Request $request)
     * @method static mixed validAuthenticationResponse(\Illuminate\Http\Request $request, mixed $result)
     * @method static void broadcast(array $channels, string $event, array $payload = [])
     * @method static array|null resolveAuthenticatedUser(\Illuminate\Http\Request $request)
     * @method static void resolveAuthenticatedUserUsing(\Closure $callback)
     * @method static \Illuminate\Broadcasting\Broadcasters\Broadcaster channel(\Illuminate\Contracts\Broadcasting\HasBroadcastChannel|string $channel, callable|string $callback, array $options = [])
     * @method static \Illuminate\Support\Collection getChannels()
     * @see \Illuminate\Broadcasting\BroadcastManager
     * @see \Illuminate\Broadcasting\Broadcasters\Broadcaster
     */
    class Broadcast {
        /**
         * Register the routes for handling broadcast channel authentication and sockets.
         *
         * @param array|null $attributes
         * @return void 
         * @static 
         */
        public static function routes($attributes = null)
        {
            /** @var \Illuminate\Broadcasting\BroadcastManager $instance */
            $instance->routes($attributes);
        }

        /**
         * Register the routes for handling broadcast user authentication.
         *
         * @param array|null $attributes
         * @return void 
         * @static 
         */
        public static function userRoutes($attributes = null)
        {
            /** @var \Illuminate\Broadcasting\BroadcastManager $instance */
            $instance->userRoutes($attributes);
        }

        /**
         * Register the routes for handling broadcast authentication and sockets.
         * 
         * Alias of "routes" method.
         *
         * @param array|null $attributes
         * @return void 
         * @static 
         */
        public static function channelRoutes($attributes = null)
        {
            /** @var \Illuminate\Broadcasting\BroadcastManager $instance */
            $instance->channelRoutes($attributes);
        }

        /**
         * Get the socket ID for the given request.
         *
         * @param \Illuminate\Http\Request|null $request
         * @return string|null 
         * @static 
         */
        public static function socket($request = null)
        {
            /** @var \Illuminate\Broadcasting\BroadcastManager $instance */
            return $instance->socket($request);
        }

        /**
         * Begin sending an anonymous broadcast to the given channels.
         *
         * @static 
         */
        public static function on($channels)
        {
            /** @var \Illuminate\Broadcasting\BroadcastManager $instance */
            return $instance->on($channels);
        }

        /**
         * Begin sending an anonymous broadcast to the given private channels.
         *
         * @static 
         */
        public static function private($channel)
        {
            /** @var \Illuminate\Broadcasting\BroadcastManager $instance */
            return $instance->private($channel);
        }

        /**
         * Begin sending an anonymous broadcast to the given presence channels.
         *
         * @static 
         */
        public static function presence($channel)
        {
            /** @var \Illuminate\Broadcasting\BroadcastManager $instance */
            return $instance->presence($channel);
        }

        /**
         * Begin broadcasting an event.
         *
         * @param mixed|null $event
         * @return \Illuminate\Broadcasting\PendingBroadcast 
         * @static 
         */
        public static function event($event = null)
        {
            /** @var \Illuminate\Broadcasting\BroadcastManager $instance */
            return $instance->event($event);
        }

        /**
         * Queue the given event for broadcast.
         *
         * @param mixed $event
         * @return void 
         * @static 
         */
        public static function queue($event)
        {
            /** @var \Illuminate\Broadcasting\BroadcastManager $instance */
            $instance->queue($event);
        }

        /**
         * Get a driver instance.
         *
         * @param string|null $driver
         * @return mixed 
         * @static 
         */
        public static function connection($driver = null)
        {
            /** @var \Illuminate\Broadcasting\BroadcastManager $instance */
            return $instance->connection($driver);
        }

        /**
         * Get a driver instance.
         *
         * @param string|null $name
         * @return mixed 
         * @static 
         */
        public static function driver($name = null)
        {
            /** @var \Illuminate\Broadcasting\BroadcastManager $instance */
            return $instance->driver($name);
        }

        /**
         * Get a Pusher instance for the given configuration.
         *
         * @param array $config
         * @return \Pusher\Pusher 
         * @static 
         */
        public static function pusher($config)
        {
            /** @var \Illuminate\Broadcasting\BroadcastManager $instance */
            return $instance->pusher($config);
        }

        /**
         * Get an Ably instance for the given configuration.
         *
         * @param array $config
         * @return \Ably\AblyRest 
         * @static 
         */
        public static function ably($config)
        {
            /** @var \Illuminate\Broadcasting\BroadcastManager $instance */
            return $instance->ably($config);
        }

        /**
         * Get the default driver name.
         *
         * @return string 
         * @static 
         */
        public static function getDefaultDriver()
        {
            /** @var \Illuminate\Broadcasting\BroadcastManager $instance */
            return $instance->getDefaultDriver();
        }

        /**
         * Set the default driver name.
         *
         * @param string $name
         * @return void 
         * @static 
         */
        public static function setDefaultDriver($name)
        {
            /** @var \Illuminate\Broadcasting\BroadcastManager $instance */
            $instance->setDefaultDriver($name);
        }

        /**
         * Disconnect the given disk and remove from local cache.
         *
         * @param string|null $name
         * @return void 
         * @static 
         */
        public static function purge($name = null)
        {
            /** @var \Illuminate\Broadcasting\BroadcastManager $instance */
            $instance->purge($name);
        }

        /**
         * Register a custom driver creator Closure.
         *
         * @param string $driver
         * @param \Closure $callback
         * @return \Illuminate\Broadcasting\BroadcastManager 
         * @static 
         */
        public static function extend($driver, $callback)
        {
            /** @var \Illuminate\Broadcasting\BroadcastManager $instance */
            return $instance->extend($driver, $callback);
        }

        /**
         * Get the application instance used by the manager.
         *
         * @return \Illuminate\Contracts\Foundation\Application 
         * @static 
         */
        public static function getApplication()
        {
            /** @var \Illuminate\Broadcasting\BroadcastManager $instance */
            return $instance->getApplication();
        }

        /**
         * Set the application instance used by the manager.
         *
         * @param \Illuminate\Contracts\Foundation\Application $app
         * @return \Illuminate\Broadcasting\BroadcastManager 
         * @static 
         */
        public static function setApplication($app)
        {
            /** @var \Illuminate\Broadcasting\BroadcastManager $instance */
            return $instance->setApplication($app);
        }

        /**
         * Forget all of the resolved driver instances.
         *
         * @return \Illuminate\Broadcasting\BroadcastManager 
         * @static 
         */
        public static function forgetDrivers()
        {
            /** @var \Illuminate\Broadcasting\BroadcastManager $instance */
            return $instance->forgetDrivers();
        }

            }
    /**
     * 
     *
     * @see \Illuminate\Bus\Dispatcher
     * @see \Illuminate\Support\Testing\Fakes\BusFake
     */
    class Bus {
        /**
         * Dispatch a command to its appropriate handler.
         *
         * @param mixed $command
         * @return mixed 
         * @static 
         */
        public static function dispatch($command)
        {
            /** @var \Illuminate\Bus\Dispatcher $instance */
            return $instance->dispatch($command);
        }

        /**
         * Dispatch a command to its appropriate handler in the current process.
         * 
         * Queueable jobs will be dispatched to the "sync" queue.
         *
         * @param mixed $command
         * @param mixed $handler
         * @return mixed 
         * @static 
         */
        public static function dispatchSync($command, $handler = null)
        {
            /** @var \Illuminate\Bus\Dispatcher $instance */
            return $instance->dispatchSync($command, $handler);
        }

        /**
         * Dispatch a command to its appropriate handler in the current process without using the synchronous queue.
         *
         * @param mixed $command
         * @param mixed $handler
         * @return mixed 
         * @static 
         */
        public static function dispatchNow($command, $handler = null)
        {
            /** @var \Illuminate\Bus\Dispatcher $instance */
            return $instance->dispatchNow($command, $handler);
        }

        /**
         * Attempt to find the batch with the given ID.
         *
         * @param string $batchId
         * @return \Illuminate\Bus\Batch|null 
         * @static 
         */
        public static function findBatch($batchId)
        {
            /** @var \Illuminate\Bus\Dispatcher $instance */
            return $instance->findBatch($batchId);
        }

        /**
         * Create a new batch of queueable jobs.
         *
         * @param \Illuminate\Support\Collection|array|mixed $jobs
         * @return \Illuminate\Bus\PendingBatch 
         * @static 
         */
        public static function batch($jobs)
        {
            /** @var \Illuminate\Bus\Dispatcher $instance */
            return $instance->batch($jobs);
        }

        /**
         * Create a new chain of queueable jobs.
         *
         * @param \Illuminate\Support\Collection|array $jobs
         * @return \Illuminate\Foundation\Bus\PendingChain 
         * @static 
         */
        public static function chain($jobs)
        {
            /** @var \Illuminate\Bus\Dispatcher $instance */
            return $instance->chain($jobs);
        }

        /**
         * Determine if the given command has a handler.
         *
         * @param mixed $command
         * @return bool 
         * @static 
         */
        public static function hasCommandHandler($command)
        {
            /** @var \Illuminate\Bus\Dispatcher $instance */
            return $instance->hasCommandHandler($command);
        }

        /**
         * Retrieve the handler for a command.
         *
         * @param mixed $command
         * @return bool|mixed 
         * @static 
         */
        public static function getCommandHandler($command)
        {
            /** @var \Illuminate\Bus\Dispatcher $instance */
            return $instance->getCommandHandler($command);
        }

        /**
         * Dispatch a command to its appropriate handler behind a queue.
         *
         * @param mixed $command
         * @return mixed 
         * @throws \RuntimeException
         * @static 
         */
        public static function dispatchToQueue($command)
        {
            /** @var \Illuminate\Bus\Dispatcher $instance */
            return $instance->dispatchToQueue($command);
        }

        /**
         * Dispatch a command to its appropriate handler after the current process.
         *
         * @param mixed $command
         * @param mixed $handler
         * @return void 
         * @static 
         */
        public static function dispatchAfterResponse($command, $handler = null)
        {
            /** @var \Illuminate\Bus\Dispatcher $instance */
            $instance->dispatchAfterResponse($command, $handler);
        }

        /**
         * Set the pipes through which commands should be piped before dispatching.
         *
         * @param array $pipes
         * @return \Illuminate\Bus\Dispatcher 
         * @static 
         */
        public static function pipeThrough($pipes)
        {
            /** @var \Illuminate\Bus\Dispatcher $instance */
            return $instance->pipeThrough($pipes);
        }

        /**
         * Map a command to a handler.
         *
         * @param array $map
         * @return \Illuminate\Bus\Dispatcher 
         * @static 
         */
        public static function map($map)
        {
            /** @var \Illuminate\Bus\Dispatcher $instance */
            return $instance->map($map);
        }

        /**
         * Allow dispatching after responses.
         *
         * @return \Illuminate\Bus\Dispatcher 
         * @static 
         */
        public static function withDispatchingAfterResponses()
        {
            /** @var \Illuminate\Bus\Dispatcher $instance */
            return $instance->withDispatchingAfterResponses();
        }

        /**
         * Disable dispatching after responses.
         *
         * @return \Illuminate\Bus\Dispatcher 
         * @static 
         */
        public static function withoutDispatchingAfterResponses()
        {
            /** @var \Illuminate\Bus\Dispatcher $instance */
            return $instance->withoutDispatchingAfterResponses();
        }

        /**
         * Specify the jobs that should be dispatched instead of faked.
         *
         * @param array|string $jobsToDispatch
         * @return \Illuminate\Support\Testing\Fakes\BusFake 
         * @static 
         */
        public static function except($jobsToDispatch)
        {
            /** @var \Illuminate\Support\Testing\Fakes\BusFake $instance */
            return $instance->except($jobsToDispatch);
        }

        /**
         * Assert if a job was dispatched based on a truth-test callback.
         *
         * @param string|\Closure $command
         * @param callable|int|null $callback
         * @return void 
         * @static 
         */
        public static function assertDispatched($command, $callback = null)
        {
            /** @var \Illuminate\Support\Testing\Fakes\BusFake $instance */
            $instance->assertDispatched($command, $callback);
        }

        /**
         * Assert if a job was pushed a number of times.
         *
         * @param string|\Closure $command
         * @param int $times
         * @return void 
         * @static 
         */
        public static function assertDispatchedTimes($command, $times = 1)
        {
            /** @var \Illuminate\Support\Testing\Fakes\BusFake $instance */
            $instance->assertDispatchedTimes($command, $times);
        }

        /**
         * Determine if a job was dispatched based on a truth-test callback.
         *
         * @param string|\Closure $command
         * @param callable|null $callback
         * @return void 
         * @static 
         */
        public static function assertNotDispatched($command, $callback = null)
        {
            /** @var \Illuminate\Support\Testing\Fakes\BusFake $instance */
            $instance->assertNotDispatched($command, $callback);
        }

        /**
         * Assert that no jobs were dispatched.
         *
         * @return void 
         * @static 
         */
        public static function assertNothingDispatched()
        {
            /** @var \Illuminate\Support\Testing\Fakes\BusFake $instance */
            $instance->assertNothingDispatched();
        }

        /**
         * Assert if a job was explicitly dispatched synchronously based on a truth-test callback.
         *
         * @param string|\Closure $command
         * @param callable|int|null $callback
         * @return void 
         * @static 
         */
        public static function assertDispatchedSync($command, $callback = null)
        {
            /** @var \Illuminate\Support\Testing\Fakes\BusFake $instance */
            $instance->assertDispatchedSync($command, $callback);
        }

        /**
         * Assert if a job was pushed synchronously a number of times.
         *
         * @param string|\Closure $command
         * @param int $times
         * @return void 
         * @static 
         */
        public static function assertDispatchedSyncTimes($command, $times = 1)
        {
            /** @var \Illuminate\Support\Testing\Fakes\BusFake $instance */
            $instance->assertDispatchedSyncTimes($command, $times);
        }

        /**
         * Determine if a job was dispatched based on a truth-test callback.
         *
         * @param string|\Closure $command
         * @param callable|null $callback
         * @return void 
         * @static 
         */
        public static function assertNotDispatchedSync($command, $callback = null)
        {
            /** @var \Illuminate\Support\Testing\Fakes\BusFake $instance */
            $instance->assertNotDispatchedSync($command, $callback);
        }

        /**
         * Assert if a job was dispatched after the response was sent based on a truth-test callback.
         *
         * @param string|\Closure $command
         * @param callable|int|null $callback
         * @return void 
         * @static 
         */
        public static function assertDispatchedAfterResponse($command, $callback = null)
        {
            /** @var \Illuminate\Support\Testing\Fakes\BusFake $instance */
            $instance->assertDispatchedAfterResponse($command, $callback);
        }

        /**
         * Assert if a job was pushed after the response was sent a number of times.
         *
         * @param string|\Closure $command
         * @param int $times
         * @return void 
         * @static 
         */
        public static function assertDispatchedAfterResponseTimes($command, $times = 1)
        {
            /** @var \Illuminate\Support\Testing\Fakes\BusFake $instance */
            $instance->assertDispatchedAfterResponseTimes($command, $times);
        }

        /**
         * Determine if a job was dispatched based on a truth-test callback.
         *
         * @param string|\Closure $command
         * @param callable|null $callback
         * @return void 
         * @static 
         */
        public static function assertNotDispatchedAfterResponse($command, $callback = null)
        {
            /** @var \Illuminate\Support\Testing\Fakes\BusFake $instance */
            $instance->assertNotDispatchedAfterResponse($command, $callback);
        }

        /**
         * Assert if a chain of jobs was dispatched.
         *
         * @param array $expectedChain
         * @return void 
         * @static 
         */
        public static function assertChained($expectedChain)
        {
            /** @var \Illuminate\Support\Testing\Fakes\BusFake $instance */
            $instance->assertChained($expectedChain);
        }

        /**
         * Assert no chained jobs was dispatched.
         *
         * @return void 
         * @static 
         */
        public static function assertNothingChained()
        {
            /** @var \Illuminate\Support\Testing\Fakes\BusFake $instance */
            $instance->assertNothingChained();
        }

        /**
         * Assert if a job was dispatched with an empty chain based on a truth-test callback.
         *
         * @param string|\Closure $command
         * @param callable|null $callback
         * @return void 
         * @static 
         */
        public static function assertDispatchedWithoutChain($command, $callback = null)
        {
            /** @var \Illuminate\Support\Testing\Fakes\BusFake $instance */
            $instance->assertDispatchedWithoutChain($command, $callback);
        }

        /**
         * Create a new assertion about a chained batch.
         *
         * @param \Closure $callback
         * @return \Illuminate\Support\Testing\Fakes\ChainedBatchTruthTest 
         * @static 
         */
        public static function chainedBatch($callback)
        {
            /** @var \Illuminate\Support\Testing\Fakes\BusFake $instance */
            return $instance->chainedBatch($callback);
        }

        /**
         * Assert if a batch was dispatched based on a truth-test callback.
         *
         * @param callable $callback
         * @return void 
         * @static 
         */
        public static function assertBatched($callback)
        {
            /** @var \Illuminate\Support\Testing\Fakes\BusFake $instance */
            $instance->assertBatched($callback);
        }

        /**
         * Assert the number of batches that have been dispatched.
         *
         * @param int $count
         * @return void 
         * @static 
         */
        public static function assertBatchCount($count)
        {
            /** @var \Illuminate\Support\Testing\Fakes\BusFake $instance */
            $instance->assertBatchCount($count);
        }

        /**
         * Assert that no batched jobs were dispatched.
         *
         * @return void 
         * @static 
         */
        public static function assertNothingBatched()
        {
            /** @var \Illuminate\Support\Testing\Fakes\BusFake $instance */
            $instance->assertNothingBatched();
        }

        /**
         * Assert that no jobs were dispatched, chained, or batched.
         *
         * @return void 
         * @static 
         */
        public static function assertNothingPlaced()
        {
            /** @var \Illuminate\Support\Testing\Fakes\BusFake $instance */
            $instance->assertNothingPlaced();
        }

        /**
         * Get all of the jobs matching a truth-test callback.
         *
         * @param string $command
         * @param callable|null $callback
         * @return \Illuminate\Support\Collection 
         * @static 
         */
        public static function dispatched($command, $callback = null)
        {
            /** @var \Illuminate\Support\Testing\Fakes\BusFake $instance */
            return $instance->dispatched($command, $callback);
        }

        /**
         * Get all of the jobs dispatched synchronously matching a truth-test callback.
         *
         * @param string $command
         * @param callable|null $callback
         * @return \Illuminate\Support\Collection 
         * @static 
         */
        public static function dispatchedSync($command, $callback = null)
        {
            /** @var \Illuminate\Support\Testing\Fakes\BusFake $instance */
            return $instance->dispatchedSync($command, $callback);
        }

        /**
         * Get all of the jobs dispatched after the response was sent matching a truth-test callback.
         *
         * @param string $command
         * @param callable|null $callback
         * @return \Illuminate\Support\Collection 
         * @static 
         */
        public static function dispatchedAfterResponse($command, $callback = null)
        {
            /** @var \Illuminate\Support\Testing\Fakes\BusFake $instance */
            return $instance->dispatchedAfterResponse($command, $callback);
        }

        /**
         * Get all of the pending batches matching a truth-test callback.
         *
         * @param callable $callback
         * @return \Illuminate\Support\Collection 
         * @static 
         */
        public static function batched($callback)
        {
            /** @var \Illuminate\Support\Testing\Fakes\BusFake $instance */
            return $instance->batched($callback);
        }

        /**
         * Determine if there are any stored commands for a given class.
         *
         * @param string $command
         * @return bool 
         * @static 
         */
        public static function hasDispatched($command)
        {
            /** @var \Illuminate\Support\Testing\Fakes\BusFake $instance */
            return $instance->hasDispatched($command);
        }

        /**
         * Determine if there are any stored commands for a given class.
         *
         * @param string $command
         * @return bool 
         * @static 
         */
        public static function hasDispatchedSync($command)
        {
            /** @var \Illuminate\Support\Testing\Fakes\BusFake $instance */
            return $instance->hasDispatchedSync($command);
        }

        /**
         * Determine if there are any stored commands for a given class.
         *
         * @param string $command
         * @return bool 
         * @static 
         */
        public static function hasDispatchedAfterResponse($command)
        {
            /** @var \Illuminate\Support\Testing\Fakes\BusFake $instance */
            return $instance->hasDispatchedAfterResponse($command);
        }

        /**
         * Dispatch an empty job batch for testing.
         *
         * @param string $name
         * @return \Illuminate\Bus\Batch 
         * @static 
         */
        public static function dispatchFakeBatch($name = '')
        {
            /** @var \Illuminate\Support\Testing\Fakes\BusFake $instance */
            return $instance->dispatchFakeBatch($name);
        }

        /**
         * Record the fake pending batch dispatch.
         *
         * @param \Illuminate\Bus\PendingBatch $pendingBatch
         * @return \Illuminate\Bus\Batch 
         * @static 
         */
        public static function recordPendingBatch($pendingBatch)
        {
            /** @var \Illuminate\Support\Testing\Fakes\BusFake $instance */
            return $instance->recordPendingBatch($pendingBatch);
        }

        /**
         * Specify if commands should be serialized and restored when being batched.
         *
         * @param bool $serializeAndRestore
         * @return \Illuminate\Support\Testing\Fakes\BusFake 
         * @static 
         */
        public static function serializeAndRestore($serializeAndRestore = true)
        {
            /** @var \Illuminate\Support\Testing\Fakes\BusFake $instance */
            return $instance->serializeAndRestore($serializeAndRestore);
        }

        /**
         * Get the batches that have been dispatched.
         *
         * @return array 
         * @static 
         */
        public static function dispatchedBatches()
        {
            /** @var \Illuminate\Support\Testing\Fakes\BusFake $instance */
            return $instance->dispatchedBatches();
        }

            }
    /**
     * 
     *
     * @see \Illuminate\Cache\CacheManager
     * @see \Illuminate\Cache\Repository
     */
    class Cache {
        /**
         * Get a cache store instance by name, wrapped in a repository.
         *
         * @param string|null $name
         * @return \Illuminate\Contracts\Cache\Repository 
         * @static 
         */
        public static function store($name = null)
        {
            /** @var \Illuminate\Cache\CacheManager $instance */
            return $instance->store($name);
        }

        /**
         * Get a cache driver instance.
         *
         * @param string|null $driver
         * @return \Illuminate\Contracts\Cache\Repository 
         * @static 
         */
        public static function driver($driver = null)
        {
            /** @var \Illuminate\Cache\CacheManager $instance */
            return $instance->driver($driver);
        }

        /**
         * Get a memoized cache driver instance.
         *
         * @param string|null $driver
         * @return \Illuminate\Contracts\Cache\Repository 
         * @static 
         */
        public static function memo($driver = null)
        {
            /** @var \Illuminate\Cache\CacheManager $instance */
            return $instance->memo($driver);
        }

        /**
         * Resolve the given store.
         *
         * @param string $name
         * @return \Illuminate\Contracts\Cache\Repository 
         * @throws \InvalidArgumentException
         * @static 
         */
        public static function resolve($name)
        {
            /** @var \Illuminate\Cache\CacheManager $instance */
            return $instance->resolve($name);
        }

        /**
         * Build a cache repository with the given configuration.
         *
         * @param array $config
         * @return \Illuminate\Cache\Repository 
         * @static 
         */
        public static function build($config)
        {
            /** @var \Illuminate\Cache\CacheManager $instance */
            return $instance->build($config);
        }

        /**
         * Create a new cache repository with the given implementation.
         *
         * @param \Illuminate\Contracts\Cache\Store $store
         * @param array $config
         * @return \Illuminate\Cache\Repository 
         * @static 
         */
        public static function repository($store, $config = [])
        {
            /** @var \Illuminate\Cache\CacheManager $instance */
            return $instance->repository($store, $config);
        }

        /**
         * Re-set the event dispatcher on all resolved cache repositories.
         *
         * @return void 
         * @static 
         */
        public static function refreshEventDispatcher()
        {
            /** @var \Illuminate\Cache\CacheManager $instance */
            $instance->refreshEventDispatcher();
        }

        /**
         * Get the default cache driver name.
         *
         * @return string 
         * @static 
         */
        public static function getDefaultDriver()
        {
            /** @var \Illuminate\Cache\CacheManager $instance */
            return $instance->getDefaultDriver();
        }

        /**
         * Set the default cache driver name.
         *
         * @param string $name
         * @return void 
         * @static 
         */
        public static function setDefaultDriver($name)
        {
            /** @var \Illuminate\Cache\CacheManager $instance */
            $instance->setDefaultDriver($name);
        }

        /**
         * Unset the given driver instances.
         *
         * @param array|string|null $name
         * @return \Illuminate\Cache\CacheManager 
         * @static 
         */
        public static function forgetDriver($name = null)
        {
            /** @var \Illuminate\Cache\CacheManager $instance */
            return $instance->forgetDriver($name);
        }

        /**
         * Disconnect the given driver and remove from local cache.
         *
         * @param string|null $name
         * @return void 
         * @static 
         */
        public static function purge($name = null)
        {
            /** @var \Illuminate\Cache\CacheManager $instance */
            $instance->purge($name);
        }

        /**
         * Register a custom driver creator Closure.
         *
         * @param string $driver
         * @param \Closure $callback
         * @param-closure-this $this  $callback
         * @return \Illuminate\Cache\CacheManager 
         * @static 
         */
        public static function extend($driver, $callback)
        {
            /** @var \Illuminate\Cache\CacheManager $instance */
            return $instance->extend($driver, $callback);
        }

        /**
         * Set the application instance used by the manager.
         *
         * @param \Illuminate\Contracts\Foundation\Application $app
         * @return \Illuminate\Cache\CacheManager 
         * @static 
         */
        public static function setApplication($app)
        {
            /** @var \Illuminate\Cache\CacheManager $instance */
            return $instance->setApplication($app);
        }

        /**
         * Determine if an item exists in the cache.
         *
         * @param array|string $key
         * @return bool 
         * @static 
         */
        public static function has($key)
        {
            /** @var \Illuminate\Cache\Repository $instance */
            return $instance->has($key);
        }

        /**
         * Determine if an item doesn't exist in the cache.
         *
         * @param string $key
         * @return bool 
         * @static 
         */
        public static function missing($key)
        {
            /** @var \Illuminate\Cache\Repository $instance */
            return $instance->missing($key);
        }

        /**
         * Retrieve an item from the cache by key.
         *
         * @param array|string $key
         * @param mixed $default
         * @return mixed 
         * @static 
         */
        public static function get($key, $default = null)
        {
            /** @var \Illuminate\Cache\Repository $instance */
            return $instance->get($key, $default);
        }

        /**
         * Retrieve multiple items from the cache by key.
         * 
         * Items not found in the cache will have a null value.
         *
         * @param array $keys
         * @return array 
         * @static 
         */
        public static function many($keys)
        {
            /** @var \Illuminate\Cache\Repository $instance */
            return $instance->many($keys);
        }

        /**
         * Obtains multiple cache items by their unique keys.
         *
         * @return iterable 
         * @param iterable<string> $keys A list of keys that can be obtained in a single operation.
         * @param mixed $default Default value to return for keys that do not exist.
         * @return iterable<string, mixed> A list of key => value pairs. Cache keys that do not exist or are stale will have $default as value.
         * @throws \Psr\SimpleCache\InvalidArgumentException
         *   MUST be thrown if $keys is neither an array nor a Traversable,
         *   or if any of the $keys are not a legal value.
         * @static 
         */
        public static function getMultiple($keys, $default = null)
        {
            /** @var \Illuminate\Cache\Repository $instance */
            return $instance->getMultiple($keys, $default);
        }

        /**
         * Retrieve an item from the cache and delete it.
         *
         * @param array|string $key
         * @param mixed $default
         * @return mixed 
         * @static 
         */
        public static function pull($key, $default = null)
        {
            /** @var \Illuminate\Cache\Repository $instance */
            return $instance->pull($key, $default);
        }

        /**
         * Store an item in the cache.
         *
         * @param array|string $key
         * @param mixed $value
         * @param \DateTimeInterface|\DateInterval|int|null $ttl
         * @return bool 
         * @static 
         */
        public static function put($key, $value, $ttl = null)
        {
            /** @var \Illuminate\Cache\Repository $instance */
            return $instance->put($key, $value, $ttl);
        }

        /**
         * Persists data in the cache, uniquely referenced by a key with an optional expiration TTL time.
         *
         * @return bool 
         * @param string $key The key of the item to store.
         * @param mixed $value The value of the item to store, must be serializable.
         * @param null|int|\DateInterval $ttl Optional. The TTL value of this item. If no value is sent and
         *                                      the driver supports TTL then the library may set a default value
         *                                      for it or let the driver take care of that.
         * @return bool True on success and false on failure.
         * @throws \Psr\SimpleCache\InvalidArgumentException
         *   MUST be thrown if the $key string is not a legal value.
         * @static 
         */
        public static function set($key, $value, $ttl = null)
        {
            /** @var \Illuminate\Cache\Repository $instance */
            return $instance->set($key, $value, $ttl);
        }

        /**
         * Store multiple items in the cache for a given number of seconds.
         *
         * @param array $values
         * @param \DateTimeInterface|\DateInterval|int|null $ttl
         * @return bool 
         * @static 
         */
        public static function putMany($values, $ttl = null)
        {
            /** @var \Illuminate\Cache\Repository $instance */
            return $instance->putMany($values, $ttl);
        }

        /**
         * Persists a set of key => value pairs in the cache, with an optional TTL.
         *
         * @return bool 
         * @param iterable $values A list of key => value pairs for a multiple-set operation.
         * @param null|int|\DateInterval $ttl Optional. The TTL value of this item. If no value is sent and
         *                                       the driver supports TTL then the library may set a default value
         *                                       for it or let the driver take care of that.
         * @return bool True on success and false on failure.
         * @throws \Psr\SimpleCache\InvalidArgumentException
         *   MUST be thrown if $values is neither an array nor a Traversable,
         *   or if any of the $values are not a legal value.
         * @static 
         */
        public static function setMultiple($values, $ttl = null)
        {
            /** @var \Illuminate\Cache\Repository $instance */
            return $instance->setMultiple($values, $ttl);
        }

        /**
         * Store an item in the cache if the key does not exist.
         *
         * @param string $key
         * @param mixed $value
         * @param \DateTimeInterface|\DateInterval|int|null $ttl
         * @return bool 
         * @static 
         */
        public static function add($key, $value, $ttl = null)
        {
            /** @var \Illuminate\Cache\Repository $instance */
            return $instance->add($key, $value, $ttl);
        }

        /**
         * Increment the value of an item in the cache.
         *
         * @param string $key
         * @param mixed $value
         * @return int|bool 
         * @static 
         */
        public static function increment($key, $value = 1)
        {
            /** @var \Illuminate\Cache\Repository $instance */
            return $instance->increment($key, $value);
        }

        /**
         * Decrement the value of an item in the cache.
         *
         * @param string $key
         * @param mixed $value
         * @return int|bool 
         * @static 
         */
        public static function decrement($key, $value = 1)
        {
            /** @var \Illuminate\Cache\Repository $instance */
            return $instance->decrement($key, $value);
        }

        /**
         * Store an item in the cache indefinitely.
         *
         * @param string $key
         * @param mixed $value
         * @return bool 
         * @static 
         */
        public static function forever($key, $value)
        {
            /** @var \Illuminate\Cache\Repository $instance */
            return $instance->forever($key, $value);
        }

        /**
         * Get an item from the cache, or execute the given Closure and store the result.
         *
         * @template TCacheValue
         * @param string $key
         * @param \Closure|\DateTimeInterface|\DateInterval|int|null $ttl
         * @param \Closure():  TCacheValue  $callback
         * @return TCacheValue 
         * @static 
         */
        public static function remember($key, $ttl, $callback)
        {
            /** @var \Illuminate\Cache\Repository $instance */
            return $instance->remember($key, $ttl, $callback);
        }

        /**
         * Get an item from the cache, or execute the given Closure and store the result forever.
         *
         * @template TCacheValue
         * @param string $key
         * @param \Closure():  TCacheValue  $callback
         * @return TCacheValue 
         * @static 
         */
        public static function sear($key, $callback)
        {
            /** @var \Illuminate\Cache\Repository $instance */
            return $instance->sear($key, $callback);
        }

        /**
         * Get an item from the cache, or execute the given Closure and store the result forever.
         *
         * @template TCacheValue
         * @param string $key
         * @param \Closure():  TCacheValue  $callback
         * @return TCacheValue 
         * @static 
         */
        public static function rememberForever($key, $callback)
        {
            /** @var \Illuminate\Cache\Repository $instance */
            return $instance->rememberForever($key, $callback);
        }

        /**
         * Retrieve an item from the cache by key, refreshing it in the background if it is stale.
         *
         * @template TCacheValue
         * @param string $key
         * @param array{ 0: \DateTimeInterface|\DateInterval|int, 1: \DateTimeInterface|\DateInterval|int } $ttl
         * @param (callable(): TCacheValue) $callback
         * @param array{ seconds?: int, owner?: string }|null $lock
         * @param bool $alwaysDefer
         * @return TCacheValue 
         * @static 
         */
        public static function flexible($key, $ttl, $callback, $lock = null, $alwaysDefer = false)
        {
            /** @var \Illuminate\Cache\Repository $instance */
            return $instance->flexible($key, $ttl, $callback, $lock, $alwaysDefer);
        }

        /**
         * Remove an item from the cache.
         *
         * @param string $key
         * @return bool 
         * @static 
         */
        public static function forget($key)
        {
            /** @var \Illuminate\Cache\Repository $instance */
            return $instance->forget($key);
        }

        /**
         * Delete an item from the cache by its unique key.
         *
         * @return bool 
         * @param string $key The unique cache key of the item to delete.
         * @return bool True if the item was successfully removed. False if there was an error.
         * @throws \Psr\SimpleCache\InvalidArgumentException
         *   MUST be thrown if the $key string is not a legal value.
         * @static 
         */
        public static function delete($key)
        {
            /** @var \Illuminate\Cache\Repository $instance */
            return $instance->delete($key);
        }

        /**
         * Deletes multiple cache items in a single operation.
         *
         * @return bool 
         * @param iterable<string> $keys A list of string-based keys to be deleted.
         * @return bool True if the items were successfully removed. False if there was an error.
         * @throws \Psr\SimpleCache\InvalidArgumentException
         *   MUST be thrown if $keys is neither an array nor a Traversable,
         *   or if any of the $keys are not a legal value.
         * @static 
         */
        public static function deleteMultiple($keys)
        {
            /** @var \Illuminate\Cache\Repository $instance */
            return $instance->deleteMultiple($keys);
        }

        /**
         * Wipes clean the entire cache's keys.
         *
         * @return bool 
         * @return bool True on success and false on failure.
         * @static 
         */
        public static function clear()
        {
            /** @var \Illuminate\Cache\Repository $instance */
            return $instance->clear();
        }

        /**
         * Begin executing a new tags operation if the store supports it.
         *
         * @param array|mixed $names
         * @return \Illuminate\Cache\TaggedCache 
         * @throws \BadMethodCallException
         * @static 
         */
        public static function tags($names)
        {
            /** @var \Illuminate\Cache\Repository $instance */
            return $instance->tags($names);
        }

        /**
         * Get the name of the cache store.
         *
         * @return string|null 
         * @static 
         */
        public static function getName()
        {
            /** @var \Illuminate\Cache\Repository $instance */
            return $instance->getName();
        }

        /**
         * Determine if the current store supports tags.
         *
         * @return bool 
         * @static 
         */
        public static function supportsTags()
        {
            /** @var \Illuminate\Cache\Repository $instance */
            return $instance->supportsTags();
        }

        /**
         * Get the default cache time.
         *
         * @return int|null 
         * @static 
         */
        public static function getDefaultCacheTime()
        {
            /** @var \Illuminate\Cache\Repository $instance */
            return $instance->getDefaultCacheTime();
        }

        /**
         * Set the default cache time in seconds.
         *
         * @param int|null $seconds
         * @return \Illuminate\Cache\Repository 
         * @static 
         */
        public static function setDefaultCacheTime($seconds)
        {
            /** @var \Illuminate\Cache\Repository $instance */
            return $instance->setDefaultCacheTime($seconds);
        }

        /**
         * Get the cache store implementation.
         *
         * @return \Illuminate\Contracts\Cache\Store 
         * @static 
         */
        public static function getStore()
        {
            /** @var \Illuminate\Cache\Repository $instance */
            return $instance->getStore();
        }

        /**
         * Set the cache store implementation.
         *
         * @param \Illuminate\Contracts\Cache\Store $store
         * @return static 
         * @static 
         */
        public static function setStore($store)
        {
            /** @var \Illuminate\Cache\Repository $instance */
            return $instance->setStore($store);
        }

        /**
         * Get the event dispatcher instance.
         *
         * @return \Illuminate\Contracts\Events\Dispatcher|null 
         * @static 
         */
        public static function getEventDispatcher()
        {
            /** @var \Illuminate\Cache\Repository $instance */
            return $instance->getEventDispatcher();
        }

        /**
         * Set the event dispatcher instance.
         *
         * @param \Illuminate\Contracts\Events\Dispatcher $events
         * @return void 
         * @static 
         */
        public static function setEventDispatcher($events)
        {
            /** @var \Illuminate\Cache\Repository $instance */
            $instance->setEventDispatcher($events);
        }

        /**
         * Determine if a cached value exists.
         *
         * @param string $key
         * @return bool 
         * @static 
         */
        public static function offsetExists($key)
        {
            /** @var \Illuminate\Cache\Repository $instance */
            return $instance->offsetExists($key);
        }

        /**
         * Retrieve an item from the cache by key.
         *
         * @param string $key
         * @return mixed 
         * @static 
         */
        public static function offsetGet($key)
        {
            /** @var \Illuminate\Cache\Repository $instance */
            return $instance->offsetGet($key);
        }

        /**
         * Store an item in the cache for the default time.
         *
         * @param string $key
         * @param mixed $value
         * @return void 
         * @static 
         */
        public static function offsetSet($key, $value)
        {
            /** @var \Illuminate\Cache\Repository $instance */
            $instance->offsetSet($key, $value);
        }

        /**
         * Remove an item from the cache.
         *
         * @param string $key
         * @return void 
         * @static 
         */
        public static function offsetUnset($key)
        {
            /** @var \Illuminate\Cache\Repository $instance */
            $instance->offsetUnset($key);
        }

        /**
         * Register a custom macro.
         *
         * @param string $name
         * @param object|callable $macro
         * @param-closure-this static  $macro
         * @return void 
         * @static 
         */
        public static function macro($name, $macro)
        {
            \Illuminate\Cache\Repository::macro($name, $macro);
        }

        /**
         * Mix another object into the class.
         *
         * @param object $mixin
         * @param bool $replace
         * @return void 
         * @throws \ReflectionException
         * @static 
         */
        public static function mixin($mixin, $replace = true)
        {
            \Illuminate\Cache\Repository::mixin($mixin, $replace);
        }

        /**
         * Checks if macro is registered.
         *
         * @param string $name
         * @return bool 
         * @static 
         */
        public static function hasMacro($name)
        {
            return \Illuminate\Cache\Repository::hasMacro($name);
        }

        /**
         * Flush the existing macros.
         *
         * @return void 
         * @static 
         */
        public static function flushMacros()
        {
            \Illuminate\Cache\Repository::flushMacros();
        }

        /**
         * Dynamically handle calls to the class.
         *
         * @param string $method
         * @param array $parameters
         * @return mixed 
         * @throws \BadMethodCallException
         * @static 
         */
        public static function macroCall($method, $parameters)
        {
            /** @var \Illuminate\Cache\Repository $instance */
            return $instance->macroCall($method, $parameters);
        }

        /**
         * Get a lock instance.
         *
         * @param string $name
         * @param int $seconds
         * @param string|null $owner
         * @return \Illuminate\Contracts\Cache\Lock 
         * @static 
         */
        public static function lock($name, $seconds = 0, $owner = null)
        {
            /** @var \Illuminate\Cache\DatabaseStore $instance */
            return $instance->lock($name, $seconds, $owner);
        }

        /**
         * Restore a lock instance using the owner identifier.
         *
         * @param string $name
         * @param string $owner
         * @return \Illuminate\Contracts\Cache\Lock 
         * @static 
         */
        public static function restoreLock($name, $owner)
        {
            /** @var \Illuminate\Cache\DatabaseStore $instance */
            return $instance->restoreLock($name, $owner);
        }

        /**
         * Remove an item from the cache if it is expired.
         *
         * @param string $key
         * @return bool 
         * @static 
         */
        public static function forgetIfExpired($key)
        {
            /** @var \Illuminate\Cache\DatabaseStore $instance */
            return $instance->forgetIfExpired($key);
        }

        /**
         * Remove all items from the cache.
         *
         * @return bool 
         * @static 
         */
        public static function flush()
        {
            /** @var \Illuminate\Cache\DatabaseStore $instance */
            return $instance->flush();
        }

        /**
         * Get the underlying database connection.
         *
         * @return \Illuminate\Database\MySqlConnection 
         * @static 
         */
        public static function getConnection()
        {
            /** @var \Illuminate\Cache\DatabaseStore $instance */
            return $instance->getConnection();
        }

        /**
         * Specify the name of the connection that should be used to manage locks.
         *
         * @param \Illuminate\Database\ConnectionInterface $connection
         * @return \Illuminate\Cache\DatabaseStore 
         * @static 
         */
        public static function setLockConnection($connection)
        {
            /** @var \Illuminate\Cache\DatabaseStore $instance */
            return $instance->setLockConnection($connection);
        }

        /**
         * Get the cache key prefix.
         *
         * @return string 
         * @static 
         */
        public static function getPrefix()
        {
            /** @var \Illuminate\Cache\DatabaseStore $instance */
            return $instance->getPrefix();
        }

        /**
         * Set the cache key prefix.
         *
         * @param string $prefix
         * @return void 
         * @static 
         */
        public static function setPrefix($prefix)
        {
            /** @var \Illuminate\Cache\DatabaseStore $instance */
            $instance->setPrefix($prefix);
        }

            }
    /**
     * 
     *
     * @method static array run(\Closure|array $tasks)
     * @method static \Illuminate\Support\Defer\DeferredCallback defer(\Closure|array $tasks)
     * @see \Illuminate\Concurrency\ConcurrencyManager
     */
    class Concurrency {
        /**
         * Get a driver instance by name.
         *
         * @param string|null $name
         * @return mixed 
         * @static 
         */
        public static function driver($name = null)
        {
            /** @var \Illuminate\Concurrency\ConcurrencyManager $instance */
            return $instance->driver($name);
        }

        /**
         * Create an instance of the process concurrency driver.
         *
         * @param array $config
         * @return \Illuminate\Concurrency\ProcessDriver 
         * @static 
         */
        public static function createProcessDriver($config)
        {
            /** @var \Illuminate\Concurrency\ConcurrencyManager $instance */
            return $instance->createProcessDriver($config);
        }

        /**
         * Create an instance of the fork concurrency driver.
         *
         * @param array $config
         * @return \Illuminate\Concurrency\ForkDriver 
         * @throws \RuntimeException
         * @static 
         */
        public static function createForkDriver($config)
        {
            /** @var \Illuminate\Concurrency\ConcurrencyManager $instance */
            return $instance->createForkDriver($config);
        }

        /**
         * Create an instance of the sync concurrency driver.
         *
         * @param array $config
         * @return \Illuminate\Concurrency\SyncDriver 
         * @static 
         */
        public static function createSyncDriver($config)
        {
            /** @var \Illuminate\Concurrency\ConcurrencyManager $instance */
            return $instance->createSyncDriver($config);
        }

        /**
         * Get the default instance name.
         *
         * @return string 
         * @static 
         */
        public static function getDefaultInstance()
        {
            /** @var \Illuminate\Concurrency\ConcurrencyManager $instance */
            return $instance->getDefaultInstance();
        }

        /**
         * Set the default instance name.
         *
         * @param string $name
         * @return void 
         * @static 
         */
        public static function setDefaultInstance($name)
        {
            /** @var \Illuminate\Concurrency\ConcurrencyManager $instance */
            $instance->setDefaultInstance($name);
        }

        /**
         * Get the instance specific configuration.
         *
         * @param string $name
         * @return array 
         * @static 
         */
        public static function getInstanceConfig($name)
        {
            /** @var \Illuminate\Concurrency\ConcurrencyManager $instance */
            return $instance->getInstanceConfig($name);
        }

        /**
         * Get an instance by name.
         *
         * @param string|null $name
         * @return mixed 
         * @static 
         */
        public static function instance($name = null)
        {
            //Method inherited from \Illuminate\Support\MultipleInstanceManager 
            /** @var \Illuminate\Concurrency\ConcurrencyManager $instance */
            return $instance->instance($name);
        }

        /**
         * Unset the given instances.
         *
         * @param array|string|null $name
         * @return \Illuminate\Concurrency\ConcurrencyManager 
         * @static 
         */
        public static function forgetInstance($name = null)
        {
            //Method inherited from \Illuminate\Support\MultipleInstanceManager 
            /** @var \Illuminate\Concurrency\ConcurrencyManager $instance */
            return $instance->forgetInstance($name);
        }

        /**
         * Disconnect the given instance and remove from local cache.
         *
         * @param string|null $name
         * @return void 
         * @static 
         */
        public static function purge($name = null)
        {
            //Method inherited from \Illuminate\Support\MultipleInstanceManager 
            /** @var \Illuminate\Concurrency\ConcurrencyManager $instance */
            $instance->purge($name);
        }

        /**
         * Register a custom instance creator Closure.
         *
         * @param string $name
         * @param \Closure $callback
         * @param-closure-this $this  $callback
         * @return \Illuminate\Concurrency\ConcurrencyManager 
         * @static 
         */
        public static function extend($name, $callback)
        {
            //Method inherited from \Illuminate\Support\MultipleInstanceManager 
            /** @var \Illuminate\Concurrency\ConcurrencyManager $instance */
            return $instance->extend($name, $callback);
        }

        /**
         * Set the application instance used by the manager.
         *
         * @param \Illuminate\Contracts\Foundation\Application $app
         * @return \Illuminate\Concurrency\ConcurrencyManager 
         * @static 
         */
        public static function setApplication($app)
        {
            //Method inherited from \Illuminate\Support\MultipleInstanceManager 
            /** @var \Illuminate\Concurrency\ConcurrencyManager $instance */
            return $instance->setApplication($app);
        }

            }
    /**
     * 
     *
     * @see \Illuminate\Config\Repository
     */
    class Config {
        /**
         * Determine if the given configuration value exists.
         *
         * @param string $key
         * @return bool 
         * @static 
         */
        public static function has($key)
        {
            /** @var \Illuminate\Config\Repository $instance */
            return $instance->has($key);
        }

        /**
         * Get the specified configuration value.
         *
         * @param array|string $key
         * @param mixed $default
         * @return mixed 
         * @static 
         */
        public static function get($key, $default = null)
        {
            /** @var \Illuminate\Config\Repository $instance */
            return $instance->get($key, $default);
        }

        /**
         * Get many configuration values.
         *
         * @param array<string|int,mixed> $keys
         * @return array<string,mixed> 
         * @static 
         */
        public static function getMany($keys)
        {
            /** @var \Illuminate\Config\Repository $instance */
            return $instance->getMany($keys);
        }

        /**
         * Get the specified string configuration value.
         *
         * @param string $key
         * @param (\Closure():(string|null))|string|null $default
         * @return string 
         * @static 
         */
        public static function string($key, $default = null)
        {
            /** @var \Illuminate\Config\Repository $instance */
            return $instance->string($key, $default);
        }

        /**
         * Get the specified integer configuration value.
         *
         * @param string $key
         * @param (\Closure():(int|null))|int|null $default
         * @return int 
         * @static 
         */
        public static function integer($key, $default = null)
        {
            /** @var \Illuminate\Config\Repository $instance */
            return $instance->integer($key, $default);
        }

        /**
         * Get the specified float configuration value.
         *
         * @param string $key
         * @param (\Closure():(float|null))|float|null $default
         * @return float 
         * @static 
         */
        public static function float($key, $default = null)
        {
            /** @var \Illuminate\Config\Repository $instance */
            return $instance->float($key, $default);
        }

        /**
         * Get the specified boolean configuration value.
         *
         * @param string $key
         * @param (\Closure():(bool|null))|bool|null $default
         * @return bool 
         * @static 
         */
        public static function boolean($key, $default = null)
        {
            /** @var \Illuminate\Config\Repository $instance */
            return $instance->boolean($key, $default);
        }

        /**
         * Get the specified array configuration value.
         *
         * @param string $key
         * @param (\Closure():(array<array-key, mixed>|null))|array<array-key, mixed>|null $default
         * @return array<array-key, mixed> 
         * @static 
         */
        public static function array($key, $default = null)
        {
            /** @var \Illuminate\Config\Repository $instance */
            return $instance->array($key, $default);
        }

        /**
         * Set a given configuration value.
         *
         * @param array|string $key
         * @param mixed $value
         * @return void 
         * @static 
         */
        public static function set($key, $value = null)
        {
            /** @var \Illuminate\Config\Repository $instance */
            $instance->set($key, $value);
        }

        /**
         * Prepend a value onto an array configuration value.
         *
         * @param string $key
         * @param mixed $value
         * @return void 
         * @static 
         */
        public static function prepend($key, $value)
        {
            /** @var \Illuminate\Config\Repository $instance */
            $instance->prepend($key, $value);
        }

        /**
         * Push a value onto an array configuration value.
         *
         * @param string $key
         * @param mixed $value
         * @return void 
         * @static 
         */
        public static function push($key, $value)
        {
            /** @var \Illuminate\Config\Repository $instance */
            $instance->push($key, $value);
        }

        /**
         * Get all of the configuration items for the application.
         *
         * @return array 
         * @static 
         */
        public static function all()
        {
            /** @var \Illuminate\Config\Repository $instance */
            return $instance->all();
        }

        /**
         * Determine if the given configuration option exists.
         *
         * @param string $key
         * @return bool 
         * @static 
         */
        public static function offsetExists($key)
        {
            /** @var \Illuminate\Config\Repository $instance */
            return $instance->offsetExists($key);
        }

        /**
         * Get a configuration option.
         *
         * @param string $key
         * @return mixed 
         * @static 
         */
        public static function offsetGet($key)
        {
            /** @var \Illuminate\Config\Repository $instance */
            return $instance->offsetGet($key);
        }

        /**
         * Set a configuration option.
         *
         * @param string $key
         * @param mixed $value
         * @return void 
         * @static 
         */
        public static function offsetSet($key, $value)
        {
            /** @var \Illuminate\Config\Repository $instance */
            $instance->offsetSet($key, $value);
        }

        /**
         * Unset a configuration option.
         *
         * @param string $key
         * @return void 
         * @static 
         */
        public static function offsetUnset($key)
        {
            /** @var \Illuminate\Config\Repository $instance */
            $instance->offsetUnset($key);
        }

        /**
         * Register a custom macro.
         *
         * @param string $name
         * @param object|callable $macro
         * @param-closure-this static  $macro
         * @return void 
         * @static 
         */
        public static function macro($name, $macro)
        {
            \Illuminate\Config\Repository::macro($name, $macro);
        }

        /**
         * Mix another object into the class.
         *
         * @param object $mixin
         * @param bool $replace
         * @return void 
         * @throws \ReflectionException
         * @static 
         */
        public static function mixin($mixin, $replace = true)
        {
            \Illuminate\Config\Repository::mixin($mixin, $replace);
        }

        /**
         * Checks if macro is registered.
         *
         * @param string $name
         * @return bool 
         * @static 
         */
        public static function hasMacro($name)
        {
            return \Illuminate\Config\Repository::hasMacro($name);
        }

        /**
         * Flush the existing macros.
         *
         * @return void 
         * @static 
         */
        public static function flushMacros()
        {
            \Illuminate\Config\Repository::flushMacros();
        }

            }
    /**
     * 
     *
     * @see \Illuminate\Log\Context\Repository
     */
    class Context {
        /**
         * Determine if the given key exists.
         *
         * @param string $key
         * @return bool 
         * @static 
         */
        public static function has($key)
        {
            /** @var \Illuminate\Log\Context\Repository $instance */
            return $instance->has($key);
        }

        /**
         * Determine if the given key is missing.
         *
         * @param string $key
         * @return bool 
         * @static 
         */
        public static function missing($key)
        {
            /** @var \Illuminate\Log\Context\Repository $instance */
            return $instance->missing($key);
        }

        /**
         * Determine if the given key exists within the hidden context data.
         *
         * @param string $key
         * @return bool 
         * @static 
         */
        public static function hasHidden($key)
        {
            /** @var \Illuminate\Log\Context\Repository $instance */
            return $instance->hasHidden($key);
        }

        /**
         * Determine if the given key is missing within the hidden context data.
         *
         * @param string $key
         * @return bool 
         * @static 
         */
        public static function missingHidden($key)
        {
            /** @var \Illuminate\Log\Context\Repository $instance */
            return $instance->missingHidden($key);
        }

        /**
         * Retrieve all the context data.
         *
         * @return array<string, mixed> 
         * @static 
         */
        public static function all()
        {
            /** @var \Illuminate\Log\Context\Repository $instance */
            return $instance->all();
        }

        /**
         * Retrieve all the hidden context data.
         *
         * @return array<string, mixed> 
         * @static 
         */
        public static function allHidden()
        {
            /** @var \Illuminate\Log\Context\Repository $instance */
            return $instance->allHidden();
        }

        /**
         * Retrieve the given key's value.
         *
         * @param string $key
         * @param mixed $default
         * @return mixed 
         * @static 
         */
        public static function get($key, $default = null)
        {
            /** @var \Illuminate\Log\Context\Repository $instance */
            return $instance->get($key, $default);
        }

        /**
         * Retrieve the given key's hidden value.
         *
         * @param string $key
         * @param mixed $default
         * @return mixed 
         * @static 
         */
        public static function getHidden($key, $default = null)
        {
            /** @var \Illuminate\Log\Context\Repository $instance */
            return $instance->getHidden($key, $default);
        }

        /**
         * Retrieve the given key's value and then forget it.
         *
         * @param string $key
         * @param mixed $default
         * @return mixed 
         * @static 
         */
        public static function pull($key, $default = null)
        {
            /** @var \Illuminate\Log\Context\Repository $instance */
            return $instance->pull($key, $default);
        }

        /**
         * Retrieve the given key's hidden value and then forget it.
         *
         * @param string $key
         * @param mixed $default
         * @return mixed 
         * @static 
         */
        public static function pullHidden($key, $default = null)
        {
            /** @var \Illuminate\Log\Context\Repository $instance */
            return $instance->pullHidden($key, $default);
        }

        /**
         * Retrieve only the values of the given keys.
         *
         * @param array<int, string> $keys
         * @return array<string, mixed> 
         * @static 
         */
        public static function only($keys)
        {
            /** @var \Illuminate\Log\Context\Repository $instance */
            return $instance->only($keys);
        }

        /**
         * Retrieve only the hidden values of the given keys.
         *
         * @param array<int, string> $keys
         * @return array<string, mixed> 
         * @static 
         */
        public static function onlyHidden($keys)
        {
            /** @var \Illuminate\Log\Context\Repository $instance */
            return $instance->onlyHidden($keys);
        }

        /**
         * Retrieve all values except those with the given keys.
         *
         * @param array<int, string> $keys
         * @return array<string, mixed> 
         * @static 
         */
        public static function except($keys)
        {
            /** @var \Illuminate\Log\Context\Repository $instance */
            return $instance->except($keys);
        }

        /**
         * Retrieve all hidden values except those with the given keys.
         *
         * @param array<int, string> $keys
         * @return array<string, mixed> 
         * @static 
         */
        public static function exceptHidden($keys)
        {
            /** @var \Illuminate\Log\Context\Repository $instance */
            return $instance->exceptHidden($keys);
        }

        /**
         * Add a context value.
         *
         * @param string|array<string, mixed> $key
         * @param mixed $value
         * @return \Illuminate\Log\Context\Repository 
         * @static 
         */
        public static function add($key, $value = null)
        {
            /** @var \Illuminate\Log\Context\Repository $instance */
            return $instance->add($key, $value);
        }

        /**
         * Add a hidden context value.
         *
         * @param string|array<string, mixed> $key
         * @param mixed $value
         * @return \Illuminate\Log\Context\Repository 
         * @static 
         */
        public static function addHidden($key, $value = null)
        {
            /** @var \Illuminate\Log\Context\Repository $instance */
            return $instance->addHidden($key, $value);
        }

        /**
         * Forget the given context key.
         *
         * @param string|array<int, string> $key
         * @return \Illuminate\Log\Context\Repository 
         * @static 
         */
        public static function forget($key)
        {
            /** @var \Illuminate\Log\Context\Repository $instance */
            return $instance->forget($key);
        }

        /**
         * Forget the given hidden context key.
         *
         * @param string|array<int, string> $key
         * @return \Illuminate\Log\Context\Repository 
         * @static 
         */
        public static function forgetHidden($key)
        {
            /** @var \Illuminate\Log\Context\Repository $instance */
            return $instance->forgetHidden($key);
        }

        /**
         * Add a context value if it does not exist yet.
         *
         * @param string $key
         * @param mixed $value
         * @return \Illuminate\Log\Context\Repository 
         * @static 
         */
        public static function addIf($key, $value)
        {
            /** @var \Illuminate\Log\Context\Repository $instance */
            return $instance->addIf($key, $value);
        }

        /**
         * Add a hidden context value if it does not exist yet.
         *
         * @param string $key
         * @param mixed $value
         * @return \Illuminate\Log\Context\Repository 
         * @static 
         */
        public static function addHiddenIf($key, $value)
        {
            /** @var \Illuminate\Log\Context\Repository $instance */
            return $instance->addHiddenIf($key, $value);
        }

        /**
         * Push the given values onto the key's stack.
         *
         * @param string $key
         * @param mixed $values
         * @return \Illuminate\Log\Context\Repository 
         * @throws \RuntimeException
         * @static 
         */
        public static function push($key, ...$values)
        {
            /** @var \Illuminate\Log\Context\Repository $instance */
            return $instance->push($key, ...$values);
        }

        /**
         * Pop the latest value from the key's stack.
         *
         * @param string $key
         * @return mixed 
         * @throws \RuntimeException
         * @static 
         */
        public static function pop($key)
        {
            /** @var \Illuminate\Log\Context\Repository $instance */
            return $instance->pop($key);
        }

        /**
         * Push the given hidden values onto the key's stack.
         *
         * @param string $key
         * @param mixed $values
         * @return \Illuminate\Log\Context\Repository 
         * @throws \RuntimeException
         * @static 
         */
        public static function pushHidden($key, ...$values)
        {
            /** @var \Illuminate\Log\Context\Repository $instance */
            return $instance->pushHidden($key, ...$values);
        }

        /**
         * Pop the latest hidden value from the key's stack.
         *
         * @param string $key
         * @return mixed 
         * @throws \RuntimeException
         * @static 
         */
        public static function popHidden($key)
        {
            /** @var \Illuminate\Log\Context\Repository $instance */
            return $instance->popHidden($key);
        }

        /**
         * Increment a context counter.
         *
         * @param string $key
         * @param int $amount
         * @return \Illuminate\Log\Context\Repository 
         * @static 
         */
        public static function increment($key, $amount = 1)
        {
            /** @var \Illuminate\Log\Context\Repository $instance */
            return $instance->increment($key, $amount);
        }

        /**
         * Decrement a context counter.
         *
         * @param string $key
         * @param int $amount
         * @return \Illuminate\Log\Context\Repository 
         * @static 
         */
        public static function decrement($key, $amount = 1)
        {
            /** @var \Illuminate\Log\Context\Repository $instance */
            return $instance->decrement($key, $amount);
        }

        /**
         * Determine if the given value is in the given stack.
         *
         * @param string $key
         * @param mixed $value
         * @param bool $strict
         * @return bool 
         * @throws \RuntimeException
         * @static 
         */
        public static function stackContains($key, $value, $strict = false)
        {
            /** @var \Illuminate\Log\Context\Repository $instance */
            return $instance->stackContains($key, $value, $strict);
        }

        /**
         * Determine if the given value is in the given hidden stack.
         *
         * @param string $key
         * @param mixed $value
         * @param bool $strict
         * @return bool 
         * @throws \RuntimeException
         * @static 
         */
        public static function hiddenStackContains($key, $value, $strict = false)
        {
            /** @var \Illuminate\Log\Context\Repository $instance */
            return $instance->hiddenStackContains($key, $value, $strict);
        }

        /**
         * Run the callback function with the given context values and restore the original context state when complete.
         *
         * @param callable $callback
         * @param array<string, mixed> $data
         * @param array<string, mixed> $hidden
         * @return mixed 
         * @static 
         */
        public static function scope($callback, $data = [], $hidden = [])
        {
            /** @var \Illuminate\Log\Context\Repository $instance */
            return $instance->scope($callback, $data, $hidden);
        }

        /**
         * Determine if the repository is empty.
         *
         * @return bool 
         * @static 
         */
        public static function isEmpty()
        {
            /** @var \Illuminate\Log\Context\Repository $instance */
            return $instance->isEmpty();
        }

        /**
         * Execute the given callback when context is about to be dehydrated.
         *
         * @param callable $callback
         * @return \Illuminate\Log\Context\Repository 
         * @static 
         */
        public static function dehydrating($callback)
        {
            /** @var \Illuminate\Log\Context\Repository $instance */
            return $instance->dehydrating($callback);
        }

        /**
         * Execute the given callback when context has been hydrated.
         *
         * @param callable $callback
         * @return \Illuminate\Log\Context\Repository 
         * @static 
         */
        public static function hydrated($callback)
        {
            /** @var \Illuminate\Log\Context\Repository $instance */
            return $instance->hydrated($callback);
        }

        /**
         * Handle unserialize exceptions using the given callback.
         *
         * @param callable|null $callback
         * @return static 
         * @static 
         */
        public static function handleUnserializeExceptionsUsing($callback)
        {
            /** @var \Illuminate\Log\Context\Repository $instance */
            return $instance->handleUnserializeExceptionsUsing($callback);
        }

        /**
         * Flush all context data.
         *
         * @return \Illuminate\Log\Context\Repository 
         * @static 
         */
        public static function flush()
        {
            /** @var \Illuminate\Log\Context\Repository $instance */
            return $instance->flush();
        }

        /**
         * Dehydrate the context data.
         *
         * @internal 
         * @return \Illuminate\Log\Context\?array 
         * @static 
         */
        public static function dehydrate()
        {
            /** @var \Illuminate\Log\Context\Repository $instance */
            return $instance->dehydrate();
        }

        /**
         * Hydrate the context instance.
         *
         * @internal 
         * @param \Illuminate\Log\Context\?array $context
         * @return \Illuminate\Log\Context\Repository 
         * @throws \RuntimeException
         * @static 
         */
        public static function hydrate($context)
        {
            /** @var \Illuminate\Log\Context\Repository $instance */
            return $instance->hydrate($context);
        }

        /**
         * Apply the callback if the given "value" is (or resolves to) truthy.
         *
         * @template TWhenParameter
         * @template TWhenReturnType
         * @param (\Closure($this): TWhenParameter)|TWhenParameter|null $value
         * @param (callable($this, TWhenParameter): TWhenReturnType)|null $callback
         * @param (callable($this, TWhenParameter): TWhenReturnType)|null $default
         * @return $this|TWhenReturnType 
         * @static 
         */
        public static function when($value = null, $callback = null, $default = null)
        {
            /** @var \Illuminate\Log\Context\Repository $instance */
            return $instance->when($value, $callback, $default);
        }

        /**
         * Apply the callback if the given "value" is (or resolves to) falsy.
         *
         * @template TUnlessParameter
         * @template TUnlessReturnType
         * @param (\Closure($this): TUnlessParameter)|TUnlessParameter|null $value
         * @param (callable($this, TUnlessParameter): TUnlessReturnType)|null $callback
         * @param (callable($this, TUnlessParameter): TUnlessReturnType)|null $default
         * @return $this|TUnlessReturnType 
         * @static 
         */
        public static function unless($value = null, $callback = null, $default = null)
        {
            /** @var \Illuminate\Log\Context\Repository $instance */
            return $instance->unless($value, $callback, $default);
        }

        /**
         * Register a custom macro.
         *
         * @param string $name
         * @param object|callable $macro
         * @param-closure-this static  $macro
         * @return void 
         * @static 
         */
        public static function macro($name, $macro)
        {
            \Illuminate\Log\Context\Repository::macro($name, $macro);
        }

        /**
         * Mix another object into the class.
         *
         * @param object $mixin
         * @param bool $replace
         * @return void 
         * @throws \ReflectionException
         * @static 
         */
        public static function mixin($mixin, $replace = true)
        {
            \Illuminate\Log\Context\Repository::mixin($mixin, $replace);
        }

        /**
         * Checks if macro is registered.
         *
         * @param string $name
         * @return bool 
         * @static 
         */
        public static function hasMacro($name)
        {
            return \Illuminate\Log\Context\Repository::hasMacro($name);
        }

        /**
         * Flush the existing macros.
         *
         * @return void 
         * @static 
         */
        public static function flushMacros()
        {
            \Illuminate\Log\Context\Repository::flushMacros();
        }

        /**
         * Restore the model from the model identifier instance.
         *
         * @param \Illuminate\Contracts\Database\ModelIdentifier $value
         * @return \Illuminate\Database\Eloquent\Model 
         * @static 
         */
        public static function restoreModel($value)
        {
            /** @var \Illuminate\Log\Context\Repository $instance */
            return $instance->restoreModel($value);
        }

            }
    /**
     * 
     *
     * @see \Illuminate\Cookie\CookieJar
     */
    class Cookie {
        /**
         * Create a new cookie instance.
         *
         * @param string $name
         * @param string $value
         * @param int $minutes
         * @param string|null $path
         * @param string|null $domain
         * @param bool|null $secure
         * @param bool $httpOnly
         * @param bool $raw
         * @param string|null $sameSite
         * @return \Symfony\Component\HttpFoundation\Cookie 
         * @static 
         */
        public static function make($name, $value, $minutes = 0, $path = null, $domain = null, $secure = null, $httpOnly = true, $raw = false, $sameSite = null)
        {
            /** @var \Illuminate\Cookie\CookieJar $instance */
            return $instance->make($name, $value, $minutes, $path, $domain, $secure, $httpOnly, $raw, $sameSite);
        }

        /**
         * Create a cookie that lasts "forever" (400 days).
         *
         * @param string $name
         * @param string $value
         * @param string|null $path
         * @param string|null $domain
         * @param bool|null $secure
         * @param bool $httpOnly
         * @param bool $raw
         * @param string|null $sameSite
         * @return \Symfony\Component\HttpFoundation\Cookie 
         * @static 
         */
        public static function forever($name, $value, $path = null, $domain = null, $secure = null, $httpOnly = true, $raw = false, $sameSite = null)
        {
            /** @var \Illuminate\Cookie\CookieJar $instance */
            return $instance->forever($name, $value, $path, $domain, $secure, $httpOnly, $raw, $sameSite);
        }

        /**
         * Expire the given cookie.
         *
         * @param string $name
         * @param string|null $path
         * @param string|null $domain
         * @return \Symfony\Component\HttpFoundation\Cookie 
         * @static 
         */
        public static function forget($name, $path = null, $domain = null)
        {
            /** @var \Illuminate\Cookie\CookieJar $instance */
            return $instance->forget($name, $path, $domain);
        }

        /**
         * Determine if a cookie has been queued.
         *
         * @param string $key
         * @param string|null $path
         * @return bool 
         * @static 
         */
        public static function hasQueued($key, $path = null)
        {
            /** @var \Illuminate\Cookie\CookieJar $instance */
            return $instance->hasQueued($key, $path);
        }

        /**
         * Get a queued cookie instance.
         *
         * @param string $key
         * @param mixed $default
         * @param string|null $path
         * @return \Symfony\Component\HttpFoundation\Cookie|null 
         * @static 
         */
        public static function queued($key, $default = null, $path = null)
        {
            /** @var \Illuminate\Cookie\CookieJar $instance */
            return $instance->queued($key, $default, $path);
        }

        /**
         * Queue a cookie to send with the next response.
         *
         * @param mixed $parameters
         * @return void 
         * @static 
         */
        public static function queue(...$parameters)
        {
            /** @var \Illuminate\Cookie\CookieJar $instance */
            $instance->queue(...$parameters);
        }

        /**
         * Queue a cookie to expire with the next response.
         *
         * @param string $name
         * @param string|null $path
         * @param string|null $domain
         * @return void 
         * @static 
         */
        public static function expire($name, $path = null, $domain = null)
        {
            /** @var \Illuminate\Cookie\CookieJar $instance */
            $instance->expire($name, $path, $domain);
        }

        /**
         * Remove a cookie from the queue.
         *
         * @param string $name
         * @param string|null $path
         * @return void 
         * @static 
         */
        public static function unqueue($name, $path = null)
        {
            /** @var \Illuminate\Cookie\CookieJar $instance */
            $instance->unqueue($name, $path);
        }

        /**
         * Set the default path and domain for the jar.
         *
         * @param string $path
         * @param string|null $domain
         * @param bool|null $secure
         * @param string|null $sameSite
         * @return \Illuminate\Cookie\CookieJar 
         * @static 
         */
        public static function setDefaultPathAndDomain($path, $domain, $secure = false, $sameSite = null)
        {
            /** @var \Illuminate\Cookie\CookieJar $instance */
            return $instance->setDefaultPathAndDomain($path, $domain, $secure, $sameSite);
        }

        /**
         * Get the cookies which have been queued for the next request.
         *
         * @return \Symfony\Component\HttpFoundation\Cookie[] 
         * @static 
         */
        public static function getQueuedCookies()
        {
            /** @var \Illuminate\Cookie\CookieJar $instance */
            return $instance->getQueuedCookies();
        }

        /**
         * Flush the cookies which have been queued for the 