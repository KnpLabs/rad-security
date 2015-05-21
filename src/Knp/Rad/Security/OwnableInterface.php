<?php

namespace Knp\Rad\Security;

interface OwnableInterface
{
    /**
     * @return OwnerInterface|null
     */
    public function getOwner();
}
