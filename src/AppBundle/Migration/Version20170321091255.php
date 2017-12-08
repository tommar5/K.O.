<?php

namespace AppBundle\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170321091255 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sub_competition (id INT AUTO_INCREMENT NOT NULL, application_id INT DEFAULT NULL, sport_id INT DEFAULT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME NOT NULL, date_from DATETIME NOT NULL, date_to DATETIME DEFAULT NULL, location VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, stage VARCHAR(255) NOT NULL, league VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, INDEX IDX_50F3592F3E030ACD (application_id), INDEX IDX_50F3592FAC78BCF8 (sport_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sub_competition ADD CONSTRAINT FK_50F3592F3E030ACD FOREIGN KEY (application_id) REFERENCES applications (id)');
        $this->addSql('ALTER TABLE sub_competition ADD CONSTRAINT FK_50F3592FAC78BCF8 FOREIGN KEY (sport_id) REFERENCES sports (id)');
        $this->addSql('DROP INDEX UNIQ_1483A5E9E7927C74 ON users');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE sub_competition');
    }
}
