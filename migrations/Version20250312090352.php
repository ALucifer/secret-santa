<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250312090352 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create secret santa table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE secret_santa (id SERIAL NOT NULL, owner_id INT NOT NULL, label VARCHAR(60) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9EB41A9B7E3C61F9 ON secret_santa (owner_id)');
        $this->addSql('ALTER TABLE secret_santa ADD CONSTRAINT FK_9EB41A9B7E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE secret_santa DROP CONSTRAINT FK_9EB41A9B7E3C61F9');
        $this->addSql('DROP TABLE secret_santa');
    }
}
