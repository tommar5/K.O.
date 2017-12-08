<?php

namespace AppBundle\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151208083308 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE licences ADD declarant_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE licences ADD CONSTRAINT FK_6314AC4FEC439BC FOREIGN KEY (declarant_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_6314AC4FEC439BC ON licences (declarant_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE licences DROP FOREIGN KEY FK_6314AC4FEC439BC');
        $this->addSql('DROP INDEX IDX_6314AC4FEC439BC ON licences');
        $this->addSql('ALTER TABLE licences DROP declarant_id');
    }
}
