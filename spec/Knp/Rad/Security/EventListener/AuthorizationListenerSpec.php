<?php

namespace spec\Knp\Rad\Security\EventListener;

use Knp\Rad\Security\OwnableInterface;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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

    function it_checks_if_user_is_granted(
        HttpKernelInterface $kernel,
        Request $request,
        ParameterBag $attributes,
        OwnableInterface $ownable,
        $checker
    ) {
        $event = new ControllerEvent(
            $kernel->getWrappedObject(),
            function() { return null; },
            $request->getWrappedObject(),
            null
        );

        $request->attributes = $attributes;
        $attributes->get('_security', [])->willReturn([
            ['roles' => ['IS_MEMBER', 'ANOTHER_ROLE']],
            ['roles' => ['IS_OWNER'], 'subject' => 'group'],
        ]);
        $attributes->has('group')->willReturn(true);
        $attributes->get('group')->willReturn($ownable);

        $checker->isGranted('IS_MEMBER', null)->willReturn(true);
        $checker->isGranted('ANOTHER_ROLE', null)->willReturn(false);
        $checker->isGranted('IS_OWNER', $ownable)->willReturn(true);

        $this
            ->shouldNotThrow(AccessDeniedException::class)
            ->during('checkIfUserIsGranted', [$event])
        ;
    }

    function it_throws_accept_denied_http_exception_when_it_is_not_authorized(
        HttpKernelInterface $kernel,
        Request $request,
        ParameterBag $attributes,
        OwnableInterface $ownable,
        $checker
    ) {
        $event = new ControllerEvent(
            $kernel->getWrappedObject(),
            function() { return null; },
            $request->getWrappedObject(),
            null
        );

        $request->attributes = $attributes;
        $attributes->get('_security', [])->willReturn([
            ['roles' => ['IS_MEMBER']],
        ]);

        $checker->isGranted('IS_MEMBER', null)->willReturn(false);

        $this
            ->shouldThrow(AccessDeniedException::class)
            ->during('checkIfUserIsGranted', [$event])
        ;
    }

    function it_throws_an_exception_when_the_role_parameter_is_not_specified(
        HttpKernelInterface $kernel,
        Request $request,
        ParameterBag $attributes
    ) {
        $event = new ControllerEvent(
            $kernel->getWrappedObject(),
            function() { return null; },
            $request->getWrappedObject(),
            null
        );

        $request->attributes = $attributes;
        $attributes->get('_security', [])->willReturn([
            ['typo_in_role_parameter' => ''],
        ]);

        $this
            ->shouldthrow('RuntimeException')
            ->during('checkIfUserIsGranted', [$event])
        ;
    }

    function it_throws_an_exception_when_a_required_object_is_not_found(
        HttpKernelInterface $kernel,
        Request $request,
        ParameterBag $attributes
    ) {
        $event = new ControllerEvent(
            $kernel->getWrappedObject(),
            function() { return null; },
            $request->getWrappedObject(),
            null
        );

        $request->attributes = $attributes;
        $attributes->get('_security', [])->willReturn([
            ['roles' => ['IS_OWNER'], 'subject' => 'group'],
        ]);
        $attributes->has('group')->willReturn(false);

        $this
            ->shouldThrow('RuntimeException')
            ->during('checkIfUserIsGranted', [$event])
        ;
    }
}
