<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250424191402 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("CREATE TYPE wishitem_enum AS ENUM('MONEY', 'EVENT', 'GIFT')");
        $this->addSql('CREATE TABLE wishitem_member (id SERIAL NOT NULL, type wishitem_enum NOT NULL, data JSON NOT NULL, PRIMARY KEY(id))');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE wishitem_member');
    }
}
