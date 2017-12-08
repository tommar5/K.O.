<?php

namespace AppBundle\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151111110206 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE licences ADD licence_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE licences ADD CONSTRAINT FK_6314AC4F26EF07C9 FOREIGN KEY (licence_id) REFERENCES licences (id)');
        $this->addSql('CREATE INDEX IDX_6314AC4F26EF07C9 ON licences (licence_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE licences DROP FOREIGN KEY FK_6314AC4F26EF07C9');
        $this->addSql('DROP INDEX IDX_6314AC4F26EF07C9 ON licences');
        $this->addSql('ALTER TABLE licences DROP licence_id');
    }
}
