<?php

namespace Knp\Rad\Security\Bundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Bundle\Bundle;

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

        $container->setParameter('knp.rad.security.listener.authorization.priority', '2');

        $container
            ->register('knp.rad.security.listener.authorization', 'Knp\Rad\Security\EventListener\AuthorizationListener')
            ->addArgument(new Reference('security.authorization_checker'))
            ->addTag('kernel.event_listener', array(
                'event'    => 'kernel.controller',
                'method'   => 'checkIfUserIsGranted',
                'priority' => '%knp.rad.security.listener.authorization.priority%',
            ))
        ;
    }
}
