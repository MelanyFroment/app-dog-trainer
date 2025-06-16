<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250610085554 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE dog DROP CONSTRAINT FK_812C397D7E3C61F9
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE dog ADD CONSTRAINT FK_812C397D7E3C61F9 FOREIGN KEY (owner_id) REFERENCES client (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE dog DROP CONSTRAINT fk_812c397d7e3c61f9
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE dog ADD CONSTRAINT fk_812c397d7e3c61f9 FOREIGN KEY (owner_id) REFERENCES app_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }
}
