<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251123131944 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE device_network_config (id INT AUTO_INCREMENT NOT NULL, acquisition_system_id INT NOT NULL, wifi_ssid VARCHAR(100) NOT NULL, signal_strength INT NOT NULL, ip_address VARCHAR(45) NOT NULL, wifi_mac_address VARCHAR(17) NOT NULL, ntp_server VARCHAR(100) NOT NULL, timezone VARCHAR(50) NOT NULL, gmt_offset_sec INT NOT NULL, daylight_offset_sec INT NOT NULL, UNIQUE INDEX UNIQ_10D4D9EA331785FF (acquisition_system_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE device_sensor (id INT AUTO_INCREMENT NOT NULL, acquisition_system_id INT NOT NULL, capture_type_id INT NOT NULL, sensor_type VARCHAR(50) NOT NULL, enabled TINYINT(1) NOT NULL, read_interval_ms INT NOT NULL, i2c_sda_pin INT DEFAULT NULL, i2c_scl_pin INT DEFAULT NULL, adc_pin INT DEFAULT NULL, warmup_duration_sec INT DEFAULT NULL, INDEX IDX_6F9666AB331785FF (acquisition_system_id), INDEX IDX_6F9666ABB7E15955 (capture_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE device_system_config (id INT AUTO_INCREMENT NOT NULL, acquisition_system_id INT NOT NULL, debug TINYINT(1) NOT NULL, buffer_size INT NOT NULL, deep_sleep_enabled TINYINT(1) NOT NULL, web_server_enabled TINYINT(1) NOT NULL, web_server_port INT NOT NULL, UNIQUE INDEX UNIQ_35A20D18331785FF (acquisition_system_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE device_task (id INT AUTO_INCREMENT NOT NULL, acquisition_system_id INT NOT NULL, task_name VARCHAR(50) NOT NULL, enabled TINYINT(1) NOT NULL, interval_ms INT NOT NULL, priority INT NOT NULL, task_config JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', INDEX IDX_DB470E1F331785FF (acquisition_system_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE device_network_config ADD CONSTRAINT FK_10D4D9EA331785FF FOREIGN KEY (acquisition_system_id) REFERENCES acquisition_system (id)');
        $this->addSql('ALTER TABLE device_sensor ADD CONSTRAINT FK_6F9666AB331785FF FOREIGN KEY (acquisition_system_id) REFERENCES acquisition_system (id)');
        $this->addSql('ALTER TABLE device_sensor ADD CONSTRAINT FK_6F9666ABB7E15955 FOREIGN KEY (capture_type_id) REFERENCES capture_type (id)');
        $this->addSql('ALTER TABLE device_system_config ADD CONSTRAINT FK_35A20D18331785FF FOREIGN KEY (acquisition_system_id) REFERENCES acquisition_system (id)');
        $this->addSql('ALTER TABLE device_task ADD CONSTRAINT FK_DB470E1F331785FF FOREIGN KEY (acquisition_system_id) REFERENCES acquisition_system (id)');

        // Add new columns as nullable first
        $this->addSql('ALTER TABLE acquisition_system ADD device_type VARCHAR(50) DEFAULT NULL, ADD firmware_version VARCHAR(20) DEFAULT NULL, ADD mac_address VARCHAR(17) DEFAULT NULL, ADD is_active TINYINT(1) DEFAULT 1, ADD last_seen DATETIME DEFAULT NULL');

        // Update existing records with default values
        $this->addSql('UPDATE acquisition_system SET device_type = "ESP32_WROOM" WHERE device_type IS NULL');
        $this->addSql('UPDATE acquisition_system SET firmware_version = "1.0.0" WHERE firmware_version IS NULL');
        $this->addSql('UPDATE acquisition_system SET mac_address = CONCAT("AA:BB:CC:DD:EE:", LPAD(id, 2, "0")) WHERE mac_address IS NULL');

        // Make columns NOT NULL
        $this->addSql('ALTER TABLE acquisition_system MODIFY device_type VARCHAR(50) NOT NULL, MODIFY firmware_version VARCHAR(20) NOT NULL, MODIFY mac_address VARCHAR(17) NOT NULL, MODIFY is_active TINYINT(1) NOT NULL');

        // Create unique index
        $this->addSql('CREATE UNIQUE INDEX UNIQ_13C61622B728E969 ON acquisition_system (mac_address)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE device_network_config DROP FOREIGN KEY FK_10D4D9EA331785FF');
        $this->addSql('ALTER TABLE device_sensor DROP FOREIGN KEY FK_6F9666AB331785FF');
        $this->addSql('ALTER TABLE device_sensor DROP FOREIGN KEY FK_6F9666ABB7E15955');
        $this->addSql('ALTER TABLE device_system_config DROP FOREIGN KEY FK_35A20D18331785FF');
        $this->addSql('ALTER TABLE device_task DROP FOREIGN KEY FK_DB470E1F331785FF');
        $this->addSql('DROP TABLE device_network_config');
        $this->addSql('DROP TABLE device_sensor');
        $this->addSql('DROP TABLE device_system_config');
        $this->addSql('DROP TABLE device_task');
        $this->addSql('DROP INDEX UNIQ_13C61622B728E969 ON acquisition_system');
        $this->addSql('ALTER TABLE acquisition_system DROP device_type, DROP firmware_version, DROP mac_address, DROP is_active, DROP last_seen');
    }
}
