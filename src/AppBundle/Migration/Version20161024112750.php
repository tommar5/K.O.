<?php

namespace AppBundle\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161024112750 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("UPDATE role_action SET roles=33808 WHERE action = 'track-licence-view'");
        $this->addSql("UPDATE role_action SET roles=33808 WHERE action = 'track-licence-confirm'");
        $this->addSql("UPDATE role_action SET roles=33808 WHERE action = 'track-licence-upload'");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
