<?php

namespace AppBundle\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161021143855 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("UPDATE role_action SET roles=32784 WHERE action = 'date-restriction'");
        $this->addSql("UPDATE role_action SET roles=32784 WHERE action = 'admin-fields-view'");
        $this->addSql("UPDATE role_action SET roles=32784 WHERE action = 'safety-chiefs-view'");
        $this->addSql("UPDATE role_action SET roles=32784 WHERE action = 'application-decline'");
        $this->addSql("UPDATE role_action SET roles=32784 WHERE action = 'application-delete'");
        $this->addSql("UPDATE role_action SET roles=32832 WHERE action = 'additional-fields-view'");
        $this->addSql("UPDATE role_action SET roles=32832 WHERE action = 'delivery-modify'");
        $this->addSql("UPDATE role_action SET roles=32832 WHERE action = 'applicatiion-status-view'");
        $this->addSql("UPDATE role_action SET roles=32832 WHERE action = 'competition-chief-view'");
        $this->addSql("UPDATE role_action SET roles=32848 WHERE action = 'application-show-actions'");
        $this->addSql("UPDATE role_action SET roles=32848 WHERE action = 'svo-delegate-confirm'");
        $this->addSql("UPDATE role_action SET roles=33792 WHERE action = 'safety-plan-confirm'");
        $this->addSql("UPDATE role_action SET roles=33792 WHERE action = 'track-acceptance-confirm'");
        $this->addSql("UPDATE role_action SET roles=34326 WHERE action = 'upload-comments'");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
