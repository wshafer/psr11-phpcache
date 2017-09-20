[![codecov](https://codecov.io/gh/wshafer/psr11-phpcache/branch/master/graph/badge.svg)](https://codecov.io/gh/wshafer/psr11-phpcache)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/wshafer/psr11-phpcache/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/wshafer/psr11-phpcache/?branch=master)
[![Build Status](https://travis-ci.org/wshafer/psr11-phpcache.svg?branch=master)](https://travis-ci.org/wshafer/psr11-phpcache)

# PSR-11 PHP Cache

[PHP Cache](http://www.php-cache.com/en/latest/) Factories for PSR-11

#### Table of Contents
- [Installation](#installation)
- [Usage](#usage)
- [Containers](#containers)
    - [Pimple](#pimple-example)
    - [Zend Service Manager](#zend-service-manager)
- [Frameworks](#frameworks)
    - [Zend Expressive](#zend-expressive)
    - [Zend Framework 3](#zend-framework-3)
    - [Slim](#slim)
- [Configuration](#configuration)
    - [Minimal Configuration](#minimal-configuration)
        - [Example](#minimal-example)
    - [Full Configuration](#full-configuration)
        - [Example](#full-example)
- [Adapters](#adapters)
    - [APC](#apc)
    - [APCU](#apcu)
    - [Array](#array)
    - [File System](#file-system)
    - <del>[Memcache](#memcache)</del>
    - [Memcached](#memcached)
    - [MongoDb](#mongodb)
    - [Predis](#predis)
    - [Redis](#redis)
    - [Void](#void)
    - [Doctrine](#doctrine)
    - [Chain](#chain)
    
# Installation

```bash
composer require wshafer/psr11-phpcache
```

# Usage

```php
<?php

// Get a pool
$pool = $container->get('myCacheServiceName');

// Get an item (existing or new)
$item = $pool->getItem('cache_key');

// Set some values and store
$item->set('value');
$item->expiresAfter(60);
$pool->save($item);

// Verify existence
$pool->hasItem('cache_key'); // True
$item->isHit(); // True

// Get stored values
$myValue = $item->get();
echo $myValue; // "value"

// Delete
$pool->deleteItem('cache_key');
$pool->hasItem('cache_key'); // False
```

Additional info can be found in the [documentation](http://www.php-cache.com/en/latest/)

# Containers
Any PSR-11 container wil work.  In order to do that you will need to add configuration
and register the factory \WShafer\PSR11PhpCache\PhpCacheFactory()

Below are some specific container examples to get you started

## Pimple Example
```php
// Create Container
$container = new \Xtreamwayz\Pimple\Container([
    // Cache using the default keys.
    'cache' => new \WShafer\PSR11PhpCache\PhpCacheFactory(),
    
    // Another Cache using a different cache configuration
    'otherCache' => function($c) {
        return \WShafer\PSR11PhpCache\PhpCacheFactory::cacheTwo($c);
    },

    'config' => [
        'caches' => [
            /*
             * At the bare minimum you must include a default cache config.
             * Otherwise a void cache will be used and operations will be 
             * be sent to the void.
             */
            'default' => [
                'type'      => 'apc',          // Required : Type of adapter
                'namespace' => 'my-namespace', // Optional : Namespace
                'prefix'    => 'prefix_',      // Optional : Prefix
                'logger'    => 'my-logger',    // Optional : PSR-1 Logger Service Name
                'options'   => [],             // Optional : Apdapter Specific Options
            ],
            
            // Another Cache
            'cacheTwo' => [
                'type'      => 'apcu',         // Required : Type of adapter
                'namespace' => 'my-namespace', // Optional : Namespace
                'prefix'    => 'prefix_',      // Optional : Prefix
                'logger'    => 'my-logger',    // Optional : PSR-1 Logger Service Name
                'options'   => [],             // Optional : Apdapter Specific Options
            ],
            
            // Cache Chain
            'chained' => [
                'type' => 'chain',
                'options' => [
                    'caches' => [
                        'default',
                        'cacheTwo',
                    ],
                ],
            ],
        ],
    ],
]);
```

## Zend Service Manager

```php
$container = new \Zend\ServiceManager\ServiceManager([
    'factories' => [
        // Cache using the default keys.
        'cache' => \WShafer\PSR11PhpCache\PhpCacheFactory::class,
        
        // Another Cache using a different cache configuration
        'otherCache' => [\WShafer\PSR11PhpCache\PhpCacheFactory::class, 'cacheTwo'],
    ]
]);

$container->setService('config', [
    'caches' => [
        /*
         * At the bare minimum you must include a default cache config.
         * Otherwise a void cache will be used and operations will be 
         * be sent to the void.
         */
        'default' => [
            'type'      => 'apc',          // Required : Type of adapter
            'namespace' => 'my-namespace', // Optional : Namespace
            'prefix'    => 'prefix_',      // Optional : Prefix
            'logger'    => 'my-logger',    // Optional : PSR-1 Logger Service Name
            'options'   => [],             // Optional : Apdapter Specific Options
        ],
        
        // Another Cache
        'cacheTwo' => [
            'type'      => 'apcu',         // Required : Type of adapter
            'namespace' => 'my-namespace', // Optional : Namespace
            'prefix'    => 'prefix_',      // Optional : Prefix
            'logger'    => 'my-logger',    // Optional : PSR-1 Logger Service Name
            'options'   => [],             // Optional : Apdapter Specific Options
        ],
        
        // Cache Chain
        'chained' => [
            'type' => 'chain',
            'options' => [
                'caches' => [
                    'default',
                    'cacheTwo',
                ],
            ],
        ],
    ],
]);
```

# Frameworks
Any framework that use a PSR-11 should work fine.   Below are some specific framework examples to get you started

## Zend Expressive
You'll need to add configuration and register the services you'd like to use.  There are number of ways to do that
but the recommended way is to create a new config file `config/autoload/cache.global.php`

### Configuration
config/autoload/cache.global.php
```php
<?php
return [
    'dependencies' => [
       'factories' => [
           // Cache using the default keys.
           'cache' => \WShafer\PSR11PhpCache\PhpCacheFactory::class,
           
           // Another Cache using a different cache configuration
           'otherCache' => [\WShafer\PSR11PhpCache\PhpCacheFactory::class, 'cacheTwo'],
       ]
    ],
    
    'caches' => [
        /*
         * At the bare minimum you must include a default cache config.
         * Otherwise a void cache will be used and operations will be 
         * be sent to the void.
         */
        'default' => [
            'type'      => 'apc',          // Required : Type of adapter
            'namespace' => 'my-namespace', // Optional : Namespace
            'prefix'    => 'prefix_',      // Optional : Prefix
            'logger'    => 'my-logger',    // Optional : PSR-1 Logger Service Name
            'options'   => [],             // Optional : Adapter Specific Options
        ],
        
        // Another Cache
        'cacheTwo' => [
            'type'      => 'apcu',         // Required : Type of adapter
            'namespace' => 'my-namespace', // Optional : Namespace
            'prefix'    => 'prefix_',      // Optional : Prefix
            'logger'    => 'my-logger',    // Optional : PSR-1 Logger Service Name
            'options'   => [],             // Optional : Adapter Specific Options
        ],
        
        // Cache Chain
        'chained' => [
            'type' => 'chain',
            'options' => [
                'caches' => [
                    'default',
                    'cacheTwo',
                ],
            ],
        ],
    ],
];
```


## Zend Framework 3
You'll need to add configuration and register the services you'd like to use.  There are number of ways to do that
but the recommended way is to create a new config file `config/autoload/cache.global.php`

### Configuration
config/autoload/cache.global.php
```php
<?php
return [
    'service_manager' => [
       'factories' => [
           // Cache using the default keys.
           'cache' => \WShafer\PSR11PhpCache\PhpCacheFactory::class,
           
           // Another Cache using a different cache configuration
           'otherCache' => [\WShafer\PSR11PhpCache\PhpCacheFactory::class, 'cacheTwo'],
       ]
    ],
    
    'caches' => [
        /*
         * At the bare minimum you must include a default cache config.
         * Otherwise a void cache will be used and operations will be 
         * be sent to the void.
         */
        'default' => [
            'type'      => 'apc',          // Required : Type of adapter
            'namespace' => 'my-namespace', // Optional : Namespace
            'prefix'    => 'prefix_',      // Optional : Prefix
            'logger'    => 'my-logger',    // Optional : PSR-1 Logger Service Name
            'options'   => [],             // Optional : Adapter Specific Options
        ],
        
        // Another Cache
        'cacheTwo' => [
            'type'      => 'apcu',         // Required : Type of adapter
            'namespace' => 'my-namespace', // Optional : Namespace
            'prefix'    => 'prefix_',      // Optional : Prefix
            'logger'    => 'my-logger',    // Optional : PSR-1 Logger Service Name
            'options'   => [],             // Optional : Adapter Specific Options
        ],
        
        // Cache Chain
        'chained' => [
            'type' => 'chain',
            'options' => [
                'caches' => [
                    'default',
                    'cacheTwo',
                ],
            ],
        ],
    ],
];
```

### Module Config
If you're not using the [Zend Component Installer](https://github.com/zendframework/zend-component-installer) you will 
also need to register the Module.

config/modules.config.php (ZF 3 skeleton)
```php
<?php

return [
    // ... Previously registered modules here
    'WShafer\\PSR11PhpCache',
];
```

config/application.config.php (ZF 2 skeleton)
```php
<?php

return [
    'modules' => [
        // ... Previously registered modules here
        'WShafer\\PSR11PhpCache',
    ]
];
```

## Slim

public/index.php
```php
<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';

// Add Configuration
$config = [
    'settings' => [
        'caches' => [
            /*
             * At the bare minimum you must include a default cache config.
             * Otherwise a void cache will be used and operations will be 
             * be sent to the void.
             */
            'default' => [
                'type'      => 'apc',          // Required : Type of adapter
                'namespace' => 'my-namespace', // Optional : Namespace
                'prefix'    => 'prefix_',      // Optional : Prefix
                'logger'    => 'my-logger',    // Optional : PSR-1 Logger Service Name
                'options'   => [],             // Optional : Adapter Specific Options
            ],
            
            // Another Cache
            'cacheTwo' => [
                'type'      => 'apcu',         // Required : Type of adapter
                'namespace' => 'my-namespace', // Optional : Namespace
                'prefix'    => 'prefix_',      // Optional : Prefix
                'logger'    => 'my-logger',    // Optional : PSR-1 Logger Service Name
                'options'   => [],             // Optional : Adapter Specific Options
            ],
            
            // Cache Chain
            'chained' => [
                'type' => 'chain',
                'options' => [
                    'caches' => [
                        'default',
                        'cacheTwo',
                    ],
                ],
            ],
        ],
    ],
];

$app = new \Slim\App($config);

// Wire up the factory
$container = $app->getContainer();

// Register the service with the container.
$container['cache'] = new \WShafer\PSR11PhpCache\PhpCacheFactory();
$container['otherCache'] = function($c) {
    return WShafer\PSR11PhpCache\PhpCacheFactory::cacheTwo($c);
};

```

# Configuration
 - Named Services : These are services names wired up to a factory. The configuration will differ based on the 
   type of container / framework in use.
    
 - [Adapters](#adapters) : Cache Pool config tell us what type of cache to use and how to connect to that cache.
   Some caches provide other special options on how to handle the data and what data to handle.  See the
   appropriate apdaptor config below.


## Minimal Configuration
A minimal configuration would consist of at least one default cache and one named service.
Please note that if you don't specify a default cache a Void pool will be used when 
you wire up the default cache.

### Minimal Example (using Zend Expressive for the example)
```php
<?php

return [
    'dependencies' => [
       'factories' => [
           // Cache using the default keys.
           'cache' => \WShafer\PSR11PhpCache\PhpCacheFactory::class,
       ]
    ],
    
    'caches' => [
        /*
         * At the bare minimum you must include a default cache config.
         * Otherwise a void cache will be used and operations will be 
         * be sent to the void.
         */
        'default' => [
            'type'    => 'apc', // Required : Type of adapter
            'options' => [],    // Optional : Adapter Specific Options
        ],
    ],
];

```


## Full Configuration (using Zend Expressive for the example)

### Full Example
```php
<?php

return [
    'dependencies' => [
       'factories' => [
           // Cache using the default keys.
           'cache' => \WShafer\PSR11PhpCache\PhpCacheFactory::class,
           
           // Another Cache using a different cache configuration
           'otherCache' => [\WShafer\PSR11PhpCache\PhpCacheFactory::class, 'cacheTwo'],
       ]
    ],
    
    'caches' => [
        /*
         * At the bare minimum you must include a default cache config.
         * Otherwise a void cache will be used and operations will be 
         * be sent to the void.
         */
        'default' => [
            'type'      => 'apc',          // Required : Type of adapter
            'namespace' => 'my-namespace', // Optional : Namespace
            'prefix'    => 'prefix_',      // Optional : Prefix
            'logger'    => 'my-logger',    // Optional : PSR-1 Logger Service Name
            'options'   => [],             // Optional : Adapter Specific Options
        ],
        
        // Another Cache
        'cacheTwo' => [
            'type'      => 'apcu',         // Required : Type of adapter
            'namespace' => 'my-namespace', // Optional : Namespace
            'prefix'    => 'prefix_',      // Optional : Prefix
            'logger'    => 'my-logger',    // Optional : PSR-1 Logger Service Name
            'options'   => [],             // Optional : Adapter Specific Options
        ],
        
        // Cache Chain
        'chained' => [
            'type' => 'chain',
            'options' => [
                'caches' => [
                    'default',
                    'cacheTwo',
                ],
            ],
        ],
    ],
];
```
# Adapters

## APC
This is a PSR-6 cache implementation using Apc. It is a part of the PHP Cache organisation. 
To read about features like tagging and hierarchy support please read the shared 
documentation at [www.php-cache.com](http://www.php-cache.com/en/latest/).

```php
<?php

return [
    'caches' => [
        'myHandlerName' => [
            'type' => 'apc',
            'options' => [
                'skipOnCli' => false, // Optional : Skip cache with CLI
            ],
        ],
    ],
];
```
Php Cache Docs: [Apc PSR-6 Cache pool](https://github.com/php-cache/apc-adapter)

## APCU
This is a PSR-6 cache implementation using Apcu. It is a part of the PHP Cache organisation. 
To read about features like tagging and hierarchy support please read the shared 
documentation at [www.php-cache.com](http://www.php-cache.com/en/latest/).

```php
<?php

return [
    'caches' => [
        'myHandlerName' => [
            'type' => 'apcu',
            'options' => [
                'skipOnCli' => false, // Optional : Skip cache with CLI
            ],
        ],
    ],
];
```
Php Cache Docs: [Apcu PSR-6 Cache pool](https://github.com/php-cache/apcu-adapter)

## Array
This is a PSR-6 cache implementation using PHP array. It is a part of the PHP Cache organisation. 
To read about features like tagging and hierarchy support please read the shared 
documentation at [www.php-cache.com](http://www.php-cache.com/en/latest/).

```php
<?php

return [
    'caches' => [
        'myHandlerName' => [
            'type' => 'array',
            'options' => [] // No options available,
        ],
    ],
];
```
Php Cache Docs: [Array PSR-6 Cache pool](https://github.com/php-cache/array-adapter)

## File System
This is a PSR-6 cache implementation using Filesystem. It is a part of the PHP Cache organisation. To read about 
features like tagging and hierarchy support please read the shared documentation at 
[www.php-cache.com](http://www.php-cache.com/en/latest/).

This implementation is using the excellent [Flysystem](http://flysystem.thephpleague.com/).

_See: [PSR-11 FlySystem](https://github.com/wshafer/psr11-flysystem) for some pre-built factories to
get up and running quickly_ 

```php
<?php

return [
    'caches' => [
        'myHandlerName' => [
            'type' => 'fileSystem',
            'options' => [
                'flySystemService' => 'my-service', // Required : Pre-configured FlySystem service from the container
                'folder'           => 'cache',      // Optional : Folder.  Default: 'cache'
            ]
        ],
    ],
];
```
Php Cache Docs: [Filesystem PSR-6 Cache pool](https://github.com/php-cache/filesystem-adapter)

## Illuminate
This is a PSR-6 cache implementation using Illuminate cache. It is a part of the PHP Cache organisation. 
To read about features like tagging and hierarchy support please read the shared documentation at 
[www.php-cache.com](http://www.php-cache.com/en/latest/).

This is a PSR-6 to Illuminate bridge.

```php
<?php

return [
    'caches' => [
        'myHandlerName' => [
            'type' => 'illuminate',
            'options' => [
                'store' => 'my-service', // Required : Pre-configured illuminate store service from the container
            ]
        ],
    ],
];
```
Php Cache Docs: [Illuminate PSR-6 Cache pool](https://github.com/php-cache/illuminate-adapter)


## <del>Memcache</del>
This adaptor is not supported by this package as there is no official release of this driver for PHP 7.
Please use the [Memcached](#memcached) adaptor instead.


## Memcached
This is a PSR-6 cache implementation using Memcached. It is a part of the PHP Cache organisation. 
To read about features like tagging and hierarchy support please read the shared documentation at 
[www.php-cache.com](http://www.php-cache.com/en/latest/).

```php
<?php

return [
    'caches' => [
        'myHandlerName' => [
            'type' => 'memcached',
            'options' => [
                // A container service is required if no servers are provided : Pre-configured memcached service from the container.
                'service' => 'my-service', 
                
                // Required if no service is provided : List of servers to add to pool.  Must provide at least one server.
                'servers' => [
                    'local' => [
                        'host'   => '127.0.0.1',
                        'port'   => 11211,
                        'weight' => 0
                    ]
                ],
                
                // Optional: List of Memcached options.  See: http://php.net/manual/en/memcached.setoption.php
                // Only set if servers are provided.
                'memcachedOptions' => [
                    \Memcached::OPT_HASH => Memcached::HASH_MURMUR
                ],
                
                // Optional :  Persistent Id.  Only used if servers are provided.
                'persistentId' => 'some_id',  
            ]
        ],
    ],
];
```
Php Cache Docs: [Memcached PSR-6 Cache pool](https://github.com/php-cache/memcached-adapter)

## MongoDb
This is a PSR-6 cache implementation using MongoDB.  It is a part of the PHP Cache organisation. 
To read about features like tagging and hierarchy support please read the shared documentation at 
[www.php-cache.com](http://www.php-cache.com/en/latest/).

```php
<?php

return [
    'caches' => [
        'myHandlerName' => [
            'type' => 'mongodb',
            'options' => [
                // A container service is required if no DSN is provided : Pre-configured Mongo Collection
                // service from the container.
                'service'  => 'my-service', 
                
                // Required if no service is provided : DSN connection string
                'dsn'      => 'mongodb://127.0.0.1',
                
                // Required if no service is provided : Database name to connect to.
                'database' => 'some-db-name',
                
                // Required if no service is provided : Collection name.
                'collection' => 'some_collection',  
            ]
        ],
    ],
];
```
Php Cache Docs: [MongoDB PSR-6 Cache pool](https://github.com/php-cache/mongodb-adapter)

## Predis
This is a PSR-6 cache implementation using Predis.  It is a part of the PHP Cache organisation. 
To read about features like tagging and hierarchy support please read the shared documentation at 
[www.php-cache.com](http://www.php-cache.com/en/latest/).

This implementation is using [Predis](https://github.com/nrk/predis). If you want an adapter with 
[PhpRedis](https://github.com/phpredis/phpredis) you should look at our 
[Redis adapter](#redis).

```php
<?php

return [
    'caches' => [
        'fromService' => [
            'type' => 'predis',
            'options' => [
                // A container service is required if no servers are provided : Pre-configured Predis Client
                // service from the container.
                'service'  => 'my-service', 
            ]
        ],
        
        'singleConnection' => [
            'type' => 'predis',
            'options' => [
                // Required if no service is provided : server(s)
                'servers'      => [
                    'tcp:/127.0.0.1:6379'
                ],
                
                // Optional : Array of options to pass to the client
                'connectionOptions' => [],
            ]
        ],
        
        'singleConnectionUsingConnectionParams' => [
            'type' => 'predis',
            'options' => [
                // Required if no service is provided : server(s)
                'servers'      => [
                    [
                        'scheme' => 'tcp',
                        'host'   => '10.0.0.1',
                        'port'   => 6379,
                    ]
                ],
                
                // Optional : Array of options to pass to the client
                'connectionOptions' => [],
            ],
        ],
        
        'cluster' => [
            'type' => 'predis',
            'options' => [
                // Required if no service is provided : server(s)
                'servers'      => [
                    'tcp://10.0.0.1?alias=first-node',
                    ['host' => '10.0.0.2', 'alias' => 'second-node'],
                ],
                
                // Optional : Array of options to pass to the client
                'connectionOptions' => ['cluster' => 'redis'],
            ],
        ]
    ],
];
```
_Note: For more connection options please see the [Predis docs](https://github.com/nrk/predis)._

Php Cache Docs: [Predis PSR-6 Cache pool](https://github.com/php-cache/predis-adapter)

## Redis
This is a PSR-6 cache implementation using Redis. It is a part of the PHP Cache organisation. 
To read about features like tagging and hierarchy support please read the shared documentation at 
[www.php-cache.com](http://www.php-cache.com/en/latest/).

This implementation is using [PhpRedis](https://github.com/phpredis/phpredis). If you want an adapter with 
[Predis](https://github.com/nrk/predis) you should look at our [Predis adapter](#predis).

```php
<?php

return [
    'caches' => [
        'fromService' => [
            'type' => 'redis',
            'options' => [
                // A container service is required if no other connection is provided : Pre-configured Php-Redis Client
                // service from the container.
                'service'  => 'my-service', 
            ]
        ],
        
        'connection' => [
            'type' => 'redis',
            'options' => [
                // Required if no service is provided : server(s)
                'server'      => [
                    'host' => '127.0.0.1',  // Required : Hostname
                    'port' => 6379,         // Optional : Port (Default: 6379)
                    'timeout' => 0.0,       // Optional : Timeout (Default: 0.0)
                    'persistent' => true,   // Optional : Use persistent connections (Default: true)
                    'persistentId' => null, // Optional : Persistent Id (Default: 'phpcache')
                ],
            ],
        ],
    ],
];
```
Php Cache Docs: [Redis PSR-6 Cache pool](https://github.com/php-cache/redis-adapter)


## Void
This is a void implementation of a PSR-6 cache. Other names for this adapter could be Blackhole or Null adapter. 
This adapter does not save anything and will always return an empty CacheItem. It is a part of the PHP 
Cache organisation. To read about features like tagging and hierarchy support please read the shared 
documentation at [www.php-cache.com](http://www.php-cache.com/en/latest/).

```php
<?php

return [
    'caches' => [
        'myHandlerName' => [
            'type' => 'void',
            'options' => [] // No options available,
        ],
    ],
];
```
Php Cache Docs: [Void PSR-6 Cache pool](https://github.com/php-cache/void-adapter)


## Doctrine
This is a PSR-6 cache implementation using Doctrine cache. It is a part of the PHP Cache organisation.
To read about features like tagging and hierarchy support please read the shared documentation at 
[www.php-cache.com](http://www.php-cache.com/en/latest/).

```php
<?php

return [
    'caches' => [
        'fromService' => [
            'type' => 'doctrine',
            'options' => [
                'service'  => 'my-service', // Required : A pre-configured doctrine cache service name
            ]
        ],
    ],
];
```
Php Cache Docs: [Doctrine PSR-6 Cache pool](https://github.com/php-cache/doctrine-adapter)

## Chain
This is a PSR-6 cache implementation using a chain of other PSR-6 cache pools. It is a part of the PHP Cache 
organisation. To read about features like tagging and hierarchy support please read the shared documentation
at [www.php-cache.com](http://www.php-cache.com/en/latest/).

```php
<?php

return [
    'caches' => [
        'fromService' => [
            'type' => 'chain',
            'options' => [
                'service'       => ['service-one', 'service-two'], // Required : An array of pre-configured cache service names
                'skipOnFailure' => false,                          // Optional : If true we will remove a pool form the chain if it fails. (Default: false)
            ]
        ],
    ],
];
```
Php Cache Docs: [PSR-6 Cache pool chain](https://github.com/php-cache/chain-adapter)

