<?php

namespace AppBundle\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170418151124 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql("INSERT INTO role_action (action,roles) VALUES ('competition-insurance-confirm',32784)");
        $this->addSql("INSERT INTO role_action (action,roles) VALUES ('organisator-license-confirm',32784)");

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE declarant_request DROP FOREIGN KEY FK_BDA3A2AA2112FF29');
        $this->addSql('ALTER TABLE declarant_request DROP FOREIGN KEY FK_BDA3A2AAF1758767');
    }
}
