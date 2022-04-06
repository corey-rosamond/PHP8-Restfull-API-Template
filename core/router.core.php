<?php
declare(strict_types = 1);
namespace App\Core
{
    require_once("./enums/httpType.enum.php");
    require_once("./attributes/route.attribute.php");
    require_once("./attributes/get.attribute.php");
    require_once("./attributes/post.attribute.php");
    require_once("./attributes/put.attribute.php");
    require_once("./attributes/delete.attribute.php");
    require_once("./exceptions/routeNotFound.exception.php");

    use Error;
    use WeakMap;
    use Exception;
    use ReflectionClass;
    use ReflectionException;
    use ReflectionAttribute;
    use App\Enums\HTTPType;
    use App\Attributes\Route;
    use App\Exceptions\RouteNotFoundException;


    /**
     * This object handles routing of requests to enable an easy implementable restfull api structure.
     */
    final class Router
    {
        /** @var Router|null This is where we hold the current instance of the router or null if it has not been created yet */
        private static ?Router $instance = null;

        /**
         * @var WeakMap This is a collection of all the registered routes.
         * @todo check if this can be cached somehow and only updated when the file system changes.
         */
        private WeakMap $routes;

        /**
         * This is the routers "__construct" method. This will test if there is
         * an active instance of the router. If one is found it will simply return it.
         * if one is not found one will be created then returned.
         *
         * @return Router
         */
        final public static function create(): Router
        {
            if (static::$instance === null) {
                static::$instance = new static();
                static::$instance->routes = new WeakMap();
            }
            return static::$instance;
        }

        /**
         * @throws ReflectionException
         * @todo find a way to lower the cyclomatic complexity of this method
         */
        public function register(...$controllers): self
        {
            foreach($controllers as $controller)
            {
                $reflectionController = new ReflectionClass($controller);
                foreach($reflectionController->getMethods() as $method)
                {
                    $attributes = $method->getAttributes(
                        Route::class,
                        ReflectionAttribute::IS_INSTANCEOF
                    );
                    foreach($attributes as $attribute) {
                        $route = $attribute->newInstance();
                        $this->registerRoute(
                            $route->method,
                            $route->path,
                            [
                                $controller,
                                $method->getName()
                            ]
                        );
                    }
                }
            }
            return $this;
        }

        private function registerRoute(HTTPType $requestMethod, string $route, callable|array $action)
        {
            try {
                $this->routes[$requestMethod][$route] = $action;
            } catch (Error $exception)
            {
                $this->routes[$requestMethod] = [];
                $this->routes[$requestMethod][$route] = $action;
            }
        }

        public static function get(string $route, callable|array $action): self
        {
            $instance = self::create();
            $instance->registerRoute(
                HTTPType::Get,
                $route,
                $action
            );
            return $instance;
        }

        public static function post(string $route, callable|array $action): self
        {
            $instance = self::create();
            $instance->registerRoute(
                HTTPType::Post,
                $route,
                $action
            );
            return $instance;
        }

        public static function put(string $route, callable|array $action): self
        {
            $instance = self::create();
            $instance->registerRoute(
                HTTPType::Put,
                $route,
                $action
            );
            return $instance;
        }

        public static function head(string $route, callable|array $action): self
        {
            $instance = self::create();
            $instance->registerRoute(
                HTTPType::Head,
                $route,
                $action
            );
            return $instance;
        }

        public static function delete(string $route, callable|array $action): self
        {
            $instance = self::create();
            $instance->registerRoute(
                HTTPType::Delete,
                $route,
                $action
            );
            return $instance;
        }

        /**
         * @throws Exception
         */
        public function resolve(string $requestMethod, string $requestURI)
        {
            // Remove any get vars in the url.
            $route = explode('?', $requestURI)[0];
            $requestMethod = HTTPType::from(strtolower($requestMethod));
            $callback = $this->routes[$requestMethod][$route] ?? null;

            // Replace this with a try/catch
            if(!$callback)
            {
                throw new RouteNotFoundException();
            }

            if(is_callable($callback))
            {
                return call_user_func($callback, $_GET);
            }

            [$callbackClass, $callbackMethod] = $callback;

            if(class_exists($callbackClass))
            {
                $callbackClass = new $callbackClass();
                if(method_exists($callbackClass, $callbackMethod))
                {
                    return call_user_func_array([
                        $callbackClass,
                        $callbackMethod,
                    ], [$_GET]);
                }
            }

            throw new RouteNotFoundException();
        }

        /**
         * Construct is set to private to disable its use please refer to the static
         * create method.
         *
         * @throws Exception Inform the programmer to use create rather than construct
         */
        private function __construct()
        {}

        /**
         * We disable cloning to prevent the creation of a second router object.
         *
         * @throws Exception Inform the programmer that cloning router is not permitted
         */
        private function __clone()
        {
            throw new Exception("Router is a Singleton and should not be cloned.");
        }

        /**
         * We make sleep throw an exception to keep the developer from using it.
         * causing there to be a second copy.
         *
         * @throws Exception Inform the developer serializing a singleton is not permitted.
         */
        final public function __sleep()
        {
            throw new Exception("Router is a singleton serialization is not permitted.");
        }

        /**
         * We make wakeup throw an exception to prevent someone from unserializing a
         * copy of router creating a second copy.
         *
         * @throws Exception Inform the developer that unserializing a singleton is not permitted
         */
        final public function __wakeup()
        {
            throw new Exception("Router is a singleton you can not unserialize it.");
        }
    }
}