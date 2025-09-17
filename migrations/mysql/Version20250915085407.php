<?php

declare(strict_types=1);

namespace mysql;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250915085407 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE member (id INT AUTO_INCREMENT NOT NULL, state VARCHAR(255) NOT NULL, secret_santa_id INT DEFAULT NULL, user_id INT DEFAULT NULL, santa_id INT DEFAULT NULL, INDEX IDX_70E4FA78EB261FF1 (secret_santa_id), INDEX IDX_70E4FA78A76ED395 (user_id), UNIQUE INDEX UNIQ_70E4FA784E0AAA2A (santa_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE secret_santa (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(60) NOT NULL, state VARCHAR(255) NOT NULL, owner_id INT NOT NULL, INDEX IDX_9EB41A9B7E3C61F9 (owner_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE task (id INT AUTO_INCREMENT NOT NULL, state enum("PENDING", "SUCCESS", "FAILURE") NOT NULL, created_at DATETIME NOT NULL, data JSON NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE token (id INT AUTO_INCREMENT NOT NULL, token VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT NULL, valid_until DATETIME NOT NULL, user_id INT DEFAULT NULL, INDEX IDX_5F37A13BA76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, is_verified TINYINT(1) DEFAULT 0 NOT NULL, password VARCHAR(255) NOT NULL, is_invited TINYINT(1) DEFAULT 0 NOT NULL, last_activity DATETIME DEFAULT NULL, pseudo VARCHAR(50) DEFAULT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE wishitem (id INT AUTO_INCREMENT NOT NULL, type enum("GIFT", "MONEY", "EVENT") NOT NULL, data JSON NOT NULL, member_id INT DEFAULT NULL, INDEX IDX_C732F7377597D3FE (member_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE member ADD CONSTRAINT FK_70E4FA78EB261FF1 FOREIGN KEY (secret_santa_id) REFERENCES secret_santa (id)');
        $this->addSql('ALTER TABLE member ADD CONSTRAINT FK_70E4FA78A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE member ADD CONSTRAINT FK_70E4FA784E0AAA2A FOREIGN KEY (santa_id) REFERENCES member (id)');
        $this->addSql('ALTER TABLE secret_santa ADD CONSTRAINT FK_9EB41A9B7E3C61F9 FOREIGN KEY (owner_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE token ADD CONSTRAINT FK_5F37A13BA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE wishitem ADD CONSTRAINT FK_C732F7377597D3FE FOREIGN KEY (member_id) REFERENCES member (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE member DROP FOREIGN KEY FK_70E4FA78EB261FF1');
        $this->addSql('ALTER TABLE member DROP FOREIGN KEY FK_70E4FA78A76ED395');
        $this->addSql('ALTER TABLE member DROP FOREIGN KEY FK_70E4FA784E0AAA2A');
        $this->addSql('ALTER TABLE secret_santa DROP FOREIGN KEY FK_9EB41A9B7E3C61F9');
        $this->addSql('ALTER TABLE token DROP FOREIGN KEY FK_5F37A13BA76ED395');
        $this->addSql('ALTER TABLE wishitem DROP FOREIGN KEY FK_C732F7377597D3FE');
        $this->addSql('DROP TABLE member');
        $this->addSql('DROP TABLE secret_santa');
        $this->addSql('DROP TABLE task');
        $this->addSql('DROP TABLE token');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE wishitem');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
