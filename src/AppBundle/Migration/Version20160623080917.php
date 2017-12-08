<?php

namespace AppBundle\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160623080917 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE time_restriction (id INT AUTO_INCREMENT NOT NULL, pay_taxes_term VARCHAR(255) DEFAULT NULL, additional_rules_term VARCHAR(255) DEFAULT NULL, additional_rules_confirmation_term VARCHAR(255) DEFAULT NULL, safety_plan_term VARCHAR(255) DEFAULT NULL, final_rules_term VARCHAR(255) DEFAULT NULL, contact_administration_term VARCHAR(255) DEFAULT NULL, track_act_term VARCHAR(255) DEFAULT NULL, is_pay_taxes_term TINYINT(1) DEFAULT NULL, is_additional_rules_term TINYINT(1) DEFAULT NULL, is_additional_rules_confirmation_term TINYINT(1) DEFAULT NULL, is_safety_plan_term TINYINT(1) DEFAULT NULL, is_final_rules_term TINYINT(1) DEFAULT NULL, is_contact_administration_term TINYINT(1) DEFAULT NULL, is_track_act_term TINYINT(1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('INSERT INTO time_restriction (id) VALUE (1)');
        $this->addSql('ALTER TABLE applications ADD reason LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE application_documents ADD CONSTRAINT FK_26B108893E030ACD FOREIGN KEY (application_id) REFERENCES applications (id)');
        $this->addSql('ALTER TABLE application_documents ADD CONSTRAINT FK_26B10889C33F7837 FOREIGN KEY (document_id) REFERENCES file_upload (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE time_restriction');
        $this->addSql('ALTER TABLE application_documents DROP FOREIGN KEY FK_26B108893E030ACD');
        $this->addSql('ALTER TABLE application_documents DROP FOREIGN KEY FK_26B10889C33F7837');
        $this->addSql('ALTER TABLE applications DROP reason');
    }
}
