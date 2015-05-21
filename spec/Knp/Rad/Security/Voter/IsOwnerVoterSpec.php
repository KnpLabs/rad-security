<?php

namespace spec\Knp\Rad\Security\Voter;

use PhpSpec\ObjectBehavior;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Knp\Rad\Security\OwnerInterface;
use Knp\Rad\Security\OwnableInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;

class IsOwnerVoterSpec extends ObjectBehavior
{
    function it_is_a_security_voter()
    {
        $this->shouldHaveType('Symfony\Component\Security\Core\Authorization\Voter\VoterInterface');
    }

    function it_should_only_support_IS_OWNER_attribute()
    {
        $this->supportsAttribute('IS_OWNER')->shouldReturn(true);
        $this->supportsAttribute('IS_SOMETHING_ELSE')->shouldReturn(false);
    }

    function it_grants_access_to_owned_object(TokenInterface $token, OwnerInterface $user, OwnableInterface $object)
    {
        $token->getUser()->willReturn($user);
        $object->getOwner()->willReturn($user);

        $this->vote($token, $object, array('IS_OWNER'))->shouldReturn(VoterInterface::ACCESS_GRANTED);
    }

    function it_denies_access_to_not_owned_object(
        TokenInterface $token,
        OwnerInterface $user,
        OwnerInterface $otherUser,
        OwnableInterface $object
    ) {
        $token->getUser()->willReturn($user);
        $object->getOwner()->willReturn($otherUser);

        $this->vote($token, $object, array('IS_OWNER'))->shouldReturn(VoterInterface::ACCESS_DENIED);
    }

    function it_abstains_to_vote_for_not_ownable_object(
        TokenInterface $token,
        UserInterface $user,
        OwnableInterface $object
    ) {
        $token->getUser()->willReturn($user);

        $this->vote($token, $object, array('IS_OWNER'))->shouldReturn(VoterInterface::ACCESS_ABSTAIN);
    }

    function it_abstains_to_vote_for_not_owner_user_token(
        TokenInterface $token,
        UserInterface $user,
        OwnableInterface $object
    ) {
        $token->getUser()->willReturn($user);
        $object->getOwner()->willReturn($user);

        $this->vote($token, $object, array('IS_OWNER'))->shouldReturn(VoterInterface::ACCESS_ABSTAIN);
    }

    function it_abstains_to_vote_for_unknown_attribute(
        TokenInterface $token,
        OwnerInterface $user,
        OwnableInterface $object
    ) {
        $token->getUser()->willReturn($user);
        $object->getOwner()->willReturn($user);

        $this->vote($token, $object, array('IS_TEST'))->shouldReturn(VoterInterface::ACCESS_ABSTAIN);
    }

    function it_grants_access_to_equal_owners(
        TokenInterface $token,
        UserInterface $user,
        OwnableInterface $object,
        UserInterface $equatableUser
    ) {
        $user->implement('Knp\Rad\Security\OwnerInterface');
        $user->implement('Symfony\Component\Security\Core\User\EquatableInterface');

        $token->getUser()->willReturn($user);
        $object->getOwner()->willReturn($equatableUser);
        $user->isEqualTo($equatableUser)->willReturn(true);

        $this->vote($token, $object, array('IS_OWNER'))->shouldReturn(VoterInterface::ACCESS_GRANTED);
    }

    function it_denies_access_to_not_equal_owners(
        TokenInterface $token,
        UserInterface $user,
        OwnableInterface $object,
        UserInterface $equatableUser
    ) {
        $user->implement('Knp\Rad\Security\OwnerInterface');
        $user->implement('Symfony\Component\Security\Core\User\EquatableInterface');

        $token->getUser()->willReturn($user);
        $object->getOwner()->willReturn($equatableUser);
        $user->isEqualTo($equatableUser)->willReturn(false);

        $this->vote($token, $object, array('IS_OWNER'))->shouldReturn(VoterInterface::ACCESS_DENIED);
    }
}
