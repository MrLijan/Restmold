<?php

namespace Mrlijan\Restmold;

use Mrlijan\Restmold\BaseRestmold;

class ApiExample extends BaseRestmold
{
    public function construct(){}
    /**
     * Get the Request's headers
     * * _Can be used for an Authentication process_
     * * __DEFAULT:__ _['Content-Type' => 'application/json', 'Accept' => 'application/json']_
     * @return array
     */
    protected function getHeaders(): array
    {
        return [];
    }

    /**
     * Get the routes list
     * * _Used to determine the available methods/requests_
     * * _Each index represents a method._
     * * __EXAMPLE:__ _['index' => ['method' => 'GET', 'path' => '/'], ...]_
     * @return array
     */
    protected function getRoutes(): array
    {
        return [
            'index' => [
                'method' => 'GET',
                'path' => '/index'
            ]
        ];
    }

    /**
     * Get the baseURI
     * * _Can be used to set the BaseURI_
     * * _RECOMMENDED TO USE_
     * * __EXAMPLE:__ _"https://localhost:9091/api"_
     * @return string
     */
    protected function getBaseURI(): string
    {
        return 'http://localhost:8080/api/v1';
    }
}
