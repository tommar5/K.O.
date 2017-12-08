<?php

namespace AppBundle\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160926134505 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE role_action (id INT AUTO_INCREMENT NOT NULL, action VARCHAR(64) DEFAULT NULL, roles INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql("INSERT INTO role_action (action, roles) VALUES ('application-new', 16),
          ('application-confirm', 32768), ('agreement-create', 32768), ('agreement-upload', 32784), ('upload-terms', 32784), 
          ('organiser-signed-agreement-upload', 6), ('invoice-upload', 80), ('invoice-delete', 64), ('invoice-confirm', 64), 
          ('organiser-license-upload', 32784), ('track-license-upload', 32784), ('safety-plan-upload', 2054), ('safety-plan-comments', 1536),
          ('safety-plan-confirm', 1024), ('additional-regulations-upload', 2054), ('regulations-comments', 33280), 
          ('regulations-confirm-secretary', 32768), ('regulations-confirm-lasf', 512), ('skk-chairman-select', 65536), ('commissioner-select', 65536), 
          ('track-license-confirm', 33808), ('lasf-spectator-select', 32784), ('lasf-technician-select', 32784), ('svo-delegate-select', 32784),
          ('svo-delegate-see', 33296), ('svo-delegate-confirm', 512), ('safety-chief-documents', 32784), ('safety-chief-select', 32784),
          ('judge-assign-organiser', 6), ('judge-assign-chief', 2048), ('skk-chairman-report-upload', 131094), ('report-upload', 131206),
          ('result-upload', 2054), ('bulletin-upload', 133126), ('competition-chief-decision-upload', 2048), ('show-invoice', 86), 
          ('upload-comments', 34304), ('safety-plan-show-comments', 1542), ('approve-document', 34320), ('regulations-show-comments', 33286)");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE role_action');
    }
}
