<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Sylius\Bundle\CoreBundle\Doctrine\Migrations\AbstractMigration;

final class Version20220728115129 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add shipping_address_in_checkout_required field to channel';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_channel ADD shipping_address_in_checkout_required TINYINT(1) DEFAULT 0 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_channel DROP shipping_address_in_checkout_required');
    }
}
