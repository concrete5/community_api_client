# Community Api Client

This is the official api client that is used to communicate with the community site.

Currently the functionality is very limited but it get's extended soon.

You can use the api client to assign achievements.

## Requirements

- PHP 7.1
- Can only run within Concrete CMS context

## Installation

The Community API client can be installed using Composer.

Composer
To install run `composer require concrete5/community_api_client`

## Configuration

There is no interface for configure the api client. However there are multiple options to do that.

### Option 1: Programmatically

If you want to setup the credentials programmatically you can do so like this:

```php
use Concrete\Core\Support\Facade\Application;
use PortlandLabs\CommunityApiClient\ApiClient;

/** @var ApiClient $apiClient */
$apiClient = $app->make(ApiClient::class);
$apiClient
    ->setEndpoint("your_endpoint")
    ->setClientId("your_client_id")
    ->setClientSecret("your_client_secret");
```

### Option 2: With a configuration file

You can also take use of an configuration file.

All you need to do is creating a file located at `application/config/community_api_client.php` with the following content:

```php
<?php
return [
    'endpoint' => 'your_endpoint',
    'client_id' => 'your_client_id',
    'client_secret' => 'your_client_secret'
];

```

### Option 3: With the Concrete CMS CLI application

You can also take use of the cli application to configure the api client.

```shell script
concrete/bin/concrete5 c5:config set community_api_client.endpoint your_endpoint
concrete/bin/concrete5 c5:config set community_api_client.client_id your_client_id
concrete/bin/concrete5 c5:config set community_api_client.client_secret your_client_secret
```

## Usage

After you have configured the endpoint you can communicate with the api interface.

The following code snippet demonstrates you how to deal with it.

```php
use Concrete\Core\Support\Facade\Application;
use PortlandLabs\CommunityApiClient\Models\Achievements;

$app = Application::getFacadeApplication();
/** @var Achievements $achievements */
$achievements = $app->make(Achievements::class);
$success = $achievements->assign("test_handle");
```

Currently there is now api documentation available. So if you want to know what functionality is available you need to take a look at the models folder within this repository.
