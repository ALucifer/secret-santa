<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250902070410 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add created_at column to task';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE task ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE NULL');
        $this->addSql('UPDATE task SET created_at = NOW()');
        $this->addSql('ALTER TABLE task ALTER COLUMN created_at SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE task DROP created_at');
    }
}
