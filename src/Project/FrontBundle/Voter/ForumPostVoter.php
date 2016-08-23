<?php

namespace Project\FrontBundle\Voter;

use Application\UserBundle\Entity\User;
use Project\FrontBundle\Entity\ForumPost;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\RoleHierarchyVoter;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * Class ForumPostVoter
 * @package Project\FrontBundle\Voter
 */
class ForumPostVoter extends Voter
{
    const EDIT = "EDIT";

    /**
     * @var RoleHierarchyVoter
     */
    private $roleHierarchyVoter;
    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    /**
     * ExhibitionVoter constructor.
     *
     * @param RoleHierarchyVoter $roleHierarchyVoter
     * @param TokenStorage $tokenStorage
     */
    public function __construct(RoleHierarchyVoter $roleHierarchyVoter, TokenStorage $tokenStorage)
    {
        $this->roleHierarchyVoter = $roleHierarchyVoter;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Perform a single access check operation on a given attribute, object and (optionally) user
     * It is safe to assume that $attribute and $object's class pass supportsAttribute/supportsClass
     * $user can be one of the following:
     *   a UserInterface object (fully authenticated user)
     *   a string               (anonymously authenticated user).
     *
     * @param string $attribute
     * @param object $object
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $object, TokenInterface $token = null)
    {
        if ($token === null) {
            $user = null;
        } else {
            /** @var User $user */
            $user = $token->getUser();
        }
        // Allow admin
        if ($user instanceof User and $user->isSuperAdmin()) {
            return true;
        }

        /** @var ForumPost $object */
        switch ($attribute) {

            // Editable from admin
            case self::EDIT:
                if (false === $user instanceof User) {
                    return false;
                }

                $valid =
                    $object->getUser() === $user
                    &&
                    $object->getCreatedAt() > new \DateTime('-30 minute');

                return $valid;
        }

        return null;
    }

    /**
     * Determines if the attribute and subject are supported by this voter.
     *
     * @param string $attribute An attribute
     * @param mixed $subject The subject to secure, e.g. an object the user wants to access or any other PHP type
     *
     * @return bool True if the attribute and subject are supported, false otherwise
     */
    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, [self::EDIT])) {
            return false;
        }

        return $subject instanceof ForumPost;
    }

    /**
     * Tells if the connected use has role
     *
     * @param string $role ROLE_XX
     * @param TokenInterface $token
     *
     * @return bool true if granted, false otherwise
     */
    private function userHasRole($role, TokenInterface $token = null)
    {
        if ($token === null || $token->getUser() == null) {
            return false;
        }

        return $this->roleHierarchyVoter->vote($token, $token->getUser(), [$role]) === VoterInterface::ACCESS_GRANTED;
    }
}
