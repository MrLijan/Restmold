<?php

namespace Mrlijan\Restmold;

use BadMethodCallException;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Illuminate\Routing\Pipeline;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;

/**
 * @method abstract array getHeaders()
 * @method abstract array getRoutes()
 * @method abstract string getBaseURI()
 * @method abstract mixed pipe(Response $response)
 */
abstract class RestModel
{
    /**
     * __The Base URI for this Api model__
     * * _REQUIRED CONST_
     * * __EXAMPLE:__ _"https://localhost:9091/api"_
     * @var string
     */
    protected string $baseURI = '';

    /**
     * __The base URI for that service__
     * * _Can be used for an Authentication process_
     * * __DEFAULT:__ _['Content-Type' => 'application/json', 'Accept' => 'application/json']_
     * @return array
     */
    abstract protected function headers(): array;

    /**
     * __Return the Request's headers__
     * * _Used to determine the available methods/requests_
     * * _Each index represents a method._
     * * __EXAMPLE:__ _['index' => ['method' => 'GET', 'path' => '/'], ...]_
     * @return array
     */
    abstract protected function routes(): array;

    /**
     * __The route map__
     * @var array<string, array<string>> $routeMap
     */
    private array $routeMap = [];

    /**
     * __The request's headers__
     * @var array $headers
     */
    private array $internalHeaders = [
        'Content-Type' => 'application/json',
        'Accept' => 'application/json'
    ];

    /**
     * The path param separator character
     * @var string
     */
    private string $pathSeparator = ':';

    /**
     * The path param separator character
     * @var string
     */
    private string $querySeparator = '?';

    /**
     * Holds a GuzzleClient instance
     * @var Client
     */
    private Client $guzzle_client;

    public function __construct()
    {
        $this->routeMap = $this->routes();
        $this->internalHeaders = array_merge($this->internalHeaders, $this->getHeaders());
        $this->guzzle_client = new Client();
    }

    /**
     * __call method
     */
    public function __call(string $name, array $args = [])
    {
        if (!$this->isMethodExists($name)) {
            $this->throwError($name);
        }

        $requestConfig = $this->buildRequestConfig($this->routeMap[$name], $args[0] ?? []);
        return $this->responsePipe($this->send($requestConfig));
    }

    /**
     * The response is being streamed here
     * * _Can be used to manipulate the response data_
     * * _Can be overwritten_
     * * ___Return $response when not in use___
     * @param Response $response
     * @return Response
     */
    protected function responsePipe(Response $response): Response
    {
        return $response;
    }

    /**
     * The response is being streamed here
     * * _Can be used to manipulate the response data_
     * * _Can be overwritten_
     * * ___Return $response when not in use___
     * @param Response $response
     * @return Response
     */
    protected function requestPipe(Request $request): Request
    {
        return $request;
    }

    /**
     * Check if method exists within the routeMap
     * @param string $methodName
     * @return bool
     */
    private function isMethodExists(string $methodName): bool
    {
        return in_array($methodName, array_keys($this->routeMap));
    }

    /**
     * Throw new BadMethodCallException
     * @param string $methodName
     */
    private function throwError(string $methodName = ''): void
    {
        $className = get_class($this);
        throw new BadMethodCallException("Call to undefined method $className->$methodName().\n This is probably happened because of empty \$routeMap.");
    }

    /**
     * Build Request config
     * @param array $requestConfig
     */
    private function buildRequestConfig(array $requestConfig, array $options = [])
    {
        if (array_key_exists('params', $options)) {
            $requestConfig['path'] = $this->setPathParams($requestConfig['path'], $options['params']);
        }

        if (array_key_exists('query', $options)) {
            $requestConfig['path'] = $this->setQueryParams($requestConfig['path'], $options['query']);
        }

        return [
            'route' => $requestConfig['path'],
            'method' => $requestConfig['method'],
            'body' => $requestConfig['body'] ?? []
        ];
    }

    /**
     * Setting the path params
     * @param string $path
     * @param array $params
     * @return string
     */
    private function setPathParams(string $path, array $params): string
    {
        $final = explode($this->pathSeparator, $path);
        $final = implode(str_replace(array_keys($params), array_values($params), $final));

        return $final;
    }

    /**
     * Setting the Query params
     * @param string $path
     * @param array $query
     * @return string
     */
    private function setQueryParams(string $path, array $query): string
    {
        $splitSections = explode($this->querySeparator, $path);
        $params = explode('&', $splitSections[1]);
        $readyParams = [];
        foreach ($params as $param) {
            $readyParams[] = $param . '=' . $query[$param];
        }

        $finalString = implode($this->querySeparator, [$splitSections[0], implode($readyParams)]);
        return $finalString;
    }


    /**
     * Executes the request
     * @param array $requestConfig
     * @return Response
     */
    private function send(array $requestConfig): Response
    {
        $requestUri = $this->baseUri . $requestConfig['route'];
        $request = new Request($requestConfig['method'], $requestUri, $this->internalHeaders, json_encode($requestConfig['body'])); // Request here before sent;

        return $this->requestPipe($request);
    }
}
