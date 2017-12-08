<?php

namespace AppBundle\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161017113053 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("UPDATE role_action SET roles=32854 WHERE action = 'contract-view'");
        $this->addSql("INSERT INTO role_action (action,roles) VALUES ('insurance-view',32790)");
        $this->addSql("INSERT INTO role_action (action,roles) VALUES ('track-licence-view',33302)");
        $this->addSql("INSERT INTO role_action (action,roles) VALUES ('organisator-licence-view',33302)");
        $this->addSql("INSERT INTO role_action (action,roles) VALUES ('application-copy-view',32854)");

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
