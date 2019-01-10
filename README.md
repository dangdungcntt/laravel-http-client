# Guzzle Wrapper for laravel

## Installation

You can install the package via composer:

```bash
composer require nddcoder/laravel-http-client
```

## Usage

#### Inject HttpClient into Controller Contructor

```php
use Nddcoder\HttpClient\HttpClient;

class TodoController extends Controller 
{
    private $http;

    public function __construct(HttpClient $http)
    {
        $this->http = $http;
    }

    public function index()
    {
        return $this->http->get('https://jsonplaceholder.typicode.com/todos');
        
        /*
            {
                "status_code": 200,
                "headers": {},
                "body": "...",
                "bodyJSON": [...]
            }
        */
    }
}
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
