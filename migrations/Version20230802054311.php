<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230802054311 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE measure_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(32) NOT NULL, units VARCHAR(16) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE measure_type_module (measure_type_id INT NOT NULL, module_id INT NOT NULL, INDEX IDX_A0EEB27E5E3758EB (measure_type_id), INDEX IDX_A0EEB27EAFC2B591 (module_id), PRIMARY KEY(measure_type_id, module_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE measured_value (id INT AUTO_INCREMENT NOT NULL, module_id INT NOT NULL, type_id INT NOT NULL, datetime DATETIME NOT NULL, value DOUBLE PRECISION NOT NULL, INDEX IDX_223D8DC0AFC2B591 (module_id), INDEX IDX_223D8DC0C54C8C93 (type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE module (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(32) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE state (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(32) NOT NULL, is_operable TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE state_history (id INT AUTO_INCREMENT NOT NULL, module_id INT NOT NULL, state_id INT NOT NULL, datetime DATETIME NOT NULL, INDEX IDX_61DA0AEDAFC2B591 (module_id), INDEX IDX_61DA0AED5D83CC1 (state_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE measure_type_module ADD CONSTRAINT FK_A0EEB27E5E3758EB FOREIGN KEY (measure_type_id) REFERENCES measure_type (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE measure_type_module ADD CONSTRAINT FK_A0EEB27EAFC2B591 FOREIGN KEY (module_id) REFERENCES module (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE measured_value ADD CONSTRAINT FK_223D8DC0AFC2B591 FOREIGN KEY (module_id) REFERENCES module (id)');
        $this->addSql('ALTER TABLE measured_value ADD CONSTRAINT FK_223D8DC0C54C8C93 FOREIGN KEY (type_id) REFERENCES measure_type (id)');
        $this->addSql('ALTER TABLE state_history ADD CONSTRAINT FK_61DA0AEDAFC2B591 FOREIGN KEY (module_id) REFERENCES module (id)');
        $this->addSql('ALTER TABLE state_history ADD CONSTRAINT FK_61DA0AED5D83CC1 FOREIGN KEY (state_id) REFERENCES state (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE measure_type_module DROP FOREIGN KEY FK_A0EEB27E5E3758EB');
        $this->addSql('ALTER TABLE measure_type_module DROP FOREIGN KEY FK_A0EEB27EAFC2B591');
        $this->addSql('ALTER TABLE measured_value DROP FOREIGN KEY FK_223D8DC0AFC2B591');
        $this->addSql('ALTER TABLE measured_value DROP FOREIGN KEY FK_223D8DC0C54C8C93');
        $this->addSql('ALTER TABLE state_history DROP FOREIGN KEY FK_61DA0AEDAFC2B591');
        $this->addSql('ALTER TABLE state_history DROP FOREIGN KEY FK_61DA0AED5D83CC1');
        $this->addSql('DROP TABLE measure_type');
        $this->addSql('DROP TABLE measure_type_module');
        $this->addSql('DROP TABLE measured_value');
        $this->addSql('DROP TABLE module');
        $this->addSql('DROP TABLE state');
        $this->addSql('DROP TABLE state_history');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
