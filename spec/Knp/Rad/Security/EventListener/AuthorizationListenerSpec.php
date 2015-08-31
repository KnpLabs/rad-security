<?php

namespace spec\Knp\Rad\Security\EventListener;

use Knp\Rad\Security\OwnableInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class AuthorizationListenerSpec extends ObjectBehavior
{
    function let(AuthorizationCheckerInterface $checker)
    {
        $this->beConstructedWith($checker);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Knp\Rad\Security\EventListener\AuthorizationListener');
    }

    function it_checks_if_user_is_granted(FilterControllerEvent $event, Request $request, ParameterBag $attributes, OwnableInterface $ownable, $checker)
    {
        $event->getRequest()->willReturn($request);
        $request->attributes = $attributes;
        $attributes->get('_security', array())->willReturn(array(
            array('roles' => array('IS_MEMBER', 'ANOTHER_ROLE')),
            array('roles' => array('IS_OWNER'), 'subject' => 'group')
        ));
        $attributes->has('group')->willReturn(true);
        $attributes->get('group')->willReturn($ownable);

        $checker->isGranted(array('IS_MEMBER', 'ANOTHER_ROLE'), null)->willReturn(true);
        $checker->isGranted(array('IS_OWNER'), $ownable)->willReturn(true);

        $this->checkIfUserIsGranted($event);
    }

    function it_throws_accept_denied_http_exception_when_it_is_not_authorized(FilterControllerEvent $event, Request $request, ParameterBag $attributes, OwnableInterface $ownable, $checker)
    {
        $event->getRequest()->willReturn($request);
        $request->attributes = $attributes;
        $attributes->get('_security', array())->willReturn(array(
            array('roles' => array('IS_MEMBER')),
        ));

        $checker->isGranted(array('IS_MEMBER'), null)->willReturn(false);

        $this
            ->shouldThrow('Symfony\Component\Security\Core\Exception\AccessDeniedException')
            ->during('checkIfUserIsGranted', array($event))
        ;
    }

    function it_throws_an_exception_when_the_role_parameter_is_not_specified(FilterControllerEvent $event, Request $request, ParameterBag $attributes)
    {
        $event->getRequest()->willReturn($request);
        $request->attributes = $attributes;
        $attributes->get('_security', array())->willReturn(array(
            array('typo_in_role_parameter' => ''),
        ));

        $this->shouldthrow('RuntimeException')->during('checkIfUserIsGranted', array($event));
    }

    function it_throws_an_exception_when_a_required_object_is_not_found(FilterControllerEvent $event, Request $request, ParameterBag $attributes)
    {
        $event->getRequest()->willReturn($request);
        $request->attributes = $attributes;
        $attributes->get('_security', array())->willReturn(array(
            array('roles' => array('IS_OWNER'), 'subject' => 'group')
        ));
        $attributes->has('group')->willReturn(false);

        $this->shouldThrow('RuntimeException')->during('checkIfUserIsGranted', array($event));
    }
}
