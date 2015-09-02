<?php

namespace Knp\Rad\Security\Voter;

use Knp\Rad\Security\OwnableInterface;
use Knp\Rad\Security\OwnerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class IsOwnerVoter implements VoterInterface
{
    const IS_OWNER = 'IS_OWNER';

    /**
     * {@inheritdoc}
     */
    public function supportsAttribute($attribute)
    {
        return self::IS_OWNER === $attribute;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        return is_subclass_of($class, 'Knp\Rad\Security\OwnableInterface');
    }

    /**
     * {@inheritdoc}
     */
    public function vote(TokenInterface $token, $object, array $attributes)
    {
        $class = true === is_object($object) ? get_class($object) : $object;

        foreach ($attributes as $attribute) {
            if (false === $this->supportsAttribute($attribute)) {
                continue;
            }

            if (false === $this->supportsClass($class)) {
                return self::ACCESS_ABSTAIN;
            }

            if (false === $token->getUser() instanceof OwnerInterface) {
                return self::ACCESS_ABSTAIN;
            }

            if (true === $this->isOwner($token->getUser(), $object)) {
                return self::ACCESS_GRANTED;
            }

            return self::ACCESS_DENIED;
        }

        return self::ACCESS_ABSTAIN;
    }

    /**
     * @param OwnerInterface $owner
     * @param OwnableInterface $ownable
     *
     * @return boolean
     */
    private function isOwner(OwnerInterface $owner, OwnableInterface $ownable)
    {
        if (true === $ownable->getOwner() instanceof UserInterface && true === $owner instanceof EquatableInterface) {
            return $owner->isEqualTo($ownable->getOwner());
        }

        return $ownable->getOwner() === $owner;
    }
}
