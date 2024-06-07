<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240606134249 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add deleted_at column to your_entity table';
    }

    public function up(Schema $schema): void
    {
        // This is just an example. Make sure to use the correct table and column names.
        $this->addSql('ALTER TABLE task ADD deleted_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // This will reverse the migration
        $this->addSql('ALTER TABLE task DROP deleted_at');
    }
}

