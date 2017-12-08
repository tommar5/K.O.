<?php

namespace AppBundle\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151102143643 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE audit_logs (id INT AUTO_INCREMENT NOT NULL, source_id INT NOT NULL, target_id INT DEFAULT NULL, blame_id INT DEFAULT NULL, action VARCHAR(12) NOT NULL, tbl VARCHAR(128) NOT NULL, diff LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json_array)\', logged_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_D62F2858953C1C61 (source_id), UNIQUE INDEX UNIQ_D62F2858158E0B66 (target_id), UNIQUE INDEX UNIQ_D62F28588C082A2E (blame_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE audit_associations (id INT AUTO_INCREMENT NOT NULL, typ VARCHAR(128) NOT NULL, tbl VARCHAR(128) NOT NULL, `label` VARCHAR(255) DEFAULT NULL, fk VARCHAR(255) NOT NULL, class VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE file_upload (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, type VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, file VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, reason LONGTEXT DEFAULT NULL, number VARCHAR(255) DEFAULT NULL, valid_until DATE DEFAULT NULL, INDEX IDX_AFAAC0A0A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mail_templates (id INT AUTO_INCREMENT NOT NULL, alias VARCHAR(255) NOT NULL, subject VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_17F263EDE16C6B94 (alias), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cms_block (id INT AUTO_INCREMENT NOT NULL, alias VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, email VARCHAR(255) NOT NULL, salt VARCHAR(48) NOT NULL, firstname VARCHAR(64) DEFAULT NULL, lastname VARCHAR(64) DEFAULT NULL, token VARCHAR(48) DEFAULT NULL, enabled TINYINT(1) NOT NULL, terms_confirmed TINYINT(1) NOT NULL, roles INT NOT NULL, about_me LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, last_login_at DATETIME DEFAULT NULL, password VARCHAR(64) DEFAULT NULL, UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), INDEX IDX_1483A5E9727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_competition (user_id INT NOT NULL, competition_id INT NOT NULL, INDEX IDX_1C11E524A76ED395 (user_id), INDEX IDX_1C11E5247B39D312 (competition_id), PRIMARY KEY(user_id, competition_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE declarant_request (id INT AUTO_INCREMENT NOT NULL, racer_id INT DEFAULT NULL, current_declarant_id INT DEFAULT NULL, new_declarant_id INT DEFAULT NULL, comment LONGTEXT DEFAULT NULL, status LONGTEXT NOT NULL, INDEX IDX_BDA3A2AA2112FF29 (racer_id), INDEX IDX_BDA3A2AAF1758767 (current_declarant_id), INDEX IDX_BDA3A2AA5088CF03 (new_declarant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE competition_jugde (id INT AUTO_INCREMENT NOT NULL, competition_id INT DEFAULT NULL, user_id INT DEFAULT NULL, role VARCHAR(255) NOT NULL, INDEX IDX_CB271C587B39D312 (competition_id), INDEX IDX_CB271C58A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE app_fixtures (name VARCHAR(255) NOT NULL, PRIMARY KEY(name)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE licences (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, expires_at DATE DEFAULT NULL, type VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, INDEX IDX_6314AC4FA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE licence_file_upload (licence_id INT NOT NULL, file_upload_id INT NOT NULL, INDEX IDX_EB0DE58A26EF07C9 (licence_id), INDEX IDX_EB0DE58A42C00547 (file_upload_id), PRIMARY KEY(licence_id, file_upload_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE competition (id INT AUTO_INCREMENT NOT NULL, main_judge_id INT DEFAULT NULL, user_id INT DEFAULT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME NOT NULL, date_from DATETIME NOT NULL, date_to DATETIME DEFAULT NULL, location VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, watcher VARCHAR(255) NOT NULL, safety_watcher VARCHAR(255) NOT NULL, INDEX IDX_B50A2CB16EF470E1 (main_judge_id), INDEX IDX_B50A2CB1A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE audit_logs ADD CONSTRAINT FK_D62F2858953C1C61 FOREIGN KEY (source_id) REFERENCES audit_associations (id)');
        $this->addSql('ALTER TABLE audit_logs ADD CONSTRAINT FK_D62F2858158E0B66 FOREIGN KEY (target_id) REFERENCES audit_associations (id)');
        $this->addSql('ALTER TABLE audit_logs ADD CONSTRAINT FK_D62F28588C082A2E FOREIGN KEY (blame_id) REFERENCES audit_associations (id)');
        $this->addSql('ALTER TABLE file_upload ADD CONSTRAINT FK_AFAAC0A0A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9727ACA70 FOREIGN KEY (parent_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE user_competition ADD CONSTRAINT FK_1C11E524A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_competition ADD CONSTRAINT FK_1C11E5247B39D312 FOREIGN KEY (competition_id) REFERENCES competition (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE declarant_request ADD CONSTRAINT FK_BDA3A2AA2112FF29 FOREIGN KEY (racer_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE declarant_request ADD CONSTRAINT FK_BDA3A2AAF1758767 FOREIGN KEY (current_declarant_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE declarant_request ADD CONSTRAINT FK_BDA3A2AA5088CF03 FOREIGN KEY (new_declarant_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE competition_jugde ADD CONSTRAINT FK_CB271C587B39D312 FOREIGN KEY (competition_id) REFERENCES competition (id)');
        $this->addSql('ALTER TABLE competition_jugde ADD CONSTRAINT FK_CB271C58A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE licences ADD CONSTRAINT FK_6314AC4FA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE licence_file_upload ADD CONSTRAINT FK_EB0DE58A26EF07C9 FOREIGN KEY (licence_id) REFERENCES licences (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE licence_file_upload ADD CONSTRAINT FK_EB0DE58A42C00547 FOREIGN KEY (file_upload_id) REFERENCES file_upload (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE competition ADD CONSTRAINT FK_B50A2CB16EF470E1 FOREIGN KEY (main_judge_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE competition ADD CONSTRAINT FK_B50A2CB1A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE audit_logs DROP FOREIGN KEY FK_D62F2858953C1C61');
        $this->addSql('ALTER TABLE audit_logs DROP FOREIGN KEY FK_D62F2858158E0B66');
        $this->addSql('ALTER TABLE audit_logs DROP FOREIGN KEY FK_D62F28588C082A2E');
        $this->addSql('ALTER TABLE licence_file_upload DROP FOREIGN KEY FK_EB0DE58A42C00547');
        $this->addSql('ALTER TABLE file_upload DROP FOREIGN KEY FK_AFAAC0A0A76ED395');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E9727ACA70');
        $this->addSql('ALTER TABLE user_competition DROP FOREIGN KEY FK_1C11E524A76ED395');
        $this->addSql('ALTER TABLE declarant_request DROP FOREIGN KEY FK_BDA3A2AA2112FF29');
        $this->addSql('ALTER TABLE declarant_request DROP FOREIGN KEY FK_BDA3A2AAF1758767');
        $this->addSql('ALTER TABLE declarant_request DROP FOREIGN KEY FK_BDA3A2AA5088CF03');
        $this->addSql('ALTER TABLE competition_jugde DROP FOREIGN KEY FK_CB271C58A76ED395');
        $this->addSql('ALTER TABLE licences DROP FOREIGN KEY FK_6314AC4FA76ED395');
        $this->addSql('ALTER TABLE competition DROP FOREIGN KEY FK_B50A2CB16EF470E1');
        $this->addSql('ALTER TABLE competition DROP FOREIGN KEY FK_B50A2CB1A76ED395');
        $this->addSql('ALTER TABLE licence_file_upload DROP FOREIGN KEY FK_EB0DE58A26EF07C9');
        $this->addSql('ALTER TABLE user_competition DROP FOREIGN KEY FK_1C11E5247B39D312');
        $this->addSql('ALTER TABLE competition_jugde DROP FOREIGN KEY FK_CB271C587B39D312');
        $this->addSql('DROP TABLE audit_logs');
        $this->addSql('DROP TABLE audit_associations');
        $this->addSql('DROP TABLE file_upload');
        $this->addSql('DROP TABLE mail_templates');
        $this->addSql('DROP TABLE cms_block');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE user_competition');
        $this->addSql('DROP TABLE declarant_request');
        $this->addSql('DROP TABLE competition_jugde');
        $this->addSql('DROP TABLE app_fixtures');
        $this->addSql('DROP TABLE licences');
        $this->addSql('DROP TABLE licence_file_upload');
        $this->addSql('DROP TABLE competition');
    }
}
