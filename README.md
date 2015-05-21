Rapid Application Development : Security
========================================

Provide RAD security components

[![Build Status](https://travis-ci.org/KnpLabs/rad-security.svg?branch=master)](https://travis-ci.org/KnpLabs/rad-security)

#Installation

```bash
composer require knplabs/rad-security ~1.0
```

```php
class AppKernel
{
    function registerBundles()
    {
        $bundles = array(
            //...
            new Knp\Rad\Security\Bundle\KnpRadSecurityBundle(),
            //...
        );

        //...

        return $bundles;
    }
}
```

#Usages

##IS_OWNER voter

You now have access to a voter that checks if the authenticated user is the owner of an object.

The user contained inside the security token must implement `Knp\Rad\Security\OwnerInterface`.

The object you're about to test ownership must implement `Knp\Rad\Security\OwnableInterface`.

**Example**
```php
<?php

namespace App\Model;

use Knp\Rad\Security\OwnerInterface;

class User implements OwnerInterface
{
}
```

```php
<?php

namespace App\Model;

use Knp\Rad\Security\OwnableInterface;
use App\Model\User;

class Book implements OwnableInterface
{
    /** @var App\Model\User */
    protected $writtenBy;

    public function __construct(User $writtenBy)
    {
        $this->writtenBy = $writtenBy;
    }

    public function getOwner()
    {
        return $this->writtenBy;
    }
}
```

```php
$zola = new \App\Model\User(); // He is the current authenticated user
$hugo = new \App\Model\User();

$germinal = new \App\Model\Book($zola);
$miserables = new \App\Model\Book($hugo);

$authorizationChecker = $container->get(/* ... */);
$authorizationChecker->isGranted(array('IS_OWNER'), $germinal); // true
$authorizationChecker->isGranted(array('IS_OWNER'), $miserables); // false
```
