<?php

namespace Knp\Rad\Security\Bundle;

use Knp\Rad\Security\DependencyInjection\SecurityExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SecurityBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $this->name = 'knp_rad_security_bundle';
    }

    /**
     * {@inheritdoc}
     */
    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new SecurityExtension();
        }

        return $this->extension;
    }
}
