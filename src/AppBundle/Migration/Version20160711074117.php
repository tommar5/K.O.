<?php

namespace AppBundle\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160711074117 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE applications ADD observer_id INT DEFAULT NULL, DROP observer');
        $this->addSql('ALTER TABLE applications ADD CONSTRAINT FK_F7C966F083F4F546 FOREIGN KEY (observer_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_F7C966F083F4F546 ON applications (observer_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE applications DROP FOREIGN KEY FK_F7C966F083F4F546');
        $this->addSql('DROP INDEX IDX_F7C966F083F4F546 ON applications');
        $this->addSql('ALTER TABLE applications ADD observer VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, DROP observer_id');
    }
}
