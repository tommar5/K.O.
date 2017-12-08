<?php

namespace AppBundle\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20170801145504 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE applications ADD inspection_date datetime DEFAULT NULL');
        $this->addSql('ALTER TABLE sub_competition ADD inspection_date datetime DEFAULT NULL');
        $this->addSql("INSERT INTO role_action (action, roles) VALUES ('safety-plan-inspection-date-edit', 1024)");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE applications DROP COLUMN inspection_date');
        $this->addSql('ALTER TABLE sub_competition DROP COLUMN inspection_date');
        $this->addSql("DELETE FROM role_action WHERE action='safety-plan-inspection-date-edit'");
    }
}
