<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220614124639 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'null values in minimum price field changed to default';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf(!is_a($this->connection->getDatabasePlatform(), \Doctrine\DBAL\Platforms\MySqlPlatform::class, true), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('UPDATE sylius_channel_pricing SET minimum_price = 0 WHERE minimum_price IS NULL');
    }

    public function down(Schema $schema): void
    {
    }
}
