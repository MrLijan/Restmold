# Introduction
Restmold (pronounced /restː/ /mold:/) is a Laravel package for modeling 3rd party HTTP services and as an HTTP client. Using this package will allow you to model your external HTTP services. 
The magic behind Restmold is that you don't need to repeatedly re-code your API class services. 

### Inspiration
The inspiration for this project started when the company I worked for migrated our giant monolith into a bunch of small microservices.
Time passed, and we found ourselves writing API services repeatedly as the number of services grew. So, inspired by the flow of Laravel's models, Restmold has born out.


# Usage
This guide assumes that your Laravel project was already set up. 

Restmold is so easy-to-use; you just need to run the generate command to create a new API model. As a result, a new folder named "ApiModels" will be created under the ```\App``` folder, including the new concrete class.

```
php artisan restmold:generate <service name>
```


# Class Structure
Each restmold concrete class will derive its props and methods from the ```RestModel``` abstract class and should also implement the following properties and methods to work properly:

| Type     | Name        | Description |
| :---     | :--:        | :---------- |
| Property | **baseURI** | The base uri for that specific service|
| Method   | **headers** | Return the request's headers. This can be used for authentication|
| Method   | **routes**  | Used to construct the service's structure| 


# Class Configuration
the routes method is being used to construct the service's structure. This means that every array index listed below will be determined as a method for this service.

Each index of that array will be constructed as follows:

```php
protected function routes(): array
{
    return [
        'methodName' => [
            'method' => 'GET', // The reuqest's method
            'path' => '/index' // The reuqest's endpoint
        ],
    ];
}
```



### Config with Query Params
Your route includes query params? We've got your back! Just use the regular syntax, excluding the values. For example: 

```php
protected function routes(): array
{
    return [
        'list' => [
            'method' => 'GET', 
            'path' => '/students?name&age'
        ],
    ];
}
```

## Implementations

### Using Query Params
Once everything is ready and configured, the service is prepared to use. 
```php
use App\ApiModels\StudentsAPIModel;

class StudentsController extends BaseController
{
    public function listByParams(StudentsAPIModel $students_api)
    {
        return $students_api->list([
            'query' => [
                'name' => 'Brendon',
                'age' => '22'
            ]
        ]);
    }
}
```

### Using request body
```php
use App\ApiModels\StudentsAPIModel;

class StudentsController extends BaseController
{
    public function createNewStudent(StudentsAPIModel $students_api)
    {
        return $students_api->create([
            'body' => [
                'firstName' => 'John',
                'lastName' => 'Dow'
                'age' => '22'
            ]
        ]);
    }
}
```


