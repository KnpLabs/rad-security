<?php

namespace Knp\Rad\Security\Routing\Matcher;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Matcher\RequestMatcherInterface;

class SecurityMatcher implements RequestMatcherInterface
{
    /**
     * @var RequestMatcherInterface
     */
    private $wrapped;

    /**
     * @param RequestMatcherInterface $wrapped
     */
    public function __construct(RequestMatcherInterface $wrapped)
    {
        $this->wrapped = $wrapped;
    }

    /**
     * {@inheritdoc}
     */
    public function matchRequest(Request $request)
    {
        $ret = $this->wrapped->matchRequest($request);

        if (false === array_key_exists('_security', $ret)) {
            return $ret;
        }

        return $ret;
    }
}
