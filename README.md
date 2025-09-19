> ## Repository abandoned 2025-11-19
>
> This repository has moved to [phpro/sulu-messenger-failed-queue-bundle](https://github.com/phpro/sulu-messenger-failed-queue-bundle/).
> You can follow a [migration guide](https://github.com/phpro/sulu-messenger-failed-queue-bundle/releases/tag/1.0.0) to upgrade your project.


# Sulu Messenger Failed Queue

This package provides a Sulu admin panel for managing the failed messages in the failure queue of symfony/messenger. 

Via the admin panel you are able to check the error/exception details of each failed message, and you can trigger a requeue/retry if needed. 

## Demo

https://github.com/tailrdigital/sulu-messenger-failed-queue-bundle/assets/1618158/77cebd91-e62c-42f3-b976-4410bea7b9d2


## Installation

```sh
composer require tailrdigital/sulu-messenger-failed-queue-bundle
```

#### Register the bundle
Make sure the bundle is activated in `config/bundles.php`:

```php
Tailr\SuluMessengerFailedQueueBundle\SuluMessengerFailedQueueBundle::class => ['all' => true]
```

#### Register new admin routes

You need to manually register the failed queue admin controller routes in the file `config/routes_admin.yaml`.

```yaml
# config/routes_admin.yaml

tailr_failed_queue:
    resource: '@SuluMessengerFailedQueueBundle/Presentation/Controller/Admin'
    type: attribute
    prefix: /admin/api
```

#### Add node dependency

Register an additional module in your admin's node dependencies via `assets/admin/package.json`: 

```json
{
  "dependencies": {
    "sulu-messenger-failed-queue-bundle": "file:node_modules/@sulu/vendor/tailrdigital/sulu-messenger-failed-queue-bundle/assets/admin"
  }  
}
```

Make sure to load the additional node module in your admin's `assets/admin/index.js` or `assets/admin/app.js` file:

```js
import 'sulu-messenger-failed-queue-bundle';
```

#### Recompile your admin assets

```sh
cd /app/assets/admin
npm install
npm run watch
```

#### Setting up your failure queue

You can set up the symfony/messenger queue according to this example configuration inside `config/packages/messenger.yaml`

```yaml
# config/packages/messenger.yaml

framework:
    messenger:
        failure_transport: failed
        transports:
            async: '%env(MESSENGER_TRANSPORT_ASYNC_DSN)%'
            failed: 'doctrine://default?queue_name=failed'
```

#### Permissions
Make sure you've set the correct permissions in the Sulu admin for this package. Go to _Settings > User Roles_ and enable the permissions you need. Afterwards you could find the Failed Queue view/panel via _Settings > Failed Queue_. 

## Configuration

If you have a non-default failure queue configuration, you are able to overwrite our default settings by creating a `config/packages/sulu_messenger_failed_queue.yaml` file.

```yaml
# config/packages/sulu_messenger_failed_queue.yaml

sulu_messenger_failed_queue:
    failure_transport_service: 'messenger.transport.failed_high_priority' # Default 'messenger.transport.failed'
    failure_transport_table: 'failed_messages' # Default 'messenger_messages'
    failure_transport_queue_name: 'failed_high_priority' # Default 'failed'
```

If needed you can define the configured (failed) transport service definition or alias name via `failure_transport_service`. 
On the other hand you could modify the database table name via `failure_transport_table` and define the (failed) queue name via `failure_transport_queue_name`. 

## Known limitations

The symfony/messenger package has support for multiple failure queues/transports, but this package only has support for visualizing and managing one (doctrine) failure queue/transport.
