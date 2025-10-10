<?php

namespace App\Services\UserRequirements;

use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

class UserRequirementsHandler
{
    /**
     * @param iterable<UserRequirementsRedirectStrategyInterface> $items
     */
    public function __construct(
        #[AutowireIterator('app.user.requirements.redirect.strategy')]
        private iterable $items,
        private Security $security,
    ) {
    }

    public function handle(): UserRequirementsFlag
    {
        $user = $this->security->getUser();

        if (!$user && !$user instanceof User) {
            throw new \LogicException('User cannot be null.');
        }

        $requirements = new UserRequirementsFlag();

        foreach ($this->items as $item) {
            if ($item->supports($user)) {
                $flag = $item->getFlag();

                is_array($flag) ? $requirements->addMany($flag) : $requirements->add($flag);
            }
        }

        return $requirements;
    }
}
