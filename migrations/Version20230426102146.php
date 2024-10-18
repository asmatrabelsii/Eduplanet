<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230426102146 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE panier (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, UNIQUE INDEX UNIQ_24CC0DF27E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE panier_cours (panier_id INT NOT NULL, cours_id INT NOT NULL, INDEX IDX_D4E7A321F77D927C (panier_id), INDEX IDX_D4E7A3217ECF78B0 (cours_id), PRIMARY KEY(panier_id, cours_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE panier ADD CONSTRAINT FK_24CC0DF27E3C61F9 FOREIGN KEY (owner_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE panier_cours ADD CONSTRAINT FK_D4E7A321F77D927C FOREIGN KEY (panier_id) REFERENCES panier (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE panier_cours ADD CONSTRAINT FK_D4E7A3217ECF78B0 FOREIGN KEY (cours_id) REFERENCES cours (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE panier DROP FOREIGN KEY FK_24CC0DF27E3C61F9');
        $this->addSql('ALTER TABLE panier_cours DROP FOREIGN KEY FK_D4E7A321F77D927C');
        $this->addSql('ALTER TABLE panier_cours DROP FOREIGN KEY FK_D4E7A3217ECF78B0');
        $this->addSql('DROP TABLE panier');
        $this->addSql('DROP TABLE panier_cours');
    }
}
