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

The Google Analytics Client uses the Analytics Reporting API. You'll need to get a Google Service Account Key from the Google Developers Console:
1. In the console, follow the steps to create a new app.
2. Enable the Analytics Reporting API
3. Click on the Credentials tab, and follow the steps to create credentials.
4. Select Service Account Key as the credential type.
5. Select a New Service Account and give it a name. Choose JSON as the Key Type.
6. Download the JSON file and save it in the location specified in the package config file.

Finally, you'll need to grant permissions to your Google Analytics property: 
1. Go to "User Management" in the admin section of the property.
2. Use the email address found in the `client_email` key from the JSON file you downloaded. Read only access is sufficient.

### Options
The Google Analytics client offers some options for configuration:

* `viewId` (required): The ID for the Google Analytics view you wish to pull from. This can be set as a default in the config file.
* `dimensions`: An array of dimensions to add to the query.
* `alias`: An optional alias to use for the metric name
* `groupByDimensions`: This option allows you to group results by dimensions. Pass an array of the indicies of the dimensions you wish you group by from the `dimensions` option. See example below.

### Example

This example queries for events, accepting the `ga:eventCategory`, `ga:eventAction`, `ga:eventLabel`, and `ga:date` dimensions. The results are grouped by a combination of `ga:eventCategory` and `ga:eventAction`.

```php
$metric = Analyst::metric(
    ['ga:totalEvents'],
    Period::days(14),
    Analyst::client('google'),
    [
        'viewId' => 'XXXXXX', // <- YOUR VIEW ID
        'dimensions' => ['ga:eventCategory', 'ga:eventAction', 'ga:eventLabel', 'ga:date'],
        'alias' => ['events'],
        'groupByDimensions' => [0, 1]
    ]);
```

The value of `$metric` will be an instance of `AnalystDataCollection`.

## Accessing your data
### Analyst Data Collections

Both clients return an instance of `AnalystDataCollection`. This collection has some accessor methods making it easy to use your data:

* `getTotal`: Returns the total number for the metric requested.
* `getGroups`: Returns an array of `AnalystDataGroup` objects.
* `getRaw`: Returns the data in the original raw format.

### Analyst Data Groups

The `AnalystDataGroup` class is a grouping of your data. Each metric call will have a minimum of one group. A group consists of two main properties: `total`, and `points`.

* `getTotal`: Returns the total metric number for that grouping.
* `getPoints`: Returns the data points for each group, formatted in a way that can be used with Google Charts.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please use the issue tracker on GitHub.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.