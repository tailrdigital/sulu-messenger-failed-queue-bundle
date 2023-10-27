# Sulu Messenger Failed-Queue

This package provides a sulu admin panel for managing the failed messages in the failure queue of symfony/messenger.

## Installation

```sh
composer require tailrdigital/sulu-messenger-failed-queue-bundle
```

Make sure the bundle is activated in `config/bundles.php`:

```php

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
            async:
                dsn: '%env(MESSENGER_TRANSPORT_ASYNC_DSN)%'
                retry_strategy:
                    max_retries: 3
                    delay: 1000
                    multiplier: 2
            failed: 'doctrine://default?queue_name=failed_messages'
```
