<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250320194440 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE secret_santa_member (id SERIAL NOT NULL, secret_santa_id INT DEFAULT NULL, user_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7E8D2683EB261FF1 ON secret_santa_member (secret_santa_id)');
        $this->addSql('CREATE INDEX IDX_7E8D2683A76ED395 ON secret_santa_member (user_id)');
        $this->addSql('ALTER TABLE secret_santa_member ADD CONSTRAINT FK_7E8D2683EB261FF1 FOREIGN KEY (secret_santa_id) REFERENCES secret_santa (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE secret_santa_member ADD CONSTRAINT FK_7E8D2683A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE secret_santa_member DROP CONSTRAINT FK_7E8D2683EB261FF1');
        $this->addSql('ALTER TABLE secret_santa_member DROP CONSTRAINT FK_7E8D2683A76ED395');
        $this->addSql('DROP TABLE secret_santa_member');
    }
}
