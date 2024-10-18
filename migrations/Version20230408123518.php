<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230408123518 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE examen (id INT AUTO_INCREMENT NOT NULL, cours_id INT DEFAULT NULL, prix DOUBLE PRECISION NOT NULL, UNIQUE INDEX UNIQ_514C8FEC7ECF78B0 (cours_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE question (id INT AUTO_INCREMENT NOT NULL, examen_id INT NOT NULL, enonce LONGTEXT NOT NULL, choix1 VARCHAR(255) NOT NULL, choix2 VARCHAR(255) NOT NULL, choix3 VARCHAR(255) NOT NULL, choix4 VARCHAR(255) NOT NULL, INDEX IDX_B6F7494E5C8659A (examen_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE examen ADD CONSTRAINT FK_514C8FEC7ECF78B0 FOREIGN KEY (cours_id) REFERENCES cours (id)');
        $this->addSql('ALTER TABLE question ADD CONSTRAINT FK_B6F7494E5C8659A FOREIGN KEY (examen_id) REFERENCES examen (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE examen DROP FOREIGN KEY FK_514C8FEC7ECF78B0');
        $this->addSql('ALTER TABLE question DROP FOREIGN KEY FK_B6F7494E5C8659A');
        $this->addSql('DROP TABLE examen');
        $this->addSql('DROP TABLE question');
    }
}
