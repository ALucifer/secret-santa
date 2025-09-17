<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250429130326 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create task table for async taks';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("CREATE TYPE state_enum AS ENUM('PENDING','SUCCESS', 'FAILURE')");
        $this->addSql('CREATE TABLE task (id SERIAL NOT NULL, state state_enum NOT NULL, data JSON NOT NULL, PRIMARY KEY(id))');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE task');
    }
}
