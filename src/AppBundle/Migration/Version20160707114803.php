<?php

namespace AppBundle\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160707114803 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE safety_chief (id INT AUTO_INCREMENT NOT NULL, firstname VARCHAR(64) DEFAULT NULL, lastname VARCHAR(64) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE applications ADD safety_chief_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE applications ADD CONSTRAINT FK_F7C966F039D275FA FOREIGN KEY (safety_chief_id) REFERENCES safety_chief (id)');
        $this->addSql('CREATE INDEX UNIQ_F7C966F039D275FA ON applications (safety_chief_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE applications DROP FOREIGN KEY FK_F7C966F039D275FA');
        $this->addSql('DROP TABLE safety_chief');
        $this->addSql('DROP INDEX UNIQ_F7C966F039D275FA ON applications');
        $this->addSql('ALTER TABLE applications DROP safety_chief_id');
    }
}
