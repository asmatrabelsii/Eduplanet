<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230520103949 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE certification ADD exam_id INT NOT NULL');
        $this->addSql('ALTER TABLE certification ADD CONSTRAINT FK_6C3C6D75578D5E91 FOREIGN KEY (exam_id) REFERENCES examen (id)');
        $this->addSql('CREATE INDEX IDX_6C3C6D75578D5E91 ON certification (exam_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE certification DROP FOREIGN KEY FK_6C3C6D75578D5E91');
        $this->addSql('DROP INDEX IDX_6C3C6D75578D5E91 ON certification');
        $this->addSql('ALTER TABLE certification DROP exam_id');
    }
}
