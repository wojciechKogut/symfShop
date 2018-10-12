<?php

    namespace App\Security;


    use App\Entity\Category;
    use App\Entity\User;
    use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
    use Symfony\Component\Security\Core\Authorization\Voter\Voter;

    class CategoryVoter extends Voter
    {
        const CREATE = 'create';
        const VIEW = 'view';
        const EDIT = 'edit';
        const DELETE = 'delete';

        /**
         * @param string $attribute
         * @param mixed $subject
         * @return bool
         */
        protected function supports($attribute, $subject)
        {
            // if the attribute isn't one we support, return false
            if (!in_array($attribute, array(self::EDIT, self::VIEW, self::CREATE, self::DELETE))) {
                return false;
            }

            // only vote on Category objects inside this voter
            if (!$subject instanceof Category) {
                return false;
            }

            return true;
        }

        /**
         * @param string $attribute
         * @param mixed $subject
         * @param TokenInterface $token
         * @return bool
         */
        protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
        {
            $user = $token->getUser();

            if (!$user instanceof User) {
                // the user must be logged in; if not, deny access
                return false;
            }

            return true;
        }
    }