Rapid Application Development : Security
========================================

Provide RAD security components

[![Build Status](https://travis-ci.org/KnpLabs/rad-security.svg?branch=master)](https://travis-ci.org/KnpLabs/rad-security)

# Official maintainers:

* [@Einenlum](https://github.com/Einenlum)

# Installation

```bash
composer require knplabs/rad-security ~4.0
```

```php
// config/bundles.php

<?php

return [
    Knp\Rad\Security\Bundle\SecurityBundle::class => ['all' => true],
];
```

# Use

## IS_OWNER voter

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

## Security from routing

You can specify security constraints directly from your routing by providing a role or an array of roles with the `roles` parameter. If you specify an array, it will be passed *as is* to the authorization checker, and that means the [authorization strategy](http://symfony.com/doc/current/cookbook/security/voters.html#changing-the-access-decision-strategy) depends on your configuration of the security component.


**Example**
```yaml
acme_demo:
    path: /demo
    defaults:
        _controller: FrameworkBundle:Template:template
        template: Acme:demo:index.html.twig
        _security:
            - roles: IS_AUTHENTICATED_FULLY
```

The main advantage comes when used with the [rad-resource-resolver](https://github.com/KnpLabs/rad-resource-resolver) component & [the ParamConverter from SensioLabs](http://symfony.com/doc/current/bundles/SensioFrameworkExtraBundle/annotations/converters.html).
You can provide a `subject` previously resolved and available in the request `attributes`.
If you have many objects resolved against which you can check security constraints, you can specify many rules.

**Example**
```yaml
acme_group_update:
    path: /team/{tid}/group/{gid}/update
    defaults:
        _controller: AcmeBundle:Group:update
        template: Acme:Group:update.html.twig
        _resources:
            team:
                # ...
            group:
                # ...
        _security:
            -
                roles: [IS_MEMBER, ANOTHER_ROLE]
                subject: team
            -
                roles: IS_OWNER
                subject: group
```
