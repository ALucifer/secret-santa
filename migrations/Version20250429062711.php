<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250429062711 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add wish items member table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE wishitem_member ADD member_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE wishitem_member ADD CONSTRAINT FK_DCFF92DF7597D3FE FOREIGN KEY (member_id) REFERENCES secret_santa_member (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_DCFF92DF7597D3FE ON wishitem_member (member_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE wishitem_member DROP CONSTRAINT FK_DCFF92DF7597D3FE');
        $this->addSql('DROP INDEX IDX_DCFF92DF7597D3FE');
        $this->addSql('ALTER TABLE wishitem_member DROP member_id');
    }
}
