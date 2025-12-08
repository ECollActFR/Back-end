<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251130221649 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create client_account table and add relation to user table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE client_account (id INT AUTO_INCREMENT NOT NULL, company_name VARCHAR(255) NOT NULL, siret VARCHAR(14) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, city VARCHAR(100) DEFAULT NULL, postal_code VARCHAR(10) DEFAULT NULL, country VARCHAR(100) DEFAULT NULL, phone VARCHAR(20) DEFAULT NULL, contact_email VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, is_active TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user ADD client_account_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649B3F62FB FOREIGN KEY (client_account_id) REFERENCES client_account (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649B3F62FB ON user (client_account_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649B3F62FB');
        $this->addSql('DROP TABLE client_account');
        $this->addSql('DROP INDEX IDX_8D93D649B3F62FB ON user');
        $this->addSql('ALTER TABLE user DROP client_account_id');
    }
}
