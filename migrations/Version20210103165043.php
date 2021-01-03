<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210103165043 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE accounting (id INT AUTO_INCREMENT NOT NULL, category_accounting_id INT DEFAULT NULL, libelle VARCHAR(255) NOT NULL, date DATE NOT NULL, prix NUMERIC(10, 2) NOT NULL, INDEX IDX_6DC501E525A6F523 (category_accounting_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category_accounting (id INT AUTO_INCREMENT NOT NULL, categorie VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE accounting ADD CONSTRAINT FK_6DC501E525A6F523 FOREIGN KEY (category_accounting_id) REFERENCES category_accounting (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE accounting DROP FOREIGN KEY FK_6DC501E525A6F523');
        $this->addSql('DROP TABLE accounting');
        $this->addSql('DROP TABLE category_accounting');
    }
}
