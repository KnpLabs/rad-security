<?php

namespace Knp\Rad\Security\EventListener;

use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AuthorizationListener
{
    /**
     * @var AuthorizationCheckerInterface
     */
    private $checker;

    /**
     * @param AuthorizationCheckerInterface $checker
     */
    public function __construct(AuthorizationCheckerInterface $checker)
    {
        $this->checker = $checker;
    }

    public function checkIfUserIsGranted(ControllerEvent $event)
    {
        $request = $event->getRequest();

        foreach ($request->attributes->get('_security', []) as $rule) {
            $roles = [];

            if (isset($rule['roles']) && ! empty($rule['roles'])) {
                $roles = $rule['roles'];
            } else {
                throw new \RuntimeException('You should provide "roles" parameter.');
            }

            if (is_string($roles)) {
                $roles = [$roles];
            }

            $subject     = null;
            $subjectName = isset($rule['subject']) ? $rule['subject'] : null;

            if ( ! empty($subjectName)) {
                if ( ! $request->attributes->has($subjectName)) {
                    throw new \RuntimeException(
                        sprintf("Subject '%s' is not available in the request attributes.", $subjectName)
                    );
                }

                $subject = $request->attributes->get($subjectName);
            }

            $authorized = false;

            foreach ($roles as $role) {
                if ($this->checker->isGranted($role, $subject)) {
                    $authorized = true;
                }
            }

            if ( ! $authorized) {
                throw new AccessDeniedException();
            }
        }
    }
}
