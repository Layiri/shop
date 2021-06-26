<?php


namespace App\core;

/**
 * Class Dispatcher
 *
 * @author Layiri Batiene
 * @package App\core
 */
class Dispatcher
{

    /**
     * @var Router
     */
    private Router $router;

    function __construct()
    {
        $this->router = new Router();
    }

    /**
     * Redirect to route
     *
     */
    public final function dispatch(): void
    {
        $this->router->route(
            $_SERVER['REQUEST_METHOD'],
            $this->pathFromUri($_SERVER['REQUEST_URI']),
            $_REQUEST);
    }

    /**
     * This method help us to add route
     * @param string $pattern
     * @param object $action
     * @return $this
     */
    public final function routing(string $pattern, object $action): static
    {
        $this->router->addRouting($pattern, $action);
        return $this;
    }

    /**
     * Return path from uri
     * @param string $path
     * @return string
     */
    public final function pathFromUri(string $path): string
    {
        $path = !empty($path) && $path[strlen($path) - 1] == '/' ? substr($path, 0, strlen($path) - 1) : $path;
        if (empty($path)) {
            return '';
        }
        $queryPos = strpos($path, '?');
        if ($queryPos !== FALSE) {
            $path = substr($path, 0, $queryPos);
        }
        return $path[0] === '/' ? substr($path, 1) : $path;
    }
}