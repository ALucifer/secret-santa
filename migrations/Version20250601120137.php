<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250601120137 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add last activity for user logged in and pseudo';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE "user" ADD last_activity TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD pseudo VARCHAR(50) DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN "user".last_activity IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE "user" DROP last_activity');
        $this->addSql('ALTER TABLE "user" DROP pseudo');
    }
}
