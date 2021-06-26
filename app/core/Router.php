<?php


namespace App\core;


/**
 * Class Router
 *
 * @author Layiri Batiene
 * @package App\core
 */
class Router
{
    /**
     * @var array
     */
    private $routing = [];

    /**
     * Add route
     * @param string $pattern
     * @param object $action
     */
    public final function addRouting(string $pattern, object $action): void
    {
        $this->routing[$pattern] = $action;
    }

    /**
     * Redirect to controller
     * @param string $method
     * @param string $path
     * @param array|null $params
     */
    public final function route(string $method, string $path, ?array $params): void
    {
        $path = "{$method} " . $this->withEscapedSlashes("/{$path}");

        foreach ($this->routing as $pattern => $handler) {
            $patternParams = $this->patternParams($pattern);
            if (!empty($patternParams)) {
                $pattern = $this->withParams($pattern);
            }
            $pattern = $this->withEscapedSlashes($pattern);
            $pattern = $this->withMethod($pattern);

            if ($this->requestMatches($pattern, $path, $patternParams, $params)) {
                $handler($params);
                return;
            }
        }

        http_response_code(404);
        if (array_key_exists('/', $this->routing)) {
            $this->route['/']([]);
        }
    }

    /**
     * @param string $pattern
     * @param string $path
     * @param string|array $patternParams
     * @param array &params
     * @return bool
     */
    private function requestMatches(string $pattern, string $path, array $patternParams, ?array &$params): bool
    {
        if (preg_match("/^{$pattern}$/i", $path, $matches)) {
            if (!empty($patternParams)) {
                for ($i = 0; $i < sizeof($patternParams); $i++) {
                    $params[$patternParams[$i]] = $matches[$i + 1];
                }
            }
            return true;
        }
        return false;
    }

    /**
     * Get pattern of params
     *
     * @param string $pattern
     * @return array
     */
    private function patternParams(string $pattern): array
    {
        $matches = [];
        if (preg_match_all('/\b(?:(?:http):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i', $pattern, $matches)) {
            return $matches[1];
        }
        return [];
    }

    /**
     * @param string $pattern
     * @return string
     */
    private function withEscapedSlashes(string $pattern): string
    {
        return str_replace('/', ':', $pattern);
    }

    /**
     * This method will return pattern with method of request
     *
     * `Example= POST shop.local/api/local
     * @param string $pattern
     * @return string
     */
    private function withMethod(string $pattern): string
    {
        return !preg_match("/^[A-Z]+ .+$/i", $pattern) ? "GET {$pattern}" : $pattern;
    }

    /**
     *  This method help us to get pattern with params
     * @param string $pattern
     * @return array|string|null
     */
    private function withParams(string $pattern): array|string|null
    {
        return preg_replace('/\b(?:(?:http):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i', '([^:]+)', $pattern);
    }
}