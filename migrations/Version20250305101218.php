<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250305101218 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add user id to token table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE token ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE token ADD CONSTRAINT FK_5F37A13BA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_5F37A13BA76ED395 ON token (user_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE token DROP CONSTRAINT FK_5F37A13BA76ED395');
        $this->addSql('DROP INDEX IDX_5F37A13BA76ED395');
        $this->addSql('ALTER TABLE token DROP user_id');
    }
}
