#  Laravel Analyst

[![Latest Version](https://img.shields.io/github/release/benwilkins/laravel-analyst.svg?style=flat-square)](https://github.com/benwilkins/laravel-analyst/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)

Use this package to retrieve analytics about your app, from one or many data sources. Currently, this package supports an internal data source and Google Analytics.

## Install

This package can be installed through Composer.

``` bash
composer require benwilkins/laravel-analyst
```

Once installed, add the service provider:

```php
// config/app.php
'providers' => [
    ...
    Benwilkins\Analyst\AnalystServiceProvider::class,
    ...
];
```

Publish the config file:

``` bash
php artisan vendor:publish --provider="Benwilkins\Analyst\AnalystServiceProvider"
```

This package also comes with a facade, making it easy to call the class:

```php
// config/app.php
'aliases' => [
    ...
    'Analyst' => Benwilkins\Analyst\AnalystFacade::class,
    ...
];
```


The following config file will be published in `config/laravel-analyst.php`

```php

return [
    /*
     * Path to the client secret json file.
     */
    'google_account_credentials_json' => storage_path('app/laravel-analyst/Google/account-credentials.json'),
    /*
     * The amount of minutes the Google API responses will be cached.
     * If you set this to zero, the responses won't be cached at all.
     */
    'cache_lifetime_in_minutes' => 60 * 24,
    /*
     * The directory where the underlying Google_Client will store it's cache files.
     */
    'cache_location' => storage_path('app/laravel-analyst/'),
    /*
     * The directory where custom internal metrics are stored.
     */
    'custom_metric_location' => '/app/Metrics/',
    /*
     * Default data client
     */
    'default_client' => 'internal',
];

```

## Internal Client

The internal client can be used to pull analytics from within your app's databse. By default, the client comes with one metric: `NewUsersMetric`.

### Custom Metrics

Custom metrics for the internal client can be used by creating a Metric class and adding it to the custom metric directory. By default, that directory is `/app/Metrics/`, but can be changed from the config.

To add a custom metric:

1. Create a new class with the namespace `Benwilkins\Analyst\Clients\Internal\Metrics`. The class should extend the Metric abstract class.
2. Your class must implement the `run` method. This method takes two arguments: `$period` and `$params`. The period is an instance of the Period class that defines the date range for the metric. The params argument allows custom parameters to be applied to the metric, such as filters.

```php
// app/Metrics/MyNewMetric.php
namespace Benwilkins\Analyst\Clients\Inernal\Metrics;

use Benwilkins\Analyst\Period;

class MyNewMetric extends Metric
{
    public function run(Period $period, $params = [])
    {
        // Code to run the metric
    }
}
```

### Example Usage

To use the Internal client, follow this example:

```php
use Benwilkins\Analyst\Period;


$data = Analyst::metric('new users', Period::days(30));
```

## Google Analytics Client

Coming soon...

## Testing

Run the tests with:

``` bash
vendor/bin/phpunit
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please use the issue tracker on GitHub.

## Credits

- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.