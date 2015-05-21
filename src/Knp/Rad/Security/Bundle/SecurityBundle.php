<?php

namespace Knp\Rad\Security\Bundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SecurityBundle extends Bundle
{
    public function __construct()
    {
        $this->name = 'knp_rad_security_bundle';
    }

    public function build(ContainerBuilder $container)
    {
        $container
            ->register('knp.rad.security.voter.is_owner', 'Knp\Rad\Security\Voter\IsOwnerVoter')
            ->addTag('security.voter')
        ;
    }
}
