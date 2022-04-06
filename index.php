<?php
declare(strict_types = 1);
namespace App
{
    require_once("./core/router.core.php");
    require_once("./controllers/user.controller.php");

    use App\Controllers\User;
    use App\Core\Router;
    use App\Exceptions\RouteNotFoundException;
    use Exception;


    try {
        Router::create()
            ->register(User::class)
            ->resolve(
                $_SERVER['REQUEST_METHOD'],
                $_SERVER['REQUEST_URI']
            );
    } catch (RouteNotFoundException $exception)
    {
        /**
         * Add some kind of logging here for repeated bad requests to maybe ip ban frequent offenders
         * as they may be trying to map the api or find a vulnerability.
         */
        echo "Forbidden!";
    } catch (Exception $exception)
    {
        echo "<pre>".print_r($exception, true)."</pre>";
    }
}