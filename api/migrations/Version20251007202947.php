<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251007202947 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE capture (id INT AUTO_INCREMENT NOT NULL, room_id INT DEFAULT NULL, type_id INT NOT NULL, value NUMERIC(6, 2) NOT NULL, description VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_8BFEA6E554177093 (room_id), INDEX IDX_8BFEA6E5C54C8C93 (type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE capture_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, description VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE room (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(15) NOT NULL, description VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE room_capture_type (room_id INT NOT NULL, capture_type_id INT NOT NULL, INDEX IDX_88902E8D54177093 (room_id), INDEX IDX_88902E8DB7E15955 (capture_type_id), PRIMARY KEY(room_id, capture_type_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE capture ADD CONSTRAINT FK_8BFEA6E554177093 FOREIGN KEY (room_id) REFERENCES room (id)');
        $this->addSql('ALTER TABLE capture ADD CONSTRAINT FK_8BFEA6E5C54C8C93 FOREIGN KEY (type_id) REFERENCES capture_type (id)');
        $this->addSql('ALTER TABLE room_capture_type ADD CONSTRAINT FK_88902E8D54177093 FOREIGN KEY (room_id) REFERENCES room (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE room_capture_type ADD CONSTRAINT FK_88902E8DB7E15955 FOREIGN KEY (capture_type_id) REFERENCES capture_type (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE capture DROP FOREIGN KEY FK_8BFEA6E554177093');
        $this->addSql('ALTER TABLE capture DROP FOREIGN KEY FK_8BFEA6E5C54C8C93');
        $this->addSql('ALTER TABLE room_capture_type DROP FOREIGN KEY FK_88902E8D54177093');
        $this->addSql('ALTER TABLE room_capture_type DROP FOREIGN KEY FK_88902E8DB7E15955');
        $this->addSql('DROP TABLE capture');
        $this->addSql('DROP TABLE capture_type');
        $this->addSql('DROP TABLE room');
        $this->addSql('DROP TABLE room_capture_type');
    }
}
