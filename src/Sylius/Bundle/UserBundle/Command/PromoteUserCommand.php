<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\UserBundle\Command;

use Sylius\Component\Core\Model\UserInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Loïc Frémont <loic@mobizel.com>
 */
class PromoteUserCommand extends AbstractRoleCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('sylius:user:promote')
            ->setDescription('Promotes a user by adding a role.')
            ->setDefinition(array(
                new InputArgument('email', InputArgument::REQUIRED, 'The email'),
                new InputArgument('role', InputArgument::REQUIRED, 'The role'),
            ))
            ->setHelp(<<<EOT
The <info>sylius:user:promote</info> command promotes a user by adding a role

  <info>php app/console fos:user:promote matthieu@email.com ROLE_CUSTOM</info>
EOT
            );
    }

    /**
     * @inheritdoc
     */
    public function executeRoleCommand(OutputInterface $output, UserInterface $user, $role)
    {
        if ($user->hasRole($role)) {
            $output->writeln(sprintf('<error>User "%s" did already have "%s" role.</error>', (string)$user, $role));
        } else {
            $this->promoteUser($user, $role);
            $this->getEntityManager()->flush();

            $output->writeln(sprintf('Role <comment>%s</comment> has been added to user <comment>%s</comment>', $role, (string)$user));
        }
    }

    /**
     * @param UserInterface $user
     * @param string $role
     */
    protected function promoteUser(UserInterface $user, $role)
    {
        $user->addRole($role);
    }
}
