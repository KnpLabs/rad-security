<?php

namespace spec\Knp\Rad\Security\Routing\Matcher;

use Exception;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Matcher\RequestMatcherInterface;

class SecurityMatcherSpec extends ObjectBehavior
{
    function let(RequestMatcherInterface $wrapped, Request $request)
    {
        $this->beConstructedWith($wrapped);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Knp\Rad\Security\Routing\Matcher\SecurityMatcher');
    }

    function it_is_a_request_matcher()
    {
        $this->shouldImplement('Symfony\Component\Routing\Matcher\RequestMatcherInterface');
    }

    function it_does_nothing_if_the_wrapped_fails($wrapped, $request)
    {
        $ex = new Exception();

        $wrapped->matchRequest($request)->willThrow($ex);

        $this->shouldThrow($ex)->duringMatchRequest($request);
    }

    function it_does_nothing_if_there_is_no_security_attribute($wrapped, $request)
    {
        $wrapped->matchRequest($request)->willReturn(['yolo']);

        $this->matchRequest($request)->shouldReturn(['yolo']);
    }
}
