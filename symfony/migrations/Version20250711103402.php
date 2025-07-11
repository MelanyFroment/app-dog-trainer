<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250711103402 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE client (id SERIAL NOT NULL, company_id INT NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, postal_code VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, profil_pic VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_C7440455979B1AD6 ON client (company_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE company (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, postcode VARCHAR(255) NOT NULL, information VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE dog (id SERIAL NOT NULL, client_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, breed VARCHAR(255) NOT NULL, age VARCHAR(255) NOT NULL, profil_pic VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_812C397D19EB6921 ON dog (client_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE schedule (id SERIAL NOT NULL, educator_id INT DEFAULT NULL, date DATE NOT NULL, start_time TIME(0) WITHOUT TIME ZONE NOT NULL, end_time TIME(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_5A3811FB887E9271 ON schedule (educator_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE session (id SERIAL NOT NULL, educator_id INT NOT NULL, schedule_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description TEXT NOT NULL, date DATE NOT NULL, start_time TIME(0) WITHOUT TIME ZONE NOT NULL, end_time TIME(0) WITHOUT TIME ZONE NOT NULL, location VARCHAR(255) NOT NULL, timeslots VARCHAR(255) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_D044D5D4887E9271 ON session (educator_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_D044D5D4A40BC2D5 ON session (schedule_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE session_dog (session_id INT NOT NULL, dog_id INT NOT NULL, PRIMARY KEY(session_id, dog_id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_FCF5A147613FECDF ON session_dog (session_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_FCF5A147634DFEB ON session_dog (dog_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE client ADD CONSTRAINT FK_C7440455979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE dog ADD CONSTRAINT FK_812C397D19EB6921 FOREIGN KEY (client_id) REFERENCES client (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE schedule ADD CONSTRAINT FK_5A3811FB887E9271 FOREIGN KEY (educator_id) REFERENCES app_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE session ADD CONSTRAINT FK_D044D5D4887E9271 FOREIGN KEY (educator_id) REFERENCES app_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE session ADD CONSTRAINT FK_D044D5D4A40BC2D5 FOREIGN KEY (schedule_id) REFERENCES schedule (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE session_dog ADD CONSTRAINT FK_FCF5A147613FECDF FOREIGN KEY (session_id) REFERENCES session (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE session_dog ADD CONSTRAINT FK_FCF5A147634DFEB FOREIGN KEY (dog_id) REFERENCES dog (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE app_user ADD company_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE app_user ADD CONSTRAINT FK_88BDF3E9979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_88BDF3E9979B1AD6 ON app_user (company_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE app_user DROP CONSTRAINT FK_88BDF3E9979B1AD6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE client DROP CONSTRAINT FK_C7440455979B1AD6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE dog DROP CONSTRAINT FK_812C397D19EB6921
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE schedule DROP CONSTRAINT FK_5A3811FB887E9271
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE session DROP CONSTRAINT FK_D044D5D4887E9271
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE session DROP CONSTRAINT FK_D044D5D4A40BC2D5
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE session_dog DROP CONSTRAINT FK_FCF5A147613FECDF
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE session_dog DROP CONSTRAINT FK_FCF5A147634DFEB
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE client
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE company
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE dog
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE schedule
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE session
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE session_dog
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_88BDF3E9979B1AD6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE app_user DROP company_id
        SQL);
    }
}
