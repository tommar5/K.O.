<?php

namespace AppBundle\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160630080944 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sports (id INT AUTO_INCREMENT NOT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME NOT NULL, name VARCHAR(255) NOT NULL, alias VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE applications ADD sport_id INT DEFAULT NULL, DROP sport');
        $this->addSql('ALTER TABLE applications ADD CONSTRAINT FK_F7C966F0AC78BCF8 FOREIGN KEY (sport_id) REFERENCES sports (id)');
        $this->addSql('CREATE INDEX IDX_F7C966F0AC78BCF8 ON applications (sport_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE applications DROP FOREIGN KEY FK_F7C966F0AC78BCF8');
        $this->addSql('DROP TABLE sports');
        $this->addSql('DROP INDEX IDX_F7C966F0AC78BCF8 ON applications');
        $this->addSql('ALTER TABLE applications ADD sport VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, DROP sport_id');
    }
}
