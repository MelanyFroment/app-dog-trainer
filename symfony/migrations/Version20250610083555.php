<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250610083555 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE client (id SERIAL NOT NULL, educator_id INT NOT NULL, firstname VARCHAR(100) NOT NULL, lastname VARCHAR(100) NOT NULL, address VARCHAR(255) NOT NULL, postal_code VARCHAR(20) NOT NULL, city VARCHAR(100) NOT NULL, phone VARCHAR(20) NOT NULL, profil_pic VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_C7440455887E9271 ON client (educator_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE dog (id SERIAL NOT NULL, owner_id INT NOT NULL, name VARCHAR(255) NOT NULL, breed VARCHAR(255) NOT NULL, age INT NOT NULL, sex VARCHAR(255) NOT NULL, weight DOUBLE PRECISION NOT NULL, vaccinated BOOLEAN NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_812C397D7E3C61F9 ON dog (owner_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE client ADD CONSTRAINT FK_C7440455887E9271 FOREIGN KEY (educator_id) REFERENCES app_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE dog ADD CONSTRAINT FK_812C397D7E3C61F9 FOREIGN KEY (owner_id) REFERENCES app_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE client DROP CONSTRAINT FK_C7440455887E9271
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE dog DROP CONSTRAINT FK_812C397D7E3C61F9
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE client
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE dog
        SQL);
    }
}
