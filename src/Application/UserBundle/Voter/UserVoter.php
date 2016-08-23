<?php

namespace Application\UserBundle\Voter;

use Application\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Class UserVoter
 * @package Application\UserBundle\Voter
 */
class UserVoter extends Voter
{
    const DISPLAY = "DISPLAY";

    /**
     *
     * @param string         $attribute
     * @param User           $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token = null)
    {
        if ($token === null) {
            $user = null;
        } else {
            /** @var User $user */
            $user = $token->getUser();
        }

        if ($user instanceof User and $user->isSuperAdmin()) {
            return true;
        }

        switch ($attribute) {
            case self::DISPLAY:
                return $subject instanceof User;
                break;
        }

        return null;
    }

    /**
     * Determines if the attribute and subject are supported by this voter.
     *
     * @param string $attribute An attribute
     * @param mixed  $subject   The subject to secure, e.g. an object the user wants to access or any other PHP type
     *
     * @return bool True if the attribute and subject are supported, false otherwise
     */
    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, [self::DISPLAY])) {
            return false;
        }

        return $subject instanceof User;
    }
}
