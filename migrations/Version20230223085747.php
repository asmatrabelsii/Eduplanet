<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230223085747 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cours ADD cathegorie_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE cours ADD CONSTRAINT FK_FDCA8C9C75654620 FOREIGN KEY (cathegorie_id) REFERENCES cathegories (id)');
        $this->addSql('CREATE INDEX IDX_FDCA8C9C75654620 ON cours (cathegorie_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cours DROP FOREIGN KEY FK_FDCA8C9C75654620');
        $this->addSql('DROP INDEX IDX_FDCA8C9C75654620 ON cours');
        $this->addSql('ALTER TABLE cours DROP cathegorie_id');
    }
}
