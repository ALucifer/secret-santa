<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250718081253 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE wishitem_member DROP CONSTRAINT fk_dcff92df7597d3fe');
        $this->addSql('DROP SEQUENCE secret_santa_member_id_seq CASCADE');
        $this->addSql('CREATE TABLE member (id SERIAL NOT NULL, secret_santa_id INT DEFAULT NULL, user_id INT DEFAULT NULL, santa_id INT DEFAULT NULL, state VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_70E4FA78EB261FF1 ON member (secret_santa_id)');
        $this->addSql('CREATE INDEX IDX_70E4FA78A76ED395 ON member (user_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_70E4FA784E0AAA2A ON member (santa_id)');
        $this->addSql('ALTER TABLE member ADD CONSTRAINT FK_70E4FA78EB261FF1 FOREIGN KEY (secret_santa_id) REFERENCES secret_santa (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE member ADD CONSTRAINT FK_70E4FA78A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE member ADD CONSTRAINT FK_70E4FA784E0AAA2A FOREIGN KEY (santa_id) REFERENCES member (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE secret_santa_member DROP CONSTRAINT fk_7e8d2683eb261ff1');
        $this->addSql('ALTER TABLE secret_santa_member DROP CONSTRAINT fk_7e8d2683a76ed395');
        $this->addSql('ALTER TABLE secret_santa_member DROP CONSTRAINT fk_7e8d26834e0aaa2a');
        $this->addSql('DROP TABLE secret_santa_member');
        $this->addSql('ALTER TABLE wishitem_member ADD CONSTRAINT FK_DCFF92DF7597D3FE FOREIGN KEY (member_id) REFERENCES member (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE wishitem_member DROP CONSTRAINT FK_DCFF92DF7597D3FE');
        $this->addSql('CREATE SEQUENCE secret_santa_member_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE secret_santa_member (id SERIAL NOT NULL, secret_santa_id INT DEFAULT NULL, user_id INT DEFAULT NULL, santa_id INT DEFAULT NULL, state VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX uniq_7e8d26834e0aaa2a ON secret_santa_member (santa_id)');
        $this->addSql('CREATE INDEX idx_7e8d2683a76ed395 ON secret_santa_member (user_id)');
        $this->addSql('CREATE INDEX idx_7e8d2683eb261ff1 ON secret_santa_member (secret_santa_id)');
        $this->addSql('ALTER TABLE secret_santa_member ADD CONSTRAINT fk_7e8d2683eb261ff1 FOREIGN KEY (secret_santa_id) REFERENCES secret_santa (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE secret_santa_member ADD CONSTRAINT fk_7e8d2683a76ed395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE secret_santa_member ADD CONSTRAINT fk_7e8d26834e0aaa2a FOREIGN KEY (santa_id) REFERENCES secret_santa_member (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE member DROP CONSTRAINT FK_70E4FA78EB261FF1');
        $this->addSql('ALTER TABLE member DROP CONSTRAINT FK_70E4FA78A76ED395');
        $this->addSql('ALTER TABLE member DROP CONSTRAINT FK_70E4FA784E0AAA2A');
        $this->addSql('DROP TABLE member');
        $this->addSql('ALTER TABLE wishitem_member DROP CONSTRAINT fk_dcff92df7597d3fe');
        $this->addSql('ALTER TABLE wishitem_member ADD CONSTRAINT fk_dcff92df7597d3fe FOREIGN KEY (member_id) REFERENCES secret_santa_member (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
