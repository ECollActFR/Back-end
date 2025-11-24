<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251124194948 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Remove MAC address and IP address fields from acquisition_system and device_network_config tables';
    }

    public function up(Schema $schema): void
    {
        // Drop unique index on acquisition_system.mac_address
        $this->addSql('DROP INDEX UNIQ_13C61622B728E969 ON acquisition_system');

        // Drop mac_address column from acquisition_system
        $this->addSql('ALTER TABLE acquisition_system DROP mac_address');

        // Drop ip_address and wifi_mac_address columns from device_network_config
        $this->addSql('ALTER TABLE device_network_config DROP ip_address');
        $this->addSql('ALTER TABLE device_network_config DROP wifi_mac_address');
    }

    public function down(Schema $schema): void
    {
        // Re-add mac_address column to acquisition_system
        $this->addSql('ALTER TABLE acquisition_system ADD mac_address VARCHAR(17) DEFAULT NULL');

        // Restore default MAC addresses
        $this->addSql('UPDATE acquisition_system SET mac_address = CONCAT("AA:BB:CC:DD:EE:", LPAD(id, 2, "0")) WHERE mac_address IS NULL');

        // Make mac_address NOT NULL
        $this->addSql('ALTER TABLE acquisition_system MODIFY mac_address VARCHAR(17) NOT NULL');

        // Re-create unique index
        $this->addSql('CREATE UNIQUE INDEX UNIQ_13C61622B728E969 ON acquisition_system (mac_address)');

        // Re-add ip_address and wifi_mac_address columns to device_network_config
        $this->addSql('ALTER TABLE device_network_config ADD ip_address VARCHAR(45) DEFAULT NULL');
        $this->addSql('ALTER TABLE device_network_config ADD wifi_mac_address VARCHAR(17) DEFAULT NULL');
    }
}
