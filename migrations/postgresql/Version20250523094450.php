<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250523094450 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add workflow on secret santa to start it';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE secret_santa ADD state VARCHAR(255) NULL');
        $this->addSql('UPDATE secret_santa SET state = \'standby\'');
        $this->addSql('ALTER TABLE secret_santa ALTER COLUMN state SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE secret_santa DROP state');
    }
}
