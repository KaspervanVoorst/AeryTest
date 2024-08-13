<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240815112907 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add a uniqueness constraint to an entry\'s ISBN';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE UNIQUE INDEX isbn_unique ON book (isbn)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX isbn_unique');
    }
}
