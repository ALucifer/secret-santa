<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250720133723 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Rename wishitem_member to wishitem';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE wishitem_member_id_seq CASCADE');
        $this->addSql('CREATE TABLE wishitem (id SERIAL NOT NULL, member_id INT DEFAULT NULL, type wishitem_enum NOT NULL, data JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C732F7377597D3FE ON wishitem (member_id)');
        $this->addSql('ALTER TABLE wishitem ADD CONSTRAINT FK_C732F7377597D3FE FOREIGN KEY (member_id) REFERENCES member (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE wishitem_member DROP CONSTRAINT fk_dcff92df7597d3fe');
        $this->addSql('DROP TABLE wishitem_member');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE wishitem_member_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE wishitem_member (id SERIAL NOT NULL, member_id INT DEFAULT NULL, type wishitem_enum NOT NULL, data JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_dcff92df7597d3fe ON wishitem_member (member_id)');
        $this->addSql('ALTER TABLE wishitem_member ADD CONSTRAINT fk_dcff92df7597d3fe FOREIGN KEY (member_id) REFERENCES member (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE wishitem DROP CONSTRAINT FK_C732F7377597D3FE');
        $this->addSql('DROP TABLE wishitem');
    }
}
