<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250523150754 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add santa';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE secret_santa_member ADD santa_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE secret_santa_member ADD CONSTRAINT FK_7E8D26834E0AAA2A FOREIGN KEY (santa_id) REFERENCES secret_santa_member (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7E8D26834E0AAA2A ON secret_santa_member (santa_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE secret_santa_member DROP CONSTRAINT FK_7E8D26834E0AAA2A');
        $this->addSql('DROP INDEX UNIQ_7E8D26834E0AAA2A');
        $this->addSql('ALTER TABLE secret_santa_member DROP santa_id');
    }
}
