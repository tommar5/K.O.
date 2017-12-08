<?php

namespace AppBundle\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161010154804 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("UPDATE role_action SET roles=32784 WHERE action = 'application-new'");
        $this->addSql("UPDATE role_action SET roles=32784 WHERE action = 'application-confirm'");
        $this->addSql("UPDATE role_action SET roles=32784 WHERE action = 'agreement-create'");
        $this->addSql("UPDATE role_action SET roles=32848 WHERE action = 'invoice-upload'");
        $this->addSql("UPDATE role_action SET roles=33280 WHERE action = 'regulations-confirm-lasf'");
        $this->addSql("UPDATE role_action SET roles=98320 WHERE action = 'skk-chairman-select'");
        $this->addSql("UPDATE role_action SET roles=2054 WHERE action = 'skk-chairman-report-upload'");
        $this->addSql("UPDATE role_action SET roles=34326 WHERE action = 'safety-plan-show-comments'");
        $this->addSql("UPDATE role_action SET roles=22 WHERE action = 'track-acceptance-upload'");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
