# Sulu Messenger Failed Queue

This package provides a Sulu admin panel for managing the failed messages in the failure queue of symfony/messenger. 
Via the admin interface you are able to check the error/exception details of each failed message, and you can trigger a requeue/retry if needed. 

## Demo
![Sulu Messenger Failed Queue In Action](/doc/images/sulu-messenger-failed-queue.gif)

## Installation

```sh
composer require tailrdigital/sulu-messenger-failed-queue-bundle
```

Make sure the bundle is activated in `config/bundles.php`:

```php
Tailr\SuluMessengerFailedQueueBundle\SuluMessengerFailedQueueBundle::class => ['all' => true]
```

#### assets/admin/package.json

Register an additional module in your admin's package.json dependencies: 

```json
{
  "dependencies": {
    "sulu-messenger-failed-queue-bundle": "file:node_modules/@sulu/vendor/tailr/sulu-messenger-failed-queue-bundle/assets/admin"
  }  
}
```

#### assets/admin/index.js

Make sure to load the additional module in your admin's `index.js` or `app.js` file.

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
framework:
    messenger:
        failure_transport: failed
        transports:
            async: '%env(MESSENGER_TRANSPORT_ASYNC_DSN)%'
            failed: 'doctrine://default?queue_name=failed'
```

#### Permissions
Make sure you've set the correct permissions in the Sulu backend for this package. Go to _Settings > User Roles_ and enable the permissions you need. 

## Configuration

If you have a non-standard failed messenger configuration, you are able to overwrite the default settings by creation a `config/packages/sulu_messenger_failed_queue.yaml` file.

```yaml
sulu_messenger_failed_queue:
    failure_transport_service: 'messenger.transport.failed'
    failure_transport_table: 'messenger_messages'
    failure_transport_queue_name: 'failed'
```

If needed you can define the configured (failed) transport service definition or alias name via `failure_transport_service`. 
On the other hand you could modify the database table name via `failure_transport_table` and define the (failed) queue name via `failure_transport_queue_name`. 

## Known limitations

The symfony/messenger package allows to configure multiple failure queues, but this package only has support for visualizing one failed queue.
