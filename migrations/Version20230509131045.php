<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230509131045 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE payment_examen (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, examen_id INT NOT NULL, prix DOUBLE PRECISION NOT NULL, reference VARCHAR(255) NOT NULL, stripe_token VARCHAR(255) DEFAULT NULL, status_stripe VARCHAR(255) DEFAULT NULL, updated_at DATE NOT NULL, created_at DATE NOT NULL, INDEX IDX_7474147AA76ED395 (user_id), INDEX IDX_7474147A5C8659A (examen_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE payment_examen ADD CONSTRAINT FK_7474147AA76ED395 FOREIGN KEY (user_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE payment_examen ADD CONSTRAINT FK_7474147A5C8659A FOREIGN KEY (examen_id) REFERENCES examen (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE payment_examen DROP FOREIGN KEY FK_7474147AA76ED395');
        $this->addSql('ALTER TABLE payment_examen DROP FOREIGN KEY FK_7474147A5C8659A');
        $this->addSql('DROP TABLE payment_examen');
    }
}
