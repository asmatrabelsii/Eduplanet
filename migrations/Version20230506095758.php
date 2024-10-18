<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230506095758 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840DA76ED395 FOREIGN KEY (user_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D7ECF78B0 FOREIGN KEY (cours_id) REFERENCES cours (id)');
        $this->addSql('CREATE INDEX IDX_6D28840DA76ED395 ON payment (user_id)');
        $this->addSql('CREATE INDEX IDX_6D28840D7ECF78B0 ON payment (cours_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840DA76ED395');
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840D7ECF78B0');
        $this->addSql('DROP INDEX IDX_6D28840DA76ED395 ON payment');
        $this->addSql('DROP INDEX IDX_6D28840D7ECF78B0 ON payment');
    }
}
