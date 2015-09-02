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

        if ($container->hasParameter('knp.rad.security.listener.authorization.priority')) {
            $container->setParameter('knp.rad.security.listener.authorization.priority', '2');
        }

        $container
            ->register('knp.rad.security.listener.authorization', 'Knp\Rad\Security\EventListener\AuthorizationListener')
            ->addArgument($container->get('security.authorization_checker'))
            ->addTag('kernel.event_listener', [
                'event'    => 'kernel.controller',
                'method'   => 'checkIfUserIsGranted',
                'priority' => $container->getParameter('knp.rad.security.listener.authorization.priority'),
            ])
        ;
    }
}
