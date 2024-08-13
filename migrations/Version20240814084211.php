<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240814084211 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create the Book entity used for this assessment.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE book_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE book (id INT NOT NULL, isbn VARCHAR(13) NOT NULL, title VARCHAR(255) NOT NULL, author VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE book_id_seq CASCADE');
        $this->addSql('DROP TABLE book');
    }
}
