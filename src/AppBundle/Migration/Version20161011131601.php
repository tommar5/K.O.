<?php

namespace AppBundle\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161011131601 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("UPDATE role_action SET roles=33808 WHERE action = 'track-acceptance-upload'");
        $this->addSql("UPDATE role_action SET roles=36374 WHERE action = 'other-documents-upload'");
        $this->addSql("UPDATE role_action SET roles=36374 WHERE action = 'other-documents-view'");
        $this->addSql("UPDATE role_action SET roles=32854 WHERE action = 'show-invoice'");

        $this->addSql("INSERT INTO role_action (action,roles) VALUES ('additional-rules-view',35350)");
        $this->addSql("INSERT INTO role_action (action,roles) VALUES ('contract-view',35414)");
        $this->addSql("INSERT INTO role_action (action,roles) VALUES ('safety-plan-view',36374)");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
